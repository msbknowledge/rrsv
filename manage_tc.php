<style>
    .button {
  text-decoration: none;
  border: none;
  padding: 12px 20px;
  cursor: pointer;
  outline: 0;
  display: inline-block;
  margin-right: 2px;
  color: #ffffff;
  /*border-radius: 12px;*/
}
.button-blue {
  background-color: #3498db;
}
.button-blue:hover {
  background-color: #2980b9;
}
.button-blue:active {
  color: #3498db;
  background-color: #ffffff;
  box-shadow: 0px 0px 6px 2px rgba(0, 0, 0, 0.2);
}
.button-disabled {
  opacity: 0.6;
  pointer-events: none;
}
.button {
  text-decoration: none;
  border: none;
  padding: 12px 20px;
  cursor: pointer;
  outline: 0;
  display: inline-block;
  margin-right: 2px;
  color: #ffffff;
  /*border-radius: 12px;*/
}
.button-green {
  background-color: #08ed3f;
}
.button-green:hover {
  background-color: #2980b9;
}
.button-green:active {
  color: #3498db;
  background-color: #ffffff;
  box-shadow: 0px 0px 6px 2px rgba(0, 0, 0, 0.2);
}
.button-disabled {
  opacity: 0.6;
  pointer-events: none;
}
</style>
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
		
			$sqlsts="update rrsv_student_registration set status='".$status."' where id='".$id."'";
			$resSts=mysqli_query($myDB,$sqlsts) or die("Error into change Student  status:".mysql_error());
			if(mysqli_affected_rows($myDB)>=1) {
				echo '<script language="javascript" type="text/javascript">';
				echo 'window.location.href="manage_tc.php?retcode=4&page='.$current_page.'";';
				echo '</script>';
			}
		  }
	}
?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='manage_tc.php';
    document.frmsearch.submit();
    return true;
  }
  
  /*function confirmdel(id){
		if(confirm("Are you sure to delete this Information?")) {
			window.location.href="manage_tc.php?mode=del&id="+id;
			return true;
		}
	}*/
	

	
	
	function confirmsearch(id,status)
	{
				if(confirm("Are you sure to change this Student status?")) {
				//	window.location.href="manage_tc.php?mode=sts&id="+id+"$status="+status+;
			window.location.href="manage_tc.php?mode=sts&id="+id+"&status="+status;
					//	window.location.href="manage_tc.php?mode=sts&id="+id+"&status="+status+'&page=<?=$current_page?>';
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

            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
             <form name="frmsearch" method="post" action="manage_tc.php" >
            <table id='empTable' class='display dataTable'>
                <thead>
                                          <tr>
                                                 <td class="text" align="center" colspan="12" valign="top">
					
                            <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                            <!--  <option value="">-Select a Section-</option>-->

                            <!--</select>-->
                            <input type="text" name="txtsearch" class="form-control" value="" size="30" maxlength="100"  placeholder="Search By Name, Reg No" style="
    width: 231px;
    margin-left: 355px;
    margin-top: 4px;
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
                       <th>Session</th>
                     
                      <th>Class Name</th>
                    
                      <th>Action</th>
                </tr>
                </thead>
                        <?php
                        $currentyear=date("Y");
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
                           $perpage=50;
                           $lowerlimit=($page-1)*$perpage;
                         
                        //  $sql="select * from rrsv_student_registration where scl_session='$currentyear' ";
                       //   $sql="select a.*,b.s_id from rrsv_student_registration as a,rrsv_tc as b where a.id=b.s_id ";
                   $sql ="select a.*,b.s_id from rrsv_student_registration as a LEFT JOIN rrsv_tc as b on  a.id=b.s_id where 1 ";
                       //$sql="select a.*,b.class_name from rrsv_student_registration as a,class as b where a.scl_class=b.id ";

                         if($txtsearch!="")
                         {
                            $sql.=" and (a.scl_name LIKE '%$txtsearch%')";
                      //  $sql.=" and (a.scl_name='".$txtsearch."')";
                        
                         }
                        if($txtsearch!="")
                         {
                        $sql.=" or (a.scl_reg_no ='".$txtsearch."' )";
                        
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
						 //  $id=$rows['id'];
						  $status= $rows['status'];
                          ?>
                      
                        <tr>
                         <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c;?></td>
                        <td class="text"valign="center" >
					   <?php
							if($rows['image']!=""){
								?>
								<img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" />
								<?php
								}
								else{
								 echo "No Image";
								}	
						?>
						</td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_reg_no'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_name'];?></td>
                    <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_session'];?></td>    
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['scl_class'];?></td>

                        
					  <td  class="text" valign="center" style="padding-left: -50px;">
					      <?
					      if($rows['s_id']!=''){
					          
					      ?>
						<a class="button button-blue button-disabled" disabled href="" title="Click to Genarate Transfer Certificate" >Genarate TC</a>	  
					<?php
					      }
					      else{
					      ?>
					      <a class="button button-blue " disabled href="g_tc.php?id=<?=$rows['id'];?>" title="Click to Genarate Transfer Certificate"  >Genarate TC</a>	  
					
                           <?php
					      }
					      
					      if($rows['s_id']!=''){
					          
					      ?>
							<a class="button button-green" href="tc.php?id=<?=$rows['id'];?>" title="Click to Print Transfer Certificate"  >Print TC</a>
					<?php
					      }
					      else{
					      ?>
					     	<a class="button button-green button-disabled"  href="tc.php?id=<?=$rows['id'];?>" title="Click to Print Transfer Certificate"  >Print TC</a>
					
                           <?php
					      }
					      ?>
					      
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
    <li class="page-item">
        <?php

                               if($page>1)
                               {
                              ?>
      <a class="page-link" href="manage_tc.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_tc.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_tc.php?page=<?=($page+1);?>">Next</a>
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
