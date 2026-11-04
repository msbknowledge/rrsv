<style>
/*Not Required*/


/*Required*/
@media (max-width: 576px){.modal-dialog.modal-dialog-slideout {width: 80%}}
.modal-dialog-slideout {min-height: 100%; margin: 0 auto 0 0 ;background: #fff;}
.modal.fade .modal-dialog.modal-dialog-slideout {-webkit-transform: translate(-100%,0);transform: translate(-100%,0);}
.modal.fade.show .modal-dialog.modal-dialog-slideout {-webkit-transform: translate(0,0);transform: translate(0,0);flex-flow: column;}
.modal-dialog-slideout .modal-content{border: 0;}

/*.modal-header h4{*/
/*	color:#fff;*/
/*	font-size:20px;*/
/*	font-weight:600;*/
/*	letter-spacing: 1px;*/
/*}*/
.view_details_ul{list-style: none; }
.view_details_ul li{display: block;color: #6a2414; font-size: 20px;}
.view_details_ul li span{padding: 0px 50px;}
@page {
      size: auto;  /* auto is the initial value */
      margin: 0mm; /* this affects the margin in the printer settings */
    }
    html {
      background-color: #FFFFFF;
      margin: 0px; /* this affects the margin on the HTML before sending to printer */
    }
   
	@media print {
            #printbtn {
                display :  none;
            }
		}
		@media print {
            #cancel {
                display :  none;
            }
        }
    
@media print {
    body{
        background-image: none;
    }
	@media print {
  #printPageButton {
    display: none;
  }
}
@media print {
  #backPageButton {
    display: none;
  }
}
}
div.a {
  text-align: center;
}
.status{
 
    margin-left: 284px;

}
</style>

<?php
//error_reporting(1);
//include('include/top.php');
//include('include/left.php');
include('include/dbcon.php');
$id=0;
$ret1code="";
$id=0;
$txtsearch="";
$status="";
$sid=0;
$did=0;
$did=0;
$scl_class="";
$scl_section="";
$scl_session="";

// print_r($_POST);
// die();
 $status                =$myDB->escape_string(trim(addslashes($_POST['status'])));
 $add_status            =$myDB->escape_string(trim(addslashes($_POST['add_status'])));
 $fromDate              =$myDB->escape_string(trim(addslashes($_POST['fromDate'])));
 $endDate               =$myDB->escape_string(trim(addslashes($_POST['endDate'])));
 
//  if($status !="" && $add_status !="") {
//      echo 434324;
//  }
//  die();
 
 
// $sql="select *, count(status) as countstatus from rrsv_student_registration where status='Active' ";
// $res=mysqli_query($myDB,$sql);
// $rows=mysqli_fetch_array($res);
// $totalstatus=$rows['countstatus'];

// $sql="select *,count(status) as countstatus1 from rrsv_student_registration where status='Inactive' ";
// $res=mysqli_query($myDB,$sql);
// $rows=mysqli_fetch_array($res);
// $totalstatus1=$rows['countstatus1'];

// $sql="select *, count(add_status) as countstatus2 from rrsv_student_registration where add_status='New Admission' ";
// $res=mysqli_query($myDB,$sql);
// $rows=mysqli_fetch_array($res);
// $totalstatus2=$rows['countstatus2'];

// $sql="select *,count(add_status) as countstatus3 from rrsv_student_registration where add_status='Readmission' ";
// $res=mysqli_query($myDB,$sql);
// $rows=mysqli_fetch_array($res);
// $totalstatus3=$rows['countstatus3'];
?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='printregister.php';
    document.frmsearch.submit();
    return true;
  }
     </script>
<!doctype html>
<html >
  <head>
    <!-- Required meta tags -->
    
    <!-- Bootstrap CSS -->
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link href="css/font-awesome.min.css" rel="stylesheet" />

    <title>Status Report</title>
    <link rel="shortcut icon" href="libray/images/logo.jpeg" />
    <link href="css/registerbooks.css" rel="stylesheet"/>
    <link href="css/font-awesome.min.css" rel="stylesheet"/>

  </head>
  <body>
  <button type="button" id="backPageButton" class="btn btn-info"><a style="color: #fff" href="manage_status.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="btn btn-info" id="printPageButton">Print</button>
  
    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <div class="row">
          <div class="col-lg-12">
            <h3 class="page-header"></h3>
            </div>
        </div>
        <!-- Form validations -->
        <div class="row">
          <div class="col-lg-12">
            <section class="panel">
             <!-- <header class="panel-heading">
              Manage Admission
              </header>-->
              <div class="panel-body">
              
                  <form name="frmsearch" method="" action="" onsubmit="javascript: return dosearch();">
                  <table width="100%" border="0" cellspacing="1" cellpadding="1">
                 
    
                      <tr>
                        <td>&nbsp;</td>
                      </tr>				
                      <div class="income">
                      <form method='post' action=''>
					   <input type="hidden" name="txtsearch" class="srinput" value="" size="30" maxlength="100"  placeholder="Search By Student Name">
      <input type='hidden' class='srinputd' name='fromDate' value='<?php if(isset($_POST['fromDate'])) echo $_POST['fromDate']; ?>'>
 
     <input type='hidden' class='srinputd' name='endDate' value='<?php if(isset($_POST['endDate'])) echo $_POST['endDate']; ?>'>
   </form>
