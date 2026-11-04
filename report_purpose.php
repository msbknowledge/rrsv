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
    <th>SL</th>
    <th>Purpose of Income</th>
    <th>Income</th>
    <th>Purpose of Expense</th>
    <th>Expenses</th>
</tr>
<?php
$sql = "SELECT 
            purpose,
            SUM(income_amount) AS total_income,
            SUM(exp_amount) AS total_expense
        FROM rrsv_scl_pl
        WHERE 1";
        
if ($fromDate != '' && $endDate != '') {
    $sql .= " AND pl_date BETWEEN '$fromDate' AND '$endDate'";
}

if ($session != '') {
    $sql .= " AND session = '$session'";
}

if ($purpose != '') {
    $sql .= " AND purpose = '$purpose'";
}

$sql .= " GROUP BY purpose ORDER BY purpose ASC";

   $res = mysqli_query($myDB, $sql);
$c = 0;

while($rows = mysqli_fetch_assoc($res)){
    $c++;

    $income = $rows['total_income'];
    $expense = $rows['total_expense'];

    $incomeamount += $income;
    $expamount += $expense;
?>
<tr>
    <td>#<?= $c ?></td>

    <td>
        <?= ($income > 0) ? $rows['purpose'] : ''; ?>
    </td>

    <td>
        <?= ($income > 0) ? $income : ''; ?>
    </td>

    <td>
        <?= ($expense > 0) ? $rows['purpose'] : ''; ?>
    </td>

    <td>
        <?= ($expense > 0) ? $expense : ''; ?>
    </td>
</tr>
<?php } ?>
<tr>
    <td colspan="2"><b>Total Cash in Hand:<?php echo $cash=$incomeamount-$expamount;?></b></td>
    <td><b><?= $incomeamount; ?></b></td>
    <td></td>
    <td><b><?= $expamount; ?></b></td>
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
