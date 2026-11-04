<?php 
include('../../include/header.php'); 
include('../../include/dbcon.php'); 
?>

<div class="main-panel">
<div class="content-wrapper">


<a href="add_items.php" class="btn btn-primary">+ Add Bill</a>
<br><br>
<h4>Student Vendor Items</h4>

<table id="student_vendor" class="table table-bordered table-striped">
<thead>
<tr>
<th>ID</th>
<th>Student Name</th>
<th>Total Amount</th>
<th>Session</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php

$q = mysqli_query($myDB,"
SELECT 
o.id,
s.scl_session,
s.scl_name,
o.total_amount
FROM rrsv_uniform_orders o
JOIN rrsv_student_registration s 
ON s.id = o.student_id
ORDER BY o.id DESC
");

while($row=mysqli_fetch_assoc($q)){
?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= $row['scl_name'] ?></td>
<td>₹<?= $row['total_amount'] ?></td>
<td><?= $row['scl_session'] ?></td>



<td>

<a href="add_items.php?order_id=<?= $row['id'] ?>" 
class="btn btn-primary btn-sm">Add</a>

<a href="edit_items.php?order_id=<?= $row['id'] ?>" 
class="btn btn-warning btn-sm">Edit</a>

<!--<a href="delete_items.php?order_id=<?= $row['id'] ?>" -->
<!--class="btn btn-danger btn-sm"-->
<!--onclick="return confirm('Delete order?')">-->
<!--Delete-->
<!--</a>-->

<a href="print_slip.php?order_id=<?= $row['id'] ?>" 
class="btn btn-success btn-sm" target="_blank">
Print Slip
</a>

</td>

</tr>

<?php } ?>
<tbody>
</table>

</div>
</div>
<script>
  $(document).ready(function () {
    $('#student_vendor').DataTable({
      pageLength: 25,
      order: [[1, 'asc']]
    });
  });
  </script>
<?php include('../../include/footer.php'); ?>