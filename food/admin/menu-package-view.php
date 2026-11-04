<?php
session_start();
include('includes/config.php');
$id = (int) $_GET['id'];

$p = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rrsv_menu_package WHERE id='$id'"));
$items = mysqli_query($con, "
    SELECT i.*
    FROM rrsv_menu_package_items pi
    INNER JOIN rrsv_food i ON i.id = pi.food_id
    WHERE pi.package_id = $id
");?>
<!doctype html>
<html>

<head>
    <title>View Package</title>
 


    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/adminlte.min.css">
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
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Projects</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Projects</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">View Package</h2>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-3">
                                Package: <?= $p['package_name'] ?>
                            </div>
                            <div class="col-md-3">
                                Meal: <?= $p['meal_type'] ?>
                            </div>
                            <div class="col-md-3">
                                Price: ₹<?= $p['price'] ?>
                            </div>
                            <div class="col-md-3">
                                Status: <?= $p['active'] ?>
                            </div>
                        </div>
                    </div>
                </div>
               
                <h5>Items</h5>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 1;
                        while ($r = mysqli_fetch_assoc($items)) { ?>
                            <tr>
                                <td><?= $sn++; ?></td>
                                <td><?= htmlspecialchars($r['name']); ?></td>
                                <td><?= htmlspecialchars($r['price']); ?></td>
                                <td><?= htmlspecialchars($r['status']); ?></td>
                                <td><?= htmlspecialchars($r['created_at']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>


                <a href="menu-package-list.php" class="btn btn-secondary">Back</a>
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
    <script src="js/jquery.min.js"></script>
</body>

</html>