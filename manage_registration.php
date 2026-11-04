<?php
error_reporting(1);
include('include/header.php');
    include('include/dbcon.php');

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
		$current=date('Y-m-d');
			$sqlsts="update rrsv_student_registration set status='".$status."',s_a_d='".$current."' where id='".$id."'";
			$resSts=mysqli_query($myDB,$sqlsts) or die("Error into change Student  status:".mysql_error());
	
			if(mysqli_affected_rows($myDB)>=1) {
				echo '<script language="javascript" type="text/javascript">';
				echo 'window.location.href="manage_registration.php?retcode=4;';
				echo '</script>';
			}
		  }
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
                  <h4 class="card-title"> <a href='add_registration.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> New Student Registration</button></a></h4>
                   </div>
            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
             <form name="frmsearch" method="post" action="manage_registration.php" >
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
                            <select name="scl_class" class="form-control" id="scl_class" style="margin-top: -44px;width: 269px;margin-left: -168px;">
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
 <th>SL. No</th>
                    <th>Photo</th>
                     <th>Reg. No</th>
                    <th>Session</th>
                     <th>Student Name</th>
                     <th>Password</th>
                     <th>Class Name</th>
                     <th>Section</th>
                      <th>Admission Status</th>
                      <th>Dise Status</th>
                     <th>Status</th>
                      <th>Actions</th>
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
                         
                       $sql="select * from rrsv_student_registration where 1 ";
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
				    //  if($scl_section!="")
        //                  {
        //                  $sql.=" or (scl_section LIKE '%$scl_section%')";
                        
        //                  }
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
						  $alt_phone=$rows['alt_phone'];
                          ?>
                      
                        <tr>
                         <td class="text" style="padding-left:10px;" valign="center">#<?=$rows['id'];?></td>
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
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_session'];?></td> 
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_name'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['password'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_class'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_section'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['add_status'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['dise_code'];?><br/><?= !empty($rows['dise_date']) ? date('d-m-Y', strtotime($rows['dise_date'])) : ''; ?></td>
						<td   valign="center" style="padding-left:10px;">
			<a href="#" onclick="javascript:confirmsearch(<?php echo $id;?>,'<?php echo $status;?>');" title="Click to change this register status" class="text">
			<i data="<?php echo $id;?>" class="btn <?php if($status=='Active'){?>btn-info<?php }else{?>btn-danger<?php }?> btn-sm" style="
    
"><?php echo $status;?>
 </i></a></td>
                        
					  <td  class="text" valign="center" style="padding-left: -50px;">
							  
						<a class="btn btn-warning" href="add_registration.php?id=<?=$rows['id'];?>" title="Click to Edit/Re Admission"  class="btn btn-warning btn-sm">Edit/Re Admission
						</a>
                                        
                       <!-- <a href="#" onclick="confirmdel(<?=$rows['id'];?>);" title="Click to delete this Admission"  class="btn btn-danger"><i class="icon_close_alt2"></i></a>-->
                          
                         <a href="viewreg.php?id=<?=$rows['id'];?>" title="Click to View this Admission Details" class="btn btn-success btn-sm" style="
    background-color: #bf4c15;
    border-color: #bf4c15;
">View</a>
<a href="https://wa.me/+91<?php echo $alt_phone;?>?text=Hi, Welcome to RASULPUR RAMKRISHNA SARADA VIDYAPITH.
 Dear Student Your Registration is Successful.
                                            Your Registration Number: <?=$rows['scl_reg_no'];?>" target="_blank" class="btn btn-success btn-sm"><i class="mdi mdi-whatsapp"></i></a>
<!--                         <a href="admission.php?id=<?=$rows['id'];?>" title="Click to Print this Admission from" class="btn btn-warning " target="_blank" style="-->
<!--    background-color: #1a9730;-->
<!--    border-color: #1f4626;-->
<!--">Print </a>-->
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
      <li class="page-item"> <a class="page-link" href="manage_registration.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_registration.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_registration.php?page=<?=($page+1);?>">Next</a>
    </li>
      <?php
                              }
                              
                              ?>
  </ul>
</nav>
        </div>
        </div>
            </div>
     

          
          
      
          
        <?php
include('include/footer.php');
?>
