<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {


	if (isset($_GET['delete'])) {
		$del_id = intval($_GET['delete']);

		// optional: check exists
		$check = mysqli_query($con, "SELECT id FROM rrsv_food_items WHERE id='$del_id'");
		if (mysqli_num_rows($check) == 0) {
			$error = "Item not found";
		} else {
			if (mysqli_query($con, "DELETE FROM rrsv_food_items WHERE id='$del_id'")) {
				$msg = "Item deleted successfully";
			} else {
				$error = mysqli_error($con);
			}
		}
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

							<table id="zctb" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<!--<th>ID</th>-->
										<th>Item Name</th>
										<!--<th>Unit</th>-->
										<!--<th>Status</th>-->
										<th>Action</th>
									</tr>
								</thead>

								<tbody>
									<?php
									$sql = mysqli_query($con, "SELECT * FROM rrsv_food_items ORDER BY id DESC");
									$cnt = 1;

									while ($row = mysqli_fetch_assoc($sql)) {
										?>
										<tr>
											<td><?= $cnt++; ?></td>
											<!--<td><?= $row['id']; ?></td>-->
											<td><?= $row['item_name']; ?></td>
											<!--<td><?= $row['unit']; ?></td>-->
											<!--<td>-->
											<!--	<?php if ($row['status'] == 'Active') { ?>-->
											<!--		<span class="label label-success">Active</span>-->
											<!--	<?php } else { ?>-->
											<!--		<span class="label label-danger">Inactive</span>-->
											<!--	<?php } ?>-->
											<!--</td>-->

											<td>
												<a href="food_add_item.php?edit=<?= $row['id']; ?>"
													class="btn btn-xs btn-primary">
													<i class="fa fa-pencil"></i>
												</a>


												<a href="food_Items_list.php?delete=<?= $row['id']; ?>"
													onclick="return confirm('Delete this item?')" class="btn btn-xs btn-danger">
													<i class="fa fa-trash"></i>
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
<?php } ?>