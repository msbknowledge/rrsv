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
			
<!--<table border=1 width=120% >-->
<!--    <tr>-->
<!--        <td colspan=6>Student Name:</td>-->

<!--        <td colspan=3>Class:</td>-->



<!--        <td colspan=5>Roll:</td>-->

      

<!--    </tr>-->
<!--<tr>-->
<!--    <td rowspan=2>Subject</td>-->
<!--    <td colspan=2>1st Unit</td>-->
<!--    <td colspan=2>2nd Unit</td>-->
<!--    <td colspan=2>3rd Unit</td>-->
<!--    <td ></td>-->
<!--    <td ></td>-->
<!--    <td ></td>-->
    <!--<td colspan=4> CO-CURRICULAR ACTIVITIES</td>-->
<!--</tr>-->
<!--<tr>-->
    
<!--    <td>Marks</td>-->
<!--    <td>H.M</td>-->
<!--   <td>Marks</td>-->
<!--    <td>H.M</td>-->
<!--   <td>Marks</td>-->
<!--    <td>H.M</td>  -->
<!--    <td>Total(200)</td>-->
<!--    <td>%</td>-->
<!--    <td>H.M(200)</td>-->
<!--    <td>GENERAL ASSESSMENT</td>-->
<!-- <td>1st Unit</td>-->
<!--<td>2nd Unit</td>-->
<!--<td>3rd Unit</td>-->
<!--</tr>-->
<!--<tr>-->
<!--    <td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--<td>1</td>-->
<!--</tr>-->
    
