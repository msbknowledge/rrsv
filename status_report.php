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
session_start();
include('include/dbcon.php');

$status_report="of students ";
// if($status != "") {
//     $status_report .= "and student status for ".$status;
// }

// print_r($_GET); 
// die();
//Array ( [student_status] => Active [admission_status] => New Admission [scl_session] => 2027 [unit] => KINDER GARDEN )
$status="";
$add_status="";
$scl_session="";
$scl_class="";

 $status                =$myDB->escape_string(trim(addslashes($_GET['student_status'])));
 $add_status            =$myDB->escape_string(trim(addslashes($_GET['admission_status'])));
 $scl_session              =$myDB->escape_string(trim(addslashes($_GET['scl_session'])));
 $scl_class               =$myDB->escape_string(trim(addslashes($_GET['unit'])));
// $_SESSION['status']=$status;
// $_SESSION['add_status']=$add_status;
// $_SESSION['scl_session']=$scl_session;
// $_SESSION['scl_class']=$scl_class;
?>

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
  <!--<a href="csv.php" class="btn btn-info">Export</a>-->
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
             <p style="font-weight: bold;">Status Report <span  id="status_report"></span></p>

		
	<p style="font-weight: bold;margin-left: -35px;"">	Total No. of Student:</p><p id="totalstatus" style="
    margin-left: 174px;
    margin-top: -40px;font-weight: bold;
"></p>
		
			
		<!--<div class="status">-->
		    		

		<!--</div>-->
		<!--<h5 style="margin-left: 74px;">-->
		<!--<br>-->
		<!--Total No.of Student:<p id="totalstatus"></p>-->
		<!--<br>-->
		<!--Admission Status:<?php echo $status;?><br>Total:<p id="totalstatus"></p>-->
  <!--      Status:<?php echo $add_status;?><br>Total:<p id="totalstatus"></p> -->
</h5>
     <table border="1">
  <tr> 
                <th width="10%">SL. NO</td>
                <th width="10%">REG. NO </th>
                <th width="10%">DATE OF ADMISSION </th>
                 <th width="10%">PHOTO</th>
                <th width="10%">STUDENT NAME</th>
                 <th width="10%">CLASS</th>
                 <th width="10%">SECTION</th>
                 <th width="10%">ROLL NO</th>
                <th width="10%">DATE OF BIRTH</th>
				<th width="10%">GENDER</th>
                <th width="10%">CAST</th>
               	<th width="10%">FATHER'S NAME</th>
				<th width="10%">MOTHER'S NAME</th>
				<th width="10%">PHONE NO</th>
				<th width="10%">WhatsApp NO</th>
               <th width="10%">ADDRESS</th>
               <th width="10%">ADMISSION TYPE</th>
               <th width="10%">STATUS</th>
                 <th width="10%">STATUS DATE</th>
				<tr>
                           
    
        <?php
  
				       $sql="select * from rrsv_student_registration where 1";
				       //////////////////////////////// using singl search ///////////////////////////////////////////
				        if($scl_session!="" && $status == "" && $add_status == "" && $scl_class == "") {
                                $sql.=" and (scl_session='".$scl_session."')";
                                $status_report .= "and session for ".$scl_session;
                        }
                        if($scl_class!="" && $status == "" && $add_status == "" && $scl_session == "") {
                                $sql.=" and (scl_class='".$scl_class."')";
                                $status_report .= "and Class for ".$scl_class;
                        }
                        if($add_status!="" && $status == "" && $scl_session == "" && $scl_class == "") {
                                $sql.=" and (add_status='".$add_status."')";
                                $status_report .= "and Student Admination status for ".$add_status;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session == "" && $scl_class == "") {
                                $sql.=" and (status='".$status."')";
                                $status_report .= "and Student status for ".$status;
                        }
                        //////////////////////////////// using singl search ///////////////////////////////////////////

                        //////////////////////////////// using session ///////////////////////////////////////////
                        if($status=="" &&  $add_status== "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and session for ".$scl_session." and Class for ".$scl_class;
                        }
                        if($status=="" &&  $add_status!= "" && $scl_session != "" && $scl_class == "") {
                                $sql.=" and (add_status='".$add_status."' and  scl_session='".$scl_session."')";
                                $status_report .= " and Admination status for ".$status." and session for ".$scl_class;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session != "" && $scl_class == "") {
                                $sql.=" and (status='".$status."' and  scl_session='".$scl_session."')";
                                $status_report .= " and Student Status for ".$status." and session for ".$scl_session;
                        }
                        if($status=="" &&  $add_status!= "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (add_status='".$add_status."' and  scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Admination Status for ".$add_status." and session for ".$scl_session." and class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (status='".$status."' and  scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Student Status for ".$status." and session for ".$scl_session." and class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status!= "" && $scl_session != "" && $scl_class == "") {
                                $sql.=" and (status='".$status."' and  scl_session='".$scl_session."' and  add_status='".$add_status."')";
                                $status_report .= "and Student Status for ".$status." and session for ".$scl_session." and Admination status for ".$add_status;

                        }
                        //////////////////////////////// using session //////////////////////////////////////////
                        
                        //////////////////////////////// using class ///////////////////////////////////////////
                        if($status=="" &&  $add_status!= "" && $scl_session == "" && $scl_class != "") {
                                $sql.=" and (add_status='".$add_status."' and  scl_class='".$scl_class."')";
                                $status_report .= " and Admination Status for ".$add_status." and Class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session == "" && $scl_class != "") {
                                $sql.=" and (status='".$status."' and  scl_class='".$scl_class."')";
                                $status_report .= " and Student Status for ".$status." and Class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status!= "" && $scl_session == "" && $scl_class != "") {
                                $sql.=" and (status='".$status."' and  add_status='".$add_status."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Student Status for ".$status." and Admination status for ".$add_status." and class for ".$scl_class;
                        }
                        //////////////////////////////// using class //////////////////////////////////////////
                        
                        //////////////////////////////// using admination status ///////////////////////////////////////////
                        if($status!="" &&  $add_status!= "" && $scl_session == "" && $scl_class == "") {
                                $sql.=" and (add_status='".$add_status."' and  status='".$status."')";
                                $status_report .= " and Admination Status for ".$add_status." and Student Status for ".$status;
                        }
                        //////////////////////////////// using admination status //////////////////////////////////////////
                        
                        //////////////////////////////// using all filds ///////////////////////////////////////////
                        if($status!="" &&  $add_status!= "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (add_status='".$add_status."' and  status='".$status."' and  scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Admination Status for ".$add_status." and Student status for ".$status." and Session for ".$scl_session." and Class for ".$scl_class;

                        }
                        //////////////////////////////// using all filds //////////////////////////////////////////
                        
                            // echo $sql;
                            // die();
                              $sql .="  order by id desc";
				     $res=mysqli_query($myDB,$sql);
                    $totalstatus=mysqli_num_rows($res);
				     
				     
				     
