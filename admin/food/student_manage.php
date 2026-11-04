<?php
session_start();
error_reporting(0);
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

include('../../include/dbcon.php');

if (empty($_SESSION['mid'])) {
     if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    } else {
        header('location:index.php');
        exit;
    }
}
$student_id = ($_GET['student_id'] ?? 0);
$stu = mysqli_query($myDB, "SELECT id,scl_reg_no,scl_name FROM rrsv_student_registration WHERE scl_reg_no='$student_id'");
$student = mysqli_fetch_assoc($stu);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recharge'])) {
    if (!isset($_POST['token'], $_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'])) {
           $error = "Invalid request"; 
        }else{
    
        // 🔒 Prevent re-use
        unset($_SESSION['token']);
    
        $student_id = $_POST['student_id'];
        $amount = (float) $_POST['amount'];
        $note = mysqli_real_escape_string($myDB, $_POST['note']);
     
        mysqli_begin_transaction($myDB);
    
            try {
        
                /* 1️⃣ Check wallet exists */
                $check = mysqli_query(
                    $myDB,
                    "SELECT balance 
                     FROM rrsv_food_wallet 
                     WHERE student_id = '$student_id'
                     FOR UPDATE"
                );
        
        
                if (mysqli_num_rows($check) == 0) {
                    /* 2️⃣ First time → INSERT wallet */
                    mysqli_query($myDB, "
                        INSERT INTO rrsv_food_wallet (student_id, balance)
                        VALUES ('$student_id', $amount)
                    ");
                } else {
                    /* 3️⃣ Already exists → UPDATE */
                    mysqli_query($myDB, "
                    UPDATE rrsv_food_wallet
                    SET balance = balance + $amount
                    WHERE student_id = '$student_id'
                ");
        
                }
        
                /* 4️⃣ Generate txn_id */
                $txn_id = generateTxnId($myDB);
               
                /* 4️⃣ Insert ledger */
                mysqli_query($myDB, "
                    INSERT INTO rrsv_food_wallet_ledger
                    (student_id,tns_id, amount, type, description)
                    VALUES
                    ('$student_id','$txn_id', $amount, 'Credit', '$note')
                ");
        
                mysqli_commit($myDB);
                $success = "Wallet recharged successfully";
        
            } catch (Exception $e) {
                print_r($e);
                die();
                mysqli_rollback($myDB);
                $error = "Recharge failed";
            }
        }
}

function generateTxnId($myDB)
{
    $date = date('ymd'); // 240207

    $res = mysqli_query(
        $myDB,
        "SELECT COUNT(*) AS total 
         FROM rrsv_food_wallet_ledger 
         WHERE DATE(created_at) = CURDATE()"
    );

    $row = mysqli_fetch_assoc($res);
    $seq = str_pad($row['total'] + 1, 2, '0', STR_PAD_LEFT);

    return "TXN{$date}{$seq}";
}


if (isset($_POST['ajax']) && $_POST['ajax'] === 'delete_ledger') {
 
    $id = (int)$_POST['id'];
    $password = $_POST['password'] ?? '';

    // 🔐 Verify admin password
   $admin = mysqli_fetch_assoc(mysqli_query($myDB,
    "SELECT admin_pwd FROM rrsv_admin WHERE admin_id ='2'"
));

if (!$admin || $password !== $admin['admin_pwd']) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Invalid password'
    ]);
    exit;
}

    mysqli_begin_transaction($myDB);

    try {

        // 🔹 Fetch ledger row
        $row = mysqli_fetch_assoc(mysqli_query($myDB, "
            SELECT student_id, amount, type
            FROM rrsv_food_wallet_ledger
            WHERE id = $id AND is_delete = 'No'
            FOR UPDATE
        "));

        if (!$row) {
            throw new Exception('Ledger not found');
        }

        $student_id = $row['student_id'];
        $amount     = $row['amount'];
         // ✅ Lock wallet balance
        $wallet = mysqli_fetch_assoc(mysqli_query($myDB,"
            SELECT balance
            FROM rrsv_food_wallet
            WHERE student_id='$student_id'
            FOR UPDATE
        "));
 $balance = (float)$wallet['balance'];
        // 🔹 Reverse wallet balance
        if ($row['type'] === 'Credit') {
            // Remove credited amount
             if ($balance < $amount) {
                throw new Exception('Insufficient wallet balance');
            }
            mysqli_query($myDB, "
                UPDATE rrsv_food_wallet
                SET balance = balance - $amount
                WHERE student_id = '$student_id'
            ");
        } else {
            // Add debited amount back
            mysqli_query($myDB, "
                UPDATE rrsv_food_wallet
                SET balance = balance + $amount
                WHERE student_id = '$student_id'
            ");
        }

        // 🔹 Soft delete ledger
        mysqli_query($myDB, "
            UPDATE rrsv_food_wallet_ledger
            SET is_delete = 'Yes'
            WHERE id = $id
        ");

        mysqli_commit($myDB);

        echo json_encode(['status' => 'success']);
        exit;

    } catch (Exception $e) {
       //mysqli_rollback($myDB);
        echo json_encode(['status' => 'error', 'msg' => '']);
        exit;
    }
}



/* ================= Food order ================= */

/* ================= AJAX HANDLERS ================= */

if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_orders') {

    $date = mysqli_real_escape_string($myDB, $_GET['date']);
    $student_id = mysqli_real_escape_string($myDB, $_GET['student_id']); // fix here

    $sql = "
        SELECT 
            oi.id,
            oi.meal_type,
            p.package_name,
            oi.price
        FROM rrsv_food_order_items oi
        INNER JOIN rrsv_food_orders o ON o.id = oi.order_id
        INNER JOIN rrsv_menu_package p ON p.id = oi.food_id
        WHERE o.student_id = '$student_id'
          AND oi.order_date = '$date'
        ORDER BY oi.meal_type
    ";

    $q = mysqli_query($myDB, $sql);

    $data = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $data[] = $r;
    }

    echo json_encode($data); // send JSON to JS
    exit;
}



if (isset($_POST['ajax']) && $_POST['ajax'] == 'cancel_order') {
    $id = $_POST['id'];

    $q = mysqli_fetch_assoc(mysqli_query($myDB, "
        SELECT oi.price
        FROM rrsv_food_order_items oi
        JOIN rrsv_food_orders o ON o.id=oi.order_id
        WHERE oi.id='$id' AND o.student_id='$student_id'
    "));

    mysqli_query($myDB, "DELETE FROM rrsv_food_order_items WHERE id='$id'");
    mysqli_query($myDB, "UPDATE rrsv_food_wallet SET balance=balance+{$q['price']} WHERE student_id='$student_id'");
    echo "OK";
    exit;
}

/* ================= WALLET ================= */

$wallet = mysqli_fetch_assoc(mysqli_query($myDB, "SELECT balance FROM rrsv_food_wallet WHERE student_id='$student_id'"));
$balance = $wallet['balance'] ?? 0;

$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

/* ================= ORDER SUBMIT ================= */

if (isset($_POST['submit_orders'])) {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $orders = $_POST['orders'] ?? [];

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

                $chk = mysqli_fetch_assoc(mysqli_query($myDB, "
                SELECT COUNT(*) c
                FROM rrsv_food_order_items oi
                JOIN rrsv_food_orders o ON o.id=oi.order_id
                WHERE o.student_id='$student_id'
                AND oi.order_date='$date'
                AND oi.meal_type='$meal'
            "));

                if ($chk['c'] > 0) {
                    $error = "Already ordered $meal on $date";
                    break 2; // ✅ FIXED
                }

                foreach ($food_ids as $fid) {
                    $p = mysqli_fetch_assoc(mysqli_query($myDB, "
                    SELECT price FROM rrsv_menu_package WHERE id='$fid'
                "));
                    $total += $p['price'];
                    $items[] = [$fid, $meal, $date, $p['price']];
                }
            }
        }
    }


    if (!$error) {
        if ($total > $balance) {
            $error = "Insufficient wallet balance";
        } else {
            mysqli_query($myDB, "INSERT INTO rrsv_food_orders(student_id,start_date,end_date,total_amount,status)
                VALUES('$student_id','$start_date','$end_date','$total','Confirmed')");
            $oid = mysqli_insert_id($myDB);

            foreach ($items as $i) {
                mysqli_query($myDB, "INSERT INTO rrsv_food_order_items(order_id,food_id,meal_type,order_date,price)
                    VALUES('$oid','$i[0]','$i[1]','$i[2]','$i[3]')");
            }

            mysqli_query($myDB, "UPDATE rrsv_food_wallet SET balance=balance-$total WHERE student_id='$student_id'");
             $note='Food Order';
              $txn_id = generateTxnId($myDB);
            mysqli_query($myDB, " INSERT INTO rrsv_food_wallet_ledger(student_id,tns_id, amount, type, description) VALUES ('$student_id','$txn_id', $total, 'Debit', '$note')");

            $success = "Order placed successfully. Total ₹$total";
        }
    }
}

/* ================= CALENDAR DATA ================= */

$events = [];
$q = mysqli_query($myDB, "
SELECT oi.order_date, oi.meal_type, SUM(oi.price) total
FROM rrsv_food_order_items oi
JOIN rrsv_food_orders o ON o.id=oi.order_id
WHERE o.student_id='$student_id' AND oi.status='Confirmed'
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
        'title' => $r['meal_type'] . " ₹" . $r['total'],
        'start' => $r['order_date'],
        'backgroundColor' => $color
    ];
}
?>
 <style>
        .meal-title {
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
        }

        .menu-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
        }

        .menu-item {
            margin-bottom: 5px;
        }
        .hide-checkbox {
    display: none !important;
}
   
		.errorWrap {
			padding: 10px;
			margin: 0 0 20px 0;
			background: #dd3d36;
			color: #fff;
			-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
		}

		.succWrap {
			padding: 10px;
			margin: 0 0 20px 0;
			background: #5cb85c;
			color: #fff;
			-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
		}
	/* REMOVE DEFAULT TAB BORDER */
.boxed-tabs {
    border-bottom: none;
    margin-bottom: 0;
}

/* TAB ITEM */
.boxed-tabs > li {
    margin-bottom: 20px;
    margin-right: 6px;
}

/* TAB LINK */
.boxed-tabs > li > a {
    border: 1px solid #e5e5e5;
    border-bottom: none;
    background: #15698f;
    color: #fbf9f6;
    padding: 10px 18px;
    font-weight: 600;
    border-radius: 6px 6px 0 0;
}

/* ACTIVE TAB */
.boxed-tabs > li.active > a,
.boxed-tabs > li.active > a:hover,
.boxed-tabs > li.active > a:focus {
    background: #0b58a1;
    color: #f7f8f8;
    border-color: #e5e5e5;
}

/* HOVER */
.boxed-tabs > li > a:hover {
    background: #bc5cad;
}

/* TAB CONTENT */
.boxed-tab-content {
    border: 1px solid #e5e5e5;
    padding: 20px;
    background: #fff;
}

	</style>
<style>
/* TAB CONTAINER */
.custom-tabs {
    border-bottom: 1px solid #ddd;
    margin-bottom: 15px;
    background-color: #f1f1d3;
    padding: 8px 14px;
    border-radius: 3px 3px 0 0;
    
}

/* TAB LINKS */
.custom-tabs > li > a {
    color: #555;
    font-weight: 500;
    padding: 8px 14px;
    border-radius: 3px 3px 0 0;
    margin: 12px;
}

/* ACTIVE TAB */
.custom-tabs > li.active > a,
.custom-tabs > li.active > a:hover,
.custom-tabs > li.active > a:focus {
    background-color: #0d6efd;
    color: #fff;
    border: 1px solid #0d6efd;
    margin: 12px;
}

/* HOVER */
.custom-tabs > li > a:hover {
    background-color: #f5f5f5;
}

/* ---------- MOBILE TAB FIX ---------- */
@media (max-width: 768px) {

    .custom-tabs {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        padding: 6px;
    }

    .custom-tabs::-webkit-scrollbar {
        display: none; /* hide scrollbar */
    }

    .custom-tabs > li {
        float: none;
        display: inline-block;
    }

    .custom-tabs > li > a {
        margin: 4px;
        padding: 8px 12px;
        font-size: 13px;
        border-radius: 20px;
    }

    .custom-tabs > li.active > a {
        margin: 4px;
    }
}

/* TAB CONTENT */
.custom-tab-content {
    border: 1px solid #ddd;
    border-top: none;
    padding: 15px;
    background: #fff;
}

.wallet-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,.15);
    padding: 8px 14px;
    border-radius: 20px;
    font-weight: 600;
}

.wallet-amount {
    font-size: 15px;
}

@media (max-width: 768px) {
    .text-right {
        text-align: left !important;
    }
    .mt-10-sm {
        margin-top: 10px;
    }
}
 .m-24 {
            margin-top: 24px;
        }
        .mt-4 {
            margin-top: 4px !important;
        }
        .p-10 {
            padding: 10px !important;
        }
        .p-5 {
            padding: 5px !important;
        }
        .hide-checkbox {
        display: none !important;
        }
        .ml-5{margin-left: 5px !important;}
        .mt-5{margin-top: 5px !important;}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<!--<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>-->
<?php  include('../../include/header.php'); ?>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body bg-primary">
                        <div class="row align-items-center">
                    
                            <!-- LEFT : Title -->
                            <div class="col-md-8 col-sm-12">
                                <h3 class="m-0 font-weight-bold">
                                    🍽️ Food & Orders
                                </h3>
                                <small class="text-light">
                                    <?= $student['scl_name'] ?> (<?= $student['scl_reg_no'] ?>)
                                </small>
                            </div>
                    
                            <!-- RIGHT : Wallet -->
                            <div class="col-md-4 col-sm-12 text-right mt-10-sm">
                                <div class="wallet-badge">
                                    <svg height="26" width="26" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.005 6h-7a6 6 0 0 0 0 12h7v2a1 1 0 0 1-1 1h-18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v2Zm-7 2h8v8h-8a4 4 0 1 1 0-8Zm0 3v2h3v-2h-3Z"
                                              fill="currentColor"/>
                                    </svg>
                                  
                                   <span class="wallet-amount">
                                        ₹ <span id="walletBalance">0.00</span>
                                    </span>
                                </div>
                            </div>
                    
                    </div>

               
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">

                   

                        <div class="">
                            <ul class="nav nav-tabs boxed-tabs">
                                <li class="active">
                                    <a href="#recharge" data-toggle="tab">RECHARGE</a>
                                </li>
                                <li>
                                    <a href="#recharge_history" data-toggle="tab">RECHARGE HISTORY</a>
                                </li>
                                <li>
                                    <a href="#food_order" data-toggle="tab">FOOD ORDER 📅</a>
                                </li>
                                <li><a href="#d_order" role="tab" data-toggle="tab">Calendar Order📅</a></li>
                                <li>
                                    <a href="#order_summary" data-toggle="tab">ORDER SUMMARY</a>
                                </li>
                                <li>
                                    <a href="#wallet_ledger" data-toggle="tab">YOUR TRANSACTIONS</a>
                                </li>
                            </ul>
                        </div>
                    
                        <!--<div class="panel-body">-->
                        <!--    <div class="tab-content">-->
                                
                        <!--    </div>-->
                        <!--</div>-->
                   


                    <div class="tab-content" style="margin-top:20px;">
                        <div class="tab-pane active class="card-body"" id="recharge">
                            <?php if (!empty($success)) { ?>
                                <div class="succWrap"><?= $success ?></div>
                            <?php } ?>
                            <?php if (!empty($error)) { ?>
                                <div class="errorWrap"><?= $error ?></div>
                            <?php } ?>

                            <form method="post">
                                <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                <div class="form-group mb-2 row">
                                    <div class="col-md-4">
                                        <!--<label><?= $student['scl_name'] ?></label>-->
                                        <input type="hidden" name="student_id" value="<?= $student['scl_reg_no'] ?>">
                                    </div>
                                </div>
                                <div class="form-group mb-2 row">
                                    <div class="col-md-3">
                                        <label>Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group mb-2 row">
                                    <div class="col-md-3">
                                        <label>Note</label>
                                        <input type="text" name="note" class="form-control" value="Wallet Recharge" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-2 row">
                                    <div class="col-md-2" style="margin-top:25px;">
                                        <button class="btn btn-success" name="recharge">Recharge</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane table-responsive" id="recharge_history">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = mysqli_query($myDB, "
									SELECT l.*, u.scl_reg_no,u.scl_name
									FROM rrsv_food_wallet_ledger l
									JOIN rrsv_student_registration u ON u.id = (SELECT MAX(id) FROM rrsv_student_registration  WHERE scl_reg_no = l.student_id)
									WHERE l.type='Credit' AND l.student_id='$student_id' AND l.is_delete = 'No' AND l.description='Wallet Recharge'
									ORDER BY l.created_at DESC
								");
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $r['scl_name'] ?></td>
                                            <td><?= number_format($r['amount'], 2) ?></td>
                                            <td><?= $r['type'] ?></td>
                                            <td><?= $r['description'] ?></td>
                                            <td><?= date('d-m-Y h:i A', strtotime($r['created_at'])) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane table-responsive" id="food_order">
                            <?php if (!empty($success)) { ?>
                                <div class="succWrap"><?= $success ?></div>
                            <?php } ?>
                            <?php if (!empty($error)) { ?>
                                <div class="errorWrap"><?= $error ?></div>
                            <?php } ?>

                            <!-- ================= ORDER FORM ================= -->
                            <form method="post" id="foodOrderForm">
                                <!--<input type="date" id="start_date" name="start_date" required>-->
                                <!--<input type="date" id="end_date" name="end_date" required>-->
                                <!--<button name="submit_orders" class="btn btn-primary btn-sm">Place Order</button>-->
                                <div class="row mb-3 align-items-center">
    
                                    <div class="col-md-4 d-flex align-items-center">
                                        <label class="me-2 mb-0 fw-bold">Start Date:</label>
                                        <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                                    </div>
                                    
                                    <div class="col-md-4 d-flex align-items-center">
                                        <label class="me-2 mb-0 fw-bold">End Date:</label>
                                        <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                                    </div>

                                
                                    <div class="col-md-4 m-24">
                                        <button type="submit" name="submit_orders" class="btn btn-primary w-100">
                                            Place Orders
                                        </button>
                                    </div>
                                
                                </div>

                                <table class="table table-striped table-bordered mt-3">
                                    <tr>
                                        <th>Day</th>
                                        <?php foreach ($meals as $m)
                                            echo "<th>" . $m . "<input type='checkbox' class='meal-check-all ml-5 mt-5 p-5' data-meal='<?= $m ?>'></th>"; ?>


                                    </tr>
                                    <?php foreach ($days as $day) { ?>
                                        <tr>
                                            <td><b><?= $day ?></b></td>
                                            <?php foreach ($meals as $meal) { ?>
                                                <td>
                                                    <?php
                                                    $qm = mysqli_query($myDB, "
                                                SELECT p.id,p.package_name,p.price
                                                FROM rrsv_menu_package p
                                                JOIN rrsv_food_daily_menu d ON d.food_id=p.id
                                                WHERE d.day_name='$day' AND d.meal_type='$meal'
                                                ");
                                                    while ($r = mysqli_fetch_assoc($qm)) {
                                                        echo "<div>
                                                <input type='checkbox'  class='meal-checkbox hide-checkbox' data-meal='<?= $meal ?>' name='orders[$day][$meal][]' value='{$r['id']}'>
                                                {$r['package_name']} ₹{$r['price']}
                                                </div>";
                                                    }
                                                    ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </form>

                          

                        </div>
                        <div class="tab-pane table-responsive" id="d_order">
					    
                      <!-- ================= CALENDAR ================= -->
                        <h3>Your Orders Calendar</h3>
                       <div id="calendar" style="max-width: 100%; margin: 20px auto;"></div>
        
                        <!-- ================= MODAL ================= -->
                        <div class="modal fade" id="orderModal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Orders</h5>
                                    </div>
                                    <div class="modal-body" id="orderBody"></div>
                                </div>
                            </div>
                        </div>

					 </div>
                        <div class="tab-pane table-responsive" id="order_summary">

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Food Package</th>
                                        <th>Meal</th>
                                        <th>Date</th>
                                        <!--<th>Qty</th>-->
                                        <th>Rate</th>
                                        <!--<th>Item Total</th>-->
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $i = 1;
                                    $grandTotal = 0;
                                    $currentOrder = 0;
                                    $orderTotal = 0;

                                    $q = mysqli_query($myDB, "
                                        SELECT
                                            o.id AS order_id,
                                            o.created_at,
                                            i.order_date,
                                            i.meal_type,
                                            i.price,
                                            i.quantity,
                                            (i.price * i.quantity) AS item_total,
                                            i.status,
                                            m.package_name
                                        FROM rrsv_food_orders o
                                        JOIN rrsv_food_order_items i ON i.order_id = o.id
                                        JOIN rrsv_menu_package m ON m.id = i.food_id
                                        WHERE o.student_id = '$student_id'
                                        ORDER BY o.id DESC, i.order_date ASC
                                    ");

                                    while ($r = mysqli_fetch_assoc($q)) {

                                        /* New Order Header */
                                        if ($currentOrder != $r['order_id']) {

                                            /* Print previous order total */
                                            if ($currentOrder != 0) {
                                            //     echo "
                                            // <tr style='background:#f8f9fa;font-weight:bold'>
                                            //     <td colspan='6' class='text-right'>Order Total</td>
                                            //     <td>" . number_format($orderTotal, 2) . "</td>
                                            // </tr>";
                                                                                $orderTotal = 0;
                                                                            }

                                                                            echo "
                                        <tr style='background:#e9ecef;font-weight:bold'>
                                            <td colspan='7'>
                                                🧾 Order #{$r['order_id']} 
                                                | Created: " . date('d-m-Y', strtotime($r['created_at'])) . "
                                            </td>
                                        </tr>";

                                            $currentOrder = $r['order_id'];
                                        }

                                       if ($r['status'] == 'Confirmed') {
                                        $orderTotal += $r['item_total'];
                                        $grandTotal += $r['item_total'];
                                      }
                                        ?>

                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($r['package_name']) ?></td>
                                            <td><?= $r['meal_type'] ?></td>
                                            <td><?= date('d-m-Y', strtotime($r['order_date'])) ?></td>
                                            <!--<td class="text-center"><?= $r['quantity'] ?></td>-->
                                            	<td class="text-right">
										     <?php if ($r['status'] === 'Cancelled') { ?>
                                                <span class="text-success">+ <?= number_format($r['price'], 2) ?></span>
                                            <?php } if ($r['status'] === 'Confirmed') { ?>
                                                <span class="text-danger">- <?= number_format($r['price'], 2) ?></span>
                                                <?php }?>
                                            <!--<td class="text-right"><?= number_format($r['price'], 2) ?></td>-->
                                            <!--<td class="text-right"><?= number_format($r['item_total'], 2) ?></td>-->
                                        </tr>

                                    <?php } ?>

                                    <!-- LAST ORDER TOTAL -->
                                    <?php if ($currentOrder != 0) { ?>
                                        <!--<tr style="background:#f8f9fa;font-weight:bold">-->
                                        <!--    <td colspan="6" class="text-right">Order Total</td>-->
                                        <!--    <td><?= number_format($orderTotal, 2) ?></td>-->
                                        <!--</tr>-->
                                    <?php } ?>

                                    <!-- GRAND TOTAL -->
                                    <tr style="background:#d1ecf1;font-weight:bold">
                                        <td colspan="4" class="text-right">ORDER TOTAL</td>
                                        <td><?= number_format($grandTotal, 2) ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane table-responsive" id="wallet_ledger">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <!--<th>Credit (₹)</th>-->
                                        <!--<th>Debit (₹)</th>-->
                                        <th>Description</th>
                                        <th>Rs(₹ )</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $i = 1;
                                    $currentStudent = '';
                                    $creditTotal = 0;
                                    $debitTotal = 0;

                                    /* 🔹 Get ledger ordered by student */
                                     $q = mysqli_query($myDB, "
                                        SELECT l.*, u.scl_name,u.scl_reg_no
                                        FROM rrsv_food_wallet_ledger l
                                        JOIN rrsv_student_registration u ON u.id = (SELECT MAX(id) FROM rrsv_student_registration  WHERE scl_reg_no = l.student_id) WHERE u.scl_reg_no='$student_id' AND is_delete='No' ORDER BY u.scl_name, l.created_at ASC");

                                    while ($r = mysqli_fetch_assoc($q)) {

                                        /* 🔹 When student changes → print summary row of previous student */
                                        if ($currentStudent != '' && $currentStudent != $r['scl_name']) {
                                            $balance = $creditTotal - $debitTotal;
                                            ?>
                                            <tr style="background:#fff3cd;font-weight:bold">
                                                <!--<td colspan="1">TOTAL</td>-->
                                                <!--<td class="text-success text-right"><?= number_format($creditTotal, 2) ?></td>-->
                                                <!--<td class="text-danger text-right"><?= number_format($debitTotal, 2) ?></td>-->
                                                <td colspan="3">
                                                   Current Balance : <b>₹ <?= number_format($balance, 2) ?></b>
                                                </td>
                                                
                                            </tr>
                                            <?php

                                            /* reset totals */
                                            $creditTotal = 0;
                                            $debitTotal = 0;
                                        }

                                        /* 🔹 Student Header Row */
                                        if ($currentStudent != $r['scl_name']) {
                                            $currentStudent = $r['scl_name'];
                                            ?>
                                            <!--<tr style="background:#e9ecef;font-weight:bold">-->
                                            <!--    <td colspan="5">👤 <?= htmlspecialchars($currentStudent) ?></td>-->
                                            <!--</tr>-->
                                            <?php
                                        }

                                        /* 🔹 Credit / Debit calculation */
                                        $credit = '';
                                        $debit = '';

                                        if ($r['type'] == 'Credit') {
                                            $credit = number_format($r['amount'], 2);
                                            $creditTotal += $r['amount'];
                                        } else {
                                            $debit = number_format($r['amount'], 2);
                                            $debitTotal += $r['amount'];
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                             <td><?= date('d-m-Y h:i A', strtotime($r['created_at'])) ?></td>
                                            <!--<td class="text-success text-right"><?= $credit ?></td>-->
                                            <!--<td class="text-danger text-right"><?= $debit ?></td>-->
                                            <td><?= htmlspecialchars($r['description']) ?></td>
                                           
                                            	<td class="text-right">
										     <?php if ($r['type'] == 'Credit') { ?>
                                                <span class="text-success">+ <?= $credit ?></span>
                                            <?php } if ($r['type'] == 'Debit') { ?>
                                                <span class="text-danger">- <?= $debit ?></span>
                                                <?php }?>
										    
										</td>
                                            <td> 
                                            <?php if ($r['type'] == 'Credit' && $r['description']=='Wallet Recharge') { ?>
                                            <button 
                                                class="btn btn-danger btn-xs delete-ledger text-white"
                                                data-id="<?= $r['id'] ?>"
                                                data-amount="<?= $r['amount'] ?>"
                                                data-type="<?= $r['type'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php
                                    /* 🔹 Final student total */
                                    if ($currentStudent != '') {
                                        $balance = $creditTotal - $debitTotal;
                                        ?>
                                        <tr style="background:#fff3cd;font-weight:bold">
                                            <!--<td colspan="1">TOTAL</td>-->
                                            <!--<td class="text-success text-right"><?= number_format($creditTotal, 2) ?></td>-->
                                            <!--<td class="text-danger text-right"><?= number_format($debitTotal, 2) ?></td>-->
                                            <td colspan="5" class="text-success text-right">
                                               Current Balance : <b>₹ <?= number_format($balance, 2) ?></b>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>






                        <div class="tab-pane table-responsive" id="recharge_history1">
                            <h3>Recharge History</h3>



                            <h2 class="page-title">Student-wise Order Summary</h2>
                            <table id="zctb" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Total Orders</th>
                                        <th>Total Amount (₹)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;

                                    $sql = "
						SELECT 
							u.id,
							u.scl_name,
							u.scl_reg_no,
							COUNT(o.id) AS total_orders,
							IFNULL(SUM(o.total_amount), 0) AS total_amount
						FROM rrsv_student_registration u
						LEFT JOIN rrsv_food_orders o 
							ON o.student_id = u.scl_reg_no
						WHERE u.scl_reg_no = '$student_id'
					GROUP BY u.scl_reg_no, u.scl_reg_no, u.scl_name
						ORDER BY total_amount DESC
					";

                                    $query = mysqli_query($myDB, $sql);

                                    while ($row = mysqli_fetch_assoc($query)) {
                                        ?>
                                        <tr>
                                            <td><?= $cnt++; ?></td>
                                            <td><?= htmlspecialchars($row['scl_name']); ?></td>
                                            <td><?= htmlspecialchars($row['scl_reg_no']); ?></td>
                                            <td class="text-center"><?= $row['total_orders']; ?></td>
                                            <td class="text-right">
                                                <?= number_format($row['total_amount'], 2); ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="student_order_details.php?student_id=<?= $row['scl_reg_no']; ?>"
                                                    class="btn btn-xs btn-info">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
   
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let calendar;
let calendarLoaded = false;

$('a[href="#d_order"]').on('shown.bs.tab', function () {

    if (calendarLoaded) {
        calendar.updateSize();
        return;
    }

    calendar = new FullCalendar.Calendar(
        document.getElementById('calendar'),
        {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: <?= json_encode($events) ?>,

            eventClick: function (info) {
                let date = info.event.startStr;
                 $('#orderModal').modal('show'); 
                $.get(
                    '?ajax=get_orders&date=' + date + '&student_id=<?= $student['scl_reg_no'] ?>',
                    function(res) {
                        if (res.error) {
                            console.error('Error:', res.error);
                            $('#orderBody').html('<div class="text-danger">Unable to load orders.</div>');
                        } else {
                            console.log(res); // JSON data array
                            // populate your modal table here
                        }
                    },
                    'json'
                );


            }
        }
    );

    calendar.render();
    calendarLoaded = true;
});

function refreshBalance() {
    $.ajax({
        url: 'get_balance.php',
        type: 'GET',
        data: {
            student_id: '<?= $student['scl_reg_no'] ?>'
        },
        dataType: 'json',
        success: function (res) {
            if (res && res.balance !== undefined) {
                $('#walletBalance').text(
                    parseFloat(res.balance).toFixed(2)
                );
            }
        }
    });
}

// Load once
refreshBalance();

// Refresh every 5 seconds
setInterval(refreshBalance, 5000);
</script>
    <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#studentTable').DataTable({
                paging: true,       // pagination
                searching: true,    // search box
                ordering: true,     // column sorting
                info: true,         // "Showing X of Y"
                lengthChange: true, // page length dropdown
                pageLength: 10,     // default rows per page
                order: [[0, 'desc']] // sort by ID desc
            });
        });
    </script>




    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.succWrap').slideUp("slow");
            }, 3000);
        });
    </script>
   
    <script>
        $(document).on('change', '.meal-check-all', function () {
            let meal = $(this).data('meal');
            let checked = $(this).is(':checked');

            $('.meal-checkbox[data-meal="' + meal + '"]').prop('checked', checked);
        });
        
    $(document).on('submit', '#foodOrderForm', function(e) {
    
        let checked = $('.meal-checkbox:checked').length;
    
        if (checked === 0) {
    
            Swal.fire({
                icon: 'warning',
                title: 'No Food Selected',
                text: 'Please select at least one food item before placing order'
            });
    
            e.preventDefault();
            return false;
        }
    
    });
    </script>

    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').min = today;
        document.getElementById('end_date').min = today;
    </script>

<script>

$(document).on('click', '.delete-ledger', function () {

    let id = $(this).data('id');
    let btn = $(this);

    Swal.fire({
        title: 'Delete Ledger?',
        text: 'Enter admin password',
        input: 'password',
        inputPlaceholder: 'Admin Password',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancel'

    }).then((result) => {

        if (!result.isConfirmed) return;

        let password = result.value;

        if (!password) {
            Swal.fire('Error', 'Password required', 'error');
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
                text: 'Ledger removed successfully',
                timer: 1500,
                showConfirmButton: false
            });

            btn.closest('tr').fadeOut(300, function () {
                $(this).remove();
            });

        } else {
            Swal.fire('Error', res.msg || 'Delete failed', 'error');
        }

    },
    error: function (xhr) {
        console.log(xhr.responseText); // debug
        Swal.fire('Error', 'Server error occurred', 'error');
    }
});


    });

});

</script>
 <?php include('../../include/footer.php'); ?>