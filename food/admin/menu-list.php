<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($con, "DELETE FROM rrsv_menu_package WHERE id='$id'");
    $msg = "Menu package deleted successfully";
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Menu Package List</title>
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

                <h2 class="page-title">Menu Packages</h2>

                <a href="menu-package-add.php" class="btn btn-success mb-3">
                    ➕ Add Menu Package
                </a>

                <?php if (!empty($msg)) { ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                <?php } ?>

                <table id="example" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM rrsv_food ORDER BY id DESC");

                        $i = 1;
                        while ($r = mysqli_fetch_assoc($q)) {
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $r['name'] ?></td>
                                <td>₹<?= number_format($r['price'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $r['status'] == 'Active' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $r['status'] ?>
                                    </span>
                                </td>
                                <td><?= $r['created_at'] ?></td>
                                
                                
                                <td>
                                    <a href="menu-package-edit.php?id=<?= $r['id'] ?>"
                                        class="btn btn-primary btn-sm">Edit</a>

                                    <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this package?')"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

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
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>

</body>

</html>