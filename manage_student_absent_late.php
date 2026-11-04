<?php
// if(isset($_GET['from_date'])) {
//     print_r($_GET);
//     die();
// }
// error_reporting(1);
 include('include/header.php');
    include('include/dbcon.php');

//Array ( [scl_session] => 2020 [scl_class] => STANDERD 2 [txtsearch] => ANI )

$scl_session="";
$unit="";
$student_id="";
$from_date="";
$to_date="";

//Array ( [token] => Hk4A3ehHsjhoaT6BlJ4E7MnYx8GQOXd2 [scl_session] => 2022 [unit] => STANDERD 2 [student_id] => 14 [from_date] => 2022-10-04 [to_date] => 2022-10-13 )
if(isset($_GET['student_id']))
{
  $student_id=$myDB->escape_string(trim(addslashes($_GET['student_id'])));
}
if(isset($_GET['scl_session']))
{
  $scl_session=$myDB->escape_string(trim(addslashes($_GET['scl_session'])));
}
if(isset($_GET['unit']))
{
  $scl_class=$myDB->escape_string(trim(addslashes($_GET['unit'])));
}
if(isset($_GET['from_date']))
{
  $from_date=$myDB->escape_string(trim(addslashes($_GET['from_date'])));
}
if(isset($_GET['to_date']))
{
  $to_date=$myDB->escape_string(trim(addslashes($_GET['to_date'])));
}
$print ="";
if($from_date !="" && $to_date !="") {
  // SELECT COUNT(status) AS st FROM `rrsv_student_attendence` WHERE `status` = 'Late' AND `student_id` = 12 AND `date` BETWEEN '2022-01-01' AND '2022-04-04' AND `class_name` = 'STANDERD 2' AND `scl_session` = 2022
    $abs_count = 0;
    $sql_absent = "SELECT COUNT(status) AS st FROM rrsv_student_attendence WHERE status = 'Absent' AND student_id = '".$student_id."' AND date BETWEEN '".$from_date."' AND '".$to_date."' AND class_name = '".$scl_class."' AND scl_session = '".$scl_session."'";
    // echo $sql_absent;
    // die();
    $result=mysqli_query($myDB,$sql_absent);
    $rows=mysqli_fetch_array($result,MYSQLI_ASSOC);
    $abs_count = $rows['st'];
    
    $late_count = 0;
    $sql_late = "SELECT COUNT(status) AS st FROM rrsv_student_attendence WHERE status = 'Late'  AND student_id = '".$student_id."' AND date BETWEEN '".$from_date."' AND '".$to_date."' AND class_name = '".$scl_class."' AND scl_session = '".$scl_session."'";
    $result=mysqli_query($myDB,$sql_late);
    $rows=mysqli_fetch_array($result,MYSQLI_ASSOC);
    $late_count = $rows['st'];
    $print = "search for From ".$from_date." to date ".$to_date." Total Absent Count ".$abs_count." and Total Late count ".$late_count;
   // echo $print;
    // die();
    // echo "get date".$from_date.$to_date;
    // print_r($_GET);
    // die();
}
	
?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='manage_registration.php';
    document.frmsearch.submit();
    return true;
  }
  
  /*function confirmdel(id){
		if(confirm("Are you sure to delete this Information?")) {
			window.location.href="manage_registration.php?mode=del&id="+id;
			return true;
		}
	}*/
	

	
	
	function confirmsearch(id,status)
	{
				if(confirm("Are you sure to change this Student status?")) {
				//	window.location.href="manage_registration.php?mode=sts&id="+id+"$status="+status+;
			window.location.href="manage_registration.php?mode=sts&id="+id+"&status="+status;
					//	window.location.href="manage_registration.php?mode=sts&id="+id+"&status="+status+'&page=<?=$current_page?>';
			return true;
	}
	}
    </script>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

        <!-- jQuery Library -->
        <script src="libray/js/jquery-3.3.1.min.js"></script>
        
        
        
