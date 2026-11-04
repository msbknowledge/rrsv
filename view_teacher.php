<?php
include('include/header.php');
$host_name = $_SERVER['HTTP_HOST'];
//die();

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
        
    if(isset($_GET['vId']) && !empty($_GET['vId'])) { 
        include('include/dbcon.php');
        $sql="select * from rrsv_teacher where id='".(int)$_GET['vId']."'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
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
        
    }
    
    
?>


<div class="main-panel">
    <div class="content-wrapper">



        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_teacher.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="card-description">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Personal Information</h4>
                                <div class="table-responsive pt-3">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                              <th>
                                                1
                                              </th>
                                              <th>
                                                <p class="lead"><b>Image</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead">
                                                    <a target="_blank" href="http://<?=$host_name;?>/teacher_image/<?=$image;?>"><img src="http://<?=$host_name;?>/teacher_image/<?=$image;?>" hight="80" width="80" alt="image"></a>
                                                </p>
                                              </th>
                                            </tr>
                                            <tr>
                                            <tr>
                                              <th>
                                                2
                                              </th>
                                              <th>
                                                <p class="lead"><b>Full Name</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$full_name;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                3
                                              </th>
                                              <th>
                                                <p class="lead"><b>Fathers Name</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$fathers_name;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                               4
                                              </th>
                                              <th>
                                                <p class="lead"><b>Mothers Name</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$mothers_name;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                               5
                                              </th>
                                              <th>
                                                <p class="lead"><b>Gender</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$gender;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                6
                                              </th>
                                              <th>
                                                <p class="lead"><b>Date Of Birth</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$d_o_b;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                7
                                              </th>
                                              <th>
                                                <p class="lead"><b>Pin</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$pin;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                8
                                              </th>
                                              <th>
                                                <p class="lead"><b>Address</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$address;?></b></p>
                                              </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <h4 class="card-title">Official Information</h4>
                                <div class="table-responsive pt-3">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <th>
                                                1
                                              </th>
                                              <th>
                                                <p class="lead"><b>Email</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$email;?></b></p>
                                              </th>
                                            </tr>
                                           
                                            <tr>
                                              <th>
                                                2
                                              </th>
                                              <th>
                                                <p class="lead"><b>Designation</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$designation;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                3
                                              </th>
                                              <th>
                                                <p class="lead"><b>Qualification</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$qualification;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                               4
                                              </th>
                                              <th>
                                                <p class="lead"><b>Monthly Salary</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$monthly_salary;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                5
                                              </th>
                                              <th>
                                                <p class="lead"><b>Medical</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$medical;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                6
                                              </th>
                                              <th>
                                                <p class="lead"><b>Aadhar No</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$aadhar_no;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                7
                                              </th>
                                              <th>
                                                <p class="lead"><b>Pan No</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$pan_no;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                8
                                              </th>
                                              <th>
                                                <p class="lead"><b>Bank Account_no</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$bank_account_no;?></b></p>
                                              </th>
                                            </tr>
                                            <tr>
                                              <th>
                                                9
                                              </th>
                                              <th>
                                                <p class="lead"><b>Bank Brunch Name</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$bank_brunch_name;?></b></p>
                                              </th>
                                            </tr>
                                             <tr>
                                              <th>
                                                9
                                              </th>
                                              <th>
                                                <p class="lead"><b>Bank Ifsc</b></p>
                                              </th>
                                              <th>
                                                <p class="text-primary lead"><b><?=$bank_ifsc;?></b></p>
                                              </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
              
                                           
                    

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
        