<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {
	$msg = $error = "";
	if (isset($_POST['save'])) {

    $invoice_no    = $_POST['invoice_no'];
    $purchase_date = $_POST['purchase_date'];
    $supplier      = $_POST['supplier'];
    $unit = $_POST['unit'];
    $items = $_POST['item_id'];
    $qtys  = $_POST['qty'];
    $rates = $_POST['rate'];
    $units = $_POST['unit'];

    $grand_total = 0;

    // 1️⃣ Insert Purchase Master
    mysqli_query($con, "
        INSERT INTO rrsv_food_purchases
        (invoice_no, purchase_date, supplier_name, grand_total)
        VALUES
        ('$invoice_no', '$purchase_date', '$supplier', 0)
    ");

    $purchase_id = mysqli_insert_id($con);

    // 2️⃣ Loop Items
    for ($i = 0; $i < count($items); $i++) {

        $item_id = (int)$items[$i];
        $qty     = (float)$qtys[$i];
        $rate    = (float)$rates[$i];
        $unit    = mysqli_real_escape_string($con, $units[$i]); // ✅ string

    if ($item_id == 0 || $qty <= 0 ) continue;

        //$total = $qty * $rate;
        $total =  $rate;
        $grand_total += $total;

        /* ==========================
           CURRENT STOCK
        ========================== */
        $stock_q = mysqli_query($con,"
            SELECT quantity 
            FROM rrsv_food_stock 
            WHERE item_id='$item_id'
        ");

        if (mysqli_num_rows($stock_q) > 0) {
            $row = mysqli_fetch_assoc($stock_q);
            $old_qty = $row['quantity'];
            $old_rate = $row['avg_price'];
            $new_qty = $old_qty + $qty;
            $new_rate = $old_rate + $rate;

            mysqli_query($con,"
                UPDATE rrsv_food_stock
                SET quantity='$new_qty',
                    avg_price='$new_rate'
                WHERE item_id='$item_id'
            ");
        } else {
            $old_qty = 0;
            $new_qty = $qty;

            mysqli_query($con,"
                INSERT INTO rrsv_food_stock
                (item_id,unit, quantity, avg_price)
                VALUES
                ('$item_id','$unit','$qty','$rate')
            ");
        }

        /* ==========================
           STOCK LEDGER (IN)
        ========================== */
        mysqli_query($con,"
            INSERT INTO rrsv_food_stock_ledger
            (item_id, ref_type, ref_id,unit, qty, rate, direction, balance_qty, remarks)
            VALUES
            (
                '$item_id',
                'PURCHASE',
                '$purchase_id',
                '$unit',
                '$qty',
                '$rate',
                'IN',
                '$new_qty',
                'Purchase Invoice #$invoice_no'
            )
        ");
    }

    // 3️⃣ Update Purchase Total
    mysqli_query($con,"
        UPDATE rrsv_food_purchases
        SET grand_total='$grand_total'
        WHERE id='$purchase_id'
    ");

    $msg = "✅ Purchase saved & stock updated successfully";
}



	?>

	<!doctype html>
	<html lang="en" class="no-js">

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="theme-color" content="#3e454c">

		<title>Purchase Items</title>

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

					<h2 class="page-title">Manage Items</h2>
					<!-- <a href="add-item.php" class="btn btn-success">Add Items</a> -->
					<br><br>

					<div class="panel panel-default">
						<div class="panel-heading">
							
						</div>

						<?php if ($error) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } elseif ($msg) { ?>
							<div class="succWrap"><?= $msg ?></div>
						<?php } ?>

						<div class="panel-body">
	<h3>Purchase Entry (Stock IN)</h3>

<?php if($msg){ ?><div class="alert alert-success"><?= $msg ?></div><?php } ?>

<form method="post">
    <div class="row">
        <div class="col-md-3">
            <label>Invoice No</label>
            <input type="text" name="invoice_no" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Date</label>
            <input type="date" name="purchase_date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Supplier</label>
            <input type="text" name="supplier" class="form-control">
        </div>
    </div>

    <hr>
<div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <b></b>
                    </div>

<div class="panel-body table-responsive">
   <table class="table table-bordered table-striped" id="itemsTable">
    <thead>
        <tr class="info">
            <th>Item</th>
            <th>Qty</th>
            <th>Unit </th>
            <th>Rate</th>
            <th>Total</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <select name="item_id[]" class="form-control" required>
                    <option value="">Select Item</option>
                    <?php
                    $items = mysqli_query($con,"SELECT * FROM rrsv_food_items WHERE status='Active'");
                    while($i=mysqli_fetch_assoc($items)){
                        echo "<option value='{$i['id']}'>{$i['item_name']} ({$i['unit']})</option>";
                    }
                    ?>
                </select>
            </td>
            
            <td>
                <input type="number" step="0.01" name="qty[]" class="form-control qty" required>
            </td>
            <td>
               	<select name="unit[]" class="form-control" required>
				<option value="">Select Unit</option>
                    <?php
                    $units = mysqli_query($con,"SELECT * FROM rrsv_food_units");
                    while($i=mysqli_fetch_assoc($units)){
                        echo "<option value='{$i['short_name']}'>{$i['short_name']}</option>";
                    }
                    ?>
				 </select>
 
            </td>


            <td>
                <input type="number" step="0.01" name="rate[]" class="form-control rate" required>
            </td>

            <td>
                <input type="text" class="form-control total" readonly>
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>
    </tbody>
</table>
</div></div>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <!-- Add Row Button (Left) -->
            <div class="col-md-6 text-left">
                <button type="button" class="btn btn-success btn-sm" id="addRow">
                    ➕ Add Item
                </button>
            </div>

            <!-- Grand Total (Right) -->
            <div class="col-md-6 text-right">
                <h4 style="margin:0;">
                    <b>Grand Total:</b> ₹ <span id="grandTotal">0.00</span>
                </h4>
            </div>
        </div>
    </div>
</div>
    <br><br>

    <button type="submit" name="save" class="btn btn-success">
        Save Purchase
    </button>
</form>

						</div>
					</div>



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
		<script>
$(document).ready(function(){

    // calculate row + grand total
    function calculateTotal(){
        let grandTotal = 0;

        $('#itemsTable tbody tr').each(function(){
            let qty  = parseFloat($(this).find('.qty').val()) || 0;
            let rate = parseFloat($(this).find('.rate').val()) || 0;
            //let total = qty * rate;
             let total =  rate;

            $(this).find('.total').val(total.toFixed(2));
            grandTotal += total;
        });

        $('#grandTotal').text(grandTotal.toFixed(2));
    }

    // on qty or rate change
    $(document).on('keyup change','.qty, .rate',function(){
        calculateTotal();
    });

    // add new row
    $('#addRow').click(function(){
        let row = $('#itemsTable tbody tr:first').clone();
        row.find('input').val('');
        row.find('select').val('');
        $('#itemsTable tbody').append(row);
    });

    // remove row
    $(document).on('click','.removeRow',function(){
        if($('#itemsTable tbody tr').length > 1){
            $(this).closest('tr').remove();
            calculateTotal();
        }
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

	</body>

	</html>
<?php } ?>