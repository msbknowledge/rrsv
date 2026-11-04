<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$order_id = $_GET['order_id'];

$order = mysqli_fetch_assoc(mysqli_query($myDB,"
SELECT o.*,s.scl_name,s.scl_reg_no,s.scl_class,s.scl_session
FROM rrsv_uniform_orders o
LEFT JOIN rrsv_student_registration s ON s.id=o.student_id
WHERE o.id='$order_id'
"));

$items = mysqli_query($myDB,"
SELECT * FROM rrsv_order_items
WHERE order_id='$order_id'
");
?>

<div class="main-panel">
<div class="content-wrapper">

<div class="card">
<div class="card-body">

<h4>Edit Student Items</h4>

<form method="POST" action="edit_save_items.php">

<input type="hidden" name="order_id" value="<?= $order_id ?>">

<div class="row">

<div class="col-md-3">
<label>Reg No</label>
<input type="text" class="form-control" value="<?= $order['scl_reg_no'] ?>" readonly>
</div>

<div class="col-md-3">
<label>Name</label>
<input type="text" class="form-control" value="<?= $order['scl_name'] ?>" readonly>
</div>

<div class="col-md-3">
<label>Class</label>
<input type="text" class="form-control" value="<?= $order['scl_class'] ?>" readonly>
</div>

<div class="col-md-3">
<label>Session</label>
<input type="text" class="form-control" value="<?= $order['scl_session'] ?>" readonly>
</div>

</div>

<hr>

<table class="table table-bordered">

<thead>
<tr>
<th>Vendor</th>
<th>Item</th>
<th>Qty</th>
<th>Rate</th>
<th>Amount</th>
<th>#</th>
</tr>
</thead>

<tbody id="itemBody">

<?php while($row=mysqli_fetch_assoc($items)){ ?>

<tr>

<td>
<select name="vendor_id[]" class="form-control">

<?php
$v=mysqli_query($myDB,"SELECT * FROM rrsv_vendor");
while($vendor=mysqli_fetch_assoc($v)){
?>

<option value="<?= $vendor['id'] ?>"
<?php if($vendor['id']==$row['vendor_id']) echo "selected"; ?>>
<?= $vendor['name'] ?>
</option>

<?php } ?>

</select>
</td>

<td>
<input type="text" name="item_name[]" class="form-control"
value="<?= $row['item'] ?>">
</td>

<td>
<input type="number" name="qty[]" class="form-control qty"
value="<?= $row['quantity'] ?>">
</td>

<td>
<input type="number" name="rate[]" class="form-control rate"
value="<?= $row['rate'] ?>">
</td>

<td>
<input type="number" name="amount[]" class="form-control amount"
value="<?= $row['amount'] ?>">
</td>

<td>
<button type="button" class="btn btn-danger removeRow">X</button>
</td>

</tr>

<?php } ?>

</tbody>

</table>

<button type="button" class="btn btn-success addRow">Add Row</button>

<br><br>

<button class="btn btn-primary">Update Items</button>

</form>

</div>
</div>

</div>
</div>

<?php include('../../include/footer.php'); ?>