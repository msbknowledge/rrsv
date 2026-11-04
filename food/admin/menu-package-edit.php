<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$msg = '';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("location:menu-package-list.php");
    exit;
}

/* Fetch package */
$p = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT * FROM rrsv_menu_package WHERE id='$id'"
));

/* Fetch selected food IDs */
$selectedFoods = [];
$sf = mysqli_query($con,
    "SELECT food_id FROM rrsv_menu_package_items WHERE package_id='$id'"
);
while ($r = mysqli_fetch_assoc($sf)) {
    $selectedFoods[] = $r['food_id'];
}

/* Update */
if (isset($_POST['update'])) {
    $name   = $_POST['package_name'];
    $meal   = $_POST['meal_type'];
    $price  = $_POST['price'];
    $active = $_POST['active'];
    $foods  = $_POST['food_id'] ?? [];

    mysqli_query($con, "
        UPDATE rrsv_menu_package 
        SET package_name='$name',
            meal_type='$meal',
            price='$price',
            active='$active'
        WHERE id='$id'
    ");

    // Reset items
    mysqli_query($con, "DELETE FROM rrsv_menu_package_items WHERE package_id='$id'");

    foreach ($foods as $fid) {
        mysqli_query($con, "
            INSERT INTO rrsv_menu_package_items(package_id, food_id)
            VALUES('$id','$fid')
        ");
    }

    $msg = "Package updated successfully";

    // Refresh selected foods
    $selectedFoods = $foods;
}
?>
<!doctype html>
<html>
<head>
    <title>Edit Menu Package</title>
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
</head>

<body>
<?php include('includes/header.php'); ?>

<div class="ts-main-content">
<?php include('includes/leftbar.php'); ?>

<div class="content-wrapper">
<div class="container-fluid">

<h3>Edit Menu Package</h3>

<?php if ($msg): ?>
    <div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<form method="post">

    <select class="form-control mb-2" name="meal_type" required>
        <option value="">Select Meal</option>
        <?php
        $meals = ['Breakfast','Lunch','Dinner','Tiffin'];
        foreach ($meals as $m) {
            $sel = ($p['meal_type'] == $m) ? 'selected' : '';
            echo "<option $sel>$m</option>";
        }
        ?>
    </select>

    <select class="form-control mb-2" name="active">
        <option value="Yes" <?= $p['active']=='Yes'?'selected':'' ?>>Active</option>
        <option value="No"  <?= $p['active']=='No'?'selected':'' ?>>Inactive</option>
    </select>

    <input class="form-control mb-2" name="package_name" id="package_name"
           value="<?= htmlspecialchars($p['package_name']) ?>" readonly>

    <input type="number" step="0.01" class="form-control mb-2" name="price"
           id="price" value="<?= $p['price'] ?>" readonly>

    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Items</h3>
        </div>

        <div class="card-body">
            <div class="row">
            <?php
            $q = mysqli_query($con, "SELECT * FROM rrsv_food WHERE status='Active'");
            while ($r = mysqli_fetch_assoc($q)) {
                $checked = in_array($r['id'], $selectedFoods) ? 'checked' : '';
            ?>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>
                            <input type="checkbox"
                                   class="food-check"
                                   name="food_id[]"
                                   value="<?= $r['id'] ?>"
                                   data-price="<?= $r['price'] ?>"
                                   data-name="<?= $r['name'] ?>"
                                   <?= $checked ?>>
                            <?= $r['name'] ?> (₹<?= $r['price'] ?>)
                        </label>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>

    <br>
    <button class="btn btn-primary" name="update">Update</button>
    <a href="menu-package-list.php" class="btn btn-secondary">Back</a>

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
    <script src="js/jquery.min.js"></script>
<script>
$(document).ready(function () {

    function recalc() {
        let total = 0;
        let names = [];

        $('.food-check:checked').each(function () {
            total += parseFloat($(this).data('price'));
            names.push($(this).data('name'));
        });

        $('#price').val(total.toFixed(2));
        $('#package_name').val(names.join(', '));
    }

    $('.food-check').on('change', recalc);

    // initial calculation
    recalc();
});
</script>

</body>
</html>