while ($rows=mysqli_fetch_array($res,MYSQLI_ASSOC))
{
$c++;

?>   
 <th width="10%"><?php echo $c;?></td>
                <td width="10%"><?=$rows['scl_reg_no'];?></td>
                 <td width="10%"><?php echo date('d-m-Y',strtotime($rows['scl_date']));?></td>
                 <td width="10%"><img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" /></td>
                  <td width="10%"><?=$rows['scl_name'];?></td>
                <td width="10%"><?=$rows['scl_class'];?></td>
				<td width="10%"><?=$rows['scl_section'];?></td>
				<td width="10%"><?=$rows['scl_roll_no'];?></td>
                <!--<td width="10%"><?=$rows['scl_category'];?></td>-->
                <td width="10%"><?php echo date('d-m-y',strtotime($rows['scl_dob']));?></td>
          		<td width="10%"><?=$rows['scl_gender'];?></td>
				<td width="10%"><?=$rows['scl_religion'];?></td>
               <td width="10%"><?=$rows['scl_father_name'];?></td>
               <td width="10%"><?=$rows['scl_mother_name'];?></td>
            	<td width="10%"><?=$rows['scl_phone_no'];?></td>
           	 	<td width="10%"><?=$rows['alt_phone'];?></td>
           	 		<td width="10%">
     		    VILL: <?=$rows['scl_address'];?>,<br>
                POST OFFICE: <?=$rows['scl_pos'];?>,<br>
                POLICE STATION: <?=$rows['scl_pol'];?>,<br>
           	 	BLOCK: <?=$rows['scl_block'];?>,<br>
           	 	GRAM PANCHAYAT/ MUNICIPALITY: <?=$rows['scl_mu'];?>,
           	 	LOCALITY: <?=$rows['scl_location'];?>,<br>
           	 	DISTRICT: <?=$rows['scl_dist'];?>,<br>
           	 	STATE: <?=$rows['scl_state'];?>,<br>
           	 	PIN: <?=$rows['scl_pin'];?></td>
           	 		
		  <!--<td width="10%"><?=date('d-m-Y',strtotime($rows['scl_date']));?></td>-->
				
				<!--<td width="10%"><img src="student_reg_image/<?=$rows['image'];?>" width="60" height="60" /></td>-->
				 <td width="10%"><?=$rows['add_status'];?></td>
				 <td width="10%"><?=$rows['status'];?></td>
				   <td width="10%"><?php echo date('d-m-y',strtotime($rows['s_a_d']));?></td>
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
  <script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='printregister.php';
    document.frmsearch.submit();
    return true;
  }
     </script>
  <script>
    $(document).ready(function () {
        alert();
      
 });

  </script>

  <script>
  
  
    
    document.getElementById("status_report").innerHTML = '<?php echo $status_report; ?>';

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
