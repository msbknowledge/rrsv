
<style>
.top_rw{ background-color:#f4f4f4; }
	.td_w{ }
	button{ padding:5px 10px; font-size:14px;}
    .invoice-box {
        max-width: 890px;
        margin: auto;
        padding:10px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 14px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
		border-bottom: solid 1px #ccc;
    }
    .invoice-box table td {
        padding: 5px;
        vertical-align:middle;
    }
    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
		font-size:12px;
    }
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    .rtl table {
        text-align: right;
    }
    .rtl table tr td:nth-child(2) {
        text-align: left;
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
</style>
<?php
include('include/dbcon.php');
$id=0;
if(isset($_GET['id']))
{
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_inquery where id='$id'";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res);
}

?>
    <!-- inner banner -->
	<button type="button" id="backPageButton" class="button" ><a style="color: #fff" href="manage_inquery.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="button" id="printPageButton">Print</button>
    <!-- //inner banner -->
    <!-- bill -->
    <section class="w3l-contacts-12 py-5">
        
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
		<tr class="top_rw">
		   <td colspan="2">
		      <h2 style="margin-bottom: 0px;"></h2>
			  <span style="">  Date: <?=date('d-m-Y',strtotime($rows['d_o_i']));?></span>
			 
		   </td>
		    
                          
                            
		    <td>
		       Addmission Form Fees
		   </td>
		</tr>
        <tr>
		   <td colspan="2">
			  <span style="color: black;">  Addmission Code: </span>
			  <span style="color: green;"><?=$rows['admission_code'];?></span>
		   </td>
		    
                          
                            
		    <td>
		       
		   </td>
		</tr>
  
            <tr class="information">
                  <td colspan="3">
                    <table>
                        <tr>
                            <td colspan="2">
                               <b> RASULPUR RAMKRISHNA SARADA VIDYAPITH </b> <br>
                               Reg. No. : SO196094, U-DISE Code : 19251610302, ESTD : 2012;<br>
                              Baidyadanga, Rasulpur, Memari, Bardhaman,

                                Pin -713151
                            </td>
                            <!--<td> <b> Addmission Form Fees </b><br>-->
                              <br>
                               <br>
                              <td><img src="libray/images/logo.jpeg" class="img"  alt="image" style="
    width: 85px;
    height: 78px;
    margin-left: -99px;
" /></td>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

                            <td colspan="3">
            <table cellspacing="0px" cellpadding="2px">
            <tr class="heading">
                <td style="width:25%;">
                    Form No
                </td>
				<td style="width:10%; text-align:center;">
                    Name.
                </td>
                <td style="width:10%; text-align:right;">
                    Phone No
                </td>
				 <td style="width:15%; text-align:right;">
                 Class
                </td>
				<td style="width:15%; text-align:right;">
                 Amount
                </td>
				
            </tr>
			<tr class="item">
               <td style="width:25%;">
              <?=$rows['from_no'];?>
                </td>
				<td style="width:10%; text-align:center;">
                   <?=$rows['name'];?>
                </td>
                <td style="width:10%; text-align:right;">
                     <?=$rows['phone'];?>
                </td>
                <td style="width:10%; text-align:right;">
                     <?=$rows['class_name'];?>
                </td>
				 <td style="width:15%; text-align:right;">
                   <?=$rows['price'];?>
                </td>

            </tr>
          
				
			<tr class="item">

                <!--<td style="width:10%; text-align:right;">-->
                <!--    322.03-->
                <!--</td>-->
				 <!--<td style="width:15%; text-align:right;">-->
     <!--           </td>-->
				 <!--<td style="width:15%; text-align:right;">-->
     <!--               380-->
     <!--           </td>-->
            </tr>
            </td>
			</table>
            <!--<tr class="total">-->
            <!--      <td colspan="3" align="right">  Total Amount in Words :  <b> Three Hundred Eighty Rupees Only </b> </td>-->
            <!--</tr>-->
			<tr>
			   <td colspan="3">
			     <table cellspacing="0px" cellpadding="2px">
				    
					 <tr>
			            <td width="50%">
						</td>
						<td>
						 	<b> Authorized Signature </b>
							<br>
							<br>
							...................................
							<br>
							<br>
							<br>
						</td>
			        </tr>
			     </table>
			   </td>
			</tr>
        </table>
    </div>

    </section>
    <!-- //bill page -->
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
   
