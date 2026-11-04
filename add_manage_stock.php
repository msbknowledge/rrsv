<?php
include('include/header.php');
include('include/dbcon.php');

$host_name = $_SERVER['HTTP_HOST'];


$id = 0;
$full_name = "";
$fathers_name = "";
$mothers_name = "";
$gender = "";
$d_o_b = "";
$pin = "";
$address = "";
$email = "";
$designation = "";
$qualification = "";
$monthly_salary = "";
$medical = "";
$aadhar_no = "";
$pan_no = "";
$bank_account_no = "";
$bank_brunch_name = "";
$bank_ifsc = "";
$image = "";
$tech_phone_no = "";

if (isset($_GET['eId']) && !empty($_GET['eId'])) {
    include('include/dbcon.php');
    $sql = "select * from rrsv_teacher where id='" . (int) $_GET['eId'] . "'";
    $res = mysqli_query($myDB, $sql);
    $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
    // print_r($rows);
    // die();
    // Array ( [id] => 10 [full_name] => mhmh [fathers_name] => hkhk [mothers_name] => jhg [gender] => hh [d_o_b] => 2022-03-02 [pin] => 3 [address] => gfhgfh [email] => ghg [designation] => ryt [qualification] => 54 [monthly_salary] => 1 [cl] => 1 [medical] => 1 [aadhar_no] => hg [pan_no] => hfh [bank_account_no] => 1 [bank_brunch_name] => 1 [bank_ifsc] => 1 [image] => 22 [status] => active )
    $id = $rows['id'];
    $full_name = $rows['full_name'];
    $fathers_name = $rows['fathers_name'];
    $mothers_name = $rows['mothers_name'];
    $gender = $rows['gender'];
    $d_o_b = $rows['d_o_b'];
    $pin = $rows['pin'];
    $address = $rows['address'];
    $email = $rows['email'];
    $designation = $rows['designation'];
    $qualification = $rows['qualification'];
    $monthly_salary = $rows['monthly_salary'];
    $medical = $rows['medical'];
    $aadhar_no = $rows['aadhar_no'];
    $pan_no = $rows['pan_no'];
    $bank_account_no = $rows['bank_account_no'];
    $bank_brunch_name = $rows['bank_brunch_name'];
    $bank_ifsc = $rows['bank_ifsc'];
    $image = $rows['image'];
    $tech_phone_no = $rows['tech_phone_no'];


}


?>


