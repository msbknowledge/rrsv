<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
}
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

        /* 4️⃣ Insert ledger */
        mysqli_query($con, "
            INSERT INTO rrsv_food_wallet_ledger
            (student_id, amount, type, description)
            VALUES
            ('$student_id', $amount, 'Credit', '$note')
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
if (isset($_POST['ajax']) && $_POST['ajax'] == 'cancel_order') {

    $id = (int)$_POST['id'];

    mysqli_begin_transaction($con);

    try {

        // 🔹 Get order price & status
        $q = mysqli_query($con, "
            SELECT oi.price, oi.status
            FROM rrsv_food_order_items oi
            JOIN rrsv_food_orders o ON o.id = oi.order_id
            WHERE oi.id = '$id'
            AND o.student_id = '$student_id'
            FOR UPDATE
        ");

        if (mysqli_num_rows($q) == 0) {
            throw new Exception('Invalid order');
        }

        $row = mysqli_fetch_assoc($q);

        // 🔹 Prevent double cancel
        if ($row['status'] === 'Cancelled') {
            throw new Exception('Already cancelled');
        }

        $price = (float)$row['price'];

        // 🔹 Cancel item
        mysqli_query($con, "
            UPDATE rrsv_food_order_items
            SET status = 'Cancelled'
            WHERE id = '$id'
        ");

        // 🔹 Refund wallet
        mysqli_query($con, "
            UPDATE rrsv_food_wallet
            SET balance = balance + $price
            WHERE student_id = '$student_id'
        ");

        // 🔹 Wallet ledger
        mysqli_query($con, "
            INSERT INTO rrsv_food_wallet_ledger
            (student_id, amount, type, description)
            VALUES
            ('$student_id', $price, 'Credit', 'Food order cancelled')
        ");

        mysqli_commit($con);

        echo json_encode(['status' => 'ok']);
    }
    catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }

    exit;
}

// if (isset($_POST['ajax']) && $_POST['ajax'] == 'cancel_order') {
//     $id = $_POST['id'];

//     $q = mysqli_fetch_assoc(mysqli_query($con, "
//         SELECT oi.price
//         FROM rrsv_food_order_items oi
//         JOIN rrsv_food_orders o ON o.id=oi.order_id
//         WHERE oi.id='$id' AND o.student_id='$student_id'
//     "));

//     //mysqli_query($con, "DELETE FROM rrsv_food_order_items WHERE id='$id'");
//     mysqli_query($con, "UPDATE FROM rrsv_food_order_items status='Cancelled' WHERE id='$id'");
//     mysqli_query($con, "UPDATE rrsv_food_wallet SET balance=balance+{$q['price']} WHERE student_id='$student_id'");
//     mysqli_query($con, "UPDATE FROM rrsv_food_order_items status='Cancelled' WHERE id='$id'");
//     $note='Food Cancelled';
//     mysqli_query($con, "INSERT INTO rrsv_food_wallet_ledger (student_id, amount, type, description) VALUES('$student_id', {$q['price']}, 'Credit', '$note')");
//     echo "OK";
//     exit;
// }

/* ================= WALLET ================= */

$wallet = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM rrsv_food_wallet WHERE student_id='$student_id'"));
$balance = $wallet['balance'] ?? 0;

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

/* ================= ORDER SUBMIT ================= */

if (isset($_POST['submit_orders'])) {
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
             mysqli_query($con, "
            INSERT INTO rrsv_food_wallet_ledger
            (student_id, amount, type, description)
            VALUES
            ('$student_id', $total, 'Debit', '$note')");

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
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
	<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

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

	</style>

</head>

<body>
	<?php include('includes/header.php'); ?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php'); ?>

		<div class="content-wrapper">
			<div class="container-fluid">
			    <div class="panel panel-info">
                            <div class="panel-heading bg-primary text-white p-15">
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

				<!--<h2 class="page-title">Food Wallet & Orders  <?= $student['scl_name'] ?>(<?= $student['scl_reg_no'] ?>) Wallet Balance: <?=$balance ?></h2>-->

				<ul class="nav nav-tabs p-5" role="tablist">
				    <li class="active"><a href="#food_order" role="tab" data-toggle="tab">Food Order</a></li>
					<li><a href="#d_order" role="tab" data-toggle="tab">Calendar Order📅</a></li>
					<li><a href="#recharge_history" role="tab" data-toggle="tab">Recharge History</a></li>
					
					<li><a href="#order_summary" role="tab" data-toggle="tab">Order Summary</a></li>
					<li><a href="#wallet_ledger" role="tab" data-toggle="tab">Wallet Ledger</a></li>
				</ul>
				<div class="tab-content" style="margin-top:20px;">
				    <div class="tab-pane" id="recharge">
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
					 
					<div class="tab-pane" id="recharge_history">
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
									JOIN rrsv_student_registration u ON u.scl_reg_no = l.student_id
									WHERE l.type='Credit' AND l.student_id='$student_id'
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
										<td><?= $r['created_at'] ?></td>
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


                                <div class="panel-body table-responsive">
            
                                 <table class="table table-bordered table-striped mt-3">
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
                                </div>
                        </form>
        
						
					</div>
					<div class="tab-pane" id="d_order">
					    
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
					<div class="tab-pane" id="order_summary">

						<table class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>Food Package</th>
									<th>Meal</th>
									<th>Status</th>
									<th>Date</th>
									<th>Qty</th>
									<th>Rate</th>
									<th>Item Total</th>
									
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
											echo "
            <tr style='background:#f8f9fa;font-weight:bold'>
                <td colspan='7' class='text-right'>Order Total</td>
                <td>" . number_format($orderTotal, 2) . "</td>
            </tr>";
											$orderTotal = 0;
										}

										echo "
        <tr style='background:#e9ecef;font-weight:bold'>
            <td colspan='8'>
                🧾 Order #{$r['order_id']} 
                | Created: " . date('d-m-Y', strtotime($r['created_at'])) . "
            </td>
        </tr>";

										$currentOrder = $r['order_id'];
									}

									$orderTotal += $r['item_total'];
									$grandTotal += $r['item_total'];
									?>

									<tr>
										<td><?= $i++ ?></td>
										<td><?= htmlspecialchars($r['package_name']) ?></td>
										<td><?= $r['meal_type'] ?></td>
										<td>
                                        <?php if ($r['status'] === 'Cancelled') { ?>
                                            <span class="label label-danger">Cancelled</span>
                                        <?php } else { ?>
                                            <span class="label label-success">Confirmed</span>
                                        <?php } ?>
                                        </td>
										<td><?= date('d-m-Y', strtotime($r['order_date'])) ?></td>
										<td class="text-center"><?= $r['quantity'] ?></td>
										<td class="text-right"><?= number_format($r['price'], 2) ?></td>
										<td class="text-right"><?= number_format($r['item_total'], 2) ?></td>
										
									</tr>

								<?php } ?>

								<!-- LAST ORDER TOTAL -->
								<?php if ($currentOrder != 0) { ?>
									<tr style="background:#f8f9fa;font-weight:bold">
										<td colspan="7" class="text-right">Order Total</td>
										<td><?= number_format($orderTotal, 2) ?></td>
									</tr>
								<?php } ?>

								<!-- GRAND TOTAL -->
								<tr style="background:#d1ecf1;font-weight:bold">
									<td colspan="7" class="text-right">GRAND TOTAL</td>
									<td><?= number_format($grandTotal, 2) ?></td>
								</tr>

							</tbody>
						</table>
					</div>
					<div class="tab-pane" id="wallet_ledger">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Credit (₹)</th>
									<th>Debit (₹)</th>
									<th>Description</th>
									<th>Date</th>
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
                                    JOIN rrsv_student_registration u ON u.scl_reg_no = l.student_id WHERE u.scl_reg_no='$student_id'
                                    ORDER BY u.scl_name, l.created_at ASC
                                ");

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
										<tr style="background:#e9ecef;font-weight:bold">
											<td colspan="5">👤 <?= htmlspecialchars($currentStudent) ?></td>
										</tr>
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
										<td class="text-success text-right"><?= $credit ?></td>
										<td class="text-danger text-right"><?= $debit ?></td>
										<td><?= htmlspecialchars($r['description']) ?></td>
										<td><?= date('d-m-Y H:i', strtotime($r['created_at'])) ?></td>
									</tr>
								<?php } ?>

								<?php
								/* 🔹 Final student total */
								if ($currentStudent != '') {
									$balance = ($creditTotal -$debitTotal);// $debitTotal;
									?>
									<tr style="background:#fff3cd;font-weight:bold">
										<td colspan="1">TOTAL</td>
										<td class="text-success text-right"><?= number_format($creditTotal, 2) ?></td>
										<td class="text-danger text-right"><?= number_format($debitTotal, 2) ?></td>
										<td colspan="2">
											Balance : <b>₹ <?= number_format($balance, 2) ?></b>
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
		<!-- Loading Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>
		<script src="js/Chart.min.js"></script>
		<script src="js/fileinput.js"></script>
		<script src="js/chartData.js"></script>
		<script src="js/main.js"></script>
		 <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.succWrap').slideUp("slow");
            }, 3000);
        });
    </script>
