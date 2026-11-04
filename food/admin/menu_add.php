<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}

$week = isset($_GET['week']) ? $_GET['week'] : 'THIS';
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday','Saturday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

if (isset($_POST['save_menu'])) {

    $week_type = $_POST['week_type'];
    mysqli_query($con, "DELETE FROM rrsv_food_daily_menu WHERE week_type='$week_type'");

    foreach ($_POST['menu'] as $day => $mealsData) {

        foreach ($mealsData as $meal => $items) {

            foreach ($items as $i => $itemPart) {
                if (isset($itemPart['item_id'])) {
                    $item_id = $itemPart['item_id'];
                }
                if (isset($itemPart['qty'])) {
                    $qty = $itemPart['qty'];
                }
            }

            if (!empty($item_id)) {
                mysqli_query($con, "
                    INSERT INTO rrsv_food_daily_menu
                    (week_type,day_name,meal_type,food_id,qty)
                    VALUES (
                        '$week_type',
                        '$day',
                        '$meal',
                        '$item_id',
                        '$qty'
                    )
                ");
            }

        }
    }

    $msg = "Menu Saved Successfully";
}
?>

<!doctype html>
<html>

<head>
    <title>Admin Menu Planner</title>
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

                <h2 class="page-title">Admin Menu Planner</h2>

                <a href="?week=THIS" class="btn btn-primary <?= ($week == 'THIS') ? 'active' : '' ?>">This Week</a>
                <a href="?week=NEXT" class="btn btn-default <?= ($week == 'NEXT') ? 'active' : '' ?>">Next Week</a>

                <br><br>

                <?php if (isset($msg)) { ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                <?php } ?>

                <form method="post">
                    <input type="hidden" name="week_type" value="<?= $week ?>">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Breakfast</th>
                                <th>Lunch</th>
                                <th>Dinner</th>
                                <th>Tiffin</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($days as $day) { ?>
                                <tr>
                                    <td><b><?= $day ?></b></td>

                                    <?php foreach ($meals as $meal) { ?>
                                        <td>
                                            <div class="meal-box">
                                                <?php
                                                $q = mysqli_query($con, "
                                                        SELECT * FROM rrsv_food_daily_menu
                                                        WHERE week_type='$week' AND day_name='$day' AND meal_type='$meal'
                                                    ") or die(mysqli_error($con));

                                                if (mysqli_num_rows($q) == 0) { ?>
                                                    <div class="menu-row" style="margin-bottom:5px;">
                                                        <select name="menu[<?= $day ?>][<?= $meal ?>][][item_id]"
                                                            class="form-control input-sm" style="width:65%;display:inline-block">

                                                            <option value="">Select Package</option>

                                                            <?php
                                                            $items = mysqli_query($con, "
                                                                SELECT * FROM rrsv_menu_package WHERE active=1
                                                            ");

                                                            while ($i = mysqli_fetch_assoc($items)) {

                                                                $sel = (isset($m['food_id']) && $m['food_id'] == $i['id']) ? 'selected' : '';

                                                                    echo "<option value='{$i['id']}' $sel>
                                                                        {$i['package_name']}
                                                                    </option>";
                                                            }
                                                            ?>
                                                        </select>

                                                        <input type="number" step="0.01"
                                                            name="menu[<?= $day ?>][<?= $meal ?>][][qty]"
                                                            value="<?= $m['qty'] ?? 1 ?>" class="form-control input-sm"
                                                            style="width:30%;display:inline-block">
                                                    </div>

                                                <?php } else {
                                                    while ($m = mysqli_fetch_assoc($q)) { ?>
                                                        <div class="menu-row" style="margin-bottom:5px;">
                                                            <select name="menu[<?= $day ?>][<?= $meal ?>][][item_id]"
                                                                class="form-control input-sm" style="width:65%;display:inline-block">

                                                                <option value="">Select Package</option>

                                                                <?php
                                                                $items = mysqli_query($con, "
                                                                    SELECT * FROM rrsv_menu_package WHERE active=1
                                                                ");

                                                                while ($i = mysqli_fetch_assoc($items)) {

                                                                    $sel = (isset($m['food_id']) && $m['food_id'] == $i['id']) ? 'selected' : '';

                                                                            echo "<option value='{$i['id']}' $sel>
                                                                        {$i['package_name']}
                                                                    </option>";
                                                                }
                                                                ?>
                                                            </select>

                                                            <input type="number" step="0.01"
                                                                name="menu[<?= $day ?>][<?= $meal ?>][][qty]"
                                                                value="<?= $m['qty'] ?? 1 ?>" class="form-control input-sm"
                                                                style="width:30%;display:inline-block">
                                                        </div>

                                                    <?php }
                                                }
                                                ?>
                                                <button type="button" class="btn btn-xs btn-success addRow">+</button>
                                            </div>
                                        </td>
                                    <?php } ?>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <button type="submit" name="save_menu" class="btn btn-success btn-lg">
                        💾 Save Menu
                    </button>

                </form>

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
        $(document).on('click', '.addRow', function () {
            var html = $(this).closest('.meal-box').find('.menu-row:first').clone();
            html.find('select,input').val('');
            $(this).before(html);
        });
    </script>

</body>

</html>