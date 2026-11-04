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

                    <!-- ===== DATE RANGE FORM ===== -->
                    <div class="income mb-3">
                        <form method="post" action="" id="dateRangeForm">
                            <label>From:</label>
                            <input type="date" name="fromDate" value="<?= $fromDate ?>" required>
                            <label>To:</label>
                            <input type="date" name="endDate" value="<?= $endDate ?>" required>
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                            <button type="button" onclick="resetForm()" class="btn btn-secondary btn-sm">Reset</button>
                        </form>
                    </div>

                    <!-- ===== DYNAMIC COLUMN SELECTOR ===== -->
                    <div class="column-selector mb-3">
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

                    <!-- ===== DATA TABLE ===== -->
                    <table id="moneyReport" class="table table-bordered table-striped text-sm">
                        <thead>
                            <tr>
                                <?php foreach($allColumns as $col): ?>
                                    <th><?= $col ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($fromDate && $endDate){
                                $sql = "SELECT a.*, b.scl_date, b.last_advance_due, b.payment_recive,
                                               b.admission_fee_val, b.re_admission_fee_val, b.uniformval, b.shoessockesval,
                                               b.sweaterslaxcap_val, b.admissionval, b.bagval, b.icardval, b.diaryval,
                                               b.bookcost, b.copycost, b.total_monthly_fee, b.car_cost, b.carfee,
                                               b.add_total_cost, b.re_total_cost, b.val_ad_du, b.advance_due, b.amount1
                                        FROM rrsv_student_registration AS a
                                        INNER JOIN rrsv_scl_studen_fee AS b ON a.id = b.pay_id
                                        WHERE b.scl_date BETWEEN '$fromDate' AND '$endDate'
                                        GROUP BY a.id";
                                $result = mysqli_query($con,$sql);
                                $sl = 1;
                                while($row = mysqli_fetch_assoc($result)){
                                    $totalCarCost = $row['car_cost']*12;
                                    $gross_with_car = $row['total_monthly_fee'] + $row['add_total_cost'] + $row['copycost'] + $totalCarCost;
                                    $gross_without_car = $row['total_monthly_fee'] + $row['add_total_cost'] + $row['copycost'];

                                    echo "<tr>";
                                    echo "<td>{$sl}</td>";
                                    echo "<td>{$row['scl_reg_no']}</td>";
                                    echo "<td>{$row['scl_roll_no']}</td>";
                                    echo "<td>".htmlspecialchars($row['scl_name'])."</td>";
                                    echo "<td>{$row['admission_fee_val']}</td>";
                                    echo "<td>{$row['re_admission_fee_val']}</td>";
                                    echo "<td>{$row['uniformval']}</td>";
                                    echo "<td>{$row['shoessockesval']}</td>";
                                    echo "<td>{$row['sweaterslaxcap_val']}</td>";
                                    echo "<td>{$row['admissionval']}</td>";
                                    echo "<td>{$row['bagval']}</td>";
                                    echo "<td>{$row['icardval']}</td>";
                                    echo "<td>{$row['diaryval']}</td>";
                                    echo "<td>{$row['bookcost']}</td>";
                                    echo "<td>{$row['copycost']}</td>";
                                    echo "<td>{$row['total_monthly_fee']}</td>";
                                    echo "<td>{$totalCarCost}</td>";
                                    echo "<td>{$row['carfee']}</td>";
                                    echo "<td>".($totalCarCost - $row['carfee'])."</td>";
                                    echo "<td>{$gross_with_car}</td>";
                                    echo "<td>{$gross_without_car}</td>";
                                    echo "<td>".($row['total_monthly_fee'] + $row['add_total_cost'])."</td>";
                                    echo "<td>{$row['payment_recive']}</td>";
                                    echo "<td>".($row['val_ad_du']=='Advance'?$row['advance_due']:0)."</td>";
                                    echo "<td>".($row['val_ad_du']=='Due'?$row['advance_due']:0)."</td>";
                                    echo "<td>".($gross_with_car - $row['payment_recive'])."</td>";
                                    echo "<td>".($gross_without_car - $row['payment_recive'])."</td>";
                                    echo "<td>{$row['amount1']}</td>";
                                    echo "<td>".($row['amount1'] - $row['payment_recive'])."</td>";
                                    echo "</tr>";
                                    $sl++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <?php include '../footer.php'; ?>
</div>

<!-- ===== SCRIPTS ===== -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script>
function resetForm(){
    $('#fromDate').val('');
    $('#endDate').val('');
    $('#dateRangeForm').submit();
}

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
