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
   $sql="select * from rrsv_student_registration where id=$id ";
            $result1=mysqli_query($myDB,$sql);
            $rows=mysqli_fetch_array($result1,MYSQLI_ASSOC);
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
    <link rel="stylesheet" type="text/css" href="libray/css/idcard.css">
    <link href="css/font-awesome.min.css" rel="stylesheet" />

    <title>iD Card</title>

  </head>
  <body>

<button type="button" id="backPageButton" class="button"><a style="color: #fff" href="manage_icard.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="button" id="printPageButton">Print</button>
    
<section>

<div class="row">
    


	<div class="col-md-2" style="margin-right:50px;margin-bottom:20px;">
		<div class="container">
			<div class="admit-card">
				<div class="BoxA border- padding mar-bot"> 
					<div class="row postin">
						<div class="col">
							<img src="libray/images/icardfinal.jpg" alt="" width="300px;">
						</div>	
						<p class="po10"><img src="student_reg_image/<?=$rows['image'];?>" alt="" width="100px;"></p>
							<p class="po1"><?=$rows['scl_name'];?></p>
							<p class="po2"><?=$rows['scl_father_name'];?></p>
							<p class="po3"><?=$rows['scl_class'];?></p>
							<!--<p class="po4"><?=$rows['scl_roll_no'];?></p>-->
							<p class="po5"><?=date('d-m-Y',strtotime($rows['scl_dob']));?></p>
							<p class="po6"><?=$rows['scl_blood'];?></p>
						
							<p class="po8"><?=$rows['scl_address'];?></p>
								<p class="po11" style="
    position: absolute;
    top: 77%;
    left: 176px;
    font-weight: bold;
    color: #151515;
    font-size: 11px;
"><?=$rows['scl_pos'];?></p>
							<p class="po9"><?=$rows['scl_phone_no'];?></p>
							<p class="po4"><?=$rows['scl_pin'];?></p>
							<p class="po7"><?=$rows['scl_dist'];?></p>
					</div>
				</div>
			</div>
		</div>
	</div>


	
</div>

</section>

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