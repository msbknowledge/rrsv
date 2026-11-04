<?php 
if(isset($_POST['token'])) {
    // print_r($_POST);
    // die();
   } 
?>
<style>
    #testSelect1_multiSelect {
         width: 200px;
    }
    
    .red {
        color: red;
    }
    
    .hight_width {
        font-size: 25px;
    }

</style>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
<!-- partial -->
<?php
session_start();
include('include/header.php');
include('include/dbcon.php');

if(isset($_POST['token'])) {
        $token =  $_POST['token'];
    $start_date =  $_POST['start_date'];
    $end_date =  $_POST['end_date'];
    $teacher_id_val =  $_POST['teacher_id_val'];

} else {
    $token =  "";
    $start_date =  "";
    $end_date =  "";
    $teacher_id_val =  "";
}
$print ="";
if($start_date !="" && $end_date !="") {
  // SELECT COUNT(status) AS st FROM `rrsv_student_attendence` WHERE `status` = 'Late' AND `student_id` = 12 AND `date` BETWEEN '2022-01-01' AND '2022-04-04' AND `class_name` = 'STANDERD 2' AND `scl_session` = 2022
    $late = 0;
    $sql_absent = "SELECT COUNT(is_late) AS st FROM rrsv_teacher_attendence WHERE is_late = 2 AND teacher_id = '".$teacher_id_val."' AND date BETWEEN '".$start_date."' AND '".$end_date."'";
    // echo $sql_absent;
    // die();
    $result=mysqli_query($myDB,$sql_absent);
    $rows=mysqli_fetch_array($result,MYSQLI_ASSOC);
    $late = $rows['st'];
    
    $present = 0;
    $sql_late = "SELECT COUNT(id) AS present_count FROM rrsv_teacher_attendence WHERE teacher_id = '".$teacher_id_val."' AND date BETWEEN '".$start_date."' AND '".$end_date."'";
    $result=mysqli_query($myDB,$sql_late);
    $rows=mysqli_fetch_array($result,MYSQLI_ASSOC);
    $present = $rows['present_count'];
    $print = "search for From ".$start_date." to date ".$end_date." Total late Count ".$late." and Total present count ".$present;
   // echo $print;
    // die();
    // echo "get date".$start_date.$end_date;
    // print_r($_GET);
    // die();
}
    function fill_cat($myDB){
        $output='';
        $sql="select id,full_name from rrsv_teacher where 1";
        $res=mysqli_query($myDB,$sql);
        while($rows=mysqli_fetch_array($res)) {
            $output .='<option value="'.$rows["id"].'">'.$rows["full_name"].'</option>';
        }
	
    	return $output;
	}
	
