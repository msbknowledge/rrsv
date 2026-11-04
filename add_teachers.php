<?php
include('include/header.php');
$host_name = $_SERVER['HTTP_HOST'];


$id = 0;
$full_name = "";
$fathers_name = "";
$mothers_name = "";
$gender = "";
$d_o_b = "";
$pin = "";
$DOJ = "";
$village = "";
$post = "";
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
    $DOJ = $rows['DOJ'];
    $village = $rows['village'];
    $post = $rows['post'];
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
                    <h4 class="card-title"> <a href='manage_teacher.php'><button type="button"
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
                        <h4 class="card-title">Personal information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Full Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="full_name" id="full_name" class="form-control"
                                            value="<?= $full_name ?>" />
                                        <span id="full_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Father's Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="fathers_name" id="fathers_name" class="form-control"
                                            value="<?= $fathers_name ?>" />
                                        <span id="fathers_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Mother's Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="mothers_name" id="mothers_name" class="form-control"
                                            value="<?= $mothers_name ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Gender</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="gender" id="gender">
                                            <option value="">--chose gender--</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="others">Others</option>
                                        </select>
                                        <span id="gender_name_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Date of Birth</label>
                                    <div class="col-sm-9">
                                        <input type="date" name="d_o_b" id="d_o_b" class="form-control"
                                            placeholder="dd/mm/yyyy" value="<?= $d_o_b ?>" />
                                        <span id="d_o_b_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pin</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="pin" id="pin" class="form-control"
                                            value="<?= $pin ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Date Of Joining</label>
                                    <div class="col-sm-10">
                                        <input type="date" name="DOJ" id="DOJ" class="form-control"
                                            value="<?= $DOJ ?>" />
                                        <span id="DOJ_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Village</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="village" id="village" class="form-control"
                                            value="<?= $village ?>" />
                                        <span id="village_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Post</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="post" id="post" class="form-control"
                                            value="<?= $post ?>" />
                                        <span id="post_error" style="color:red;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Official information-->

                        <h4 class="card-title">Official information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="email" id="email" class="form-control"
                                            value="<?= $email ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Phone</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="tech_phone_no" id="email" class="form-control"
                                            value="<?= $tech_phone_no ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Designation</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="designation" id="email" class="form-control"
                                            value="<?= $designation ?>" />
                                        <!--<select class="form-control" name="designation" id="designation">-->
                                        <!--    <option value="">--chose designation--</option>-->
                                        <!--    <option value="a">A</option>-->
                                        <!--    <option value="b">B</option>-->
                                        <!--    <option value="c">C</option>-->
                                        <!--</select>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Qualification</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="qualification" id="qualification" class="form-control"
                                            value="<?= $qualification ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Monthly Salary</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="monthly_salary" id="monthly_salary"
                                            class="form-control" value="<?= $monthly_salary ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">CL</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="cl" id="cl" class="form-control" value="<?= $cl ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Medical</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="medical" id="medical" class="form-control"
                                            value="<?= $medical ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Aadhar No</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="aadhar_no" id="aadhar_no" class="form-control"
                                            value="<?= $aadhar_no ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pan No</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="pan_no" id="pan_no" class="form-control"
                                            value="<?= $pan_no ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bank Account No</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bank_account_no" id="bank_account_no"
                                            class="form-control" value="<?= $bank_account_no ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bank Brunch Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bank_brunch_name" id="bank_brunch_name"
                                            class="form-control" value="<?= $bank_brunch_name ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Bank Ifsc</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="bank_ifsc" id="bank_ifsc" class="form-control"
                                            value="<?= $bank_ifsc ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label>File uploade</label>
                                    <input type="file" name="image" id="image" class="file-upload-default"
                                        onchange="loadFile(event)">
                                    <div class="input-group col-md-6">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary"
                                                type="button">Upload</button>
                                            <img id="output" height="100" width="100" />
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

        var loadFile = function (event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src) // free memory
            }
        };

        if ('<?php echo $image; ?>' != "") {
            //alert('<?php echo $image; ?>');
            var output = document.getElementById('output');
            output.src = 'http://' + '<?php echo $host_name; ?>' + '/teacher_image/' + '<?php echo $image; ?>';
        }
        $(document).ready(function () {
            $('select[name="gender"]').val('<?php echo $gender; ?>');
            $('select[name="designation"]').val('<?php echo $designation; ?>');

            $('#form_reset').click(function () {
                $('#form_submit')[0].reset();
            });

        });

        (function ($) {
            'use strict';
            $(function () {
                $('.file-upload-browse').on('click', function () {
                    var file = $(this).parent().parent().parent().find('.file-upload-default');
                    file.trigger('click');
                });
                $('.file-upload-default').on('change', function () {
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
                data: { file: 1, data: $('form').serialize() },
                beforeSend: function () {
                    $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2 disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Submiting...</button>');
                },
                success: function (data) {
                    // alert(data);
                    if (data == 1) {
                        $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Teacher Information  Update successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                    }
                    if (data == 2) {
                        $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Teacher Information  Insert successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                    }
                    if (data == 3) {
                        $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Sory duplicate Entry!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                    }
                }
            });
        }


    </script>