<script>
function canDeleteOrder(orderDate) {
    const now = new Date();

    const today = new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate()
    );

    const orderDay = new Date(orderDate);

    // ❌ Past date
    if (orderDay < today) {
        return false;
    }

    // ❌ Today but after 5 AM
    if (orderDay.getTime() === today.getTime() && now.getHours() >= 5) {
        return false;
    }

    // ✅ Future date OR Today before 5 AM
    return true;
}
</script>

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

                $.get('?ajax=get_orders&date=' + date, function (res) {
                    let html = '<table class="table table-bordered">';
                    res.forEach(o => {
                        let canDelete = canDeleteOrder(date);
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
                            </tr>`;
                    });
                    html += '</table>';
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
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').min = today;
        document.getElementById('end_date').min = today;
        if ($_POST['start_date'] < date('Y-m-d')) {
            die('Invalid start date');
        }
        
        if ($_POST['end_date'] < $_POST['start_date']) {
            die('Invalid date range');
        }

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
let foodCalendar;

$('a[href="#food_order"]').on('shown.bs.tab', function () {

    if (foodCalendar) return; // prevent re-init

    foodCalendar = new FullCalendar.Calendar(document.getElementById('foodCalendar'), {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: <?= json_encode($events) ?>,
        eventClick: function (info) {
            let d = info.event.startStr;
            $('#orderModal').modal('show');

            $.get('?ajax=get_orders&date=' + d, function (r) {
                let h = '<table class="table">';
                r.forEach(o => {
                
                let canDelete = canDeleteOrder(d);
                
                h += `<tr>
                    <td>${o.meal_type}</td>
                    <td>${o.package_name}</td>
                    <td>₹${o.price}</td>
                    <td>
                        ${canDelete 
                            ? `<button class="btn btn-danger btn-sm" onclick="cancel(${o.id})">X</button>`
                            : `<button class="btn btn-secondary btn-sm" disabled>Locked</button>`
                        }
                    </td>
                </tr>`;


                });
                h += '</table>';
                $('#orderBody').html(h);
            }, 'json');
        }
    });

    foodCalendar.render();
})


        function cancel(id) {
            if (confirm('Cancel item?')) {
                $.post('', { ajax: 'cancel_order', id: id }, function (res) {
                    if (res.status === 'ok') {
                        alert('Order cancelled');
                        location.reload();
                    } else {
                        alert(res.msg);
                    }
                }, 'json');

            }
        }


    </script>

</body>

</html>