</div>
                        </table>
                    </form>
        <div class="a">
				   <h2>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2>
                    <p style="font-weight: bold;">Reg. No. : SO196094, U-DISE Code : 19251610302, ESTD : 2012;   Baidyadanga, Rasulpur, Memari, Purba Bardhaman, Pin -713151</p>
            <!--<h3>Baidyadanga, Rasulpur, Burdwan</h3>-->
            <!--<h4>Reg. No. - S/1L/96094</h4>-->
             <p style="font-weight: bold;">Status Report For Start Date: <?php if($fromDate==''){
		    
		}
		else{
		echo date('d-m-y',strtotime($fromDate));
		}
		?> and End Date: 
		<?php
		if($endDate==''){
		    
		}else{
		echo date('d-m-y',strtotime($endDate));
		}
		?>
		</p>
			</div>
		<!--<div class="status">-->
		    		

		<!--</div>-->
		<h5 style="margin-left: 74px;">
		<br>
		Total No.of Student:<p id="totalstatus"></p>
		<br>
		Admission Status:<?php echo $status;?><br>Total:<p id="totalstatus"></p>
        Status:<?php echo $add_status;?><br>Total:<p id="totalstatus"></p> 
</h5>
     <table border="1">
  <tr> 
                <th width="2%">SL.<br>NO</th>
                <th width="2%">REG.<br>NO</th>
                <th width="2%">DATE OF ADMISSION</th>
                 <th width="2%">PHOTO</th>
                <th width="2%">STUDENT NAME</th>
                 <th width="2%">CLASS</th>
                 <th width="2%">SECTION</th>
                 <th width="2%">ROLL No</th>
                <th width="2%">DATE OF BIRTH</th>
				<th width="2%">GENDER</th>
                <th width="2%">CAST</th>
               	<th width="2%">FATHER'S NAME</th>
				<th width="2%">MOTHER'S NAME</th>
				<th width="2%">PHONE</th>
				<th width="2%">Alt PHONE</th>
               <th width="2%">ADDRESS</th>
               <th width="2%">Admission Type</th>
               <th width="2%">Status</th>
                 <th width="2%">Status Date</th>
				<tr>
                           
    
        <?php

        //                     if($status != "" && $add_status != "" && $fromDate == "" && $endDate == "") {
        //                         $sql="select * from rrsv_student_registration where status='".$status."' and  add_status='".$add_status."'";
                               
        //                     }

        //                     if($status != "" && $add_status != "" && $fromDate != "" && $endDate != "") {
        //                         $sql="select * from rrsv_student_registration where status='".$status."' and  add_status='".$add_status."' and (scl_date between '".$fromDate."' and '".$endDate."')";
                               
        //                     }
                         
		      //              if($status!="" && $add_status == "" && $fromDate == "" && $endDate == "") {
        //                         $sql="select * from rrsv_student_registration where status='".$status."'";
                              
        //                     }
                            
					   //     if($status=="" && $add_status != "" && $fromDate == "" && $endDate == "") {
        //                         $sql="select * from rrsv_student_registration where add_status='".$add_status."'";
                               
        //                     }
                            
        //                      if($status!="" && $add_status == "" && $fromDate != "" && $endDate != "") {
        //                         $sql="select * from rrsv_student_registration where status='".$status."' and (scl_date between '".$fromDate."' and '".$endDate."')";
                               
        //                     }
                            
					   //     if($status=="" && $add_status != "" && $fromDate != "" && $endDate != "") {
        //                         $sql="select * from rrsv_student_registration where add_status='".$add_status."' and (scl_date between '".$fromDate."' and '".$endDate."')";
                                
        //                     }
				    //   if($status=="" && $add_status == "" && $fromDate != "" && $endDate != "") {
        //                         $sql="select * from rrsv_student_registration where (scl_date between '".$fromDate."' and '".$endDate."')";
                                
        //                     }
				     
				       $sql="select * from rrsv_student_registration where 1";
				       if($status != "" && $add_status != "" && $fromDate == "" && $endDate == "") {
				         $sql.=" and (status='".$status."' and  add_status='".$add_status."')";   
				       }   
				    if($status != "" && $add_status != "" && $fromDate != "" && $endDate != "") {
                                $sql.=" and ( status='".$status."' and  add_status='".$add_status."' and (scl_date between '".$fromDate."' and '".$endDate."'))";
                               
                            }
                         
		                    if($status!="" && $add_status == "" && $fromDate == "" && $endDate == "") {
                                $sql.=" and (status='".$status."')";
                              
                            }
                            
					        if($status=="" && $add_status != "" && $fromDate == "" && $endDate == "") {
                                $sql.=" and (add_status='".$add_status."')";
                               
                            }
                            
                             if($status!="" && $add_status == "" && $fromDate != "" && $endDate != "") {
                                $sql.=" and ( status='".$status."' and (scl_date between '".$fromDate."' and '".$endDate."'))";
                               
                            }
                            
					        if($status=="" && $add_status != "" && $fromDate != "" && $endDate != "") {
                                $sql.="and add_status='".$add_status."' and (scl_date between '".$fromDate."' and '".$endDate."'))";
                                
                            }
				    //   if($status=="" && $add_status == "" && $fromDate != "" && $endDate != "") {
        //                         $sql.="and (scl_date between '".$fromDate."' and '".$endDate."')";
                                
        //                     } 
                               if( $fromDate != "" && $endDate != "") {
                                $sql.=" and (scl_date between '".$fromDate."' and '".$endDate."')";
                               
                            }
				     $res=mysqli_query($myDB,$sql);
                    $totalstatus=mysqli_num_rows($res);
				     
				     
				     
