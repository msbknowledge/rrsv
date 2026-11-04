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
session_start();
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
 $scl_session=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
 $scl_class=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
 
$_SESSION['fromDate']=$fromDate;
$_SESSION['endDate']=$endDate;
$_SESSION['scl_session']=$scl_session;
$_SESSION['scl_class']=$scl_class;

?>
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
    <title>Money Registra Book Report</title>

  </head>
  <body>
  <button type="button" id="backPageButton" class="button"><a style="color: #fff" href="manage_print.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="button" id="printPageButton">Print</button>
  <a href="csv.php" class="button">Export</a>
  
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
                    <!--<p>Money Registra Book Repory</p>-->
                    <p style="font-weight: bold;">Money Report For Start Date :<?=date('d-m-Y',strtotime($fromDate));?> and End Date :<?=date('d-m-Y',strtotime($endDate));?></p>
			</div>  

    <table border=1>
<tr>
    <td rowspan="2" style="width:10%" >Sl.No</td>
    <td rowspan="2" style="width:10%" >Reg.No</td>
    <td rowspan="2" style="width:10%">Roll.No</td>
     <td rowspan="2" style="width:10%">Name</td>
     <td colspan="2"style="width:10%">Admission</td>
    <td colspan="7" style="width:10%">Admission Charges</td>
     <td colspan="6" style="width:10%">Books</td>
     <td colspan="6" style="width:10%">Copy</td>
     <td colspan="6" style="width:10%">Tution Fee</td>
   
     <td rowspan="2" style="width:10%">Total Car Cost(P.A)</td>
       <td rowspan="2" style="width:10%"> Car Cost Paid(P.M)</td>
    <td rowspan="2" style="width:10%">Total Amount Due for Car</td> 
  <td rowspan="2" style="width:10%">Total Gross Amount(With Car) </td>
   <td rowspan="2" style="width:10%">Total Gross Amount(with Out Car) </td>
        <td rowspan="2" style="width:10%">Total Bill Amount </td>        
       <td rowspan="2" style="width:10%">Total Payable Amount </td>
  <td rowspan="2" style="width:10%"> After Billing Advance Amount</td>
 <td rowspan="2" style="width:10%">After Billing Due Amount </td>
    <td rowspan="2" style="width:10%">Total Amount Due(with Car) </td>
        <td rowspan="2" style="width:10%">Total Amount Due(With out Car) </td>
  <td rowspan="2" style="width:10%">Statement Amount </td>
  <td rowspan="2" style="width:10%">Statement  Due Amount </td>
</tr>

<tr>
    
    <td >New Admission</td>
    <td >ReAdmission</td>
    <td >Uniform</td>
    <td >Shoe</td>
     <td >Winter</td>
    <td >Admission Form</td>
     <td >Bag</td>
    <td >I.card</td>
    <td>Diary</td>
    <td >NR</td>
    <td >KG</td>
     <td >STD-I</td>
    <td >STD-II</td>
     
    <td >STD-III</td>
    <td >STD-IV</td>
    <td >NR</td>
    <td >KG</td>
     <td >STD-I</td>
    <td >STD-II</td>
     
    <td >STD-III</td>
    <td >STD-IV</td>
     <td >NR</td>
    <td >KG</td>
     <td >STD-I</td>
    <td >STD-II</td>
     
    <td >STD-III</td>
    <td >STD-IV</td>
    </tr>

    <?php
         $bgcolor="";
  $c=0;
 
 //echo   $sql="select a.*,b.monthly_fee_val,b.payment_recive,b.car_cost,b.icard_val,b.admission_fee_val,b.re_admission_fee_val,b.uniform_val,b.shoes_sockes_val,b.admission_val,b.bag_val,b.book_cost,b.copy_cost from rrsv_student_registration a left join rrsv_scl_studen_fee b on a.id=b.pay_id and b.scl_date between '".$fromDate."' and '".$endDate."' ";
 $sql="select a.*,b.scl_date,b.last_advance_due,b.payment_recive from  rrsv_student_registration as a, rrsv_scl_studen_fee as b where  b.scl_date between '".$fromDate."' and '".$endDate."' and a.id=b.pay_id group by a.id " ;
