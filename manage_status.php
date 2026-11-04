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
				echo 'window.location.href="manage_bill.php?retcode=4&page='.$current_page.'";';
				echo '</script>';
			}
		  }
	}
?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='manage_bill.php';
    document.frmsearch.submit();
    return true;
  }
  
  /*function confirmdel(id){
		if(confirm("Are you sure to delete this Information?")) {
			window.location.href="manage_bill.php?mode=del&id="+id;
			return true;
		}
	}*/
	

	
	
	function confirmsearch(id,status)
	{
				if(confirm("Are you sure to change this Student status?")) {
				//	window.location.href="manage_bill.php?mode=sts&id="+id+"$status="+status+;
			window.location.href="manage_bill.php?mode=sts&id="+id+"&status="+status;
					//	window.location.href="manage_bill.php?mode=sts&id="+id+"&status="+status+'&page=<?=$current_page?>';
			return true;
	}
	}
    </script>
    <style>
        .button.btn.btn-primary.button {
    margin-left: 541px;
    margin-top: -71px;
}
.addstatus {
    margin-top: -43px;
}
    </style>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

        <!-- jQuery Library -->
        <script src="libray/js/jquery-3.3.1.min.js"></script>
        
        
        
<div class="main-panel">
        <div class="content-wrapper">

            
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
                                  <div >
            <!-- Table -->
            <div style="
    margin-left: 460px;
    font-weight: bold;
">
            Status Report Search
            </div> 
                        
            <!-- Table -->
             <form name="frmsearch" method="GET" action="status_report.php" id="frmsearch" >
            <table id='empTable' class='display dataTable'>
                <thead>
                 <tr>
                        <td class="text" align="center" colspan="8" valign="top">
                        <select name="student_status" class="form-control" style="width: 188px;margin-left: 290px;">
                            <option value="">--Select Status--</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <select name="admission_status" class="form-control addstatus" style="width: 216px;margin-left: -139px;">
                            <option value="">--Select Admission Status--</option>
                            <option value="New Admission">New Admission</option>
                            <option value="Readmission">Readmission</option>
                        </select>
						<select name="scl_session" id="scl_session" class="form-control minput" style="width: 152px;margin-top: -47px;margin-left: -870px;">
                                                         <option value="">-Select a Session -</option>
                                <?php
									for($i = date("Y")-3; $i <=date("Y")+10; $i++)
								{
									echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
								}

								?>
                                                </select>
                        <select name="unit" id="unit" class="form-control minput" style="width: 152px;margin-top: -47px;margin-left: -538px;">
                                                   <option value="">-Select a Class-</option>
                                    <?php
									$id=0;
									$sql="select * from rrsv_class order by id";
									$res=mysqli_query($myDB,$sql);
									while($obj=mysqli_fetch_array($res,MYSQLI_ASSOC))
									{
									?>
								<option value="<?php echo $obj['class_name'];?>">
									<?php echo $obj['class_name'];?>
          						</option>
									<?php
									}
									?>
                									                                                </select>
                        <button type="submit" value="" class="btn btn-primary button" valign="center" style="margin-left: 634px;"> View Report</button>
                        <!--<button type="submit" value="" name="Reset" class="btn btn-primary buttonr" valign="center">Refresh</button>-->
                        
                </tr>
                </form>

        </table>

            <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
        <?php

                               if($page>1)
                               {
                              ?>
      <li class="page-item"> <a class="page-link" href="manage_status.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_status.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_status.php?page=<?=($page+1);?>">Next</a>
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

  <script>
  $(document).ready(function () {
     $('#frmsearch').on('submit', function() {
        var fromDate=$('#fromDate').val();
        var endDate=$('#endDate').val();
         if(fromDate!='' && endDate=='' ){
            alert('Input Enddate');
            return false; 
        }
       
});      
 });

  </script>        
          
      
          
        <?php
include('include/footer.php');
?>
