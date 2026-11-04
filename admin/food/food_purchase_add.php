<?php
session_start();
error_reporting(0);

include('../../include/dbcon.php');
	$msg = $error = "";

if (empty($_SESSION['mid'])) {
	header('location:index.php');
} else {

	if (isset($_POST['save'])) {

    $invoice_no    = $_POST['invoice_no'];
    $purchase_date =date('Y-m-d');//$_POST['purchase_date'];
    $supplier      = $_POST['supplier'];
    $unit = $_POST['unit'];
    $items = $_POST['item_id'];
    $qtys  = $_POST['qty'];
    $rates = $_POST['rate'];
    $units = $_POST['unit'];
    $totals = $_POST['total'];
//print_r($_POST);die();
    $grand_total = 0;
$invoice_file = '';

if(isset($_FILES['invoice']) && $_FILES['invoice']['name'] != ''){

    $file_name = $_FILES['invoice']['name'];
    $tmp_name  = $_FILES['invoice']['tmp_name'];
    $file_size = $_FILES['invoice']['size'];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','pdf'];

    if(!in_array($ext, $allowed)){
        $error = "❌ Only JPG, PNG, PDF allowed";
    }
    elseif($file_size > 2*1024*1024){
        $error = "❌ Max file size 2MB";
    }
    else{

        $upload_dir = '../../uploads/purchase_invoice/';

        // ✅ Create folder if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $new_name = 'PUR_' . time() . '.' . $ext;
        $upload_path = $upload_dir . $new_name;

        if(move_uploaded_file($tmp_name, $upload_path)){
            $invoice_file = $new_name;
        } else {
            $error = "❌ Upload failed";
        }
    }
}
    // 1️⃣ Insert Purchase Master
    mysqli_query($myDB, "
        INSERT INTO rrsv_food_purchases
        (invoice_no, purchase_date, supplier_name, grand_total,invoice_file)
        VALUES
        ('$invoice_no', '$purchase_date', '$supplier', 0,'$invoice_file')
    ");

    $purchase_id = mysqli_insert_id($myDB);

    // 2️⃣ Loop Items
    for ($i = 0; $i < count($items); $i++) {

        $item_id = (int)$items[$i];
        $qty     = (float)$qtys[$i];
        $rate    = (float)$rates[$i];
        $unit    = mysqli_real_escape_string($myDB, $units[$i]); // ✅ string
        $total=(float)$totals[$i];

    if ($item_id == 0 || $qty <= 0 ) continue;

       // $total = $qty * $rate;
        
        $grand_total += $total;

        /* ==========================
           CURRENT STOCK
        ========================== */
        $stock_q = mysqli_query($myDB,"
            SELECT quantity 
            FROM rrsv_food_stock 
            WHERE item_id='$item_id'
        ");

        if (mysqli_num_rows($stock_q) > 0) {
            $row = mysqli_fetch_assoc($stock_q);
            $old_qty = $row['quantity'];
            $old_rate = $row['avg_price'];
            $new_qty = $old_qty + $qty;
            //$new_rate = ($row['avg_price'] + $rate);//$new_qty;
$new_rate = (($old_qty * $old_rate) + ($qty * $rate)) / ($old_qty + $qty);
            mysqli_query($myDB,"
                UPDATE rrsv_food_stock
                SET quantity='$new_qty',
                    avg_price='$new_rate'
                WHERE item_id='$item_id'
            ");
        } else {
            $old_qty = 0;
            $new_qty = $qty;

            mysqli_query($myDB,"
                INSERT INTO rrsv_food_stock
                (item_id,unit, quantity, avg_price)
                VALUES
                ('$item_id','$unit','$qty','$rate')
            ");
        }

        /* ==========================
           STOCK LEDGER (IN)
        ========================== */
        mysqli_query($myDB,"
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
    mysqli_query($myDB,"
        UPDATE rrsv_food_purchases
        SET grand_total='$grand_total'
        WHERE id='$purchase_id'
    ");

    $msg = "✅ Purchase saved & stock updated successfully";

	  header("Location: ".$_SERVER['PHP_SELF']."?success=1");
    exit;   
	}
if(isset($_GET['success'])){
    $msg = "✅ Purchase saved & stock updated successfully";
}
// 🔹 AUTO INVOICE NUMBER
$year = date('Y');
$prefix = "PUR-$year-";

// get last invoice
$q = mysqli_query($myDB,"
    SELECT invoice_no 
    FROM rrsv_food_purchases 
    WHERE invoice_no LIKE '$prefix%' 
    ORDER BY id DESC 
    LIMIT 1
");

if (mysqli_num_rows($q) > 0) {
    $row = mysqli_fetch_assoc($q);
    $lastNo = (int) substr($row['invoice_no'], -4);
    $newNo  = $lastNo + 1;
} else {
    $newNo = 1;
}

$invoice_no = $prefix . str_pad($newNo, 4, '0', STR_PAD_LEFT);

include('../../include/header.php');

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
			.mb-2 {
				margin-bottom:2px;
			}
		    .info{background:#d1ecf1;
			font-weight:bold;
			}
			.ml-5{
			    margin-left:5px;
			}
			.ml-5{
			    margin-left:5px;
			}
			.pt-5{
			    padding: 5px;
			}
			.pl-5{
			    padding-left: 5px;
			}
		</style>
		<!-- jQuery Library -->

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   Purchase Entry (Stock IN)
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">	
			

						<?php if ($error) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } elseif ($msg) { ?>
							<div class="succWrap"><?= $msg ?></div>
						<?php } ?>

						<div class="panel-body">
	<h3></h3>

<?php if($msg){ ?>
<!--<div class="alert alert-success"><?= $msg ?></div>-->
<?php } ?>

<form method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-3">
            <label class="mb-2">Invoice No</label>
            <input type="text" name="invoice_no" value="<?= $invoice_no?>" class="form-control" readonly>
        </div>
        <!--<div class="col-md-3">-->
        <!--    <label class="mb-2">Date</label>-->
        <!--    <input type="date" name="purchase_date" class="form-control" required>-->
        <!--</div>-->
        <div class="col-md-4">
            <label class="mb-2">Supplier</label>
            <input type="text" name="supplier" class="form-control">
        </div>
        <div class="col-md-4">
    <label class="mb-2">Upload Invoice</label>
    <input type="file" name="invoice" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
</div>
    </div>

    <hr>
<div class="panel panel-default mb-2">
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
            <th>Total</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <select name="item_id[]" class="form-control itemSelect select2" required>
                    <option value="">Select Item</option>
                    <?php
                    $items = mysqli_query($myDB,"SELECT * FROM rrsv_food_items WHERE status='Active'");
                    while($i=mysqli_fetch_assoc($items)){
                        echo "<option value='{$i['id']}'>{$i['item_name']}</option>";
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
        $units = mysqli_query($myDB,"SELECT * FROM rrsv_food_units");
        while($u=mysqli_fetch_assoc($units)){
            echo "<option value='{$u['short_name']}'>{$u['short_name']}</option>";
        }
        ?>
    </select>
</td>

<td>
    <input type="number" step="0.01" name="total[]" class="form-control total" required>
    <input type="hidden" name="rate[]" class="rate">
</td>



            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>
    </tbody>
</table>
</div></div>

<div class="panel panel-default mb-2">
    <div class="panel-body">
        <div class="row">
            <!-- Add Row Button (Left) -->
            <div class="col-md-6 text-left">
                <button type="button" class="btn btn-success btn-sm" id="addRow" style="margin-left:10px;">
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

    <button type="submit" name="save" class="btn btn-success" style="margin-left:10px;">
        Save Purchase
    </button>
</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
    <?php include('../../include/footer.php'); ?>

    <!-- Datatable JS -->
   
    <script>
$(document).ready(function () {
    $('#itemsTable1').DataTable({
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
	<script>
$(document).ready(function(){

    function calculateTotal(){
        let grandTotal = 0;

        $('#itemsTable tbody tr').each(function(){

            let qty   = parseFloat($(this).find('.qty').val()) || 0;
            let total = parseFloat($(this).find('.total').val()) || 0;

            let rate = 0;
            if(qty > 0){
                rate = total / qty;
            }
            $(this).find('.rate').val(rate.toFixed(4));
           // $(this).find('.rate').val(rate.toFixed(2));
            grandTotal += total;
        });

        $('#grandTotal').text(grandTotal.toFixed(2));
    }

    $(document).on('keyup change','.qty, .total', function(){
        calculateTotal();
    });

    $('#addRow').click(function(){
        let row = $('#itemsTable tbody tr:first').clone();
        row.find('input').val('');
        row.find('select').val('');
        $('#itemsTable tbody').append(row);
    });

    $(document).on('click','.removeRow', function(){
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
<script>
function initItemSelect2() {
    $('.itemSelect').select2({
        placeholder: 'Select Item',
        width: '100%'
    });
}

$(document).ready(function () {
    initItemSelect2();

    $('#addRow').on('click', function () {
        let row = $('#itemsTable tbody tr:first').clone();

        row.find('input').val('');
        row.find('select').val('');

        $('#itemsTable tbody').append(row);

        initItemSelect2(); // re-init for new row
    });
});
</script>

	</body>

	</html>
<?php } ?>