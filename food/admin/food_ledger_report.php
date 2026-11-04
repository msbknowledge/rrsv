<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} 


	$where = "1";

if (!empty($_GET['from_date'])) {
    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $where .= " AND DATE(l.created_at) BETWEEN '$from' AND '$to'";
}

if (!empty($_GET['item_id'])) {
    $item_id = $_GET['item_id'];
    $where .= " AND l.item_id='$item_id'";
}

$sql = "
    SELECT 
        l.*,
        i.item_name,
        i.unit
    FROM rrsv_food_stock_ledger l
    JOIN rrsv_food_items i ON i.id = l.item_id
    WHERE $where
    ORDER BY l.created_at ASC
";

$result = mysqli_query($con,$sql);

	


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

		<title>Manage Items</title>

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

					<h2 class="page-title">Manage Products</h2>
					<a href="food_add_item.php" class="btn btn-success">Add Product</a>
					<br><br>

					<div class="panel panel-default">
						<div class="panel-heading">Product List</div>
						<div class="panel-body">

							<?php if ($error) { ?>
								<div class="alert alert-danger"><?= $error ?></div>
							<?php } elseif ($msg) { ?>
								<div class="alert alert-success"><?= $msg ?></div>
							<?php } ?>

							<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Item</th>
            <th>Type</th>
            <th>IN</th>
            <th>OUT</th>
            <th>Rate</th>
            <th>Balance</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
            <td><?= $row['item_name'] ?> (<?= $row['unit'] ?>)</td>
            <td>
                <span class="label label-info"><?= $row['ref_type'] ?></span>
            </td>
            <td class="text-success">
                <?= $row['direction']=='IN' ? $row['qty'] : '-' ?>
            </td>
            <td class="text-danger">
                <?= $row['direction']=='OUT' ? $row['qty'] : '-' ?>
            </td>
            <td><?= number_format($row['rate'],2) ?></td>
            <td><b><?= $row['balance_qty'] ?></b></td>
            <td><?= $row['remarks'] ?></td>
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

	</body>

	</html>