<style>
  /
  /*Required*/
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

  .status {

    margin-left: 284px;

  }
</style>

<?php
include('include/dbcon.php');
$id = 0;
$teacher_id = "";
$month = 0;
$year = "";
if (isset($_GET['teacher_id'])) {
  $teacher_id = $myDB->escape_string(trim(addslashes($_GET['teacher_id'])));
  $month = $myDB->escape_string(trim(addslashes($_GET['month'])));
  $year = $myDB->escape_string(trim(addslashes($_GET['year'])));
  // $endDate               =$myDB->escape_string(trim(addslashes($_GET['endDate'])));
}

$sql5 = "SELECT full_name,monthly_salary FROM `rrsv_teacher` where id=$teacher_id";
$result5 = mysqli_query($myDB, $sql5);
$s = mysqli_fetch_array($result5);
$full_name = $s['full_name'];
$monthly_salary = $s['monthly_salary'];
///////////////////////////////
//////////////////////////////////

//$sql6 = "SELECT sum(leave_adjust_days) AS amount_val FROM `rrsv_teacher_salary_statement` where leave_adjust_days=1 and teacher_id=$teacher_id and salary_month like '%$year%'";
$sql6 = "SELECT sum(leave_adjust_days) AS amount_val FROM `rrsv_teacher_salary_statement` where leave_adjust_days=1 and teacher_id=$teacher_id and year='$year'";
$result6 = mysqli_query($myDB, $sql6);
$s = mysqli_fetch_array($result6);
$cl_count = $s['amount_val'];
$remaining_cl = 10 - $cl_count;



?>
<script language="javascript" type="text/javascript">

  function dosearch() {
    document.frmsearch.method = 'post';
    document.frmsearch.action = 'printregister.php';
    document.frmsearch.submit();
    return true;
  }
</script>
<!doctype html>
<html>

<head>
  <!-- Required meta tags -->

  <!-- Bootstrap CSS -->

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <title>Status Report</title>
  <link rel="shortcut icon" href="libray/images/logo.jpeg" />
  <link href="css/registerbooks.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />

</head>

<body>
  <button type="button" id="backPageButton" onclick="window.history.back()" class="btn btn-info"
    style="color: #fff">Back</button>
  <button type="button" onclick="window.print();" class="btn btn-info">Print</button>

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

                <p style="font-weight: bold;">Teacher Status Report
                </p>
              </div>

              <h5 style="margin-left: 74px;">Teacher Name : <span><?= $full_name ?></span>&nbsp; &nbsp;Gross Salary :
                <?= $monthly_salary ?> &nbsp; &nbsp;Used CL : <span id="totalstatus"><?= $cl_count ?>&nbsp;
                  &nbsp;Remaing CL
                  : <span id="totalstatus"><?= $remaining_cl ?></span>
              </h5>
              <table border="1">
                <tr>
                  <th width="2%">SL.<br>NO</th>
                  <th width="2%">Month & Year</th>
                  <th width="2%">Working Days</th>
                  <th width="2%">Total Late</th>
                  <th width="2%">Total P.C Class</th>
                  <th width="2%">CL Adjust</th>
                  <th width="2%">Deduct Amount</th>
                  <th width="2%">In Hand Amount</th>
                </tr>
                <?php
                $sql = "SELECT * FROM rrsv_teacher_salary_statement WHERE teacher_id = $teacher_id AND year = '$year'";
                $result = mysqli_query($myDB, $sql);

                $sum_working_days = 0;
                $sum_late_days = 0;
                $sum_no_of_pc_class = 0;
                $sum_leave_adjust_days = 0;
                $sum_deduct_amount = 0;
                $sum_total = 0;

                $l = mysqli_num_rows($result);
                if ($l < 1) {
                  echo '<tr><td class="errtext" align="center" colspan=8>No Record Found.</td></tr>';
                } else {
                  $i = 1;
                  while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $sum_working_days += $rows['working_days'];
                    $sum_late_days += $rows['late_days'];
                    $sum_no_of_pc_class += $rows['no_of_pc_class'];
                    $sum_leave_adjust_days += $rows['leave_adjust_days'];
                    $sum_deduct_amount += $rows['deduct_amount'];
                    $sum_total += $rows['total'];
                    ?>
                    <tr>
                      <td width="2%"><?= $i++; ?></td>
                      <td width="2%"><?= $rows['salary_month']; ?></td>
                      <td width="2%"><?= $rows['working_days']; ?></td>
                      <td width="2%"><?= $rows['late_days']; ?></td>
                      <td width="2%"><?= $rows['no_of_pc_class']; ?></td>
                      <td width="2%"><?= $rows['leave_adjust_days']; ?></td>
                      <td width="2%"><?= $rows['deduct_amount']; ?></td>
                      <td width="2%"><?= $rows['total']; ?></td>
                    </tr>
                    <?php
                  }
                }
                ?>
                <!-- Sum row -->
                <tr style="font-weight: bold;">
                  <td colspan="2" align="center">Total</td>
                  <td><?= $sum_working_days; ?></td>
                  <td><?= $sum_late_days; ?></td>
                  <td><?= $sum_no_of_pc_class; ?></td>
                  <td><?= $sum_leave_adjust_days; ?></td>
                  <td><?= $sum_deduct_amount; ?></td>
                  <td><?= $sum_total; ?></td>
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
    $(document).ready(function () {
      alert();

    });

  </script>


</body>

</html>