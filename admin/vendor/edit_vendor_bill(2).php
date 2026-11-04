<?php
// edit_vendor_bill.php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo "Invalid bill id.";
  exit;
}

// fetch bill
$bill_q = mysqli_query($myDB, "SELECT * FROM rrsv_vendor_bills WHERE id = {$id}");
$bill = mysqli_fetch_assoc($bill_q);
if (!$bill) {
  echo "Bill not found.";
  exit;
}

// fetch items for this bill
$items_q = mysqli_query($myDB, "SELECT * FROM rrsv_vendor_bill_items WHERE bill_id = {$id} ORDER BY id ASC");

// fetch vendors for dropdown
$vendors_q = mysqli_query($myDB, "SELECT id, name FROM rrsv_vendor WHERE status='Active' ORDER BY name ASC");
?>
<link href="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>libray/jquery-ui/jquery-ui.min.css">
<script src="<?php echo BASE_URL; ?>libray/jquery-ui/jquery-ui.min.js"></script>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Vendor Bill #<?php echo htmlentities($bill['bill_no']); ?></h4>

        <form action="update_vendor_bill.php" method="POST" id="editBillForm">
          <input type="hidden" name="id" value="<?php echo (int)$bill['id']; ?>">

          <div class="row">
            <div class="col-md-4 form-group">
              <label>Vendor</label>
              <select name="vendor_id" class="form-control" required>
                <option value="">-- Select Vendor --</option>
                <?php while ($v = mysqli_fetch_assoc($vendors_q)) {
                  $sel = ($v['id'] == $bill['vendor_id']) ? 'selected' : '';
                  echo "<option value='{$v['id']}' {$sel}>" . htmlentities($v['name']) . "</option>";
                } ?>
              </select>
            </div>

            <div class="col-md-4 form-group">
              <label>Bill No</label>
              <input type="text" name="bill_no" class="form-control" value="<?php echo htmlentities($bill['bill_no']); ?>" required>
            </div>

            <div class="col-md-4 form-group">
              <label>Bill Date</label>
              <input type="date" name="bill_date" class="form-control" value="<?php echo htmlentities($bill['bill_date']); ?>" required>
            </div>
          </div>

          <br>

          <table class="table table-bordered" id="itemsTableEdit">
            <thead>
              <tr>
                <th>Item</th>
                <th style="width:100px">Qty</th>
                <th style="width:140px">Rate</th>
                <th style="width:100px">GST %</th>
                <th style="width:140px">Taxable</th>
                <th style="width:70px">#</th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <?php
              // output existing items (if none, output one empty row)
              $hasRows = false;
              mysqli_data_seek($items_q, 0);
              while ($it = mysqli_fetch_assoc($items_q)) {
                $hasRows = true;
                $iname = htmlentities($it['item_name']);
                $qty = (float)$it['qty'];
                $rate = (float)$it['rate'];
                $gst = 0;//isset($it['qty']) ? (float)$it['qty'] : 0.00;
                $taxable = number_format($it['amount'], 2, '.', '');
                echo "<tr>
                  <td><input type='text' name='item_name[]' class='form-control itemAuto' value=\"{$iname}\" required></td>
                  <td><input type='number' step='0.01' name='qty[]' class='form-control qty' value=\"{$qty}\" required></td>
                  <td><input type='number' step='0.01' name='rate[]' class='form-control rate' value=\"{$rate}\" required></td>
                  <td><input type='number' step='0.01' name='gst_rate[]' class='form-control gst_rate' value=\"{$gst}\" required></td>
                  <td><input type='text' name='amount[]' class='form-control amount' value=\"{$taxable}\" readonly></td>
                  <td><button type='button' class='btn btn-danger btn-sm removeRow'>-</button></td>
                </tr>";
              }

              if (!$hasRows) {
                // empty row
                echo "<tr>
                  <td><input type='text' name='item_name[]' class='form-control itemAuto' required></td>
                  <td><input type='number' step='0.01' name='qty[]' class='form-control qty' value='1' required></td>
                  <td><input type='number' step='0.01' name='rate[]' class='form-control rate' value='0.00' required></td>
                  <td><input type='number' step='0.01' name='gst_rate[]' class='form-control gst_rate' value='0.00' required></td>
                  <td><input type='text' name='amount[]' class='form-control amount' value='0.00' readonly></td>
                  <td><button type='button' class='btn btn-danger btn-sm removeRow'>-</button></td>
                </tr>";
              }
              ?>
            </tbody>
          </table>

          <button type="button" id="addItemRow" class="btn btn-success">+ Add Item</button>

          <br><br>

          <div style="text-align:right;">
            <h4 class="btn btn-light border">Total: ₹ <span id="grandTotal"><?php echo number_format((float)$bill['total_amount'],2); ?></span></h4>
            <input type="hidden" name="total_amount" id="total_amount" value="<?php echo number_format((float)$bill['total_amount'],2,'.',''); ?>">
          </div>

          <br>
 <div class="col-md-8 form-group">
          <label>Remarks</label>
          <textarea name="remarks" class="form-control"><?php echo htmlentities($bill['remarks']); ?></textarea>
 </div>
          <br>
          <button class="btn btn-primary">Update Bill</button>
          <a href="manage_vendor_bill.php" class="btn btn-light">Cancel</a>

        </form>

      </div>
    </div>
  </div>
