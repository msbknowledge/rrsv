<?php
error_reporting(1);
include('../../include/header.php');
include('../../include/dbcon.php');

$id = 0;
$status = '';
$msg = '';
$current_page = $_GET['page'] ?? 1;

/* ------------------ GET PARAMS ------------------ */
if (isset($_GET['id'])) {
    $id = $myDB->escape_string($_GET['id']);
}

if (isset($_GET['mode'])) {
    $mode = $myDB->escape_string($_GET['mode']);
}

if (isset($_GET['status'])) {
    $status = $myDB->escape_string($_GET['status']);
}

if (isset($_GET['retcode'])) {
    if ($_GET['retcode'] == 1) $msg = "Course edited successfully";
    if ($_GET['retcode'] == 3) $msg = "Organization deleted successfully";
    if ($_GET['retcode'] == 4) $msg = "Organization inactivated successfully";
}

/* ------------------ STATUS UPDATE ------------------ */
if ($id > 0 && $mode == 'sts') {
    $status = ($status == 'Active') ? 'Inactive' : 'Active';

    $sql = "UPDATE rrsv_student_registration 
            SET status='$status' 
            WHERE id='$id'";
    mysqli_query($myDB, $sql);

    header("Location: manage_bill.php?retcode=4&page=$current_page");
    exit;
}

/* ------------------ PAGINATION ------------------ */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$perpage = 2000;
$offset = ($page - 1) * $perpage;

/* ------------------ FETCH DATA ------------------ */
$sql = "SELECT * FROM rrsv_scl_pl ORDER BY id DESC LIMIT $offset,$perpage";
$result = mysqli_query($myDB, $sql);

$countRes = mysqli_query($myDB, "SELECT COUNT(*) AS total FROM rrsv_scl_pl");
$totalRow = mysqli_fetch_assoc($countRes);
$totalpage = ceil($totalRow['total'] / $perpage);

$incomeamount = 0;
$expamount = 0;
?>

<script>
function confirmsearch(id, status) {
    if (confirm("Are you sure to change this Student status?")) {
        window.location.href = "manage_bill.php?mode=sts&id=" + id + "&status=" + status;
    }
}
</script>

<link href="libray/DataTables/datatables.min.css" rel="stylesheet">

<div class="main-panel">
<div class="content-wrapper">

<div class="card">
<div style="font-weight:bold;text-align:center;">Income & Expense Report</div>

<form method="post" action="report.php"id="frmsearch">
<table class="table">

<tr>
<td colspan="8" align="center">

<select name="purpose" class="form-control" style="width:200px;display:inline;">
<option value="">-- Select Purpose --</option>
<?php
$q = mysqli_query($myDB, "SELECT DISTINCT purpose FROM rrsv_scl_pl");
while ($r = mysqli_fetch_assoc($q)) {
    echo "<option value='{$r['purpose']}'>{$r['purpose']}</option>";
}
?>
</select>

<input type="date" name="fromDate" id="fromDate" class="form-control" style="width:160px;display:inline;">
<input type="date" name="endDate" id="endDate" class="form-control" style="width:160px;display:inline;">

<select name="session" class="form-control" style="width:150px;display:inline;">
<option value="">Session</option>
<?php
for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
    echo "<option value='$i'>$i</option>";
}
?>
</select>

<button type="submit" class="btn btn-primary">View Report</button>

</td>
</tr>

<tr>
<th>#</th>
<th>Bill No</th>
<th>Date</th>
<th>Purpose</th>
<th>Income</th>
<th>Expense</th>
</tr>

<?php
$i = 0;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $i++;
        $incomeamount += $row['income_amount'];
        $expamount += $row['exp_amount'];
        ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= $row['bill'] ?></td>
            <td><?= date('d-m-Y', strtotime($row['pl_date'])) ?></td>
            <td><?= $row['purpose'] ?></td>
            <td><?= $row['income_amount'] ?></td>
            <td><?= $row['exp_amount'] ?></td>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='6' align='center'>No Records Found</td></tr>";
}
?>

<tr>
<td><b>Total Cash:</b> <?= $incomeamount - $expamount ?></td>
<td></td>
<td></td>
<td><?= $incomeamount ?></td>
<td></td>
<td><?= $expamount ?></td>
</tr>

</table>
</form>

<!-- Pagination -->
<ul class="pagination justify-content-center">
<?php if ($page > 1): ?>
<li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
<?php endif; ?>

<?php for ($i = 1; $i <= $totalpage; $i++): ?>
<li class="page-item"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
<?php endfor; ?>

<?php if ($page < $totalpage): ?>
<li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
<?php endif; ?>
</ul>

</div>
</div>
</div>

<script>
$('#frmsearch').submit(function () {
    if (!$('#fromDate').val() || !$('#endDate').val()) {
        alert('Please select date range');
        return false;
    }
});
</script>

<?php include('../../include/footer.php'); ?>