// 	print_r(fill_cat($myDB));
// 	die();
?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <!--Add Attendence-->
                        <h4 class="card-title">Add Attendence</h4>
                        <form action="teacher_attendence_post.php" id="add_attendence" method="POST">
                            <input type="hidden" name="token" id="token" value="<?=$_SESSION['_token']?>">
                            <input type="hidden" name="id" id="id" value="0">
                         <div class="row">
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Teacher Name</label>
                                            <div class="col-sm-12">
                                                <select name="teacher_id" id="teacher_id" class="form-control minput">
                                                    <option value="">--Select Teacher Name--</option>
                                                    <?php echo fill_cat($myDB);?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="date" name="date" onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>In Time</label>
                                            <div class="col-sm-12">
                                                <input type="time" id="in_time" name="in_time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Out Time</label>
                                            <div class="col-sm-12">
                                                <input type="time" id="out_time" name="out_time">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!--<div class="col-md-2 dis_month"> -->
                                    <!--    <div class="form-group row">-->
                                    <!--        <label class="col-form-label"><span></span>Provision Class</label>-->
                                    <!--        <div class="col-sm-12">-->
                                    <!--            <input type="number"  class="form-control" id="provision_class" name="provision_class" value="0">-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>&nbsp;</label>
                                            <div class="col-sm-12">
                                                <input type="checkbox" id="is_extra_day" name="is_extra_day"> Is Extra Day
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <div>
                            <div class="row">
                            <h4 class="card-title">Add P C Class  <span>&nbsp; <button type="button" name="add" id="add" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-account-plus"></i></button></span></h4>
                            
                                <div class="table-responsive" >
                                    <table class="table table-striped" id="crud_table">
                                        <thead>
                                        <tr>
                                            <th>P C Teacher Name</th>
                                            <th>Class Count</th>
                                        </tr>
                                        </thead>
                                       <tr>
                                       <td>
                                    <select name="p_c_teacher_class[p_c_teacher_id][]" class="form-control cat" id="p_c_teacher_id1" accesskey="1" >
                                        <option value="">--Select Teacher Name--</option>
									    <?php echo fill_cat($myDB);?>
								    </select>
                                </td>
 	          						 <td>
								<input type="number" accesskey="1" class="form-control qtycal" id="cls_count1" name="p_c_teacher_class[cls_count][]" placeholder="Class Count" onchange="myFunction()"  />
							 </td>
                                </tr>
                                    </table>
                                </div>
                            </div>
                            
                                    
                                                           
                                <div class="row">
                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                </div>
                                 </form>
                                 </div>
                                  <!--Add Attendence-->
                                  
                               <!--Search Attendence-->
                            <h4 class="card-title">Search Attendence  <span>&nbsp; <a href="teacher_attendence.php"><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-refresh"></i></button></a></span></h4>
                            
                            <form action="" id="search_attendence" method="POST">
                                 <div class="row">
                                    
                                    <input type="hidden" name="token" id="token" value="<?=$_SESSION['_token']?>">
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Teacher Name</label>
                                            <div class="col-sm-12">
                                                <select name="teacher_id_val" id="teacher_id_val" class="form-control minput">
                                                    <option value="">--Select Teacher Name--</option>
                                                    <?php
                                                        $sql="select id,full_name from rrsv_teacher where 1";
                                                        $res=mysqli_query($myDB,$sql);
                                                        while($rows=mysqli_fetch_array($res)) {
                                                    ?>
                                                    <option value="<?=$rows['id'];?>" <?php echo ((int)$rows['id'] == (int)$teacher_id_val) ? 'selected' : ''; ?>><?=$rows['full_name'];?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>From Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="start_date" name="start_date"  value="<?=$start_date?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>To Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?=$end_date?>">
                                            </div>
                                        </div>
                                    </div>
                                   
                                <div class="row">
                                    <button type="submit" class="btn btn-primary me-2">Search</button>
                                </div>
                                 </div>
                                </form>
                    </div>
                    
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Teacher Attendence</h4>
                        <?=$print?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th  class="card-title">
                                                sl
                                            </th>
                                            <th  class="card-title">
                                                Teacher Name
                                            </th>
                                            <th  class="card-title">
                                               Date
                                            </th>
                                            <th  class="card-title">
                                                In Time
                                            </th>
                                            <th  class="card-title">
                                                Out Time
                                            </th>
                                            <th  class="card-title">
                                                Working Houre
                                            </th>
                                            <th  class="card-title">
                                                Status
                                            </th>
                                            <th class="card-title">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php
                                       

                                            if(isset($_GET['page']))
                                              {
                                              $page=$_GET['page'];
                                              }
                                              else
                                              {
                                              $page=1;
                                              }
                                               $perpage=100;
                                                $lowerlimit=($page-1)*$perpage;

                                                 $sql="SELECT rrsv_teacher_attendence.*, rrsv_teacher.full_name FROM rrsv_teacher_attendence INNER JOIN rrsv_teacher ON rrsv_teacher_attendence.teacher_id = rrsv_teacher.id";
                                                 
//                                                  SELECT  rrsv_student_behaviour.*,
//   rrsv_teacher.full_name AS teacher_name,
//   rrsv_student_registration.scl_name AS student_name
// FROM  rrsv_student_behaviour
// INNER JOIN rrsv_teacher
//   ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id
// INNER JOIN rrsv_student_registration
//   ON rrsv_student_behaviour.student_id = rrsv_student_registration.id WHERE 1 AND (rrsv_student_registration.scl_name LIKE '%a%') AND (rrsv_student_behaviour.unit = 'STANDERD 2') AND (rrsv_student_behaviour.scl_session = '2022')



                                                    if(isset($_POST['teacher_id_val'])) {
                                                        $perpage=100;
                                                            if($_POST['teacher_id_val'] != "" && $_POST['start_date'] != "" && $_POST['end_date'] != "") {
                                                                $sql .=" WHERE teacher_id = ".$_POST['teacher_id_val']." AND  (date BETWEEN '".$_POST['start_date']."' AND '".$_POST['end_date']."')";
                                                            }
                                                            else if($_POST['teacher_id_val'] != "" && $_POST['start_date'] == "" || $_POST['end_date'] == "") {
                                                                $sql .=" WHERE teacher_id = ".$_POST['teacher_id_val'];
                                                            }
                                                            else if($_POST['teacher_id_val'] == "" && $_POST['start_date'] != "" && $_POST['end_date'] != "") {
                                                                $sql .=" WHERE (date BETWEEN '".$_POST['start_date']."' AND '".$_POST['end_date']."')";
                                                            }
                                                            else {
                                                                $sql .=" ";
                                                            }
                                                     }
                                                    // $sql .=" ORDER BY rrsv_teacher_attendence.date  DESC";
                                                     
                                                 $result=mysqli_query($myDB,$sql);
                                                 $totalrecord=mysqli_num_rows($result);
                                                  $totalpage=ceil($totalrecord/$perpage);
                                                 $sql .=" order by id desc limit $lowerlimit,$perpage";
                                                 $result1=mysqli_query($myDB,$sql);
                                                 $l=mysqli_num_rows($result1);
                                                 $result=mysqli_query($myDB,$sql);
                                                 // echo $sql;
                                                  if($l>0)

                         {

                         while ($rows=mysqli_fetch_array($result1,MYSQLI_ASSOC))

                         {
                      
                        
                           $c++;
						   $id=$rows['id'];
						  $status= $rows['status'];
                          ?>
                          
                                                 
                          
                                                 
                                        <tr>
                                            <td>
                                                <?=$rows['id'];?>
                                            </td>
                                            <td>
                                                <?=$rows['full_name'];?>
                                            </td>
                                            <td>
                                                <?=date('d/m/Y', strtotime($rows['date']));?>
                                            </td>
                                            <td>
                                                <?=$rows['in_time'];?>
                                            </td>
                                            <td>
                                                <?=$rows['out_time'];?>
                                            </td>
                                            <td>
                                                <?=gmdate("H:i", (int)$rows['time_in_second']);?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if($rows['is_extra_day'] == 1) {
                                                        echo 'Extra Day';
                                                    } else {
                                                        echo $rows['is_late'] == 2 ? 'Late' : 'Not Late';
                                                    }   
                                                    $date = $rows['date'];
                                                ?>
                                            </td>
                                            <td>

                                                <a href="teacher_p_c_class.php?id=<?=$rows['teacher_id'];?>&date=<?=$rows['date'];?>"  class="btn btn-success">Check P.C Class</a>
                                                <button onclick="teacher_dlt(<?=$rows['teacher_id'];?>,'<?=$date?>')" class="btn btn-success v" style="background-color: #bf4c15;border-color: #bf4c15;"><i class="mdi mdi-delete"></i></button>
                                            </td>
                                        </tr>
                                        <?php } } else { echo "no record found";}
     
                                                   ?>
                                    </tbody>
                                </table>
                                 <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
        <?php

                               if($page>1)
                               {
                              ?>
      <li class="page-item"> <a class="page-link" href="teacher_attendence.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="teacher_attendence.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="teacher_attendence.php?page=<?=($page+1);?>">Next</a>
    </li>
      <?php
                              }
                              
                              ?>
  </ul>
</nav>
                            </div>
                        </div>
                    </div>
                </div>
       
                    
               

<?php
include('include/footer.php');
?>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
<script src="multiselect-dropdown.js" ></script>
 <script>
    $(document).ready(function () {
        if('<?php echo $token;?>' != "") {
            $("#teacher_id_val").focus();
        }
    });
    
    $('#add_attendence').on('submit', function() {
        var teacherId = $('#teacher_id').val();
        if(teacherId == "" || teacherId == null) {
            alert("Please Select teacher name");
            return false; 
        }
        var date = $('#date').val();
        if(date == "" || date == null) {
            alert("Please Select date");
            return false; 
        }
        var in_time = $('#in_time').val();
        if(in_time == "" || in_time == null) {
            alert("Please Select in_time");
            return false; 
        }
        var out_time = $('#out_time').val();
        if(out_time != "") {
             var in_time = Number(in_time.replace(":", ""));
             var out_time = Number(out_time.replace(":", ""));
             
             var val = out_time - in_time;
             
                  if( Math.sign(val) == -1) {
                      alert("Out Timme Not Less Than In Time");
                       return false; 
                  }
        }
         
     });
     
     $('#search_attendence').on('submit', function() {
        var teacherId = $('#teacher_id_val').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(teacherId == "" && start_date == "" && end_date == "") {
            alert("Please Select atleast one fild for search");
            return false; 
        }
        
        if(start_date != "" && end_date == "") {
            alert("Please Select end date");
            return false; 
        }
        
        if(start_date == "" && end_date != "") {
            alert("Please Select start date");
            return false; 
        }
        
        const x = new Date(start_date);
        const y = new Date(end_date);

        if (x.getTime() > y.getTime()) {
            alert("To Date Not Less Than Form Date");
            return false; 
        }
         
     });
     
     function teacher_dlt(teacher_id,date) {
        if (confirm('Are you sure want to delete this record?')) {
        //alert(date);
            window.location.href = "teacher_attendence_post.php?teacher_id="+teacher_id+"&date="+date;
        }
     }
     
    // $(document).ready(function(){
	var i=$('table tr').length;
     $("#add").on('click',function(){
        html = '<tr id="row'+i+'">';
        html += '<th><select name="p_c_teacher_class[p_c_teacher_id][]" id="p_c_teacher_id'+i+'" accesskey="'+i+'" class="form-control cat"><option value="">--Select Teacher Name--</option><?php
         echo fill_cat($myDB); ?></select></th>';
		html += '<th><input type="number" class="form-control qtycal" name="p_c_teacher_class[cls_count][]"  id="cls_count'+i+'"  accesskey="'+i+'"  placeholder="Class Count" onchange="myFunction()"></td>';

        $('#crud_table').append(html);
        i++;
    });
    
     </script>

 