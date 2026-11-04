<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (empty($_SESSION['alogin'])) {
	header('location:index.php');
}
$page_title = "Student Order Summary";

$from = $_GET['from'] ?? '2026-01-22';
$to   = $_GET['to']   ?? '2026-01-27';
$grandQty = 0;
$grandAmount = 0;
$sql = "
SELECT 
    s.scl_name AS student_name,
    o.student_id,
    oi.order_date,
    oi.meal_type,
    f.name AS food_name,
    oi.quantity,
    oi.price,
    (oi.quantity * oi.price) AS amount
FROM rrsv_food_orders o
JOIN rrsv_food_order_items oi ON oi.order_id = o.id
JOIN rrsv_student_registration s ON s.scl_reg_no = o.student_id
JOIN rrsv_food f ON f.id = oi.food_id
WHERE 
    o.status = 'Confirmed'
    AND oi.status = 'Confirmed'
    AND oi.order_date BETWEEN '$from' AND '$to'
ORDER BY
    s.scl_name,
    oi.order_date,
    FIELD(oi.meal_type,'Breakfast','Lunch','Dinner')

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
                <div class="card-body">
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

<?php
$currentStudent = '';
$currentDate = '';

while($row = mysqli_fetch_assoc($result)) {

    // New student header
    if ($currentStudent !== $row['student_name']) {
        if ($currentStudent !== '') echo "</tbody></table><hr>";
        $currentStudent = $row['student_name'];
        $currentDate = '';

        echo "<h4 class='text-primary'>
                🧑 Student: {$row['student_name']} ({$row['student_id']})
              </h4>";

        echo "<table class='table table-bordered'>";
        echo "<thead>
                <tr class='bg-info text-white'>
                    <th>Date</th>
                    <th>Meal</th>
                    <th>Package</th>
                    <th>Qty</th>
                </tr>
              </thead>
              <tbody>";
    }

    echo "<tr>
            <td>".date('d-m-Y', strtotime($row['order_date']))."</td>
            <td><b>{$row['meal_type']}</b></td>
            <td>{$row['food_name']}</td>
            <td class='text-center'>{$row['quantity']}</td>
          </tr>";
}
echo "</tbody></table>";
?>


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

    

