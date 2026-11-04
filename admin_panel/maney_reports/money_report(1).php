<?php
session_start();
include '../dbcon.php';

// ---------------------- GET FILTER VALUES ---------------------- //
$fromDate    = $_POST['fromDate'] ?? '';
$endDate     = $_POST['endDate'] ?? '';
$scl_class   = $_POST['scl_class'] ?? '';
$scl_session = $_POST['scl_session'] ?? '';

$_SESSION['fromDate']    = $fromDate;
$_SESSION['endDate']     = $endDate;
$_SESSION['scl_class']   = $scl_class;
$_SESSION['scl_session'] = $scl_session;

include('../header.php');
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <?php include '../top_navbar.php'; ?>
    </header>
    <?php include '../left_navbar.php'; ?>

    <div class="content-wrapper">
        <section class="content">
            <div class="box box-danger">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Money Report</h3>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="window.history.back()">Back</button>
                        <button class="btn btn-success btn-sm" onclick="window.print()">Print</button>
                        <a href="csv.php" class="btn btn-info btn-sm">Export CSV</a>
                    </div>
                </div>

                <div class="box-body">
                    
                     <div class="column-selector mb-3 btn-secondary alert alert-info ">
                        <strong>Select Columns to Display:</strong><br>
                        <?php
                        $allColumns = [
                            "Sl.No","Reg. No","Roll No","Name","Admission","ReAdmission","Uniform","Shoes","Winter",
                            "Admission Form","Bag","I.card","Diary","Books","Copy","Tution Fee","Total Car Cost (P.A)",
                            "Car Cost Paid (P.M)","Total Due for Car","Total Gross (with Car)","Total Gross (without Car)",
                            "Total Bill","Total Payable","After Billing Advance","After Billing Due","Total Due (with Car)",
                            "Total Due (without Car)","Statement Amount","Statement Due"
                        ];

                        foreach($allColumns as $index => $col){
                            echo '<label style="margin-right:10px;"><input type="checkbox" class="col-toggle" data-col="'.$index.'" checked> '.$col.'</label>';
                        }
                        ?>
                    </div>
                    <div class="income mb-3">
                        <form method="post" action="" id="dateRangeForm">
                            <label for="fromDate">From:</label>
                            <input type="date" name="fromDate" id="fromDate" value="<?php if(isset($_POST['fromDate'])) echo $_POST['fromDate']; ?>" required>
                    
                            <label for="endDate">To:</label>
                            <input type="date" name="endDate" id="endDate" value="<?php if(isset($_POST['endDate'])) echo $_POST['endDate']; ?>" required>
                    
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                            <button type="button" onclick="resetForm()" class="btn btn-secondary btn-sm">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Money Report</h3>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="window.history.back()">Back</button>
                        <button class="btn btn-success btn-sm" onclick="window.print()">Print</button>
                        <a href="csv.php" class="btn btn-info btn-sm">Export CSV</a>
                    </div>
                </div>

                <div class="box-body">
                    <div class="text-center mb-3">
                        <h4>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h4>
                        <p><strong>Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012 | Baidyadanga, Rasulpur, Memari, Purba Bardhaman, Pin -713151</strong></p>
                        <p><strong>Money Report for: <?= date('d-m-Y', strtotime($fromDate)); ?> to <?= date('d-m-Y', strtotime($endDate)); ?></strong></p>
                    </div>

                    <table id="moneyReport" class="table table-bordered table-striped text-sm">
                        <thead>
                             <tr>
                            
                             <tr/>
                             <tr>
                                 
                             <tr/>
                            <tr>
                                <th>Sl.No</th>
                                <th>Reg. No</th>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Admission</th>
                                <th>ReAdmission</th>
                                <th>Uniform</th>
                                <th>Shoes</th>
                                <th>Winter</th>
                                <th>Admission Form</th>
                                <th>Bag</th>
                                <th>I.card</th>
                                <th>Diary</th>
                                <th>Books</th>
                                <th>Copy</th>
                                <th>Tution Fee</th>
                                <th>Total Car Cost (P.A)</th>
                                <th>Car Cost Paid (P.M)</th>
                                <th>Total Due for Car</th>
                                <th>Total Gross (with Car)</th>
                                <th>Total Gross (without Car)</th>
                                <th>Total Bill</th>
                                <th>Total Payable</th>
                                <th>After Billing Advance</th>
                                <th>After Billing Due</th>
                                <th>Total Due (with Car)</th>
                                <th>Total Due (without Car)</th>
                                <th>Statement Amount</th>
                                <th>Statement Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php
                            
                            