while ($rows=mysqli_fetch_array($res,MYSQLI_ASSOC))
{
$c++;

?>   
 <th width="2%"><?php echo $c;?></th>
                <th width="2%"><?=$rows['scl_reg_no'];?></th>
                 <th width="2%"><?php echo date('d-m-Y',strtotime($rows['scl_date']));?></th>
                 <th width="2%"><img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" /></th>
                  <th width="2%"><?=$rows['scl_name'];?></th>
                <th width="2%"><?=$rows['scl_class'];?></th>
				<th width="2%"><?=$rows['scl_section'];?></th>
				<th width="2%"><?=$rows['scl_roll_no'];?></th>
                <!--<th width="2%"><?=$rows['scl_category'];?></th>-->
                <th width="2%"><?php echo date('d-m-y',strtotime($rows['scl_dob']));?></th>
          		<th width="2%"><?=$rows['scl_gender'];?></th>
				<th width="2%"><?=$rows['scl_religion'];?></th>
               <th width="2%"><?=$rows['scl_father_name'];?></th>
               <th width="2%"><?=$rows['scl_mother_name'];?></th>
            	<th width="2%"><?=$rows['scl_phone_no'];?></th>
           	 	<th width="2%"><?=$rows['alt_phone'];?></th>
           	 		<th width="2%">
     		    Vill:<?=$rows['scl_address'];?><br>
                Post Office:<?=$rows['scl_pos'];?><br>
                Police Station:<?=$rows['scl_pol'];?><br>
           	 	Block:<?=$rows['scl_block'];?><br>
           	 	Gram Panchayat / Municipality:<?=$rows['scl_mu'];?>
           	 	Locality:<?=$rows['scl_location'];?><br>
           	 	District:<?=$rows['scl_dist'];?><br>
           	 	State:<?=$rows['scl_pin'];?>
           	 	Pin:<?=$rows['scl_pin'];?></th>
           	 		
		  <!--<th width="2%"><?=date('d-m-Y',strtotime($rows['scl_date']));?></th>-->
				
				<!--<th width="2%"><img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" /></th>-->
				 <th width="2%"><?=$rows['add_status'];?></th>
				 <th width="2%"><?=$rows['status'];?></th>
				   <th width="2%"><?php echo date('d-m-y',strtotime($rows['s_a_d']));?></th>
				<tr>      
        <?php
}
?>
</table>
</div>
 
                   </div>
              </div>
            </section>
          </div>
        </div>
       
        <!-- page end-->
      </section>
    </section>
    <!--main content end-->
   
  </section>
  <!-- container section end -->

  <!-- javascripts -->
  <script src="js/jquery.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <!-- nice scroll -->
  <script src="js/jquery.scrollTo.min.js"></script>
  <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
  <!-- jquery validate js -->
  <script type="text/javascript" src="js/jquery.validate.min.js"></script>

  <!-- custom form validation script for this page-->
  <script src="js/form-validation-script.js"></script>
  <!--custome script for all page-->
  <script src="js/scripts.js"></script>
  <script>
    $(document).ready(function () {
        alert();
      
 });

  </script>

  <script>
  
  
    
  
  document.getElementById("totalstatus").innerHTML = '<?php echo $totalstatus; ?>';
    document.getElementById("totalstatus1").innerHTML = '<?php echo $totalstatus; ?>';
  
		function generatePDF() {
		window.print();
		}
		    function cancel() {
		        window.history.go(-1);
		    }
    </script>
</body>

</html>
