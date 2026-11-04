<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<?php
session_start();
include('../../include/header.php');
include('../../include/dbcon.php');



$msg = $error = "";

/* ================= DELETE PURCHASE ================= */
if (isset($_GET['delete'])) {
    $pid = (int) $_GET['delete'];

    // reverse stock
    $items = mysqli_query($myDB, "
        SELECT item_id, qty 
        FROM rrsv_food_stock_ledger
        WHERE ref_type='PURCHASE' AND ref_id='$pid'
    ");

    while ($r = mysqli_fetch_assoc($items)) {
        mysqli_query($myDB, "
            UPDATE rrsv_food_stock
            SET quantity = quantity - {$r['qty']}
            WHERE item_id='{$r['item_id']}'
        ");
    }

    // delete ledger + items + purchase
    mysqli_query($myDB, "DELETE FROM rrsv_food_stock_ledger WHERE ref_type='PURCHASE' AND ref_id='$pid'");
    mysqli_query($myDB, "DELETE FROM rrsv_food_purchases WHERE id='$pid'");

    $msg = "Purchase deleted & stock reversed";
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

@media print {
    .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate {
        display: none !important;
    }
}
		</style>



<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <h2 class="page-title">Purchase List</h2>
					<a href="food_purchase_add.php" class="btn btn-success">Stock In</a>
					<a href="food_expense_add.php" class="btn btn-success">Stock Out</a>
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                   
					<br><br>

            <?php if ($msg) { ?>
                <div class="alert alert-success"><?= $msg ?></div><?php } ?>
<div class="panel-body table-responsive">
            <table class="table table-bordered table-striped" id="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Invoice File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($myDB, "SELECT * FROM rrsv_food_purchases ORDER BY id DESC");
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
                                <?php if(!empty($r['invoice_file'])){ ?>
    <a href="../../uploads/purchase_invoice/<?= $r['invoice_file'] ?>"  class="btn btn-info btn-sm" target="_blank">
<i class="fa fa-eye text-primary"></i>
    </a>
<?php } else { ?>
    -
<?php } ?>
                          </td>  
                            <td>
                               
                                <a href="purchase-view.php?id=<?= $r['id'] ?>" class="btn btn-info btn-sm">View</a>
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
        </div>

    </div>
 </div>
 <link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
    <?php include('../../include/footer.php'); ?>


<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">-->
<!--<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>-->
    <script>
$(document).ready(function () {
    $('#tbl').DataTable({
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