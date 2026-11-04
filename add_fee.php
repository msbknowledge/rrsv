<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_fee where id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
?>
      <!-- partial -->
      <div class="main-panel">  
                  <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_fee.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                    
                  <h4 class="card-title">Add Student Fees Rate</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_class_post.php' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                   <div class="form-group">
                      <label for="exampleInputUsername1">Session</label>
	                    <select name="scl_session" class="form-control" value="<?=$rows['scl_session'];?>"   id="scl_session">
                              <option value="">-Select Session-</option>
                                <?php
									for($i = date("Y")-3; $i <=date("Y")+10; $i++)
								{
									?>
									<!-- '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;-->
								<option value="<?php echo $i;?>"<?php if(trim($rows['scl_session']==$i))echo "selected";?>>
								<?php echo $i;?>
          						</option>
									<?php
								}

								?>
							</select>
                     <span id="errorsession" style="color:red;"></span>
                    </div>
                     <div class="form-group">
                      <label for="exampleInputUsername1">Class</label>
                            <select name="class_name" class="form-control" id="class_name">
                                <option value="">-Select Class-</option>
                                    <?php
									$id=0;
									$sql="select * from rrsv_class order by id";
									$res=mysqli_query($myDB,$sql);
									while($obj=mysqli_fetch_array($res,MYSQLI_ASSOC))
									{
									?>
								<option value="<?php echo $obj['class_name'];?>"<?php if(trim($rows['scl_class']==$obj['class_name']))echo "selected";?>>
									<?php echo $obj['class_name'];?>
          						</option>
									<?php
									}
									?>
                            </select>
                            
                     <span id="errorclass" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Admission Fees</label>
                      <input type="text" class="form-control" id="monthly_admission_fee" placeholder="Admission Fees" name="monthly_admission_fee" value='<?=$rows['monthly_admission_fee'];?>' >
                     <span id="error" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Re-Admission Fees</label>
                      <input type="text" class="form-control" id="readmission_fee" placeholder="Re-Admission Fees" name="readmission_fee" value='<?=$rows['readmission_fee'];?>' >
                     <span id="error1" style="color:red;"></span>
                    </div>
                      <div class="form-group">
                      <label for="exampleInputUsername1">Monthly Fees</label>
                      <input type="text" class="form-control" id="monthly_fee" placeholder="Monthly Fees" name="monthly_fee" value='<?=$rows['monthly_fee'];?>' >
                     <span id="error2" style="color:red;"></span>
                    </div>
                      <div class="form-group">
                      <label for="exampleInputUsername1">Admission Form Fees</label>
                      <input type="text" class="form-control" id="ad_form_charge" placeholder="Admission Form Fees" name="ad_form_charge" value='<?=$rows['ad_form_charge'];?>' >
                     <span id="error3" style="color:red;"></span>
                    </div>
                    <!--  <div class="form-group">-->
                    <!--  <label for="exampleInputUsername1">Book & Copys</label>-->
                    <!--  <input type="text" class="form-control" id="book_charge" placeholder="Section Name" name="book_charge" value='<?=$rows['book_charge'];?>' >-->
                    <!-- <span id="error1" style="color:red;"></span>-->
                    <!--</div>-->
                        <div class="form-group">
                      <label for="exampleInputUsername1">Student Uniform Charge</label>
                      <input type="text" class="form-control" id="uniform" placeholder="Student Uniform Charge" name="uniform" value='<?=$rows['uniform'];?>' >
                     <span id="error4" style="color:red;"></span>
                    </div>
                          <div class="form-group">
                      <label for="exampleInputUsername1">Diary Charge</label>
                      <input type="text" class="form-control" id="diary" placeholder="Diary Charge" name="diary" value='<?=$rows['diary'];?>' >
                     <span id="error5" style="color:red;"></span>
                    </div>
                                  <div class="form-group">
                      <label for="exampleInputUsername1">ID Card Charge</label>
                      <input type="text" class="form-control" id="icard" placeholder="ID Card Charge" name="icard" value='<?=$rows['icard'];?>' >
                     <span id="error6" style="color:red;"></span>
                    </div>
        
                              <div class="form-group">
                      <label for="exampleInputUsername1">Bag Charge</label>
                      <input type="text" class="form-control" id="bag" placeholder="Bag Charge" name="bag" value='<?=$rows['bag'];?>' >
                     <span id="error7" style="color:red;"></span>
                    </div>
                                  <div class="form-group">
                      <label for="exampleInputUsername1">Sweater, Slax, Cap Charge</label>
                      <input type="text" class="form-control" id="sweater" placeholder="Sweater, Slax, Cap Charge" name="sweater" value='<?=$rows['sweater'];?>' >
                     <span id="error8" style="color:red;"></span>
                    </div>
                                  <div class="form-group">
                      <label for="exampleInputUsername1">Shoes Charge</label>
                      <input type="text" class="form-control" id="shoes" placeholder="Shoes Charge" name="shoes" value='<?=$rows['shoes'];?>' >
                     <span id="error9" style="color:red;"></span>
                    </div>        
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_fee.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
                </div>
              </div>
            </div>
           </div>
        </div>
         <?php
include('include/footer.php');
?>
<script>
    $('form').submit(function(e){
        e.preventDefault();
        var scl_session = $('#scl_session').val();
         var class_name = $('#class_name').val();
         var monthly_admission_fee = $('#monthly_admission_fee').val();
         var monthly_fee = $('#monthly_fee').val();
         var readmission_fee = $('#readmission_fee').val();
         
         var ad_form_charge = $('#ad_form_charge').val();  
         var uniform = $('#uniform').val();
         var diary = $('#diary').val();
         var icard = $('#icard').val();
         var bag = $('#bag').val();
         var sweater = $('#sweater').val();
         var shoes = $('#shoes').val();

          if(scl_session==''){
              	$('#errorsession').html('Please Enter Session Name.');
		        return false;
                 }
                 if(class_name==''){
              	$('#errorclass').html('Please Enter Class Name.');
		        return false;
                 }
                 if(monthly_admission_fee==''){
              	$('#error').html('Please Enter Admission Fee.');
		        return false;
                 }
                 if(readmission_fee==''){
              	$('#error1').html('Please Enter Readmission Fee.');
		        return false;
                 }
                  if(monthly_fee==''){
              	$('#error2').html('Please Enter Monthly Fee.');
		        return false;
                 }

                  if(ad_form_charge==''){
              	$('#error3').html('Please Enter Addmission Form Charge.');
		        return false;
                 }
                if(uniform==''){
              	$('#error4').html('Please Enter Uniform Charge.');
		        return false;
                 }
                 if(diary==''){
              	$('#error5').html('Please Enter Diary Charge.');
		        return false;
                 }
                 if(icard==''){
              	$('#error6').html('Please Enter Icard Charge.');
		        return false;
                 }
                 if(bag==''){
              	$('#error7').html('Please Enter Bag Charge.');
		        return false;
                 }
                 if(sweater==''){
              	$('#error8').html('Please Enter Sweater Charge.');
		        return false;
                 }
               if(shoes==''){
              	$('#error9').html('Please Enter Shoes Charge.');
		        return false;
                 }
            ajax_fun();

    });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "ajaxfee.php",
                data: $('form').serialize(),
                success: function(val) {
                    
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Student Fees Rate Saved Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#class_name').val(""); 
                        $('#section_name').val(""); 
                }
                if(val == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Student Fees Rate Edited Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                 if(val == 2) {
                     
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>The Section and Class Name User has given is already Saved!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       