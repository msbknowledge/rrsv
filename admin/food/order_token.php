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
LEFT JOIN rrsv_student_registration s ON s.scl_reg_no = o.student_id
LEFT JOIN rrsv_teacher t ON (o.student_id = CONCAT('T-', t.id) OR o.student_id = CAST(t.id AS CHAR))
JOIN rrsv_menu_package f ON f.id = oi.food_id
WHERE 
    o.status = 'Confirmed'
    AND oi.status = 'Confirmed'
    AND oi.order_date BETWEEN '$from' AND '$to'
    GROUP BY
    o.student_id
ORDER BY
    FIELD(oi.meal_type,'Breakfast','Lunch','Dinner','Tiffin'),
    oi.order_date,
    student_name
";

$result = mysqli_query($myDB, $sql);

?>
<style>
.token-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.token-box {
    width: 260px;
    border: 1px solid #ddd;
    border-left: 6px solid #0d6efd;
    border-radius: 6px;
    padding: 10px;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,.05);
}

.token-box .date {
    font-size: 12px;
    color: #666;
}

.token-box .student {
    font-weight: bold;
    font-size: 14px;
}

.token-box .package {
    color: #198754;
    font-size: 13px;
}

.token-box .price {
    text-align: right;
    font-weight: bold;
    margin-top: 5px;
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

    if ($currentMeal !== $row['meal_type']) {

        // close previous meal
        if ($currentMeal !== '') {
            echo "
                </div>
                <div class='alert alert-warning text-right mt-2'>
                    <b>Total {$currentMeal}: ₹ ".number_format($mealTotal,2)."</b>
                </div><br>
            ";
            $mealTotal = 0;
        }

        $currentMeal = $row['meal_type'];

        echo "<h3 class='bg-dark text-white p-2 mt-4'>
                🍽️ {$currentMeal}
              </h3>
              <div class='token-wrap'>";
    }

    $mealTotal  += $row['price'];
    $grandTotal += $row['price'];

    echo "
        <div class='token-box'>
            <div class='date'>📅 ".date('d-m-Y', strtotime($row['order_date']))."</div>
            <div class='student'>👦 {$row['student_name']} ({$row['student_id']})</div>
            <div class='package'>📦 {$row['food_name']}</div>
            <div class='price'>₹ ".number_format($row['price'],2)."</div>
        </div>
    ";
}

// last meal close
if ($currentMeal !== '') {
    echo "
        </div>
        <div class='alert alert-warning text-right mt-2'>
            <b>Total {$currentMeal}: ₹ ".number_format($mealTotal,2)."</b>
        </div>
    ";
}

// grand total
echo "
    <div class='alert alert-success text-right mt-4'>
        <h4><b>Grand Total: ₹ ".number_format($grandTotal,2)."</b></h4>
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

    

