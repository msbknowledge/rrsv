<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');

if (empty($_SESSION['mid'])) {
    header('location:index.php');
    exit;
}

/* =========================
   DATE FILTER
========================= */
$wherePurchase = "1=1";
$whereSales    = "1=1";

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    // $from = mysqli_real_escape_string($myDB, $_GET['from_date']);
    // $to   = mysqli_real_escape_string($myDB, $_GET['to_date']);
    $from = !empty($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01'); // first day of month
$to   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('m-d-Y');      // today

    $wherePurchase .= " AND DATE(created_at) BETWEEN '$from' AND '$to'";
    $whereSales    .= " AND order_date BETWEEN '$from' AND '$to'";
}

/* =========================
   PURCHASE SUMMARY
========================= */
$purchaseSql = "
SELECT DATE(created_at) AS report_date, SUM(qty * rate) AS total_purchase
FROM rrsv_food_stock_ledger
WHERE direction = 'IN' AND $wherePurchase
GROUP BY DATE(created_at)
";
$res = mysqli_query($myDB, $purchaseSql);

$purchaseData = [];
while ($r = mysqli_fetch_assoc($res)) {
    $purchaseData[$r['report_date']] = $r['total_purchase'];
}

/* =========================
   SALES SUMMARY
========================= */
$salesSql = "
SELECT order_date AS report_date, SUM(price * quantity) AS total_sales
FROM rrsv_food_order_items
WHERE status = 'Confirmed' AND $whereSales
GROUP BY order_date
";
$res = mysqli_query($myDB, $salesSql);

$salesData = [];
while ($r = mysqli_fetch_assoc($res)) {
    $salesData[$r['report_date']] = $r['total_sales'];
}

/* =========================
   MERGE DATES
========================= */
$allDates = array_unique(array_merge(array_keys($purchaseData), array_keys($salesData)));
rsort($allDates);
?>
<style>

/* NORMAL TABLE STYLE */
#profitTable1{
    width:100%;
    border-collapse:collapse;
}

#profitTable1 th,
#profitTable1 td{
    padding:8px;
    border:1px solid #ddd;
}
#profitTable1 caption{
    caption-side: top;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 8px;
    text-align: center;
}
/* PRINT STYLE */
@media print {

    /* hide everything */
    body * {
        visibility: hidden;
    }

    /* show only table */
    #profitTable1,
    #profitTable1 * {
        visibility: visible;
    }

    /* table full page */
    #profitTable1 {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        margin: 0;
        padding: 0;
        font-size: 11px;
    }

    /* remove margins */
    @page {
        margin: 10mm;
    }

    /* table formatting */
    table {
        width: 100% !important;
        border-collapse: collapse;
    }

    th {
        background: #f2f2f2 !important;
        color: #000 !important;
        font-weight: bold;
    }

    th, td {
        border: 1px solid #000;
        padding: 2px;
        text-align: center;
    }

    /* hide buttons, sidebar etc */
    button,
    .btn,
    form,
    .sidebar,
    .navbar,
    .main-panel header,
    .content-wrapper form {
        display: none !important;
    }

}

</style>
<link href="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css" rel="stylesheet">

<div class="main-panel">
<div class="content-wrapper">

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="row">
            <div class="col-md-3">
                <label class="m-2">From Date</label>
                <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? $from ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="m-2">To Date</label>
                <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? $to ?>" class="form-control">
            </div>
            <div class="col-md-2 text-center">
                <button class="btn btn-primary  mt-4">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- PROFIT TABLE -->
<div class="card">
<div class="card-body">

<div class="mb-3 text-right">
    <button onclick="window.print()" class="btn btn-success">
        🖨️ Print
    </button>
</div>

<table class="table table-bordered" id="profitTable1">
   <caption class="table-caption text-center font-weight-bold">
        Daily Profit Summary (Sales − Purchase)
    </caption>
<thead class="bg-dark text-white">
<tr>
    <!--<th></th>-->
    <th>Date</th>
    <th>Purchase (₹)</th>
    <th>Sales (₹)</th>
    <th>Profit / Loss (₹)</th>
</tr>
</thead>
<tbody>

<?php
$totalPurchase = $totalSales = $totalProfit = 0;

foreach ($allDates as $date) {
    $purchase = $purchaseData[$date] ?? 0;
    $sales    = $salesData[$date] ?? 0;
    $profit   = $sales - $purchase;

    $totalPurchase += $purchase;
    $totalSales    += $sales;
    $totalProfit   += $profit;
?>
<tr data-date="<?= $date ?>">
    <!--<td class="details-control text-center" style="cursor:pointer;">➕</td>-->
    <td><?= date('d-m-Y', strtotime($date)) ?></td>
    <td class="text-danger"><?= number_format($purchase,2) ?></td>
    <td class="text-success"><?= number_format($sales,2) ?></td>
    <td class="<?= $profit >= 0 ? 'text-success':'text-danger' ?>">
        <b><?= number_format($profit,2) ?></b>
    </td>
</tr>
<?php } ?>

</tbody>
<tfoot style="font-weight:bold;background:#f3f3f3">
<tr>
    <td colspan="1" class="text-right">TOTAL</td>
    <td class="text-danger"><?= number_format($totalPurchase,2) ?></td>
    <td class="text-success"><?= number_format($totalSales,2) ?></td>
    <td class="<?= $totalProfit >= 0 ? 'text-success':'text-danger' ?>">
        <?= number_format($totalProfit,2) ?>
    </td>
</tr>
</tfoot>
</table>

</div>
</div>

</div>
</div>

<?php include('../../include/footer.php'); ?>

<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>

<script>
$(function () {

    var table = $('#profitTable').DataTable({
        order: [[1,'desc']],
        pageLength: 10
    });

    $('#profitTable tbody').on('click', '.details-control', function () {

        var tr   = $(this).closest('tr');
        var row  = table.row(tr);
        var date = tr.data('date');

        if (row.child.isShown()) {
            row.child.hide();
            $(this).html('➕');
        } else {
            $.get('ajax_profit_details.php', {date: date}, function (html) {
                row.child(html).show();
                tr.find('.details-control').html('➖');
            });
        }
    });

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const today = new Date();

    // First day of current month
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    // Format function (YYYY-MM-DD)
    function formatDate(date) {
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        let day = ("0" + date.getDate()).slice(-2);
        return date.getFullYear() + "-" + month + "-" + day;
    }

    document.querySelector('input[name="from_date"]').value = formatDate(firstDay);
    document.querySelector('input[name="to_date"]').value = formatDate(today);

});
</script>