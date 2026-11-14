<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

include '../../../include/config.php';
include '../../dbcon.php';

// Access Control: Only logged-in teachers
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}

$teacher_session = $_SESSION['user_data'];
$teacher_id = intval($_SESSION['user_id'] ?? ($teacher_session['id'] ?? 0));
$teacher_code = 'T-' . $teacher_id;

// Fetch fresh teacher data
$t_query = mysqli_query($con, "SELECT * FROM rrsv_teacher WHERE id = $teacher_id LIMIT 1");
$teacher = mysqli_fetch_assoc($t_query);
if (!$teacher) {
    header("Location: ../../index.php?error=Teacher profile not found");
    exit;
}

// Function to generate transaction ID
function generateTeacherTxnIdPortal($con) {
    $date = date('ymd');
    $res = mysqli_query(
        $con,
        "SELECT COUNT(*) AS total 
         FROM rrsv_food_wallet_ledger 
         WHERE DATE(created_at) = CURDATE()"
    );
    $row = mysqli_fetch_assoc($res);
    $seq = str_pad(($row['total'] ?? 0) + 1, 3, '0', STR_PAD_LEFT);
    return "TXN{$date}{$seq}";
}

/* =========================================================
   AJAX: GET ORDERS FOR CALENDAR MODAL
========================================================= */
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_orders') {
    header('Content-Type: application/json');
    $date = mysqli_real_escape_string($con, $_GET['date'] ?? '');

    $q = mysqli_query($con, "
        SELECT oi.id, oi.meal_type, p.package_name, oi.price, oi.status
        FROM rrsv_food_order_items oi
        JOIN rrsv_food_orders o ON o.id = oi.order_id
        JOIN rrsv_menu_package p ON p.id = oi.food_id
        WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
          AND oi.order_date = '$date'
        ORDER BY FIELD(oi.meal_type, 'Breakfast', 'Lunch', 'Dinner', 'Tiffin'), oi.id ASC
    ");

    $data = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $data[] = $r;
    }
    echo json_encode($data);
    exit;
}

