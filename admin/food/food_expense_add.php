<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');
$issue_no = "EXP-0001";

$q = mysqli_query($myDB, "SELECT id FROM rrsv_food_expense ORDER BY id DESC LIMIT 1");
if ($row = mysqli_fetch_assoc($q)) {
    $next_id = $row['id'] + 1;
    $issue_no = 'EXP-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
}

if (empty($_SESSION['mid'])) {
	header('location:index.php');
}
 else {
	if (isset($_POST['save'])) {

    $issue_no    = $_POST['issue_no'];
    $issue_date = date('Y-m-d');//$_POST['issue_date'];
    $issued_to   = 0;//$_POST['issued_to'];

    $items = $_POST['item_id'];
    $qtys  = $_POST['qty'];
    $rates = $_POST['rate'];
    $units = $_POST['unit'];

    $grand_total = 0;

    /* 1️⃣ Insert Issue Master */
    mysqli_query($myDB,"
        INSERT INTO rrsv_food_expense
        (issue_no, issue_date, issued_to, grand_total)
        VALUES
        ('$issue_no','$issue_date','$issued_to',0)
    ");

    $issue_id = mysqli_insert_id($myDB);

    /* 2️⃣ Loop Items */
    for ($i = 0; $i < count($items); $i++) {

        $item_id = (int)$items[$i];
        $qty     = (float)$qtys[$i];
        $rate    = (float)$rates[$i];
        $unit    = mysqli_real_escape_string($myDB, $units[$i]);

        if ($item_id == 0 || $qty <= 0) continue;

        $total = $qty * $rate;
        $grand_total += $total;

        /* ==========================
           CHECK CURRENT STOCK
        ========================== */
        $stock_q = mysqli_query($myDB,"
            SELECT quantity, avg_price
            FROM rrsv_food_stock
            WHERE item_id='$item_id'
        ");

        if (mysqli_num_rows($stock_q) == 0) {
            $error = "Stock not available for item ID $item_id";
            break;
        }

        $row = mysqli_fetch_assoc($stock_q);
        $old_qty = (float)$row['quantity'];

        if ($old_qty < $qty) {
            $error = "Insufficient stock for item ID $item_id";
            break;
        }

        $new_qty = $old_qty - $qty;

        /* ==========================
           UPDATE STOCK
        ========================== */
        mysqli_query($myDB,"
            UPDATE rrsv_food_stock
            SET quantity='$new_qty'
            WHERE item_id='$item_id'
        ");

        /* ==========================
           STOCK LEDGER (OUT)
        ========================== */
        mysqli_query($myDB,"
            INSERT INTO rrsv_food_stock_ledger
            (item_id, ref_type, ref_id, unit, qty, rate, direction, balance_qty, remarks)
            VALUES
            (
                '$item_id',
                'EXPENSE',
                '$issue_id',
                '$unit',
                '$qty',
                '$rate',
                'OUT',
                '$new_qty',
                'EXPENSE No #$issue_no'
            )
        ");
    }

    /* 3️⃣ Update Issue Total */
    if (!$error) {
        mysqli_query($myDB,"
            UPDATE rrsv_food_expense
            SET grand_total='$grand_total'
            WHERE id='$issue_id'
        ");
        $msg = "✅ Stock issued successfully";
    }
    
    
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

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                  Expense Entry (Stock OUT)
                </h4>
                <!--<a href="food_add_item.php" class="btn btn-success">Add Product</a>-->
            </div>
        </div>
        <div class="card">
        	<div class="card-body">
						

						<?php if ($error) { ?>
							<div class="errorWrap"><?= $error ?></div>
						<?php } elseif ($msg) { ?>
							<div class="succWrap"><?= $msg ?></div>
						<?php } ?>

						<div class="panel-body">
                        	<h3></h3>
                        
                        <?php if($msg){ ?><div class="alert alert-success"><?= $msg ?></div><?php } ?>
                        
                        <form method="post">
                        
                        <div class="row">
                            <div class="col-md-3">
                                <label class="mb-2">Expense No</label>
                               <input type="text"
       name="issue_no"
       class="form-control"
       value="<?= htmlspecialchars($issue_no) ?>"
       readonly>
                            </div>
                        
                            <!--<div class="col-md-3">-->
                            <!--    <label class="mb-2">Expense Date</label>-->
                            <!--    <input type="date" name="issue_date" class="form-control" required>-->
                            <!--</div>-->
                        
                            <!--<div class="col-md-4">-->
                            <!--    <label>Expense To</label>-->
                            <!--    <input type="text" name="issued_to" class="form-control" required>-->
                            <!--</div>-->
                        </div>
                        
                        <hr>
                        
                        <div class="panel panel-default mb-2">
                           
                        
                            <div class="panel-body table-responsive">
                                <table class="table table-bordered table-striped" id="itemsTable">
                                    <thead>
                                        <tr class="info">
                                        <th>Item</th>
                                        <th>Available</th>
                                        <th>Qty</th>
                                        <!--<th>Unit</th>-->
                                        <!--<th>Total</th>-->
                                        <th>#</th>
                                    </tr>
                        
                                    </thead>
                                    <tbody>
                                        <tr>
                            <td>
                                <select name="item_id[]" class="form-control itemSelect" required>
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
                                <span class="stock badge badge-info">0</span>
                                <input type="hidden" class="stockVal">
                            </td>
                        
                            <td>
                                <input type="number" step="0.01" name="qty[]" class="form-control qty" required>
                            </td>
                        
                          
                                 <td style="display:none">
                                <select name="unit[]" class="form-control unit">
                                    <option value="">Select Unit</option>
                                    <?php
                                    $units = mysqli_query($myDB,"SELECT * FROM rrsv_food_units");
                                    while($u=mysqli_fetch_assoc($units)){
                                        echo "<option value='{$u['short_name']}'>{$u['short_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                                 <input type="hidden" step="0.01" name="total[]" class="form-control total" readonly>
                                <input type="hidden" name="rate[]" class="rate">
                           
                        
                            <!--<td>-->
                            <!--    <input type="number" step="0.01" name="total[]" class="form-control total" readonly>-->
                            <!--    <input type="hidden" name="rate[]" class="rate">-->
                            <!--</td>-->
                        
                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                            </td>
                        </tr>
                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="panel panel-default mb-2">
                            <div class="panel-body">
                                <div class="row">
                        
                                    <div class="col-md-6 text-left" style="margin-left:10px;">
                                        <button type="button" class="btn btn-success btn-sm" id="addRow">
                                            ➕ Add Item
                                        </button>
                                    </div>
                        
                                    <div class="col-md-6 text-right" style="display:none">
                                        <h4>
                                            <b>Grand Total:</b> ₹ <span id="grandTotal">0.00</span>
                                        </h4>
                                    </div>
                        
                                </div>
                            </div>
                        </div>
                        
                        <br>
                        
                        <button type="submit" name="save" class="btn btn-danger" style="margin-left:10px;">
                            Expense Stock
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
    $('#repo').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { targets: [4,5,6], className: 'text-right' }
        ]
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
$(document).ready(function(){

    function calculateGrandTotal(){
        let grandTotal = 0;

        $('#itemsTable tbody tr').each(function(){
            let total = parseFloat($(this).find('.total').val()) || 0;
            grandTotal += total;
        });

        $('#grandTotal').text(grandTotal.toFixed(2));
    }

    // 🔹 Load stock & rate when item selected
    $(document).on('change','.itemSelect', function(){
        let row = $(this).closest('tr');
        let itemId = $(this).val();

        if(!itemId) return;

        // Get stock
        $.getJSON('ajax_get_stock.php', {item_id: itemId}, function(res){
            row.find('.stock').text(res.stock);
            row.find('.stockVal').val(res.stock);
        });

        // Get rate
        $.getJSON('ajax_get_item_rate.php', {item_id: itemId}, function(res){
            row.find('.rate').val(res.rate);
            row.find('.unit').val(res.unit);
        });
    });

    // ✨ Auto calculate total on qty input
    $(document).on('keyup change','.qty', function(){
        let row   = $(this).closest('tr');
        let qty   = parseFloat($(this).val()) || 0;
        let rate  = parseFloat(row.find('.rate').val()) || 0;
        let stock = parseFloat(row.find('.stockVal').val()) || 0;

        // Stock validation
        if(qty > stock){
            alert('❌ Quantity exceeds available stock');
            $(this).val(stock);
            qty = stock;
        }

        let total = qty * rate;
        row.find('.total').val(total.toFixed(2));

        calculateGrandTotal();
    });


 function updateItemDropdowns() {

        let selectedItems = [];

        // Collect all selected item IDs
        $('.itemSelect').each(function(){
            let val = $(this).val();
            if(val){
                selectedItems.push(val);
            }
        });

        // Disable selected items in all dropdowns
        $('.itemSelect').each(function(){

            let currentVal = $(this).val();

            $(this).find('option').each(function(){

                let optionVal = $(this).attr('value');

                if(!optionVal) return;

                // Disable if selected elsewhere
                if(
                    selectedItems.includes(optionVal) &&
                    optionVal !== currentVal
                ){
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });
    }

    // Run when item changes
    $(document).on('change', '.itemSelect', function(){
        updateItemDropdowns();
    });

    $('#addRow').click(function(){
        let row = $('#itemsTable tbody tr:first').clone();
        row.find('input').val('');
        row.find('select').val('');
        row.find('.stock').text('0');
        $('#itemsTable tbody').append(row);
        updateItemDropdowns();
    });

    $(document).on('click','.removeRow', function(){
        if($('#itemsTable tbody tr').length > 1){
            $(this).closest('tr').remove();
            calculateGrandTotal();
            updateItemDropdowns();
        }
    });

});
</script>

<?php } ?>