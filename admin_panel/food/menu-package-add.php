<?php
session_start();
include('../dbcon.php');
if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}
$msg = '';   // ✅ FIX
if (isset($_POST['save'])) {
    $name = $_POST['package_name'];
    $meal = $_POST['meal_type'];
    $price = $_POST['price'];
    $active = $_POST['active'];
    $foods = $_POST['food_id'] ?? [];

    mysqli_query($con, "
        INSERT INTO rrsv_menu_package(package_name,meal_type,price,active)
        VALUES('$name','$meal','$price','$active')
    ");
    $pid = mysqli_insert_id($con);

    foreach ($foods as $fid) {
        mysqli_query($con, "
            INSERT INTO rrsv_menu_package_items(package_id,food_id)
            VALUES('$pid','$fid')
        ");
    }
    $msg = "Package added successfully";
}
?>
<!doctype html>
<html>

<head>
    <title>Add Menu Package</title>
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
			.form-control {
    height: 44px;
    border-radius: 6px;
}

		</style>

	</head>

	<body>
		<?php include('includes/header.php'); ?>

		<div class="ts-main-content">
			<?php include('includes/leftbar.php'); ?>

			<div class="content-wrapper">
				<div class="container-fluid">
    <h3>Add Menu Package</h3>
    <?php if ($msg)
        echo "<div class='alert alert-success'>$msg</div>"; ?>

    <form method="post">
          <!-- ROW 1 -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <select class="form-control" name="meal_type" required>
                    <option value="">Select Meal</option>
                    <option>Breakfast</option>
                    <option>Lunch</option>
                    <option>Dinner</option>
                    <option>Tiffin</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <select class="form-control" name="active" required>
                    <option value="Yes">Active</option>
                    <option value="No">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- ROW 2 -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <input type="text"
                       class="form-control"
                       name="package_name" id="package_name"
                       placeholder="Package Name"
                       readonly>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <input type="number"
                       step="0.01"
                       class="form-control"
                       name="price" id="price"
                       placeholder="Price" required>
            </div>
        </div>
    </div>

        <!--<select class="form-control mb-2" name="meal_type" required>-->
        <!--    <option value="">Select Meal</option>-->
        <!--    <option>Breakfast</option>-->
        <!--    <option>Lunch</option>-->
        <!--    <option>Dinner</option>-->
        <!--    <option>Tiffin</option>-->
        <!--</select>-->

        <!--<select class="form-control mb-2" name="active">-->
        <!--    <option value="Yes">Active</option>-->
        <!--    <option value="No">Inactive</option>-->
        <!--</select>-->
        <!--<input class="form-control mb-2" name="package_name" id="package_name" placeholder="Package Name" readonly>-->

        <!--<input type="number" step="0.01" class="form-control mb-2" name="price" id="price">-->

      
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Items</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <?php
                        $q = mysqli_query($con, "SELECT * FROM rrsv_food WHERE status='Active'");
                        while ($r = mysqli_fetch_assoc($q)) {
                            ?>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="food-check" name="food_id[]" value="<?= $r['id'] ?>"
                                            data-price="<?= $r['price'] ?>" data-name="<?= $r['name'] ?>">
                                        <label for="customCheckbox1" class="custom-control-label"> <?= $r['name'] ?>
                                            </label>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>



                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <br>
        <button class="btn btn-success" name="save">Save</button>
        <a href="menu-package-list.php" class="btn btn-primary">Back</a>
    </form>

    
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

            $('.food-check').on('change', function () {

                let total = 0;
                let names = [];

                $('.food-check:checked').each(function () {
                    // total += parseFloat($(this).data('price'));
                    names.push($(this).data('name'));
                });

                // $('#price').val(total.toFixed(2));
                $('#package_name').val(names.join(', '));
            });

        });
    </script>

</body>

</html>