/* =========================================================
   ORDER SUBMISSION
========================================================= */
$wallet_q = mysqli_query($con, "SELECT balance FROM rrsv_food_wallet WHERE student_id = '$teacher_code' OR student_id = '$teacher_id' LIMIT 1");
$wallet = mysqli_fetch_assoc($wallet_q);
$balance = (float)($wallet['balance'] ?? 0);

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_orders'])) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $orders = $_POST['orders'] ?? [];

    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    if (empty($start_date) || empty($end_date)) {
        $error = "Please specify both Start Date and End Date.";
    } elseif (strtotime($start_date) < strtotime(date('Y-m-d'))) {
        $error = "Start date cannot be in the past.";
    } elseif (strtotime($end_date) < strtotime($start_date)) {
        $error = "End date cannot be earlier than start date.";
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
                    // Check duplicate
                    $chk = mysqli_fetch_assoc(mysqli_query($con, "
                        SELECT COUNT(*) AS c
                        FROM rrsv_food_order_items oi
                        JOIN rrsv_food_orders o ON o.id = oi.order_id
                        WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
                          AND oi.order_date = '$date'
                          AND oi.meal_type = '$meal'
                          AND oi.status = 'Confirmed'
                    "));

                    if ($chk['c'] > 0) {
                        $error = "Already ordered $meal on " . date('d-m-Y (l)', strtotime($date));
                        break 2;
                    }

                    foreach ($food_ids as $fid) {
                        $p = mysqli_fetch_assoc(mysqli_query($con, "SELECT price FROM rrsv_menu_package WHERE id = '$fid' LIMIT 1"));
                        $price = (float)($p['price'] ?? 0);
                        $total += $price;
                        $items[] = [$fid, $meal, $date, $price];
                    }
                }
            }
        }

        if (empty($error)) {
            if (empty($items)) {
                $error = "Please select at least one meal package.";
            } elseif ($balance < $total) {
                $error = "Insufficient wallet balance. Required: ₹" . number_format($total, 2) . ", Available: ₹" . number_format($balance, 2) . ". Please request a wallet recharge from school admin.";
            } else {
                mysqli_begin_transaction($con);
                try {
                    mysqli_query($con, "
                        INSERT INTO rrsv_food_orders (student_id, start_date, end_date, total_amount, status, created_at)
                        VALUES ('$teacher_code', '$start_date', '$end_date', $total, 'Confirmed', NOW())
                    ");
                    $oid = mysqli_insert_id($con);

                    foreach ($items as $i) {
                        mysqli_query($con, "
                            INSERT INTO rrsv_food_order_items (order_id, food_id, meal_type, order_date, price, quantity, status)
                            VALUES ($oid, {$i[0]}, '{$i[1]}', '{$i[2]}', {$i[3]}, 1, 'Confirmed')
                        ");
                    }

                    mysqli_query($con, "
                        UPDATE rrsv_food_wallet 
                        SET balance = balance - $total 
                        WHERE student_id = '$teacher_code' OR student_id = '$teacher_id'
                    ");

                    $txn_id = generateTeacherTxnIdPortal($con);
                    $note = "Teacher Food Order #" . $oid . " (" . count($items) . " meals)";
                    $safe_note = mysqli_real_escape_string($con, $note);
                    mysqli_query($con, "
                        INSERT INTO rrsv_food_wallet_ledger (student_id, tns_id, amount, type, description, created_at)
                        VALUES ('$teacher_code', '$txn_id', $total, 'Debit', '$safe_note', NOW())
                    ");

                    mysqli_commit($con);
                    $success = "Order placed successfully! Total ₹" . number_format($total, 2) . " for " . count($items) . " meals.";
                    $balance -= $total;
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    $error = "Order failed: " . $e->getMessage();
                }
            }
        }
    }
}

/* =========================================================
   CALENDAR DATA
========================================================= */
$events = [];
$q = mysqli_query($con, "
    SELECT oi.order_date, oi.meal_type, SUM(oi.price) AS total
    FROM rrsv_food_order_items oi
    JOIN rrsv_food_orders o ON o.id = oi.order_id
    WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
      AND oi.status = 'Confirmed'
    GROUP BY oi.order_date, oi.meal_type
");

while ($r = mysqli_fetch_assoc($q)) {
    if ($r['meal_type'] == 'Breakfast') {
        $color = '#22c55e';
    } elseif ($r['meal_type'] == 'Lunch') {
        $color = '#f97316';
    } elseif ($r['meal_type'] == 'Dinner') {
        $color = '#6366f1';
    } elseif ($r['meal_type'] == 'Tiffin') {
        $color = '#14b8a6';
    } else {
        $color = '#64748b';
    }

    $events[] = [
        'title' => $r['meal_type'] . " ₹" . number_format($r['total'], 0),
        'start' => $r['order_date'],
        'backgroundColor' => $color,
        'borderColor' => $color
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Teacher Food Portal | RRSV</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <?php include('../../header.php'); ?>

    <style>
        .food-banner-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            padding: 16px 22px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .wallet-badge-portal {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 18px;
            border-radius: 25px;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .wallet-amount-portal {
            font-size: 18px;
            color: #fff;
        }
        .boxed-tabs > li > a {
            font-weight: 600;
            border-radius: 4px 4px 0 0;
        }
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover {
            border-top: 3px solid #2a5298;
            font-weight: 700;
            color: #2a5298;
        }
        .succWrap {
            padding: 10px 15px;
            margin-bottom: 15px;
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
            border-radius: 4px;
        }
        .errorWrap {
            padding: 10px 15px;
            margin-bottom: 15px;
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
            border-radius: 4px;
        }
        .quick-helper-btn {
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <!-- Header -->
    <header class="main-header">
        <a href="#" class="logo">
            <span class="logo-mini"><b>RRSV</b></span>
            <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
        </a>
        <?php include '../../top_navbar.php'; ?>
    </header>

    <!-- Sidebar -->
    <?php include '../../left_navbar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-body" style="padding: 15px;">

                            <!-- Banner Card -->
                            <div class="food-banner-card">
                                <div class="row align-items-center">
                                    <div class="col-md-8 col-sm-12">
                                        <h3 style="margin: 0 0 5px 0; font-weight: bold;">
                                            <i class="fa fa-cutlery"></i> Order Your Healthy Food
                                        </h3>
                                        <div style="font-size: 14px; opacity: 0.9;">
                                            <?= htmlspecialchars($teacher['full_name']); ?> 
                                            (<?= htmlspecialchars($teacher['designation'] ?: 'Teacher'); ?> &bull; <?= $teacher_code; ?>)
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 text-right" style="margin-top: 10px;">
                                        <div class="wallet-badge-portal">
                                            <i class="fa fa-google-wallet fa-lg"></i>
                                            <span class="wallet-amount-portal">
                                                ₹ <span id="walletBalance"><?= number_format($balance, 2); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="padding: 6px 12px; background: #fff3cd; color: #856404; border-radius: 4px; margin-bottom: 15px; font-size: 13px;">
                                <strong><i class="fa fa-info-circle"></i> Note:</strong> Food orders must be placed before 12:00 midnight for the following days.
                            </div>

                            <?php if (!empty($success)): ?>
                                <div class="succWrap"><i class="fa fa-check-circle"></i> <?= $success; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($error)): ?>
                                <div class="errorWrap"><i class="fa fa-exclamation-circle"></i> <?= $error; ?></div>
                            <?php endif; ?>

                            <!-- Navigation Tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="active"><a href="#food_order" role="tab" data-toggle="tab"><i class="fa fa-calendar-plus-o"></i> Food Order</a></li>
                                <li><a href="#d_order" role="tab" data-toggle="tab"><i class="fa fa-calendar"></i> Calendar Order 📅</a></li>
                                <li><a href="#recharge_history" role="tab" data-toggle="tab"><i class="fa fa-history"></i> Recharge History</a></li>
                                <li><a href="#order_summary" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> Order Summary</a></li>
                                <li><a href="#wallet_ledger" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i> Your Transactions</a></li>
                                <li><a href="#monthly_slip" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i> Monthly Statement</a></li>
                            </ul>

                            <div class="tab-content" style="margin-top: 15px;">

                                <!-- ================= TAB 1: FOOD ORDER ================= -->
                                <div class="tab-pane active" id="food_order">
                                    <div class="d-flex justify-content-between align-items-center mb-2" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <h4 style="margin: 0; font-weight: bold; color: #2a5298;">Select Meals & Date Range</h4>
                                        <div>
                                            <button type="button" class="btn btn-primary btn-xs quick-helper-btn" id="btnQuickMonSatLunch">
                                                <i class="fa fa-bolt"></i> Mon-Sat Lunch Only
                                            </button>
                                            <button type="button" class="btn btn-info btn-xs quick-helper-btn" id="btnQuickAllLunch">
                                                Select All Lunch
                                            </button>
                                            <button type="button" class="btn btn-default btn-xs quick-helper-btn" id="btnClearSelection">
                                                Clear All
                                            </button>
                                        </div>
                                    </div>

                                    <form method="post" id="portalFoodOrderForm">
                                        <div class="row" style="margin-bottom: 15px;">
                                            <div class="col-md-4 col-sm-6">
                                                <label>Start Date:</label>
                                                <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label>End Date:</label>
                                                <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-4 col-sm-12" style="margin-top: 25px;">
                                                <button type="submit" name="submit_orders" class="btn btn-success btn-block" style="font-weight: bold;">
                                                    <i class="fa fa-check"></i> Place Food Order
                                                </button>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead style="background: #f4f6f9;">
                                                    <tr>
                                                        <th style="width: 130px;">Day</th>
                                                        <?php foreach ($meals as $m): ?>
                                                            <th>
                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                    <span><?= $m; ?></span>
                                                                    <label style="font-weight: normal; margin: 0; font-size: 11px;">
                                                                        <input type="checkbox" class="meal-check-all" data-meal="<?= $m; ?>"> All
                                                                    </label>
                                                                </div>
                                                            </th>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($days as $day): ?>
                                                        <tr data-day="<?= $day; ?>">
                                                            <td><b><?= $day; ?></b></td>
                                                            <?php foreach ($meals as $meal): ?>
                                                                <td>
                                                                    <?php
                                                                    $qm = mysqli_query($con, "
                                                                        SELECT p.id, p.package_name, p.price
                                                                        FROM rrsv_menu_package p
                                                                        JOIN rrsv_food_daily_menu d ON d.food_id = p.id
                                                                        WHERE d.day_name = '$day' AND d.meal_type = '$meal'
                                                                    ");
                                                                    if (mysqli_num_rows($qm) == 0):
                                                                        echo "<span class='text-muted' style='font-size:11px;'>—</span>";
                                                                    else:
                                                                        while ($r = mysqli_fetch_assoc($qm)):
                                                                    ?>
                                                                        <div style="margin-bottom: 5px; font-size: 13px;">
                                                                            <label style="font-weight: normal; cursor: pointer;">
                                                                                <input type="checkbox" 
                                                                                       class="meal-checkbox" 
                                                                                       data-day="<?= $day; ?>" 
                                                                                       data-meal="<?= $meal; ?>" 
                                                                                       name="orders[<?= $day; ?>][<?= $meal; ?>][]" 
                                                                                       value="<?= $r['id']; ?>">
                                                                                <?= htmlspecialchars($r['package_name']); ?> 
                                                                                <b style="color: #2a5298;">₹<?= number_format($r['price'], 0); ?></b>
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

                                <!-- ================= TAB 2: CALENDAR ORDER ================= -->
                                <div class="tab-pane" id="d_order">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <h4 style="margin: 0; font-weight: bold; color: #2a5298;"><i class="fa fa-calendar"></i> Your Meal Calendar</h4>
                                        <div>
                                            <span class="label" style="background:#22c55e;">Breakfast</span>
                                            <span class="label" style="background:#f97316;">Lunch</span>
                                            <span class="label" style="background:#6366f1;">Dinner</span>
                                            <span class="label" style="background:#14b8a6;">Tiffin</span>
                                        </div>
                                    </div>
                                    <p class="text-muted" style="font-size: 13px;">Click on any meal event to view details or cancel with refund.</p>
                                    <div id="calendar" style="max-width: 100%; margin: 10px auto;"></div>

                                    <!-- Order Modal -->
                                    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary" style="color: #fff;">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title font-weight-bold">Meals on <span id="modalDateDisplay"></span></h4>
                                                </div>
                                                <div class="modal-body" id="orderBody">
                                                    <div class="text-center text-muted">Loading...</div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ================= TAB 3: RECHARGE HISTORY ================= -->
                                <div class="tab-pane table-responsive" id="recharge_history">
                                    <table class="table table-bordered table-striped">
                                        <thead style="background: #f4f6f9;">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Teacher</th>
                                                <th>Amount (₹)</th>
                                                <th>Type</th>
                                                <th>Description / Payment Mode</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $rq = mysqli_query($con, "
                                                SELECT * FROM rrsv_food_wallet_ledger
                                                WHERE (student_id = '$teacher_code' OR student_id = '$teacher_id')
                                                  AND type = 'Credit'
                                                  AND is_delete = 'No'
                                                ORDER BY created_at DESC
                                            ");
                                            if (mysqli_num_rows($rq) == 0):
                                            ?>
                                                <tr><td colspan="6" class="text-center text-muted">No recharge history found.</td></tr>
                                            <?php
                                            else:
                                                while ($r = mysqli_fetch_assoc($rq)):
                                            ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><b><?= htmlspecialchars($teacher['full_name']); ?></b></td>
                                                    <td class="text-success font-weight-bold">+ ₹<?= number_format((float)$r['amount'], 2); ?></td>
                                                    <td><span class="label label-success"><?= $r['type']; ?></span></td>
                                                    <td><?= htmlspecialchars($r['description']); ?></td>
                                                    <td><?= date('d-m-Y h:i A', strtotime($r['created_at'])); ?></td>
                                                </tr>
                                            <?php
                                                endwhile;
                                            endif;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ================= TAB 4: ORDER SUMMARY ================= -->
                                <div class="tab-pane table-responsive" id="order_summary">
                                    <table class="table table-striped table-bordered">
                                        <thead style="background: #f4f6f9;">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Food Package</th>
                                                <th>Meal</th>
                                                <th>Date</th>
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

                                            $oq = mysqli_query($con, "
                                                SELECT 
                                                    o.id AS order_id,
                                                    o.created_at,
                                                    i.id AS item_id,
                                                    i.order_date,
                                                    i.meal_type,
                                                    i.price,
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
                                                <tr><td colspan="7" class="text-center text-muted">No orders found.</td></tr>
                                            <?php
                                            else:
                                                while ($orow = mysqli_fetch_assoc($oq)):
                                                    if ($currOrder != $orow['order_id']):
                                                        $currOrder = $orow['order_id'];
                                            ?>
                                                        <tr style="background:#e9ecef; font-weight:bold;">
                                                            <td colspan="7">
                                                                🧾 Order #<?= $orow['order_id']; ?> 
                                                                | Placed on: <?= date('d-m-Y', strtotime($orow['created_at'])); ?>
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
                                                    <td><?= htmlspecialchars($orow['package_name']); ?></td>
                                                    <td><?= $orow['meal_type']; ?></td>
                                                    <td><?= date('d-m-Y (l)', strtotime($orow['order_date'])); ?></td>
                                                    <td>
                                                        <?php if ($orow['status'] === 'Confirmed'): ?>
                                                            <span class="label label-success">Confirmed</span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Cancelled</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php if ($orow['status'] === 'Confirmed'): ?>
                                                            <span class="text-danger font-weight-bold">- ₹<?= number_format((float)$orow['price'], 2); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="text-decoration: line-through;">₹<?= number_format((float)$orow['price'], 2); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($orow['status'] === 'Confirmed' && strtotime($orow['order_date']) >= strtotime(date('Y-m-d'))): ?>
                                                            <button class="btn btn-danger btn-xs" onclick="cancelOrderItem(<?= $orow['item_id']; ?>)">
                                                                <i class="fa fa-times"></i> Cancel
                                                            </button>
                                                        <?php elseif ($orow['status'] === 'Cancelled'): ?>
                                                            <small class="text-muted">Refunded</small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Completed</small>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                                <tr style="background:#d1ecf1; font-weight:bold;">
                                                    <td colspan="5" class="text-right">TOTAL CONFIRMED MEALS AMOUNT:</td>
                                                    <td class="text-right text-primary" style="font-size: 15px;">₹ <?= number_format($grandTotal, 2); ?></td>
                                                    <td></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ================= TAB 5: TRANSACTIONS ================= -->
                                <div class="tab-pane table-responsive" id="wallet_ledger">
                                    <table class="table table-bordered table-striped">
                                        <thead style="background: #f4f6f9;">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Date & Time</th>
                                                <th>Txn ID</th>
                                                <th>Description</th>
                                                <th class="text-right">Credit / Debit (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $li = 1;
                                            $creditSum = 0;
                                            $debitSum = 0;

                                            $lq = mysqli_query($con, "
                                                SELECT * FROM rrsv_food_wallet_ledger
                                                WHERE (student_id = '$teacher_code' OR student_id = '$teacher_id')
                                                  AND is_delete = 'No'
                                                ORDER BY created_at ASC
                                            ");

                                            if (mysqli_num_rows($lq) == 0):
                                            ?>
                                                <tr><td colspan="5" class="text-center text-muted">No transactions found.</td></tr>
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
                                                </tr>
                                            <?php endwhile; ?>
                                                <tr style="background:#fff3cd; font-weight:bold;">
                                                    <td colspan="4" class="text-right">CURRENT NET WALLET BALANCE:</td>
                                                    <td class="text-right text-dark" style="font-size: 15px;">₹ <?= number_format($creditSum - $debitSum, 2); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ================= TAB 6: MONTHLY STATEMENT ================= -->
                                <div class="tab-pane" id="monthly_slip">
                                    <?php
                                    $curr_month = date('Y-m');
                                    $m_start = $curr_month . '-01';
                                    $m_end = date('Y-m-t', strtotime($m_start));

                                    $m_query = mysqli_query($con, "
                                        SELECT i.order_date, i.meal_type, i.price, m.package_name
                                        FROM rrsv_food_order_items i
                                        JOIN rrsv_food_orders o ON o.id = i.order_id
                                        JOIN rrsv_menu_package m ON m.id = i.food_id
                                        WHERE (o.student_id = '$teacher_code' OR o.student_id = '$teacher_id')
                                          AND i.status = 'Confirmed'
                                          AND i.order_date BETWEEN '$m_start' AND '$m_end'
                                        ORDER BY i.order_date ASC
                                    ");
                                    $m_items = [];
                                    $m_total = 0;
                                    while ($mr = mysqli_fetch_assoc($m_query)) {
                                        $m_items[] = $mr;
                                        $m_total += (float)$mr['price'];
                                    }
                                    ?>
                                    <div class="box box-solid box-default" style="padding: 15px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                            <h4 style="margin: 0; font-weight: bold;"><i class="fa fa-file-text-o"></i> Statement for <?= date('F Y'); ?></h4>
                                            <button type="button" class="btn btn-default btn-sm" onclick="window.print()">
                                                <i class="fa fa-print"></i> Print Statement
                                            </button>
                                        </div>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Meal</th>
                                                    <th>Package</th>
                                                    <th class="text-right">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($m_items)): ?>
                                                    <tr><td colspan="4" class="text-center text-muted">No confirmed meals this month.</td></tr>
                                                <?php else: 
                                                    foreach ($m_items as $mi):
                                                ?>
                                                    <tr>
                                                        <td><?= date('d-m-Y (D)', strtotime($mi['order_date'])); ?></td>
                                                        <td><b><?= $mi['meal_type']; ?></b></td>
                                                        <td><?= htmlspecialchars($mi['package_name']); ?></td>
                                                        <td class="text-right">₹ <?= number_format((float)$mi['price'], 2); ?></td>
                                                    </tr>
                                                <?php 
                                                    endforeach; 
                                                endif; 
                                                ?>
                                                <tr style="background: #e2e8f0; font-weight: bold;">
                                                    <td colspan="3" class="text-right">TOTAL FOR <?= strtoupper(date('F Y')); ?> (<?= count($m_items); ?> meals):</td>
                                                    <td class="text-right text-primary">₹ <?= number_format($m_total, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../../footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let calendar;
let calendarLoaded = false;

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
            $('#orderBody').html('<div class="text-center text-muted p-3"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

            $.get('?ajax=get_orders&date=' + date, function (res) {
                if (!res || res.length === 0) {
                    $('#orderBody').html('<div class="alert alert-info text-center">No orders found on ' + date + '</div>');
                    return;
                }

                let html = '<table class="table table-bordered table-striped">';
                html += '<thead><tr><th>Meal</th><th>Package</th><th>Price</th><th>Action</th></tr></thead><tbody>';

                res.forEach(function (o) {
                    html += '<tr>';
                    html += '<td><b>' + o.meal_type + '</b></td>';
                    html += '<td>' + o.package_name + '</td>';
                    html += '<td>₹' + parseFloat(o.price).toFixed(2) + '</td>';
                    html += '<td>';
                    if (o.status === 'Confirmed') {
                        html += '<button class="btn btn-danger btn-xs" onclick="cancelOrderItem(' + o.id + ')"><i class="fa fa-times"></i> Cancel</button>';
                    } else {
                        html += '<span class="label label-default">Cancelled</span>';
                    }
                    html += '</td></tr>';
                });

                html += '</tbody></table>';
                $('#orderBody').html(html);
            }, 'json');
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

// Check-all for specific meal
$(document).on('change', '.meal-check-all', function () {
    let meal = $(this).data('meal');
    let isChecked = $(this).is(':checked');
    $('.meal-checkbox[data-meal="' + meal + '"]').prop('checked', isChecked);
});

// Quick helper: Mon-Sat Lunch
$('#btnQuickMonSatLunch').on('click', function () {
    $('.meal-checkbox').prop('checked', false);
    $('.meal-check-all').prop('checked', false);

    const weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    weekdays.forEach(function(day) {
        $('.meal-checkbox[data-day="' + day + '"][data-meal="Lunch"]').prop('checked', true);
    });

    Swal.fire({
        icon: 'success',
        title: 'Selected',
        text: 'Monday to Saturday lunch meals selected automatically.',
        timer: 1500,
        showConfirmButton: false
    });
});

// Quick helper: All Lunch
$('#btnQuickAllLunch').on('click', function () {
    $('.meal-checkbox[data-meal="Lunch"]').prop('checked', true);
    $('.meal-check-all[data-meal="Lunch"]').prop('checked', true);
});

// Quick helper: Clear All
$('#btnClearSelection').on('click', function () {
    $('.meal-checkbox').prop('checked', false);
    $('.meal-check-all').prop('checked', false);
});

// Validation on order submit
$('#portalFoodOrderForm').on('submit', function (e) {
    let checked = $('.meal-checkbox:checked').length;
    if (checked === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Food Selected',
            text: 'Please select at least one meal before placing your order.'
        });
        e.preventDefault();
        return false;
    }
});

// Cancel Order Item
function cancelOrderItem(id) {
    Swal.fire({
        title: 'Cancel this meal order?',
        text: 'The meal amount will be refunded directly to your wallet.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, Cancel & Refund',
        cancelButtonText: 'No'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'ajax_food_orders.php',
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: 'cancel_order',
                id: id
            },
            success: function (res) {
                if (res.status === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cancelled',
                        text: res.msg,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#orderModal').modal('hide');
                    refreshBalance();
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    Swal.fire('Error', res.msg || 'Cancellation failed', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Server communication error', 'error');
            }
        });
    });
}
</script>
</body>
</html>
