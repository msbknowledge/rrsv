<?php 
session_start();
include('include/dbcon.php');

$fromDate=$_SESSION['fromDate'];
$endDate=$_SESSION['endDate'];



  
  
				       $sql="select a.*,b.scl_date,b.last_advance_due,b.payment_recive from  rrsv_student_registration as a, rrsv_scl_studen_fee as b where  b.scl_date between '".$fromDate."' and '".$endDate."' and a.id=b.pay_id group by a.id " ;

			       //////////////////////////////// using singl search ///////////////////////////////////////////
				       
				     $res=mysqli_query($myDB,$sql);
                   
				     
     
	if(mysqli_num_rows($res) > 0)
 {
  $output .= '
   <table class="table" bordered="1">  
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
  ';
while($row = mysqli_fetch_array($res))
  {

      $c++;
   $output .= '
   <tr> 
    <td>'.$c.'</td>  
      <td>'.$row['scl_reg_no'].'</td>
    <td>'.$row["scl_date"].'</td>  
   <td>'.$row["scl_name"].'</td> 
 <td >'.$row['admission_fee_val'].'</td>
  <td >'.$row['re_admission_fee_val'].'</td>
  <td >'.$row['uniformval'].'</td>
  <td >'.$row['shoessockesval'].'</td>
  <td >'.$row['sweaterslaxcap_val'].'</td>
  <td >'.$row['admissionval'].'</td>
  <td >'.$row['bagval'].'</td>
  <td >'.$row['icardval'].'</td>
  <td >'.$row['diaryval'].'</td>
  
  if($row["scl_class"]=="NURSERY"){
      
       $dd=$row["scl_class"];
       $rr1=$rr1+$row["bookcost"];
 
   <td >'.$row["bookcost"].'</td>
   
  }
  else{
  ?>
  <td >0</td>
  
  }
   if($row["scl_class"]=="KINDER GARTEN"){
       $dd1=$row["scl_class"];
        $rr2=$rr2+$row["bookcost"];
  ?>
  <td >'.$row["bookcost"].'</td>
   
  }
  else{
  ?>
  <td >0</td>
  
  }
   if($row["scl_class"]=="STANDERD 1"){
        $dd2=$row["scl_class"];
        $rr3=$rr3+$row["bookcost"];
       ?>
  <td >'.$row["bookcost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($row["scl_class"]=="STANDERD 2"){
       $dd3=$row["scl_class"];
       $rr4=$rr4+$row["bookcost"];
       ?>
  <td >'.$row["bookcost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
 if($row["scl_class"]=="STANDERD 3"){
      $dd4=$row["scl_class"];
      $rr5=$rr5+$row["bookcost"];
       ?>
  <td >'.$row["bookcost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($row["scl_class"]=="STANDERD 4"){
       $dd5=$row["scl_class"];
       $rr6=$rr6+$row["bookcost"];
       ?>
  <td >'.$row["bookcost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  ?>
    <?php
  if($row["scl_class"]=="NURSERY"){
      $sclclass1=$row["scl_class"];
      $totalcost1=$totalcost1+$row["copycost"];
  ?>
<td >'.$row["copycost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($row["scl_class"]=="KINDER GARTEN"){
       $sclclass2=$row["scl_class"];
     $totalcost2=$totalcost2+$row["copycost"];
  ?>
 <td >'.$row["copycost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($row["scl_class"]=="STANDERD 1"){
       $sclclass3=$row["scl_class"];
      $totalcost3=$totalcost3+$row["copycost"];
       ?>
 <td >'.$row["copycost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($row["scl_class"]=="STANDERD 2"){
      $sclclass4=$row["scl_class"];
      $totalcost4=$totalcost4+$row["copycost"];
       ?>
<td >'.$row["copycost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
 if($row["scl_class"]=="STANDERD 3"){
     $sclclass5=$row["scl_class"];
      $totalcost5=$totalcost5+$row["copycost"];
       ?>
<td >'.$row["copycost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($row["scl_class"]=="STANDERD 4"){
      $sclclass6=$row["scl_class"];
      $totalcost6=$totalcost6+$row["copycost"];
       ?>
<td >'.$row["copycost"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  ?>
    <?php
  if($row["scl_class"]=="NURSERY"){
      $class1=$row["scl_class"];
      $totalmonthy1=$totalmonthy1+$row["totalmonthly_fee_val"]
  ?>
<td >'.$row["totalmonthly_fee_val"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($row["scl_class"]=="KINDER GARTEN"){
       $class2=$row["scl_class"];
        $totalmonthy2=$totalmonthy2+$row["totalmonthly_fee_val"]
  ?>
 <td >'.$row['totalmonthly_fee_val'].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($row["scl_class"]=="STANDERD 1"){
       $class3=$row["scl_class"];
        $totalmonthy3=$totalmonthy3+$row["totalmonthly_fee_val"]
       ?>
  <td >'.$row['totalmonthly_fee_val'].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($row["scl_class"]=="STANDERD 2"){
      $class4=$row["scl_class"];
       $totalmonthy4=$totalmonthy4+$row["totalmonthly_fee_val"]
       ?>
  <td >'.$row['totalmonthly_fee_val'].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
 if($row["scl_class"]=="STANDERD 3"){
     $class5=$row["scl_class"];
      $totalmonthy5=$totalmonthy5+$row["totalmonthly_fee_val"]
       ?>
 <td >'.$row["totalmonthly_fee_val"].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($row["scl_class"]=="STANDERD 4"){
      $class6=$row["scl_class"];
       $totalmonthy6=$totalmonthy6+$row["totalmonthly_fee_val"]
       ?>
  <td >'.$row['totalmonthly_fee_val'].'</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  ?>
 
  
  <td><?php echo $totalcarcost;
  $totalcarcost1=$totalcarcost1+$totalcarcost;
  ?>
  </td>
  <td>'.$row['carfee'].'</td>
  <td><?php echo $totalcardue;
  $totalcardue1=$totalcardue1+$totalcardue;
 ?></td>
    
    
    
   <td>
     <?php
     if($row["add_status"]=="New Admission"){
        $addstatus=$row["add_status"]; 
          echo $add_total_cost;
          $addtotalcost1=$addtotalcost1+$add_total_cost;
         
     }
     elseif($row["add_status"]=="Readmission") {
          $addstatus=$row["add_status"]; 
        echo $re_total_cost;
        $readdtotalcost1=$readdtotalcost1+$re_total_cost;
     }
     ?>
     
 </td>
   <td>
     <?php
     if($row["add_status"]=="New Admission"){
        $add_status1= $row["add_status"];
          echo $add_total_cost1;
         $addtotalcost2=$addtotalcost2+$add_total_cost1;
     }
     else      if($row["add_status"]=="Readmission") {
         $add_status1= $row["add_status"];
        echo $re_total_cost1;
        $retotalcost1=$retotalcost1+$re_total_cost1;
     }
     ?>
     
 </td>
 
 <td>
     <?php
     if($row["add_status"]=="New Admission"){
         $add_status2= $row["add_status"];
          echo $addtotalpay;
         $addtotalpay1=$addtotalpay1+$addtotalpay;
         
     }
     else      if($row["add_status"]=="Readmission") {
         $add_status2= $row["add_status"];
        echo $readdtotalpay;
        $readdtotalpay1=$readdtotalpay1+$readdtotalpay;
     }
     ?>
     
 </td>
 <td>".$row["paymentrecive"]+$row["admissionval"];
  $totalpaymentrecive=$totalpaymentrecive+$row["paymentrecive"]+$row["admissionval"];
  ?></td>
<td><?php if($row["val_ad_du"]=="Advance"){
echo $row["advance_due"];
}
?>
</td>

<td><?php if($row["val_ad_du"]=="Due"){
echo $row["advance_due"];
}
?>
</td>

 <td>
     <?php
     if($row["add_status"]=="New Admission"){
          $add_status3= $row["add_status"];
          echo $totaladddue;
          $totaladddue3=$totaladddue3+$totaladddue;
         
     }
     else      if($row["add_status"]=="Readmission") {
          $add_status3= $row["add_status"];
        echo $totalreadddue;
        $totalreadddue3=$totalreadddue3+$totalreadddue;
     }
     ?>
     
 </td>
  <td>
     <?php
     if($row["add_status"]=="New Admission"){
         
          echo $totaladddue1;
          $totaladddue4=$totaladddue4+$totaladddue1;
         
     }
     else      if($row["add_status"]=="Readmission") {
       echo $totalreadddue1;
          $totaladddue4=$totaladddue4+$totalreadddue1;
     }
     ?>
     
 </td>
 <td>".$row["amount1"]."</td>


 <td>
     <?php
     if($row["add_status"]=="New Admission"){
         $addstatus=$row["add_status"];
         if($row["amount1"]!=0){
          echo $newadsatelementamount;
        $totalnewadsatelementamount=$totalnewadsatelementamount+$newadsatelementamount;
         }
         else
         {
          echo 0;  
         }

     }
     else      if($row["add_status"]=="Readmission") {
           if($row["amount1"]!=0){
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
    <td><?php echo $totaladmission_fee_val."</td>
     <td><?php echo $totalreadmission_fee_val."</td>
      <td><?php echo $totaluniformval."</td>
      <td><?php echo $totalshoessockesval."</td>
    <td><?php echo $totalsweaterslaxcap_val."</td>
    <td><?php echo $totaladmissionval."</td>
    <td><?php echo $totalbagval."</td>
    <td><?php echo $totalicardval."</td>
    <td><?php echo $totaldiaryval."</td>
   <?php
   
  if($dd=="NURSERY"){
     
  ?>
   <td ><?php echo $rr1."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
if($dd1=="KINDER GARTEN"){
  ?>
  <td ><?php echo $rr2."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($dd2=="STANDERD 1"){
       ?>
  <td ><?php echo $rr3."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($dd3=="STANDERD 2"){
       ?>
  <td ><?php echo $rr4."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
 if($dd4=="STANDERD 3"){
       ?>
  <td ><?php echo $rr5."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($dd5=="STANDERD 4"){
       ?>
  <td ><?php echo $rr6."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  ?>
   <?php
   
  if($sclclass1=="NURSERY"){
     
  ?>
   <td ><?php echo $totalcost1."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
if($sclclass2=="KINDER GARTEN"){
  ?>
  <td ><?php echo $totalcost2."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($sclclass3=="STANDERD 1"){
       ?>
  <td ><?php echo $totalcost3."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($sclclass4=="STANDERD 2"){
       ?>
  <td ><?php echo $totalcost4."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
 if($sclclass5=="STANDERD 3"){
       ?>
  <td ><?php echo $totalcost5."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($sclclass6=="STANDERD 4"){
       ?>
  <td ><?php echo $totalcost6."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  ?>
     <?php
   
  if($class1=="NURSERY"){
     
  ?>
   <td ><?php echo $totalmonthy1."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
if($class2=="KINDER GARTEN"){
  ?>
  <td ><?php echo $totalmonthy2."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
   if($class3=="STANDERD 1"){
       ?>
  <td ><?php echo $totalmonthy3."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($class4=="STANDERD 2"){
       ?>
  <td ><?php echo $totalmonthy4."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
 if($class5=="STANDERD 3"){
       ?>
  <td ><?php echo $totalmonthy5."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  if($class6=="STANDERD 4"){
       
       ?>
  <td ><?php echo $totalmonthy6."</td>
   <?php
  }
  else{
  ?>
  <td >0</td>
  <?php
  }
  ?>
  
  <td><?php echo $totalcarcost1."</td>
  <td><?php echo $totalcar."</td>
  <td><?php echo $totalcardue1."</td>
  <td><?php
 $Totalgawc= $addtotalcost1+$readdtotalcost1;
  echo $Totalgawc."
  </td> 
  <td>
      <?php 
  echo  $Totalgawuc=$addtotalcost2+$retotalcost1."
    
  </td>

  <td>
      <?php
    echo $totalpaid= $addtotalpay1+$readdtotalpay1;
      
     ?> 
  </td>
  <td><?php echo $totalpaymentrecive."</td>
<td><?php echo $totaladvance."</td>  
<td><?php echo $totaladvancedue."</td>  
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
    <?php echo $totasamount."
</td>

<td>
    <?php
echo $statmenttotal=  $totalnewadsatelementamount+$totalreadsatelementamount;
    ?>
    
</td>
   </tr>
   ';
  }
   $output .= '</table>';
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=statusreport.xls');
  echo $output;
 }
 else{
    echo "<script language='JavaScript'>";
		echo "window.location.href='status_report.php?status=<?php echo$status.'';";
		echo "</script>"; 
 }
 
	?>