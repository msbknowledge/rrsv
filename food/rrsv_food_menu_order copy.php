<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// Assume logged-in student ID is stored in session
$student_id = $_SESSION['student_id'] ?? 1; // Replace with actual session logic

$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

// Handle form submission
if (isset($_POST['submit_orders'])) {
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];
    $orders = $_POST['orders'] ?? [];

    $period = new DatePeriod(
        new DateTime($start_date),
        new DateInterval('P1D'),
        (new DateTime($end_date))->modify('+1 day')
    );

    foreach ($period as $date) {
        $date_str = $date->format('Y-m-d');
        $day_name = $date->format('l'); // Sunday, Monday, etc.

        if (isset($orders[$day_name])) {
            foreach ($orders[$day_name] as $meal_type => $food_ids) {
                foreach ($food_ids as $food_id) {
                    mysqli_query($con, "
                        INSERT INTO rrsv_student_orders
                        (student_id, food_id, meal_type, order_date)
                        VALUES
                        ('$student_id', '$food_id', '$meal_type', '$date_str')
                    ");
                }
            }
        }
    }
    $success_msg = "Orders placed successfully!";
}

?>

<!doctype html>
<html>
<head>
    <title>Menu Order</title>
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
        .meal-title { font-weight: bold; color: #2c3e50; margin-top: 10px; }
        .menu-card { background: #f8f9fa; padding: 10px; border-radius: 6px; }
        .menu-item { margin-bottom: 5px; }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <h2 class="text-center">Menu Order</h2>
            <?php if (!empty($success_msg)) { ?>
                <div class="alert alert-success text-center"><?= $success_msg ?></div>
            <?php } ?>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Start Date:</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>End Date:</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" name="submit_orders" class="btn btn-primary">Place Orders</button>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading text-center">Select Menu Items</div>
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
                                                    SELECT p.id, p.package_name, p.price
                                                    FROM rrsv_food_daily_menu m
                                                    JOIN rrsv_menu_package p ON p.id = m.food_id
                                                    WHERE m.week_type = 'THIS'
                                                    AND m.day_name = '$day'
                                                    AND m.meal_type = '$meal'
                                                    AND m.status = 'Active'
                                                ");
                                                if (mysqli_num_rows($q) == 0) {
                                                    echo "<span class='text-muted'>Not Available</span>";
                                                } else {
                                                    while ($row = mysqli_fetch_assoc($q)) { ?>
                                                        <div class="menu-item">
                                                            <input type="checkbox" name="orders[<?= $day ?>][<?= $meal ?>][]" value="<?= $row['id'] ?>">
                                                            <b><?= $row['package_name'] ?></b> - ₹<?= $row['price'] ?>
                                                        </div>
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
            </form>

            <!-- Display Student Orders -->
            <h3 class="mt-4">Your Orders</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="info">
                            <th>Date</th>
                            <th>Meal</th>
                            <th>Item</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders_q = mysqli_query($con, "
                            SELECT o.order_date, o.meal_type, p.package_name, p.price
                            FROM rrsv_student_orders o
                            JOIN rrsv_menu_package p ON p.id = o.food_id
                            WHERE o.student_id = '$student_id'
                            ORDER BY o.order_date, FIELD(o.meal_type,'Breakfast','Lunch','Dinner','Tiffin')
                        ");
                        if (mysqli_num_rows($orders_q) == 0) {
                            echo "<tr><td colspan='4' class='text-center'>No orders yet</td></tr>";
                        } else {
                            while ($row = mysqli_fetch_assoc($orders_q)) { ?>
                                <tr>
                                    <td><?= $row['order_date'] ?></td>
                                    <td><?= $row['meal_type'] ?></td>
                                    <td><?= $row['package_name'] ?></td>
                                    <td>₹<?= $row['price'] ?></td>
                                </tr>
                            <?php }
                        }
                        ?>
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
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.succWrap').slideUp("slow");
            }, 3000);
        });
    </script>
</body>
</html>
