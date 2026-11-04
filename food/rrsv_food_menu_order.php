<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$student_id = $_SESSION['scl_reg_no'] ?? 1;
//print_r($student_id);die();
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

// Get wallet balance
$wallet = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM rrsv_food_wallet WHERE student_id='$student_id'"));
$balance = $wallet['balance'] ?? 0;

// Handle form submission
if (isset($_POST['submit_orders'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $orders = $_POST['orders'] ?? [];

    $total_amount = 0;
    $order_items = [];

    // $period = new DatePeriod(
    //     new DateTime($start_date),
    //     new DateInterval('P1D'),
    //     (new DateTime($end_date))->modify('+1 day')
    // );
    // foreach ($period as $date) {
    //     $date_str = $date->format('Y-m-d');
    //     $day_name = $date->format('l');

    //     if (isset($orders[$day_name])) {
    //         foreach ($orders[$day_name] as $meal_type => $food_ids) {
    //             foreach ($food_ids as $food_id) {
    //                 $food = mysqli_fetch_assoc(mysqli_query($con, "SELECT price FROM rrsv_menu_package WHERE id='$food_id'"));
    //                 $price = $food['price'] ?? 0;
    //                 $total_amount += $price;
    //                 $order_items[] = [
    //                     'food_id' => $food_id,
    //                     'meal_type' => $meal_type,
    //                     'order_date' => $date_str,
    //                     'price' => $price
    //                 ];
    //             }
    //         }
    //     }
    // }

      /* ===== Build Date loop===== */
$dates = [];
$start = new DateTime($start_date);
$end   = new DateTime($end_date);
$end->modify('+1 day');

while($start < $end){
    $dates[] = $start->format('Y-m-d');
    $start->modify('+1 day');
}

$has_match = false;
 
foreach ($dates as $d) {
    $day = date('l', strtotime($d));
    if (isset($orders[$day])) {
        $has_match = true;
        break;
    }
}

// if (!$has_match) {
//     $error_msg = "Selected date range does not match selected menu days.";
// }

 /* ===== Collect Orders (FIXED) ===== */
/* ===== Collect Orders (FINAL & CORRECT) ===== */


foreach ($dates as $order_date) {

        $day_name = date('l', strtotime($order_date));
 
        if (!isset($orders[$day_name])) {
            continue;
        }

        foreach ($orders[$day_name] as $meal_type => $package_ids) {

            foreach ($package_ids as $package_id) {

                /* ===== SAME DATE + MEAL CHECK ===== */
               $dup_q = mysqli_query($con, "
                    SELECT COUNT(*) AS c
                    FROM rrsv_food_order_items oi
                    JOIN rrsv_food_orders o ON o.id = oi.order_id
                    WHERE o.student_id = '$student_id'
                    AND oi.order_date = '$order_date'
                    AND oi.meal_type = '$meal_type'
                ");
                
                $dup = mysqli_fetch_assoc($dup_q);
                
                if ($dup['c'] > 0) {
                    continue; // skip duplicate meal for that date
                }


                /* ===== PRICE ===== */
                $p_q = mysqli_query($con,"
                    SELECT price FROM rrsv_menu_package WHERE id='$package_id'
                ");

                if (mysqli_num_rows($p_q) == 0) {
                    continue;
                }

                $price = (float)mysqli_fetch_assoc($p_q)['price'];

                $total_amount += $price;

                $order_items[] = [
                    'order_date' => $order_date,
                    'meal_type'  => $meal_type,
                    'package_id' => $package_id,
                    'price'      => $price
                ];
            }
        }
    }



    if ($total_amount > $balance) {
        $error_msg = "Insufficient wallet balance. Your balance: ₹$balance, Total order: ₹$total_amount";
    } else {
        
       // print_r($order_items);die();
        // Insert order
        mysqli_query($con, "INSERT INTO rrsv_food_orders (student_id,start_date,end_date,total_amount,status) 
                            VALUES ('$student_id','$start_date','$end_date','$total_amount','Pending')");
        $order_id = mysqli_insert_id($con);

        // Insert order items
        foreach ($order_items as $item) {
            $check = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(*) AS total
                FROM rrsv_food_order_items
                WHERE order_id = '$order_id'
                AND order_date = '{$item['order_date']}'
                AND meal_type = '{$item['meal_type']}'
            "));

            if ($check['total'] > 0) {
                // SAME DATE + SAME MEAL → SKIP
                $error_msg = "Already Have Order";
                continue;
            }
            // print_r($order_items);
            // die();
            mysqli_query($con, "INSERT INTO rrsv_food_order_items
                (order_id,food_id,meal_type,order_date,price)
                VALUES ('$order_id','" . $item['food_id'] . "','" . $item['meal_type'] . "','" . $item['order_date'] . "','" . $item['price'] . "')");
        }

        // Deduct wallet
        $new_balance = $balance - $total_amount;
        mysqli_query($con, "UPDATE rrsv_food_wallet SET balance='$new_balance' WHERE student_id='$student_id'");

        // Add ledger entry
        mysqli_query($con, "INSERT INTO rrsv_food_wallet_ledger
            (student_id,amount,type,description)
            VALUES ('$student_id','$total_amount','Debit','Menu order #$order_id')");
        mysqli_query($con, "UPDATE rrsv_food_orders SET status='Confirmed' WHERE student_id='$student_id' and id='$order_id'");

        $success_msg = "Order placed successfully! Total: ₹$total_amount. Remaining Balance: ₹$new_balance";
    }
}
?>

<!DOCTYPE html>
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
        .meal-title {
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
        }

        .menu-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
        }

        .menu-item {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="container mt-3">
                    <h2>Menu Order</h2>
                    <p>Wallet Balance: <b>₹<?= $balance ?></b></p>
                    <?php if (!empty($success_msg)) {
                        echo "<div class='alert alert-success'>$success_msg</div>";
                    } ?>
                    <?php if (!empty($error_msg)) {
                        echo "<div class='alert alert-danger'>$error_msg</div>";
                    } ?>

                    <form method="POST">
                        <div class="row mb-3 panel-heading text-center">
                            <div class="col-md-3">
                                <label>Start Date:</label><input type="date" name="start_date" class="form-control" required>
                                
                            </div>
                            <div class="col-md-3">
                                <label>End Date:</label><input type="date" name="end_date" class="form-control" required>
                                
                            </div>
                            <div class="col-md-3 m-24">
                                <button type="submit" name="submit_orders" class="btn btn-primary">Place Orders</button>
                            </div>
                        </div>
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <b>Wallet Balance: <b>₹<?= $balance ?></b>
                    </div>
                    <div class="panel-body table-responsive">
                                        
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="info">
                                <th>Day</th>
                                <?php foreach ($meals as $meal) {
                                    echo "<th>$meal</th>";
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($days as $day) { ?>
                                <tr>
                                    <td><b><?= $day ?></b></td>
                                    <?php foreach ($meals as $meal) { ?>
                                        <td>
                                            <?php
                                            $q = mysqli_query($con, "SELECT id,package_name,price FROM rrsv_menu_package
                                            WHERE id IN (SELECT food_id FROM rrsv_food_daily_menu
                                            WHERE day_name='$day' AND meal_type='$meal' AND status='Active')");
                                            while ($row = mysqli_fetch_assoc($q)) {
                                                echo "<div class='menu-item'>
                                                <input type='checkbox' name='orders[$day][$meal][]' value='" . $row['id'] . "'>
                                                <b>" . $row['package_name'] . "</b> - ₹" . $row['price'] . "
                                            </div>";
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

                    <h3 class="mt-4">Your Orders</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="info">
                                <th>Order Date</th>
                                <th>Meal</th>
                                <th>Item</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orders_q = mysqli_query($con, "SELECT oi.order_date, oi.meal_type, p.package_name, oi.price
                            FROM rrsv_food_order_items oi
                            JOIN rrsv_food_orders o ON o.id=oi.order_id
                            JOIN rrsv_menu_package p ON p.id=oi.food_id
                            WHERE o.student_id='$student_id'
                            ORDER BY oi.order_date, FIELD(oi.meal_type,'Breakfast','Lunch','Dinner','Tiffin')");
                            if (mysqli_num_rows($orders_q) == 0)
                                echo "<tr><td colspan='4' class='text-center'>No orders yet</td></tr>";
                            while ($row = mysqli_fetch_assoc($orders_q)) {
                                echo "<tr>
                                <td>{$row['order_date']}</td>
                                <td>{$row['meal_type']}</td>
                                <td>{$row['package_name']}</td>
                                <td>₹{$row['price']}</td>
                            </tr>";
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