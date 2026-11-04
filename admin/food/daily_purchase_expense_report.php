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

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $where .= " AND d.report_date BETWEEN '$from' AND '$to'";
}

/* DAILY SUMMARY */
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
<style>
td.details-control {
    background: #f8f9fa;
    text-align: center;
    font-size: 18px;
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

<!-- REPORT TABLE -->
<div class="card">
<div class="card-body">
<h4 class="card-title">Daily Purchase vs Expense vs Profit</h4>

<table class="table table-bordered table-striped" id="dailyReport">
<thead class="bg-info text-white">
<tr>
    <th></th>
    <th>Date</th>
    <th>Purchase (₹)</th>
    <th>Expense (₹)</th>
    <th>Profit / Loss (₹)</th>
</tr>
</thead>
<tbody>

<?php
$tp = $te = $tpr = 0;

while($row = mysqli_fetch_assoc($result)){
    $tp += $row['purchase_total'];
    $te += $row['expense_total'];
    $tpr += $row['profit'];
?>
<tr data-date="<?= $row['report_date'] ?>">
     <td class="details-control text-center" style="cursor:pointer;font-weight:bold;">+</td>
    <td><?= date('d-m-Y', strtotime($row['report_date'])) ?></td>
    <td class="text-success"><?= number_format($row['purchase_total'],2) ?></td>
    <td class="text-danger"><?= number_format($row['expense_total'],2) ?></td>
    <td class="<?= $row['profit']>=0?'text-success':'text-danger' ?>">
        <b><?= number_format($row['profit'],2) ?></b>
    </td>
</tr>
<?php } ?>

</tbody>

<tfoot style="background:#f3f3f3;font-weight:bold">
<tr>
    <td colspan="2" class="text-right">TOTAL</td>
    <td class="text-success"><?= number_format($tp,2) ?></td>
    <td class="text-danger"><?= number_format($te,2) ?></td>
    <td><?= number_format($tpr,2) ?></td>
</tr>
</tfoot>
</table>
</div>
</div>

</div>
</div>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>


<script>
function formatDetails(data) {

    let html = `<div style="padding:10px;">`;

    if (data.purchase && data.purchase.length > 0) {
        html += `
        <h6 class="text-success">📥 Purchase Details</h6>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Item</th><th>Qty</th><th>Rate</th><th>Total</th>
            </tr>`;
        data.purchase.forEach(r => {
            html += `
            <tr>
                <td>${r.item_name}</td>
                <td>${r.qty}</td>
                <td>₹ ${r.rate}</td>
                <td>₹ ${r.total}</td>
            </tr>`;
        });
        html += `</table>`;
    }

    if (data.expense && data.expense.length > 0) {
        html += `
        <h6 class="text-danger mt-2">📤 Expense Details</h6>
        <table class="table table-sm table-bordered">
            <tr>
                <th>Item</th><th>Qty</th><th>Rate</th><th>Total</th>
            </tr>`;
        data.expense.forEach(r => {
            html += `
            <tr>
                <td>${r.item_name}</td>
                <td>${r.qty}</td>
                <td>₹ ${r.rate}</td>
                <td>₹ ${r.total}</td>
            </tr>`;
        });
        html += `</table>`;
    }

    html += `</div>`;
    return html;
}

$(document).ready(function () {

    var table = $('#dailyReport').DataTable({
        order: [[1, 'desc']],
        pageLength: 10,
        columnDefs: [{
            targets: 0,
            orderable: false,
            className: 'details-control'
        }]
    });

    $('#dailyReport tbody').on('click', 'td.details-control', function () {

        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var date = tr.attr('data-date');
        var cell = $(this);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            cell.text('+');
        } else {
            cell.text('⏳');

            $.ajax({
                url: 'ajax_daily_item_details.php',
                type: 'GET',
                data: { date: date },
                dataType: 'json',
                success: function (res) {
                    row.child(
                        `<tr>
                            <td colspan="5">${formatDetails(res)}</td>
                         </tr>`
                    ).show();
                    tr.addClass('shown');
                    cell.text('−');
                },
                error: function () {
                    alert('AJAX failed');
                    cell.text('+');
                }
            });
        }
    });
});
</script>


<?php include('../../include/footer.php'); ?>