<div class="main-panel">
        <div class="content-wrapper">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                      <a href='add_student_absent_late.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add Student Absent/Late</button></a>
                      <a href='add_student_search_absent_late.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i>Search Absent/Late</button></a>
                      <button class="btn btn-primary btn-rounded btn-fw" valign="center" onclick="window.location.assign('manage_student_absent_late.php?');"><i class="mdi mdi-refresh"></i> Refresh</button>
                    </h4>
                </div>
            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">

                    <div >
            <!-- Table -->
             <form name="frmsearch" method="get" action="" >
                 <?=$print;?>
            <table id='empTable' class='display dataTable'>
                <thead>
                                          <tr>
                       
                </tr>
                </form>
                                        
                <tr>
 <th>SL. No</th>
                    <th>Date</th>
                     <th>Student Name</th>
                        <th>Status</th>
                      <th>Class Name</th>
                        <th>Session</th>
                      <th>Action</th>
                </tr>
                </thead>
 <?php
 $bgcolor="";
                          $c=0;
                        

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
                         
                     //  $sql="select * from rrsv_student_registration where 1 ";
                       $sql="SELECT  rrsv_student_attendence.*,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_attendence INNER JOIN rrsv_student_registration ON rrsv_student_attendence.student_id = rrsv_student_registration.id ";

// AND (rrsv_student_registration.scl_name LIKE '%a%') AND (rrsv_student_behaviour.unit = 'STANDERD 2') AND (rrsv_student_behaviour.scl_session = '2022')
                         if($from_date !="" && $to_date !="")
                         {
                        $sql.="WHERE student_id = '".$student_id."' AND date BETWEEN '".$from_date."' AND '".$to_date."' AND class_name = '".$scl_class."'";
                        
                         }
                        //  echo $sql;
                        //  die();
		      //        if($scl_session!="")
        //                  {
        //                  $sql.=" and (rrsv_student_behaviour.scl_session='".$scl_session."')";
                        
        //                  }
					   //  if($scl_class!="")
        //                  {
        //                  $sql.=" and (rrsv_student_behaviour.unit='".$scl_class."')";
                        
        //                  }

                       
                        
                         $result=mysqli_query($myDB,$sql);
                             $totalrecord=mysqli_num_rows($result);
                             $totalpage=ceil($totalrecord/$perpage);
                             $sql .="  order by id desc limit $lowerlimit,$perpage";
                             $result1=mysqli_query($myDB,$sql);
                             $l=mysqli_num_rows($result1);
                             $result=mysqli_query($myDB,$sql);

                         if($l>0)

                         {

                         while ($rows=mysqli_fetch_array($result1,MYSQLI_ASSOC))

                         {
                      
                        
                           $c++;
						   $id=$rows['id'];
						  //$status= $rows['status'];
						  //$alt_phone=$rows['alt_phone'];
                          ?>
                      
                        <tr>
                         <td class="text" style="padding-left:10px;" valign="center">#<?=$id;?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=date('d-m-Y', strtotime($rows['date']));?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['student_name'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['status'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['class_name'];?></td> 
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_session'];?></td>
                                                
                        

<td><button type="button" class="btn btn-danger btn-rounded btn-icon" onclick="activity_dlt(<?=$rows['id'];?>)"><i class="mdi mdi-delete"></i></button></td>
                        </tr>
                        
                         
                         <?php
						 
                           }
                          }
						  
                          else {
                            echo "<tr>";
                            echo "<td class='errtext' align='center' colspan=7>Records Not Found</td>";
                            echo "</tr>";
                           }
					   
                       ?>
          
          
        </table>

            <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
        <?php

                               if($page>1)
                               {
                              ?>
      <li class="page-item"> <a class="page-link" href="manage_student_absent_late.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_student_absent_late.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_student_absent_late.php?page=<?=($page+1);?>">Next</a>
    </li>
      <?php
                              }
                              
                              ?>
  </ul>
</nav>
        </div>
        </div>
            </div>
     
<script>
    function activity_dlt(id) {
        if (confirm('Are you sure want to delete this record?')) {
         //alert(id);
           window.location.href = "student_absent_late_post.php?dlt_id="+id;
        }
     }
</script>
          
          
      
          
        <?php
include('include/footer.php');
?>
