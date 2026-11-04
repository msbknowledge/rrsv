<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}

/* =========================
   DATE FILTER
========================= */
$wherePurchase = "1=1";
$whereSales    = "1=1";

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = mysqli_real_escape_string($myDB, $_GET['from_date']);
    $to   = mysqli_real_escape_string($myDB, $_GET['to_date']);

    $wherePurchase .= " AND DATE(l.created_at) BETWEEN '$from' AND '$to'";
    $whereSales    .= " AND foi.order_date BETWEEN '$from' AND '$to'";
}

/* =========================
   ITEM-WISE PURCHASE QUERY
========================= */
$purchaseSql = "
SELECT 
    DATE(l.created_at) AS report_date,
    i.item_name,
    i.unit,
    SUM(l.qty) AS total_qty,
    SUM(l.qty * l.rate) AS total_purchase
FROM rrsv_food_stock_ledger l
JOIN rrsv_food_items i ON i.id = l.item_id
WHERE l.direction = 'IN' AND $wherePurchase
GROUP BY DATE(l.created_at), l.item_id
ORDER BY report_date DESC, i.item_name
";
$purchaseResult = mysqli_query($myDB, $purchaseSql);

/* =========================
   SALES QUERY
========================= */
$salesSql = "
SELECT 
    foi.order_date,
    mp.package_name,
    mp.meal_type,
    SUM(foi.quantity) AS total_qty,
    SUM(foi.price * foi.quantity) AS total_amount
FROM rrsv_food_order_items foi
JOIN rrsv_menu_package mp ON mp.id = foi.food_id
WHERE foi.status = 'Confirmed' AND $whereSales
GROUP BY foi.order_date, mp.id
ORDER BY foi.order_date DESC
";
$salesResult = mysqli_query($myDB, $salesSql);
?>

<link href="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css" rel="stylesheet">

<div class="main-panel">
<div class="content-wrapper">

<!-- ================= FILTER ================= -->
<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="row">
            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>" class="form-control">
            </div>
            <div class="col-md-2 mt-4">
                <button class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= PURCHASE TABLE ================= -->
<div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title text-success">Item-wise Purchase Summary</h4>

        <table class="table table-bordered table-striped" id="purchaseTable">
            <thead class="bg-success text-white">
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Unit</th>
                    <th>Total Qty</th>
                    <th>Total Purchase (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $purchaseTotal = 0;
                while ($row = mysqli_fetch_assoc($purchaseResult)) {
                    $purchaseTotal += $row['total_purchase'];
                ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($row['report_date'])) ?></td>
                    <td><?= $row['item_name'] ?></td>
                    <td><?= $row['unit'] ?></td>
                    <td><?= number_format($row['total_qty'], 2) ?></td>
                    <td class="text-success"><?= number_format($row['total_purchase'], 2) ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;background:#f3f3f3">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-success"><?= number_format($purchaseTotal, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- ================= SALES TABLE ================= -->
<div class="card">
    <div class="card-body">
        <h4 class="card-title text-info">Daily Sales Summary</h4>

        <table class="table table-bordered table-striped" id="salesTable">
            <thead class="bg-info text-white">
                <tr>
                    <th>Date</th>
                    <th>Package Name</th>
                    <th>Meal Type</th>
                    <th>Total Qty</th>
                    <th>Total Sales (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $salesTotal = 0;
                while ($row = mysqli_fetch_assoc($salesResult)) {
                    $salesTotal += $row['total_amount'];
                ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($row['order_date'])) ?></td>
                    <td><?= $row['package_name'] ?></td>
                    <td><?= $row['meal_type'] ?></td>
                    <td><?= number_format($row['total_qty'], 2) ?></td>
                    <td class="text-info"><?= number_format($row['total_amount'], 2) ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;background:#f3f3f3">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-info"><?= number_format($salesTotal, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</div>
</div>

<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>
<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>

<script>
$(document).ready(function () {
    $('#purchaseTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10
    });

    $('#salesTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10
    });
});
</script>
