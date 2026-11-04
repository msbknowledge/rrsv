<?php
error_reporting(1);
    include('include/dbcon.php');
    
if(isset($_GET['dlt_id'])) { ?>
    
    <script>
		if(confirm("Are you sure to delete this Information?")) {
			window.location.href="manage_marksheet.php?mode=del&id="+id;
			return true;
		}
	</script>
		
	<?php	
    $student_id = (int)$_GET['dlt_id'];
    $class = $_GET['scl_class'];
    $session = (int)$_GET['scl_session'];

    $sql="select * from rrsv_marksheet_unit where student_id=$student_id and session=$session and class='$class'";
    $qryresult=mysqli_query($myDB,$sql);
    $totalrecord=mysqli_num_rows($qryresult);
    if($totalrecord === 0) { ?>
    
    <script>
    alert('marksheet already empty!');
    	window.location.href="manage_marksheet.php?";
    </script>
    
    
    <?php exit(); }
    $sql = "DELETE FROM rrsv_marksheet WHERE student_id=$student_id and class='$class' and session=$session";
    $result=mysqli_query($myDB,$sql);
    
      $sql2 = "DELETE FROM rrsv_marksheet_unit WHERE student_id=$student_id and class='$class' and session=$session";
    $result2=mysqli_query($myDB,$sql2);
  // die();
    if ($result === TRUE && $result2 === TRUE) {
        echo '<script>';
        echo 'alert("Record deleted successfully.");';
        echo 'window.history.go(-1)';
        echo '</script>';
        } else {
            echo '<script>';
            echo 'alert("Error deleting record:");';
        	echo 'window.history.go(-1);';
        	echo '</script>';
        }
    //die();
 }
 
 

$ret1code="";
$id=0;
$txtsearch="";
$status="";
$sid=0;
$did=0;
	if(isset($_GET['page'])){
		$current_page=$_GET['page'];
	}else{
		$current_page="";
	}
if(isset($_GET['id'])) {
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}

if(isset($_POST['txtsearch']))
{
  $txtsearch=$myDB->escape_string(trim(addslashes($_POST['txtsearch'])));
}
if(isset($_POST['scl_session']))
{
  $scl_session=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
}
if(isset($_POST['scl_class']))
{
  $scl_class=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
}

if(isset($_POST['scl_section']))
{
  $scl_section=$myDB->escape_string(trim(addslashes($_POST['scl_section'])));
}

if(isset($_GET['retcode'])) {
  $retcode=$myDB->escape_string(trim(addslashes($_GET['retcode'])));
}

if($retcode==1) {
  $msg="course has been editted successfully";
}


if($retcode==3) {
  $msg="Organization has been deleted successfully";
}
if($retcode==4) {
  $msg="Organization has been Inactive successfully";
}


if(isset($_GET['mode'])) {
	$mode=$myDB->escape_string($_GET['mode']);
}

if(isset($_GET['status'])) {
	$status=$myDB->escape_string($_GET['status']);
}
if($id > 0) {
if($mode=='sts'){
		  if(trim($status)=='Active')
			  $status='Inactive';
		  else $status='Active';
		
	echo		$sqlsts="update rrsv_student_registration set status='".$status."' where id='".$id."'";
			$resSts=mysqli_query($myDB,$sqlsts) or die("Error into change Student  status:".mysql_error());
			if(mysqli_affected_rows($myDB)>=1) {
				echo '<script language="javascript" type="text/javascript">';
				echo 'window.location.href="manage_marksheet.php?retcode=4&page='.$current_page.'";';
				echo '</script>';
			}
		  }
	}
	
	include('include/header.php');

?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='manage_marksheet.php';
    document.frmsearch.submit();
    return true;
  }
  
  function confirmdel(text){
		if(confirm("Are you sure to delete this Marksheet?")) {
		    
		    let myArray = text.split("&");
		    
		    let dlt_id = myArray[0];  
		    let scl_class = myArray[1];
		    let scl_session = myArray[2];
		    
        // console.log(myArray)
			window.location.href="manage_marksheet.php?dlt_id="+dlt_id+"&scl_class="+scl_class+"&scl_session="+scl_session;
			return true;
		}
	}
	

	
	
	function confirmsearch(id,status)
	{
				if(confirm("Are you sure to change this Student status?")) {
				//	window.location.href="manage_marksheet.php?mode=sts&id="+id+"$status="+status+;
			window.location.href="manage_marksheet.php?mode=sts&id="+id+"&status="+status;
					//	window.location.href="manage_marksheet.php?mode=sts&id="+id+"&status="+status+'&page=<?=$current_page?>';
			return true;
	}
	}
    </script>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

        <!-- jQuery Library -->
        <script src="libray/js/jquery-3.3.1.min.js"></script>
        
        
        
