<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}

/* Week toggle */
$week = isset($_GET['week']) ? $_GET['week'] : 'THIS';

$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];
?>

<!doctype html>
<html>

<head>
    <title>Weekly Menu</title>
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
        .meal-title {
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px
        }

        .menu-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>

        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="text-center">Weekly Menu</h2>

                <div class="text-center">
                    <a href="?week=THIS" class="btn btn-primary <?= ($week == 'THIS') ? 'active' : '' ?>">This Week</a>
                    <a href="?week=NEXT" class="btn btn-default <?= ($week == 'NEXT') ? 'active' : '' ?>">Next Week</a>
                </div>

                <hr>


                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <b>Weekly Menu (<?= $week ?> Week)</b>
                    </div>

                    <div class="panel-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="info">
                                    <th>Day</th>
                                    <?php foreach ($meals as $meal) { ?>
                                        <th class="text-center"><?= $meal ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($days as $day) { ?>
                                    <tr>
                                        <td><b><?= $day ?></b></td>

                                        <?php foreach ($meals as $meal) { ?>

                                            <td>
                                                <?php
                                                $q = mysqli_query($con, "
                                                    SELECT p.package_name, p.price
                                                    FROM rrsv_food_daily_menu m
                                                    JOIN rrsv_menu_package p ON p.id = m.food_id
                                                    WHERE m.week_type = '$week'
                                                    AND m.day_name  = '$day'
                                                    AND m.meal_type = '$meal'
                                                    AND m.status = 'Active'
                                                ");

                                                if (mysqli_num_rows($q) == 0) {
                                                    echo "<span class='text-muted'>Not Available</span>";
                                                } else {
                                                    while ($row = mysqli_fetch_assoc($q)) { ?>
                                                        <div class="menu-item">
                                                            <b><?= $row['package_name'] ?></b><br>
                                                            <span class="text-success">₹<?= $row['price'] ?></span>

                                                            <form method="post" action="student_place_order_range.php">
                                                                <input type="hidden" name="food_id" value="<?= $food_id ?>">
                                                                <input type="hidden" name="meal_type" value="<?= $meal ?>">

                                                                <label>From</label>
                                                                <input type="date" name="from_date" required
                                                                    class="form-control input-sm">

                                                                <label>To</label>
                                                                <input type="date" name="to_date" required
                                                                    class="form-control input-sm">

                                                                <button class="btn btn-xs btn-success" style="margin-top:5px">
                                                                    Order
                                                                </button>
                                                            </form>

                                                        </div>
                                                        <hr style="margin:6px 0">
                                                    <?php }
                                                }
                                                ?>
                                            </td>

                                        <?php } ?>

                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                    
                    
                    
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
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.succWrap').slideUp("slow");
            }, 3000);
        });
    </script>
</body>

</html>