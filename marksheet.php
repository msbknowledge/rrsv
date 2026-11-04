<style>

    @page {
      size: auto;  /* auto is the initial value */
      margin: 0mm; /* this affects the margin in the printer settings */
    }
    html {
      background-color: #FFFFFF;
      margin: 0px; /* this affects the margin on the HTML before sending to printer */
    }
    body {
      border: solid 1px #FFFFFF;
      margin: 10mm 15mm 10mm 15mm; /* margin you want for the content */
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
p {
    margin-top: -56px;
    margin-bottom: 1rem;
}
</style>
<?php
    include('include/dbcon.php');
error_reporting(1);
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
//$datetxt="";

  if(isset($_GET['id']))
{
$id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}
   $sql="select * from rrsv_student_registration where id=$id ";
            $result1=mysqli_query($myDB,$sql);
            $rows=mysqli_fetch_array($result1,MYSQLI_ASSOC);
            $scl_class=$rows['scl_class'];
            		 
?>
<!doctype html>
<html lang="en">
  <head>
  <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <link rel="stylesheet" type="text/css" href="css/marksheet.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link href="css/font-awesome.min.css" rel="stylesheet" />

    <title>marksheet</title>

  </head>
    <body>

	<button type="button" id="backPageButton" class="btn btn-info"><a style="color: #fff" href="manage_marksheet.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="btn btn-info" id="printPageButton">Print</button>

	<section>
	<div class="container">
		<div class="admit-card">
			<div class="BoxA border- padding mar-bot"> 
				<div class="row">
					<div class="col-sm-12" id='li_5'>
						<h2>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2> 
					</div>					
				</div>
				<div class="row">
					<div class="col-sm-2">
						<img src="student_reg_image/<?=$rows['image'];?>" width="100px;"/>
					</div>
					<div class="col-sm-8">
						<p style="font-weight:bold;">Baidyadanga:Rasulpur;Burdwan.Govt.Rag.No-SO196094</p>
						<!--<p style="font-weight:bold;">Vill.-Teghari P.O.-Rajput Teghari P.S.-Raghunathganj Dist.-Murshidabad Pin.-742213</p>-->
						<p style="font-weight:bold;">Academic Session - 2019</p>
					</div>
					<div class="col-sm-2">
						 <div class="image">
						 <img src="libray/images/logo.jpeg" width="100px;"/>
                        </div>
					</div>
				</div>
			</div>
			

 <table border=1 style="width: 120%; height: 115%;">
            <tr>
                <td class="pd">Student Name</td>
                <td class="pd"><?=$rows['scl_name'];?></td>
                <td class="pd">Class</td>
                <td style="text-align:center;" class="pd"><?=$rows['scl_class'];?></td>
                <td class="pd">sec</td>
                <td style="text-align:center;" class="pd"><?=$rows['scl_section'];?></td>
                <td class="pd">Roll</td>
                <td style="text-align:center;" class="pd"><?=$rows['scl_roll_no'];?></td>
            </tr>
          
            <tr>
                 <td>
               
                    <table border=1 style="width: 101%; height: 100%;">
                   
                        <tr>
                            <td colspan=6  >Subject</td>
                          
                        </tr>
                        <?php
                          $sql="select * from marksheet_details  where reg_id=$id group by sub_id ";
		$res=mysqli_query($myDB,$sql);
		while($rows=mysqli_fetch_array($res)){
                        ?>
                        <tr>
                           
                            <td ><?=$rows['sub_id'];?></td>
                            
                          
                        </tr>
                        <?php
		}
		?>
                       
                   
                </tr>
                <tr>
                    <td>GRAND TOTAL</td>
                   
                </tr>
        </table>
                    
                </td>
                <td>
               
                    <table border=1 style="width: 101%; height: 100%;">
                   
                        <tr>
                            <td colspan=6  >1st Unit</td>
                          
                        </tr>
                        <tr>
                           
                            <td >Mark</td>
                            <td >H.M</td>
                          
                        </tr>
                        <?php
                          $sql="select * from marksheet_details  where reg_id=$id and scl_semi='1st Unit' ";
		$res=mysqli_query($myDB,$sql);
		while($rows=mysqli_fetch_array($res)){
		    $total1st=$total1st+$rows['writen'];
		    $total1storal=$total1storal+$rows['oral'];
                        ?>
                        <tr>
                            <td ><?=$rows['writen'];?></td>
                            <td ><?=$rows['oral'];?></td>
                          
                    </tr>
                    <?php
		}
		?>
		<td><?php echo $total1st;?></td>
			<td><?php echo $total1storal;?></td>
        </table>
                    
                </td>
 <td>
               
                    <table border=1 style="width: 101%; height: 100%;">
                   
                        <tr>
                            <td colspan=6  >2nd Unit</td>
                          
                        </tr>
                        <tr>
                           
                            <td >Mark</td>
                            <td >H.M</td>
                          
                        </tr>
                           </tr>
                        <?php
                          $sql="select * from marksheet_details  where reg_id=$id and scl_semi='2nd Unit' ";
		$res=mysqli_query($myDB,$sql);
		while($rows=mysqli_fetch_array($res)){
		      $total2st=$total2st+$rows['writen'];
		    $total2storal=$total2storal+$rows['oral'];
                        ?>
                        <tr>
                            <td ><?=$rows['writen'];?></td>
                            <td ><?=$rows['oral'];?></td>
                          

                        </tr>
                     <tr>
                  <?php
		}
		?>
           	<td><?php echo $total2st;?></td>
			<td><?php echo $total2storal;?></td>
        </table>
                    
                </td> 
             
       <td>
               
                    <table border=1 style="width: 101%; height: 100%;">
                   
                        <tr>
                            <td colspan=6  >3rd Unit</td>
                          
                        </tr>
                        <tr>
                           
                            <td >Mark</td>
                            <td >H.M</td>
                          
                        </tr>
                        <?php
                          $sql="select * from marksheet_details  where reg_id=$id and scl_semi='3rd Unit' ";
		$res=mysqli_query($myDB,$sql);
		while($rows=mysqli_fetch_array($res)){
		   $total3st=$total2st+$rows['writen'];
		    $total3storal=$total2storal+$rows['oral'];
                        ?>
                        <tr>
                            <td ><?=$rows['writen'];?></td>
                            <td ><?=$rows['oral'];?></td>
                           
                        </tr>
                   <?php
		}
		?>
        <td><?php echo $total3st;?></td>
			<td><?php echo $total3storal;?></td>
        </table>
                    
                </td>   
                <td>
               
                    <table border=1 style="width: 101%; height: 100%;">
                   
                        <tr>
                            <td colspan=6  ><br></td>
                          
                        </tr>
                        <tr>
                           
                            <td >Total(200)</td>
                            <td >%</td>
                             <td> H.M(200)</td>
                          
                        </tr>
		  <?php
//  $sql="select * from marksheet_details where reg_id='".$id."' ";
// $res=mysqli_query($myDB,$sql);
// while($obj1=mysqli_fetch_array($res)){
//  $unit=  $obj1['scl_semi'];
// }
                    $sql="select * from marksheet_details where reg_id='".$id."'  ";
                   
                   $sql1="select a.*,b.scl_semi from rrsv_subject as a,marksheet_details as b where a.class_name='".$scl_class."' ";
		  $res=mysqli_query($myDB,$sql1);
		  while($obj=mysqli_fetch_array($res)){
		   $sql .=" and sub_id='".$obj['sub_name']."'";
	
		  }

		   $res=mysqli_query($myDB,$sql);

		   while($row=mysqli_fetch_array($res)){
		        
		  $t=$t+$row['writen'];
		  
                    ?>
                        <tr>
                            <td ><?php echo $t;?></td>
                            <td >6</td>
                            <td >7</td>

                        </tr>
                        <?php
		  }     
                        ?>
                    
     	<td>50</td>
     		<td>50</td>
     			<td>50</td>
               
             
        </table>
                    
                </td>
               
               
                <td colspan=6>
                    <table border=1 style="width: 101%; height: 100%;">
                        <tr>
                            <td colspan=6  > CO-CURRICULAR ACTIVITIES</td>
                        </tr>
                        <tr>
                        <td  style="color:green;text-align:center;">GENERAL ASSESSMENT</td>
                            <td >1st Unit</td>
                            <td >2nd Unit</td>
                            <td >3rd Unit</td>
                          
                        </tr>
 <td >
               BEHAVIOUR
           </td>
          <?php
          $sql="select * from marksheet_details";
          
        $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Behaviour' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Behaviour' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Behaviour' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-3'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
          ?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>

       </tr>  
              <tr>
           <td >
               NEATNESS
           </td>
<?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Neatness' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Neatness' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Neatness' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
     <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
              <tr>
           <td >
               DISCIPLINE
           </td>
<?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Discipline' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Discipline' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Discipline' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31' and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
              <tr>
           <td >
               SELF CONFIDENCE
           </td>
<?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Self Confidence' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Self Confidence' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Self Confidence' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
                     <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
              <tr>
           <td >
               RESPONSIBILITY
           </td>

         <?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Responsibility' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Responsibility' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Responsibility' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
       </tr>  
              <tr>
           <td >
               INITIATIVE
           </td>

         <?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Initiative' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Initiative' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Initiative' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
              <tr>
           <td >
               CONCENTRATION
           </td>

             <?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Concentration' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Concentration' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Concentration' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
              <tr>
           <td >
               PUNCTUALITY
           </td>

              <?php
  $cur=date('Y');
       $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Punctuality' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Punctuality' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'   and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Punctuality' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
              <tr>
           <td >
               REGULARITY
           </td>

             <?php
  $cur=date('Y');
        $sql="  SELECT a.activties,count(a.activties) as ac1 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Regularity' and a.student_id='$id' and a.date BETWEEN '$cur-01-01' and '$cur-04-30'  and b.scl_semi='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb=$rows['ac1'];
        $sql="  SELECT a.activties,count(a.activties) as ac2 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Regularity' and a.student_id='$id' and a.date BETWEEN '$cur-05-01' and '$cur-09-30'  and b.scl_semi='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb1=$rows['ac2'];
         $sql="  SELECT a.activties,count(a.activties) as ac3 FROM `rrsv_student_behaviour` as a,marksheet_details as b where a.activties='Regularity' and a.student_id='$id' and a.date BETWEEN '$cur-10-01' and '$cur-12-31'  and b.scl_semi='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
        $totalb3=$rows['ac3'];
?>
           <td><?
           if($totalb>4){
               echo 'B';
           }
           elseif($totalb>8){
           echo 'C';
           }
           elseif($totalb>12){
               echo "D";
           }
           ?></td>

        <td><?
           if($totalb1>4){
               echo 'B';
           }
           elseif($totalb1>8){
           echo 'C';
           }
           elseif($totalb1>12){
               echo "D";
           }
           ?></td>

          <td><?
           if($totalb3>4){
               echo 'B';
           }
           elseif($totalb3>8){
           echo 'C';
           }
           elseif($totalb3>12){
               echo "D";
           }
           ?></td>
       </tr>  
             
 <!--             <tr>-->
 <!--          <td >-->
 <!--              BODY WEIGHT-->
 <!--          </td>-->
        
 <!--<td><?php echo $strtotal1 ;?></td>-->

 <!--          <td><?php echo $strtotal2 ;?></td>-->

 <!--          <td><?php echo $strtotal3 ;?></td>-->
 <!--      </tr>  -->
              <tr>
       <!--    <td >-->
       <!--        HOME PROJECT-->
       <!--    </td>-->

       <!--    <td><?php echo $strtotal1 ;?></td>-->

       <!--    <td><?php echo $strtotal2 ;?></td>-->

       <!--    <td><?php echo $strtotal3 ;?></td>-->
       <!--</tr>  -->
              <tr>
           <td >
               ABSENT
           </td>
<?php
$cur=date('Y');
       $sql="  SELECT count(student_id) as ac1 FROM `rrsv_student_attendence` where student_id='$id' and date BETWEEN '$cur-01-01' and '$cur-04-30' and unit='1st Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
       $totalb=$rows['ac1'];
       
       $cur=date('Y');
       $sql="  SELECT count(student_id) as ac2 FROM `rrsv_student_attendence` where student_id='$id' and date BETWEEN '$cur-05-01' and '$cur-09-30'
 and unit='2nd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
       $totalb1=$rows['ac2'];
       
       $sql="  SELECT count(student_id) as ac2 FROM `rrsv_student_attendence` where student_id='$id' and date BETWEEN 
'$cur-10-01' and '$cur-12-31' and unit='3rd Unit'";
        $res=mysqli_query($myDB,$sql);
        $rows=mysqli_fetch_array($res);
       $totalb3=$rows['ac3'];       
?>
           <td><?php echo $totalb;?></td>
          
           <td><?php echo $totalb1;?></td>
           
           <td><?php echo $totalb3;?></td>
       </tr>  

        
                    </table>
                </td>
              </tr>
              
           
            
          </table>
         <table border=1 style="width: 120%;">
               <tr>
                 <td>Examination</td>
                 <td>Guardian Signature with Date</td>
                  <td colspan=8 rowspan=4><p>Principal's Signature with Date</p></td>
             </tr> 

             <tr>
                 <td>1st Unit(Jan to April)</td>
                 <td></td>
             </tr>
             <tr>
               <td>2nd Unit(May to Sept)</td>

             </tr>
             <tr>                
             <td>3rd Unit(Oct to Dec)</td>
             <td></td>
             </tr>
                <tr>
 <td  >Teacher Signature With Date & Comments</td>
             </tr> 
         </table> 
</div>
</div>
</section>

	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
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