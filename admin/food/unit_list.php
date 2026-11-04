<?php
session_start();


include('../../include/header.php');
include('../../include/dbcon.php');


$msg = "";

/* ADD / UPDATE */
if (isset($_POST['save'])) {
    $id   = (int)($_POST['id'] ?? 0);
    $unit = mysqli_real_escape_string($myDB, $_POST['unit_name']);
    $short= mysqli_real_escape_string($myDB, $_POST['short_name']);

    if ($id) {
        mysqli_query($myDB, "
            UPDATE rrsv_food_units 
            SET unit_name='$unit', short_name='$short'
            WHERE id='$id'
        ");
        $msg = "Unit updated successfully";
    } else {
        mysqli_query($myDB, "
            INSERT INTO rrsv_food_units (unit_name, short_name)
            VALUES ('$unit','$short')
        ");
        $msg = "Unit added successfully";
    }
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($myDB, "DELETE FROM rrsv_food_units WHERE id='$id'");
    $msg = "Unit deleted successfully";
}

/* EDIT DATA */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(
        mysqli_query($myDB, "SELECT * FROM rrsv_food_units WHERE id='$id'")
    );
}
?>

     <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

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
<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                

                <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                <?php endif; ?>

<div class="row">

    <!-- LEFT: ADD / EDIT FORM -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <?= $edit ? 'Edit Unit' : 'Add Unit' ?>
            </div>

            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

                    <div class="form-group">
                        <label>Unit Name</label>
                        <input type="text" name="unit_name"
                               value="<?= $edit['unit_name'] ?? '' ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Short Name</label>
                        <input type="text" name="short_name"
                               value="<?= $edit['short_name'] ?? '' ?>"
                               class="form-control" required>
                    </div>

                    <button name="save" class="btn btn-success">
                        <?= $edit ? 'Update' : 'Save' ?>
                    </button>

                    <?php if ($edit) { ?>
                        <a href="unit_master.php" class="btn btn-secondary">Cancel</a>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

   
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Unit Name</th>
                            <th>Short</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $q = mysqli_query($myDB, "SELECT * FROM rrsv_food_units ORDER BY id DESC");
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($q)) {
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['unit_name'] ?></td>
                            <td><?= $r['short_name'] ?></td>
                            <td>
                                <a href="?edit=<?= $r['id'] ?>"
                                   class="btn btn-sm btn-primary">Edit</a>

                                <a href="?delete=<?= $r['id'] ?>"
                                   onclick="return confirm('Delete unit?')"
                                   class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
         


 </div>
        </div>
    </div>

</div>

    <?php include('../../include/footer.php'); ?>
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