//echo $sql="select a.*,b.scl_name,b.re_admission_fee_val,b.admission_fee_val from rrsv_student_registration as a,rrsv_scl_studen_fee as b where b.scl_class='".$scl_class."' and b.scl_session='".$scl_session."' and b.scl_date between '".$fromDate."' and '".$endDate."' group by a.scl_name ";
// echo $sql="select *, distinct scl_name from rrsv_scl_studen_fee  where scl_class=scl_class and scl_session=scl_session and scl_date between '".$fromDate."' and '".$endDate."'";

  $result1=mysqli_query($myDB,$sql);
  $c=0;
while ($rows=mysqli_fetch_array($result1))
{

  if($bgcolor=='#cfffaa')
    {$bgcolor='#FFFFFF';} 
  else
    {$bgcolor='#cfffaa';
     
    }
    
    
    /* Total Calculation */
$totaladmission_fee_val                 =$totaladmission_fee_val+$rows['admission_fee_val']; 
$totalreadmission_fee_val               =$totalreadmission_fee_val+$rows['re_admission_fee_val']; 
$totaluniformval                        =$totaluniformval+ $rows['uniformval'];
$totalshoessockesval                    =$totalshoessockesval+ $rows['shoessockesval'];
$totalsweaterslaxcap_val                =$totalsweaterslaxcap_val+$rows['sweaterslaxcap_val'];    
$totaladmissionval                      =$totaladmissionval+$rows['admissionval'];
$totalbagval                            =$totalbagval+$rows['bagval'];
$totalicardval                          =$totalicardval+$rows['icardval'];
$totaldiaryval                          =$totaldiaryval+$rows['diaryval'];
$totalcar                               =$totalcar+$carfee;


$totalcarcost                           =$rows['car_cost']*12; /*calculation for total car cost*/    
$totalcar                               =$totalcar+$rows['carfee'];  /* Calculation for sum of totla car cost*/
$totalcardue                            =$totalcarcost-$rows['carfee']; /* Calcutation for Total amount due for car*/
$add_total_cost                         =$rows['total_monthly_fee']+$rows['add_total_cost']+$totalcarcost; /* Total Gross amount with car New Admission*/
 $re_total_cost                          =$rows['total_monthly_fee']+$rows['re_total_cost']+$totalcarcost;  /* Total Gross amount with car Re Admission*/
$add_total_cost1                        =$rows['total_monthly_fee']+$rows['add_total_cost']; /* Total Gross amount with out car New Admission*/
$re_total_cost1                         =$rows['total_monthly_fee']+$rows['re_total_cost'];  /* Total Gross amount with out car Re Admission*/ 
$addtotalpay                            =$rows['admission_fee_val']+$rows['uniformval']+$rows['shoessockesval']+$rows['sweaterslaxcap_val']+$rows['admissionval']+$rows['bagval']+$rows['icardval']+$rows['diaryval']+$rows['bookcost']+$rows['copycost']+$rows['totalmonthly_fee_val']+$rows['carfee'];
$readdtotalpay                          =$rows['re_admission_fee_val']+$rows['uniformval']+$rows['shoessockesval']+$rows['sweaterslaxcap_val']+$rows['admissionval']+$rows['bagval']+$rows['icardval']+$rows['diaryval']+$rows['bookcost']+$rows['copycost']+$rows['totalmonthly_fee_val']+$rows['carfee'];       



$totaladddue                            =$add_total_cost-$addtotalpay; /*Total Amount Due(with Car) New Admission*/ 
$totalreadddue                          =$re_total_cost-$readdtotalpay;/*Total Amount Due(with Car) Re Admission*/ 
$totaladddue1                           =$add_total_cost1-$addtotalpay;/*Total Amount Due(with out Car) New Admission*/ 
$totalreadddue1                         =$re_total_cost1-$readdtotalpay;/*Total Amount Due(with out Car) Re Admission*/
/*  Calculation for After Billing Advance/Due Amount */ 
 if($rows['val_ad_du']=='Advance'){
 $totaladvance=$totaladvance+ $rows['advance_due'];
}
else  if($rows['val_ad_du']=='Due'){
 $totaladvancedue=$totaladvancedue+ $rows['advance_due'];  
}
 
/* Satelement value calculation   */
$newadsatelementamount                  =abs($rows['amount1']-$addtotalpay);
$readsatelementamount                  =abs($rows['amount1']-$readdtotalpay);
$satelmentamount                        =$satelmentamount+$rows['amount1'];
$totasamount=$totasamount+$rows['amount1'];

$c++;


?>
<tr bgcolor="<?php echo $bgcolor;?>" >
<td widtd="2%" ><?php echo $c;?></td>
<td widtd="2%" ><?=$rows['scl_reg_no'];?></td>
<td widtd="2%" ><?=$rows['scl_roll_no'];?></td>
 <td widtd="2%"><?=$rows['scl_name'];?></td>
 <td widtd="2%"><?=$rows['admission_fee_val'];?></td>
  <td widtd="2%"><?=$rows['re_admission_fee_val'];?></td>
  <td widtd="2%"><?=$rows['uniformval'];?></td>
  <td widtd="2%"><?=$rows['shoessockesval'];?></td>
  <td widtd="2%"><?=$rows['sweaterslaxcap_val'];?></td>
  <td widtd="2%"><?=$rows['admissionval'];?></td>
  <td widtd="2%"><?=$rows['bagval'];?></td>
  <td widtd="2%"><?=$rows['icardval'];?></td>
  <td widtd="2%"><?=$rows['diaryval'];?></td>
  <?php
  if($rows['scl_class']=='NURSERY'){
      
       $dd=$rows['scl_class'];
       $rr1=$rr1+$rows['bookcost'];
  ?>
   <td widtd="2%"><?=$rows['bookcost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($rows['scl_class']=='KINDER GARDEN'){
       $dd1=$rows['scl_class'];
        $rr2=$rr2+$rows['bookcost'];
  ?>
  <td widtd="2%"><?=$rows['bookcost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($rows['scl_class']=='STANDERD 1'){
        $dd2=$rows['scl_class'];
        $rr3=$rr3+$rows['bookcost'];
       ?>
  <td widtd="2%"><?=$rows['bookcost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($rows['scl_class']=='STANDERD 2'){
       $dd3=$rows['scl_class'];
       $rr4=$rr4+$rows['bookcost'];
       ?>
  <td widtd="2%"><?=$rows['bookcost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
 if($rows['scl_class']=='STANDERD 3'){
      $dd4=$rows['scl_class'];
      $rr5=$rr5+$rows['bookcost'];
       ?>
  <td widtd="2%"><?=$rows['bookcost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($rows['scl_class']=='STANDERD 4'){
       $dd5=$rows['scl_class'];
       $rr6=$rr6+$rows['bookcost'];
       ?>
  <td widtd="2%"><?=$rows['bookcost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  ?>
    <?php
  if($rows['scl_class']=='NURSERY'){
      $sclclass1=$rows['scl_class'];
      $totalcost1=$totalcost1+$rows['copycost'];
  ?>
<td widtd="2%"><?=$rows['copycost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($rows['scl_class']=='KINDER GARDEN'){
       $sclclass2=$rows['scl_class'];
     $totalcost2=$totalcost2+$rows['copycost'];
  ?>
 <td widtd="2%"><?=$rows['copycost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($rows['scl_class']=='STANDERD 1'){
       $sclclass3=$rows['scl_class'];
      $totalcost3=$totalcost3+$rows['copycost'];
       ?>
 <td widtd="2%"><?=$rows['copycost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($rows['scl_class']=='STANDERD 2'){
      $sclclass4=$rows['scl_class'];
      $totalcost4=$totalcost4+$rows['copycost'];
       ?>
<td widtd="2%"><?=$rows['copycost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
 if($rows['scl_class']=='STANDERD 3'){
     $sclclass5=$rows['scl_class'];
      $totalcost5=$totalcost5+$rows['copycost'];
       ?>
<td widtd="2%"><?=$rows['copycost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($rows['scl_class']=='STANDERD 4'){
      $sclclass6=$rows['scl_class'];
      $totalcost6=$totalcost6+$rows['copycost'];
       ?>
<td widtd="2%"><?=$rows['copycost'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  ?>
    <?php
  if($rows['scl_class']=='NURSERY'){
      $class1=$rows['scl_class'];
      $totalmonthy1=$totalmonthy1+$rows['totalmonthly_fee_val']
  ?>
<td widtd="2%"><?=$rows['totalmonthly_fee_val'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($rows['scl_class']=='KINDER GARDEN'){
       $class2=$rows['scl_class'];
        $totalmonthy2=$totalmonthy2+$rows['totalmonthly_fee_val']
  ?>
 <td widtd="2%"><?=$rows['totalmonthly_fee_val'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($rows['scl_class']=='STANDERD 1'){
       $class3=$rows['scl_class'];
        $totalmonthy3=$totalmonthy3+$rows['totalmonthly_fee_val']
       ?>
  <td widtd="2%"><?=$rows['totalmonthly_fee_val'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($rows['scl_class']=='STANDERD 2'){
      $class4=$rows['scl_class'];
       $totalmonthy4=$totalmonthy4+$rows['totalmonthly_fee_val']
       ?>
  <td widtd="2%"><?=$rows['totalmonthly_fee_val'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
 if($rows['scl_class']=='STANDERD 3'){
     $class5=$rows['scl_class'];
      $totalmonthy5=$totalmonthy5+$rows['totalmonthly_fee_val']
       ?>
 <td widtd="2%"><?=$rows['totalmonthly_fee_val'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($rows['scl_class']=='STANDERD 4'){
      $class6=$rows['scl_class'];
       $totalmonthy6=$totalmonthy6+$rows['totalmonthly_fee_val']
       ?>
  <td widtd="2%"><?=$rows['totalmonthly_fee_val'];?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  ?>
 
  
  <td><?php echo $totalcarcost;
  $totalcarcost1=$totalcarcost1+$totalcarcost;
  ?>
  </td>
  <td><?=$rows['carfee'];?></td>
  <td><?php echo $totalcardue;
  $totalcardue1=$totalcardue1+$totalcardue;
 ?></td>
    
    
    
   <td>
     <?php
     if($rows['add_status']=="New Admission"){
        $addstatus=$rows['add_status']; 
          echo $add_total_cost;
          $addtotalcost1=$addtotalcost1+$add_total_cost;
         
     }
     elseif($rows['add_status']=="Readmission") {
          $addstatus=$rows['add_status']; 
        echo $re_total_cost;
        $readdtotalcost1=$readdtotalcost1+$re_total_cost;
     }
     ?>
     
 </td>
   <td>
     <?php
     if($rows['add_status']=="New Admission"){
        $add_status1= $rows['add_status'];
          echo $add_total_cost1;
         $addtotalcost2=$addtotalcost2+$add_total_cost1;
     }
     else      if($rows['add_status']=="Readmission") {
         $add_status1= $rows['add_status'];
        echo $re_total_cost1;
        $retotalcost1=$retotalcost1+$re_total_cost1;
     }
     ?>
     
 </td>
 
 <td>
     <?php
     if($rows['add_status']=="New Admission"){
         $add_status2= $rows['add_status'];
          echo $addtotalpay;
         $addtotalpay1=$addtotalpay1+$addtotalpay;
         
     }
     else      if($rows['add_status']=="Readmission") {
         $add_status2= $rows['add_status'];
        echo $readdtotalpay;
        $readdtotalpay1=$readdtotalpay1+$readdtotalpay;
     }
     ?>
     
 </td>
 <td><?=$rows['paymentrecive']+$rows['admissionval'];
  $totalpaymentrecive=$totalpaymentrecive+$rows['paymentrecive']+$rows['admissionval'];
  ?></td>
<td><?php if($rows['val_ad_du']=='Advance'){
echo $rows['advance_due'];
}
?>
</td>

<td><?php if($rows['val_ad_du']=='Due'){
echo $rows['advance_due'];
}
?>
</td>

 <td>
     <?php
     if($rows['add_status']=="New Admission"){
          $add_status3= $rows['add_status'];
          echo $totaladddue;
          $totaladddue3=$totaladddue3+$totaladddue;
         
     }
     else      if($rows['add_status']=="Readmission") {
          $add_status3= $rows['add_status'];
        echo $totalreadddue;
        $totalreadddue3=$totalreadddue3+$totalreadddue;
     }
     ?>
     
 </td>
  <td>
     <?php
     if($rows['add_status']=="New Admission"){
         
          echo $totaladddue1;
          $totaladddue4=$totaladddue4+$totaladddue1;
         
     }
     else      if($rows['add_status']=="Readmission") {
       echo $totalreadddue1;
          $totaladddue4=$totaladddue4+$totalreadddue1;
     }
     ?>
     
 </td>
 <td><?=$rows['amount1'];?></td>


 <td>
     <?php
     if($rows['add_status']=="New Admission"){
         $addstatus=$rows['add_status'];
         if($rows['amount1']!=0){
          echo $newadsatelementamount;
        $totalnewadsatelementamount=$totalnewadsatelementamount+$newadsatelementamount;
         }
         else
         {
          echo 0;  
         }

     }
     else      if($rows['add_status']=="Readmission") {
           if($rows['amount1']!=0){
       echo $readsatelementamount;
       $totalreadsatelementamount=$totalreadsatelementamount+$readsatelementamount;
           }
           else{
               echo 0;
           }
        
     }
     ?>
     
 </td>
</tr>
<?php
}
?>
<tr>
    <td >Total----</td>
    <td></td>
    <td></td>
    <td></td>
    <td><?php echo $totaladmission_fee_val;?></td>
     <td><?php echo $totalreadmission_fee_val;?></td>
      <td><?php echo $totaluniformval;?></td>
      <td><?php echo $totalshoessockesval;?></td>
    <td><?php echo $totalsweaterslaxcap_val;?></td>
    <td><?php echo $totaladmissionval;?></td>
    <td><?php echo $totalbagval;?></td>
    <td><?php echo $totalicardval;?></td>
    <td><?php echo $totaldiaryval;?></td>
   <?php
   
  if($dd=='NURSERY'){
     
  ?>
   <td widtd="2%"><?php echo $rr1;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
if($dd1=='KINDER GARDEN'){
  ?>
  <td widtd="2%"><?php echo $rr2;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($dd2=='STANDERD 1'){
       ?>
  <td widtd="2%"><?php echo $rr3;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($dd3=='STANDERD 2'){
       ?>
  <td widtd="2%"><?php echo $rr4;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
 if($dd4=='STANDERD 3'){
       ?>
  <td widtd="2%"><?php echo $rr5;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($dd5=='STANDERD 4'){
       ?>
  <td widtd="2%"><?php echo $rr6;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  ?>
   <?php
   
  if($sclclass1=='NURSERY'){
     
  ?>
   <td widtd="2%"><?php echo $totalcost1;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
if($sclclass2=='KINDER GARDEN'){
  ?>
  <td widtd="2%"><?php echo $totalcost2;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($sclclass3=='STANDERD 1'){
       ?>
  <td widtd="2%"><?php echo $totalcost3;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($sclclass4=='STANDERD 2'){
       ?>
  <td widtd="2%"><?php echo $totalcost4;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
 if($sclclass5=='STANDERD 3'){
       ?>
  <td widtd="2%"><?php echo $totalcost5;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($sclclass6=='STANDERD 4'){
       ?>
  <td widtd="2%"><?php echo $totalcost6;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  ?>
     <?php
   
  if($class1=='NURSERY'){
     
  ?>
   <td widtd="2%"><?php echo $totalmonthy1;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
if($class2=='KINDER GARDEN'){
  ?>
  <td widtd="2%"><?php echo $totalmonthy2;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
   if($class3=='STANDERD 1'){
       ?>
  <td widtd="2%"><?php echo $totalmonthy3;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($class4=='STANDERD 2'){
       ?>
  <td widtd="2%"><?php echo $totalmonthy4;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
 if($class5=='STANDERD 3'){
       ?>
  <td widtd="2%"><?php echo $totalmonthy5;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  if($class6=='STANDERD 4'){
       
       ?>
  <td widtd="2%"><?php echo $totalmonthy6;?></td>
   <?php
  }
  else{
  ?>
  <td widtd="2%">0</td>
  <?php
  }
  ?>
  
  <td><?php echo $totalcarcost1;?></td>
  <td><?php echo $totalcar;?></td>
  <td><?php echo $totalcardue1;?></td>
  <td><?php
 $Totalgawc= $addtotalcost1+$readdtotalcost1;
  echo $Totalgawc;?>
  </td> 
  <td>
      <?php 
  echo  $Totalgawuc=$addtotalcost2+$retotalcost1;?>
    
  </td>

  <td>
      <?php
    echo $totalpaid= $addtotalpay1+$readdtotalpay1;
      
     ?> 
  </td>
  <td><?php echo $totalpaymentrecive;?></td>
<td><?php echo $totaladvance;?></td>  
<td><?php echo $totaladvancedue;?></td>  
<td>
     <?php
   
echo $totalduewithcar=   $totaladddue3+$totalreadddue3;/* Sum of Total Amount Due(with Car)*/
     ?>
     </td>
     
<td>
    <?php
  echo $totalduewithoutcat= $totalreadddue4+$totaladddue4;
    ?>
</td>  
<td>
    <?php echo $totasamount;?>
</td>

<td>
    <?php
echo $statmenttotal=  $totalnewadsatelementamount+$totalreadsatelementamount;
    ?>
    
</td>
</tr>
                                    
   
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
		function generatePDF() {
		window.print();
		}
		    function cancel() {
		        window.history.go(-1);
		    }
    </script>
</body>

</html>
