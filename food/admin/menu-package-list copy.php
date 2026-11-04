<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($con, "DELETE FROM rrsv_menu_package_items WHERE package_id='$id'");
    mysqli_query($con, "DELETE FROM rrsv_menu_package WHERE id='$id'");

    $msg = "Menu package deleted successfully";
}
?>
<!doctype html>
<html>
<head>
<title>Menu Package List</title>

<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include('includes/header.php'); ?>
<div class="ts-main-content">
<?php include('includes/leftbar.php'); ?>

<div class="content-wrapper container-fluid">

<h2 class="page-title">Menu Packages</h2>

<a href="menu-package-add.php" class="btn btn-success mb-3">
➕ Add Menu Package
</a>
<br/><br/>
<hr/>
<?php if (!empty($msg)) { ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php } ?>

<table id="datatable" class="table table-bordered table-striped">
<thead>
<tr>
<th>#</th>
<th>Package Name</th>
<th>Meal Type</th>
<th>Items</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php
$q = mysqli_query($con, "
    SELECT 
        p.id,
        p.package_name,
        p.meal_type,
        p.price,
        p.active,
        COUNT(pi.food_id) AS total_items
    FROM rrsv_menu_package p
    LEFT JOIN rrsv_menu_package_items pi ON pi.package_id = p.id
    GROUP BY p.id
    ORDER BY p.id DESC
");

$i = 1;
while ($r = mysqli_fetch_assoc($q)) {
?>
<tr>
<td><?= $i++ ?></td>
<td><?= $r['package_name'] ?></td>
<td><?= $r['meal_type'] ?></td>
<td>
<span class="badge badge-info">
<?= $r['total_items'] ?> items
</span>
</td>
<td>₹<?= number_format($r['price'],2) ?></td>
<td>
<span class="badge <?= $r['active']=='Yes' ? 'badge-success' : 'badge-danger' ?>">
<?= $r['active'] ?>
</span>
</td>
<td>
<a href="menu-package-view.php?id=<?= $r['id'] ?>" 
   class="btn btn-info btn-sm">View</a>

<a href="menu-package-edit.php?id=<?= $r['id'] ?>" 
   class="btn btn-primary btn-sm">Edit</a>

<a href="?delete=<?= $r['id'] ?>" 
   onclick="return confirm('Delete this package?')" 
   class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function(){
    $('#datatable').DataTable();
});
</script>

</body>
</html>
