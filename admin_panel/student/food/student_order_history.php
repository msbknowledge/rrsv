<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

include '../../../include/config.php';
include '../../dbcon.php';
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}
$teacher = $_SESSION['user_data'];
//$student = $_SESSION['user_data'];
//print_r($_SESSION['scl_reg_no']  );
include('../../header.php');

?>

<!DOCTYPE html>
<html>
    
<!-- FullCalendar -->
<!--<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">-->
<!--<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>-->

<?php include('../../header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <!-- Header -->
    <header class="main-header">
        <a href="#" class="logo">
            <span class="logo-mini"><b>MSB</b></span>
             <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
        </a>
        <?php include '../../top_navbar.php'; ?>
    </header>

    <!-- Sidebar -->
    <?php include '../../left_navbar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!--<section class="content-header">-->
        <!--    <h1>Homework / Exam List</h1>-->
        <!--</section>-->

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                        </div>

                        <div class="box-body">
                      
                      <?php
                $student_id = ($_SESSION['scl_reg_no'] ?? 0);
                $stu = mysqli_query($con, "SELECT id,scl_reg_no,scl_name FROM rrsv_student_registration WHERE scl_reg_no='$student_id'");
                $student = mysqli_fetch_assoc($stu);
                
                if (isset($_POST['recharge'])) {
                
                    $student_id = $_POST['student_id'];
                    $amount     = (float)$_POST['amount'];
                    $note       = ($_POST['note'])??'Order by'. ($student_id);
                
                    mysqli_begin_transaction($con);
                
                    try {
                
                        /* 1️⃣ Check wallet exists */
                        $check = mysqli_query($con,
                            "SELECT balance 
                             FROM rrsv_food_wallet 
                             WHERE student_id = '$student_id'
                             FOR UPDATE"
                        );
                
                
                        if (mysqli_num_rows($check) == 0) {
                            /* 2️⃣ First time → INSERT wallet */
                            mysqli_query($con, "
                                INSERT INTO rrsv_food_wallet (student_id, balance)
                                VALUES ('$student_id', $amount)
                            ");
                        } else {
                            /* 3️⃣ Already exists → UPDATE */
                            mysqli_query($con, "
                            UPDATE rrsv_food_wallet
                            SET balance = balance + $amount
                            WHERE student_id = '$student_id'
                        ");
                
                        }
                /* 4️⃣ Generate txn_id */
                $txn_id = generateTxnId($con);
               
                        /* 4️⃣ Insert ledger */
                        mysqli_query($con, "
                            INSERT INTO rrsv_food_wallet_ledger
                            (student_id,tns_id, amount, type, description)
                            VALUES
                            ('$student_id','$txn_id', $amount, 'Credit', '$note')
                        ");
                
                        mysqli_commit($con);
                        $success = "Wallet recharged successfully";
                
                    } catch (Exception $e) {
                //print_r($e);die();
                        mysqli_rollback($con);
                        $error = "Recharge failed";
                    }
                }
                





/* ================= Food order ================= */

/* ================= AJAX HANDLERS ================= */

if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_orders') {
    $date = $_GET['date'];

    $q = mysqli_query($con, "
        SELECT oi.id, oi.meal_type, p.package_name, oi.price
        FROM rrsv_food_order_items oi
        JOIN rrsv_food_orders o ON o.id=oi.order_id
        JOIN rrsv_menu_package p ON p.id=oi.food_id
        WHERE o.student_id='$student_id'
        AND oi.order_date='$date' AND oi.status='Confirmed'
        ORDER BY oi.meal_type
    ");

    $data = [];
    while ($r = mysqli_fetch_assoc($q))
        $data[] = $r;
    echo json_encode($data);
    exit;
}


if (isset($_POST['ajax']) && $_POST['ajax'] === 'cancel_order') {

   $student_id = ($_SESSION['scl_reg_no'] ?? 0);
    if (!$student_id) {
        echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
        exit;
    }
    $id = (int)$_POST['id'];

    if (!$student_id || !$id) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
        exit;
    }

    mysqli_begin_transaction($con);

    try {

        $row = mysqli_fetch_assoc(mysqli_query($con, "
            SELECT oi.price, oi.quantity, oi.status, oi.order_date
            FROM rrsv_food_order_items oi
            JOIN rrsv_food_orders o ON o.id = oi.order_id
            WHERE oi.id = '$id'
            AND o.student_id = '$student_id'
            FOR UPDATE
        "));

        if (!$row) {
            throw new Exception('Order not found');
        }

        if (strtotime($row['order_date']) < strtotime(date('Y-m-d'))) {
            throw new Exception('Past orders cannot be cancelled');
        }

        if ($row['status'] === 'Cancelled') {
            throw new Exception('Already cancelled');
        }

        $refund = $row['price'] * $row['quantity'];

        mysqli_query($con, "
            UPDATE rrsv_food_order_items
            SET status = 'Cancelled'
            WHERE id = '$id'
        ");

        mysqli_query($con, "
            UPDATE rrsv_food_wallet
            SET balance = balance + $refund
            WHERE student_id = '$student_id'
        ");

        mysqli_query($con, "
            INSERT INTO rrsv_food_wallet_ledger
            (student_id, amount, type, description)
            VALUES
            ('$student_id', $refund, 'Credit', 'Food order cancelled')
        ");

        mysqli_commit($con);

        echo json_encode([
    'status' => 'ok',
    'msg' => 'Order cancelled successfully'
]);

    }
    catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }

    exit;
}

function generateTxnId($con)
{
    $date = date('ymd'); // 240207

    $res = mysqli_query(
        $con,
        "SELECT COUNT(*) AS total 
         FROM rrsv_food_wallet_ledger 
         WHERE DATE(created_at) = CURDATE()"
    );

    $row = mysqli_fetch_assoc($res);
    $seq = str_pad($row['total'] + 1, 2, '0', STR_PAD_LEFT);

    return "TXN{$date}{$seq}";
}

/* ================= WALLET ================= */

$wallet = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM rrsv_food_wallet WHERE student_id='$student_id'"));
$balance = $wallet['balance'] ?? 0;

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

/* ================= ORDER SUBMIT ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_orders'])) {
//print_r($_POST);die();
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

                $chk = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(*) c
                FROM rrsv_food_order_items oi
                JOIN rrsv_food_orders o ON o.id=oi.order_id
                WHERE o.student_id='$student_id'
                AND oi.order_date='$date'
                AND oi.meal_type='$meal' AND oi.status='Confirmed'
            "));

                if ($chk['c'] > 0) {
                    $error = "Already ordered $meal on $date";
                    break 2; // ✅ FIXED
                }

                foreach ($food_ids as $fid) {
                    $p = mysqli_fetch_assoc(mysqli_query($con, "
                    SELECT price FROM rrsv_menu_package WHERE id='$fid'
                "));
                    $total += $p['price'];
                    $items[] = [$fid, $meal, $date, $p['price']];
                }
            }
        }
    }


    if (!$error) {
        if (!$items && $total==0) {
            $error = "No items selected";
        }elseif ($balance < $total) {
            $error = "Insufficient wallet balance";
        } else {
            mysqli_query($con, "INSERT INTO rrsv_food_orders(student_id,start_date,end_date,total_amount,status)
                VALUES('$student_id','$start_date','$end_date','$total','Confirmed')");
            $oid = mysqli_insert_id($con);

            foreach ($items as $i) {
                mysqli_query($con, "INSERT INTO rrsv_food_order_items(order_id,food_id,meal_type,order_date,price)
                    VALUES('$oid','$i[0]','$i[1]','$i[2]','$i[3]')");
            }

            mysqli_query($con, "UPDATE rrsv_food_wallet SET balance=balance-$total WHERE student_id='$student_id'");
            $note='Food Order';
            /* 4️⃣ Generate txn_id */
                $txn_id = generateTxnId($con);
               
             mysqli_query($con, "
            INSERT INTO rrsv_food_wallet_ledger
            (student_id,tns_id, amount, type, description)
            VALUES
            ('$student_id','$txn_id', $total, 'Debit', '$note')");

            $success = "Order placed successfully. Total ₹$total";
        }
    }
}

/* ================= CALENDAR DATA ================= */

$events = [];
$q = mysqli_query($con, "
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
     #calendar {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      min-height: 600px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
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
	<style>
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
		.page-title {
    font-weight: 600;
}

.panel-heading {
    border-radius: 4px 4px 0 0;
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

.custom-table tbody tr:nth-child(odd) {
    background-color: #f1f5f9;   /* light gray-blue */
}

.custom-table tbody tr:nth-child(even) {
    background-color: #e2e8f0;   /* deeper gray */
}

.custom-table tbody tr:hover {
    background-color: #cbd5e1 !important;
}
.nav-tabs>li>a {
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
    background-color: #39a1ae !important;
    color:#fff;
}
.nav-tabs > li {
    margin-bottom: 20px;
    margin-right: 6px;
}

	</style>


                    <div class="panel-heading bg-primary text-white p-15">
                        <div class="row align-items-center">
                    
                            <!-- LEFT : Title -->
                            <div class="col-md-8 col-sm-12">
                                <h3 class="m-0 font-weight-bold">
                                    🍽️ Order Your Healthy Food
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
                    <div class="panel-heading text-red text-white p-5">
                        <div class="row align-items-center">
                    
                            <!-- LEFT : Title -->
                            <div class="col-md-8 col-sm-12">
                                <span class="mt-5">
                                    <strong></b>NB: Orders must be placed by 12:00 midnight on the previous day.</strong>
                                </span>
                                </div>
                    
                        </div>
                    </div>

				<!--<h2 class="page-title">Food Wallet & Orders  <?= $student['scl_name'] ?>(<?= $student['scl_reg_no'] ?>) Wallet Balance: <?=$balance ?></h2>-->

				<ul class="nav nav-tabs p-5" role="tablist">
				    <li class="active"><a href="#food_order" role="tab" data-toggle="tab">Food Order</a></li>
					<li><a href="#d_order" role="tab" data-toggle="tab">Calendar Order📅</a></li>
					<li><a href="#recharge_history" role="tab" data-toggle="tab">Recharge History</a></li>
					
					<li><a href="#order_summary" role="tab" data-toggle="tab">Order Summary</a></li>
					<li><a href="#wallet_ledger" role="tab" data-toggle="tab">Your Transactions</a></li>
				</ul>
				<div class="tab-content" style="margin-top:20px;">
				    <div class="tab-pane" id="recharge">
				        <div id="msg_box"></div>

						<?php if (!empty($success)) { ?>
							<div class="succWrap"><?= $success ?></div>
						<?php } ?>
						<?php if (!empty($error)) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } ?>

						<form method="post">
							<div class="form-group mb-2 row">
								<div class="col-md-4">
									<label><?= $student['scl_name'] ?></label>
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
									<input type="text" name="note" class="form-control" value="Wallet Recharge">
								</div>
							</div>
							<div class="form-group mb-2 row">
								<div class="col-md-2" style="margin-top:25px;">
									<button class="btn btn-success" name="recharge">Recharge</button>
								</div>
							</div>
						</form>
					</div>
					 
					<div class="tab-pane panel-body table-responsive" id="recharge_history">
						<table class="table table-bordered table-striped">
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
								$q = mysqli_query($con, "
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
					<div class="tab-pane fade active in" id="food_order">
						<?php if (!empty($success)) { ?>
							<div class="succWrap"><?= $success ?></div>
						<?php } ?>
						<?php if (!empty($error)) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } ?>
						
                        <!-- ================= ORDER FORM ================= -->
                        <form method="post">
                            
                                <div class="row mb-3 align-items-center">
    <?php
$tomorrow = date('Y-m-d', strtotime('+1 day'));
?>
                                    <div class="col-md-4 d-flex align-items-center">
                                        <label class="me-2 mb-0 fw-bold">Start Date:</label>
                                        <input type="date" name="start_date" class="form-control" required min="<?= $tomorrow ?>">
                                    </div>
                                    
                                    <div class="col-md-4 d-flex align-items-center">
                                        <label class="me-2 mb-0 fw-bold">End Date:</label>
                                        <input type="date" name="end_date" class="form-control" required min="<?= $tomorrow ?>">
                                    </div>

                                
                                   
                                
                                </div>


                                <div class="panel-body table-responsive mt-5">
            
                                 <table class="table table-bordered table-striped mt-3 custom-table">
                                    <tr>
                                        <th>Day</th><?php foreach ($meals as $m)
                                            echo "<th>". $m."<input type='checkbox' class='meal-check-all ml-5 mt-5 p-5' data-meal='<?= $m ?>'></th>"; ?>
                                            
    
                                    </tr>
                                    <?php foreach ($days as $day) { ?>
                                        <tr>
                                            <td><b><?= $day ?></b></td>
                                            <?php foreach ($meals as $meal) { ?>
                                                <td>
                                                    <?php
                                                    $qm = mysqli_query($con, "
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
                                 <div class="col-md-4 m-24">
                                        <button type="submit" name="submit_orders" class="btn btn-primary w-100">
                                            Place Orders
                                        </button>
                                    </div>
                                </div>
                        </form>
        
						
					</div>
					<div class="tab-pane" id="d_order">
					    
                      <!-- ================= CALENDAR ================= -->
                        <h3>Your Orders Calendar</h3>
                       <div id="calendar" style="max-width: 100%; margin: 20px auto;"></div>
        
                        <!-- ================= MODAL ================= -->
                        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Orders</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body" id="orderBody">
                                        <!-- AJAX content will go here -->
                                    </div>
                                </div>
                            </div>
                        </div>



					 </div>
					<div class="tab-pane panel-body table-responsive" id="order_summary">

						<table class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>Food Package</th>
									<th>Meal</th>
									<th>Status</th>
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

								$q = mysqli_query($con, "
                                    SELECT 
                                        i.id,
                                        o.id AS order_id,
                                        o.created_at,
                                        i.order_date,
                                        i.meal_type,
                                        i.price,
                                        i.quantity,
                                         i.status,
                                        (i.price * i.quantity) AS item_total,
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
								// 			echo "
        //     <tr style='background:#f8f9fa;font-weight:bold'>
        //         <td colspan='7' class='text-right'>Order Total</td>
        //         <td>" . number_format($orderTotal, 2) . "</td>
        //     </tr>";
											$orderTotal = 0;
										}

										echo "
        <tr style='background:#e9ecef;font-weight:bold'>
            <td colspan='8'>
                🧾 Order #{$r['order_id']} 
                | Created: " . date('d-m-Y h:i A', strtotime($r['created_at'])) . "
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
										<td>
                                            <?php
                                            $canDelete = (
                                                $r['status'] === 'Confirmed'
                                                && strtotime($r['order_date']) >= strtotime(date('Y-m-d', strtotime('+1 day')))
                                            );

                                            if ($r['status'] === 'Cancelled') { ?>
                                                <span class="label label-danger">Cancelled</span>
                                            <?php } else { ?>
                                                <span class="label label-success">Confirmed</span>
                                        
                                                <?php if ($canDelete) { ?>
                                                <button type="button"
        class="btn btn-danger btn-xs cancel-btn"
        data-id="<?= (int)$r['id'] ?>">
    ✖
</button>
                                                <?php } else { ?>
                                                    <button class="btn btn-default btn-xs" disabled>
                                                        Locked
                                                    </button>
                                                <?php } ?>
                                        
                                            <?php } ?>
                                        </td>

										<td><?= date('d-m-Y', strtotime($r['order_date'])) ?></td>
										<!--<td class="text-center"><?= $r['quantity'] ?></td>-->
										<td class="text-right">
										     <?php if ($r['status'] === 'Cancelled') { ?>
                                                <span class="text-success">+ <?= number_format($r['price'], 2) ?></span>
                                            <?php } if ($r['status'] === 'Confirmed') { ?>
                                                <span class="text-danger">- <?= number_format($r['price'], 2) ?></span>
                                                <?php }?>
										    
										</td>
										<!--<td class="text-right"><?= number_format($r['item_total'], 2) ?></td>-->
										
									</tr>

								<?php } ?>

								<!-- LAST ORDER TOTAL -->
								<?php if ($currentOrder != 0) { ?>
									<!--<tr style="background:#f8f9fa;font-weight:bold">-->
									<!--	<td colspan="7" class="text-right">Order Total</td>-->
									<!--	<td><?= number_format($orderTotal, 2) ?></td>-->
									<!--</tr>-->
								<?php } ?>

								<!-- GRAND TOTAL -->
								<tr style="background:#d1ecf1;font-weight:bold">
									<td colspan="6" class="text-right">Order Total</td>
									<td><?= number_format($grandTotal, 2) ?></td>
								</tr>

							</tbody>
						</table>
					</div>
					<div class="tab-pane panel-body table-responsive" id="wallet_ledger">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Date</th>
									<!--<th>Credit (₹)</th>-->
									<!--<th>Debit (₹)</th>-->
									<th>Description</th>
									<th>Rs(₹ )</th>
									
								</tr>
							</thead>
							<tbody>

								<?php
								$i = 1;
								$currentStudent = '';
								$creditTotal = 0;
								$debitTotal = 0;

								/* 🔹 Get ledger ordered by student */
								$q = mysqli_query($con, "
                                    SELECT l.*, u.scl_name,u.scl_reg_no
                                    FROM rrsv_food_wallet_ledger l
                                    JOIN rrsv_student_registration u ON u.id = (SELECT MAX(id) FROM rrsv_student_registration  WHERE scl_reg_no = l.student_id) WHERE u.scl_reg_no='$student_id' AND is_delete='No' ORDER BY u.scl_name, l.created_at ASC");

								while ($r = mysqli_fetch_assoc($q)) {

									/* 🔹 When student changes → print summary row of previous student */
									if ($currentStudent != '' && $currentStudent != $r['scl_name']) {
										$balance = $creditTotal - $debitTotal;
										?>
										<tr style="background:#fff3cd;font-weight:bold">
											<td colspan="1">TOTAL</td>
											<td class="text-success text-right"><?= number_format($creditTotal, 2) ?></td>
											<td class="text-danger text-right"><?= number_format($debitTotal, 2) ?></td>
											<td colspan="2">
												Balance : <b>₹ <?= number_format($balance, 2) ?></b>
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
										<!--	<td colspan="5">👤 <?= htmlspecialchars($currentStudent) ?></td>-->
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
									
									</tr>
								<?php } ?>

								<?php
								/* 🔹 Final student total */
								if ($currentStudent != '') {
									$balance = ($creditTotal -$debitTotal);// $debitTotal;
									?>
									<tr style="background:#fff3cd;font-weight:bold">
										<!--<td colspan="1">TOTAL</td>-->
										<!--<td class="text-success text-right"><?= number_format($creditTotal, 2) ?></td>-->
										<!--<td class="text-danger text-right"><?= number_format($debitTotal, 2) ?></td>-->
										<td colspan="5"class="text-success text-right">
										Current	Balance : <b>₹ <?= number_format($balance, 2) ?></b>
										</td>
									</tr>
								<?php } ?>

							</tbody>
						</table>
					</div>

				</div>
		
		
		</div> <!-- /.box-body -->
                    </div> <!-- /.box -->
                </div>
            </div>
        </section>
    </div>

    <?php include '../../footer.php'; ?>
    <div class="control-sidebar-bg"></div>
</div>
<!-- Scripts -->
<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script src="../../assets/js/jquery-2.2.3.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
<script src="../../assets/js/jquery.dataTables.min.js"></script>
<script src="../../assets/js/dataTables.bootstrap.min.js"></script>
<script src="../../assets/js/app.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<!-- Include SweetAlert2 -->
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).on('click','.cancel-btn',function(e){

    e.preventDefault();

    let id = $(this).data('id');

    cancel(id);

});
function cancel(id){

    Swal.fire({
        title:'Are you sure?',
        text:'Do you want to cancel this order?',
        icon:'warning',
        showCancelButton:true,
        confirmButtonText:'Yes'
    }).then((result)=>{

        if(!result.isConfirmed) return;

        $.ajax({
            url:'ajax_food_orders.php',
            type:'POST',
            dataType:'json',
            data:{ajax:'cancel_order',id:id},
            success:function(res){

                if(res.status==='ok'){
                    Swal.fire('Cancelled!',res.msg,'success')
                    .then(()=>{
            window.location.href = window.location.pathname;
        });
                }else{
                    Swal.fire('Error!',res.msg,'error');
                }

            }
        });

    });

    return false;
}
</script>


<script type="text/javascript">
$(document).ready(function () {
setTimeout(function () {
    $('.succWrap').slideUp("slow");
}, 10000);
});
</script>



<!-- YOUR SCRIPT LAST -->
<script>
let calendar;
let calendarLoaded = false;

$(document).on('shown.bs.tab', 'a[href="#d_order"]', function () {

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

                let date = info.event.startStr.split('T')[0];
 // ✅ FIX
                $('#orderModal').modal('show');

                // loading state
                $('#orderBody').html(
                    '<div class="text-center p-3 text-muted">Loading...</div>'
                );

                $.get('?ajax=get_orders&date=' + date, function (res) {

                    let html = `
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Meal</th>
                                    <th>Package</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    if (res.length === 0) {
                        html += `
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No orders found
                                </td>
                            </tr>
                        `;
                    } else {
                        res.forEach(o => {
                            let canDelete = true; // or your rule
                            html += `
                                <tr>
                                    <td>${o.meal_type}</td>
                                    <td>${o.package_name}</td>
                                    <td>₹${o.price}</td>
                                    <td>
                                        ${canDelete
                                            ? `<button class="btn btn-danger btn-sm" onclick="cancel(${o.id})">X</button>`
                                            : `<button class="btn btn-secondary btn-sm" disabled>Locked</button>`
                                        }
                                    </td>
                                </tr>
                            `;
                        });
                    }

                    html += '</tbody></table>';
                    $('#orderBody').html(html);

                }, 'json');
            }
        }
    );

    calendar.render();
    calendarLoaded = true;
});
</script>


    <script>
    $(document).on('change', '.meal-check-all', function () {
        let meal = $(this).data('meal');
        let checked = $(this).is(':checked');
    
        $('.meal-checkbox[data-meal="' + meal + '"]').prop('checked', checked);
    });
    </script>

<script>
function refreshBalance() {
    $.ajax({
        url: 'get_balance.php',
        type: 'GET',
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

// 🔁 Refresh every 5 seconds
setInterval(refreshBalance, 5000);
</script>


<script>
$(function () {
  $("#example1").DataTable();
});

function showMessage(type, text) {
    $('#msg_box').html(`
        <div class="alert alert-${type} alert-dismissible fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ${text}
        </div>
    `);

    // auto-hide after 3 seconds
    setTimeout(function () {
        $('#msg_box .alert').fadeOut();
    }, 3000);
}

</script>
<script>
    $('#from, #to').datepicker();

function activateOrderSummaryTab() {

    // Remove active from all tab headers
    $('.nav-tabs li').removeClass('active');

    // Remove active from all tab contents
    $('.tab-pane').removeClass('fade in active');

    // Add active to Order Summary tab header
    $('a[href="#order_summary"]').parent('li').addClass('active');

    // Add active to Order Summary content
    $('#order_summary').addClass('fade in active');
}
</script>

</body>
</html>
