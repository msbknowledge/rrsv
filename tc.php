<?php
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
$items=[];
$itemstr='';
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
if(isset($_POST['items']))
{
  $items=$_POST['items'];
}
  $itemstr=implode(',',$items);
  if(isset($_GET['id']))
{
$id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}
   $sql="select a.*,b.scl_pos,b.scl_pin,b.scl_reg_no from rrsv_tc as a,rrsv_student_registration as b where a.s_id=$id and a.s_id=b.id ";
            $result1=mysqli_query($myDB,$sql);
            $rows=mysqli_fetch_array($result1,MYSQLI_ASSOC);
            $bday = new DateTime($rows['scl_dob']); // Your date of birth
            
 $today = new Datetime(date($rows['d_o_t']));
 //$today=$rows['d_o_t'];
 $diff = $today->diff($bday);
 
//  $y=date('Y',strtotime($rows['scl_dob']));
//  $m=date('m',strtotime($rows['scl_dob']));
//  $d=date('d',strtotime($rows['scl_dob']));

?>
<style>
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
</style>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="libray/css/tc.css">
    <link href="css/font-awesome.min.css" rel="stylesheet" />

    <title>Transfer Certificate</title>

  </head>
  <body>

<button type="button" id="backPageButton" class="button"><a style="color: #fff" href="manage_tc.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="button" id="printPageButton">Print</button>
    
<section>

<div class="row">
    


	<div class="col-md-8">
		<div class="container">
			<div class="admit-card">
				<div class="BoxA border- padding mar-bot"> 
					<div class="row postin">
						<div class="col">
							<img src="libray/images/tc12.jpg" alt="" width="1500px;">
						</div>	
					
							<p class="po1"><?=$rows['name'];?></p>
							<p class="po15"><?=$rows['scl_reg_no'];?></p>
							<p class="po2"><?=$rows['fname'];?></p>
					    	<p class="po3"><?=$rows['address'];?></p>
					    	<p class="po16"> <?=$rows['scl_pos'];?></p>
					       	<p class="po17"> <?=$rows['scl_pin'];?></p>	
                            <p class="po4"><?=$rows['scl_dist'];?></p>
                            <p class="po5"><?=date('d-m-y',strtotime($rows['d_o_t']));?></p>  
                            <p class="po6"><?=date('d-m-y',strtotime($rows['scl_dob']));?></p> 
                         
                            <p class="po7"><?php echo $diff->y;?></p>  
                            <p class="po8"><?php echo $diff->m;?></p>
                            
                                  <p class="po9"><?php  echo $diff->d;?></p> 
                            
                            <p class="po10"><?=$rows['a_class'];?></p>  
                             <p class="po11">GENERAL</p>   
                             <p class="po12"><?=$rows['p_class'];?></p>
                            <p class="po13"><?=$rows['s_char'];?></p> 
                             <p class="po14"><?=date('d-m-y',strtotime($rows['d_o_t']));?></p>  
					</div>
				</div>
			</div>
		</div>
	</div>


	
</div>

</section>
<!--<section>-->

<!--<div class="row">-->
    


<!--	<div class="col-md-8">-->
<!--		<div class="container">-->
<!--			<div class="admit-card">-->
<!--				<div class="BoxA border- padding mar-bot"> -->
<!--					<div class="row postin">-->
<!--						<div class="col">-->
<!--							<img src="libray/images/tc_dup1.jpg" alt="" width="1500px;">-->
<!--						</div>	-->
					
<!--							<p class="po1"><?=$rows['name'];?></p>-->
<!--							<p class="po15"><?=$rows['scl_reg_no'];?></p>-->
<!--							<p class="po2"><?=$rows['fname'];?></p>-->
<!--					    	<p class="po3"><?=$rows['address'];?></p>-->
<!--					    	<p class="po16">, <?=$rows['scl_pos'];?></p>-->
<!--					       	<p class="po17">, <?=$rows['scl_pin'];?></p>	-->
<!--                            <p class="po4"><?=$rows['scl_dist'];?></p>-->
<!--                            <p class="po5"><?=date('d-m-y',strtotime($rows['d_o_t']));?></p>  -->
<!--                            <p class="po6"><?=date('d-m-y',strtotime($rows['d_o_a']));?></p> -->
<!--                            <p class="po7"><?php echo $diff->y;?></p>  -->
<!--                            <p class="po8"><?php echo $diff->m;?></p>-->
<!--                            <p class="po9"><?php  echo $diff->d;?></p> -->
<!--                            <p class="po10"><?=$rows['a_class'];?></p>  -->
<!--                             <p class="po11">GENERAL</p>   -->
<!--                             <p class="po12"><?=$rows['p_class'];?></p>-->
<!--                            <p class="po13"><?=$rows['s_char'];?></p> -->
<!--                             <p class="po14"><?=date('d-m-y',strtotime($rows['d_o_t']));?></p>  -->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->


	
<!--</div>-->

<!--</section>-->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
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