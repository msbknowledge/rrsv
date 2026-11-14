<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (empty($_SESSION['mid'])) {
	header('location:index.php');
}
$page_title = "Student Order Summary";

$from = !empty($_GET['from'])? $_GET['from']: date('Y-m-d');

$to = !empty($_GET['to'])? $_GET['to']: date('Y-m-d');


$sql = "
SELECT 
    oi.meal_type,
    oi.order_date,
    COALESCE(s.scl_name, CONCAT(t.full_name, ' [Teacher]'), o.student_id) AS student_name,
    o.student_id,
    f.package_name AS food_name,
    oi.price
FROM rrsv_food_orders o
JOIN rrsv_food_order_items oi ON oi.order_id = o.id
LEFT JOIN rrsv_student_registration s ON s.id = (SELECT MAX(id) FROM rrsv_student_registration WHERE scl_reg_no = o.student_id)
LEFT JOIN rrsv_teacher t ON (o.student_id = CONCAT('T-', t.id) OR o.student_id = CAST(t.id AS CHAR))
JOIN rrsv_menu_package f ON f.id = oi.food_id
WHERE 
    oi.status = 'Confirmed'
    AND oi.order_date BETWEEN '$from' AND '$to'
ORDER BY
    FIELD(oi.meal_type,'Breakfast','Lunch','Dinner','Tiffin'),
    oi.order_date,
    student_name
";


$result = mysqli_query($myDB, $sql);

?>
<style>
@media print {

    @page {
        size: A4;
        margin: 8mm;
    }

    body{
        background:#fff;
        margin:0;
        padding:0;
    }
form,
    .btn,
    .mb-3 {
        display: none !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
    /* Hide unwanted layout */
    .sidebar,
    .navbar,
    footer,
    header,
    nav,
    .settings-panel {
        display:none !important;
    }
 /* Hide amount column in print */
    .amount-col,
    .amount-row{
        display:none !important;
    }
    /* REMOVE visibility:hidden (IMPORTANT) */

    .print-area{
        width: 100%;
        margin: 0;
        padding: 0;
        font-size: 12px;
    }

    table{
        width:100%;
        border-collapse:collapse;
        page-break-inside:auto;
    }

    tr{
        page-break-inside:avoid;
    }

    h3{
        page-break-before: auto; /* or always if you want new page */
    }

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
        <input type="date" name="from" value="<?=  $from?>" class="form-control">
    </div>
    <div class="col-md-3">
        <input type="date" name="to" value="<?= $to ?>" class="form-control">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>
<div class="mb-3 text-right">
    <button onclick="window.print()" class="btn btn-success">
        🖨️ Print
    </button>
</div>
<div class="print-area">
<?php
$currentMeal = '';
$mealTotal = 0;
$grandTotal = 0;
// print_r($result );
// die();
while ($row = mysqli_fetch_assoc($result)) {

    // New MEAL section
    if ($currentMeal !== $row['meal_type']) {

        // Close previous meal table & show subtotal
        if ($currentMeal !== '') {
            echo "
                <tr class='bg-warning bg-opacity-25 amount-row'>
    <th colspan='4' class='text-right amount-col'>Total {$currentMeal}</th>
    <th class='text-right amount-col'>₹ ".number_format($mealTotal,2)."</th>
</tr>
                </tbody></table><br>
            ";
            $mealTotal = 0; // reset for next meal
        }

        $currentMeal = $row['meal_type'];
$sl = 1; // reset here
        echo "<h3 class='bg-success bg-opacity-50 text-white p-2'>
                🍽️ {$currentMeal}
              </h3>";

        echo "<table class='table table-bordered table-striped'>
                <thead>
                    <tr class='bg-info text-white'>
                    <th>SL</th>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Package</th>
                        <th class='text-right amount-col'>Price (₹)</th>
                    </tr>
                </thead>
                <tbody>";
    }

    // accumulate totals
    $mealTotal  += $row['price'];
    $grandTotal += $row['price'];

    echo "<tr>
           <td>".$sl++."</td>
            <td>".date('d-m-Y', strtotime($row['order_date']))."</td>
            <td>{$row['student_name']} ({$row['student_id']})</td>
            <td>{$row['food_name']}</td>
            <td class='text-right amount-col'>".number_format($row['price'],2)."</td>
          </tr>";
}
if ($currentMeal !== '') {
    echo "
        <tr class='bg-warning bg-opacity-25 amount-row'>
            <th colspan='4' class='text-right amount-col'>Total {$currentMeal}</th>
            <th class='text-right amount-col'>₹ ".number_format($mealTotal,2)."</th>
        </tr>
        </tbody></table>
    ";
}

// GRAND TOTAL
echo "
    <div class='alert alert-success text-right mt-3 amount-row'>
        <h4><b>Grand Total Amount: ₹ ".number_format($grandTotal,2)."</b></h4>
    </div>
";

?>
  </div>
				 </div>
            </div>
        </div>

    </div>

    <?php include('../../include/footer.php'); ?>

   <script>
$('#from, #to').datepicker({
    dateFormat: 'dd-mm-yy'
});
</script>
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

    

