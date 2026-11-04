
<?php
session_start();


include('../../include/header.php');
include('../../include/dbcon.php');

if (empty($_SESSION['mid'])) {
	header('location:index.php');
} else {


	$msg = $error = "";
	$item_name = $unit = "";
	$edit_id = 0;

if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);

    // optional: check exists
    $check = mysqli_query($myDB, "SELECT id FROM rrsv_food_items WHERE id='$del_id'");
    if (!$check) {
        $error = mysqli_error($myDB);
    } elseif (mysqli_num_rows($check) == 0) {
        $error = "Item not found";
    } else {
        if (mysqli_query($myDB, "DELETE FROM rrsv_food_items WHERE id='$del_id'")) {
            $msg = "Item deleted successfully";
        } else {
            $error = mysqli_error($myDB);
        }
    }
}



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
                 WHERE LOWER(TRIM(item_name)) = LOWER(TRIM('$item_name'))
                 AND id!=$edit_id"
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


<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   <button class="btn btn-success addBtn"
                        data-toggle="modal"
                        data-target="#itemModal">
                  Add Item
                </button>



                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">

							<?php if ($error) { ?>
								<div class="alert alert-danger"><?= $error ?></div>
							<?php } elseif ($msg) { ?>
								<div class="alert alert-success"><?= $msg ?></div>
							<?php } ?>
							
						
                        <div class="panel-body table-responsive">
							<table id="itemTable" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Item Name</th>
										<th>Action</th>
									</tr>
								</thead>

								<tbody>
									<?php
									$sql = mysqli_query($myDB, "SELECT * FROM rrsv_food_items ORDER BY id Asc");
									$cnt = 1;

									while ($row = mysqli_fetch_assoc($sql)) {
										?>
										<tr>
											<td><?= $cnt++; ?></td>
											<td><?= $row['item_name']; ?></td>
										
											<td>
<!--											   <button-->
<!--   class="btn btn-xs btn-primary editBtn"-->
<!--   data-toggle="modal"-->
<!--   data-target="#itemModal"-->
<!--   data-id="<?= $row['id']; ?>"-->
<!--   data-name="<?= htmlspecialchars($row['item_name'], ENT_QUOTES) ?>">-->
<!--   Edit-->
<!--</button>-->







												<a href="grocery_items_list.php?delete=<?= $row['id']; ?>"
													onclick="return confirm('Delete this item?')" class="btn btn-xs btn-danger">Delete
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

<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="edit_id" value="">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" value="" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="submit" class="btn btn-primary">Add Item</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

   <!-- jQuery already loaded -->
<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<?php include('../../include/footer.php'); ?>

<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">-->
<!--<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>-->
<script>
 $('#itemModal').on('show.bs.modal', function (e) {
    let button = $(e.relatedTarget); // Button that triggered the modal
    let modal  = $(this);

    if (button.hasClass('addBtn')) {
        modal.find('form')[0].reset();
        modal.find('input[name="edit_id"]').val('');
        modal.find('input[name="item_name"]').val('');
        modal.find('.modal-title').text('Add Item');
        modal.find('button[type="submit"]').text('Add Item');
    }

    if (button.hasClass('editBtn')) {
        let id = button.data('id');
        let name = button.data('name');

        console.log("Editing:", id, name); // Debug

        modal.find('input[name="edit_id"]').val(id);
        modal.find('input[name="item_name"]').val(name);
        modal.find('.modal-title').text('Edit Item');
        modal.find('button[type="submit"]').text('Update Item');
    }
});



$('form').on('submit', function () {
    $('#itemModal').modal('hide');
});
</script>

    <script>
$(document).ready(function () {
    $('#itemTable').DataTable({
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
<?php } ?>