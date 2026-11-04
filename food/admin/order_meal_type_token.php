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


$sql = "
SELECT 
    oi.meal_type,
    oi.order_date,
    s.scl_name AS student_name,
    o.student_id,
    f.package_name AS food_name,
    oi.price
FROM rrsv_food_orders o
JOIN rrsv_food_order_items oi ON oi.order_id = o.id
JOIN rrsv_student_registration s ON s.scl_reg_no = o.student_id
JOIN rrsv_menu_package f ON f.id = oi.food_id
WHERE 
    o.status = 'Confirmed'
    AND oi.status = 'Confirmed'
    AND oi.order_date BETWEEN '$from' AND '$to'
    GROUP BY
    s.scl_reg_no
ORDER BY
    FIELD(oi.meal_type,'Breakfast','Lunch','Dinner'),
    oi.order_date,
    s.scl_name


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
<?php
$currentMeal = '';
$mealTotal = 0;
$grandTotal = 0;

while ($row = mysqli_fetch_assoc($result)) {

    // New MEAL section
    if ($currentMeal !== $row['meal_type']) {

        // Close previous meal table & show subtotal
        if ($currentMeal !== '') {
            echo "
                <tr class='bg-warning'>
                    <th colspan='3' class='text-right'>Total {$currentMeal}</th>
                    <th class='text-right'>₹ ".number_format($mealTotal,2)."</th>
                </tr>
                </tbody></table><br>
            ";
            $mealTotal = 0; // reset for next meal
        }

        $currentMeal = $row['meal_type'];

        echo "<h3 class='bg-dark text-white p-2'>
                🍽️ {$currentMeal}
              </h3>";

        echo "<table class='table table-bordered table-striped'>
                <thead>
                    <tr class='bg-info text-white'>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Package</th>
                        <th class='text-right'>Price (₹)</th>
                    </tr>
                </thead>
                <tbody>";
    }

    // accumulate totals
    $mealTotal  += $row['price'];
    $grandTotal += $row['price'];

    echo "<tr>
            <td>".date('d-m-Y', strtotime($row['order_date']))."</td>
            <td>{$row['student_name']} ({$row['student_id']})</td>
            <td>{$row['food_name']}</td>
            <td class='text-right'>".number_format($row['price'],2)."</td>
          </tr>";
}
if ($currentMeal !== '') {
    echo "
        <tr class='bg-warning'>
            <th colspan='3' class='text-right'>Total {$currentMeal}</th>
            <th class='text-right'>₹ ".number_format($mealTotal,2)."</th>
        </tr>
        </tbody></table>
    ";
}

// GRAND TOTAL
echo "
    <div class='alert alert-success text-right mt-3'>
        <h4><b>Grand Total Amount: ₹ ".number_format($grandTotal,2)."</b></h4>
    </div>
";

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

    

