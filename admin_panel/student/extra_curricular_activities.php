<?php

include '../dbcon.php';

if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    die('Student ID is required.');
}

$id = (int) $_GET['student_id'];


/*
|--------------------------------------------------------------------------
| Default Values
|--------------------------------------------------------------------------
*/

$scl_name    = '';
$scl_class   = '';
$scl_roll_no = '';
$scl_reg_no  = '';
$scl_session = '';
$student_image = '';


/*
|--------------------------------------------------------------------------
| Student Information
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            scl_name,
            scl_class,
            scl_roll_no,
            scl_reg_no,
            scl_session,
            image
        FROM rrsv_student_registration
        WHERE id = $id
        LIMIT 1";

$res = mysqli_query($con, $sql);

if (!$res) {
    die('Student Query Error: ' . mysqli_error($con));
}

$student = mysqli_fetch_assoc($res);

if (!$student) {
    die('Student not found.');
}

$student_image = $student['image'];

$scl_name    = $student['scl_name'];
$scl_class   = $student['scl_class'];
$scl_roll_no = $student['scl_roll_no'];
$scl_reg_no  = $student['scl_reg_no'];
$scl_session = $student['scl_session'];


/*
|--------------------------------------------------------------------------
| Check Marksheet
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM rrsv_marksheet
        WHERE student_id = '$id'
          AND session = '$scl_session'
          AND class = '$scl_class'
          AND LOWER(subject) <> 'drawing'";

$qryresult = mysqli_query($con, $sql);

if (!$qryresult) {
    die('Marksheet Query Error: ' . mysqli_error($con));
}

$totalrecord = mysqli_num_rows($qryresult);

if ($totalrecord === 0) {
?>
<script>
    alert('Please enter minimum 1 unit to print marksheet!');
    window.history.back();
</script>
<?php
    exit;
}


/*
|--------------------------------------------------------------------------
| Behaviour / Assessment
|--------------------------------------------------------------------------
*/

function check_qry($id, $unit, $activity, $scl_session)
{
    include('include/dbcon.php');

    $cur = (int) $scl_session;

    switch ($unit) {

        case 1:
            $start_date = $cur . '-01-01';
            $end_date   = $cur . '-04-30';
            break;

        case 2:
            $start_date = $cur . '-05-01';
            $end_date   = $cur . '-09-30';
            break;

        case 3:
            $start_date = $cur . '-10-01';
            $end_date   = $cur . '-12-31';
            break;

        default:
            $start_date = $cur . '-01-01';
            $end_date   = $cur . '-12-31';
            break;
    }

    $activity = mysqli_real_escape_string($con, $activity);

    $sql = "SELECT COUNT(activties) AS activity
            FROM rrsv_student_behaviour
            WHERE student_id = $id
              AND activties = '$activity'
              AND date BETWEEN '$start_date' AND '$end_date'";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        return 0;
    }

    $row = mysqli_fetch_assoc($res);

    return (int) ($row['activity'] ?? 0);
}


/*
|--------------------------------------------------------------------------
| Behaviour Grade
|--------------------------------------------------------------------------
*/

function get_qry($id, $unit, $activity, $scl_session)
{
    $result = check_qry(
        $id,
        $unit,
        $activity,
        $scl_session
    );

    if ($result > 18) {
        return 'D';
    }

    if ($result > 12) {
        return 'C';
    }

    if ($result > 6) {
        return 'B';
    }

    return 'A';
}


/*
|--------------------------------------------------------------------------
| Absent
|--------------------------------------------------------------------------
*/

function absent_qry($id, $unit, $scl_class, $scl_session)
{
    include('include/dbcon.php');

    $cur = (int) $scl_session;

    switch ($unit) {

        case 1:
            $start_date = $cur . '-01-01';
            $end_date   = $cur . '-04-30';
            break;

        case 2:
            $start_date = $cur . '-05-01';
            $end_date   = $cur . '-09-30';
            break;

        case 3:
            $start_date = $cur . '-10-01';
            $end_date   = $cur . '-12-31';
            break;

        default:
            $start_date = $cur . '-01-01';
            $end_date   = $cur . '-12-31';
            break;
    }

    $scl_class = mysqli_real_escape_string($con, $scl_class);

    $sql = "SELECT COUNT(status) AS status
            FROM rrsv_student_attendence
            WHERE student_id = $id
              AND date BETWEEN '$start_date' AND '$end_date'
              AND status = 'Absent'
              AND class_name = '$scl_class'
              AND scl_session = '$scl_session'";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        return 0;
    }

    $row = mysqli_fetch_assoc($res);

    return (int) ($row['status'] ?? 0);
}


