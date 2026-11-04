<?php
include('include/header.php');
include('include/dbcon.php');



$id=0;
$status="";
$rows=0;
$res=0;
if(isset($_GET['id']))
{
$id=$_GET['id'];
$sql="select * from  rrsv_student_registration where id=$id";
// $sql="select a.*,b.class_name,c.section_name from student_registration as a,class as b,section as c where a.scl_class=b.id and a.scl_section=c.id and a.id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
$txtsearch=0;
if(isset($_POST['txtsearch']))
{
  $txtsearch=$_POST['txtsearch'];
}
//   print_r($rows);
//  die();   
?>


<div class="main-panel">
    <div class="content-wrapper">



        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_registration.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="card-description">
                    </p>
                    <form id="form_submit" class="form-sample" action="add_registration_post.php" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?=$rows['id'];?>">
					<input type="hidden" name="scl_reg_no" value="<?=$rows['scl_reg_no'];?>">
                        <input type="hidden" name="token" value="<?php echo $token;?>"/>
                        <!--<h4 class="card-title">Personal information</h4>-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Admission Date</label>
                                    <div class="col-sm-9">
                                        <input type="date" name="scl_date" id="scl_date" class="form-control" value="<?=$rows['scl_date'];?>" readonly/>
                                        <span id="full_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Session</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="scl_session" value="<?=$rows['scl_session'];?>"   id="scl_session" readonly>
                                             <option value="">-Select a Session -</option>
                                <?php
									for($i = date("Y")-3; $i <=date("Y")+1; $i++)
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
                                        <span id="fathers_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Admission for Class:</label>
                                    <div class="col-sm-9">
                                  <select name="scl_class" class="form-control" id="scl_class" readonly>
                                <option value="">-Select a Class-</option>
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
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Section</label>
                                    <div class="col-sm-9">
                                         <input type="text" name="scl_roll_no" class="form-control"  size="50" maxlength="100" id="scl_roll_no" value="<?=$rows['scl_section'];?>" readonly>
                                        <span id="gender_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Roll No</label>
                                    <div class="col-sm-9">
                                      <input type="text" name="scl_roll_no" class="form-control"  size="50" maxlength="100" id="scl_roll_no" value="<?=$rows['scl_roll_no'];?>" readonly>
                                        <span id="d_o_b_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                           
                        </div>


                        <!--Official information-->

                        <!--<h4 class="card-title">Official information</h4>-->
                         <h4 class="card-title">Personal information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Full Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_name" id="email" class="form-control" value="<?=$rows['scl_name'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DOB</label>
                                    <div class="col-sm-9">
                                        <input type="date" name="scl_dob" id="date" class="form-control" value="<?=$rows['scl_dob'];?>" readonly/>
                                        <label for="agecal">Age as On(<?php echo date('d/m/Y'); ?>): <span id="agecal" style="background-color:#60ab59;padding: 3px 15px 3px 15px;color: white;font-weight: bold; border-radius: 5px;" >loading..</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Student Aadhar No</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_aadhar" id="qualification" class="form-control" value="<?=$rows['scl_aadhar']?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group row">-->
                            <!--        <label>Aadhar Card / DOB Certificate</label>-->
                            <!--      	<input type="file" name="aadhar_image" class="input" value="<?=$rows['aadhar_image'];?>" size="63" maxlength="100" readonly >-->
                                   
                            <!--    </div>-->
                            <!--</div>-->
                               
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Phone No</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="scl_phone_no" id="monthly_salary" class="form-control" value="<?=$rows['scl_phone_no']?>" readonly/>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Alternative/WhatsApp No</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="alt_phone" id="monthly_salary" class="form-control" value="<?=$rows['alt_phone']?>" readonly/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Father's Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_father_name" id="cl" class="form-control" value="<?=$rows['scl_father_name'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Mother's Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_mother_quli" id="medical" class="form-control" value="<?=$rows['scl_mother_name'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Gender:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_gender" id="medical" class="form-control" value="<?=$rows['scl_gender'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                              <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Cast:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_religion" id="medical" class="form-control" value="<?=$rows['scl_religion'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">BPL Status and Number:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_bpl" id="medical" class="form-control" value="<?=$rows['scl_bpl'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Mother Tongue:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_language" id="medical" class="form-control" value="<?=$rows['scl_language'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Email:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_email" id="medical" class="form-control" value="<?=$rows['scl_email'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Type of Disability:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_disa" id="medical" class="form-control" value="<?=$rows['scl_disa'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Identification Mark:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_ide" id="medical" class="form-control" value="<?=$rows['scl_ide'];?>" readonly/>
                                    </div>
                                </div>
                            </div>
                               <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Nationality:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="nationality" id="medical" class="form-control" value="<?=$rows['nationality'];?>" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Religion:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="religion" id="medical" class="form-control" value="<?=$rows['religion'];?>" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <h4 class="card-title">Address information</h4>
                          <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Village</label>
                                    <div class="col-sm-9">
                                        <!--<textarea class="form-control" name="scl_address" id="address" rows="4" readonly><?=$rows['scl_address'];?></textarea>-->
                                         <input type="text" name="scl_address" id="pin" class="form-control" value="<?=$rows['scl_address']?>" readonly/>
                                        <span id="address_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Post Office</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_pos" id="pin" class="form-control" value="<?=$rows['scl_pos']?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Police Station</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_pol" id="pin" class="form-control" value="<?=$rows['scl_pol']?>" readonly/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Block</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_block" id="pin" class="form-control" value="<?=$rows['scl_block']?>" readonly/>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                               
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Gram Panchayat / Municipality</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_mu" id="pin" class="form-control" value="<?=$rows['scl_mu']?>" readonly/>
                                    </div>
                                </div>
                                
                            </div>
                              <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Locality</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_location" id="pin" class="form-control" value="<?=$rows['scl_location']?>" readonly/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             

                            </div>
                             <div class="row">
                                  <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">District</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_dist" id="pin" class="form-control" value="<?=$rows['scl_dist']?>" readonly/>
                                    </div>
                                </div>
                            </div>


                              <div class="col-md-6">
                                 <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">State</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_state" id="pin" class="form-control" value="<?=$rows['scl_state']?>" readonly/>
                                    </div>
                                </div>
                                </div>
                             <div class="col-md-6">
                                 <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pin</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="scl_pin" id="pin" class="form-control" value="<?=$rows['scl_pin']?>" readonly/>
                                    </div>
                                </div>
                                </div>
                        </div>
                          <h4 class="card-title">Bank information</h4>
                        <div class="row">
                        
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bank Account No</label>
                                    <div class="col-sm-9">
                                        <input type="text"  name="bank_ac_no" id="bank_account_no" class="form-control" value="<?=$rows['bank_ac_no'];?>"/ readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bank Brunch Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="branch_name" id="bank_brunch_name" class="form-control" value="<?=$rows['branch_name'];?>"/ readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bank IFSC</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bank_ifsc_code" id="bank_ifsc" class="form-control" value="<?=$rows['bank_ifsc_code'];?>"/ readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label>File uploade</label>
                                 
                                    <div class="input-group col-md-6">
                                        <a href="student_aadhar/<?=$rows['aadhar_image'];?>" class="text" target="_blank"><b>Aadhaar View </b></a></strong></td>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label>File uploade</label>
                                 
                                    <div class="input-group col-md-6">
                                        <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                               <img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" /><br>
                                <a href="student_reg_image/<?=$rows['image'];?>" class="text" target="_blank"><b>Photo Zoom </b></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div id="submit_button">-->
                        <!--<button type="submit" class="btn btn-primary me-2">Submit</button>-->
                        <!--</div>-->
                    </form>
                   <!--<button id="form_reset" class="btn btn-light">Cancel</button>-->
                   <!-- <div id="alert"></div>-->

                </div>
            </div>
        </div>


    </div>





    <?php
    include('include/footer.php');
    ?>

    <script>
    
     var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
  };
  
if('<?php echo $image; ?>' !="") {
     var output = document.getElementById('output');
      output.src = 'http://rasulpuranathsamitykgschool.com/template/teacher_image/'+'<?php echo $image; ?>';
}
    $( document ).ready(function() {
        $('select[name="gender"]').val('<?php echo $gender; ?>');
        $('select[name="designation"]').val('<?php echo $designation; ?>');
        
        $('#form_reset').click(function(){
            $('#form_submit')[0].reset();
        });

    });
    
        (function($) {
            'use strict';
            $(function() {
                $('.file-upload-browse').on('click', function() {
                    var file = $(this).parent().parent().parent().find('.file-upload-default');
                    file.trigger('click');
                });
                $('.file-upload-default').on('change', function() {
                    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
                });
            });
        })(jQuery);

        // $('form').submit(function(e){
        //     e.preventDefault();
        //     //alert(JSON.stringify($('form').serialize()));
        //     //return false;
        //     var full_name = $('#full_name').val();
        //     var fathers_name = $('#fathers_name').val();
        //     var gender_name = $('#gender_name').val();
        //     var d_o_b = $('#d_o_b').val();
        //     var address = $('textarea#address').val();

        //     if(full_name == ''){
        //         $('#full_name_error').html('Please Enter Full Name.');
        //     }
        //     if(fathers_name == ''){
        //         $('#fathers_name_error').html('Please Enter Fathers Name.');
        //     }
        //     if(gender_name == ''){
        //         $('#gender_name_error').html('Please Enter Gender Name.');
        //     }
        //     if(d_o_b == ''){
        //         $('#d_o_b_error').html('Please Enter Date Of Birth Name.');
        //     }
        //     if(address == ''){
        //         $('#address_error').html('Please Enter Address.');
        //     }
        //     // if(fathers_name == ''){
        //     //   $('#fathers_name_error').html('Please Enter Fathers Name.');
        //     // }
        //     // if(full_name == ''){
        //     //   $('#full_name_error').html('Please Enter Full Name.');
        //     // }
        //     // if(fathers_name == ''){
        //     //   $('#fathers_name_error').html('Please Enter Fathers Name.');
        //     // }
        //     // if(full_name == ''){
        //     //   $('#full_name_error').html('Please Enter Full Name.');
        //     // }
        //     // if(fathers_name == ''){
        //     //   $('#fathers_name_error').html('Please Enter Fathers Name.');
        //     // }

        //     if(full_name !="" && fathers_name !="" && gender_name !="" && d_o_b !="" && address !="") {
        //         //call_ajax_submit();
        //         $('form').submit();
        //     }
        //     //return false;


        // });
// 
        function call_ajax_submit() {
                   //var property = document.getElementById('photo').files[0];

            $.ajax({
                type: "POST",
                url: "add_teachers_post.php",
                data: {file:1,data:$('form').serialize()},
                beforeSend: function() {
                    $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2 disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Submiting...</button>');
                },
                success: function(data) {
                   // alert(data);
                    if(data==1) {
                        $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Teacher Information  Update successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                    }
                    if(data==2) {
                        $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Teacher Information  Insert successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                    }
                    if(data==3) {
                        $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Sory duplicate Entry!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                    }
                }
            });
        }


    </script>
     <script type="text/javascript">

$(document).ready(function(){

		$('#scl_class').change(function(){

		var scl_class=$('#scl_class').val();

		$.ajax({

				url:'section.php',

				type:'POST',

				data:'scl_class='+scl_class,

				success:function(result){

				$('#scl_section').html(result);

				$('#scl_section').selectpicker('refresh');

			}

		});

	});	
	
			$('#scl_section').change(function(){
		var scl_class=$('#scl_class').val();
		var scl_section=$('#scl_section').val();
		var scl_session=$('#scl_session').val();
		$.ajax({
			url:'roll-ajax.php',
				type:'POST',
				data : {scl_class:scl_class,scl_section:scl_section, scl_session:scl_session},
				success:function(result){
				$('#scl_roll_no').val(result);
			}

		});

	});	
	
    
 });
 
 function dateDiff(startingDate, endingDate) {
    var startDate = new Date(new Date(startingDate).toISOString().substr(0, 10));
    if (!endingDate) {
        endingDate = new Date().toISOString().substr(0, 10);    // need date in YYYY-MM-DD format
    }
    var endDate = new Date(endingDate);
    if (startDate > endDate) {
        var swap = startDate;
        startDate = endDate;
        endDate = swap;
    }
    var startYear = startDate.getFullYear();
    var february = (startYear % 4 === 0 && startYear % 100 !== 0) || startYear % 400 === 0 ? 29 : 28;
    var daysInMonth = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    var yearDiff = endDate.getFullYear() - startYear;
    var monthDiff = endDate.getMonth() - startDate.getMonth();
    if (monthDiff < 0) {
        yearDiff--;
        monthDiff += 12;
    }
    var dayDiff = endDate.getDate() - startDate.getDate();
    if (dayDiff < 0) {
        if (monthDiff > 0) {
            monthDiff--;
        } else {
            yearDiff--;
            monthDiff = 11;
        }
        dayDiff += daysInMonth[startDate.getMonth()];
    }

    return yearDiff + 'Year ' + monthDiff + 'Month ' + dayDiff + 'Day';
}


    var end = new Date().toISOString().slice(0, 10);

</script>   

<?php
if(isset($rows)) {
    $date = $rows['scl_dob'];
    if($date !="" || $date != null) { ?>
        <script>
        var start = "<?=$date;?>";
            document.getElementById('agecal').innerText = dateDiff(start, end);
        </script>
        <?php } 
        
    
} ?>