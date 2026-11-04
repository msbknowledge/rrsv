<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$student_id = $_GET['student_id'];

$student = mysqli_fetch_assoc(mysqli_query($myDB,"
SELECT * FROM rrsv_student_registration 
WHERE id='$student_id'
"));
?>
<!--<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>-->
<link rel="stylesheet" 
href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!--<link rel="stylesheet" -->
<!--href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">-->
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>-->
<div class="main-panel">
<div class="content-wrapper">

<div class="card">
<div class="card-body">

<h4 class="card-title">Add Student Items</h4>

<form method="POST" action="save_items.php">

<input type="hidden" name="student_id" value="<?= $student_id ?>">

<div class="row">

<div class="col-md-3 form-group">
<label>Search Student</label>

<input type="text" id="student_search" 
class="form-control" 
placeholder="Search by Name / Reg No">

<input type="hidden" name="student_id" id="student_id">

</div>

<div class="col-md-3 form-group">
<label>Reg No</label>
<input type="text" id="scl_reg_no" 
class="form-control" readonly>
</div>

<div class="col-md-2 form-group">
<label>Class</label>
<input type="text" id="scl_class" 
class="form-control" readonly>
</div>

<div class="col-md-2 form-group">
<label>Session</label>
<input type="text" id="scl_session" 
class="form-control" readonly>
</div>

<div class="col-md-2 form-group">
<label>Name</label>
<input type="text" id="scl_name" 
class="form-control" readonly>
</div>

</div>

<hr>

<h5>Items</h5>

<table class="table table-bordered" id="itemTable">

<thead>
<tr>
<th>Vendor</th>
<th>Item</th>
<th width="120">Qty</th>
<th width="120">Rate</th>
<th width="120">Amount</th>
<th width="50">#</th>
</tr>
</thead>

<tbody id="itemBody">

<tr>

<td>
<select name="vendor_id[]" class="form-control">

<?php
$v = mysqli_query($myDB,"SELECT * FROM rrsv_vendor");

while($vendor=mysqli_fetch_assoc($v)){
?>

<option value="<?= $vendor['id'] ?>">
<?= $vendor['name'] ?>
</option>

<?php } ?>

</select>
</td>

<td>
<input type="text" name="item_name[]" class="form-control">
</td>

<td>
<input type="number" name="qty[]" class="form-control qty">
</td>

<td>
<input type="number" name="rate[]" class="form-control rate">
</td>

<td>
<input type="number" name="amount[]" class="form-control amount">
</td>

<td>
<button type="button" class="btn btn-success addRow">+</button>
</td>

</tr>

</tbody>

</table>

<div class="row p-2">

<div class="col-md-8"></div>

<div class="col-md-4 text-right">

<h4 class="border p-2">

Total Amount :
<span id="totalAmount">0.00</span>

</h4>

<input type="hidden" name="total_amount" id="total_amount">

</div>

</div>

<br>

<button class="btn btn-primary">Save Items</button>

</form>

</div>
</div>

</div>
</div>


<script src="<?php echo BASE_URL; ?>libray/DataTables/dataTables.buttons.min.js"></script>
<script src="<?php echo BASE_URL; ?>libray/DataTables/buttons.print.min.js"></script>
<script>

$(document).on("click",".addRow",function(){

let row = `

<tr>

<td>
<select name="vendor_id[]" class="form-control">

<?php
$v = mysqli_query($myDB,"SELECT * FROM rrsv_vendor");
while($vendor=mysqli_fetch_assoc($v)){
?>

<option value="<?= $vendor['id'] ?>">
<?= $vendor['name'] ?>
</option>

<?php } ?>

</select>
</td>

<td>
<input type="text" name="item_name[]" class="form-control">
</td>

<td>
<input type="number" name="qty[]" class="form-control qty">
</td>

<td>
<input type="number" name="rate[]" class="form-control rate">
</td>

<td>
<input type="number" name="amount[]" class="form-control amount">
</td>

<td>
<button type="button" class="btn btn-danger removeRow">X</button>
</td>

</tr>

`;

$("#itemBody").append(row);

});
$(document).on("click",".removeRow",function(){

$(this).closest("tr").remove();
calculateTotal();

});

$(document).on("keyup",".qty,.rate",function(){

let row = $(this).closest("tr");

let qty = parseFloat(row.find(".qty").val()) || 0;
let rate = parseFloat(row.find(".rate").val()) || 0;

let amount = qty * rate;

row.find(".amount").val(amount.toFixed(2));

calculateTotal();

});

function calculateTotal(){

let total = 0;

$(".amount").each(function(){

total += parseFloat($(this).val()) || 0;

});

$("#totalAmount").text(total.toFixed(2));
$("#total_amount").val(total.toFixed(2));

}


$("#student_search").autocomplete({

source: "search_student.php",
minLength: 1,

select: function(event,ui){

$("#student_id").val(ui.item.id);
$("#scl_reg_no").val(ui.item.reg_no);
$("#scl_name").val(ui.item.name);
$("#scl_class").val(ui.item.class);
$("#scl_session").val(ui.item.session);

}

});

</script>
<?php include('../../include/footer.php'); ?>