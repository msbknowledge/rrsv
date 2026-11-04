<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
}
$page_title = "Student Order Summary";

$from = $_GET['from'] ?? '2026-01-22';
$to   = $_GET['to']   ?? '2026-01-27';
$grandQty = 0;
$grandAmount = 0;
$sql = "
SELECT oi.order_date, s.scl_name AS student_name, f.package_name AS food_name, oi.meal_type, oi.quantity, oi.price, (oi.quantity * oi.price) AS amount FROM rrsv_food_orders o JOIN rrsv_food_order_items oi ON oi.order_id = o.id JOIN rrsv_student_registration s ON s.scl_reg_no = o.student_id JOIN rrsv_menu_package f ON f.id = oi.food_id WHERE o.status = 'Confirmed' AND oi.status = 'Confirmed' AND oi.order_date BETWEEN '$from' AND '$to' ORDER BY oi.order_date, s.scl_name, oi.meal_type
";

$result = mysqli_query($myDB, $sql);

?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   Student-wise Order Summary
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin ">
            <div class="card table-responsive">
                <div>
				<h2 class="page-title"></h2>
				<form method="get" class="row mb-3">
    <div class="col-md-3">
        <input type="date" name="from" value="<?= $from ?>" class="form-control">
    </div>
    <div class="col-md-3">
        <input type="date" name="to" value="<?= $to ?>" class="form-control">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>

			<table id="studentTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Student Name</th>
            <th>Food Item</th>
            <th>Meal Type</th>
            <th>Qty</th>
            <th>Rate (₹)</th>
            <th>Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { 
         $grandQty += $row['quantity'];
            $grandAmount += $row['amount'];
        ?>
        <tr>
            <td><?= date('d-m-Y', strtotime($row['order_date'])) ?></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['food_name']) ?></td>
            <td><?= $row['meal_type'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= number_format($row['amount'], 2) ?></td>
        </tr>
        <?php } ?>
    </tbody>
     <tfoot>
        <tr class="bg-info text-white">
            <th colspan="4" class="text-right">GRAND TOTAL</th>
            <th class="text-right"><?= number_format($grandQty, 2) ?></th>
            <th></th>
            <th class="text-right">₹ <?= number_format($grandAmount, 2) ?></th>
        </tr>
    </tfoot>
</table>

				 </div>
            </div>
        </div>

    </div>

    <?php include('../../include/footer.php'); ?>

    <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
    <script>
    $(document).ready(function () {
    $('#studentTable').DataTable({
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

    

