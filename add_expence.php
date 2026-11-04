<?php
include('include/header.php');
include('include/dbcon.php');
$rows = 0;
$readOnly = ""; // Default: Inputs are editable
if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
  $sql = "select * from  rrsv_expence where id=$id";
  $res = mysqli_query($myDB, $sql);
  $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
  $readOnly = "readonly"; // Set all fields to read-only except id and file
}
?>
      <!-- partial -->
      <div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Add Expense</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_expence_post.php' method='post' onsubmit="return validateForm()" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?= $rows['id']; ?>"/>
                      <input type="hidden" name="token" value="<?php echo $token; ?>"/>
                      <div class="" id="change">
                 
                  <div class="form-group">
                      <label for="exampleInputUsername1">Expense  Head</label>
                      <select name="expence_name" class="form-control" id="expence_name" <?= $readOnly; ?>>
                                <option value="">Select Expense Head</option>
                            <?php
                            // $id=0;
                            $sql = "select * from rrsv_expence_head order by id";
                            $res = mysqli_query($myDB, $sql);

                            while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                              ?>
                                                                      <option value="<?php echo $obj['expence']; ?>"<?php if (trim($rows['expence_name'] == $obj['expence']))
                                                                           echo "selected"; ?>>
                                                                      <?php echo $obj['expence']; ?>
          
                                                                      </option>
                                                                          <?php
                            }
                            ?>
                          </select>
                     <span id="error1" style="color:red;"></span>
                    </div>
</div>
                    <div class="form-group">
                      <label for="exampleInputUsername1"> Expence Amount</label>
                      <input type="number" class="form-control" id="amount" placeholder="Expence Amount" name="amount" value='<?= $rows['amount']; ?>' <?= $readOnly; ?>>
                     <span id="error2" style="color:red;"></span>
                    </div>
                   <div class="form-group">
                      <label for="exampleInputUsername1">Date of Expence</label>
                      <input type="date" class="form-control" id="d_o_e" placeholder="Date of Expence" name="d_o_e" value='<?= $rows['d_o_e']; ?>' <?= $readOnly; ?>>
                     <span id="error3" style="color:red;"></span>
                    </div>
                    <div class="form-group">
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
                    <div id="passwordContainer"></div>
                     <div class="form-group">
                      <label for="exampleInputUsername1">Details </label>
                     <textarea id="exp_desc" name="exp_desc" class="form-control" rows="4" cols="50" <?= $readOnly; ?> id="exp_desc" >
                        <?= $rows['exp_desc']; ?>
                    </textarea>
                      <span id="error3" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Vouture</label>
                      <input type="file" class="form-control" id="image" placeholder="Date of Expence" name="image" value='<?= $rows['image']; ?>' >
                   <span id="error5" style="color:red;"></span>
                    </div>
                     
                    
                    <button type="submit" class="btn btn-primary me-2" id="submit" name="submit">Submit</button>
                  
                  </form>
                    <a href="manage_expence.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
                </div>
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
      handleDateChange("d_o_e", "passwordContainer", "col-md-12"); 
    });

   $(document).ready(function(){
       $('#submit').click(function(){
        var expence_name=$('#expence_name').val();

          var amount=$('#amount').val();
          var d_o_e=$('#d_o_e').val(); 
          var session=$('#session').val(); 
          var exp_desc=$('#exp_desc').val(); 
          var image=$('#image').val(); 
          if(expence_name==''){
                $('#error1').html('Please Enter Expence Head.');
            return false;
                 }
                 if(amount==''){
                $('#error2').html('Please Enter Ampunt.');
            return false;
                 }
                    if(d_o_e==''){
                $('#error3').html('Please Enter  Date of Expence.');
            return false;
                 }
                 if(session==''){
                $('#error5').html('Please Enter Session of Expence.');
            return false;
                 }
             if(exp_desc==''){
                $('#error4').html('Please Enter  Expence Details.');
            return false;
                 }
                 if(passwordValidate() === false) {
                  return false;
                 }
                    // if ($("#password").length) {
                    //   //alert(11);
                    //   var passwordValue = $("#password").val();

                    //   if (passwordValue === "") {
                    //     alert("Password is required!");
                    //     return false; // Prevent further execution
                    //   }
                    //   if (passwordValue != "abcd") {
                    //     alert("password did not match!");
                    //     return false;
                    //   } 
                    // }
        //          if(image==''){
        //       	$('#error5').html('Please Enter  Expence Details.');
          //  return false;
        //          }
         //   ajax_fun();

    });
    
   });    
    // function ajax_fun() {

    //     $.ajax({
    //             type: "POST",
    //             url: "add_expence_post.php",
    //             data: $('form').serialize(),
    //             success: function(val) {
    //             if(val == 0) {
    //                 $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Section Saved Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
    //                 	$('#error').html('');
    //                   $('#expence_name').val(""); 
    //                     $('#amount').val(""); 
    //                 $('#d_o_e').val("");  
    //                 $('#exp_desc').val(""); 
    //             }
              

    //          }
    //     });
        
    // }    
</script>
        
       