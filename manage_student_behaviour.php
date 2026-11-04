<?php
// if(isset($_POST['txtsearch'])) {
//     print_r($_POST);
//     die();
// }
error_reporting(1);
include('include/header.php');
    include('include/dbcon.php');

//Array ( [scl_session] => 2020 [scl_class] => STANDERD 2 [txtsearch] => ANI )

$scl_session="";
$scl_class="";
$txtsearch="";

if(isset($_GET['id'])) {
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}

if(isset($_GET['txtsearch']))
{
  $txtsearch=$myDB->escape_string(trim(addslashes($_GET['txtsearch'])));
}
if(isset($_GET['scl_session']))
{
  $scl_session=$myDB->escape_string(trim(addslashes($_GET['scl_session'])));
}
if(isset($_GET['scl_class']))
{
  $scl_class=$myDB->escape_string(trim(addslashes($_GET['scl_class'])));
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
                  <h4 class="card-title"> <a href='add_student_behaviour.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add Student Activuty</button></a></h4>
                   </div>
            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                  <button class="btn btn-primary" valign="center" onclick="window.location.assign('manage_student_behaviour.php?');"><i class="mdi mdi-refresh"></i> Refresh</button>

                    <div >
            <!-- Table -->
             <form name="frmsearch" method="get" action="" >
            <table id='empTable' class='display dataTable'>
                <thead>
                                          <tr>
                        <td class="text" align="center" colspan="12" valign="top">
							<select name="scl_session" class="form-control" value="<?=$rows['scl_session'];?>" id="scl_session"style="
    width: 240px;
    margin-left: -703px;
">
                              <option value="">-Select a Session -</option>
                                <?php
									for($i = date("Y")-3; $i <=date("Y")+10; $i++)
								{
									echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
								}

								?>
							</select>
                            <select name="scl_class" class="form-control" id="scl_class" style="
    margin-top: -44px;
    width: 269px;
    margin-left: -168px;
">
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
                            <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                            <!--  <option value="">-Select a Section-</option>-->

                            <!--</select>-->
                            <input type="text" name="txtsearch" class="form-control" value="" size="30" maxlength="100"  placeholder="Search By Name" style="
    width: 231px;
    margin-left: 355px;
    margin-top: -44px;
">
<div class="butt" style="
    margin-top: -46px;
    margin-left: 796px;
">
                        <button type="submit" value="" class="btn btn-primary button" valign="center">Search</button>
                        </div>
                </tr>
                </form>
                                        
                <tr>
 <th>SL. No</th>
                    <th>Date</th>
                     <th>Teacher Name</th>
                     <th>Student Name</th>
                      <th>Class Name</th>
                        <th>Session</th>
                                            <th>Activity Type</th>

                        <th style="width:30%">Descripition</th>
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
                       $sql="SELECT  rrsv_student_behaviour.*,rrsv_teacher.full_name AS teacher_name,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_behaviour INNER JOIN rrsv_teacher ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id INNER JOIN rrsv_student_registration ON rrsv_student_behaviour.student_id = rrsv_student_registration.id ";

// AND (rrsv_student_registration.scl_name LIKE '%a%') AND (rrsv_student_behaviour.unit = 'STANDERD 2') AND (rrsv_student_behaviour.scl_session = '2022')
                         if($txtsearch!="")
                         {//monthly_salary like'%".$searchValue."%'
                        $sql.=" AND (rrsv_student_registration.scl_name like'%".$txtsearch."%')";
                        
                         }
		              if($scl_session!="")
                         {
                         $sql.=" and (rrsv_student_behaviour.scl_session='".$scl_session."')";
                        
                         }
					     if($scl_class!="")
                         {
                         $sql.=" and (rrsv_student_behaviour.unit='".$scl_class."')";
                        
                         }

                       
                        
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
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['teacher_name'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['student_name'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['unit'];?></td> 
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_session'];?></td>
                                                <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['activties'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=strtoupper($rows['des']);?></td>
                        

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
      <li class="page-item"> <a class="page-link" href="manage_student_behaviour.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_student_behaviour.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_student_behaviour.php?page=<?=($page+1);?>">Next</a>
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
           window.location.href = "student_behaviour_post.php?dlt_id="+id;
        }
     }
</script>
          
          
      
          
        <?php
include('include/footer.php');
?>
