<?php
include('../../include/dbcon.php');
$date = $_GET['date'];
?>
<div class="card">
<div class="card-body">
<h6 class="text-success">Purchase Details</h6>
<table class="table  table-bordered">
<tr><th>#</th><th>Item</th><th>Qty</th><th>Rate</th><th>Total</th></tr>
<?php
$q = mysqli_query($myDB,"
SELECT i.item_name, l.qty, l.rate, l.qty*l.rate total
FROM rrsv_food_stock_ledger l
JOIN rrsv_food_items i ON i.id=l.item_id
WHERE l.direction='IN' AND DATE(l.created_at)='$date'
");
while($r=mysqli_fetch_assoc($q)){
?>
<tr>
    <td>*</td>
<td><?= $r['item_name'] ?></td>
<td><?= $r['qty'] ?></td>
<td>₹<?= $r['rate'] ?></td>
<td>₹<?= $r['total'] ?></td>
</tr>
<?php } ?>
</table>

<h6 class="text-info mt-3">Sales Details</h6>
<table class="table table-bordered">
<tr><th>#</th><th>Package</th><th>Meal</th><th>Qty</th><th>Rate</th><th>Total</th></tr>
<?php
$q = mysqli_query($myDB,"
SELECT mp.package_name, mp.meal_type, foi.quantity, foi.price, foi.quantity*foi.price total
FROM rrsv_food_order_items foi
JOIN rrsv_menu_package mp ON mp.id=foi.food_id
WHERE foi.order_date='$date' AND foi.status='Confirmed'
");
while($r=mysqli_fetch_assoc($q)){
?>
<tr>
   <td>*</td>
<td><?= $r['package_name'] ?></td>
<td><?= $r['meal_type'] ?></td>
<td><?= $r['quantity'] ?></td>
<td>₹<?= $r['price'] ?></td>
<td>₹<?= $r['total'] ?></td>
</tr>
<?php } ?>
</table>
</div>
</div>

