<?php
session_start();
include('../../include/header.php');
include('../../include/dbcon.php');

$week = isset($_GET['week']) ? $_GET['week'] : 'THIS';
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday','Saturday'];
$meals = ['Breakfast', 'Lunch', 'Dinner', 'Tiffin'];

if (isset($_POST['save_menu'])) {

    $week_type = $_POST['week_type'];
    mysqli_query($myDB, "DELETE FROM rrsv_food_daily_menu WHERE week_type='$week_type'");

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
                mysqli_query($myDB, "
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



<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   Admin Menu Planner
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body table-responsive">
                
                <a href="?week=THIS" class="btn btn-primary <?= ($week == 'THIS') ? 'active' : '' ?>">This Week</a>
                <!--<a href="?week=NEXT" class="btn btn-default <?= ($week == 'NEXT') ? 'active' : '' ?>">Next Week</a>-->

                <br><br>

                <?php if (isset($msg)) { ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                <?php } ?>

                <form method="post">
                    <input type="hidden" name="week_type" value="<?= $week ?>">

                    <table class="table table-bordered table-striped">
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
                                                $q = mysqli_query($myDB, "
                                                        SELECT * FROM rrsv_food_daily_menu
                                                        WHERE week_type='$week' AND day_name='$day' AND meal_type='$meal'
                                                    ") or die(mysqli_error($myDB));

                                                if (mysqli_num_rows($q) == 0) { ?>
                                                    <div class="menu-row" style="margin-bottom:5px;">
                                                        <select name="menu[<?= $day ?>][<?= $meal ?>][][item_id]"
                                                            class="form-control input-sm" style="width:95%;display:inline-block">

                                                            <option value="">Select Package</option>

                                                            <?php
                                                            $items = mysqli_query($myDB, "
                                                                SELECT * FROM rrsv_menu_package WHERE active='Yes'
                                                            ");

                                                            while ($i = mysqli_fetch_assoc($items)) {

                                                                $sel = (isset($m['food_id']) && $m['food_id'] == $i['id']) ? 'selected' : '';

                                                                    echo "<option value='{$i['id']}' $sel>
                                                                        {$i['package_name']}-({$i['price']})
                                                                    </option>";
                                                            }
                                                            ?>
                                                        </select>

                                                        <input type="hidden" step="0.01"
                                                            name="menu[<?= $day ?>][<?= $meal ?>][][qty]"
                                                            value="<?= $m['qty'] ?? 1 ?>" class="form-control input-sm"
                                                            style="width:30%;display:inline-block">
                                                    </div>

                                                <?php } else {
                                                    while ($m = mysqli_fetch_assoc($q)) { ?>
                                                        <div class="menu-row" style="margin-bottom:5px;">
                                                            <select name="menu[<?= $day ?>][<?= $meal ?>][][item_id]"
                                                                class="form-control input-sm" style="width:95%;display:inline-block">

                                                                <option value="">Select Package</option>

                                                                <?php
                                                                $items = mysqli_query($myDB, "
                                                                    SELECT * FROM rrsv_menu_package WHERE active='Yes'
                                                                ");

                                                                while ($i = mysqli_fetch_assoc($items)) {

                                                                    $sel = (isset($m['food_id']) && $m['food_id'] == $i['id']) ? 'selected' : '';

                                                                            echo "<option value='{$i['id']}' $sel>
                                                                        {$i['package_name']}-({$i['price']})
                                                                    </option>";
                                                                }
                                                                ?>
                                                            </select>

                                                            <input type="hidden" step="0.01"
                                                                name="menu[<?= $day ?>][<?= $meal ?>][][qty]"
                                                                value="<?= $m['qty'] ?? 1 ?>" class="form-control input-sm"
                                                                style="width:30%;display:inline-block">
                                                        </div>

                                                    <?php }
                                                }
                                                ?>
                                                <!--<button type="button" class="btn btn-xs btn-success addRow">+</button>-->
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
        </div>

    </div>

    <?php include('../../include/footer.php'); ?>

    <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
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