<?php
session_start();
include '../dbcon.php';

// --------------------------------------------------
// GET FILTER VALUES
// --------------------------------------------------
$fromDate    = $_POST['fromDate'] ?? '';
$endDate     = $_POST['endDate'] ?? '';
$scl_reg_no  = trim($_POST['scl_reg_no'] ?? '');
$scl_session = trim($_POST['scl_session'] ?? '');

// --------------------------------------------------
// HEADER
// --------------------------------------------------
include('../header.php');
?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

    <!-- Header -->
    <header class="main-header">
        <?php include '../top_navbar.php'; ?>
    </header>

    <!-- Sidebar -->
    <?php include '../left_navbar.php'; ?>

    <!-- Content -->
    <div class="content-wrapper">

        <section class="content">

            <!-- ==========================================
                 SEARCH FORM
            =========================================== -->
            <div class="box box-danger">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-search"></i>
                        Student Money Bill
                    </h3>
                </div>

                <div class="box-body">

                    <form method="post" action="">

                        <div class="row">

                            <!-- Registration No -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Student Registration No.</label>

                                    <input
                                        type="text"
                                        name="scl_reg_no"
                                        class="form-control"
                                        placeholder="Enter Registration No."
                                        value="<?= htmlspecialchars($scl_reg_no) ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Session -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Session</label>

                                    <input
                                        type="text"
                                        name="scl_session"
                                        class="form-control"
                                        placeholder="Example: 2026"
                                        value="<?= htmlspecialchars($scl_session) ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- From Date -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date</label>

                                    <input
                                        type="date"
                                        name="fromDate"
                                        class="form-control"
                                        value="<?= htmlspecialchars($fromDate) ?>"
                                    >
                                </div>
                            </div>

                            <!-- To Date -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>

                                    <input
                                        type="date"
                                        name="endDate"
                                        class="form-control"
                                        value="<?= htmlspecialchars($endDate) ?>"
                                    >
                                </div>
                            </div>

                            <!-- Search -->
                            <div class="col-md-1">
                                <div class="form-group">

                                    <label>&nbsp;</label>

                                    <button
                                        type="submit"
                                        class="btn btn-primary btn-block"
                                    >
                                        <i class="fa fa-search"></i>
                                    </button>

                                </div>
                            </div>

                        </div>

                    </form>

                </div>
            </div>


<?php

// ==================================================
// ONLY RUN QUERY WHEN REGISTRATION NO IS PROVIDED
// ==================================================

