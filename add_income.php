<?php
include('include/header.php');
include('include/dbcon.php');

if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
  $sql = "select * from  rrsv_income where id=$id";
  $res = mysqli_query($myDB, $sql);
  $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
}
?>
<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">



    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"> <a href='manage_income.php'><button class="btn btn-light"><i
                  class="mdi mdi-arrow-left"></i>Back</button></a></h4>

        </div>

      </div>

      <div class="card">
        <div class="card-body">
          <p class="card-description">
          </p>
          <form id="form_submit" class="form-sample" action="stock_report.php?" method="GET">
            <input type="hidden" name="token" id="token" value="Hk4A3ehHsjhoaT6BlJ4E7MnYx8GQOXd2">
            <div class="row">
              <div id="alert"></div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Type
                    </h4>
                    <select name="income_type" id="income_type" class="form-control minput">
                      <option value="with_stock">With Stock</option>
                      <option value="without_stock">Without Stock</option>
                    </select>
                    <span id="income_type_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 with_stock_class">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Class
                    </h4>
                    <select name="class_id" id="class_id" class="form-control minput">
                      <option value="0">-Choose-</option>
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

              <div class="col-md-3">
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


              <div class="col-md-3 with_stock_class">
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

              <div class="col-md-3 with_stock_class">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Select Stock Name
                    </h4>
                    <select name="stock_master_id" id="stock_master_id" class="form-control minput">
                      <option value="0">-Choose-</option>

                    </select>
                    <span id="stock_master_id_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>


              <div class="col-md-3 without_stock_class" style="display: none;">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Income Head
                    </h4>
                    <select name="income_name" class="form-control" id="income_name">
                      <option value="">--Select Income Head--</option>
                      <?php
                      // $id=0;
                      $sql = "select * from rrsv_income_head order by id";
                      $res = mysqli_query($myDB, $sql);

                      while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                        ?>
                        <option value="<?php echo $obj['income']; ?>" <?php if (trim($rows['income_name'] == $obj['income']))
                             echo "selected"; ?>>
                          <?php echo $obj['income']; ?>

                        </option>
                        <?php
                      }
                      ?>
                    </select>
                    <span id="income_name_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Income Amount
                    </h4>
                    <input type="number" class="form-control" id="amount" placeholder="Income Amount" name="amount"
                      value=''>
                    <span id="amount_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Date of Income
                    </h4>
                    <input type="date" class="form-control" id="d_o_i" placeholder="Date of Income" name="d_o_i"
                      value=''>
                    <span id="d_o_i_error" class="error" style="color:red;"></span>
                  </div>
                </div>
              </div>

              <div class="col-md-6 dis_month">
                <div class="form-group row">
                  <div class="col-sm-12">
                    <h4 class="card-title">
                      Details
                    </h4>
                    <textarea id="income_desc" name="income_desc" class="form-control" rows="2" cols="50">

                    </textarea>
                  </div>
                </div>
              </div>
              <br><br>

              <div id="passwordContainer"></div> <!-- Password input will be added here -->
              <br>
              <div class="col-md-3 dis_month">
                <h4 class="card-title">
                  &nbsp;
                </h4>
                <button type="submit" class="btn btn-primary">Submit</button>

              </div>




            </div>
          </form>
          <button type="submit" onclick="location.reload();" class="btn btn-dark btn-rounded btn-icon"><i
              class="mdi mdi-refresh"></i></button>



        </div>
      </div>
    </div>


  </div>
  <?php
  include('include/footer.php');
  ?>

  <script src="include/handelDateChange.js"></script>
  <script>
    $(document).ready(function () {
      $('#income_type').on('change', function () {
        let selectedValue = $(this).val();
        //alert(selectedValue);
        if (selectedValue === 'with_stock') {
          $('.with_stock_class').val("");
          $('.with_stock_class').show();
          $('.without_stock_class').hide();

        } else {
          $('.without_stock_class').show();
          $('.with_stock_class').hide();
        }
      });
    });
    $(document).ready(function () {
      // Define the AJAX function
      function fetchData() {
        //alert(56);
        // Get selected values from both dropdowns
        var stock_type = $('#stock_type').val();
        var session = $('#session').val();
        var class_name = $('#class_id option:selected').text().trim();

        // Make the AJAX request
        $.ajax({
          type: "POST",
          url: "stock_ajax_get_name.php",
          data: { stock_type, session, class_name },
          success: function (data) {
            var response = JSON.parse(data);
            $("#stock_master_id").empty();
            $("#stock_master_id").append("<option value=''>--choose--</option>");
            for (var i = 0; i < response.length; i++) {
              var id = response[i]['id'];
              var name = response[i]['name'];
              $("#stock_master_id").append("<option value='" + id + "'>" + name + "</option>");
            }
          }
        });

      }

      // Attach the fetchData function to the change event for both dropdowns 
      $('#class_id').on('change', fetchData);
      $('#stock_type').on('change', fetchData);
      $('#session').on('change', fetchData);
    });

    $(document).ready(function () {
      $('#stock_master_id').on('change', function () {
        validate_stock();
      });
    });
    function validate_stock() {
      //alert("stock_master_id-- " + $('#stock_master_id').val() + "stock_type-- " + $('#stock_type').val() + "session-- " + $('#session').val() + "class_id-- " + $('#class_id').val());
      var stock_master_id = $('#stock_master_id').val();
      var stock_type = $('#stock_type').val();
      var session = $('#session').val();
      var class_id = $('#class_id').val();

      $.ajax({
        type: "POST",
        url: "validate_stock.php",
        data: { stock_type, session, class_id, stock_master_id },
        success: function (data) {
          //alert(data);
          if (data === "false") {
            $('#stock_master_id').val("");
          }

        }
      });


    }

    // $(document).ready(function () {
    //   $("#d_o_i").change(function () {
    //     var selectedDate = new Date($(this).val()); // Get selected date
    //     var currentDate = new Date();
    //     currentDate.setHours(0, 0, 0, 0); // Normalize current date to remove time
    //     // Check if selected date is in the past
    //     if (selectedDate < currentDate) {
    //       $("#passwordContainer").empty();
    //       // Create HTML elements dynamically using jQuery
    //       var passwordDiv = $("<div>").addClass("col-md-3 dis_month");
    //       var formGroup = $("<div>").addClass("form-group row");
    //       var colDiv = $("<div>").addClass("col-sm-12");
    //       var title = $("<h4>").addClass("card-title").text("Password (when backdated entry needs password)");
    //       var passwordInput = $("<input>").attr({
    //         type: "text",
    //         id: "password",
    //         name: "password"
    //       }).addClass("form-control");
    //       var errorSpan = $("<span>").attr("id", "d_o_i_error").addClass("error").css("color", "red");
    //       // Append elements in the correct structure
    //       colDiv.append(title, passwordInput, errorSpan);
    //       formGroup.append(colDiv);
    //       passwordDiv.append(formGroup);

    //       // Append the final structure to the container
    //       $("#passwordContainer").append(passwordDiv);
    //     } else {
    //       $("#passwordContainer").empty();
    //     }
    //   });
    // });



    $(document).ready(function () {
      handleDateChange("d_o_i", "passwordContainer", "col-md-3");
    });


    $('form').submit(function (e) {
      //alert(33);
      $('.error').html('');
      e.preventDefault();
      var income_type = $('#income_type').val();
      var income_name = $('#income_name').val();
      var amount = $('#amount').val();
      var d_o_i = $('#d_o_i').val();
      var income_desc = $('#income_desc').val();
        var session = $('#session').val();

      if (income_type === 'with_stock') {
        var stock_type = $('#stock_type').val();
        
        var class_id = $('#class_id').val();
        var stock_master_id = $('#stock_master_id').val();

        if (stock_type == '') {
          $('#stock_type_error').html('Please Enter Income Type.');
          status = false;
        }
        
        if (class_id == 0) {
          $('#class_id_error').html('Please Enter  Class.');
          status = false;
        }
        if (stock_master_id == 0) {
          $('#stock_master_id_error').html('Please Enter  stock name.');
          status = false;
        }
      } else {
        var income_name = $('#income_name').val();
        if (income_name == "") {
          $('#income_name_error').html('Please Enter  Name of Income.');
        }
        status = false;
      }

      var status = true;
      if (income_type == '') {
        $('#income_type_error').html('Please Enter Income Type.');
        status = false;
      }
      if (amount == '') {
        $('#amount_error').html('Please Enter Income Amount.');
        status = false;
      }
      if (d_o_i == '') {
        $('#d_o_i_error').html('Please Enter  Date of Income.');
        status = false;
      }
      if (session == '') {
          $('#session_error').html('Please Enter session.');
          status = false;
        }

      if (status === false) {
        return false;
      }
      if (passwordValidate() === false) {
        return false;
      }
      ajax_fun();


      // if ($("#password").length) {
      //   //alert(11);
      //   var passwordValue = $("#password").val();

      //   if (passwordValue === "") {
      //     alert("Password is required!");
      //     return false; // Prevent further execution
      //   }
      //   if (passwordValue === "abcd") {
      //     ajax_fun();
      //   } else {
      //     alert("password did not match!");
      //     return false;
      //   }
      // } else {
      //   ajax_fun();
      // }


    });


    function ajax_fun() {

      $.ajax({
        type: "POST",
        url: "add_income_post.php",
        data: $('form').serialize(),
        success: function (val) {
          if (val == 0) {
            $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Income Saved Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            $('#error').html('');
            $('#income_name').val("");
            $('#amount').val("");
            $('#d_o_i').val("");
            $('#income_desc').val("");
          }


        }
      });

    }    
  </script>