<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_book where id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
?>
      <!-- partial -->
<div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <!--Add Attendence-->
                        <h4 class="card-title">Teacher Salary</h4>
                        <form action="teacher_salary_generate.php" id="add_attendence" method="GET">
                            <input type="hidden" name="id" id="id" value="0">
                                <div class="row">
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Teacher Name</label>
                                            <div class="col-sm-12">
                                                <select name="teacher_id" id="teacher_id" class="form-control minput">
                                                    <option value="">--Select Teacher Name--</option>
                                                    <?php
                                                        $sql="select id,full_name from rrsv_teacher where 1";
                                                        $res=mysqli_query($myDB,$sql);
                                                        while($rows=mysqli_fetch_array($res)) {
                                                    ?>
                                                    <option value="<?=$rows['id'];?>"><?=$rows['full_name'];?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Month</label>
                                            <div class="col-sm-12">
                                                <select name="month" id="month" class="form-control minput">
                                                    <option value="">--Select Month--</option>
                                                    <?php
                                                       for ($m=1; $m<=12; $m++) {
                                                            $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                                    ?>
                                                    <option value="<?=$month;?>"><?=$month;?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Year</label>
                                            <div class="col-sm-12">
                                                <select name="year" id="year" class="form-control minput">
                                                    <option value="">--Select Year--</option>
                                                    <?php
                                                        $years = range(strftime("%Y", time()), 2010);
                                                       foreach($years as $year) {
                                                    ?>
                                                    <option value="<?=$year;?>"><?=$year;?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>&nbsp;</label>
                                            <div class="col-sm-12">
                                                <button type="button" id="check_status" class="btn btn-primary me-2">Check Status</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>&nbsp;</label>
                                            <div class="col-sm-12">
                                                <button type="button" id="genarate_salary" class="btn btn-primary me-2">Generate This Mont Salary</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
    // $('form').submit(function(e){
    //     //e.preventDefault();
    //     alert(45645);
    //      var teacherId = $('#teacher_id').val();
    //     if(teacherId == "" || teacherId == null) {
    //         alert("Please Select teacher name");
    //         return false; 
    //     }
    // });
    
    $("#genarate_salary").click(function(){
       //  alert('genarate_salary');
         var teacherId = $('#teacher_id').val();
        if(teacherId == "" || teacherId == null) {
            alert("Please Select teacher name");
            return false; 
        } else {
            $('form').submit();
        }
    });
    
    $("#check_status").click(function(){
         //alert(45645);
         var teacherId = $('#teacher_id').val();
         var month = $('#month').val();
        var year = $('#year').val();
                  
        if(teacherId == "" || teacherId == null) {year
            alert("Please Select teacher name");
            return false; 
        } else if(month == "" || month == null) {
            alert("Please Select a month");
            return false;
        } else if(year == "" || year == null) {
            alert("Please Select a year");
            return false;
        } else {
            $('form').attr('action', 'teacher_salary_report.php');
            $('form').submit();
        }
    });
    
    
</script>
        
       