<?php
session_start();


include('../../include/header.php');
include('../../include/dbcon.php');


$msg = '';
$edit = false;
$id = 0;
$name = '';
$price = '';
$status = 'Active';

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($myDB, "DELETE FROM rrsv_food WHERE id='$id'");
    $msg = "Food deleted successfully";
}

/* EDIT LOAD */
if (isset($_GET['edit'])) {
    $edit = true;
    $id = (int) $_GET['edit'];
    $row = mysqli_fetch_assoc(mysqli_query($myDB, "SELECT * FROM rrsv_food WHERE id='$id'"));
    $name = $row['name'];
    $price = $row['price'];
    $status = $row['status'];
}

/* SAVE / UPDATE */
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    if ($_POST['id']) {
        $id = (int) $_POST['id'];
        mysqli_query($myDB, "
            UPDATE rrsv_food 
            SET name='$name', price='$price', status='$status'
            WHERE id='$id'
        ");
        $msg = "Food updated successfully";
    } else {
        mysqli_query($myDB, "
            INSERT INTO rrsv_food(name,price,status)
            VALUES('$name','$price','$status')
        ");
        $msg = "Food added successfully";
    }

    $edit = false;
    $name = $price = '';
}

?>

    <style>
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
        .ml-5{margin-left: 5px !important;}
        .mt-5{margin-top: 5px !important;}
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
                                
                <form method="post" class="card p-3 mb-4">
                    <input type="hidden" name="id" value="<?= $edit ? $id : '' ?>">
                
                    <div class="row align-items-end">
                        <!-- Food Name -->
                        <div class="col-md-4">
                            <label class="m-2">Food Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   required
                                   value="<?= htmlspecialchars($name) ?>">
                        </div>
                
                        <!-- Status -->
                        <div class="col-md-4">
                            <label class="m-2">Status</label>
                            <select name="status" class="form-control">
                                <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                
                        <!-- Button -->
                        <div class="col-md-3 m-24">
                           <button type="button" id="btnSave" class="btn btn-success w-100">
                    <?= $edit ? 'Update' : 'Add' ?>
                </button>
                
                        </div>
                    </div>
                </form>
                <div class="panel-body table-responsive">
                                <!-- FOOD LIST -->
                                <table id="foodTable" class="table table-bordered table-striped">
                
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Food Name</th>
                                            <!--<th>Price (₹)</th>-->
                                            <th>Status</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sn = 1;
                                        $q = mysqli_query($myDB, "SELECT * FROM rrsv_food ORDER BY id DESC");
                                        while ($r = mysqli_fetch_assoc($q)) {
                                            ?>
                                            <tr>
                                                <td><?= $sn++ ?></td>
                                                <td><?= htmlspecialchars($r['name']) ?></td>
                                                <!--<td><?= $r['price'] ?></td>-->
                                                <td><?= $r['status'] ?></td>
                                                <td>
                                                    <a href="?edit=<?= $r['id'] ?>" class="btn btn-info btn-sm">Edit</a>
                                                    <!--<a href="?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm"-->
                                                    <!--    onclick="return confirm('Delete this food?')">Delete</a>-->
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
<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
    <?php include('../../include/footer.php'); ?>

  
<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">-->
<!--<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>-->
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
<script>
$('#btnSave').on('click', function (e) {
    e.preventDefault();

    var btn = $(this);
    btn.prop('disabled', true);
 let name   = $('input[name="name"]').val().trim();
    let status = $('select[name="status"]').val();

    // ✅ CLIENT VALIDATION
    if (name === '') {
        $('form').before(`
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                    <span>Food name is required</span>
                    <button type="button" class="close ml-2 bg-danger " data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `
        );
        return;
    }

    $.ajax({
        url: '', // same PHP page
        type: 'POST',
        data: $('form').serialize() + '&save=1',
        success: function (res) {

            // show success without reload
            $('.alert').remove();
            $('form').before(
                   `
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
        <span>Saved successfully</span>
        <button type="button" class="close ml-2" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
`
               
            );

            // reset form after ADD (not edit)
            if ($('input[name="id"]').val() === '') {
                $('form')[0].reset();
            }
        },
        error: function () {
            alert('Something went wrong');
        },
        complete: function () {
            btn.prop('disabled', false);
        }
    });
});
</script>

    