</div>

<?php include('../../include/footer.php'); ?>

<script>
/* Autocomplete item search using vendor_items table (items_ajax.php)
   items_ajax.php should return JSON like:
   [{id:1, label:"Paracetamol 500mg", value:"Paracetamol 500mg", rate:50.00, gst:12.00}, ...]
*/
function calculateTotals() {
  let grand = 0.00;
  $('#itemsBody tr').each(function() {
    let qty = parseFloat($(this).find('.qty').val()) || 0;
    let rate = parseFloat($(this).find('.rate').val()) || 0;
    let gst = parseFloat($(this).find('.gst_rate').val()) || 0;
    let taxable = qty * rate;
    $(this).find('.amount').val(taxable.toFixed(2));
    // For display total we include GST (cgst+sgst)
    let gstAmt = taxable * (gst/100);
    let lineTotal = taxable + gstAmt;
    grand += lineTotal;
  });
  $('#grandTotal').text(grand.toFixed(2));
  $('#total_amount').val(grand.toFixed(2));
}

$(document).ready(function() {
  // attach calculate on change
  $(document).on('keyup change', '.qty, .rate, .gst_rate', calculateTotals);
  calculateTotals();

  // add empty row
  $('#addItemRow').click(function(){
    $('#itemsBody').append(`
      <tr>
        <td><input type='text' name='item_name[]' class='form-control itemAuto' required></td>
        <td><input type='number' step='0.01' name='qty[]' class='form-control qty' value='1' required></td>
        <td><input type='number' step='0.01' name='rate[]' class='form-control rate' value='0.00' required></td>
        <td><input type='number' step='0.01' name='gst_rate[]' class='form-control gst_rate' value='0.00' required></td>
        <td><input type='text' name='amount[]' class='form-control amount' value='0.00' readonly></td>
        <td><button type='button' class='btn btn-danger btn-sm removeRow'>-</button></td>
      </tr>
    `);
  });

  // remove row
  $(document).on('click', '.removeRow', function(){
    $(this).closest('tr').remove();
    calculateTotals();
  });

  // item autocomplete using items_ajax.php
  $(document).on('focus', '.itemAuto', function(){
    $(this).autocomplete({
      source: function(request, response) {
        $.getJSON('items_ajax.php', { term: request.term }, response);
      },
      minLength: 1,
      select: function(event, ui) {
        // fill rate and gst in the same row
        let row = $(this).closest('tr');
        row.find('.rate').val(ui.item.rate);
        row.find('.gst_rate').val(ui.item.gst);
        calculateTotals();
      }
    });
  });

}); // document ready
</script>