if ($scl_reg_no != '' && $scl_session != '') {

    // --------------------------------------------------
    // ESCAPE VALUES
    // --------------------------------------------------
    $reg_no = mysqli_real_escape_string($con, $scl_reg_no);
    $session = mysqli_real_escape_string($con, $scl_session);

    // --------------------------------------------------
    // DATE CONDITION
    // --------------------------------------------------
    $dateCondition = "";

    if ($fromDate != '' && $endDate != '') {

        $from = mysqli_real_escape_string($con, $fromDate);
        $to   = mysqli_real_escape_string($con, $endDate);

        $dateCondition = "
            AND b.scl_date BETWEEN '$from' AND '$to'
        ";
    }

    // --------------------------------------------------
    // STUDENT MONEY BILL QUERY
    // IMPORTANT:
    // NO GROUP BY a.id
    // because one student has multiple payment records.
    // --------------------------------------------------

    $sql = "
        SELECT
            a.scl_reg_no,
            a.scl_roll_no,
            a.scl_name,
            a.scl_class,
            a.scl_section,
            a.scl_session,

            b.scl_date,

            b.admission_fee_val,
            b.re_admission_fee_val,

            a.uniformval,
            a.shoessockesval,
            a.sweaterslaxcap_val,
            a.admissionval,
            a.bagval,
            a.icardval,
            a.diaryval,
            a.bookcost,
            a.copycost,

            a.totalmonthly_fee_val,

            b.car_cost,

            a.carfee,
            a.total_monthly_fee,
            a.add_total_cost,
            a.re_total_cost,

            a.paymentrecive,
            a.amount1,

            b.advance_due,
            b.val_ad_du,
            b.last_advance_due,

            a.add_status

        FROM rrsv_student_registration AS a

        INNER JOIN rrsv_scl_studen_fee AS b
            ON a.id = b.pay_id

        WHERE
            a.scl_reg_no = '$reg_no'
            AND a.scl_session = '$session'
            $dateCondition

        ORDER BY
            b.scl_date DESC,
            b.id DESC
    ";

    $result = mysqli_query($con, $sql);

    if (!$result) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Database Error:</strong> ';
        echo htmlspecialchars(mysqli_error($con));
        echo '</div>';
    } else {

        // --------------------------------------------------
        // CHECK STUDENT FOUND
        // --------------------------------------------------

        if (mysqli_num_rows($result) == 0) {

            echo '
            <div class="alert alert-warning">
                <i class="fa fa-warning"></i>
                No money bill record found for Registration No.
                <strong>' . htmlspecialchars($scl_reg_no) . '</strong>
            </div>
            ';

        } else {

            // --------------------------------------------------
            // FETCH FIRST ROW FOR STUDENT INFORMATION
            // --------------------------------------------------

            $studentRows = [];

            while ($r = mysqli_fetch_assoc($result)) {
                $studentRows[] = $r;
            }

            $student = $studentRows[0];

?>

            <!-- ==========================================
                 STUDENT INFORMATION
                 
                 SELECT a.scl_reg_no, a.scl_roll_no, a.scl_name, a.scl_class, a.scl_section, a.scl_session, b.scl_date, b.admission_fee_val, b.re_admission_fee_val, a.uniformval, a.shoessockesval, a.sweaterslaxcap_val, a.admissionval, a.bagval, a.icardval, a.diaryval, a.bookcost, a.copycost, a.totalmonthly_fee_val, b.car_cost, a.carfee, a.total_monthly_fee, a.add_total_cost, a.re_total_cost, a.paymentrecive, a.amount1, b.advance_due, b.val_ad_du, a.add_status, b.last_advance_due FROM rrsv_student_registration AS a INNER JOIN rrsv_scl_studen_fee AS b ON a.id = b.pay_id WHERE a.scl_reg_no = '2026-A-397' AND a.scl_session = '2026' ORDER BY b.scl_date DESC;
            =========================================== -->

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        <i class="fa fa-user"></i>
                        Student Information
                    </h3>

                </div>

                <div class="box-body">

                    <div class="row">

                        <div class="col-md-2">
                            <strong>Reg. No.</strong><br>
                            <?= htmlspecialchars($student['scl_reg_no']) ?>
                        </div>

                        <div class="col-md-1">
                            <strong>Roll</strong><br>
                            <?= htmlspecialchars($student['scl_roll_no']) ?>
                        </div>

                        <div class="col-md-3">
                            <strong>Student Name</strong><br>
                            <?= htmlspecialchars($student['scl_name']) ?>
                        </div>

                        <div class="col-md-2">
                            <strong>Class</strong><br>
                            <?= htmlspecialchars($student['scl_class']) ?>
                        </div>

                        <div class="col-md-2">
                            <strong>Section</strong><br>
                            <?= htmlspecialchars($student['scl_section']) ?>
                        </div>

                        <div class="col-md-2">
                            <strong>Session</strong><br>
                            <?= htmlspecialchars($student['scl_session']) ?>
                        </div>

                    </div>

                </div>
            </div>


            <!-- ==========================================
                 MONEY BILL
            =========================================== -->

            <div class="box box-success">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        <i class="fa fa-money"></i>
                        Monthly Money Bill
                    </h3>

                </div>

                <div class="box-body">

                    <div class="table-responsive">

                        <table
                            id="moneyReport"
                            class="table table-bordered table-striped table-hover"
                            style="width:100%;"
                        >

                            <thead>

                                <tr>

                                    <th>Sl.</th>

                                    <th>Date</th>

                                    <th>Admission</th>

                                    <th>Re-Admission</th>

                                    <th>Uniform</th>

                                    <th>Shoes</th>

                                    <th>Winter</th>

                                    <th>Admission Form</th>

                                    <th>Bag</th>

                                    <th>I.Card</th>

                                    <th>Diary</th>

                                    <th>Books</th>

                                    <th>Copy</th>

                                    <th>Tuition Fee</th>

                                    <th>Car Cost P.M.</th>

                                    <th>Car Cost P.A.</th>

                                    <th>Total Bill</th>

                                    <th>Payment</th>

                                    <th>Advance</th>

                                    <th>Due</th>

                                    <th>Last Advance/Due</th>

                                    <th>Statement Amount</th>

                                    <th>Statement Due</th>

                                </tr>

                            </thead>

                            <tbody>

<?php

            $sl = 1;

            // --------------------------------------------------
            // TOTAL VARIABLES
            // --------------------------------------------------

            $grandAdmission       = 0;
            $grandReAdmission    = 0;
            $grandUniform        = 0;
            $grandShoes          = 0;
            $grandWinter         = 0;
            $grandAdmissionForm  = 0;
            $grandBag            = 0;
            $grandIcard          = 0;
            $grandDiary          = 0;
            $grandBooks          = 0;
            $grandCopy           = 0;
            $grandTuition        = 0;
            $grandCarPM          = 0;
            $grandCarPA          = 0;
            $grandBill           = 0;
            $grandPayment        = 0;
            $grandAdvance        = 0;
            $grandDue            = 0;
            $grandStatement      = 0;
            $grandStatementDue   = 0;


            foreach ($studentRows as $row):

                // --------------------------------------------------
                // SAFE NUMERIC VALUES
                // --------------------------------------------------

                $admission =
                    (float)$row['admission_fee_val'];

                $reAdmission =
                    (float)$row['re_admission_fee_val'];

                $uniform =
                    (float)$row['uniformval'];

                $shoes =
                    (float)$row['shoessockesval'];

                $winter =
                    (float)$row['sweaterslaxcap_val'];

                $admissionForm =
                    (float)$row['admissionval'];

                $bag =
                    (float)$row['bagval'];

                $icard =
                    (float)$row['icardval'];

                $diary =
                    (float)$row['diaryval'];

                $books =
                    (float)$row['bookcost'];

                $copy =
                    (float)$row['copycost'];

                $tuition =
                    (float)$row['total_monthly_fee'];

                $carPM =
                    (float)$row['car_cost'];

                $carPA =
                    $carPM * 12;

                $payment =
                    (float)$row['paymentrecive'];

                $statementAmount =
                    (float)$row['amount1'];

                // --------------------------------------------------
                // ADVANCE
                // --------------------------------------------------

                $advance = 0;

                if ($row['val_ad_du'] == 'Advance') {
                    $advance = (float)$row['advance_due'];
                }

                // --------------------------------------------------
                // DUE
                // --------------------------------------------------

                $due = 0;

                if ($row['val_ad_du'] == 'Due') {
                    $due = (float)$row['advance_due'];
                }

                // --------------------------------------------------
                // TOTAL BILL
                // --------------------------------------------------

                $totalBill =
                    $admission
                    + $reAdmission
                    + $uniform
                    + $shoes
                    + $winter
                    + $admissionForm
                    + $bag
                    + $icard
                    + $diary
                    + $books
                    + $copy
                    + $tuition
                    + $carPM;

                // --------------------------------------------------
                // STATEMENT DUE
                // --------------------------------------------------

                $statementDue = 0;

                if ($statementAmount != 0) {
                    $statementDue =
                        abs($statementAmount - $payment);
                }


                // --------------------------------------------------
                // GRAND TOTAL
                // --------------------------------------------------

                $grandAdmission      += $admission;
                $grandReAdmission   += $reAdmission;
                $grandUniform       += $uniform;
                $grandShoes         += $shoes;
                $grandWinter        += $winter;
                $grandAdmissionForm += $admissionForm;
                $grandBag           += $bag;
                $grandIcard         += $icard;
                $grandDiary         += $diary;
                $grandBooks         += $books;
                $grandCopy          += $copy;
                $grandTuition       += $tuition;
                $grandCarPM         += $carPM;
                $grandCarPA         += $carPA;
                $grandBill          += $totalBill;
                $grandPayment       += $payment;
                $grandAdvance       += $advance;
                $grandDue           += $due;
                $grandStatement     += $statementAmount;
                $grandStatementDue  += $statementDue;

?>

                                <tr>

                                    <td><?= $sl++; ?></td>

                                    <td>
                                        <?= date(
                                            'd-m-Y',
                                            strtotime($row['scl_date'])
                                        ); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($admission, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($reAdmission, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($uniform, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($shoes, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($winter, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($admissionForm, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($bag, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($icard, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($diary, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($books, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($copy, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($tuition, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($carPM, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($carPA, 2); ?>
                                    </td>

                                    <td class="text-right">
                                        <strong>
                                            <?= number_format($totalBill, 2); ?>
                                        </strong>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format($payment, 2); ?>
                                    </td>

                                    <td class="text-right">

                                        <?php if ($advance > 0): ?>

                                            <span class="label label-success">
                                                <?= number_format($advance, 2); ?>
                                            </span>

                                        <?php else: ?>

                                            0.00

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-right">

                                        <?php if ($due > 0): ?>

                                            <span class="label label-danger">
                                                <?= number_format($due, 2); ?>
                                            </span>

                                        <?php else: ?>

                                            0.00

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-right">
                                        <?= number_format(
                                            (float)$row['last_advance_due'],
                                            2
                                        ); ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format(
                                            $statementAmount,
                                            2
                                        ); ?>
                                    </td>

                                    <td class="text-right">

                                        <?php if ($statementDue > 0): ?>

                                            <span class="text-danger">
                                                <strong>
                                                    <?= number_format(
                                                        $statementDue,
                                                        2
                                                    ); ?>
                                                </strong>
                                            </span>

                                        <?php else: ?>

                                            0.00

                                        <?php endif; ?>

                                    </td>

                                </tr>

<?php
            endforeach;
?>

                            </tbody>

                            <!-- =====================================
                                 GRAND TOTAL
                            ====================================== -->

                            <tfoot>

                                <tr>

                                    <th colspan="2">
                                        GRAND TOTAL
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandAdmission,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandReAdmission,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandUniform,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandShoes,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandWinter,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandAdmissionForm,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandBag,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandIcard,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandDiary,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandBooks,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandCopy,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandTuition,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandCarPM,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandCarPA,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandBill,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandPayment,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandAdvance,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandDue,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        -
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandStatement,
                                            2
                                        ); ?>
                                    </th>

                                    <th>
                                        <?= number_format(
                                            $grandStatementDue,
                                            2
                                        ); ?>
                                    </th>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>
            </div>


            <!-- ==========================================
                 SUMMARY
            =========================================== -->

            <div class="row">

                <div class="col-md-3">

                    <div class="small-box bg-blue">

                        <div class="inner">

                            <h3>
                                <?= number_format(
                                    $grandBill,
                                    2
                                ); ?>
                            </h3>

                            <p>Total Bill</p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-file-text"></i>
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="small-box bg-green">

                        <div class="inner">

                            <h3>
                                <?= number_format(
                                    $grandPayment,
                                    2
                                ); ?>
                            </h3>

                            <p>Total Payment</p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-money"></i>
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="small-box bg-aqua">

                        <div class="inner">

                            <h3>
                                <?= number_format(
                                    $grandAdvance,
                                    2
                                ); ?>
                            </h3>

                            <p>Total Advance</p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-arrow-up"></i>
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="small-box bg-red">

                        <div class="inner">

                            <h3>
                                <?= number_format(
                                    $grandDue,
                                    2
                                ); ?>
                            </h3>

                            <p>Total Due</p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-arrow-down"></i>
                        </div>

                    </div>

                </div>

            </div>

<?php
        }
    }
}

?>

        </section>

    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

</div>


<!-- ==================================================
     DATATABLE
=================================================== -->

<script>

$(document).ready(function () {

    $('#moneyReport').DataTable({

        "scrollX": true,

        "paging": true,

        "searching": true,

        "ordering": true,

        "responsive": false,

        "pageLength": 25,

        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        dom:
            'Bfrtip',

        buttons: [

            {
                extend: 'copy',
                title: 'Student Money Bill'
            },

            {
                extend: 'csv',
                title: 'Student Money Bill'
            },

            {
                extend: 'excel',
                title: 'Student Money Bill'
            },

            {
                extend: 'pdf',
                title: 'Student Money Bill',
                orientation: 'landscape',
                pageSize: 'A3'
            },

            {
                extend: 'print',
                title: ''
            },

            'colvis'

        ]

    });

});

</script>

</body>
</html>