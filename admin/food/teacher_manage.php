<?php
session_start();
error_reporting(0);

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

include('../../include/dbcon.php');
date_default_timezone_set('Asia/Kolkata');

// Authentication check
if (empty($_SESSION['mid']) && empty($_SESSION['alogin'])) {
    if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
        exit;
    } else {
        header('location:index.php');
        exit;
    }
}

$teacher_id = intval($_GET['teacher_id'] ?? 0);
if ($teacher_id <= 0) {
    header('location:teacher_list.php');
    exit;
}

// Fetch teacher details
$t_res = mysqli_query($myDB, "SELECT * FROM rrsv_teacher WHERE id = $teacher_id LIMIT 1");
$teacher = mysqli_fetch_assoc($t_res);
if (!$teacher) {
    header('location:teacher_list.php');
    exit;
}

$teacher_code = 'T-' . $teacher['id'];

// Helper function for transaction ID
function generateTeacherTxnId($myDB) {
    $date = date('ymd');
    $res = mysqli_query(
        $myDB,
        "SELECT COUNT(*) AS total 
         FROM rrsv_food_wallet_ledger 
         WHERE DATE(created_at) = CURDATE()"
    );
    $row = mysqli_fetch_assoc($res);
    $seq = str_pad(($row['total'] ?? 0) + 1, 3, '0', STR_PAD_LEFT);
    return "TXN{$date}{$seq}";
}

