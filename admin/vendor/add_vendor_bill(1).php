
<?php
include('../../include/header.php');
include('../../include/dbcon.php'); 
// Get last bill no
$lastBill = mysqli_query($myDB, "SELECT id FROM rrsv_expence ORDER BY id DESC LIMIT 1");
$nextBillNo = 1;

if ($lastBill && mysqli_num_rows($lastBill) > 0) {
    $row = mysqli_fetch_assoc($lastBill);

    // If bill_no is numeric (e.g. 1, 2, 3…)
    if (is_numeric($row['id'])) {
        $nextBillNo = $row['id'] + 1;
    }
}
?>
<div class="main-panel">
<div class="content-wrapper">


<div class="card">
<div class="card-body">
<h4 class="card-title">Add Vendor Bill</h4>


        <form action="save_vendor_bill.php" method="POST" id="billForm" enctype="multipart/form-data">
            
            
                <div class="row">
                    <div class="col-md-4 form-group">
                    <label>Vendor</label>
                    <select name="vendor_id" class="form-control" required>
                    <option value="">Select Vendor</option>
                    <?php
                    $sql = mysqli_query($myDB, "SELECT * FROM rrsv_vendor WHERE status='Active'");
                    while ($v = mysqli_fetch_assoc($sql)) {
                    echo "<option value='{$v['id']}'>{$v['name']}</option>";
                    }
                    ?>
                    </select>
                    </div>
                
                
                    <div class="col-md-4 form-group">
                    <label>Bill No</label>
                     <input type="text" name="bill_no" class="form-control" value="<?php echo $nextBillNo; ?>" readonly>
                    </div>
                    
                    
                    <div class="col-md-4 form-group">
                    <label>Bill Date</label>
                    <input type="date" name="bill_date" id="bill_date" class="form-control" required>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-md-4 form-group">
                          <label for="exampleInputUsername1">Session of Expence</label>
                            <select name="session" class="form-control" id="session">
                          <option value="">-Select a Session -</option>
                          <?php
                          for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
                          }
                    
                          ?>
                        </select>                     
                        <span id="error5" style="color:red;"></span>
                    </div>
                
                
                    <div class="col-md-4 form-group">
                        <label>Vouture</label>
                         <input type="file" name="vouture_file" class="form-control" value="" >
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label>Details</label>
                        <textarea name="remarks" class="form-control"></textarea>
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
                <tr>
                <td>
                    <input type="hidden" name="item_id[]" class="item_id">
                        <input type="text" name="item_name[]" class="form-control item_name" required>
                </td>
                
                <td><input type="number" name="qty[]" class="form-control qty" required></td>
                <td><input type="text"  name="rate[]" class="form-control rate" required></td>
                <td><input type="text" name="amount[]" class="form-control amount" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
                </tr>
                </tbody>
            </table>
            <div class="row p-2">
                <div class="col-md-4 form-group">
                <!--<button type="button" class="btn btn-success" id="addRow">+ Add Item</button>-->
                </div>
                <div class="col-md-7 form-group" style="text-align:right;">
                <h4 class="btn btn-light border " style="font-weight:700px; font-size:24px;">Total Amount: <span id="totalAmount">0.00</span></h4>
                <input type="hidden" name="total_amount" id="total_amount">
                </div>
            </div>
            
            <br><br>
            <div class="row">
                <div class="col-md-4 form-group">
                <button class="btn btn-primary">Save Bill</button>
               </div>
            </div>
            
            </form>
</div>
</div>


</div>
</div>


<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>
<!-- jQuery UI CSS (keep only one copy) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- Load a clean jQuery and jQuery UI for autocomplete, then noConflict so it doesn't clash with page scripts -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
var jqx = jQuery.noConflict(true);

jqx(function () {

  // Delegated focus binding so dynamic rows work
  jqx(document).on("focus", ".item_name", function () {
    var $input = jqx(this);

    // prevent reinitializing autocomplete on same input
    if ($input.data("ui-autocomplete")) {
      return;
    }

    $input.autocomplete({
      source: "search_expense.php", // <-- adjust path if file lives elsewhere
      minLength: 1,
      delay: 150,
      select: function (event, ui) {
        var $row = $input.closest("tr");
        $row.find(".item_id").val(ui.item.item_id || "");
        $input.val(ui.item.label || ui.item.value || "");
        //$row.find(".rate").val(parseFloat(ui.item.rate).toFixed(2)); // auto rate
        $row.find(".qty").focus();
         calculateAmount();
        return false; // prevent default value overwrite if needed
      }
    });
  });

});


</script>

<script>
function calculateAmount() {
let total = 0;
$("#itemBody tr").each(function () {
let qty = parseFloat($(this).find(".qty").val()) || 0;
let rate = parseFloat($(this).find(".rate").val()) || 0;
let amount = qty * rate;
$(this).find(".amount").val(amount.toFixed(2));
total += amount;
});
$("#totalAmount").text(total.toFixed(2));
$("#total_amount").val(total.toFixed(2));
}
$(document).on("keyup change", ".qty, .rate", calculateAmount);
$("#addRow").click(function () {
$("#itemBody").append(`
<tr>
<td> <input type="hidden" name="item_id[]" class="item_id">
<input type="text" name="item_name[]" class="form-control item_name" autocomplete="off" required></td>
<td><input type="number" name="qty[]" class="form-control qty" required></td>
<td><input type="number" step="0.01" name="rate[]" class="form-control rate " required></td>
<td><input type="text" name="amount[]" class="form-control amount" readonly></td>
<td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
</tr>
`);
});
$(document).on("click", ".removeRow", function () {
$(this).closest("tr").remove();
calculateAmount();
});

document.addEventListener('DOMContentLoaded', function () {
    let today = new Date().toISOString().split('T')[0];
    let billDate = document.getElementById('bill_date');

    billDate.value = today;  // auto-select today
    billDate.setAttribute('min', today); // disable backdate
});
</script>


<?php include('../../include/footer.php'); ?>