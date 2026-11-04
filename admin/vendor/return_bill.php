<?php
// edit_vendor_bill.php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    exit("Invalid bill id.");
}

// Fetch bill master
$bill = mysqli_fetch_assoc(mysqli_query($myDB,
    "SELECT * FROM rrsv_vendor_bills WHERE id = {$id} LIMIT 1"
));
if (!$bill) exit("Bill not found.");

// Fetch bill items
$items_q = mysqli_query($myDB,
    "SELECT * FROM rrsv_vendor_bill_items WHERE bill_id = {$id} ORDER BY id ASC"
);

// Fetch vendor list
$vendors_q = mysqli_query($myDB,
    "SELECT id, name FROM rrsv_vendor WHERE status='Active' ORDER BY name ASC"
);
?>

<div class="main-panel">
<div class="content-wrapper">

<div class="card">
<div class="card-body">

<h4 class="card-title">Edit Vendor Bill #<?= htmlspecialchars($bill['bill_no']) ?></h4>

<form action="save_return.php" method="POST" id="billForm">
<input type="hidden" name="id" value="<?= $bill['id'] ?>">

<div class="row">
    <div class="col-md-4 form-group">
        <label>Vendor</label>
        <select name="vendor_id" class="form-control" readonly>
            <option value="">Select Vendor</option>
            <?php while ($v = mysqli_fetch_assoc($vendors_q)) { ?>
                <option value="<?= $v['id'] ?>" 
                    <?= ($v['id'] == $bill['vendor_id']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($v['name']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label>Bill No</label>
        <input type="text" name="bill_no" class="form-control"
               value="<?= htmlspecialchars($bill['bill_no']) ?>" readonly>
    </div>

    <div class="col-md-4 form-group">
        <label>Bill Date</label>
        <input type="date" name="bill_date" class="form-control"
               value="<?= $bill['bill_date'] ?>" readonly>
    </div>
</div>

<br>

<table class="table table-bordered" id="itemTable">
<thead>
<tr>
    <th>Item Name</th>
    <th width="120">Qty</th>
    <th width="120">Amount</th>
</tr>
</thead>

<tbody id="itemBody">
<?php if (mysqli_num_rows($items_q) > 0) {
while ($it = mysqli_fetch_assoc($items_q)) { ?>
<tr>
    <td>
        <input type="hidden" name="item_id[]" value="<?= $it['item_id'] ?>" class="item_id">
        <input type="text" name="item_name[]" value="<?= htmlspecialchars($it['item_name']) ?>"
               class="form-control item_name" autocomplete="off" readonly>
    </td>

    <td>
        <input type="number" name="qty[]" value="<?= $it['qty'] ?>"
               class="form-control qty" required>
    </td>

    <td>
        <input type="text" name="amount[]" value="<?= $it['amount'] ?>"
               class="form-control amount" readonly>
    </td>
</tr>
<?php }} else { ?>
<tr>
    <td>
        <input type="hidden" name="item_id[]" class="item_id">
        <input type="text" name="item_name[]" class="form-control item_name" autocomplete="off" required>
    </td>

    <td><input type="number" name="qty[]" class="form-control qty" required></td>

    <td><input type="text" name="amount[]" class="form-control amount" readonly></td>
</tr>
<?php } ?>
</tbody>
</table>

<div class="row p-2">
    <div class="col-md-7" style="text-align:right">
        <h4 class="btn btn-light border" style="font-size:22px">
            Total Amount: <span id="totalAmount"><?= number_format($bill['total_amount'],2) ?></span>
        </h4>
        <input type="hidden" name="total_amount" id="total_amount" value="<?= $bill['total_amount'] ?>">
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control"><?= htmlspecialchars($bill['remarks']) ?></textarea>
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

<!-- Autocomplete + jQuery -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
// Prevent conflict
// var jq = jQuery.noConflict();

// function calculateTotal() {
//     let total = 0;
//     jq("#itemBody tr").each(function () {
//         let amount = parseFloat(jq(this).find(".amount").val()) || 0;
//         total += amount;
//     });

//     jq("#totalAmount").text(total.toFixed(2));
//     jq("#total_amount").val(total.toFixed(2));
// }

// jq(document).on("keyup change", ".amount, .qty", calculateTotal);

var jq = jQuery.noConflict();

function calculateTotal() {
    let total = 0;

    jq("#itemBody tr").each(function () {
        let amount = parseFloat(jq(this).find(".amount").val()) || 0;
        total += amount;
    });

    jq("#totalAmount").text(total.toFixed(2));
    jq("#total_amount").val(total.toFixed(2));
}

// AUTO UPDATE AMOUNT WHEN QTY CHANGES
jq(document).on("keyup change", ".qty", function () {
    let row = jq(this).closest("tr");

    let qty = parseFloat(row.find(".qty").val()) || 0;
    let amountInput = row.find(".amount");

    // Get old amount & stored base qty
    let oldAmount = parseFloat(amountInput.data("old-amount")) || parseFloat(amountInput.val()) || 0;
    let oldQty = parseFloat(row.find(".qty").data("old-qty")) || qty;

    if (oldQty > 0) {
        let rate = oldAmount / oldQty;
        let newAmount = rate * qty;

        amountInput.val(newAmount.toFixed(2));
    }

    calculateTotal();
});

// Store original qty & amount on page load
jq(document).ready(function () {
    jq("#itemBody tr").each(function () {
        let qty = parseFloat(jq(this).find(".qty").val()) || 0;
        let amt = parseFloat(jq(this).find(".amount").val()) || 0;

        jq(this).find(".qty").data("old-qty", qty);
        jq(this).find(".amount").data("old-amount", amt);
    });
});
</script>


<?php include('../../include/footer.php'); ?>
