<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (empty($_SESSION['mid'])) {
	header('location:index.php');
}

$id = (int) $_GET['id'];

/* ================= PURCHASE MASTER ================= */
$purchase = mysqli_fetch_assoc(mysqli_query($myDB, "
    SELECT * FROM rrsv_food_purchases WHERE id='$id'
"));

if (!$purchase) {
    echo "Invalid Purchase ID";
    exit;
}

/* ================= PURCHASE ITEMS ================= */
$items = mysqli_query($myDB, "
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
                  Purchase Details
					
                </h4>
                <!--<a href="food_add_item.php" class="btn btn-success">Add Product</a>-->
            </div>
        </div>

        <div class="col-12 grid-margin ">
            <div class="card table-responsive">
                <div class="card-body">
            <h2></h2>

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
                <!--<div class="col-md-3">-->
                <!--    <b>Grand Total:</b><br>-->
                <!--    ₹<?= number_format($purchase['grand_total'], 2) ?>-->
                <!--</div>-->
            </div>

            <hr>

            <table class="table table-bordered table-striped" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <!--<th>Rate</th>-->
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sum = 0;
                    while ($r = mysqli_fetch_assoc($items)) {
                        $total = $r['qty'] * $r['rate'];
                        $sum += $total;
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['item_name'] ?> (<?= $r['unit'] ?>)</td>
                            <td><?= $r['qty'] ?></td>
                            <!--<td>₹<?= number_format($r['rate'], 2) ?></td>-->
                            <td>₹<?= number_format($total, 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Grand Total</th>
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
    $('#tbl').DataTable({
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