<div class="main-panel">
    <div class="content-wrapper">



        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='stock.php'><button type="button"
                                class="btn btn-dark btn-rounded btn-icon"><i
                                    class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="card-description">
                    </p>
                    <form id="form_submit" class="form-sample" action="add_teachers_post.php?" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id" value="<?= $id ?>" />
                        <input type="hidden" name="token" value="<?php echo $token; ?>" />
                        <h4 class="card-title">Add Stock</h4>
                        <div class="row">
                            <div class="col-md-4" id="select_class_div">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Select Class
                                    </h4>
                                    <div class="col-sm-9">
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
                                        <span id="gender_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Select Session
                                    </h4>
                                    <div class="col-sm-9">
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
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <div class="col-sm-9">
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
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">

                                    <div class="col-sm-9">
                                        <h4 class="card-title">
                                            Stock Name
                                        </h4>
                                        <select name="stock_master_id" id="stock_master_id" class="form-control minput">
                                            <option value="">-Choose-</option>
                                        </select>
                                        <span id="stock_master_id_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Select Publishers
                                    </h4>
                                    <div class="col-sm-9">
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
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Quantity
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="qty" id="qty" class="form-control" placeholder="qty"
                                            value="<?= $qty ?>" />
                                        <span id="qty_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        MRP
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="mrp" id="mrp" step="0.01" class="form-control"
                                            placeholder="mrp" value="<?= $mrp ?>" />
                                        <span id="mrp_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Total
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="total" id="total" class="form-control"
                                            placeholder="total" value="<?= $total ?>" readonly />
                                        <span id="total_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Comission Prectantage
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="comission_prectantage" id="comission_prectantage"
                                            class="form-control" step="0.01" placeholder="comission_prectantage"
                                            value="<?= $comission_prectantage ?>" />
                                        <span id="comission_prectantage_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Comission Amount
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="comission_amount" id="comission_amount"
                                            class="form-control" placeholder="comission_amount"
                                            value="<?= $comission_amount ?>" readonly />
                                        <span id="comission_amount_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Total Purchase Value
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="purchase_value" id="purchase_value"
                                            class="form-control" placeholder="purchase_value"
                                            value="<?= $purchase_value ?>" readonly />
                                        <span id="purchase_value_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Date
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="date" class="form-control" id="date" placeholder="Date" name="date"
                                            value=''>

                                        <span id="date_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <h4 class="card-title">
                                        Purchase Value Per Unit
                                    </h4>
                                    <div class="col-sm-9">
                                        <input type="number" name="purchaseValuePerUnit" id="purchaseValuePerUnit"
                                            class="form-control" placeholder="purchase_value"
                                            value="<?= $purchase_value ?>" readonly />
                                        <span id="purchaseValuePerUnit_error" class="error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="submit_button">
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                        </div>
                    </form>
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
    // Run once on load
    toggleClassDiv();

    // Run on change
    $("#stock_type").change(function () {
        toggleClassDiv();
    });

    function toggleClassDiv() {
        var stockType = $("#stock_type").val();

        stockType === "OTHERS"
            ? $("#select_class_div").hide() // hide 
            : $("#select_class_div").show();                          // show
    }
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
            function multiply() {
                var qty = parseInt($('#qty').val()) || 0; // Use 0 if empty
                var mrp = parseFloat($('#mrp').val()) || 0; // Use 0 if empty

                // Multiply the values and show the result
                var total = qty * mrp;
                $('#total').val(total.toFixed(2));
            }

            function calculateValues() {
                var qty = parseInt($('#qty').val()) || 0; // Use 0 if empty
                var mrp = parseFloat($('#mrp').val()) || 0; // Use 0 if empty
                // Multiply the values and show the result
                var total = qty * mrp;
                $('#total').val(total.toFixed(2));

                var commissionPercentage = parseFloat($('#comission_prectantage').val()) || 0; // Use 0 if empty
                var mrp = parseFloat($('#mrp').val()) || 0; // Use 0 if empty

                // Calculate purchase value (quantity * price per unit)
                //var purchaseValue = integerVal * doubleVal;

                // Calculate commission amount (purchase value * (commission percentage / 100))
                var commissionAmount = total * (commissionPercentage / 100);

                // Display results


                $('#comission_amount').val(commissionAmount.toFixed(2));
                var purchase_value = total - commissionAmount.toFixed(2);
                $('#purchase_value').val(purchase_value.toFixed(2));
                // Purchase value per unit (total purchase value / quantity)
                var purchaseValuePerUnit = purchase_value / qty;
                $('#purchaseValuePerUnit').val(purchaseValuePerUnit.toFixed(2));
            }

            // Call the multiply function on every input event (captures typing, copy-paste, etc.)
            // $('#qty, #mrp').on('input', function () {
            //     multiply();
            // });
            // Call calculateValues function on each keyup/input event for any of the fields
            $('#qty, #mrp, #comission_prectantage').on('input', function () {
                calculateValues();
            });

        });
        $('form').submit(function (e) {

            e.preventDefault();

            $('.error').html('');
            var date = $('#date').val();
            var qty = $('#qty').val();
            var mrp = $('#mrp').val();
            var total = $('#total').val();
            var stock_type = $("#stock_type option:selected").val();
            var stock_master_id = $("#stock_master_id  option:selected").val();
            var publishers_id = $("#publishers_id  option:selected").val();
            var session = $("#session option:selected").val();
            var comission_prectantage = $('#comission_prectantage').val();
            var comission_amount = $('#comission_amount').val();
            var purchase_value = $('#purchase_value').val();
            var purchaseValuePerUnit = $('#purchaseValuePerUnit').val();

            if (!session) {
                $("#session_error").html("Please select a session.");
            }
            if (!stock_type) {
                $("#stock_type_error").html("Please select at least One stock.");
            }
            if (!stock_master_id) {
                $("#stock_master_id_error").html("Please select at least One stock Name.");
            }
            if (!publishers_id) {
                $("#publishers_id_error").html("Please select at least One Publishers Name.");
            }
            if (!date) {
                $('#date_error').html('Please Enter Date.');
            }
            if (!qty) {
                $('#qty_error').html('Please Enter Quentity Amount.');
            }
            if (!mrp) {
                $('#mrp_error').html('Please Enter MRP');
            }
            if (!total) {
                $('#total_error').html('Please Enter Total .');
            }
            if (!comission_prectantage) {
                $('#comission_prectantage_error').html('Please Enter comission Prectantage.');
            }
            if (!comission_amount) {
                $('#comission_amount_error').html('Please Enter comission amount.');
            }
            if (!purchase_value) {
                $('#purchase_value_error').html('Please Enter purchase Value.');
            }
            if (!purchaseValuePerUnit) {
                $('#purchaseValuePerUnit_error').html('Please Enter purchase Value Per Unit.');
            }


            if (session && stock_type && stock_master_id && publishers_id && date && qty && mrp && total && comission_prectantage && comission_amount && purchase_value && purchaseValuePerUnit) {
                call_ajax_submit();
            }
            // return true;
            //$('form').get(0).submit();


        });

        function call_ajax_submit() {

            $.ajax({
                type: "POST",
                url: "add_manage_stock_post.php",
                data: $('form').serialize(),
                beforeSend: function () {
                    $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2 disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Submiting...</button>');
                },
                success: function (data) {

                    if (data == 0) {
                        alert("Stock update successfully.");
                        window.location.href = 'stock.php';
                    } else {
                        alert("Something went wrong.");
                    }

                }
            });
        }


    </script>