<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}

$msg = $error = "";

/* ================= DELETE PURCHASE ================= */
if (isset($_GET['delete'])) {
    $pid = (int) $_GET['delete'];

    // reverse stock
    $items = mysqli_query($con, "
        SELECT item_id, qty 
        FROM rrsv_food_stock_ledger
        WHERE ref_type='PURCHASE' AND ref_id='$pid'
    ");

    while ($r = mysqli_fetch_assoc($items)) {
        mysqli_query($con, "
            UPDATE rrsv_food_stock
            SET quantity = quantity - {$r['qty']}
            WHERE item_id='{$r['item_id']}'
        ");
    }

    // delete ledger + items + purchase
    mysqli_query($con, "DELETE FROM rrsv_food_stock_ledger WHERE ref_type='PURCHASE' AND ref_id='$pid'");
    mysqli_query($con, "DELETE FROM rrsv_food_purchases WHERE id='$pid'");

    $msg = "Purchase deleted & stock reversed";
}
?>

<!doctype html>
<html>

<head>
    <title>Purchase List</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

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
                    <h2 class="page-title">Purchase List</h2>
					<a href="food_purchase_add.php" class="btn btn-success">Add Product</a>
					<br><br>

            <?php if ($msg) { ?>
                <div class="alert alert-success"><?= $msg ?></div><?php } ?>

            <table class="table table-bordered table-striped" id="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($con, "SELECT * FROM rrsv_food_purchases ORDER BY id DESC");
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($q)) {
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['invoice_no'] ?></td>
                            <td><?= $r['purchase_date'] ?></td>
                            <td><?= $r['supplier_name'] ?></td>
                            <td>₹<?= $r['grand_total'] ?></td>
                            <td>
                                <a href="purchase-view.php?id=<?= $r['id'] ?>" 
   class="btn btn-info btn-sm">View</a>
                                <!--<a href="purchase-edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Edit</a>-->
                                <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete purchase?')"
                                    class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
</div>
        </div>
    </div>
<!-- Loading Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap-select.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>
		<script src="js/Chart.min.js"></script>
		<script src="js/fileinput.js"></script>
		<script src="js/chartData.js"></script>
		<script src="js/main.js"></script>
    <script>$('#tbl').DataTable();</script>
</body>

</html>