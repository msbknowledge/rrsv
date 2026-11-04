<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$student_id = $_SESSION['scl_reg_no'] ?? 1;

/* ================= AJAX HANDLERS ================= */

if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_orders') {
    $date = $_GET['date'];

    $q = mysqli_query($con, "
        SELECT oi.id, oi.meal_type, p.package_name, oi.price
        FROM rrsv_food_order_items oi
        JOIN rrsv_food_orders o ON o.id=oi.order_id
        JOIN rrsv_menu_package p ON p.id=oi.food_id
        WHERE o.student_id='$student_id'
        AND oi.order_date='$date'
        ORDER BY oi.meal_type
    ");

    $data = [];
    while ($r = mysqli_fetch_assoc($q))
        $data[] = $r;
    echo json_encode($data);
    exit;
}

if (isset($_POST['ajax']) && $_POST['ajax'] == 'cancel_order') {
    $id = $_POST['id'];

    $q = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT oi.price
        FROM rrsv_food_order_items oi
        JOIN rrsv_food_orders o ON o.id=oi.order_id
        WHERE oi.id='$id' AND o.student_id='$student_id'
    "));

    mysqli_query($con, "DELETE FROM rrsv_food_order_items WHERE id='$id'");
    mysqli_query($con, "UPDATE rrsv_food_wallet SET balance=balance+{$q['price']} WHERE student_id='$student_id'");
    echo "OK";
    exit;
}

/* ================= WALLET ================= */

$wallet = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM rrsv_food_wallet WHERE student_id='$student_id'"));
$balance = $wallet['balance'] ?? 0;

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

/* ================= ORDER SUBMIT ================= */