/* =========================================================
   1. RECHARGE HANDLER
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recharge'])) {
    if (!isset($_POST['token'], $_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
        $error = "Invalid security token. Please try again.";
    } else {
        unset($_SESSION['token']);
        $_SESSION['token'] = bin2hex(random_bytes(32));

        $amount = (float)$_POST['amount'];
        $payment_mode = trim($_POST['payment_mode'] ?? 'Cash');
        $custom_note = trim($_POST['note'] ?? '');

        if ($amount <= 0) {
            $error = "Please enter a valid amount greater than 0.";
        } else {
            $desc = "Wallet Recharge (" . $payment_mode . ")";
            if (!empty($custom_note)) {
                $desc .= " - " . $custom_note;
            }
            $safe_desc = mysqli_real_escape_string($myDB, $desc);

            mysqli_begin_transaction($myDB);
            try {
                // Check if wallet exists
                $check = mysqli_query(
                    $myDB,
                    "SELECT balance FROM rrsv_food_wallet 
                     WHERE student_id = '$teacher_code' OR student_id = '$teacher_id' 
                     FOR UPDATE"
                );

                if (mysqli_num_rows($check) == 0) {
                    mysqli_query($myDB, "
                        INSERT INTO rrsv_food_wallet (student_id, balance)
                        VALUES ('$teacher_code', $amount)
                    ");
                } else {
                    mysqli_query($myDB, "
                        UPDATE rrsv_food_wallet
                        SET balance = balance + $amount, student_id = '$teacher_code'
                        WHERE student_id = '$teacher_code' OR student_id = '$teacher_id'
                    ");
                }

                $txn_id = generateTeacherTxnId($myDB);

                mysqli_query($myDB, "
                    INSERT INTO rrsv_food_wallet_ledger
                    (student_id, tns_id, amount, type, description, created_at)
                    VALUES
                    ('$teacher_code', '$txn_id', $amount, 'Credit', '$safe_desc', NOW())
                ");

                mysqli_commit($myDB);
                $success = "Teacher wallet recharged successfully with ₹" . number_format($amount, 2) . " via " . htmlspecialchars($payment_mode) . ".";
            } catch (Exception $e) {
                mysqli_rollback($myDB);
                $error = "Recharge transaction failed: " . $e->getMessage();
            }
        }
    }
}

/* =========================================================
   2. AJAX: DELETE RECHARGE LEDGER
========================================================= */
if (isset($_POST['ajax']) && $_POST['ajax'] === 'delete_ledger') {
    header('Content-Type: application/json');
    $id = (int)($_POST['id'] ?? 0);
    $password = trim($_POST['password'] ?? '');

    // Verify admin password
    $admin = mysqli_fetch_assoc(mysqli_query($myDB, "SELECT admin_pwd FROM rrsv_admin WHERE admin_id = '2' LIMIT 1"));
    if (!$admin || $password !== $admin['admin_pwd']) {
        echo json_encode(['status' => 'error', 'msg' => 'Incorrect Admin Password']);
        exit;
    }

    mysqli_begin_transaction($myDB);
    try {
        $row = mysqli_fetch_assoc(mysqli_query($myDB, "
            SELECT student_id, amount, type 
            FROM rrsv_food_wallet_ledger 
            WHERE id = $id AND is_delete = 'No' 
            FOR UPDATE
        "));

        if (!$row) {
            throw new Exception('Ledger record not found or already deleted');
        }

        $sid = $row['student_id'];
        $amt = (float)$row['amount'];

        $wallet = mysqli_fetch_assoc(mysqli_query($myDB, "
            SELECT balance FROM rrsv_food_wallet 
            WHERE student_id = '$sid' OR student_id = '$teacher_code' OR student_id = '$teacher_id' 
            FOR UPDATE
        "));
        $balance = (float)($wallet['balance'] ?? 0);

        if ($row['type'] === 'Credit') {
            if ($balance < $amt) {
                throw new Exception('Cannot delete: wallet balance (₹' . number_format($balance, 2) . ') is lower than recharge amount (₹' . number_format($amt, 2) . ')');
            }
            mysqli_query($myDB, "
                UPDATE rrsv_food_wallet 
                SET balance = balance - $amt 
                WHERE student_id = '$sid' OR student_id = '$teacher_code' OR student_id = '$teacher_id'
            ");
        } else {
            mysqli_query($myDB, "
                UPDATE rrsv_food_wallet 
                SET balance = balance + $amt 
                WHERE student_id = '$sid' OR student_id = '$teacher_code' OR student_id = '$teacher_id'
            ");
        }

        mysqli_query($myDB, "
            UPDATE rrsv_food_wallet_ledger 
            SET is_delete = 'Yes' 
            WHERE id = $id
        ");

        mysqli_commit($myDB);
        echo json_encode(['status' => 'success']);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($myDB);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        exit;
    }
}

/* =========================================================
   3. AJAX: GET ORDERS FOR A SPECIFIC DATE (Calendar Click)
========================================================= */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_orders') {
    header('Content-Type: application/json');
    $date = mysqli_real_escape_string($myDB, $_GET['date'] ?? '');

    $sql = "
        SELECT 
            oi.id,
            oi.meal_type,
            p.package_name,
            oi.price,
            oi.status
        FROM rrsv_food_order_items oi
        INNER JOIN rrsv_food_orders o ON o.id = oi.order_id
        INNER JOIN rrsv_menu_package p ON p.id = oi.food_id
        WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
          AND oi.order_date = '$date'
        ORDER BY FIELD(oi.meal_type, 'Breakfast', 'Lunch', 'Dinner', 'Tiffin'), oi.id ASC
    ";

    $q = mysqli_query($myDB, $sql);
    $data = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $data[] = $r;
    }
    echo json_encode($data);
    exit;
}

/* =========================================================
   4. AJAX: CANCEL ORDER ITEM
========================================================= */
if (isset($_POST['ajax']) && $_POST['ajax'] === 'cancel_order') {
    header('Content-Type: application/json');
    $id = (int)($_POST['id'] ?? 0);

    mysqli_begin_transaction($myDB);
    try {
        $item = mysqli_fetch_assoc(mysqli_query($myDB, "
            SELECT oi.id, oi.meal_type, oi.order_date, oi.price, oi.status, oi.quantity, p.package_name
            FROM rrsv_food_order_items oi
            JOIN rrsv_food_orders o ON o.id = oi.order_id
            JOIN rrsv_menu_package p ON p.id = oi.food_id
            WHERE oi.id = $id AND (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
            FOR UPDATE
        "));

        if (!$item) {
            throw new Exception('Order item not found');
        }

        if ($item['status'] === 'Cancelled') {
            echo json_encode(['status' => 'success', 'msg' => 'Already cancelled']);
            exit;
        }

        $refund = (float)$item['price'] * (int)($item['quantity'] ?: 1);

        // Mark cancelled
        mysqli_query($myDB, "UPDATE rrsv_food_order_items SET status = 'Cancelled' WHERE id = $id");

        // Refund to wallet
        mysqli_query($myDB, "
            UPDATE rrsv_food_wallet 
            SET balance = balance + $refund 
            WHERE student_id = '$teacher_code' OR student_id = '$teacher_id'
        ");

        // Add ledger record
        $txn_id = generateTeacherTxnId($myDB);
        $desc = "Order Refund: " . $item['meal_type'] . " on " . date('d-m-Y', strtotime($item['order_date']));
        $safe_desc = mysqli_real_escape_string($myDB, $desc);
        mysqli_query($myDB, "
            INSERT INTO rrsv_food_wallet_ledger 
            (student_id, tns_id, amount, type, description, created_at)
            VALUES 
            ('$teacher_code', '$txn_id', $refund, 'Credit', '$safe_desc', NOW())
        ");

        mysqli_commit($myDB);
        echo json_encode(['status' => 'success', 'msg' => 'Order cancelled and ₹' . number_format($refund, 2) . ' refunded to wallet']);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($myDB);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        exit;
    }
}

/* =========================================================
   5. WALLET BALANCE RETRIEVAL
========================================================= */
$wallet_res = mysqli_query($myDB, "
    SELECT balance FROM rrsv_food_wallet 
    WHERE student_id = '$teacher_code' OR student_id = '$teacher_id' 
    LIMIT 1
");
$wallet_row = mysqli_fetch_assoc($wallet_res);
$balance = (float)($wallet_row['balance'] ?? 0);

$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

/* =========================================================
   6. FOOD ORDER SUBMISSION
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_orders'])) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $orders = $_POST['orders'] ?? [];

    if (empty($start_date) || empty($end_date)) {
        $error = "Please specify both Start Date and End Date.";
    } elseif (strtotime($end_date) < strtotime($start_date)) {
        $error = "End Date cannot be earlier than Start Date.";
    } elseif (empty($orders)) {
        $error = "Please select at least one meal package.";
    } else {
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            (new DateTime($end_date))->modify('+1 day')
        );

        $items = [];
        $total = 0;

        foreach ($period as $d) {
            $date = $d->format('Y-m-d');
            $day = $d->format('l');

            if (!empty($orders[$day])) {
                foreach ($orders[$day] as $meal => $food_ids) {
                    // Check if already ordered this meal on this date
                    $chk_sql = "
                        SELECT COUNT(*) AS c
                        FROM rrsv_food_order_items oi
                        JOIN rrsv_food_orders o ON o.id = oi.order_id
                        WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
                          AND oi.order_date = '$date'
                          AND oi.meal_type = '$meal'
                          AND oi.status = 'Confirmed'
                    ";
                    $chk = mysqli_fetch_assoc(mysqli_query($myDB, $chk_sql));

                    if ($chk['c'] > 0) {
                        $error = "Already ordered $meal on " . date('d-m-Y (l)', strtotime($date)) . ". Duplicate meal not allowed.";
                        break 2;
                    }

                    foreach ($food_ids as $fid) {
                        $p_res = mysqli_query($myDB, "SELECT price, package_name FROM rrsv_menu_package WHERE id = '$fid' LIMIT 1");
                        $p = mysqli_fetch_assoc($p_res);
                        $price = (float)($p['price'] ?? 0);
                        $total += $price;
                        $items[] = [$fid, $meal, $date, $price];
                    }
                }
            }
        }

        if (empty($error)) {
            if (empty($items)) {
                $error = "No meals were selected for the chosen date range.";
            } elseif ($total > $balance) {
                $error = "Insufficient wallet balance. Required: ₹" . number_format($total, 2) . ", Available: ₹" . number_format($balance, 2) . ". Please recharge wallet or select fewer meals.";
            } else {
                mysqli_begin_transaction($myDB);
                try {
                    // Insert order
                    mysqli_query($myDB, "
                        INSERT INTO rrsv_food_orders (student_id, start_date, end_date, total_amount, status, created_at)
                        VALUES ('$teacher_code', '$start_date', '$end_date', $total, 'Confirmed', NOW())
                    ");
                    $oid = mysqli_insert_id($myDB);

                    // Insert order items
                    foreach ($items as $item) {
                        mysqli_query($myDB, "
                            INSERT INTO rrsv_food_order_items (order_id, food_id, meal_type, order_date, price, quantity, status)
                            VALUES ($oid, {$item[0]}, '{$item[1]}', '{$item[2]}', {$item[3]}, 1, 'Confirmed')
                        ");
                    }

                    // Deduct wallet
                    mysqli_query($myDB, "
                        UPDATE rrsv_food_wallet 
                        SET balance = balance - $total 
                        WHERE student_id = '$teacher_code' OR student_id = '$teacher_id'
                    ");

                    // Add debit ledger
                    $txn_id = generateTeacherTxnId($myDB);
                    $desc = "Teacher Food Order #$oid (" . count($items) . " meals: " . date('d M', strtotime($start_date)) . " to " . date('d M', strtotime($end_date)) . ")";
                    $safe_desc = mysqli_real_escape_string($myDB, $desc);
                    mysqli_query($myDB, "
                        INSERT INTO rrsv_food_wallet_ledger 
                        (student_id, tns_id, amount, type, description, created_at)
                        VALUES 
                        ('$teacher_code', '$txn_id', $total, 'Debit', '$safe_desc', NOW())
                    ");

                    mysqli_commit($myDB);
                    $success = "Food orders placed successfully! Total ₹" . number_format($total, 2) . " for " . count($items) . " meal items.";
                    $balance -= $total;
                } catch (Exception $e) {
                    mysqli_rollback($myDB);
                    $error = "Failed to place orders: " . $e->getMessage();
                }
            }
        }
    }
}

/* =========================================================
   7. CALENDAR DATA
========================================================= */
$events = [];
$cal_q = mysqli_query($myDB, "
    SELECT oi.order_date, oi.meal_type, SUM(oi.price) AS total, COUNT(*) AS cnt
    FROM rrsv_food_order_items oi
    JOIN rrsv_food_orders o ON o.id = oi.order_id
    WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
      AND oi.status = 'Confirmed'
    GROUP BY oi.order_date, oi.meal_type
");

while ($r = mysqli_fetch_assoc($cal_q)) {
    $m = $r['meal_type'];
    if ($m == 'Breakfast') {
        $color = '#22c55e';
    } elseif ($m == 'Lunch') {
        $color = '#f97316';
    } elseif ($m == 'Dinner') {
        $color = '#6366f1';
    } elseif ($m == 'Tiffin') {
        $color = '#14b8a6';
    } else {
        $color = '#64748b';
    }

    $events[] = [
        'title' => $m . ' ₹' . number_format($r['total'], 0),
        'start' => $r['order_date'],
        'backgroundColor' => $color,
        'borderColor' => $color
    ];
}

$teacher_img = !empty($teacher['image']) && file_exists('../../teacher_image/' . $teacher['image']) 
    ? '../../teacher_image/' . htmlspecialchars($teacher['image']) 
    : '../../libray/images/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Food Management - <?= htmlspecialchars($teacher['full_name']); ?></title>
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

    <style>
        .teacher-header-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .wallet-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(4px);
        }
        .wallet-amount {
            font-size: 18px;
            color: #fff;
        }

        /* TABS */
        .boxed-tabs {
            border-bottom: 2px solid #2a5298;
            margin-bottom: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .boxed-tabs > li > a {
            border: 1px solid #d2d6de;
            border-bottom: none;
            background: #f4f6f9;
            color: #495057;
            padding: 10px 18px;
            font-weight: 600;
            border-radius: 6px 6px 0 0;
            display: block;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }
        .boxed-tabs > li.active > a,
        .boxed-tabs > li.active > a:hover,
        .boxed-tabs > li.active > a:focus {
            background: #2a5298;
            color: #fff !important;
            border-color: #2a5298;
        }
        .boxed-tabs > li > a:hover {
            background: #e2e6ea;
            color: #2a5298;
        }

        .tab-content {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }

        .succWrap {
            padding: 12px 18px;
            margin-bottom: 20px;
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
            border-radius: 4px;
            font-weight: 500;
        }
        .errorWrap {
            padding: 12px 18px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
            border-radius: 4px;
            font-weight: 500;
        }

        /* Printable Monthly Bill Slip */
        @media print {
            body * {
                visibility: hidden;
            }
            #printMonthlySlip, #printMonthlySlip * {
                visibility: visible;
            }
            #printMonthlySlip {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none !important;
            }
        }

        .quick-btn {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .meal-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
        }
        .meal-card-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 6px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<?php include('../../include/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">

        <!-- Top Teacher Profile Card -->
        <div class="card teacher-header-card mb-4">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    
                    <!-- Left: Avatar & Teacher Info -->
                    <div class="col-md-7 col-sm-12 d-flex align-items-center">
                        <img src="<?= $teacher_img; ?>" alt="Teacher Avatar" 
                             style="width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; margin-right: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($teacher['full_name']); ?>&background=ffffff&color=1e3c72';">
                        <div>
                            <div class="d-flex align-items-center flex-wrap">
                                <h3 class="m-0 font-weight-bold text-white mr-2">
                                    <?= htmlspecialchars($teacher['full_name']); ?>
                                </h3>
                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px;">
                                    <?= htmlspecialchars($teacher['designation'] ?: 'Teacher'); ?>
                                </span>
                            </div>
                            <div class="text-white-50 mt-1" style="font-size: 13px;">
                                <span><i class="fa fa-id-badge"></i> Code: <b><?= $teacher_code; ?></b></span> &bull; 
                                <span><i class="fa fa-phone"></i> <?= htmlspecialchars($teacher['tech_phone_no'] ?: 'N/A'); ?></span>
                                <?php if (!empty($teacher['monthly_salary'])): ?>
                                    &bull; <span><i class="fa fa-money"></i> Salary: ₹<?= number_format($teacher['monthly_salary']); ?>/mo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Wallet & Back Link -->
                    <div class="col-md-5 col-sm-12 text-md-right mt-3 mt-md-0">
                        <div class="wallet-badge mr-2">
                            <i class="fa fa-google-wallet fa-lg"></i>
                            <span class="wallet-amount">
                                ₹ <span id="walletBalance"><?= number_format($balance, 2); ?></span>
                            </span>
                        </div>
                        <a href="teacher_list.php" class="btn btn-outline-light btn-sm font-weight-bold shadow-sm" style="border-radius: 20px;">
                            <i class="fa fa-arrow-left"></i> All Teachers
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Alert messages -->
        <?php if (!empty($success)): ?>
            <div class="succWrap shadow-sm">
                <i class="fa fa-check-circle mr-1"></i> <?= $success; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="errorWrap shadow-sm">
                <i class="fa fa-exclamation-triangle mr-1"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <!-- Main Card with Navigation Tabs -->
        <div class="card shadow-sm border-0" style="border-radius: 8px;">
            <div class="card-body p-0">

                <ul class="nav nav-tabs boxed-tabs px-3 pt-3">
                    <li class="active">
                        <a href="#recharge" data-toggle="tab"><i class="fa fa-plus-circle"></i> RECHARGE</a>
                    </li>
                    <li>
                        <a href="#recharge_history" data-toggle="tab"><i class="fa fa-history"></i> RECHARGE HISTORY</a>
                    </li>
                    <li>
                        <a href="#food_order" data-toggle="tab"><i class="fa fa-cutlery"></i> FOOD ORDER 📅</a>
                    </li>
                    <li>
                        <a href="#d_order" data-toggle="tab"><i class="fa fa-calendar"></i> CALENDAR ORDER 📅</a>
                    </li>
                    <li>
                        <a href="#order_summary" data-toggle="tab"><i class="fa fa-list-alt"></i> ORDER SUMMARY</a>
                    </li>
                    <li>
                        <a href="#wallet_ledger" data-toggle="tab"><i class="fa fa-exchange"></i> TRANSACTIONS</a>
                    </li>
                    <li>
                        <a href="#monthly_bill" data-toggle="tab"><i class="fa fa-file-text-o"></i> MONTHLY BILL / SALARY SLIP 🖨️</a>
                    </li>
                </ul>

                <div class="tab-content">

                    <!-- ========================================================
                         TAB 1: RECHARGE (With Teacher Salary Deduction option)
                    ======================================================== -->
                    <div class="tab-pane active" id="recharge">
                        <div class="row">
                            <div class="col-md-7">
                                <h5 class="font-weight-bold text-primary mb-3">
                                    <i class="fa fa-credit-card"></i> Add Funds to Teacher Food Wallet
                                </h5>
                                <p class="text-muted" style="font-size: 13px;">
                                    Recharge the teacher's wallet via Cash, Online / UPI, or record as a <b>Salary Deduction</b> to be automatically settled with monthly payroll.
                                </p>

                                <form method="post" id="rechargeForm">
                                    <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">
                                    <input type="hidden" name="teacher_id" value="<?= $teacher['id']; ?>">

                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 col-form-label font-weight-bold">Recharge Amount (₹) <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold">₹</span>
                                                </div>
                                                <input type="number" step="1" min="10" name="amount" class="form-control form-control-lg" placeholder="e.g. 500, 1000, 2000" required>
                                            </div>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-outline-secondary btn-xs mr-1" onclick="$('input[name=amount]').val(300)">+₹300</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs mr-1" onclick="$('input[name=amount]').val(500)">+₹500</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs mr-1" onclick="$('input[name=amount]').val(1000)">+₹1,000</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs mr-1" onclick="$('input[name=amount]').val(2000)">+₹2,000</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 col-form-label font-weight-bold">Payment Mode <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <select name="payment_mode" class="form-control" id="paymentModeSelect" required>
                                                <option value="Cash" selected>Cash Payment</option>
                                                <option value="Online / UPI">Online / UPI (Google Pay, PhonePe, Paytm)</option>
                                                <option value="Salary Deduction">Salary Deduction (Attach with Monthly Payroll)</option>
                                                <option value="Bank Transfer">Bank Transfer / NEFT</option>
                                            </select>
                                            <small id="salaryDeductHelp" class="text-info d-none mt-1">
                                                <i class="fa fa-info-circle"></i> This amount will be recorded as Salary Deduction and reflected in the Monthly Food Bill slip.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 col-form-label font-weight-bold">Note / Reference</label>
                                        <div class="col-md-8">
                                            <input type="text" name="note" class="form-control" placeholder="Optional remark (e.g. UPI Ref / Receipt # / Month advance)">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">
                                        <div class="col-md-8 offset-md-4">
                                            <button type="submit" name="recharge" class="btn btn-success font-weight-bold px-4 py-2 shadow-sm">
                                                <i class="fa fa-check-circle"></i> Complete Recharge
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-5">
                                <div class="card bg-light border-0 p-3" style="border-radius: 8px;">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fa fa-info-circle text-primary"></i> Teacher Food Account Summary</h6>
                                    <ul class="list-unstyled mb-0" style="font-size: 13px; line-height: 2;">
                                        <li><strong>Teacher:</strong> <?= htmlspecialchars($teacher['full_name']); ?></li>
                                        <li><strong>Designation:</strong> <?= htmlspecialchars($teacher['designation'] ?: 'Teacher'); ?></li>
                                        <li><strong>Food Account Code:</strong> <code><?= $teacher_code; ?></code></li>
                                        <li><strong>Current Balance:</strong> <span class="badge badge-success">₹ <?= number_format($balance, 2); ?></span></li>
                                        <?php if (!empty($teacher['monthly_salary'])): ?>
                                            <li><strong>Monthly Salary:</strong> ₹ <?= number_format($teacher['monthly_salary']); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                    <hr>
                                    <small class="text-muted">
                                        <i class="fa fa-shield"></i> All recharges generate an audit transaction ID and update the ledger immediately.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 2: RECHARGE HISTORY
                    ======================================================== -->
                    <div class="tab-pane" id="recharge_history">
                        <h5 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-history"></i> Recharge & Top-up History
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Date & Time</th>
                                        <th>Transaction ID</th>
                                        <th>Amount (₹)</th>
                                        <th>Type</th>
                                        <th>Payment Mode / Description</th>
                                        <th class="text-center" style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ri = 1;
                                    $rq = mysqli_query($myDB, "
                                        SELECT * FROM rrsv_food_wallet_ledger
                                        WHERE (student_id = '$teacher_code' OR student_id = '$teacher_id')
                                          AND type = 'Credit'
                                          AND is_delete = 'No'
                                        ORDER BY created_at DESC
                                    ");
                                    if (mysqli_num_rows($rq) == 0):
                                    ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                No recharge records found for this teacher.
                                            </td>
                                        </tr>
                                    <?php
                                    else:
                                        while ($rrow = mysqli_fetch_assoc($rq)):
                                    ?>
                                        <tr>
                                            <td><?= $ri++; ?></td>
                                            <td><?= date('d-m-Y h:i A', strtotime($rrow['created_at'])); ?></td>
                                            <td><code><?= htmlspecialchars($rrow['tns_id'] ?: 'N/A'); ?></code></td>
                                            <td class="font-weight-bold text-success">+ ₹<?= number_format((float)$rrow['amount'], 2); ?></td>
                                            <td><span class="badge badge-success"><?= $rrow['type']; ?></span></td>
                                            <td><?= htmlspecialchars($rrow['description']); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-xs delete-ledger text-white" 
                                                        data-id="<?= $rrow['id']; ?>" 
                                                        data-amount="<?= $rrow['amount']; ?>"
                                                        title="Delete Recharge (Admin password required)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 3: FOOD ORDER (With Quick Mon-Sat lunch buttons)
                    ======================================================== -->
                    <div class="tab-pane" id="food_order">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <h5 class="font-weight-bold text-primary m-0">
                                <i class="fa fa-calendar-check-o"></i> Book Meals for Teacher
                            </h5>
                            <div class="mt-2 mt-md-0">
                                <button type="button" class="btn btn-outline-primary btn-sm quick-btn font-weight-bold" id="btnQuickWeekdayLunch">
                                    <i class="fa fa-bolt"></i> Quick: Mon-Sat Lunch Only
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm quick-btn font-weight-bold" id="btnQuickSelectAllLunch">
                                    <i class="fa fa-check-square-o"></i> Select All Lunch
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-btn" id="btnClearAllMeals">
                                    <i class="fa fa-times"></i> Clear All
                                </button>
                            </div>
                        </div>

                        <form method="post" id="teacherFoodOrderForm">
                            <div class="card bg-light border-0 p-3 mb-3" style="border-radius: 6px;">
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                                        <label class="font-weight-bold mb-1">Start Date:</label>
                                        <input type="date" name="start_date" id="order_start_date" class="form-control" required min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                                        <label class="font-weight-bold mb-1">End Date:</label>
                                        <input type="date" name="end_date" id="order_end_date" class="form-control" required min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-4 col-sm-12 mt-3 mt-md-0">
                                        <label class="d-none d-md-block mb-1">&nbsp;</label>
                                        <button type="submit" name="submit_orders" class="btn btn-primary btn-block font-weight-bold shadow-sm py-2">
                                            <i class="fa fa-shopping-cart"></i> Place Orders Now
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered meal-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 140px;">Day</th>
                                            <?php foreach ($meals as $m): ?>
                                                <th>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span><?= $m; ?></span>
                                                        <label class="m-0 font-weight-normal" style="font-size: 11px;">
                                                            <input type="checkbox" class="meal-check-all ml-1" data-meal="<?= $m; ?>"> All
                                                        </label>
                                                    </div>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($days as $day): ?>
                                            <tr data-day="<?= $day; ?>">
                                                <td class="font-weight-bold bg-light">
                                                    <?= $day; ?>
                                                    <?php if ($day === 'Sunday'): ?>
                                                        <br><small class="text-danger">(Sunday)</small>
                                                    <?php endif; ?>
                                                </td>
                                                <?php foreach ($meals as $meal): ?>
                                                    <td>
                                                        <?php
                                                        $qm = mysqli_query($myDB, "
                                                            SELECT p.id, p.package_name, p.price
                                                            FROM rrsv_menu_package p
                                                            JOIN rrsv_food_daily_menu d ON d.food_id = p.id
                                                            WHERE d.day_name = '$day' AND d.meal_type = '$meal'
                                                        ");
                                                        if (mysqli_num_rows($qm) == 0):
                                                            echo "<span class='text-muted' style='font-size: 11px;'>—</span>";
                                                        else:
                                                            while ($r = mysqli_fetch_assoc($qm)):
                                                        ?>
                                                            <div class="meal-card-item">
                                                                <label class="m-0 d-flex align-items-center cursor-pointer">
                                                                    <input type="checkbox" 
                                                                           class="meal-checkbox mr-2" 
                                                                           data-day="<?= $day; ?>" 
                                                                           data-meal="<?= $meal; ?>" 
                                                                           name="orders[<?= $day; ?>][<?= $meal; ?>][]" 
                                                                           value="<?= $r['id']; ?>"
                                                                           data-price="<?= $r['price']; ?>">
                                                                    <span>
                                                                        <b><?= htmlspecialchars($r['package_name']); ?></b>
                                                                        <span class="text-primary font-weight-bold ml-1">₹<?= number_format($r['price'], 0); ?></span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        <?php 
                                                            endwhile;
                                                        endif;
                                                        ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                    <!-- ========================================================
                         TAB 4: CALENDAR ORDER
                    ======================================================== -->
                    <div class="tab-pane" id="d_order">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold text-primary m-0">
                                <i class="fa fa-calendar"></i> Monthly Food Order Calendar
                            </h5>
                            <div>
                                <span class="badge mr-1" style="background:#22c55e; color:#fff;">Breakfast</span>
                                <span class="badge mr-1" style="background:#f97316; color:#fff;">Lunch</span>
                                <span class="badge mr-1" style="background:#6366f1; color:#fff;">Dinner</span>
                                <span class="badge mr-1" style="background:#14b8a6; color:#fff;">Tiffin</span>
                            </div>
                        </div>
                        <p class="text-muted" style="font-size: 13px;">
                            Click any meal badge on the calendar to see details or cancel individual items with an automatic wallet refund.
                        </p>
                        
                        <div id="calendar" style="max-width: 100%; margin: 15px auto;"></div>

                        <!-- Calendar Orders Modal -->
                        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title font-weight-bold"><i class="fa fa-cutlery"></i> Orders on <span id="modalDateDisplay"></span></h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="orderBody">
                                        <div class="text-center p-3 text-muted">Loading orders...</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 5: ORDER SUMMARY
                    ======================================================== -->
                    <div class="tab-pane" id="order_summary">
                        <h5 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-list-alt"></i> Detailed Food Orders Breakdown
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Food Package</th>
                                        <th>Meal</th>
                                        <th>Meal Date</th>
                                        <th>Status</th>
                                        <th class="text-right">Price (₹)</th>
                                        <th class="text-center" style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $oi = 1;
                                    $grandTotal = 0;
                                    $currOrder = 0;

                                    $oq = mysqli_query($myDB, "
                                        SELECT 
                                            o.id AS order_id,
                                            o.created_at,
                                            i.id AS item_id,
                                            i.order_date,
                                            i.meal_type,
                                            i.price,
                                            i.quantity,
                                            i.status,
                                            m.package_name
                                        FROM rrsv_food_orders o
                                        JOIN rrsv_food_order_items i ON i.order_id = o.id
                                        JOIN rrsv_menu_package m ON m.id = i.food_id
                                        WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
                                        ORDER BY o.id DESC, i.order_date ASC
                                    ");

                                    if (mysqli_num_rows($oq) == 0):
                                    ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No food orders recorded yet.</td>
                                        </tr>
                                    <?php
                                    else:
                                        while ($orow = mysqli_fetch_assoc($oq)):
                                            if ($currOrder != $orow['order_id']):
                                                $currOrder = $orow['order_id'];
                                    ?>
                                                <tr style="background:#edf2f7; font-weight:bold;">
                                                    <td colspan="7" class="text-dark">
                                                        <i class="fa fa-receipt text-primary"></i> Order #<?= $orow['order_id']; ?> 
                                                        &bull; Placed: <?= date('d-m-Y', strtotime($orow['created_at'])); ?>
                                                    </td>
                                                </tr>
                                    <?php
                                            endif;
                                            if ($orow['status'] === 'Confirmed') {
                                                $grandTotal += (float)$orow['price'];
                                            }
                                    ?>
                                        <tr>
                                            <td><?= $oi++; ?></td>
                                            <td class="font-weight-bold"><?= htmlspecialchars($orow['package_name']); ?></td>
                                            <td><span class="badge badge-light border"><?= $orow['meal_type']; ?></span></td>
                                            <td><?= date('d-m-Y (D)', strtotime($orow['order_date'])); ?></td>
                                            <td>
                                                <?php if ($orow['status'] === 'Confirmed'): ?>
                                                    <span class="badge badge-success">Confirmed</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Cancelled</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                <?php if ($orow['status'] === 'Confirmed'): ?>
                                                    <span class="text-danger">- ₹<?= number_format((float)$orow['price'], 2); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted" style="text-decoration: line-through;">₹<?= number_format((float)$orow['price'], 2); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($orow['status'] === 'Confirmed'): ?>
                                                    <button class="btn btn-danger btn-xs btn-cancel-order" data-id="<?= $orow['item_id']; ?>" title="Cancel item & refund">
                                                        <i class="fa fa-times"></i> Cancel
                                                    </button>
                                                <?php else: ?>
                                                    <small class="text-muted">Refunded</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php
                                        endwhile;
                                    ?>
                                        <tr style="background:#e0f2fe; font-weight:bold;">
                                            <td colspan="5" class="text-right text-dark">TOTAL ACTIVE FOOD CONSUMPTION:</td>
                                            <td class="text-right text-primary" style="font-size: 16px;">₹ <?= number_format($grandTotal, 2); ?></td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 6: TRANSACTIONS / LEDGER
                    ======================================================== -->
                    <div class="tab-pane" id="wallet_ledger">
                        <h5 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-exchange"></i> Teacher Food Wallet Ledger
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Date & Time</th>
                                        <th>Txn ID</th>
                                        <th>Description</th>
                                        <th class="text-right">Credit / Debit (₹)</th>
                                        <th class="text-center" style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $li = 1;
                                    $creditSum = 0;
                                    $debitSum = 0;

                                    $lq = mysqli_query($myDB, "
                                        SELECT * FROM rrsv_food_wallet_ledger
                                        WHERE (student_id = '$teacher_code' OR student_id = '$teacher_id')
                                          AND is_delete = 'No'
                                        ORDER BY created_at ASC
                                    ");

                                    if (mysqli_num_rows($lq) == 0):
                                    ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No transactions recorded yet.</td>
                                        </tr>
                                    <?php
                                    else:
                                        while ($lrow = mysqli_fetch_assoc($lq)):
                                            $amt = (float)$lrow['amount'];
                                            if ($lrow['type'] === 'Credit') {
                                                $creditSum += $amt;
                                            } else {
                                                $debitSum += $amt;
                                            }
                                    ?>
                                        <tr>
                                            <td><?= $li++; ?></td>
                                            <td><?= date('d-m-Y h:i A', strtotime($lrow['created_at'])); ?></td>
                                            <td><code><?= htmlspecialchars($lrow['tns_id'] ?: 'N/A'); ?></code></td>
                                            <td><?= htmlspecialchars($lrow['description']); ?></td>
                                            <td class="text-right font-weight-bold">
                                                <?php if ($lrow['type'] === 'Credit'): ?>
                                                    <span class="text-success">+ ₹<?= number_format($amt, 2); ?></span>
                                                <?php else: ?>
                                                    <span class="text-danger">- ₹<?= number_format($amt, 2); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($lrow['type'] === 'Credit'): ?>
                                                    <button class="btn btn-danger btn-xs delete-ledger text-white" 
                                                            data-id="<?= $lrow['id']; ?>" 
                                                            data-amount="<?= $lrow['amount']; ?>"
                                                            title="Delete transaction (Admin password required)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                        <tr style="background:#fff3cd; font-weight:bold;">
                                            <td colspan="4" class="text-right">CALCULATED NET BALANCE:</td>
                                            <td class="text-right text-dark" style="font-size: 16px;">
                                                ₹ <?= number_format($creditSum - $debitSum, 2); ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================
                         TAB 7: MONTHLY BILL / SALARY ATTACHMENT SLIP
                    ======================================================== -->
                    <div class="tab-pane" id="monthly_bill">
                        <?php
                        $selected_month = $_GET['bill_month'] ?? date('Y-m');
                        $bill_start = $selected_month . '-01';
                        $bill_end = date('Y-m-t', strtotime($bill_start));

                        $mb_query = mysqli_query($myDB, "
                            SELECT 
                                i.order_date,
                                i.meal_type,
                                i.price,
                                m.package_name
                            FROM rrsv_food_order_items i
                            JOIN rrsv_food_orders o ON o.id = i.order_id
                            JOIN rrsv_menu_package m ON m.id = i.food_id
                            WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
                              AND i.status = 'Confirmed'
                              AND i.order_date BETWEEN '$bill_start' AND '$bill_end'
                            ORDER BY i.order_date ASC, FIELD(i.meal_type, 'Breakfast', 'Lunch', 'Dinner', 'Tiffin')
                        ");

                        $month_meals = [];
                        $month_total = 0;
                        while ($mb_row = mysqli_fetch_assoc($mb_query)) {
                            $month_meals[] = $mb_row;
                            $month_total += (float)$mb_row['price'];
                        }
                        ?>

                        <div class="no-print d-flex justify-content-between align-items-center mb-4 flex-wrap bg-light p-3" style="border-radius: 6px;">
                            <form method="get" class="form-inline">
                                <input type="hidden" name="teacher_id" value="<?= $teacher['id']; ?>">
                                <label class="font-weight-bold mr-2">Select Month:</label>
                                <input type="month" name="bill_month" class="form-control mr-2" value="<?= htmlspecialchars($selected_month); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-filter"></i> Load Month
                                </button>
                            </form>
                            <div>
                                <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" onclick="window.print()">
                                    <i class="fa fa-print"></i> Print Salary Food Slip
                                </button>
                            </div>
                        </div>

                        <!-- Printable Statement Container -->
                        <div id="printMonthlySlip" class="border p-4 bg-white shadow-sm" style="border-radius: 6px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                            <div class="text-center border-bottom pb-3 mb-3">
                                <h3 class="m-0 font-weight-bold text-dark" style="letter-spacing: 0.5px;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h3>
                                <p class="m-0 text-muted" style="font-size: 13px;">Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012</p>
                                <p class="m-0 text-muted" style="font-size: 13px;">Baidyadanga, Rasulpur, Memari, Purba Bardhaman - 713151</p>
                                <div class="badge badge-primary px-3 py-2 mt-2 text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                                    Teacher Monthly Food Bill & Salary Deduction Slip
                                </div>
                            </div>

                            <div class="row mb-3" style="font-size: 14px;">
                                <div class="col-6">
                                    <table class="table table-sm table-borderless m-0">
                                        <tr>
                                            <td style="width: 140px;"><strong>Teacher Name:</strong></td>
                                            <td><?= htmlspecialchars($teacher['full_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Designation:</strong></td>
                                            <td><?= htmlspecialchars($teacher['designation'] ?: 'Teacher'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Teacher ID:</strong></td>
                                            <td><?= $teacher_code; ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6 text-right">
                                    <table class="table table-sm table-borderless m-0">
                                        <tr>
                                            <td><strong>Billing Month:</strong></td>
                                            <td class="font-weight-bold text-primary"><?= date('F Y', strtotime($bill_start)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone No:</strong></td>
                                            <td><?= htmlspecialchars($teacher['tech_phone_no'] ?: 'N/A'); ?></td>
                                        </tr>
                                        <?php if (!empty($teacher['monthly_salary'])): ?>
                                        <tr>
                                            <td><strong>Monthly Salary:</strong></td>
                                            <td>₹ <?= number_format($teacher['monthly_salary']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <h6 class="font-weight-bold text-dark border-bottom pb-1 mb-2">Itemized Meal Consumption Details</h6>
                            <table class="table table-bordered table-sm align-middle" style="font-size: 13px;">
                                <thead style="background: #f1f5f9;">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">#</th>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Meal Type</th>
                                        <th>Menu Package</th>
                                        <th class="text-right">Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($month_meals)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">No meals recorded for this month.</td>
                                        </tr>
                                    <?php else: 
                                        $bi = 1;
                                        foreach ($month_meals as $mm):
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $bi++; ?></td>
                                            <td><?= date('d-m-Y', strtotime($mm['order_date'])); ?></td>
                                            <td><?= date('l', strtotime($mm['order_date'])); ?></td>
                                            <td><b><?= $mm['meal_type']; ?></b></td>
                                            <td><?= htmlspecialchars($mm['package_name']); ?></td>
                                            <td class="text-right">₹ <?= number_format((float)$mm['price'], 2); ?></td>
                                        </tr>
                                    <?php 
                                        endforeach; 
                                    endif; 
                                    ?>
                                    <tr style="background: #e2e8f0; font-weight: bold;">
                                        <td colspan="5" class="text-right">TOTAL MONTHLY MEAL CONSUMPTION (<?= count($month_meals); ?> Meals):</td>
                                        <td class="text-right text-primary" style="font-size: 15px;">₹ <?= number_format($month_total, 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row mt-4 pt-3">
                                <div class="col-7">
                                    <div class="p-2 border bg-light" style="font-size: 12px; border-radius: 4px;">
                                        <b>Notice for Accounts:</b><br>
                                        This statement certifies that <b><?= htmlspecialchars($teacher['full_name']); ?></b> consumed <b><?= count($month_meals); ?> meals</b> during <b><?= date('F Y', strtotime($bill_start)); ?></b> amounting to <b>₹<?= number_format($month_total, 2); ?></b>.
                                    </div>
                                </div>
                                <div class="col-5">
                                    <table class="table table-sm table-bordered" style="font-size: 13px;">
                                        <tr>
                                            <td><strong>Gross Food Billed:</strong></td>
                                            <td class="text-right font-weight-bold">₹ <?= number_format($month_total, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Net Amount Due/Deductible:</strong></td>
                                            <td class="text-right font-weight-bold text-danger">₹ <?= number_format($month_total, 2); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-5 pt-4 text-center" style="font-size: 13px;">
                                <div class="col-4">
                                    <div style="border-top: 1px dashed #333; padding-top: 5px;">
                                        <strong>Teacher's Signature</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div style="border-top: 1px dashed #333; padding-top: 5px;">
                                        <strong>Kitchen Manager</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div style="border-top: 1px dashed #333; padding-top: 5px;">
                                        <strong>Accountant / Headmaster</strong>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>

<script>
let calendar;
let calendarLoaded = false;

// Initialize FullCalendar on tab click
$('a[href="#d_order"]').on('shown.bs.tab', function () {
    if (calendarLoaded) {
        calendar.updateSize();
        return;
    }

    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: <?= json_encode($events); ?>,
        eventClick: function (info) {
            let date = info.event.startStr.split('T')[0];
            $('#modalDateDisplay').text(date);
            $('#orderModal').modal('show');
            $('#orderBody').html('<div class="text-center p-3 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading orders...</div>');

            $.get('?ajax=get_orders&date=' + date + '&teacher_id=<?= $teacher['id']; ?>', function(res) {
                if (!res || res.length === 0) {
                    $('#orderBody').html('<div class="alert alert-info text-center m-0">No orders found on ' + date + '</div>');
                    return;
                }

                let html = '<table class="table table-bordered table-striped align-middle">';
                html += '<thead class="thead-light"><tr><th>Meal</th><th>Package</th><th>Price</th><th>Action</th></tr></thead><tbody>';

                res.forEach(function(item) {
                    html += '<tr>';
                    html += '<td><b>' + item.meal_type + '</b></td>';
                    html += '<td>' + item.package_name + '</td>';
                    html += '<td>₹' + parseFloat(item.price).toFixed(2) + '</td>';
                    html += '<td>';
                    if (item.status === 'Confirmed') {
                        html += '<button class="btn btn-danger btn-xs btn-cancel-order" data-id="' + item.id + '"><i class="fa fa-times"></i> Cancel</button>';
                    } else {
                        html += '<span class="badge badge-secondary">Cancelled</span>';
                    }
                    html += '</td></tr>';
                });

                html += '</tbody></table>';
                $('#orderBody').html(html);
            }, 'json').fail(function() {
                $('#orderBody').html('<div class="alert alert-danger text-center m-0">Error fetching orders</div>');
            });
        }
    });

    calendar.render();
    calendarLoaded = true;
});

// Auto refresh wallet balance
function refreshBalance() {
    $.ajax({
        url: 'get_balance.php',
        type: 'GET',
        data: { teacher_id: '<?= $teacher['id']; ?>' },
        dataType: 'json',
        success: function (res) {
            if (res && res.balance !== undefined) {
                $('#walletBalance').text(parseFloat(res.balance).toFixed(2));
            }
        }
    });
}
refreshBalance();
setInterval(refreshBalance, 5000);

// Payment mode change helper
$('#paymentModeSelect').on('change', function() {
    if ($(this).val() === 'Salary Deduction') {
        $('#salaryDeductHelp').removeClass('d-none');
    } else {
        $('#salaryDeductHelp').addClass('d-none');
    }
});

// Check-all by meal type
$(document).on('change', '.meal-check-all', function () {
    let meal = $(this).data('meal');
    let isChecked = $(this).is(':checked');
    $('.meal-checkbox[data-meal="' + meal + '"]').prop('checked', isChecked);
});

// Teacher Quick helper 1: Mon-Sat Lunch Only
$('#btnQuickWeekdayLunch').on('click', function() {
    $('.meal-checkbox').prop('checked', false);
    $('.meal-check-all').prop('checked', false);

    const weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    weekdays.forEach(function(day) {
        $('.meal-checkbox[data-day="' + day + '"][data-meal="Lunch"]').prop('checked', true);
    });

    Swal.fire({
        icon: 'success',
        title: 'Lunches Selected',
        text: 'Monday to Saturday lunch meals selected automatically.',
        timer: 1500,
        showConfirmButton: false
    });
});

// Teacher Quick helper 2: Select All Lunches
$('#btnQuickSelectAllLunch').on('click', function() {
    $('.meal-checkbox[data-meal="Lunch"]').prop('checked', true);
    $('.meal-check-all[data-meal="Lunch"]').prop('checked', true);
});

// Teacher Quick helper 3: Clear All
$('#btnClearAllMeals').on('click', function() {
    $('.meal-checkbox').prop('checked', false);
    $('.meal-check-all').prop('checked', false);
});

// Form submit validation
$('#teacherFoodOrderForm').on('submit', function(e) {
    let checkedCount = $('.meal-checkbox:checked').length;
    if (checkedCount === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Food Selected',
            text: 'Please select at least one meal package before placing your order.'
        });
        e.preventDefault();
        return false;
    }
});

// Delete ledger record (AJAX with Admin Password)
$(document).on('click', '.delete-ledger', function () {
    let id = $(this).data('id');
    let btn = $(this);

    Swal.fire({
        title: 'Delete Recharge Record?',
        text: 'This will reverse the wallet balance. Please enter Admin Password to proceed:',
        input: 'password',
        inputPlaceholder: 'Enter Admin Password',
        showCancelButton: true,
        confirmButtonText: 'Confirm Delete',
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;
        let password = result.value;

        if (!password) {
            Swal.fire('Error', 'Password is required to delete ledger records.', 'error');
            return;
        }

        $.ajax({
            url: window.location.href,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: 'delete_ledger',
                id: id,
                password: password
            },
            success: function (res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Ledger record removed and balance updated.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
                    refreshBalance();
                } else {
                    Swal.fire('Error', res.msg || 'Deletion failed', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Server error occurred while deleting record.', 'error');
            }
        });
    });
});

// Cancel Order Item (AJAX)
$(document).on('click', '.btn-cancel-order', function() {
    let id = $(this).data('id');
    let btn = $(this);

    Swal.fire({
        title: 'Cancel this meal order?',
        text: 'The meal price will be refunded back to the teacher\'s wallet.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, Cancel & Refund',
        cancelButtonText: 'No'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: window.location.href,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: 'cancel_order',
                id: id
            },
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Cancelled',
                        text: res.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    btn.closest('tr').find('td:nth-child(5)').html('<span class="badge badge-secondary">Cancelled</span>');
                    btn.replaceWith('<small class="text-muted">Refunded</small>');
                    refreshBalance();
                } else {
                    Swal.fire('Error', res.msg || 'Cancellation failed', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Server communication error', 'error');
            }
        });
    });
});

// Auto hide alerts after 4 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.succWrap').slideUp(500);
    }, 4000);
});
</script>

<?php include('../../include/footer.php'); ?>
</body>
</html>