// ---------------------- FETCH DATA ---------------------- //
// $sql = "SELECT a.*, b.scl_date, b.last_advance_due, b.payment_recive, b.admission_fee_val, b.re_admission_fee_val, 
//               b.uniformval, b.shoessockesval, b.sweaterslaxcap_val, b.admissionval, b.bagval, b.icardval, b.diaryval, 
//               b.bookcost, b.copycost, b.total_monthly_fee, b.car_cost, b.carfee, b.add_total_cost, b.re_total_cost, b.val_ad_du, b.advance_due, b.amount1, b.add_status 
//         FROM rrsv_student_registration AS a
//         JOIN rrsv_scl_studen_fee AS b ON a.id=b.pay_id
//         WHERE b.scl_date BETWEEN '$fromDate' AND '$endDate'
//         GROUP BY a.id";
$sql = "SELECT a.*, b.scl_date, b.last_advance_due, b.payment_recive
        FROM rrsv_student_registration AS a
        INNER JOIN rrsv_scl_studen_fee AS b ON a.id = b.pay_id
        WHERE b.scl_date BETWEEN '$fromDate' AND '$endDate'
        GROUP BY a.id";

$result = mysqli_query($con, $sql);
$sl = 0;
$totalPaid = 0;
$totalDue = 0;

$result = mysqli_query($con, $sql);

// ---------------------- INITIALIZE TOTALS ---------------------- //
$totals = [];
$classes = ['NURSERY','KINDER GARDEN','STANDERD 1','STANDERD 2','STANDERD 3','STANDERD 4'];

foreach($classes as $cls) {
    $totals['books'][$cls]       = 0;
    $totals['copy'][$cls]        = 0;
    $totals['monthly_fee'][$cls] = 0;
}

