<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
}

$page_title = "Student Order Summary";

$from = !empty($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$to   = !empty($_GET['to']) ? $_GET['to'] : date('Y-m-d');

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
    oi.status='Confirmed'
    AND oi.order_date BETWEEN '$from' AND '$to'
ORDER BY
FIELD(oi.meal_type,'Breakfast','Lunch','Dinner','Tiffin'),
oi.order_date,
student_name
";

$result = mysqli_query($myDB,$sql);
?>

<!-- ================= PRINT CSS ================= -->
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

    /* Hide layout */
    .sidebar,
    .navbar,
    footer,
    header,
    nav,
    .settings-panel,
    .main-panel > *:not(.content-wrapper){
        display:none !important;
    }

    body *{
        visibility:hidden;
    }

    .print-area,
    .print-area *{
        visibility:visible;
    }

    .print-area{
        position:absolute;
        left:0;
        top:0;
        width:100%;
    }

    table{
        page-break-inside:auto;
    }

    tr{
        page-break-inside:avoid;
    }

}
</style>

<link href="<?php echo BASE_URL;?>libray/DataTables/datatables.min.css" rel="stylesheet">

<script src="<?php echo BASE_URL;?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
<div class="content-wrapper">

<!-- ================= FILTER ================= -->
<div class="card">
<div class="card-body">

<h4 class="card-title">Student-wise Order Summary</h4>

<form method="get" class="row mb-3">

<div class="col-md-3">
<input type="date" id="from" name="from"
value="<?= $from ?>" class="form-control">
</div>

<div class="col-md-3">
<input type="date" id="to" name="to"
value="<?= $to ?>" class="form-control">
</div>

<div class="col-md-2">
<button class="btn btn-primary">Filter</button>
</div>

<div class="col-md-2">
<button type="button"
onclick="printReport()"
class="btn btn-success">
🖨 Print
</button>
</div>

</form>

</div>
</div>

<!-- ================= PRINT AREA ================= -->
<div id="printReport" class="print-area">

<h2 style="text-align:center;">
Student Order Summary
</h2>

<p style="text-align:center;">
From : <?= date('d-m-Y',strtotime($from)) ?>
&nbsp;&nbsp;
To : <?= date('d-m-Y',strtotime($to)) ?>
</p>

<?php
$currentMeal='';
$mealTotal=0;
$grandTotal=0;

while($row=mysqli_fetch_assoc($result)){

if($currentMeal!=$row['meal_type']){

if($currentMeal!=''){
echo "
<tr style='background:#ffeeba;font-weight:bold'>
<td colspan='3' align='right'>
Total $currentMeal
</td>
<td align='right'>₹".number_format($mealTotal,2)."</td>
</tr>
</tbody></table><br>";
$mealTotal=0;
}

$currentMeal=$row['meal_type'];
$sl=1;

echo "<h3 style='background:#28a745;
color:#fff;padding:8px'>
🍽 {$currentMeal}
</h3>";

echo "<table border='1'
width='100%'
cellpadding='6'
style='border-collapse:collapse'>

<thead style='background:#17a2b8;color:#fff'>
<tr>
<th width='5%'>SL</th>
<th width='20%'>Date</th>
<th width='45%'>Student</th>
<th width='30%'>Package</th>
</tr>
</thead>
<tbody>";
}

$mealTotal+=$row['price'];
$grandTotal+=$row['price'];

echo "<tr>
<td>".$sl++."</td>
<td>".date('d-m-Y',
strtotime($row['order_date']))."</td>
<td>{$row['student_name']}
({$row['student_id']})
</td>
<td>{$row['food_name']}</td>
</tr>";
}

if($currentMeal!=''){
echo "</tbody></table>";
}
?>

</div>
<!-- ================= END PRINT ================= -->

</div>
</div>

<?php include('../../include/footer.php'); ?>

<!-- ================= PRINT SCRIPT ================= -->
<script>
function printReport(){

    var content =
        document.getElementById('printReport').innerHTML;

    var win = window.open('', '',
        'width=900,height=700');

    win.document.write(`
        <html>
        <head>
        <title>Print Report</title>
        </head>
        <body>
        ${content}
        </body>
        </html>
    `);

    win.document.close();
    win.focus();
    win.print();
}
</script>