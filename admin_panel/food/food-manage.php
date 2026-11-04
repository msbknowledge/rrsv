<?php
session_start();
include('../dbcon.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}

$msg = '';
$edit = false;
$id = 0;
$name = '';
$price = '';
$status = 'Active';

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($con, "DELETE FROM rrsv_food WHERE id='$id'");
    $msg = "Food deleted successfully";
}

/* EDIT LOAD */
if (isset($_GET['edit'])) {
    $edit = true;
    $id = (int) $_GET['edit'];
    $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rrsv_food WHERE id='$id'"));
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
        mysqli_query($con, "
            UPDATE rrsv_food 
            SET name='$name', price='$price', status='$status'
            WHERE id='$id'
        ");
        $msg = "Food updated successfully";
    } else {
        mysqli_query($con, "
            INSERT INTO rrsv_food(name,price,status)
            VALUES('$name','$price','$status')
        ");
        $msg = "Food added successfully";
    }

    $edit = false;
    $name = $price = '';
}
?>
<!doctype html>
<html>

<head>
    <title>Food Master</title>
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
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>

        <div class="content-wrapper">
            <div class="container-fluid">

                <h3>Food Management</h3>

                <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                <?php endif; ?>
                
<form method="post" class="card p-3 mb-4">
    <input type="hidden" name="id" value="<?= $edit ? $id : '' ?>">

    <div class="row align-items-end">
        <!-- Food Name -->
        <div class="col-md-4">
            <label>Food Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   required
                   value="<?= htmlspecialchars($name) ?>">
        </div>

        <!-- Status -->
        <div class="col-md-4">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <!-- Button -->
        <div class="col-md-3 m-24">
            <button name="save" class="btn btn-success w-100">
                <?= $edit ? 'Update' : 'Add' ?>
            </button>
        </div>
    </div>
</form>
<div class="panel-body table-responsive">
                <!-- FOOD LIST -->
                <table class="table table-bordered table-striped">
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
                        $q = mysqli_query($con, "SELECT * FROM rrsv_food ORDER BY id DESC");
                        while ($r = mysqli_fetch_assoc($q)) {
                            ?>
                            <tr>
                                <td><?= $sn++ ?></td>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <!--<td><?= $r['price'] ?></td>-->
                                <td><?= $r['status'] ?></td>
                                <td>
                                    <a href="?edit=<?= $r['id'] ?>" class="btn btn-info btn-sm">Edit</a>
                                    <a href="?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this food?')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
   </div>
            </div>
        </div>
    </div>

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