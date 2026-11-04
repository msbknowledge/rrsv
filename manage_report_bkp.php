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
		
	echo		$sqlsts="update rrsv_student_registration set status='".$status."' where id='".$id."'";
			$resSts=mysqli_query($myDB,$sqlsts) or die("Error into change Student  status:".mysql_error());
			if(mysqli_affected_rows($myDB)>=1) {
				echo '<script language="javascript" type="text/javascript">';
				echo 'window.location.href="manage_report.php?retcode=4&page='.$current_page.'";';
				echo '</script>';
			}
		  }
	}
?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='manage_report.php';
    document.frmsearch.submit();
    return true;
  }
  
  /*function confirmdel(id){
		if(confirm("Are you sure to delete this Information?")) {
			window.location.href="manage_report.php?mode=del&id="+id;
			return true;
		}
	}*/
	

	
	
	function confirmsearch(id,status)
	{
				if(confirm("Are you sure to change this Student status?")) {
				//	window.location.href="manage_report.php?mode=sts&id="+id+"$status="+status+;
			window.location.href="manage_report.php?mode=sts&id="+id+"&status="+status;
					//	window.location.href="manage_report.php?mode=sts&id="+id+"&status="+status+'&page=<?=$current_page?>';
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
             <form name="frmsearch" method="post" action="expenditure_report.php" >
            <table id='empTable' class='display dataTable'>
                <thead>
                                          <tr>
 <input type='date' class='srinputd' name='fromDate' value='<?php if(isset($_POST['fromDate'])) echo $_POST['fromDate']; ?>'>
                     
                         <input type='date' class='srinputd' name='endDate' value='<?php if(isset($_POST['endDate'])) echo $_POST['endDate']; ?>'>
                             <button type="submit" value="" class="btn btn-primary button" valign="center">View Report</button>
                        <!--<button type="submit" value="" name="Reset" class="btn btn-primary buttonr" valign="center"><i class="fa fa-refresh"></i></button>-->
                </tr>
                </form>
                <tr>
                <th>SL.No</th>
                    <th>Month</th>
                    <th>Total Income</th>
                     <th>Total Expance</th>
                      <th>Total Profit & Lose</th>


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
                           $perpage=2;
                           $lowerlimit=($page-1)*$perpage;
                         
 $sql="select *, date_format(pl_date, '%m-%Y') as month, sum(scl_net) as income1,sum(income_amount) as income2,sum(exp_amount) as exp,sum(scl_net)+sum(income_amount)- sum(abs(exp_amount)) AS 'Balance' ,SUM(scl_net)+sum(income_amount) as 'TotalIncome' FROM rrsv_scl_pl WHERE 1 ";
                           if(isset($_POST['but_search'])){
                            $fromDate = $_POST['fromDate'];
                            $endDate = $_POST['endDate'];
                  
                            if(!empty($fromDate) && !empty($endDate)){
                               $sql .= " and pl_date 
                                            between '".$fromDate."' and '".$endDate."' ";
                            }
                          }
                  

 $result=mysqli_query($myDB,$sql);
                             $totalrecord=mysqli_num_rows($result);
                             $totalpage=ceil($totalrecord/$perpage);
                             $sql .=" group by date_format(pl_date,'%m-%Y') order by pl_date desc limit $lowerlimit,$perpage";
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

                         <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['month'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['TotalIncome'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['exp'];?></td>
						  <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['Balance'];?></td>
                        
					 <!-- <td  class="text" valign="center" style="padding-left: -50px;">-->
							  
						<!--<a class="btn btn-primary" href="add_bill.php?id=<?=$rows['id'];?>" title="Click to edit this Student"  class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>-->
						<!--</a>-->
                                        
                       <!-- <a href="#" onclick="confirmdel(<?=$rows['id'];?>);" title="Click to delete this Admission"  class="btn btn-danger"><i class="icon_close_alt2"></i></a>-->
                          

                         <!--</td>-->
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
      <a class="page-link" href="manage_report.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_report.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_report.php?page=<?=($page+1);?>">Next</a>
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
