<?php
include('include/header.php');
include('include/dbcon.php');
?>


<div class="main-panel">
    <div class="content-wrapper">



        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href="manage_student_absent_late.php?"><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a>Student Search For Absent/late</h4>
                </div>
                
            </div>
            
            <div class="card">
                <div class="card-body">
                    <p class="card-description">
                    </p>
                    <form id="form_submit" class="form-sample" action="manage_student_absent_late.php?" method="GET" enctype="multipart/form-data">
                            <input type="hidden" name="token" id="token" value="Hk4A3ehHsjhoaT6BlJ4E7MnYx8GQOXd2">
                                <div class="row">
                                    
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <select name="scl_session" id="scl_session" class="form-control minput">
                                                        <option value="">-Select a Session -</option>
                                                        <?php
                        									for($i = date("Y")-3; $i <=date("Y")+10; $i++)
                        								{
                        									echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
                        								}
                        
                        								?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <select name="unit" id="unit" class="form-control minput">
                                                    <option value="">-Select a Class-</option>
                                                    <?php
                									$id=0;
                									$sql="select * from rrsv_class order by id";
                									$res=mysqli_query($myDB,$sql);
                									while($obj=mysqli_fetch_array($res,MYSQLI_ASSOC))
                									{
                									?>
                								<option value="<?php echo $obj['class_name'];?>"<?php if(trim($rows['class_name']==$obj['class_name']))echo "selected";?>>
                									<?php echo $obj['class_name'];?>
                          						</option>
                									<?php
                									}
                									?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month">
                                        <button type="button" id="find_student" class="btn btn-primary me-2">Find Student</button>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <select name="student_id" id="student_id" class="form-control minput">
                                                    <option value="">--Select Student Name--</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <h4 class="card-title">
                                                    From Date
                                                </h4>
                                                <input type="date" class="form-control" id="from_date" name="from_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <h4 class="card-title">
                                                    To Date
                                                </h4>
                                                <input type="date" class="form-control" id="to_date" name="to_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 dis_month">
                                        <br>
                                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    </div>
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
    
    $("#txt_search").keyup(function(){
        var search = $(this).val();
        if(search != ""){
//alert(search);
            $.ajax({
                url: 'getSearch.php',
                type: 'post',
                data: {search:search},
                dataType: 'json',
                success:function(response){
                    var len = response.length;
                    $("#student_id").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        $("#student_id").append("<option value='"+id+"'>"+name+"</option>");
                    }
                    //<option value="">--Select Student Name--</option>
                    // binding click event to li
                    // $("#searchResult li").bind("click",function(){
                    //     setText(this);
                    // });
                }
            });
        }
    });


$("#find_student").click(function(){
        var unit = $('#unit').val();
        var scl_session = $('#scl_session').val();
        if(scl_session == ''){
            alert('Please select a Session');
            return false;
        }
        if(unit == '') {
            alert('Please select a Class');
            return false;
        }
        
       // alert(43535);
            $.ajax({
                url: 'getSearch.php',
                type: 'post',
                data: {scl_session:scl_session,class:unit},
                dataType: 'json',
                success:function(response){
                    var len = response.length;
                    $("#student_id").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        $("#student_id").append("<option value='"+id+"'>"+name+"</option>");
                    }
                }
            });
});




        $('form').submit(function(e){
            //e.preventDefault();
            //alert(JSON.stringify($('form').serialize()));
            //return false;
             var scl_session = $('#scl_session').val();
            var unit = $('#unit').val();
            var student_id = $('#student_id').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
           

            

            if(scl_session == ''){
                alert('Please select a Session');
                return false;
            }
            if(unit == ''){
                alert('Please select a Class');
                return false;
            }
            if(student_id == ''){
                alert('Please select student name');
                return false;
            }
            if(from_date == ''){
                alert('Please select From Date.');
                return false;
            }
            if(to_date == ''){
                alert('Please Enter To Date');
                return false;
            }
            
           
                $('form').submit();

        });






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
        