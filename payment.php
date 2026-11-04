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

#scl_heiding {
    margin-bottom: 0px;
}

</style>

<!doctype html>
<?php
	include('include/dbcon.php');
$id=0;
if(isset($_GET['recep_no']))
{
  $recep_no=$myDB->escape_string(trim(addslashes($_GET['recep_no'])));
$sql="select a.*,b.scl_name,b.scl_roll_no,b.scl_class,b.scl_section from rrsv_scl_studen_fee as a,rrsv_student_registration as b where a.recep_no=$recep_no and a.pay_id=b.id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res);
}
?>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/payment.css">
    <link href="css/font-awesome.min.css" rel="stylesheet" />

    <title>Payment</title>

  </head>
  <body>

	<button type="button" id="backPageButton" class="btn btn-info"><a style="color: #fff" href="manage_print.php"><i class="fa fa-backward" aria-hidden="true"></i></a></button>
	<button type="button" onclick = "generatePDF()" class="btn btn-info" id="printPageButton"><i class="fa fa-print" aria-hidden="true"></i></button>
    
<section>
	<div class="container" style="margin-top:50px;">
		<div class="admit-card">

			<div class="BoxA border- padding mar-bot"> 
				<div class="row">
					<div class="col-sm-12">
						<h2 id="scl_heiding">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2>
						<p style="font-weight:bold;margin-top:10px;font-size:20px;padding-bottom:12px;">Baidyadanga,Rasulpur,</p>
						<p style="font-weight:bold;font-size:20px;">Rasulpur, Purba Bardhaman, Pin -713151</p>
					</div>					
				</div>

			</div>
			
			<div class="BoxD border- padding mar-bot">
				<div class="row">
					<div class="col-sm-12 paragrph">
						<p style="padding-left:20px;margin-top:-20px;">Name : <span><?=$rows['scl_name'];?></span>&nbsp;<br>
						Class : <span><?=$rows['scl_class'];?></span>&nbsp;&nbsp;Sec : <span><?=$rows['scl_section'];?></span>&nbsp;&nbsp;Roll No : <span><?=$rows['scl_roll_no'];?></span></p>
						<p style="padding-left:20px;margin-top:10px;margin-bottom:30px;">For Month Of : <span><?=$rows['scl_month'];?></span></p>
					</div>
				</div>
			</div>

			<div class="BoxD border- padding mar-bot">

				<div class="row">
					<div class="col-sm-12">
						<div class="box">
						<div class="row">
							<div class="col-md-1 para">
								<div class="lft1">
									<p>1.</p>
									<p style="margin-top:-10px;">2.</p>
									<p style="margin-top:-10px;">3.</p>
									<p style="margin-top:-10px;">4.</p>
									<p style="margin-top:-10px;">5.</p>
								    <p style="margin-top:-10px;">6.</p>
									<p style="margin-top:-10px;">7.</p>
									<p style="margin-top:-10px;">8.</p>
									<p style="margin-top:-10px;">9.</p>
									<p style="margin-top:-10px;">10.</p>
									<p style="margin-top:-10px;">11.</p>
									<p style="margin-top:-10px;">12.</p>
									<p style="margin-top:-10px;">13.</p>
									<p style="margin-top:-10px;">14.</p>
									<p style="margin-top:-10px;">15.</p>
									<p style="margin-top:-10px;">16.</p>
									<p style="margin-top:-10px;">17.</p>
									<p style="margin-top:-10px;">18.</p>
									
								</div>
							</div>
							<div class="col-md-8 para">
								<h4 style="padding-bottom:10px;padding-right:50px;">Particulars Of Fees</h4>
								<div class="lft">
									<p>Admission Fee</p>
									<p style="margin-top:-10px;">Tuition Fee</p>
									<p style="margin-top:-10px;">Admission Form</p>
										<p style="margin-top:-10px;">Reamission Form</p>
									<p style="font-weight:bold;margin-top:-10px;">Student Uniform</p>
									<p style="margin-top:-10px;">Icard:</p>
									<p style="margin-top:-10px;">Sweater,Slax,Cap</p>
									<p style="margin-top:-10px;">Badge</p>
									<p style="margin-top:-10px;">Shoes & Sockes</p>
									<h4>Book</h4>
									
									<?php
									$sql="select * from rrsv_book_cost where cost_id=$recep_no";
								    $res=mysqli_query($myDB,$sql);
								    while($row=mysqli_fetch_array($res)){
									?>
									<p style="margin-top:-10px;"><?=$row['book_name'];?></p>
									<?php
								    }
								    ?>
									<h4>Copy</h4>
									<?php
									$sql="select * from rrsv_copy_cost where cost_id=$recep_no";
								    $res=mysqli_query($myDB,$sql);
								    while($row=mysqli_fetch_array($res)){
									?>
									<p style="margin-top:-10px;"><?=$row['copy_name'];?></p>
									<?php
								    }
								    ?>
									
								
									<!--<p style="margin-top:-10px;">Full Payment</p>-->
									<!--<p style="margin-top:-10px;">Belt Fee</p>-->
									
									
									<!--<p style="margin-top:-10px;">Annual Programme fee</p>-->
									<!--<p style="margin-top:-10px;">Computer Fee</p>-->
									<!--<p style="margin-top:-10px;">School Bag Fee</p>-->
									<!--<p style="margin-top:-10px;">Others Fee</p>-->
								</div>

									<div class="row">
										<div class="col-md-6">
											<p style="padding-top:5px;">No:_<?=$rows['recep_no'];?></p>
										</div>
										<div class="col-md-6">
											<p style="padding-top:5px;">Total Cost<span class="total"><?=$rows['total_cost'];?></span></p>
											<p style="padding-top:5px;">Payment Received Amount<span class="total"><?=$rows['payment_recive'];?></span></p>
											<p style="padding-top:5px;">Advance/Due Amount<span class="total"><?=$rows['advance_due'];?></span></p>
										</div>
									</div>
							</div>
							<div class="col-md-3 para1">
								<h4 style="padding-bottom:10px;padding-right:50px;">Amounts(Rs.)</h4>
								<p><?=$rows['admission_fee_val'];?></p>
								<p><?=$rows['admission_fee_val'];?></p>
								<p style="margin-top:-10px;"><?=$rows['monthly_fee_val'];?></p>
								
								<p style="margin-top:-10px;"><?=$rows['admission_val'];?></p>
								<p style="margin-top:-10px;"><?=$rows['uniform_val'];?></p>
								<p style="margin-top:-10px;"><?=$rows['icard_val'];?></p>
								<p style="margin-top:-10px;"><?=$rows['sweater_slax_cap_val'];?></p>
								<p style="margin-top:-10px;"><?=$rows['bag_val'];?></p>
								<p style="margin-top:-10px;"><?=$rows['shoes_sockes_val'];?></p>
									<?php
									$sql="select * from rrsv_book_cost where cost_id=$recep_no";
								    $res=mysqli_query($myDB,$sql);
								    while($obj=mysqli_fetch_array($res)){
									?>
							<p style="margin-top:-10px;"><?=$obj['rate'];?></p>
							<?php
								    }
								    ?>
								    	<?php
									$sql="select * from rrsv_copy_cost where cost_id=$recep_no";
								    $res=mysqli_query($myDB,$sql);
								    while($obj=mysqli_fetch_array($res)){
									?>
							<p style="margin-top:-10px;"><?=$obj['rate'];?></p>
							<?php
								    }
								    ?>
							</div>
							<div class="brd"></div>
							<!--<div class="bd"></div>-->
							<div class="bd1"></div>
							<div class="brd1"></div>							
						</div>
						</div>
						
					</div>
					<!--<p style="padding-top:10px;padding-left:40px;font-weight:600;">Rs. in words :&nbsp;&nbsp;</p>-->
				</div>
				<div class="row sign">
					<div class="col-md-4">
						<p>Signature Of Student</p>
					</div>
					<div class="col-md-4">
						<p>Date :_&nbsp;<?=date('d-m-Y',strtotime($rows['scl_date']));?></p>
					</div>
					<div class="col-md-4">
						<p>Collecting Officers</p>
					</div>
				</div>
		</div>
	</div>