$totals['admission_fee']       = 0;
$totals['readmission_fee']     = 0;
$totals['uniform']             = 0;
$totals['shoes']               = 0;
$totals['winter']              = 0;
$totals['admission_form']      = 0;
$totals['bag']                 = 0;
$totals['icard']               = 0;
$totals['diary']               = 0;
$totals['car_cost_total']      = 0;
$totals['car_fee_paid']        = 0;
$totals['car_due']             = 0;
$totals['gross_with_car']      = 0;
$totals['gross_without_car']   = 0;
$totals['bill_amount']         = 0;
$totals['payable']             = 0;
$totals['advance']             = 0;
$totals['due']                 = 0;
$totals['total_due_with_car']  = 0;
$totals['total_due_without_car'] = 0;
$totals['statement_amount']    = 0;
$totals['statement_due']       = 0;


        $sl = 1;
        while($row = mysqli_fetch_assoc($result)) {
           // print_r($row );die();
            $totalCarCost = $row['car_cost']*12;
            $gross_with_car = $row['total_monthly_fee'] + $row['add_total_cost'] + $row['copycost'] + $totalCarCost;
            $gross_without_car = $row['total_monthly_fee'] + $row['add_total_cost'] + $row['copycost'];

            // -------------------- UPDATE TOTALS -------------------- //
            $totals['admission_fee'] += $row['admission_fee_val'];
            $totals['readmission_fee'] += $row['re_admission_fee_val'];
            $totals['uniform'] += $row['uniformval'];
            $totals['shoes'] += $row['shoessockesval'];
            $totals['winter'] += $row['sweaterslaxcap_val'];
            $totals['admission_form'] += $row['admissionval'];
            $totals['bag'] += $row['bagval'];
            $totals['icard'] += $row['icardval'];
            $totals['diary'] += $row['diaryval'];

            if(in_array($row['scl_class'],$classes)) {
                $totals['books'][$row['scl_class']] += $row['bookcost'];
                $totals['copy'][$row['scl_class']] += $row['copycost'];
                $totals['monthly_fee'][$row['scl_class']] += $row['total_monthly_fee'];
            }

            $totals['car_cost_total'] += $totalCarCost;
            $totals['car_fee_paid'] += $row['carfee'];
            $totals['car_due'] += $totalCarCost - $row['carfee'];
            $totals['gross_with_car'] += $gross_with_car;
            $totals['gross_without_car'] += $gross_without_car;
            $totals['bill_amount'] += $row['total_monthly_fee'] + $row['add_total_cost'];
            $totals['payable'] += $row['payment_recive'];
            if($row['val_ad_du']=='Advance') $totals['advance'] += $row['advance_due'];
            if($row['val_ad_du']=='Due') $totals['due'] += $row['advance_due'];
            $totals['total_due_with_car'] += $gross_with_car - $row['payment_recive'];
            $totals['total_due_without_car'] += $gross_without_car - $row['payment_recive'];
            $totals['statement_amount'] += $row['amount1'];
            $totals['statement_due'] += max(0,$row['amount1']-$row['payment_recive']);
        ?>
                          
                            <tr>
                                <td><?= $sl++; ?></td>
                                <td><?= $row['scl_reg_no']; ?></td>
                                <td><?= $row['scl_roll_no']; ?></td>
                                <td><?= htmlspecialchars($row['scl_name']); ?></td>
                                <td><?= $row['admission_fee_val']; ?></td>
                                <td><?= $row['re_admission_fee_val']; ?></td>
                                <td><?= $row['uniformval']; ?></td>
                                <td><?= $row['shoessockesval']; ?></td>
                                <td><?= $row['sweaterslaxcap_val']; ?></td>
                                <td><?= $row['admissionval']; ?></td>
                                <td><?= $row['bagval']; ?></td>
                                <td><?= $row['icardval']; ?></td>
                                <td><?= $row['diaryval']; ?></td>
                                <td><?= $row['bookcost']; ?></td>
                                <td><?= $row['copycost']; ?></td>
                                <td><?= $row['total_monthly_fee']; ?></td>
                                <td><?= $totalCarCost; ?></td>
                                <td><?= $row['carfee']; ?></td>
                                <td><?= $totalCarCost - $row['carfee']; ?></td>
                                <td><?= $totalGrossWithCar; ?></td>
                                <td><?= $totalGrossWithoutCar; ?></td>
                                <td><?= $row['total_monthly_fee'] + $row['add_total_cost']; ?></td>
                                <td><?= $row['payment_recive']; ?></td>
                                <td><?= ($row['val_ad_du']=='Advance')?$row['advance_due']:0; ?></td>
                                <td><?= ($row['val_ad_du']=='Due')?$row['advance_due']:0; ?></td>
                                <td><?= $totalGrossWithCar - $row['payment_recive']; ?></td>
                                <td><?= $totalGrossWithoutCar - $row['payment_recive']; ?></td>
                                <td><?= $row['amount1']; ?></td>
                                <td><?= $row['amount1'] - $row['payment_recive']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <?php include '../footer.php'; ?>
</div>
 <script src="../assets/js/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="../assets/js/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="../assets/js/app.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../assets/js/demo.js"></script>
<script>



$(document).ready(function(){
    var table = $('#moneyReport').DataTable({
        "scrollX": true,
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });

    // ===== Dynamic column toggle =====
    $('.col-toggle').on('change', function(){
        var colIndex = $(this).data('col');
        table.column(colIndex).visible(!table.column(colIndex).visible());
    });
});
</script>

</body>
</html>