/*
|--------------------------------------------------------------------------
| General Assessment Data
|--------------------------------------------------------------------------
*/

function get_data($student_id, $session, $class, $parm)
{
    include('include/dbcon.php');

    /*
    | Column name is controlled internally by your code.
    | Do not pass user input directly as $parm.
    */

    $allowed = [
        'unit',

        '1st_unit_body_weight',
        '2nd_unit_body_weight',
        '3rd_unit_body_weight',

        '1st_unit_home_project',
        '2nd_unit_home_project',
        '3rd_unit_home_project',

        '1st_unit_grade',
        '2nd_unit_grade',
        '3rd_unit_grade'
    ];

    if (!in_array($parm, $allowed, true)) {
        return '';
    }

    $session = mysqli_real_escape_string($con, $session);
    $class   = mysqli_real_escape_string($con, $class);

    $sql = "SELECT `$parm`
            FROM rrsv_marksheet_unit
            WHERE student_id = $student_id
              AND session = '$session'
              AND class = '$class'
            LIMIT 1";

    $res = mysqli_query($con, $sql);

    if (!$res) {
        return '';
    }

    $row = mysqli_fetch_assoc($res);

    return $row[$parm] ?? '';
}


/*
|--------------------------------------------------------------------------
| Unit
|--------------------------------------------------------------------------
*/

