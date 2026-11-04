<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (empty($_SESSION['alogin'])) {
	header('location:index.php');
}

	$where = "1";

if (!empty($_GET['from_date'])) {
    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $where .= " AND DATE(p.purchase_date) BETWEEN '$from' AND '$to'";
}

if (!empty($_GET['item_id'])) {
    $item_id = $_GET['item_id'];
    $where .= " AND l.item_id='$item_id'";
}

$sql = "
    SELECT
    p.invoice_no,
    p.purchase_date,
    p.supplier_name,

    i.item_name,
    l.unit,
    l.qty,
    l.rate,
    (l.qty * l.rate) AS item_amount,

    p.grand_total
FROM rrsv_food_purchases p
JOIN rrsv_food_stock_ledger l
    ON l.ref_type = 'PURCHASE'
   AND l.ref_id = p.id
JOIN rrsv_food_items i
    ON i.id = l.item_id
WHERE $where
ORDER BY p.purchase_date ASC, p.invoice_no ASC


";

$result = mysqli_query($myDB,$sql);

	


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
                   Manage Report
					
                </h4>
                <!--<a href="food_add_item.php" class="btn btn-success">Add Product</a>-->
            </div>
        </div>

        <div class="col-12 grid-margin ">
            <div class="card table-responsive">
                <div class="card-body">

				
					<br><br>

					<div class="panel panel-default">
						<div class="panel-heading">Product List</div>
						<div class="panel-body">

							<?php if ($error) { ?>
								<div class="alert alert-danger"><?= $error ?></div>
							<?php } elseif ($msg) { ?>
								<div class="alert alert-success"><?= $msg ?></div>
							<?php } ?>

						<table class="table table-bordered table-striped" id="repo">
<thead>
<tr>
    <th>Date</th>
    <th>Invoice</th>
    <th>Supplier</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Rate</th>
    <th>Amount (₹)</th>
    <th>Invoice Total (₹)</th>
</tr>
</thead>
<tbody>
<?php
$lastInvoice = '';
$grand_item_amount = 0;
$grand_invoice_total = 0;

$shownInvoices = [];
while($row = mysqli_fetch_assoc($result)){
     $grand_item_amount += $row['item_amount'];

    // Count invoice total only ONCE per invoice
    if (!in_array($row['invoice_no'], $shownInvoices)) {
        $grand_invoice_total += $row['grand_total'];
        $shownInvoices[] = $row['invoice_no'];
    }
?>
<tr>
    <td><?= date('d-m-Y', strtotime($row['purchase_date'])) ?></td>
    <td><?= $row['invoice_no'] ?></td>
    <td><?= $row['supplier_name'] ?></td>
    <td><?= $row['item_name'] ?> (<?= $row['unit'] ?>)</td>
    <td class="text-right"><?= number_format($row['qty'],2) ?></td>
    <td class="text-right"><?= number_format($row['rate'],2) ?></td>
    <td class="text-right"><?= number_format($row['item_amount'],2) ?></td>

    <!-- show invoice total only once -->
    <td class="text-right">
        <?php
        if($lastInvoice != $row['invoice_no']){
            echo number_format($row['grand_total'],2);
            $lastInvoice = $row['invoice_no'];
             
        } else {
           echo '-';
            
        }
        ?>
    </td>
</tr>
<?php } ?>
</tbody>
<tfoot>
<tr style="background:#f3f3f3;font-weight:bold;">
    <td colspan="6" class="text-right">GRAND TOTAL</td>
    <td class="text-right">₹ <?= number_format($grand_item_amount,2) ?></td>
    <td class="text-right">₹ <?= number_format($grand_invoice_total,2) ?></td>
</tr>
</tfoot>
</table>



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
