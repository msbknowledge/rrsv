<?php
//print_r($_POST);
//die();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

<style>
  /*Not Required*/


  /*Required*/
  @media print {
  .no-print {
    display: none !important;
  }
  
}
body {
    text-align: center !important; /* Center align body content */
  }

  table {
    margin: 0 auto !important; /* Center the table horizontally */
  }
  @media (max-width: 576px) {
    .modal-dialog.modal-dialog-slideout {
      width: 80%
    }
  }

  .modal-dialog-slideout {
    min-height: 100%;
    margin: 0 auto 0 0;
    background: #fff;
  }

  .modal.fade .modal-dialog.modal-dialog-slideout {
    -webkit-transform: translate(-100%, 0);
    transform: translate(-100%, 0);
  }

  .modal.fade.show .modal-dialog.modal-dialog-slideout {
    -webkit-transform: translate(0, 0);
    transform: translate(0, 0);
    flex-flow: column;
  }

  .modal-dialog-slideout .modal-content {
    border: 0;
  }

  .modal-header h4 {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 1px;
  }

  .view_details_ul {
    list-style: none;
  }

  .view_details_ul li {
    display: block;
    color: #6a2414;
    font-size: 20px;
  }

  .view_details_ul li span {
    padding: 0px 50px;
  }

  @page {
    size: auto;
    /* auto is the initial value */
    margin: 0mm;
    /* this affects the margin in the printer settings */
  }

  html {
    background-color: #FFFFFF;
    margin: 0px;
    /* this affects the margin on the HTML before sending to printer */
  }

  @media print {
    #printbtn {
      display: none;
    }
  }

  @media print {
    #cancel {
      display: none;
    }
  }

  @media print {
    body {
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
$id = 0;
$ret1code = "";
$id = 0;
$txtsearch = "";
$status = "";
$sid = 0;
$did = 0;
$did = 0;
$scl_class = "";
$scl_section = "";

$session = "";
$stockType = "";
$class_id = 0;
$stock_master_id = 0;
$publishers_id = 0;
$from_date = "";
$to_date = "";
if (isset($_GET['stock_type']) && $_GET['stock_type'] != "") {
  $stockType = mysqli_real_escape_string($myDB, $_GET['stock_type']);
  $sql = "";
  $tableName = "";
  $fieldName = "";
  if ($stockType === "BOOK") {
    $fieldName = "book_name";
    $tableName = "rrsv_book";
    //$sql = "SELECT id,$name FROM rrsv_book";
  } else if ($stockType === "COPY") {
    $fieldName = "copy_name";
    $tableName = "rrsv_copy";
    //$sql = "SELECT id,$name FROM rrsv_copy";
  } else if ($stockType === "OTHERS") {
    $tableName = "stock_master";
    $fieldName = "name";
    //$sql = "SELECT id,$name FROM stock_master";
  }
}

if (isset($_GET['class_id']) && $_GET['class_id'] != "") {
  $class_id = mysqli_real_escape_string($myDB, $_GET['class_id']);
}
$from_date = "";
if (isset($_GET['stock_master_id']) && $_GET['stock_master_id'] != "") {
  $stock_master_id = mysqli_real_escape_string($myDB, $_GET['stock_master_id']);
}
if (isset($_GET['from_date']) && $_GET['from_date'] != "") {
  $from_date = mysqli_real_escape_string($myDB, $_GET['from_date']);
}
if (isset($_GET['to_date']) && $_GET['to_date'] != "") {
  $to_date = mysqli_real_escape_string($myDB, $_GET['to_date']);
}
if (isset($_GET['publishers_id']) && $_GET['publishers_id'] != "") {
  $publishers_id = mysqli_real_escape_string($myDB, $_GET['publishers_id']);
}
if (isset($_GET['session']) && $_GET['session'] != "") {
  $session = mysqli_real_escape_string($myDB, $_GET['session']);
}
// echo $tableName;
// echo $fieldName;
// die;
// $fromDate = $myDB->escape_string(trim(addslashes($_POST['fromDate'])));
// $endDate = $myDB->escape_string(trim(addslashes($_POST['endDate'])));
// $purpose = $myDB->escape_string(trim(addslashes($_POST['purpose'])));

// $scl_session = $myDB->escape_string(trim(addslashes($_POST['scl_session'])));
// $scl_class = $myDB->escape_string(trim(addslashes($_POST['scl_class'])));

?>


<!doctype html>
<html>

<head>
  <!-- Required meta tags -->

  <!-- Bootstrap CSS -->

  <link href="css/moneybook.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="libray/images/logo.jpeg" />
  <title>Stock Report</title>

</head>

<body>
  <button type="button" id="backPageButton" class="button"><a style="color: #fff" href="stock.php">Back</a></button>
  <button type="button" onclick="generatePDF()" class="button" id="printPageButton">Print</button>


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
                      <input type="hidden" name="txtsearch" class="srinput" value="" size="30" maxlength="100"
                        placeholder="Search By Student Name">
                      <input type='hidden' class='srinputd' name='fromDate' value='<?php if (isset($_POST['fromDate']))
                        echo $_POST['fromDate']; ?>'>

                      <input type='hidden' class='srinputd' name='endDate' value='<?php if (isset($_POST['endDate']))
                        echo $_POST['endDate']; ?>'>
                    </form>
                  </div>
                </table>
              </form>
              <div class="a">
                <h2>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2>
                <p style="font-weight: bold;">Reg. No. : SO196094, U-DISE Code : 19251610302, ESTD : 2012; Baidyadanga,
                  Rasulpur, Memari, Purba Bardhaman, Pin -713151</p>
                <p style="font-weight: bold;">Stock Report For Start Date :<?= isset($_GET['date']) ?
                  date('d-m-Y', strtotime($fromDate)) : ""; ?> and End Date :
                  <?= isset($_GET['date']) ? date('d-m-Y', strtotime($endDate)) : ""; ?>
                </p>
                <p style="font-weight: bold;">Purpose : <?= isset($_GET['date']) ? $purpose : ""; ?></p>
              </div>
              <div>


              </div>
              <table border=1 width=80% style="
    margin-left: 126px;
">
                <tr>
                  <td style="width:10%">Sl.No</td>
                  <td style="width:10%">Date</td>
                  <td style="width:10%">Class</td>
                  <td style="width:10%">Session</td>
                  <td style="width:10%">Stock Type</td>
                  <td style="width:10%">Stock Name</td>
                  <td style="width:10%">publishers Name</td>
                  <td style="width:10%">Quantity</td>
                  <td style="width:10%" class="no-print">MRP</td>
                  <td style="width:10%" class="no-print">Total</td>
                  <td style="width:10%" class="no-print">Comission Prectantage</td>
                  <td style="width:10%" class="no-print">Comission Amount</td>
                  <td style="width:10%" class="no-print">Purchase Value</td>

                  <td style="width:10%">Purchase Value Per Unit</td>
                  <td style="width:10%" class="no-print">Sale Quantity</td>
                  <td style="width:10%">Stock Quantity After Sale</td>
                  <td style="width:10%" class="no-print">Total Sale Price</td>

                  <td style="width:10%">Stock Return Quantity</td>
                  <td style="width:10%">Stock Return Value</td>
                  <td style="width:10%">Stock Return Damage Quantity</td>
                  <td style="width:10%">Stock Return Damage Value</td>
                  <td style="width:10%">Damage Remarks</td>

                  <td style="width:10%; text-align:center;" class="no-print">Actions</td>
                </tr>
                <?php
                $c = 0;

                if ($stockType === "" && $stock_master_id === 0) {
                  //echo 1;
                  $sql = "select a.*, b.class_name,d.publishers_name from stock as a left join rrsv_class b on a.class_id=b.id left join rrsv_publishers d on a.publishers_id=d.id";
                  $sql .= " WHERE a.stock_return IS NOT NULL AND a.stock_return != 0 order by id ASC";
                } else {
                  //echo 2;
                  $sql = "select a.*, b.class_name,c.$fieldName AS stock_master_id,d.publishers_name from stock as a left join rrsv_class b on a.class_id=b.id left join $tableName c on a.stock_master_id=c.id  left join rrsv_publishers d on a.publishers_id=d.id where 1";
                  if ($stockType != "") {
                    //echo 11111;
                    $sql .= " and a.stock_type = '" . $stockType . "'";
                  }
                  if ($class_id != 0) {
                    //echo 22222;
                    $sql .= " and b.id = $class_id";
                  }
                  if ($stock_master_id != 0) {
                    //echo 33333;
                    $sql .= " and c.id = $stock_master_id";
                  }
                  if ($session != "") {
                    //echo 44444;
                    $sql .= " and a.session = '" . $session . "'";
                  }
                  if ($publishers_id != 0) {
                    // echo 55555;
                    $sql .= " and a.publishers_id = '" . $publishers_id . "'";
                  }
                  if ($from_date && $to_date) {

                    $sql .= " and date between '" . $from_date . "' and '" . $to_date . "'";
                  }
                 // $sql .= " order by id ASC";
                 $sql .= " and a.stock_return IS NOT NULL AND a.stock_return != 0 order by id ASC";

                }

                //echo $sql;
                // die();
                
                $c = 0;
                $totals = [
                  'qty' => 0,
                  'mrp' => 0,
                  'total' => 0,
                  'commission_percentage' => 0,
                  'commission_amount' => 0,
                  'purchase_value' => 0,
                  'purchase_value_per_unit' => 0,
                  'sale' => 0,
                  'stock_after_sale' => 0,
                  'price' => 0,
                  'stock_return' => 0,
  'value_after_return' => 0,
  'damage_qty' => 0,
  'damage_value' => 0,
                ];

                $res = mysqli_query($myDB, $sql);
                while ($rows = mysqli_fetch_array($res)) {
                  // $incomeamount=$incomeamount+$rows['income_amount'];
                  //  $expamount=$expamount+$rows['exp_amount'];
                  $dId = $rows['id'];
                  $c++;
                  $stockName = "";
                  if (is_numeric($rows['stock_master_id'])) {
                    $stock_master_id = $rows['stock_master_id'];
                    $stockType = $rows['stock_type'];
                    $fieldName = "";
                    $tableName = "";
                    if ($stockType === "BOOK") {
                      $fieldName = "book_name";
                      $tableName = "rrsv_book";
                    } else if ($stockType === "COPY") {
                      $fieldName = "copy_name";
                      $tableName = "rrsv_copy";
                    } else if ($stockType === "OTHERS") {
                      $tableName = "stock_master";
                      $fieldName = "name";
                    }
                    $sql = "SELECT $fieldName FROM $tableName where id =" . $stock_master_id;
                    $getStockName = mysqli_query($myDB, $sql);
                    $stockName = mysqli_fetch_array($getStockName)[$fieldName];
                  } else {
                    $stockName = $rows['stock_master_id'];
                  }
                  ;
                  $totals['qty'] += $rows['qty'];
                  $totals['mrp'] += $rows['mrp'];
                  $totals['total'] += $rows['total'];
                  $totals['commission_percentage'] += $rows['comission_prectantage'];
                  $totals['commission_amount'] += $rows['comission_amount'];
                  $totals['purchase_value'] += $rows['purchase_value'];
                  $totals['purchase_value_per_unit'] += $rows['purchase_value_per_unit'];
                  $totals['sale'] += $rows['sale'];
                  $totals['stock_after_sale'] += $rows['stock_after_sale'];
                  $totals['price'] += $rows['price'];
                  $totals['stock_return'] += $rows['stock_return'];
      $totals['value_after_return'] += $rows['value_after_return'];
      $totals['damage_qty'] += $rows['damage_qty'];
      $totals['damage_value'] += $rows['damage_value'];
                  ?>
                  <tr>
                    <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center">
                      <?= date('d-m-Y', strtotime($rows['date'])); ?>
                    </td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['class_name']; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['session']; ?></td>

                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['stock_type']; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $stockName; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['publishers_name']; ?></td>
                    <td class="text" valign="center" style="padding-left:10px;"><?= $rows['qty']; ?></td>
                    <td class="no-print" style="padding-left:10px;" valign="center"><?= $rows['mrp']; ?></td>

                    <td class="no-print" valign="center" style="padding-left:10px;"><?= $rows['total']; ?></td>
                    <td class="no-print" valign="center" style="padding-left:10px;"><?= $rows['comission_prectantage']; ?>
                    </td>
                    <td class="no-print" style="padding-left:10px;" valign="center"><?= $rows['comission_amount']; ?></td>
                    <td class="no-print" valign="center" style="padding-left:10px;"><?= $rows['purchase_value']; ?></td>

                    <td class="text" valign="center" style="padding-left:10px;"><?= $rows['purchase_value_per_unit']; ?>
                    </td>
                    <td class="no-print" valign="center" style="padding-left:10px;"><?= $rows['sale']; ?>
                    </td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['stock_after_sale']; ?></td>
                    <td  class="no-print" valign="center" style="padding-left:10px;"><?= $rows['price']; ?></td>

                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['stock_return']; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['value_after_return']; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['damage_qty']; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['damage_value']; ?></td>
                    <td class="text" style="padding-left:10px;" valign="center"><?= $rows['damage_remarks']; ?></td>


                    <td class="no-print" style="text-align: center;">
                      <?php if ($rows['sale'] == "" || $rows['sale'] == 0) { ?>
                        <a href="stock_delete_check_password.php?dId=<?= $dId ?>" class="btn btn-danger btn-sm">
                          <i class="fa fa-trash"></i>Delete
                        </a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php
                }
                ?>
                <!-- Totals Row -->
                <tr style="font-weight: bold;">
                  <td colspan="7" style="text-align: right;">Totals:</td>
                  <td><?= $totals['qty']; ?></td>
                  <td class="no-print"><?= number_format($totals['mrp'], 2); ?></td>
                  <td class="no-print"><?= number_format($totals['total'], 2); ?></td>
                  <td class="no-print"><?= number_format($totals['commission_percentage'], 2); ?></td>
                  <td class="no-print"><?= number_format($totals['commission_amount'], 2); ?></td>
                  <td class="no-print"><?= number_format($totals['purchase_value'], 2); ?></td>
                  <td><?= number_format($totals['purchase_value_per_unit'], 2); ?></td>
                  <td class="no-print"><?= $totals['sale']; ?></td>
                  <td><?= $totals['stock_after_sale']; ?></td>
                  <td class="no-print"><?= number_format($totals['price'], 2); ?></td>
                  <td><?= $totals['stock_return']; ?></td>
      <td><?= number_format($totals['value_after_return'], 2); ?></td>
      <td><?= $totals['damage_qty']; ?></td>
      <td><?= number_format($totals['damage_value'], 2); ?></td>

                  <td></td>
                </tr>
                <!-- Grand Totals Row (Return + Damage only)
    <tr style="font-weight: bold; border-top: 3px double #000; background: #e8ffe8;">
      <td colspan="7" style="text-align: right;">Grand Totals (Returns & Damages):</td>
      <td><?= $totals['stock_return']; ?></td>
      <td><?= number_format($totals['value_after_return'], 2); ?></td>
      <td><?= $totals['damage_qty']; ?></td>
      <td><?= number_format($totals['damage_value'], 2); ?></td>
      <td colspan="2"></td>
    </tr> -->
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