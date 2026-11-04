<?php
include('include/header.php');
include('include/dbcon.php');
?>


<div class="main-panel">
  <div class="content-wrapper">



    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"> <a href='add_manage_stock.php'><button type="button"
                class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Insert
                Stock</button></a></h4>

        </div>

      </div>

      <div class="card">
        <div class="card-body">
          <p class="card-description">
          </p>
          <form id="form_submit" class="form-sample" action="stock_report.php?" method="GET">
            <input type="hidden" name="token" id="token" value="Hk4A3ehHsjhoaT6BlJ4E7MnYx8GQOXd2">
            <div class="row">

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Class
                    </h4>
                    <select name="class_id" id="class_id" class="form-control minput">
                      <option value="">-Choose-</option>
                      <?php
                      $id = 0;
                      $sql = "select * from rrsv_class order by id";
                      $res = mysqli_query($myDB, $sql);
                      while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                        ?>
                        <option value="<?php echo $obj['id']; ?>">
                          <?php echo $obj['class_name']; ?>
                        </option>
                        <?php
                      }
                      ?>
                    </select>
                    <span id="class_id_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Session
                    </h4>
                    <select name="session" class="form-control" id="session">
                      <option value="">-Select a Session -</option>
                      <?php
                      for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
                      }

                      ?>
                    </select>
                    <span id="session_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Stock Type
                    </h4>
                    <select name="stock_type" id="stock_type" class="form-control minput">
                      <option value="">-Choose -</option>
                      <option value="BOOK">BOOK</option>
                      <option value="COPY">COPY</option>
                      <option value="OTHERS">OTHERS</option>
                    </select>
                    <span id="stock_type_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Stock Name
                    </h4>
                    <select name="stock_master_id" id="stock_master_id" class="form-control minput">
                      <option value="">-Choose-</option>

                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Publishers
                    </h4>
                    <select name="publishers_id" id="publishers_id" class="form-control minput">
                      <option value="">-Choose-</option>
                      <?php
                      $id = 0;
                      $sql = "select * from rrsv_publishers order by id";
                      $res = mysqli_query($myDB, $sql);
                      while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                        ?>
                        <option value="<?php echo $obj['id']; ?>">
                          <?php echo $obj['publishers_name']; ?>
                        </option>
                        <?php
                      }
                      ?>
                    </select>
                    <span id="publishers_id_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      From Date
                    </h4>
                    <input type="date" class="form-control" id="from_date" name="from_date">
                    <span id="from_date_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      To Date
                    </h4>
                    <input type="date" class="form-control" id="to_date" name="to_date">
                    <span id="to_date_error" class="error" style="color:red;"></span>
                  </div>

                </div>
              </div>

              <div class="col-md-3 dis_month">
                <h4 class="card-title">
                  &nbsp;
                </h4>
                <button type="submit" id="search_stock" class="btn btn-primary">Search</button>
                <button type="submit" id="search_stock_return" class="btn btn-info">Search Stock Return</button>
              


              </div>




            </div>
          </form>
          <button type="button" onclick="location.reload();" class="btn btn-dark btn-rounded btn-icon"><i
              class="mdi mdi-refresh"></i></button>
          <button id="form_reset" class="btn btn-light">Cancel</button>
          <div id="alert"></div>

        </div>
      </div>
    </div>


  </div>





  <?php
  include('include/footer.php');
  ?>

<script>
$(document).ready(function () {
  var clickedButton = null;
  var originalAction = $('#form_submit').attr('action'); // stock_report.php

  // Track button clicks
  $('#search_stock').click(function () {
    clickedButton = 'search';
  });
  $('#search_stock_return').click(function () {
    clickedButton = 'return';
  });

  // --- AJAX dropdown fetch ---
  function fetchData() {
    var stock_type = $('#stock_type').val();
    var session = $('#session').val();
    var class_name = $('#class_id option:selected').text().trim();

    $.ajax({
      type: "POST",
      url: "stock_ajax_get_name.php",
      data: { stock_type, session, class_name },
      success: function (data) {
        var response = JSON.parse(data);
        $("#stock_master_id").empty().append("<option value=''>--choose--</option>");
        for (var i = 0; i < response.length; i++) {
          var id = response[i]['id'];
          var name = response[i]['name'];
          $("#stock_master_id").append("<option value='" + id + "'>" + name + "</option>");
        }
      }
    });
  }
  $('#class_id, #stock_type, #session').on('change', fetchData);

  // --- Form validation + submit ---
  $('form').submit(function (e) {
    e.preventDefault();
    $('.error').html('');

    var stock_type = $('#stock_type option:selected').text().trim();
    var class_id = $('#class_id option:selected').val();

    if ((stock_type === "BOOK" || stock_type === "COPY") && !class_id) {
      $("#class_id_error").html("<b>Please select a class.</b>");
      return false;
    }
    if ((stock_type != "BOOK" && stock_type != "COPY") && class_id) {
      $("#stock_type_error").html("<b>When you choose class, please select stock type Book or Copy.</b>");
      return false;
    }

    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    if (from_date && !to_date) {
      $("#to_date_error").html("<b>Please select To Date.</b>");
      return false;
    }
    if (!from_date && to_date) {
      $("#from_date_error").html("<b>Please select From Date.</b>");
      return false;
    }

    // Change form action depending on button
    if (clickedButton === 'return') {
      $('#form_submit').attr('action', 'stock_return_report.php');
    } else {
      $('#form_submit').attr('action', originalAction); // stock_report.php
    }

    // Submit the form normally
    this.submit();
  });
});
</script>