<!--</table>-->
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
                            <td colspan=6 style="color:blue;text-align:center;" class="pd1">1st Unit</td>
                        </tr>
                        <tr>
                            <td  style="color:green;text-align:center;">Subject</td>
                            <td class="pd1">Mark</td>
                            <td class="pd1">H.M</td>
                          
                        </tr>

                        <?php
                        $subtotal_full=0;
                        $subtotal_writen=0;
                        $subtotal_oral=0;
                        $subtotal_grade=0;
                        $subtotal_total=0;
                    
                   $sql="select a.*,b.* from marksheet as  a , marksheet_details as b  where b.scl_semi='1st Unit' and b.reg_id='".$rows['id']."' and a.scl_class='$scl_class'and  a.id=b.m_id";         
                $res=mysqli_query($myDB,$sql);
                 while($rows=mysqli_fetch_array($res))
                {
                ?>
                      
                        <tr>
                            <td class="pd1"><?=$rows['sub_id'];?></td>
                            <td class="pd1"><?=$rows['writen'];?></td>
                            <td class="pd1"><?=$rows['ful'];?></td>

                        </tr>
                         <?php
                            $subtotal_full=$subtotal_full+$rows['ful'];
                            $subtotal_writen=$subtotal_writen+$rows['writen'];
                            $subtotal_oral=$subtotal_oral+$rows['oral'];
                            $subtotal_total=$subtotal_total+$rows['total'];
                            $par=$subtotal_total/$subtotal_full*100;
                    
                            }
                        ?>
                        <!--<tr>-->
                           
                        <!--    <td><?=$subtotal_full?></td>-->
                        <!--    <td><?=$subtotal_writen?></td>-->
                           
                        <!--</tr>-->
                        <tr>
                       
                    <td  style="color:blue;text-align:left;" class="pd1">Grand</td>
                    <td><?=$subtotal_writen?></td>
                    <td><?=$subtotal_full;?></td>
                        </tr>

                        <tr>
                            <td  style="color:blue;text-align:left;" class="pd1">Drawing</td>
                            <td ><?=$position;?>
                            </td>
                        </tr>
        
                    </table>
                    
                </td>
                
                <td>
                    <table border=1 style="width: 101%; height: 100%;">
                        <tr>
                            <td colspan=6  style="color:blue;text-align:center;" class="pd1">2nd Unit</td>
                        </tr>
                        <tr>
                        <td  style="color:green;text-align:center;">Subject</td>
                            <td class="pd1">Mark</td>
                            <td class="pd1">H.M</td>

                        </tr>
                        <!--<tr>-->
                        <!--    <td class="pd1">50/25</td>-->
                        <!--    <td class="pd1">40/20</td>-->
                        <!--    <td class="pd1">10/5</td>-->
                        <!--    <td class="pd1">50/25</td>-->
                        <!--</tr>-->
                        <?php
                        $subtotal_full1=0;
                        $subtotal_writen1=0;
                        $subtotal_oral1=0;
                        $subtotal_total1=0;

                  $sql="select a.*,b.* from marksheet as  a , marksheet_details as b  where b.scl_semi='2nd Unit' and b.reg_id='".$id."' and a.scl_class='$scl_class'and  a.id=b.m_id";         
                $res=mysqli_query($myDB,$sql);
                $res=mysqli_query($myDB,$sql);
                 while($rows=mysqli_fetch_array($res))
                {
                    ?>
                        <tr>
                            
                            <td class="pd1"><?=$rows['sub_id'];?></td>
                            <td class="pd1"><?=$rows['ful'];?></td>
                            <td class="pd1"><?=$rows['writen'];?></td>

                        </tr>
                          
                        <?php
                            $subtotal_full1=$subtotal_full1+$rows['ful'];
                            $subtotal_writen1=$subtotal_writen1+$rows['writen'];
                            $subtotal_oral1=$subtotal_oral1+$rows['oral'];
                            $subtotal_total1=$subtotal_total1+$rows['total'];
                            $par=$subtotal_total1/$subtotal_full1*100;

                        }
                            
                        ?>
  <td  style="color:blue;text-align:left;" class="pd1">Grand</td>
                    <td><?=$subtotal_writen1?></td>
                    <td><?=$subtotal_full1;?></td>
                        </tr>

                        <tr>
                            <td  style="color:blue;text-align:left;" class="pd1">Drawing</td>
                            <td ><?=$position;?>
                            </td>
                        </tr>

        
                    </table>
                    
                </td>
                <td>
                     <table border=1 style="width: 101%; height: 100%;">
                        <tr>
                            <td colspan=6  style="color:blue;text-align:center;" class="pd1">3nd Unit</td>
                        </tr>
                        <tr>
                        <td  style="color:green;text-align:center;">Subject</td>
                            <td class="pd1">Mark</td>
                            <td class="pd1">H.M</td>
                            <!--<td class="pd1">Total(200)</td>-->
                            <!-- <td class="pd1">%</td>-->
                            <!--<td class="pd1">H.M</td>-->

                        </tr>
                        <!--<tr>-->
                        <!--    <td class="pd1">50/25</td>-->
                        <!--    <td class="pd1">40/20</td>-->
                        <!--    <td class="pd1">10/5</td>-->
                        <!--    <td class="pd1">50/25</td>-->
                        <!--</tr>-->
                        <?php
                        $subtotal_full1=0;
                        $subtotal_writen1=0;
                        $subtotal_oral1=0;
                        $subtotal_total1=0;
                   //     $sql="select a.sub_name,b.sub_id,b.ful,b.writen,b.oral,b.total,b.grade,b.scl_semi from scl_subject as a left join marksheet_details as b on a.id=b.sub_id and b.scl_semi='2nd Semister' and b.reg_id='".$row['id']."'";
         //  echo   $sql="select a.book_name,b.sub_id,b.ful,b.writen,b.oral,b.total,b.grade,b.scl_semi from rrsv_book as a , marksheet_details as b ,marksheet as c where b.scl_semi='3rd Semister' and b.reg_id='".$id."' and a.scl_class='$scl_class'and  c.id=b.m_id group by b.sub_id ";         
             $sql="select a.*,b.* from marksheet as  a , marksheet_details as b  where b.scl_semi='3rd Unit' and b.reg_id='".$id."' and a.scl_class='$scl_class'and  a.id=b.m_id";         
                $res=mysqli_query($myDB,$sql);
                $res=mysqli_query($myDB,$sql);
                 while($rows=mysqli_fetch_array($res))
                {
                    ?>
                        <tr>
                            
                            <td class="pd1"><?=$rows['sub_id'];?></td>
                            <td class="pd1"><?=$rows['ful'];?></td>
                            <td class="pd1"><?=$rows['writen'];?></td>


                        </tr>
                          
                        <?php
                            $subtotal_full2=$subtotal_full2+$rows['ful'];
                            $subtotal_writen2=$subtotal_writen2+$rows['writen'];
                            $subtotal_oral1=$subtotal_oral1+$rows['oral'];
                            $subtotal_total1=$subtotal_total1+$rows['total'];
                            $par=$subtotal_total1/$subtotal_full1*100;

                        }
                            
                        ?>
  <td  style="color:blue;text-align:left;" class="pd1">Grand</td>
                    <td><?=$subtotal_writen2?></td>
                    <td><?=$subtotal_full2;?></td>
                        </tr>

                        <tr>
                            <td  style="color:blue;text-align:left;" class="pd1">Drawing</td>
                            <td ><?=$position;?>
                            </td>
                        </tr>
        
                    </table>
                </td>
                 <td>
                     <table border=1 style="width: 101%; height: 100%;">
                        <!--<tr>-->
                        <!--    <td colspan=6  style="color:blue;text-align:center;" class="pd1"></td>-->
                        <!--</tr>-->
                        <tr>

                            <td class="pd1">Total(200)</td>
                             <td   class="pd1">%</td>
                            <td colspan=2 class="pd1">H.M</td>

                        </tr>
                        <!--<tr>-->
                        <!--    <td class="pd1">50/25</td>-->
                        <!--    <td class="pd1">40/20</td>-->
                        <!--    <td class="pd1">10/5</td>-->
                        <!--    <td class="pd1">50/25</td>-->
                        <!--</tr>-->
                        <?php
                        $subtotal_full1=0;
                        $subtotal_writen1=0;
                        $subtotal_oral1=0;
                        $subtotal_total1=0;
                   //     $sql="select a.sub_name,b.sub_id,b.ful,b.writen,b.oral,b.total,b.grade,b.scl_semi from scl_subject as a left join marksheet_details as b on a.id=b.sub_id and b.scl_semi='2nd Semister' and b.reg_id='".$row['id']."'";
         //  echo   $sql="select a.book_name,b.sub_id,b.ful,b.writen,b.oral,b.total,b.grade,b.scl_semi from rrsv_book as a , marksheet_details as b ,marksheet as c where b.scl_semi='3rd Semister' and b.reg_id='".$id."' and a.scl_class='$scl_class'and  c.id=b.m_id group by b.sub_id ";         
             $sql="select a.*,b.* from marksheet as  a , marksheet_details as b  where  b.reg_id='".$id."' and a.scl_class='$scl_class'and  a.id=b.m_id";         
                $res=mysqli_query($myDB,$sql);
                $res=mysqli_query($myDB,$sql);
                 while($rows=mysqli_fetch_array($res))
                {
                    ?>
                        <tr>
                            
                            <td class="pd1"><?=$rows['total'];?></td>
                            <td class="pd1"><?=$rows['ful'];?></td>
                            <td class="pd1"><?=$rows['writen'];?></td>


                        </tr>
                          
                        <?php
                            $subtotal_full1=$subtotal_full1+$rows['ful'];
                            $subtotal_writen1=$subtotal_writen1+$rows['writen'];
                            $subtotal_oral1=$subtotal_oral1+$rows['oral'];
                            $subtotal_total1=$subtotal_total1+$rows['total'];
                            $par=$subtotal_total1/$subtotal_full1*100;

                        }
                            
                        ?>
  <td  style="color:blue;text-align:left;" class="pd1">Grand</td>
                    <td><?=$par?></td>
                    <td><?=$pgrand;?></td>
                        </tr>

                        <tr>
                            <td  style="color:blue;text-align:left;" class="pd1">Drawing</td>
                            <td ><?=$position;?>
                            </td>
                        </tr>
        
                    </table>
                </td>
                <td colspan=6>
                    <table border=1 style="width: 101%; height: 100%;">
                        <tr>
                            <td colspan=6 style="color:blue;text-align:center;" class="pd1"> CO-CURRICULAR ACTIVITIES</td>
                        </tr>
                        <tr>
                        <td  style="color:green;text-align:center;">GENERAL ASSESSMENT</td>
                            <td class="pd1">1st Unit</td>
                            <td class="pd1">2nd Unit</td>
                            <td class="pd1">3rd Unit</td>
                          
                        </tr>
                                   <?php
                                
      $sql="select count(des) as a from rrsv_student_behaviour where student_id=$id and unit='1st Unit'";
      $res=mysqli_query($myDB,$sql);
      $rows=mysqli_fetch_array($res);
     $totalcount=$rows['a'];
     if($totalcount>0 and $totalcount<4)
     {
         $strtotal1='A';
     }
     elseif($totalcount>5 and $totalcount<8)
     {
           $strtotal1='B';
     }
      elseif($totalcount>9 and $totalcount<12)
     {
           $strtotal1='C';
     }
          elseif($totalcount>13)
     {
           $strtotal1='D';
     }    
     
      $sql="select count(des) as a from rrsv_student_behaviour where student_id=$id and unit='2nd Unit'";
      $res=mysqli_query($myDB,$sql);
      $rows=mysqli_fetch_array($res);
     $totalcount=$rows['a'];
     if($totalcount>0 and $totalcount<4)
     {
         $strtotal2='A';
     }
     elseif($totalcount>5 and $totalcount<8)
     {
           $strtotal2='B';
     }
      elseif($totalcount>9 and $totalcount<12)
     {
           $strtotal2='C';
     }
          elseif($totalcount>13)
     {
           $strtotal2='D';
     }    
     
      $sql="select count(des) as a from rrsv_student_behaviour where student_id=$id and unit='3rd Unit'";
      $res=mysqli_query($myDB,$sql);
      $rows=mysqli_fetch_array($res);
     $totalcount=$rows['a'];
     if($totalcount>0 and $totalcount<4)
     {
         $strtotal3='A';
     }
     elseif($totalcount>5 and $totalcount<8)
     {
           $strtotal3='B';
     }
      elseif($totalcount>9 and $totalcount<12)
     {
           $strtotal3='C';
     }
          elseif($totalcount>13)
     {
           $strtotal3='D';
     }    
     
     ?>
 <td >
               BEHAVIOUR
           </td>
          
           <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>

       </tr>  
              <tr>
           <td >
               NEATNESS
           </td>

 <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               DISCIPLINE
           </td>

           <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               SELF CONFIDENCE
           </td>

           <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               RESPONSIBILITY
           </td>

          <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               INITIATIVE
           </td>

          <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               CONCENTRATION
           </td>

            <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               PUNCTUALITY
           </td>

            <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               REGULARITY
           </td>

            <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               BODY WEIGHT
           </td>
        
 <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               HOME PROJECT
           </td>

           <td><?php echo $strtotal1 ;?></td>

           <td><?php echo $strtotal2 ;?></td>

           <td><?php echo $strtotal3 ;?></td>
       </tr>  
              <tr>
           <td >
               ABSENT
           </td>