<div class="main-panel">
        <div class="content-wrapper">

            
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
             <form name="frmsearch" method="post" action="manage_marksheet.php" >
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
                            <input type="text" name="txtsearch" class="form-control" value="" size="30" maxlength="100"  placeholder="Search By Name, Reg No" style="
    width: 231px;
    margin-left: 355px;
    margin-top: -44px;
">
<div class="butt" style="
    margin-top: -46px;
    margin-left: 796px;
">
                        <button type="submit" value="" class="btn btn-primary button" valign="center">Search</button>
                        <button type="submit" value="" name="Reset" class="btn btn-primary buttonr" valign="center">Refresh</button>
                        </div>
                </tr>
                </form>
                <tr>
                <th>SL.No</th>
                    <th>Photo</th>
                    <th>Registration No</th>
                     <th>Student Name</th>
                      <th>Class Name</th>
                    <th>Roll</th>    
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
                         
                         $sql="select * from rrsv_student_registration where status='Active' ";
                       //$sql="select a.*,b.class_name from rrsv_student_registration as a,class as b where a.scl_class=b.id ";

                          if($txtsearch!="")
                         {
                        $sql.=" and (scl_name LIKE '%$txtsearch%')";
                        
                         }
                        if($txtsearch!="")
                         {
                        $sql.=" or (scl_reg_no ='".$txtsearch."' )";
                        
                         }
		                  if($scl_session!="")
                         {
                         $sql.=" and (scl_session='".$scl_session."')";
                        
                         }
					     if($scl_class!="")
                         {
                         $sql.=" and (scl_class='".$scl_class."')";
                        
                         }
                         /*if($luid!='1')
                         {
                         $sql.=" and (created_user='$luid' or updated_user='$luid')";
                         }*/

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
						  $status= $rows['status'];
                          ?>
                      
                        <tr>
                         <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c;?></td>
                        <td class="text"valign="center" >
					   <?php
							if($image<>" "){
								?>
								<img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" />
								<?php
								}
								else{
								 echo "No Image";
								}	
						?>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_reg_no'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_name'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_class'];?></td>
						  <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_roll_no'];?></td>
                        
					  <td  class="text" valign="center" style="padding-left: -50px;">
							  
						<a class="btn btn-primary" href="add_marksheet2.php?id=<?=$rows['id'];?>" title="Click to student Bill"  class="btn btn-primary">Marksheet Create</a>
                        	<a href="marksheet2.php?student_id=<?=$rows['id'];?>&scl_class=<?=$rows['scl_class'];?>&scl_session=<?=$rows['scl_session'];?>" title="Click to print Marksheet" class="btn btn-success v" style="background-color: #bf4c15;border-color: #bf4c15;">Print</a>    	                
						<?php $dlt_id=$rows['id'];$scl_class=$rows['scl_class'];$scl_session=$rows['scl_session'];?>
						
						<div class="btn-group">
  <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #e76bbeff;border-color: #a204a4ff;">
    Edit
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="add_marksheet2_edit2.php?student_id=<?=$rows['id'];?>&scl_class=<?=$rows['scl_class'];?>&scl_session=<?=$rows['scl_session'];?>&unit=1">1st Unit</a></li>
    <li><a class="dropdown-item" href="add_marksheet2_edit2.php?student_id=<?=$rows['id'];?>&scl_class=<?=$rows['scl_class'];?>&scl_session=<?=$rows['scl_session'];?>&unit=2">2nd Unit</a></li>
    <li><a class="dropdown-item" href="add_marksheet2_edit2.php?student_id=<?=$rows['id'];?>&scl_class=<?=$rows['scl_class'];?>&scl_session=<?=$rows['scl_session'];?>&unit=3">3rd Unit</a></li>
  </ul>
</div>
<a class="btn btn-danger" onclick="confirmdel('<?=$dlt_id?>&<?=$scl_class;?>&<?=$scl_session?>')" title="Delete">Delete</a>

                         </td>
                        </tr>
                        
                         <tr>

                         </tr>
                         <?php
						 
                           }
                          }
						  
                          else {
                            echo "<tr>";
                            echo "<td class='errtext' align='center' colspan=6>Records Not Found</td>";
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
      <li class="page-item"> <a class="page-link" href="manage_marksheet.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_marksheet.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_marksheet.php?page=<?=($page+1);?>">Next</a>
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