$check_unit = get_data(
    $id,
    $scl_session,
    $scl_class,
    'unit'
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        General Assessment -
        <?= htmlspecialchars($scl_name); ?>
    </title>


    <style>

        /* =========================================================
           RESET
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #e5e5e5;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }


        /* =========================================================
           SCREEN TOOLBAR
        ========================================================= */

        .print-toolbar {

            width: 210mm;

            margin: 15px auto;

            text-align: right;
        }

        .btn {

            border: 0;

            padding: 8px 18px;

            margin-left: 5px;

            border-radius: 4px;

            cursor: pointer;

            font-size: 14px;
        }

        .btn-back {

            background: #6c757d;

            color: #fff;
        }

        .btn-print {

            background: #0d6efd;

            color: #fff;
        }


        /* =========================================================
           A4 PAGE
        ========================================================= */

        .a4-page {

            width: 210mm;

            min-height: 297mm;

            margin: 0 auto;

            padding: 10mm;

            background: #fff;

            box-shadow:
                0 0 8px rgba(0, 0, 0, 0.25);
        }


        /* =========================================================
           SCHOOL TITLE
        ========================================================= */

        .school-title {

            text-align: center;

            font-size: 19px;

            font-weight: 700;

            text-decoration: underline;

            margin: 0 0 5px 0;

            line-height: 1.2;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .header-table {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 5mm;
        }

        .header-table td {

            border: none;

            padding: 2px;

            vertical-align: middle;

            text-align: center;
        }

        .school-logo {

            width: 25mm;

            height: 25mm;

            object-fit: contain;
        }

        .student-photo {

            width: 25mm;

            height: 25mm;

            object-fit: cover;

            border: 1px solid #000;
        }

        .school-info {

            font-size: 11px;

            font-weight: 600;

            line-height: 1.5;
        }


        /* =========================================================
           STUDENT INFORMATION
        ========================================================= */

        .student-info {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 6mm;
        }

        .student-info td {

            border: 1px solid #000;

            padding: 5px 7px;

            font-size: 10px;

            font-weight: 600;
        }

        .student-name {

            font-size: 12px !important;

            font-weight: 700 !important;
        }


        /* =========================================================
           GENERAL ASSESSMENT TABLE
        ========================================================= */

        .assessment-table {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;
        }

        .assessment-table th,
        .assessment-table td {

            border: 1px solid #000;

            text-align: center;

            vertical-align: middle;

            padding: 5px 4px;

            font-size: 10px;

            line-height: 1.2;
        }

        .assessment-table th {

            font-weight: 700;

            background: #f3f3f3;
        }

        .assessment-table th:first-child,
        .assessment-table td:first-child {

            width: 55%;

            text-align: left;

            padding-left: 8px;

            font-weight: 600;
        }

        .assessment-table th:not(:first-child),
        .assessment-table td:not(:first-child) {

            width: 15%;
        }

        .section-title {

            font-size: 12px !important;

            text-align: center !important;

            font-weight: 700 !important;

            background: #eaeaea !important;
        }


        /* =========================================================
           SIGNATURE
        ========================================================= */

        .signature-table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 10mm;
        }

        .signature-table td {

            border: 1px solid #000;

            text-align: center;

            font-size: 10px;

            padding: 5px;
        }

        .signature-title {

            font-weight: 700;

            height: 10mm;
        }

        .signature-space {

            height: 30mm;
        }


        /* =========================================================
           PRINT
        ========================================================= */

        @page {

            size: A4 portrait;

            margin: 0;
        }

        @media print {

            html,
            body {

                width: 210mm;

                height: 297mm;

                background: #fff;

                margin: 0;

                padding: 0;
            }

            .print-toolbar {

                display: none !important;
            }

            .a4-page {

                width: 210mm;

                min-height: 297mm;

                margin: 0;

                padding: 10mm;

                box-shadow: none;
            }

            .school-title {

                font-size: 18px;
            }

            .school-info {

                font-size: 10px;
            }

            .student-info td {

                font-size: 10px;

                padding: 4px 6px;
            }

            .assessment-table th,
            .assessment-table td {

                font-size: 9px;

                padding: 4px 3px;
            }

            .signature-table td {

                font-size: 9px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     BUTTONS
========================================================= -->

<div class="print-toolbar">

    <button
        type="button"
        class="btn btn-back"
        onclick="window.history.back();">

        Back

    </button>


    <button
        type="button"
        class="btn btn-print"
        onclick="window.print();">

        Print

    </button>

</div>


<!-- =========================================================
     A4 PAGE
========================================================= -->

<div class="a4-page">


    <!-- SCHOOL TITLE -->

    <h1 class="school-title">

        RASULPUR RAMKRISHNA SARADA VIDYAPITH

    </h1>


    <!-- =====================================================
         SCHOOL HEADER
    ====================================================== -->

    <table class="header-table">

        <tr>

            <td style="width:20%;">

                <img
                    src="../../libray/images/logo.jpeg"
                    class="school-logo"
                    alt="School Logo">

            </td>


            <td style="width:60%;">

                <div class="school-info">

                    Reg. No. : SO196094
                    &nbsp; | &nbsp;
                    U-DISE Code : 19251610302
                    &nbsp; | &nbsp;
                    ESTD : 2012

                    <br>

                    Baidyadanga, Rasulpur, Memari,
                    Purba Bardhaman,
                    Pin - 713151

                </div>

            </td>


            <td style="width:20%;">

                <?php if (!empty($student_image)): ?>

                    <img
                        src="../../student_reg_image/<?= htmlspecialchars($student_image); ?>"
                        class="student-photo"
                        alt="Student Photo">

                <?php endif; ?>

            </td>

        </tr>

    </table>


    <!-- =====================================================
         STUDENT DETAILS
    ====================================================== -->

    <table class="student-info">

        <tr>

            <td colspan="4"
                class="student-name">

                Name :
                <?= htmlspecialchars($scl_name); ?>

            </td>

        </tr>


        <tr>

            <td>

                Registration No :
                <?= htmlspecialchars($scl_reg_no); ?>

            </td>


            <td>

                Session :
                <?= htmlspecialchars($scl_session); ?>

            </td>


            <td>

                Class :
                <?= htmlspecialchars($scl_class); ?>

            </td>


            <td>

                Roll No :
                <?= htmlspecialchars($scl_roll_no); ?>

            </td>

        </tr>

    </table>


    <!-- =====================================================
         GENERAL ASSESSMENT
    ====================================================== -->

    <table class="assessment-table">

        <thead>

            <tr>

                <th colspan="4"
                    class="section-title">

                    GENERAL ASSESSMENT

                </th>

            </tr>


            <tr>

                <th>
                    GENERAL ASSESSMENT
                </th>

                <th>
                    1st Unit
                </th>

                <th>
                    2nd Unit
                </th>

                <th>
                    3rd Unit
                </th>

            </tr>

        </thead>


        <tbody>


            <!-- BEHAVIOUR -->

            <?php if ($check_unit == '2nd_unit'): ?>

                <tr>

                    <td>BEHAVIOUR</td>

                    <td>
                        <?= get_qry(
                            $id,
                            1,
                            'Behaviour',
                            $scl_session
                        ); ?>
                    </td>

                    <td>--</td>

                    <td>--</td>

                </tr>


            <?php elseif ($check_unit == '3rd_unit'): ?>

                <tr>

                    <td>BEHAVIOUR</td>

                    <td>
                        <?= get_qry(
                            $id,
                            1,
                            'Behaviour',
                            $scl_session
                        ); ?>
                    </td>

                    <td>
                        <?= get_qry(
                            $id,
                            2,
                            'Behaviour',
                            $scl_session
                        ); ?>
                    </td>

                    <td>--</td>

                </tr>


            <?php else: ?>

                <tr>

                    <td>BEHAVIOUR</td>

                    <td>
                        <?= get_qry(
                            $id,
                            1,
                            'Behaviour',
                            $scl_session
                        ); ?>
                    </td>

                    <td>
                        <?= get_qry(
                            $id,
                            2,
                            'Behaviour',
                            $scl_session
                        ); ?>
                    </td>

                    <td>
                        <?= get_qry(
                            $id,
                            3,
                            'Behaviour',
                            $scl_session
                        ); ?>
                    </td>

                </tr>

            <?php endif; ?>


            <!-- NEATNESS -->

            <tr>

                <td>NEATNESS</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Neatness',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Neatness',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Neatness',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- DISCIPLINE -->

            <tr>

                <td>DISCIPLINE</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Discipline',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Discipline',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Discipline',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- SELF CONFIDENCE -->

            <tr>

                <td>SELF CONFIDENCE</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Self_confidence',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Self_confidence',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Self_confidence',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- RESPONSIBILITY -->

            <tr>

                <td>RESPONSIBILITY</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Responsibility',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Responsibility',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Responsibility',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- INITIATIVE -->

            <tr>

                <td>INITIATIVE</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Initiative',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Initiative',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Initiative',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- CONCENTRATION -->

            <tr>

                <td>CONCENTRATION</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Concentration',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Concentration',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Concentration',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- PUNCTUALITY -->

            <tr>

                <td>PUNCTUALITY</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Punctuality',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Punctuality',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Punctuality',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- REGULARITY -->

            <tr>

                <td>REGULARITY</td>

                <td>
                    <?= get_qry(
                        $id,
                        1,
                        'Regularity',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        2,
                        'Regularity',
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= get_qry(
                        $id,
                        3,
                        'Regularity',
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- BODY WEIGHT -->

            <tr>

                <td>BODY WEIGHT</td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '1st_unit_body_weight'
                        )
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '2nd_unit_body_weight'
                        )
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '3rd_unit_body_weight'
                        )
                    ); ?>
                </td>

            </tr>


            <!-- HOME PROJECT -->

            <tr>

                <td>HOME PROJECT</td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '1st_unit_home_project'
                        )
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '2nd_unit_home_project'
                        )
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '3rd_unit_home_project'
                        )
                    ); ?>
                </td>

            </tr>


            <!-- ABSENT -->

            <tr>

                <td>ABSENT</td>

                <td>
                    <?= absent_qry(
                        $id,
                        1,
                        $scl_class,
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= absent_qry(
                        $id,
                        2,
                        $scl_class,
                        $scl_session
                    ); ?>
                </td>

                <td>
                    <?= absent_qry(
                        $id,
                        3,
                        $scl_class,
                        $scl_session
                    ); ?>
                </td>

            </tr>


            <!-- GRADES -->

            <tr>

                <td>GRADES</td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '1st_unit_grade'
                        )
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '2nd_unit_grade'
                        )
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        get_data(
                            $id,
                            $scl_session,
                            $scl_class,
                            '3rd_unit_grade'
                        )
                    ); ?>
                </td>

            </tr>


        </tbody>

    </table>


    <!-- =====================================================
         SIGNATURE
    ====================================================== -->

    <table class="signature-table">

        <tr>

            <td class="signature-title">

                Principal Signature with Date

            </td>

        </tr>


        <tr>

            <td class="signature-space">

            </td>

        </tr>

    </table>


</div>


</body>

</html>
<script>
    function printDiv(id){
    var printContents = document.getElementById(id).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>