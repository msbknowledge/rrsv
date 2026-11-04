<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
}
$student_id = ($_GET['student_id'] ?? 0);
$stu = mysqli_query($con, "SELECT id,scl_reg_no,scl_name FROM rrsv_student_registration WHERE scl_reg_no='$student_id'");
$student = mysqli_fetch_assoc($stu);

if (isset($_POST['recharge'])) {

    $student_id = $_POST['student_id'];
    $amount     = (float)$_POST['amount'];
    $note       = mysqli_real_escape_string($con, $_POST['note']);

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
print_r($e);die();
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
        AND oi.order_date='$date'
        ORDER BY oi.meal_type
    ");

    $data = [];
    while ($r = mysqli_fetch_assoc($q))
        $data[] = $r;
    echo json_encode($data);
    exit;
}

if (isset($_POST['ajax']) && $_POST['ajax'] == 'cancel_order') {
    $id = $_POST['id'];

    $q = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT oi.price
        FROM rrsv_food_order_items oi
        JOIN rrsv_food_orders o ON o.id=oi.order_id
        WHERE oi.id='$id' AND o.student_id='$student_id'
    "));

    mysqli_query($con, "DELETE FROM rrsv_food_order_items WHERE id='$id'");
    mysqli_query($con, "UPDATE rrsv_food_wallet SET balance=balance+{$q['price']} WHERE student_id='$student_id'");
    echo "OK";
    exit;
}

/* ================= WALLET ================= */

$wallet = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM rrsv_food_wallet WHERE student_id='$student_id'"));
$balance = $wallet['balance'] ?? 0;

$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday','Saturday'];
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

                $chk = mysqli_fetch_assoc(mysqli_query($con, "
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
        if ($total > $balance) {
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
WHERE o.student_id='$student_id'
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
        .hide-checkbox {
    display: none !important;
}
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
	</style>

</head>

<body>
	<?php include('includes/header.php'); ?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php'); ?>

		<div class="content-wrapper">
			<div class="container-fluid">

				<h2 class="page-title">Food Wallet & Orders</h2>

				<ul class="nav nav-tabs" role="tablist">
					<li class="active"><a href="#recharge" role="tab" data-toggle="tab">Recharge</a></li>
					<li><a href="#recharge_history" role="tab" data-toggle="tab">Recharge History</a></li>
					<li><a href="#food_order" role="tab" data-toggle="tab">Food Order 📅</a></li>
					<li><a href="#order_summary" role="tab" data-toggle="tab">Order Summary</a></li>
					<li><a href="#wallet_ledger" role="tab" data-toggle="tab">Wallet Ledger</a></li>
				</ul>
				<div class="tab-content" style="margin-top:20px;">
				    <div class="tab-pane active" id="recharge">
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
					<div class="tab-pane" id="food_order">
						<?php if (!empty($success)) { ?>
							<div class="succWrap"><?= $success ?></div>
						<?php } ?>
						<?php if (!empty($error)) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } ?>
						
                        <!-- ================= ORDER FORM ================= -->
                        <form method="post">
                            <input type="date" id="start_date" name="start_date" required>
                            <input type="date" id="end_date" name="end_date" required>
                            <button name="submit_orders" class="btn btn-primary btn-sm">Place Order</button>
        
                            <table class="table table-bordered mt-3">
                                <tr>
                                    <th>Day</th><?php foreach ($meals as $m)
                                        echo "<th>". $m."<input type='checkbox' class='meal-check-all' data-meal='<?= $m ?>'></th>"; ?>
                                        

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
                        </form>
        
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
                <td colspan='6' class='text-right'>Order Total</td>
                <td>" . number_format($orderTotal, 2) . "</td>
            </tr>";
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

									$orderTotal += $r['item_total'];
									$grandTotal += $r['item_total'];
									?>

									<tr>
										<td><?= $i++ ?></td>
										<td><?= htmlspecialchars($r['package_name']) ?></td>
										<td><?= $r['meal_type'] ?></td>
										<td><?= date('d-m-Y', strtotime($r['order_date'])) ?></td>
										<td class="text-center"><?= $r['quantity'] ?></td>
										<td class="text-right"><?= number_format($r['price'], 2) ?></td>
										<td class="text-right"><?= number_format($r['item_total'], 2) ?></td>
									</tr>

								<?php } ?>

								<!-- LAST ORDER TOTAL -->
								<?php if ($currentOrder != 0) { ?>
									<tr style="background:#f8f9fa;font-weight:bold">
										<td colspan="6" class="text-right">Order Total</td>
										<td><?= number_format($orderTotal, 2) ?></td>
									</tr>
								<?php } ?>

								<!-- GRAND TOTAL -->
								<tr style="background:#d1ecf1;font-weight:bold">
									<td colspan="6" class="text-right">GRAND TOTAL</td>
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
								<?php } ?>

							</tbody>
						</table>
					</div>

				



					
					<div class="tab-pane" id="recharge_history1">
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

								$query = mysqli_query($con, $sql);

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
        let calendar;

$('a[href="#food_order"]').on('shown.bs.tab', function () {

    if (calendar) return; // prevent re-init

    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: <?= json_encode($events) ?>,
        eventClick: function (info) {
            let d = info.event.startStr;
            $('#orderModal').modal('show');

            $.get('?ajax=get_orders&date=' + d, function (r) {
                let h = '<table class="table">';
                r.forEach(o => {
                    h += `
                        <tr>
                            <td>${o.meal_type}</td>
                            <td>${o.package_name}</td>
                            <td>₹${o.price}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="cancel(${o.id})">X</button>
                            </td>
                        </tr>`;
                });
                h += '</table>';
                $('#orderBody').html(h);
            }, 'json');
        }
    });

    calendar.render();
})


        function cancel(id) {
            if (confirm('Cancel item?')) {
                $.post('', { ajax: 'cancel_order', id: id }, () => location.reload());
            }
        }


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
    </script>

</body>

</html>