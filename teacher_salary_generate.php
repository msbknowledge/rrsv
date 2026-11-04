<?php
//print_r($_GET);

include('include/dbcon.php');

if(isset($_GET['teacher_id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['teacher_id'])));
$month=	$myDB->escape_string(trim(addslashes($_GET['month'])));
$year=	$myDB->escape_string(trim(addslashes($_GET['year'])));

$month_year = $month." ".$year;

$sql9 = "SELECT * FROM `rrsv_teacher_salary_statement` where salary_month='$month_year' and teacher_id=$id";
// print_r($sql9);

// die();
$qr9 = mysqli_query($myDB, $sql9);
 $count = mysqli_num_rows($qr9);

if($count > 0) {
    $cccc =  mysqli_fetch_array($qr9, MYSQLI_ASSOC);
    $ids = $cccc['id'];
//die();
?>
        <script language="javascript" type="text/javascript">;
        alert('Salary Already Generated.');
        window.location.href="teacher_salary_statement4.php?id=<?=$ids;?>";
        </script>;
<?php }
//die();
// $count3 = mysqli_fetch_array($qr9, MYSQLI_ASSOC);
// $provision_class = $count3['cls'];
include('include/header.php');


    $lp_days = 0;
    function year_check($my_year){
      if ($my_year % 400 == 0)
         return 29;
      else if ($my_year % 100 == 0)
         return 28;
      else if ($my_year % 4 == 0)
         return 29;
      else
        return 28;
   }
   $lp_days = year_check($year); //1111; //
   
   
switch ($month) {
  case "January":
    $monthly_date = $year."-01-31";
    $frist_date = $year."-01-01";
    $last_date = $year."-01-31";
    break;
  case "February":
    $monthly_date = $year."-02-".$lp_days;
    $frist_date = $year."-02-01";
    $last_date = $year."-02-".$lp_days;
    break;
  case "March":
    $monthly_date = $year."-03-31";
    $frist_date = $year."-03-01";
    $last_date = $year."-03-31";
    
    break;
 case "April":
    $monthly_date = $year."-04-30";
    $frist_date = $year."-04-01";
    $last_date = $year."-04-30";
    break;
  case "May":
    $monthly_date = $year."-05-31";
    $frist_date = $year."-05-01";
    $last_date = $year."-05-31";
    break;
  case "June":
    $monthly_date = $year."-06-30";
    $frist_date = $year."-06-01";
    $last_date = $year."-06-30";
    break;
  case "July":
    $monthly_date = $year."-07-31";
    $frist_date =  $year."-07-01";
    $last_date =  $year."-07-31";
    break;
  case "August":
    $monthly_date = $year."-08-31";
    $frist_date =  $year."-08-01";
    $last_date =  $year."-08-31";
    break;
  case "September":
    $monthly_date = $year."-09-30";
     $frist_date =  $year."-09-01";
    $last_date =  $year."-09-30";
    break;
    case "October":
    $monthly_date = $year."-10-31";
    $frist_date =  $year."-10-01";
    $last_date =  $year."-10-31";
    break;
  case "November":
    $monthly_date = $year."-11-30";
     $frist_date =  $year."-11-01";
    $last_date =  $year."-11-30";
    break;
  case "December":
    $monthly_date = $year."-12-31";
     $frist_date =  $year."-12-01";
    $last_date =  $year."-12-31";
    break;
    
    
  default:
    $monthly_date = $year."-00-00";
     $frist_date =  $year."-00-00";
    $last_date =  $year."-00-00";
}



// echo $monthly_date;
// die();
$teacher_id = $id;
// $frist_date = date('Y-m-01');
// $last_date = date('Y-m-t');

$sql = "SELECT count(*) AS count FROM `rrsv_teacher_attendence` where date between '$frist_date' and '$last_date' and is_extra_day=0 and teacher_id=$teacher_id";

$qr = mysqli_query($myDB, $sql);
$count = mysqli_fetch_array($qr, MYSQLI_ASSOC);
$presents_days = $count['count'];


$sql2 = "SELECT count(is_late) AS late FROM `rrsv_teacher_attendence` where date between '$frist_date' and '$last_date' and teacher_id=$teacher_id and is_extra_day!=1 and is_late=2";
$qr2 = mysqli_query($myDB, $sql2);
$count2 = mysqli_fetch_array($qr2, MYSQLI_ASSOC);
$late_days = $count2['late'];

$sql3 = "SELECT sum(p_c_class) AS cls FROM `rrsv_teacher_attendence` where date between '$frist_date' and '$last_date' and teacher_id=$teacher_id";
$qr3 = mysqli_query($myDB, $sql3);
$count3 = mysqli_fetch_array($qr3, MYSQLI_ASSOC);
$provision_class = $count3['cls'];

$sql4 = "SELECT count(is_extra_day) AS extra FROM `rrsv_teacher_attendence` where date between '$frist_date' and '$last_date' and is_extra_day=1 and teacher_id=$teacher_id";
$qr4 = mysqli_query($myDB, $sql4);
$count4 = mysqli_fetch_array($qr4, MYSQLI_ASSOC);
$extra = $count4['extra'];


$sql="select * from rrsv_teacher where id=$id";
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
                        <h4 class="card-title"><button type="button" onclick="window. history.back();" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button>&nbsp;&nbsp;Generate This Month Salary</h4>
                        <form action="teacher_salary_post.php" id="add_attendence" method="POST">
                            <input type="hidden" name="monthly_salary" id="monthly_salary" value="<?=$rows['monthly_salary'];?>">
                            <input type="hidden" name="teacher_name" id="teacher_name" value="<?=$rows['full_name'];?>">
                            <input type="hidden" name="teacher_id" id="teacher_id" value="<?=$rows['id'];?>">
                            <input type="hidden" name="salary_month" id="salary_month" value="<?=$month." ".$year;?>">
                            <input type="hidden" name="monthly_date" id="monthly_date" value="<?=$monthly_date;?>">
                            <input type="hidden" name="first_date" id="first_date" value="<?=$frist_date;?>">
                            <input type="hidden" name="last_date" id="last_date" value="<?=$last_date;?>">
                            <input type="hidden" name="id" id="id" value="0">
                                <div class="row">
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Teacher Id</label>
                                            <div class="col-sm-12">
                                                <h4 class="card-title"><?=$rows['id'];?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Teacher Name</label>
                                            <div class="col-sm-12">
                                                <h4 class="card-title"><?=$rows['full_name'];?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Monthly Salary</label>
                                            <div class="col-sm-12">
                                                <h4 class="card-title"><?=$rows['monthly_salary'];?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Salary Month</label>
                                            <div class="col-sm-12">
                                                <h4 class="card-title"><?php echo $month." ".$year; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                   
                                    
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Total working days</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="working_days" name="working_days" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Total Present days</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="presents_days" name="presents_days" value="<?=$presents_days?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Total absent</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="total_absent" name="total_absent" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Total late</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="total_late" name="total_late" value="<?=$late_days?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Total extra days</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="total_extra_days" name="total_extra_days" value="<?=$extra?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Provision Class</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="provision_class" name="provision_class" value="<?=$provision_class?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Mobile Bill</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="mobile" name="mobile" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    
                              
                                 <div class="row">
                                    
                                            <button type="submit" class="btn btn-primary me-2">Generate Salary</button>
                                        
                                    <br>
                                            <button type="button" onclick="window.location.reload();" class="btn btn btn-light me-2">Reset <i class="mdi mdi-refresh"></i></button>
                                        
                                    </div>
                                </div>
                                 </form> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         <?php
include('include/footer.php');
?>
<script>
    $('#working_days').on('input',function(e){
        get_absent();
    });
    
    $('#presents_days').on('input',function(e){
        get_absent();
    });
    
    function get_absent() {
         var  working_days = Number($('#working_days').val());
      var presents_days = Number($('#presents_days').val());
      var total_absent = working_days - presents_days;
       $('#total_absent').val(total_absent);
    }
    
    $("#genarate_salary").click(function(){
         //alert(45645);
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
        if(teacherId == "" || teacherId == null) {
            alert("Please Select teacher name");
            return false; 
        } else if(month == "" || month == null) {
            alert("Please Select a month");
            return false;
        }else {
            $('form').submit();
        }
    });
    
    
</script>
        
       