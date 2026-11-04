<?php
// edit_vendor_bill.php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo "Invalid bill id.";
  exit;
}

// Fetch bill
$bill_q = mysqli_query($myDB, "SELECT * FROM rrsv_vendor_bills WHERE id = {$id}");
$bill = mysqli_fetch_assoc($bill_q);
if (!$bill) {
  echo "Bill not found.";
  exit;
}

// Fetch items
$items_q = mysqli_query($myDB, "SELECT * FROM rrsv_vendor_bill_items WHERE bill_id = {$id} ORDER BY id ASC");

// Fetch vendors
$vendors_q = mysqli_query($myDB, "SELECT id, name FROM rrsv_vendor WHERE status='Active' ORDER BY name ASC");
?>

<div class="main-panel">
<div class="content-wrapper">

<div class="card">
<div class="card-body">

<h4 class="card-title">Edit Vendor Bill #<?php echo $bill['bill_no']; ?></h4>

<form action="update_vendor_bill.php" method="POST" id="billForm">
<input type="hidden" name="id" value="<?php echo $bill['id']; ?>">

<div class="row">
<div class="col-md-4 form-group">
<label>Vendor</label>
<select name="vendor_id" class="form-control" required>
<option value="">Select Vendor</option>
<?php while ($v = mysqli_fetch_assoc($vendors_q)) {
$sel = ($v['id'] == $bill['vendor_id']) ? "selected" : "";
echo "<option value='{$v['id']}' {$sel}>{$v['name']}</option>";
} ?>
</select>
</div>

<div class="col-md-4 form-group">
<label>Bill No</label>
<input type="text" name="bill_no" value="<?php echo $bill['bill_no']; ?>" class="form-control" readonly>
</div>

<div class="col-md-4 form-group">
<label>Bill Date</label>
<input type="date" name="bill_date" value="<?php echo $bill['bill_date']; ?>" class="form-control" readonly>
</div>
</div>

<br>

<table class="table table-bordered" id="itemTable">
<thead>
<tr>
<th>Item Name</th>
<th width="120">Qty</th>
<th width="120">Rate</th>
<th width="120">Amount</th>
<th width="50">#</th>
</tr>
</thead>
<tbody id="itemBody">

<?php
if (mysqli_num_rows($items_q) > 0) {
while ($it = mysqli_fetch_assoc($items_q)) {
?>
<tr>
<td>
<input type="hidden" name="item_id[]" value="<?php echo $it['item_id']; ?>" class="item_id">
<input type="text" name="item_name[]" value="<?php echo $it['item_name']; ?>" class="form-control item_name" autocomplete="off" required>
</td>
<td><input type="number" name="qty[]" value="<?php echo $it['qty']; ?>" class="form-control qty" required></td>
<td><input type="number" step="0.01" name="rate[]" value="<?php echo $it['rate']; ?>" class="form-control rate" readonly=""></td>
<td><input type="text" name="amount[]" value="<?php echo $it['amount']; ?>" class="form-control amount" readonly></td>
<td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
</tr>
<?php
}
} else {
?>
<tr>
<td>
<input type="hidden" name="item_id[]" class="item_id">
<input type="text" name="item_name[]" class="form-control item_name" autocomplete="off" required>
</td>
<td><input type="number" name="qty[]" class="form-control qty" required></td>
<td><input type="number" step="0.01" name="rate[]" class="form-control rate" required></td>
<td><input type="text" name="amount[]" class="form-control amount" readonly></td>
<td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
</tr>
<?php } ?>

</tbody>
</table>

<div class="row p-2">
<div class="col-md-4 form-group">
<!--<button type="button" class="btn btn-success" id="addRow">+ Add Item</button>-->
</div>

<div class="col-md-7 form-group" style="text-align:right">
<h4 class="btn btn-light border" style="font-weight:700px; font-size:24px;">Total Amount: <span id="totalAmount"><?php echo number_format($bill['total_amount'],2); ?></span></h4>
<input type="hidden" name="total_amount" id="total_amount" value="<?php echo $bill['total_amount']; ?>">
</div>
</div>
<div class="row">
<div class="col-md-4 form-group">
<label>Remarks</label>
<textarea name="remarks" class="form-control"><?php echo $bill['remarks']; ?></textarea>
</div>
</div>

<br>
<button class="btn btn-primary">Update Bill</button>
<a href="manage_vendor_bill.php" class="btn btn-light">Cancel</a>

</form>

</div>
</div>

</div>
</div>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- jQuery UI for autocomplete -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


<script>
var jqx = jQuery.noConflict(true);

// calculate totals
function calculateAmount() {
    let total = 0;
    jqx("#itemBody tr").each(function () {
        let qty = parseFloat(jqx(this).find(".qty").val()) || 0;
        let rate = parseFloat(jqx(this).find(".rate").val()) || 0;
        let amount = qty * rate;
        jqx(this).find(".amount").val(amount.toFixed(2));
        total += amount;
    });
    jqx("#totalAmount").text(total.toFixed(2));
    jqx("#total_amount").val(total.toFixed(2));
}

jqx(document).on("keyup change", ".qty, .rate", calculateAmount);

// add row
jqx("#addRow").click(function () {
    jqx("#itemBody").append(`
<tr>
<td>
<input type="hidden" name="item_id[]" class="item_id">
<input type="text" name="item_name[]" class="form-control item_name" autocomplete="off" required>
</td>
<td><input type="number" name="qty[]" class="form-control qty" required></td>
<td><input type="number" step="0.01" name="rate[]" class="form-control rate" readonly></td>
<td><input type="text" name="amount[]" class="form-control amount" readonly></td>
<td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
</tr>
`);
});

// remove row
jqx(document).on("click", ".removeRow", function () {
    jqx(this).closest("tr").remove();
    calculateAmount();
});

// autocomplete
jqx(document).on("focus", ".item_name", function () {
    var $input = jqx(this);

    if ($input.data("ui-autocomplete")) return;

    $input.autocomplete({
        source: "search_expense.php",
        minLength: 1,
        select: function (event, ui) {
            var $row = $input.closest("tr");
            $row.find(".item_id").val(ui.item.item_id);
            $input.val(ui.item.label);
             $row.find(".rate").val(parseFloat(ui.item.rate).toFixed(2)); // auto rate
            $row.find(".qty").focus();
            calculateAmount();
            return false;
        }
    });
});
</script>

<?php include('../../include/footer.php'); ?>
