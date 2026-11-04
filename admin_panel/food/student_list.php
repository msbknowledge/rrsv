<?php
session_start();
error_reporting(0);
include('../dbcon.php');
if (empty($_SESSION['alogin'])) {
	header('location:index.php');
}
$page_title = "Student Order Summary";
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
    u.scl_reg_no,
    u.scl_name,
    COUNT(DISTINCT o.id) AS total_orders,
    COALESCE(SUM(o.total_amount), 0) AS total_amount
FROM rrsv_student_registration u
LEFT JOIN rrsv_food_orders o ON o.student_id = u.scl_reg_no
GROUP BY u.id, u.scl_name, u.scl_reg_no
ORDER BY total_amount DESC;

                ";

						$query = mysqli_query($con, $sql);

						while ($row = mysqli_fetch_assoc($query)) {
							?>
							<tr>
								<td><?= $cnt++; ?></td>
								<td><?= htmlspecialchars($row['scl_reg_no']); ?></td>
								<td><?= htmlspecialchars($row['scl_name']); ?></td>
								<td class="text-center"><?= $row['total_orders']; ?></td>
								<td class="text-right">
									<?= number_format($row['total_amount'], 2); ?>
								</td>
								<td class="text-center">
									<a href="student_manage.php?student_id=<?= $row['scl_reg_no']; ?>" class="btn btn-xs btn-info">
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

</body>

</html>