<?php

                                 $sql="select count(student_id) as b from rrsv_student_attendence  where unit='1st Unit'  and student_id='".$id."'";         
                                    $res=mysqli_query($myDB,$sql);
                                    $row=mysqli_fetch_array($res);
                                  $absentciunt1=$row['b'];        
           ?>
           <td><?php echo $absentciunt1;?></td>
           <?php
        
                                      $sql="select count(student_id) as b from rrsv_student_attendence  where unit='2nd Unit'  and student_id='".$id."'";         
                                    $res=mysqli_query($myDB,$sql);
                                    $row=mysqli_fetch_array($res);
                                  $absentciunt2=$row['b'];    
         
          
           ?>
           <td><?php echo $absentciunt2;?></td>
           <?php
                                      $sql="select count(student_id) as b from rrsv_student_attendence  where unit='3rd Unit'  and student_id='".$id."'";         
                                    $res=mysqli_query($myDB,$sql);
                                    $row=mysqli_fetch_array($res);
                                  $absentciunt3=$row['b'];    
           
         
           ?>
           <td><?php echo $absentciunt3;?></td>
       </tr>  

        
                    </table>
                </td>
              </tr>
             <tr>
                 <td>Examination</td>
                 <td>Guardian Signature with Date</td>
                  <td colspan=8 rowspan=4><p>Principal's Signature with Date</p></td>
             </tr> 

             <tr>
                 <td>1st Unit</td>
                 <td></td>
             </tr>
             <tr>
               <td>2nd Unit</td>

             </tr>
             <tr>                
             <td>3rd Unit</td>
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