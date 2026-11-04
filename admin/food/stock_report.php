<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (empty($_SESSION['mid'])) {
	header('location:index.php');
}
$grand_in_rs = 0;
$grand_out_rs = 0;
$grand_available_rs = 0;
	$where = "1";

if (!empty($_GET['from_date'])) {
    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $where .= " AND DATE(l.created_at) BETWEEN '$from' AND '$to'";
}

if (!empty($_GET['item_id'])) {
    $item_id = $_GET['item_id'];
    $where .= " AND l.item_id='$item_id'";
}

$sql = "
SELECT
    i.id,
    i.item_name,
    l.unit,

    SUM(CASE WHEN l.direction='IN' THEN l.qty ELSE 0 END) AS total_in,
    SUM(CASE WHEN l.direction='OUT' THEN l.qty ELSE 0 END) AS total_out,

    (
        SUM(CASE WHEN l.direction='IN' THEN l.qty ELSE 0 END)
        -
        SUM(CASE WHEN l.direction='OUT' THEN l.qty ELSE 0 END)
    ) AS available_qty,
   SUM(CASE WHEN l.direction='IN' THEN l.qty * l.rate END) AS total_in_value,
   SUM(CASE WHEN l.direction='OUT' THEN l.qty * l.rate END) AS total_out_value,

    MAX(CASE WHEN l.direction='IN' THEN l.rate ELSE 0 END) AS rate

FROM rrsv_food_stock_ledger l
JOIN rrsv_food_items i ON i.id = l.item_id
WHERE $where
GROUP BY i.id
ORDER BY i.item_name ASC";

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
		@media print {

    /* Hide everything */
    /* Hide unwanted layout */
    .sidebar,
    .navbar,
    footer,
    header,
    nav,
    .settings-panel {
        display:none !important;
    }
   .btn,
     .dataTables_length,
    .dataTables_filter,
    .dataTables_paginate,
    .dataTables_info,
    .panel-heading {
        display: none !important;
    }
   
.table-responsive {
        overflow: visible !important;
    }
   
}
		</style>


<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   Stock Report
					
                </h4>
               <button onclick="printTableOnly()" class="btn btn-primary">
    🖨 Print Table
</button>
                <!--<a href="food_add_item.php" class="btn btn-success">Add Product</a>-->
            </div>
        </div>

        <div class="col-12 grid-margin ">
            <div class="card">
                <div class="card-body">

				
					<br><br>

					<div class="panel panel-default">
						<div class="panel-heading">Product List</div>
						<div class="panel-body table-responsive">

							<?php if ($error) { ?>
								<div class="alert alert-danger"><?= $error ?></div>
							<?php } elseif ($msg) { ?>
								<div class="alert alert-success"><?= $msg ?></div>
							<?php } ?>

							<table class="table table-bordered table-striped" id="repo">
                                <thead>
                                    <tr>
                                <th>Item</th>
                                <th>Total IN</th>
                                <th>Total OUT</th>
                                <th>Available Qty</th>
                                <!--<th>Rate</th>-->
                                <!--<th>Total In (Rs)</th>-->
                                <!--<th>Total Out (Rs)</th>-->
                                <!-- <th>Total Available (Rs)</th>-->
                            </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)) { 
                                
                                $total_out =$row['total_in_value'];// $row['total_out'] * $row['rate'];
                                $total_in =$row['total_in_value']; //$row['total_in'] * $row['rate'];
                                $total_available =($total_in-$total_out);//$row['available_qty'] * $row['rate'];
                                 // 🔥 Grand totals
                                $grand_in_rs += $total_in;
                                $grand_out_rs += $total_out;
                                $grand_available_rs += $total_available;
                            ?>
                            <tr>
                                <td><?= $row['item_name'] ?> </td>
                                <td class="text-success"><?= number_format($row['total_in'],2) ?>(<?= $row['unit'] ?>)</td>
                                <td class="text-danger"><?= number_format($row['total_out'],2) ?>(<?= $row['unit'] ?>)</td>
                                <td><b><?= number_format($row['available_qty'],2) ?>(<?= $row['unit'] ?>)</b></td>
                                <!--<td>₹ <?= number_format($row['rate'],2) ?></td>-->
                                <!--<td><b>₹ <?= number_format($total_in,2) ?></b></td>-->
                                <!--<td><b>₹ <?= number_format($total_out,2) ?></b></td>-->
                                <!--<td><b>₹ <?= number_format($total_available,2) ?></b></td>-->
                            </tr>
                            <?php } ?>
                                </tbody>
                                <tfoot>
                            <tr style="background:#f3f3f3;font-weight:bold;">
                                <td class="text-right">TOTAL</td>
                                <td colspan="3"></td>
                                <!--<td>₹ <?= number_format($grand_in_rs,2) ?></td>-->
                                <!--<td>₹ <?= number_format($grand_out_rs,2) ?></td>-->
                                <!--<td>₹ <?= number_format($grand_available_rs,2) ?></td>-->
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
	<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
    <?php include('../../include/footer.php'); ?>
    
 <script>
$(document).ready(function () {
    $('#repo').DataTable({
        paging: true,       // pagination
        searching: true,    // search box
        ordering: true,     // column sorting
        info: true,         // "Showing X of Y"
        lengthChange: true, // page length dropdown
        pageLength: 10,     // default rows per page
        order: [[0, 'desc']] ,// sort by ID desc
        // columnDefs: [
        //     { targets: [4,5,6], className: 'text-right' }
        // ]
    });
});
</script>
   

		<script type="text/javascript">
			$(document).ready(function () {
				setTimeout(function () {
					$('.succWrap').slideUp("slow");
				}, 3000);
			});
		function printTableOnly() {

    let table = $('#repo').DataTable();

    table.page.len(-1).draw(); // show all rows

    setTimeout(function(){
        window.print();
        table.page.len(10).draw(); // restore
    }, 500);
}
		</script>
