<?php
include('../../include/dbcon.php');

$order_id = intval($_GET['order_id']);

$q = mysqli_query($myDB,"
SELECT 
s.scl_name,
s.scl_reg_no,
o.id AS order_no,
o.total_amount,
v.name AS vendor_name,
i.item,
i.quantity,
i.rate,
i.amount

FROM rrsv_uniform_orders o

JOIN rrsv_student_registration s 
ON s.id = o.student_id

JOIN rrsv_order_items i 
ON i.order_id = o.id

LEFT JOIN rrsv_vendor v 
ON v.id = i.vendor_id

WHERE o.id='$order_id'
");

$items = [];
$vendors = [];
$student = [];

while($row=mysqli_fetch_assoc($q)){

$items[] = $row;

$vendors[$row['vendor_name']][] = $row;

$student = $row;

}
?>

<h3>Student Copy</h3>

Student Name : <?= $student['scl_name'] ?><br>
Reg No : <?= $student['scl_reg_no'] ?><br>
Order No : <?= $student['order_no'] ?><br>

<br>

<table border="1" width="100%" cellpadding="5">

<tr>
<th>Vendor</th>
<th>Item</th>
<th>Qty</th>
<th>Rate</th>
<th>Amount</th>
</tr>

<?php foreach($items as $row){ ?>

<tr>

<td><?= $row['vendor_name'] ?></td>
<td><?= $row['item'] ?></td>
<td><?= $row['quantity'] ?></td>
<td><?= $row['rate'] ?></td>
<td><?= $row['amount'] ?></td>

</tr>

<?php } ?>

<tr>
<td colspan="4" align="right"><b>Total</b></td>
<td><b><?= $student['total_amount'] ?></b></td>
</tr>

</table>

<hr style="border:1px dashed #000">
<h3>Vendor Slip</h3>

<?php foreach($vendors as $vendor=>$rows){ ?>


Student Name : <?= $student['scl_name'] ?><br>
Reg No : <?= $student['scl_reg_no'] ?><br>
Order No : <?= $student['order_no'] ?><br>
<h4>Vendor : <?= $vendor ?></h4>

<table border="1" width="100%" cellpadding="5">

<tr>
<th>Item</th>
<th>Qty</th>
<th>Rate</th>
</tr>

<?php foreach($rows as $r){ ?>

<tr>

<td><?= $r['item'] ?></td>
<td><?= $r['quantity'] ?></td>
<td><?= $r['rate'] ?></td>

</tr>

<?php } ?>

</table>

<br>

<?php } ?>