<?php
session_start();
include('../../include/dbcon.php');
if (empty($_SESSION['alogin'])) {
    header('location:index.php');
}

$id = (int) $_GET['id'];

/* ================= LOAD PURCHASE ================= */
$pur = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rrsv_food_purchases WHERE id='$id'"));

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    // reverse old stock
    $old = mysqli_query($con, "
        SELECT item_id, qty FROM rrsv_food_stock_ledger
        WHERE ref_type='PURCHASE' AND ref_id='$id'
    ");
    while ($r = mysqli_fetch_assoc($old)) {
        mysqli_query($con, "
            UPDATE rrsv_food_stock
            SET quantity = quantity - {$r['qty']}
            WHERE item_id='{$r['item_id']}'
        ");
    }
    mysqli_query($con, "DELETE FROM rrsv_food_stock_ledger WHERE ref_type='PURCHASE' AND ref_id='$id'");

    // update master
    mysqli_query($con, "
        UPDATE rrsv_food_purchases
        SET invoice_no='{$_POST['invoice_no']}',
            purchase_date='{$_POST['purchase_date']}',
            supplier_name='{$_POST['supplier']}'
        WHERE id='$id'
    ");

    // add new stock
    $total = 0;
    for ($i = 0; $i < count($_POST['item_id']); $i++) {
        $item = $_POST['item_id'][$i];
        $qty = $_POST['qty'][$i];
        $rate = $_POST['rate'][$i];
        if ($qty <= 0)
            continue;

        $total += ($qty * $rate);

        mysqli_query($con, "
            UPDATE rrsv_food_stock
            SET quantity=quantity+$qty, avg_price='$rate'
            WHERE item_id='$item'
        ");

        mysqli_query($con, "
            INSERT INTO rrsv_food_stock_ledger
            (item_id,ref_type,ref_id,qty,rate,direction,remarks)
            VALUES('$item','PURCHASE','$id','$qty','$rate','IN','Purchase Edit')
        ");
    }

    mysqli_query($con, "UPDATE rrsv_food_purchases SET grand_total='$total' WHERE id='$id'");
    header("location:purchase-list.php");
}
?>

<!doctype html>
<html>

<head>
    <title>Edit Purchase</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
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
        <h3>Edit Purchase</h3>

        <form method="post">
            <input class="form-control" name="invoice_no" value="<?= $pur['invoice_no'] ?>"><br>
            <input type="date" class="form-control" name="purchase_date" value="<?= $pur['purchase_date'] ?>"><br>
            <input class="form-control" name="supplier" value="<?= $pur['supplier_name'] ?>"><br>

            <table class="table table-bordered">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Rate</th>
                </tr>
                <?php
                $items = mysqli_query($con, "
SELECT l.*,i.item_name
FROM rrsv_food_stock_ledger l
JOIN rrsv_food_items i ON i.id=l.item_id
WHERE l.ref_type='PURCHASE' AND l.ref_id='$id'
");
                while ($r = mysqli_fetch_assoc($items)) {
                    ?>
                    <tr>
                        <td>
                            <select name="item_id[]" class="form-control">
                                <option value="<?= $r['item_id'] ?>"><?= $r['item_name'] ?></option>
                            </select>
                        </td>
                        <td><input name="qty[]" value="<?= $r['qty'] ?>" class="form-control"></td>
                        <td><input name="rate[]" value="<?= $r['rate'] ?>" class="form-control"></td>
                    </tr>
                <?php } ?>
            </table>

            <button name="update" class="btn btn-success">Update Purchase</button>
        </form>
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