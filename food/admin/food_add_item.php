<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {

	$msg = $error = "";
	$item_name = $unit = "";
	$edit_id = 0;

	/* ---------- EDIT FETCH ---------- */
	if (isset($_GET['edit'])) {
		$edit_id = intval($_GET['edit']);
		$res = mysqli_query($con, "SELECT * FROM rrsv_food_items WHERE id=$edit_id");
		if ($row = mysqli_fetch_assoc($res)) {
			$item_name = $row['item_name'];
			$unit = $row['unit'];
		}
	}

	/* ---------- ADD / UPDATE ---------- */
	if (isset($_POST['submit'])) {

		$item_name = mysqli_real_escape_string($con, $_POST['item_name']);
		$unit = $_POST['unit'];
		$edit_id = intval($_POST['edit_id']);

		if ($item_name == "") {
			$error = "Item name and unit required";
		} else {

			// Duplicate check (ignore self in edit)
			$dup = mysqli_query(
				$con,
				"SELECT id FROM rrsv_food_items 
				WHERE item_name='$item_name' AND id!=$edit_id"
			);

			if (mysqli_num_rows($dup) > 0) {
				$error = "Item already exists";
			} else {

				if ($edit_id > 0) {
					// UPDATE
					$sql = "UPDATE rrsv_food_items 
							SET item_name='$item_name', unit='$unit'
							WHERE id=$edit_id";
					$msg = "Item updated successfully";
				} else {
					// INSERT
					$sql = "INSERT INTO rrsv_food_items (item_name, unit)
							VALUES ('$item_name', '$unit')";
					$msg = "Item added successfully";
				}

				if (!mysqli_query($con, $sql)) {
					$error = mysqli_error($con);
				}
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

					<h2 class="page-title">Manage Items</h2>
					<!-- <a href="add-item.php" class="btn btn-success">Add Items</a> -->
					<br><br>

					<div class="panel panel-default">
						<div class="panel-heading">
							<?= ($edit_id ? 'Edit Food Item' : 'Add Food Item'); ?>
						</div>

						<?php if ($error) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } elseif ($msg) { ?>
							<div class="succWrap"><?= $msg ?></div>
						<?php } ?>

						<div class="panel-body">
							<form method="post">

								<input type="hidden" name="edit_id" value="<?= $edit_id ?>">

								<div class="form-group">
									<label>Item Name *</label>
									<input type="text" name="item_name" value="<?= htmlentities($item_name) ?>"
										class="form-control" required>
								</div>

								<!--<div class="form-group">-->
								<!--	<label>Unit *</label>-->
								<!--	<select name="unit" class="form-control" required>-->
								<!--		<option value="">Select Unit</option>-->
								<!--		<option value="Kg" <?= ($unit == 'Kg') ? 'selected' : '' ?>>Kg</option>-->
								<!--		<option value="Ltr" <?= ($unit == 'Ltr') ? 'selected' : '' ?>>Ltr</option>-->
								<!--		<option value="Pcs" <?= ($unit == 'Pcs') ? 'selected' : '' ?>>Pcs</option>-->
								<!--	</select>-->
								<!--</div>-->

								<button type="submit" name="submit" class="btn btn-primary">
									<?= ($edit_id ? 'Update Item' : 'Add Item'); ?>
								</button>

								<?php if ($edit_id) { ?>
									<a href="food_Items_list.php" class="btn btn-default">Cancel</a>
								<?php } ?>

							</form>
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