</section>

<section>
	<div class="container" style="margin-top:50px;">
		<div class="admit-card">

			<div class="BoxA border- padding mar-bot"> 
				<div class="row">
					<div class="col-sm-12">
						<h2 id="scl_heiding">RASULPUR ANATH SAMITY K.G. SCHOOL</h2>
						<p style="font-weight:bold;margin-top:10px;font-size:20px;padding-bottom:12px;">Govt.Regd.No.-S/IL/95439 * School Code - 19092100601</p>
						<p style="font-weight:bold;font-size:20px;">Rasulpur, Purba Bardhaman, Pin -713151</p>
					</div>					
				</div>

			</div>
			
			<div class="BoxD border- padding mar-bot">
				<div class="row">
					<div class="col-sm-12 paragrph">
						<p style="padding-left:20px;margin-top:-20px;">Name : <span><?=$rows['scl_name'];?></span>&nbsp;<br>
						Class : <span><?=$rows['scl_class'];?></span>&nbsp;&nbsp;Sec : <span><?=$rows['scl_section'];?></span>&nbsp;&nbsp;Roll No : <span><?=$rows['scl_roll_no'];?></span></p>
						<p style="padding-left:20px;margin-top:10px;margin-bottom:30px;">For Month Of : <span><?=$rows['scl_month'];?></span></p>
					</div>
				</div>
			</div>

			<div class="BoxD border- padding mar-bot">

				<div class="row">
					<div class="col-sm-12">
						<div class="box">
						<div class="row">
							<div class="col-md-1 para">
								<div class="lft1">
									<p>1.</p>
									<p style="margin-top:-10px;">2.</p>
									<p style="margin-top:-10px;">3.</p>
									<p style="margin-top:-10px;">4.</p>
									<p style="margin-top:-10px;">5.</p>
								<p style="margin-top:-10px;">6.</p>
									<p style="margin-top:-10px;">7.</p>
									<p style="margin-top:-10px;">8.</p>
									<p style="margin-top:-10px;">9.</p>
									<p style="margin-top:-10px;">10.</p>
									
								</div>
							</div>
							<div class="col-md-8 para">
								<h4 style="padding-bottom:10px;padding-right:50px;">Particulars Of Fees</h4>
								<div class="lft">
									<p>Admission Fee</p>
									<p style="margin-top:-10px;">Tuition Fee</p>
									<p style="margin-top:-10px;">Donation</p>
									<p style="font-weight:bold;margin-top:-10px;">Developement Fee</p>
									<p style="margin-top:-10px;">Examanition Fee</p>
									<p style="margin-top:-10px;">Diary</p>
									<p style="margin-top:-10px;">Badge</p>
									<p style="margin-top:-10px;">Saraswati Puja</p>
									<p style="margin-top:-10px;">Festival</p>
									<p style="margin-top:-10px;">Games Fee</p>
									<!--<p style="margin-top:-10px;">Magazine Fee</p>-->
									<!--<p style="margin-top:-10px;">Electricty Fee</p>-->
									<!--<p style="margin-top:-10px;">Computer</p>-->
								</div>

									<div class="row">
										<div class="col-md-6">
											<p style="padding-top:5px;">No:_<?=$rows['recep_no'];?></p>
										</div>
										<div class="col-md-6">
											<p style="padding-top:5px;">Total:_<span class="total"><?=$rows['scl_net'];?></span></p>
										</div>
									</div>
							</div>
							<div class="col-md-3 para1">
								<h4 style="padding-bottom:10px;padding-right:50px;">Amounts(Rs.)</h4>
								<p><?=$rows['scl_admission'];?></p>
								<p style="margin-top:-10px;"><?=$rows['scl_instalment'];?></p>
								<p style="margin-top:-10px;"><?=$rows['full_payment'];?></p>
								<p style="margin-top:-10px;"><?=$rows['car_fee'];?></p>
								<p style="margin-top:-10px;"></p>
								<p style="margin-top:-10px;"></p>
								<p style="margin-top:-10px;"></p>
								
								 
							</div>
							<div class="brd"></div>
							<div class="bd"></div>
							<div class="bd1"></div>
							<div class="brd1"></div>							
						</div>
						</div>
						
					</div>
					<!--<p style="padding-top:10px;padding-left:40px;font-weight:600;">Rs. in words :&nbsp;&nbsp;</p>-->
				</div>
				<div class="row sign">
					<div class="col-md-4">
						<p>Signature Of Student</p>
					</div>
					<div class="col-md-4">
						<p>Date :_&nbsp;<?=date('d-m-Y',strtotime($rows['scl_date']));?></p>
					</div>
					<div class="col-md-4">
						<p>Collecting Officers</p>
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