<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit;
}

$where = "1=1";

// Date filter
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $where .= " AND d.report_date BETWEEN '$from' AND '$to'";
}

/*
 DAILY SUMMARY QUERY
 - Purchases from rrsv_food_purchases
 - Expenses from rrsv_food_expense
*/
$sql = "
SELECT 
    d.report_date,

    IFNULL(p.purchase_total,0) AS purchase_total,
    IFNULL(e.expense_total,0) AS expense_total,

    (IFNULL(p.purchase_total,0) - IFNULL(e.expense_total,0)) AS profit

FROM (
    SELECT DISTINCT DATE(created_at) AS report_date
    FROM rrsv_food_stock_ledger
) d

LEFT JOIN (
    SELECT 
        DATE(purchase_date) AS pdate,
        SUM(grand_total) AS purchase_total
    FROM rrsv_food_purchases
    GROUP BY DATE(purchase_date)
) p ON p.pdate = d.report_date

LEFT JOIN (
    SELECT 
        DATE(issue_date) AS edate,
        SUM(grand_total) AS expense_total
    FROM rrsv_food_expense
    GROUP BY DATE(issue_date)
) e ON e.edate = d.report_date

WHERE $where
ORDER BY d.report_date DESC
";

$result = mysqli_query($myDB, $sql);
?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<div class="main-panel">
    <div class="content-wrapper">

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
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daily Purchase vs Expense Summary</h4>
            
                    <table class="table table-bordered table-striped" id="dailyReport">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Date</th>
                                <th>Purchase (₹)</th>
                                <th>Expense (₹)</th>
                                <th>Profit / Loss (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalPurchase = 0;
                            $totalExpense  = 0;
                            $totalProfit   = 0;
            
                            while($row = mysqli_fetch_assoc($result)) {
            
                                $totalPurchase += $row['purchase_total'];
                                $totalExpense  += $row['expense_total'];
                                $totalProfit   += $row['profit'];
                            ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($row['report_date'])) ?></td>
            
                                <td class="text-success">
                                    <?= number_format($row['purchase_total'],2) ?>
                                </td>
            
                                <td class="text-danger">
                                    <?= number_format($row['expense_total'],2) ?>
                                </td>
            
                                <td class="<?= ($row['profit'] >= 0) ? 'text-success' : 'text-danger' ?>">
                                    <b><?= number_format($row['profit'],2) ?></b>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
            
                        <tfoot style="background:#f3f3f3;font-weight:bold;">
                            <tr>
                                <td class="text-right">TOTAL</td>
                                <td class="text-success"><?= number_format($totalPurchase,2) ?></td>
                                <td class="text-danger"><?= number_format($totalExpense,2) ?></td>
                                <td><?= number_format($totalProfit,2) ?></td>
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
    $('#dailyReport').DataTable({
        paging: true,
        ordering: true,
        searching: true,
        pageLength: 10,
        order: [[0, 'desc']]
    });
});
</script>
