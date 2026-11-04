<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$id = (int) $_GET['id'];

/* ================= PURCHASE MASTER ================= */
$purchase = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT * FROM rrsv_food_purchases WHERE id='$id'
"));

if (!$purchase) {
    echo "Invalid Purchase ID";
    exit;
}

/* ================= PURCHASE ITEMS ================= */
$items = mysqli_query($con, "
    SELECT 
        l.qty,
        l.rate,
        i.item_name,
        i.unit
    FROM rrsv_food_stock_ledger l
    JOIN rrsv_food_items i ON i.id=l.item_id
    WHERE l.ref_type='PURCHASE'
      AND l.ref_id='$id'
");
?>

<!doctype html>
<html>

<head>
    <title>Purchase Details</title>
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

            <h2>Purchase Details</h2>

            <div class="info-box row">
                <div class="col-md-3">
                    <b>Invoice No:</b><br><?= $purchase['invoice_no'] ?>
                </div>
                <div class="col-md-3">
                    <b>Date:</b><br><?= $purchase['purchase_date'] ?>
                </div>
                <div class="col-md-3">
                    <b>Supplier:</b><br><?= $purchase['supplier_name'] ?>
                </div>
                <div class="col-md-3">
                    <b>Grand Total:</b><br>
                    ₹<?= number_format($purchase['grand_total'], 2) ?>
                </div>
            </div>

            <hr>

            <table class="table table-bordered table-striped" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sum = 0;
                    while ($r = mysqli_fetch_assoc($items)) {
                        $total = $r['qty'] * $r['rate'];
                        //$total = $r['rate'];
                        $sum += $total;
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['item_name'] ?> (<?= $r['unit'] ?>)</td>
                            <td><?= $r['qty'] ?></td>
                            <td>₹<?= number_format($r['rate'], 2) ?></td>
                            <td>₹<?= number_format($total, 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Grand Total</th>
                        <th>₹<?= number_format($sum, 2) ?></th>
                    </tr>
                </tfoot>
            </table>

            <a href="purchase-list.php" class="btn btn-secondary">
                ← Back to List
            </a>
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