if (isset($_POST['submit_orders']) && !empty($_POST['submit_orders']) ) {

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $orders = $_POST['orders'] ?? [];

    $period = new DatePeriod(
        new DateTime($start_date),
        new DateInterval('P1D'),
        (new DateTime($end_date))->modify('+1 day')
    );

    $items = [];
    $total = 0;

    foreach ($period as $d) {
        $date = $d->format('Y-m-d');
        $day = $d->format('l');

        if (!empty($orders[$day])) {
            foreach ($orders[$day] as $meal => $food_ids) {

                $chk = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(*) c
                FROM rrsv_food_order_items oi
                JOIN rrsv_food_orders o ON o.id=oi.order_id
                WHERE o.student_id='$student_id'
                AND oi.order_date='$date'
                AND oi.meal_type='$meal'
            "));

                if ($chk['c'] > 0) {
                    $error = "Already ordered $meal on $date";
                    break 2; // ✅ FIXED
                }

                foreach ($food_ids as $fid) {
                    $p = mysqli_fetch_assoc(mysqli_query($con, "
                    SELECT price FROM rrsv_menu_package WHERE id='$fid'
                "));
                    $total += $p['price'];
                    $items[] = [$fid, $meal, $date, $p['price']];
                }
            }
        }
    }


    if (!$error) {
        if (!$items && $total==0) {
            $error = "No items selected";
        }elseif ($balance < $total) {
            $error = "Insufficient wallet balance";
        } else {
            mysqli_query($con, "INSERT INTO rrsv_food_orders(student_id,start_date,end_date,total_amount,status)
                VALUES('$student_id','$start_date','$end_date','$total','Confirmed')");
            $oid = mysqli_insert_id($con);

            foreach ($items as $i) {
                mysqli_query($con, "INSERT INTO rrsv_food_order_items(order_id,food_id,meal_type,order_date,price)
                    VALUES('$oid','$i[0]','$i[1]','$i[2]','$i[3]')");
            }

            mysqli_query($con, "UPDATE rrsv_food_wallet SET balance=balance-$total WHERE student_id='$student_id'");
            $success = "Order placed successfully. Total ₹$total";
        }
    }
}

/* ================= CALENDAR DATA ================= */

$events = [];
$q = mysqli_query($con, "
SELECT oi.order_date, oi.meal_type, SUM(oi.price) total
FROM rrsv_food_order_items oi
JOIN rrsv_food_orders o ON o.id=oi.order_id
WHERE o.student_id='$student_id'
GROUP BY oi.order_date, oi.meal_type
");

while ($r = mysqli_fetch_assoc($q)) {
    if ($r['meal_type'] == 'Breakfast') {
        $color = '#22c55e';
    } elseif ($r['meal_type'] == 'Lunch') {
        $color = '#f97316';
    } elseif ($r['meal_type'] == 'Dinner') {
        $color = '#6366f1';
    } elseif ($r['meal_type'] == 'Tiffin') {
        $color = '#14b8a6';
    } else {
        $color = '#64748b';
    }


    $events[] = [
        'title' => $r['meal_type'] . " ₹" . $r['total'],
        'start' => $r['order_date'],
        'backgroundColor' => $color
    ];
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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
        .p-5{
             padding: 5px;
        }
        .ml-5{margin-left: 5px;}
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">

                <h3>Wallet Balance: ₹<?= $balance.'-' .$_SESSION['student_id']?></h3>
                <?php if ($error)
                    echo "<div class='alert alert-danger'>$error</div>"; ?>
                <?php if ($success)
                    echo "<div class='alert alert-success'>$success</div>"; ?>

                <!-- ================= ORDER FORM ================= -->
                <form method="post">
                    <div class="row mb-3 panel-heading text-center">
                        <div class="col-md-3">
                        <input type="date" id="start_date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-3">
                        <input type="date" id="end_date" class="form-control" name="end_date" required>
                        </div>
                        <div class="col-md-3">
                        <button name="submit_orders" class="btn btn-primary btn-sm">Place Order</button>
                        </div>
                    </div>
<div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <b>Wallet Balance: <b>₹<?= $balance ?></b>
                    </div>
                    <div class="panel-body table-responsive">
                    <table class="table table-bordered table-striped mt-3">
                        <tr>
                            <th>Day</th><?php foreach ($meals as $m)
                                echo "<th>$m</th>"; ?>
                        </tr>
                        <?php foreach ($days as $day) { ?>
                            <tr>
                                <td><b><?= $day ?></b></td>
                                <?php foreach ($meals as $meal) { ?>
                                    <td>
                                        <?php
                                        $qm = mysqli_query($con, "
                                        SELECT p.id,p.package_name,p.price
                                        FROM rrsv_menu_package p
                                        JOIN rrsv_food_daily_menu d ON d.food_id=p.id
                                        WHERE d.day_name='$day' AND d.meal_type='$meal'
                                        ");
                                                                                while ($r = mysqli_fetch_assoc($qm)) {
                                                                                    echo "<div>
                                        <input  class='ml-5' type='checkbox' name='orders[$day][$meal][]' value='{$r['id']}'>
                                        {$r['package_name']} ₹{$r['price']}
                                        </div>";
                                }
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </table>
                     </div>
                <!-- ================= CALENDAR ================= -->
               
                <div id="calendar" class="p-5"></div>
                </div>
                </form>

               

                <!-- ================= MODAL ================= -->
                <div class="modal fade" id="orderModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Orders</h5>
                            </div>
                            <div class="modal-body" id="orderBody"></div>
                        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                events: <?= json_encode($events) ?>,
                eventClick: function (info) {
                    let d = info.event.startStr;
                    $('#orderModal').modal('show');
                    $.get('?ajax=get_orders&date=' + d, function (r) {
                        let h = '<table class="table">';
                        JSON.parse(r).forEach(o => {
                            h += `<tr>
                        <td>${o.meal_type}</td>
                        <td>${o.package_name}</td>
                        <td>₹${o.price}</td>
                        <td><button class="btn btn-danger btn-sm" onclick="cancel(${o.id})">X</button></td>
                        </tr>`;
                        });
                        h += '</table>';
                        $('#orderBody').html(h);
                    });
                }
            });
            calendar.render();
        });

        function cancel(id) {
            if (confirm('Cancel item?')) {
                $.post('', { ajax: 'cancel_order', id: id }, () => location.reload());
            }
        }
    </script>
<script>
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').min = today;
    document.getElementById('end_date').min = today;
</script>
</body>

</html>