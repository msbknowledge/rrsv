<?php
// print_r($_POST);
// die();
?>

<style>
/*Not Required*/


/*Required*/
@media (max-width: 576px){.modal-dialog.modal-dialog-slideout {width: 80%}}
.modal-dialog-slideout {min-height: 100%; margin: 0 auto 0 0 ;background: #fff;}
.modal.fade .modal-dialog.modal-dialog-slideout {-webkit-transform: translate(-100%,0);transform: translate(-100%,0);}
.modal.fade.show .modal-dialog.modal-dialog-slideout {-webkit-transform: translate(0,0);transform: translate(0,0);flex-flow: column;}
.modal-dialog-slideout .modal-content{border: 0;}

.modal-header h4{
	color:#fff;
	font-size:20px;
	font-weight:600;
	letter-spacing: 1px;
}
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
.button {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
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


 $fromDate=$myDB->escape_string(trim(addslashes($_POST['fromDate'])));
 $endDate=$myDB->escape_string(trim(addslashes($_POST['endDate'])));
 $purpose=$myDB->escape_string(trim(addslashes($_POST['purpose']))); 
 
 $session=$myDB->escape_string(trim(addslashes($_POST['session'])));
 $scl_class=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));

?>
 <link rel="shortcut icon" href="libray/images/logo.jpeg" />
  <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">-->
  <!--<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>-->
  <!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>-->
  <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>-->
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
   
    <link href="css/moneybook.css" rel="stylesheet"/>
    <link href="css/font-awesome.min.css" rel="stylesheet"/>
 <link rel="shortcut icon" href="libray/images/logo.jpeg" />
    <title>Income/Expense Report</title>

  </head>
  <body>
  <button type="button" id="backPageButton" class="button"><a style="color: #fff" href="manage_expenditure.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="button" id="printPageButton">Print</button>
 
  
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
<p style="font-weight: bold;">Income & Expense Report For Start Date : <?=date('d-m-Y',strtotime($fromDate));?> and End Date : <?=date('d-m-Y',strtotime($endDate));?></p>
   <p style="font-weight: bold;">Purpose : <?php echo $purpose;?></p>
			</div>  
      <div>
          
       
      </div>
    <table border=1 width=80% style="
    margin-left: 126px;
">
<tr>
    <td  style="width:10%" >Sl.No</td>
     <td  style="width:10%" >Bill No</td>
     <td  style="width:10%" >Session</td>
    <td  style="width:10%" >Bill Date</td>
    <td  style="width:10%">Purpose for Income</td>
     <td  style="width:10%">Income</td>
       <td  style="width:10%">Purpose for Expence</td> 
     <td style="width:10%">Expence</td>
</tr>
<?php
$c=0;
$sql="select * from rrsv_scl_pl where 1 ";
// SELECT *  FROM `rrsv_scl_pl` WHERE `pl_date` BETWEEN '2022-05-01' AND '2022-11-20' AND `purpose` = 'BOOKS ' ORDER BY `pl_date` ASC
if($fromDate!="" && $endDate!="" && $session=="" && $purpose=="")
                        {
                      $sql.=" and  pl_date between '".$fromDate."' and '".$endDate."' order by pl_date ASC";
                        } 
  if($fromDate!="" && $endDate!="" && $session!="" && $purpose=="")
                        {
                      $sql.=" and  pl_date between '".$fromDate."' and '".$endDate."' and session='".$session."' order by pl_date ASC";
                        } 
                  if($fromDate!="" && $endDate!="" && $session=="" && $purpose!="")
                        {
                      $sql.="  and  pl_date between '".$fromDate."' and '".$endDate."' and purpose='".$purpose."' order by pl_date ASC";
                        }
                        
                      if($fromDate!="" && $endDate!="" && $session!="" && $purpose!="")
                        {
                      $sql.="  and  pl_date between '".$fromDate."' and '".$endDate."' and session='".$session."' and purpose='".$purpose."' order by pl_date ASC";
                        }
  		        // echo  $sql;
  		        // die();
                        
  		         
                     
 $res=mysqli_query($myDB,$sql);
 while($rows=mysqli_fetch_array($res)){
    $incomeamount=$incomeamount+$rows['income_amount'];
     $expamount=$expamount+$rows['exp_amount'];
     $c++;
     ?>
   <tr>
                         <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c;?></td>
                          <td class="text" style="padding-left:10px;" valign="center"><?=$rows['bill'];?></td>
                          <td class="text" style="padding-left:10px;" valign="center"><?=$rows['session'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?=date('d-m-Y',strtotime($rows['pl_date']));?></td>
                         <td  class="text" valign="center" style="padding-left:10px;">
                             
                             <?php
                             if($rows['income_amount']==0){
                          echo $rows['purpose'];
                             }
                             else{
                               echo $rows['purpose'];
                             }
                             ?>
                             </td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['income_amount'];?></td>
                         <td  class="text" valign="center" style="padding-left:10px;"><?php
                             if($rows['exp_amount']==0){
                         
                             }
                             else
                             {
                               echo $rows['purpose'];   
                             }
                             ?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['exp_amount'];?></td>   
<?php     
 }
?>
<tr>
    <td>Total Cash in Hand:<?php echo $cash=$incomeamount-$expamount;?>
    
    </td>
    <td></td>
    <td></td>
        <td></td>
        <td></td>
    <td><?php echo $incomeamount;?></td>
    <td></td>
        <td><?php echo $expamount;?></td>
    
</tr>
<table>



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
		function generatePDF() {
		window.print();
		}
		    function cancel() {
		        window.history.go(-1);
		    }
    </script>
</body>

</html>
