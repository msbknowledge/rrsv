<?php
include('include/header.php');
include('include/dbcon.php');
?>
<style>
  #stockDetailsContainer {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    /* 3 columns */
    gap: 1rem;
    /* Space between elements */
    align-items: center;
    max-width: 900px;
    margin: 0 auto;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  h4 {
    margin-bottom: 0.5rem;
    font-size: 1rem;
    font-weight: bold;
    color: #333;
  }

  textarea,
  input {
    padding: 0.5rem;
    font-size: 1rem;
  }

  input[type="text"] {
    background-color: #f7f7f7;
  }

  input[type="number"]:focus,
  textarea:focus {
    border-color: #007bff;
  }

  textarea {
    resize: vertical;
    min-height: 100px;
  }
</style>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">
            <a href='add_manage_stock.php'>
              <button type="button" class="btn btn-primary btn-rounded btn-fw">
                <i class="mdi mdi-comment-plus-outline"></i> Insert Stock
              </button>
            </a>
          </h4>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <form id="form_submit" class="form-sample">
            <input type="hidden" name="token" id="token" value="Hk4A3ehHsjhoaT6BlJ4E7MnYx8GQOXd2">

            <div class="row">
              <!-- Select Class -->
              <div class="col-md-3">
                <div class="form-group">
                  <h4 class="card-title">Select Class</h4>
                  <select name="class_id" id="class_id" class="form-control">
                    <option value="">-Choose-</option>
                    <?php
                    $sql = "SELECT * FROM rrsv_class ORDER BY id";
                    $res = mysqli_query($myDB, $sql);
                    while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                      echo "<option value='{$obj['id']}'>{$obj['class_name']}</option>";
                    }
                    ?>
                  </select>
                  <span id="class_id_error" class="error" style="color:red;"></span>
                </div>
              </div>

              <!-- Select Session -->
              <div class="col-md-3">
                <div class="form-group">
                  <h4 class="card-title">Select Session</h4>
                  <select name="session" id="session" class="form-control">
                    <option value="">-Choose-</option>
                    <?php
                    for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                      echo "<option value='$i'>$i</option>";
                    }
                    ?>
                  </select>
                  <span id="session_error" class="error" style="color:red;"></span>
                </div>
              </div>

              <!-- Select Stock Type -->
              <div class="col-md-3">
                <div class="form-group">
                  <h4 class="card-title">Select Stock Type</h4>
                  <select name="stock_type" id="stock_type" class="form-control">
                    <option value="">-Choose-</option>
                    <option value="BOOK">BOOK</option>
                    <option value="COPY">COPY</option>
                    <option value="OTHERS">OTHERS</option>
                  </select>
                  <span id="stock_type_error" class="error" style="color:red;"></span>
                </div>
              </div>

              <!-- StockReturn Button -->
              <div class="col-md-3">
                <h4 class="card-title">&nbsp;</h4>
                <button type="button" id="stockReturnBtn" class="btn btn-primary">StockReturn</button>
              </div>
            </div>

            <div id="stockReturnContainer" class="mt-4"></div>
            <div id="stockDetailsContainer" class="mt-4"></div>

            <div class="mt-4">
              <button type="submit" id="submitButton" class="btn btn-primary" disabled>Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include('include/footer.php');
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    // Step 1: StockReturn button click event
    $('#stockReturnBtn').on('click', function () {
      if (!validateForm()) return;

      var formData = $('#form_submit').serialize();

      $.ajax({
        type: "POST",
        url: "get_stock_data.php",
        data: formData,
        success: function (response) {
          var stockData = JSON.parse(response);

          var dropdown = '<select name="return_stock_name" id="return_stock_name" class="form-control">';
          dropdown += '<option value="">--Choose Stock--</option>';
          stockData.forEach(function (item) {
            dropdown += `<option value="${item.id}">${item.name}</option>`;
          });
          dropdown += '</select>';

          $('#stockReturnContainer').html(dropdown);
        }
      });
    });

    // Fetch stock details on dropdown change
    $(document).on('change', '#return_stock_name', function () {
      var stockId = $(this).val();
      if (!stockId) {
        $('#submitButton').prop('disabled', true);
        return;
      } else {
        $('#submitButton').prop('disabled', false);
      }


      $.ajax({
        type: "POST",
        url: "get_stock_details.php",
        data: { stockId: stockId },
        success: function (response) {
          var stockDetails = JSON.parse(response);
          var purchaseValue = stockDetails.purchase_value_per_unit;
          var currentStockQty = stockDetails.stock_after_sale;

          $('#stockDetailsContainer').html(`
    <div class="form-group">
      <h4>Current stock Quentity</h4>
      <div> 
        <span id="currentStockQty">${currentStockQty}</span>
      </div>
    </div>
    <div class="form-group">
      <h4>Purchase Value Per Unit</h4>
      <div> 
        <span id="purchaseValue">${purchaseValue}</span>
      </div>
    </div>

    <div class="form-group">
      <h4>Return Quantity</h4>
      <input type="number" id="return_qty" placeholder="Return Quantity" class="form-control mt-2" />
    </div>

    <div class="form-group">
      <h4>Return Amount</h4>
      <input type="text" id="return_amount" placeholder="Return Amount" readonly class="form-control mt-2" />
    </div>

    <div class="form-group">
      <h4>Damage Quantity</h4>
      <input type="number" id="damage_qty" placeholder="Damage Quantity" class="form-control mt-2" />
    </div>

    <div class="form-group">
      <h4>Damage Amount</h4>
      <input type="text" class="form-control mt-2" id="damage_amount" placeholder="Damage Amount" name="damage_amount">
    </div>

    <div class="form-group">
      <h4>Total Amount</h4>
      <input type="text" class="form-control" id="total_amount" name="total_amount" placeholder="Total Amount" readonly>
    </div>

    <div class="form-group">
      <h4>Damage Remarks</h4>
      <textarea id="damage_remarks" placeholder="Damage Remarks" class="form-control mt-2"></textarea>
    </div>
  `);
        }
      });
    });
    function makeAllValueNull() {
      $('#return_qty').val(null);
      $('#return_amount').val(null);
      $('#damage_qty').val(null);
      $('#damage_remarks').val(null);
      $('#total_amount').val(null);
      $('#damage_amount').val(null);
    }
    // // Update return quantity when damage quantity changes
    $(document).on('input', '#damage_qty, #return_qty', function () {
      const purchaseValuePerUnit = parseFloat($('#purchaseValue').text()) || 0;
      const currentStockQty = parseFloat($('#currentStockQty').text()) || 0;
      const damageQty = parseInt($('#damage_qty').val()) || 0;
      //alert(damageQty);
      const totalQty = parseInt($('#return_qty').val()) || 0;

      if (totalQty > currentStockQty) {
        alert('return qty should not geter then current Stock Qty.');
        makeAllValueNull();
        return false;
      }
      if (damageQty > totalQty) {
        alert('damage qty should not geter then return Qty.');
        makeAllValueNull();
        return false;
      }

      // Calculate the updated return quantity
      const updatedReturnQty = totalQty - damageQty;
      $('#return_qty').val(updatedReturnQty >= 0 ? updatedReturnQty : 0);

      // Calculate the updated return amount
      const returnAmount = updatedReturnQty * purchaseValuePerUnit;
      $('#return_amount').val(returnAmount.toFixed(2));

      // Calculate and set damage amount
      const damageAmount = damageQty * purchaseValuePerUnit;
      $('#damage_amount').val(damageAmount.toFixed(2));

      // Calculate total amount (Return Amount + Damage Amount)
      const totalAmount = returnAmount + damageAmount;
      $('#total_amount').val(totalAmount.toFixed(2));

    });

    // Form validation
    function validateForm() {
      let isValid = true;
      $('.error').html('');

      if (!$('#class_id').val()) {
        $('#class_id_error').text('Class is required.');
        isValid = false;
      }

      if (!$('#session').val()) {
        $('#session_error').text('Session is required.');
        isValid = false;
      }

      if (!$('#stock_type').val()) {
        $('#stock_type_error').text('Stock type is required.');
        isValid = false;
      }

      return isValid;
    }

    // Step 9: Submit data to update the stock table
    $('#form_submit').on('submit', function (e) {
      e.preventDefault();
      const returnQty = parseInt($('#return_qty').val()) || 0;
      if (returnQty == null || returnQty <= 0) {
        alert("please put some return quantity!");
        return false;
      }
      var returnAmount = $('#return_amount').val();
      var damageQty = parseInt($('#damage_qty').val()) || 0;
      var damageRemarks = $('#damage_remarks').val();
      var totalAmount = $('#total_amount').val();
      var damageAmount = $('#damage_amount').val();
      var stockId = $('#return_stock_name').val();
      let activityStatus = 1;
      if (parseInt($('#currentStockQty').text()) === (damageQty + returnQty)) {
        activityStatus = 0;
      }
      if (!validateFinalForm(returnQty, returnAmount, damageQty)) return;

      $.ajax({
        type: "POST",
        url: "update_stock.php", // PHP page to update stock data
        data: {
          stockId: stockId,
          returnQty: returnQty,
          returnAmount: returnAmount,
          damageQty: damageQty,
          damageAmount: damageAmount,
          totalAmount: totalAmount,
          damageRemarks: damageRemarks,
          activityStatus: activityStatus
        },
        success: function (response) {
          alert('Stock updated successfully!');
        }
      });
    });

    function validateFinalForm(returnQty, returnAmount, damageQty) {
      if (!returnQty || !returnAmount) {
        alert('Return quantity and amount are required!');
        return false;
      }
      // if (damageQty > returnQty) {
      //   alert('Damage quantity exceeds return quantity!');
      //   return false;
      // }
      return true;
    }

  });
</script>