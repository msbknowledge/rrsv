<?php
session_start();
error_reporting(0);
include('../../include/header.php');
include('../../include/dbcon.php');

if (empty($_SESSION['alogin'])) {
	header('location:index.php');
} else {

	$msg = $error = "";
	$item_name = $unit = "";
	$edit_id = 0;

	/* ---------- EDIT FETCH ---------- */
	if (isset($_GET['edit'])) {
		$edit_id = intval($_GET['edit']);
		$res = mysqli_query($myDB, "SELECT * FROM rrsv_food_items WHERE id=$edit_id");
		if ($row = mysqli_fetch_assoc($res)) {
			$item_name = $row['item_name'];
			$unit = $row['unit'];
		}
	}

	/* ---------- ADD / UPDATE ---------- */
	if (isset($_POST['submit'])) {

		$item_name = mysqli_real_escape_string($myDB, $_POST['item_name']);
		$unit = $_POST['unit'];
		$edit_id = intval($_POST['edit_id']);

		if ($item_name == "") {
			$error = "Item name and unit required";
		} else {

			// Duplicate check (ignore self in edit)
			$dup = mysqli_query(
				$myDB,
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

				if (!mysqli_query($myDB, $sql)) {
					$error = mysqli_error($myDB);
				}
			}
		}

	}


	?>

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

	
<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   <?= ($edit_id ? 'Edit Food Item' : 'Add Food Item'); ?>
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div>

					<div class="card-body">

						<?php if ($error) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } elseif ($msg) { ?>
							<div class="succWrap"><?= $msg ?></div>
						<?php } ?>

						<div class="panel-body">
							<form method="post">
							    <div class="row align-items-end">
                            <div class="col-md-4">
								<input type="hidden" name="edit_id" value="<?= $edit_id ?>">
									<label>Item Name *</label>
									<input type="text" name="item_name" value="<?= htmlentities($item_name) ?>"
										class="form-control" required>
								</div>
								<div class="col-md-4">
                                <button type="submit" name="submit" class="btn btn-primary">
									<?= ($edit_id ? 'Update Item' : 'Add Item'); ?>
								</button>



								<?php if ($edit_id) { ?>
									<a href="grocery_items_list.php" class="btn btn-default">Cancel</a>
								<?php } ?>
	</div>	</div>
							</form>
						</div>
					</div>



				</div>
			</div>
		</div>
	</div>
		</div>
	 <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
    <script>
$(document).ready(function () {
    $('#foodTable').DataTable({
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

<?php } ?>