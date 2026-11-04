<?php

include '../dbcon.php';

if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    die('Student ID is required.');
}

$id = (int) $_GET['student_id'];

$scl_name = '';
$scl_class = '';
$scl_roll_no = '';
$scl_reg_no = '';
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
| Marksheet
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
    echo "<script>
        alert('Please enter minimum 1 unit to print marksheet!');
        window.history.back();
    </script>";
    exit;
}


/*
|--------------------------------------------------------------------------
| Percentage
|--------------------------------------------------------------------------
*/
function cal_percentage($num_amount, $num_total)
{
    $num_amount = (float) $num_amount;
    $num_total  = (float) $num_total;

    if ($num_total <= 0) {
        return '0';
    }

    return number_format(($num_amount / $num_total) * 100, 0);
}


/*
|--------------------------------------------------------------------------
| Drawing
|--------------------------------------------------------------------------
*/
$drawing = [
    '1st_unit_marks' => 0,
    '1st_unit_hm'    => 0,
    '2nd_unit_marks' => 0,
    '2nd_unit_hm'    => 0,
    '3rd_unit_marks' => 0,
    '3rd_unit_hm'    => 0
];

$sql = "SELECT *
        FROM rrsv_marksheet
        WHERE student_id = '$id'
          AND session = '$scl_session'
          AND class = '$scl_class'
          AND LOWER(subject) = 'drawing'
        LIMIT 1";

$res = mysqli_query($con, $sql);

if ($res) {
    $drawing_row = mysqli_fetch_assoc($res);

    if ($drawing_row) {
        $drawing = array_merge($drawing, $drawing_row);
    }
}

$drawing_total_marks =
    (int)$drawing['1st_unit_marks'] +
    (int)$drawing['2nd_unit_marks'] +
    (int)$drawing['3rd_unit_marks'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Marksheet - <?= htmlspecialchars($scl_name); ?></title>

    <style>

        /* =========================================================
           SCREEN
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #e5e5e5;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

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

        /*
        A4 PAPER
        */
        .a4-page {

            width: 210mm;
            min-height: 297mm;

            margin: 0 auto;
            padding: 10mm;

            background: #fff;

            box-shadow: 0 0 8px rgba(0,0,0,.25);
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .school-title {

            text-align: center;

            font-size: 19px;

            font-weight: 700;

            text-decoration: underline;

            margin: 0 0 5px 0;

            line-height: 1.2;
        }

        .header-table {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 5px;
        }

        .header-table td {

            border: 0;

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

            margin-top: 3mm;

            margin-bottom: 5mm;
        }

        .student-info td {

            border: 1px solid #000;

            padding: 5px 7px;

            font-size: 11px;

            font-weight: 600;
        }

        .student-name {

            font-size: 12px !important;

            font-weight: 700 !important;
        }

        /* =========================================================
           MARKS TABLE
        ========================================================= */

        .marks-table {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

            margin-top: 3mm;
        }

        .marks-table th,
        .marks-table td {

            border: 1px solid #000;

            text-align: center;

            vertical-align: middle;

            padding: 4px 2px;

            font-size: 8.5px;

            line-height: 1.15;

            font-weight: 600;

        }

        .marks-table th {

            font-weight: 700;

            background: #f3f3f3;
        }

        .marks-table th:first-child,
        .marks-table td:first-child {

            width: 22%;

            text-align: left;

            padding-left: 5px;
        }

        .marks-table th:not(:first-child),
        .marks-table td:not(:first-child) {

            width: 8.666%;
        }

        .grand-total td {

            font-weight: 700;

            background: #f5f5f5;
        }

        .drawing-row td {

            font-weight: 600;
        }

        /* =========================================================
           SIGNATURE TABLE
        ========================================================= */

        .signature-table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 6mm;
        }

        .signature-table th,
        .signature-table td {

            border: 1px solid #000;

            text-align: center;

            font-size: 9px;

            padding: 5px;

            height: 10mm;

            vertical-align: middle;

        }

        .signature-table th {

            font-weight: 700;

            background: #f3f3f3;
        }

        .signature-table .signature-space {

            height: 18mm;
        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .report-note {

            text-align: center;

            font-size: 8px;

            font-style: italic;

            margin-top: 5mm;
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

                margin: 0;

                padding: 0;

                background: #fff;
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

                page-break-after: avoid;
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

            .marks-table th,
            .marks-table td {

                font-size: 8px;

                padding: 3px 2px;
            }

            .signature-table th,
            .signature-table td {

                font-size: 8px;
            }

            .signature-table .signature-space {

                height: 17mm;
            }

        }

    </style>

</head>

<body>

<!-- =========================================================
     SCREEN BUTTONS
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

            <td colspan="4" class="student-name">

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
         MARKS TABLE
    ====================================================== -->

    <table class="marks-table">

        <thead>

        <tr>

            <th>Subject</th>

            <th>
                1st Unit<br>
                Marks<br>
                (60)
            </th>

            <th>
                1st Unit<br>
                H.M<br>
                (60)
            </th>

            <th>
                2nd Unit<br>
                Marks<br>
                (80)
            </th>

            <th>
                2nd Unit<br>
                H.M<br>
                (80)
            </th>

            <th>
                3rd Unit<br>
                Marks<br>
                (60)
            </th>

            <th>
                3rd Unit<br>
                H.M<br>
                (60)
            </th>

            <th>
                Total<br>
                (200)
            </th>

            <th>
                %
            </th>

            <th>
                H.M<br>
                (200)
            </th>

        </tr>

        </thead>

        <tbody>

        <?php

        $frist_marks  = 0;
        $frist_hm     = 0;

        $second_marks = 0;
        $second_hm    = 0;

        $third_marks  = 0;
        $third_hm     = 0;

        $total_marks  = 0;

        $row_val = 0;

        while ($rows = mysqli_fetch_assoc($qryresult)):

            $frist_marks += (int)$rows['1st_unit_marks'];

            $frist_hm += (int)$rows['1st_unit_hm'];

            $second_marks += (int)$rows['2nd_unit_marks'];

            $second_hm += (int)$rows['2nd_unit_hm'];

            $third_marks += (int)$rows['3rd_unit_marks'];

            $third_hm += (int)$rows['3rd_unit_hm'];

            $total_marks += (int)$rows['total'];

            $row_val++;

        ?>

        <tr>

            <td>
                <?= htmlspecialchars($rows['subject']); ?>
            </td>

            <td>
                <?= (int)$rows['1st_unit_marks']; ?>
            </td>

            <td>
                <?= (int)$rows['1st_unit_hm']; ?>
            </td>

            <td>
                <?= (int)$rows['2nd_unit_marks']; ?>
            </td>

            <td>
                <?= (int)$rows['2nd_unit_hm']; ?>
            </td>

            <td>
                <?= (int)$rows['3rd_unit_marks']; ?>
            </td>

            <td>
                <?= (int)$rows['3rd_unit_hm']; ?>
            </td>

            <td>
                <?= (int)$rows['total']; ?>
            </td>

            <td>
                <?= htmlspecialchars($rows['percent']); ?>
            </td>

            <td>
                <?= isset($rows['total_hm'])
                    ? (int)$rows['total_hm']
                    : '00'; ?>
            </td>

        </tr>

        <?php endwhile; ?>


        <?php

        $total_exam_marks = $row_val * 200;

        ?>

        <!-- GRAND TOTAL -->

        <tr class="grand-total">

            <td>
                GRAND TOTAL
            </td>

            <td>
                <?= $frist_marks; ?>
            </td>

            <td>
                <?= $frist_hm; ?>
            </td>

            <td>
                <?= $second_marks; ?>
            </td>

            <td>
                <?= $second_hm; ?>
            </td>

            <td>
                <?= $third_marks; ?>
            </td>

            <td>
                <?= $third_hm; ?>
            </td>

            <td>
                <?= $total_marks; ?>
            </td>

            <td>

                <?= cal_percentage(
                    $total_marks,
                    $total_exam_marks
                ); ?>

            </td>

            <td>
                ---
            </td>

        </tr>


        <!-- DRAWING -->

        <tr class="drawing-row">

            <td>
                DRAWING
            </td>

            <td>
                <?= (int)$drawing['1st_unit_marks']; ?>
            </td>

            <td>
                <?= (int)$drawing['1st_unit_hm']; ?>
            </td>

            <td>
                <?= (int)$drawing['2nd_unit_marks']; ?>
            </td>

            <td>
                <?= (int)$drawing['2nd_unit_hm']; ?>
            </td>

            <td>
                <?= (int)$drawing['3rd_unit_marks']; ?>
            </td>

            <td>
                <?= (int)$drawing['3rd_unit_hm']; ?>
            </td>

            <td>
                <?= $drawing_total_marks; ?>
            </td>

            <td>
                <?= cal_percentage(
                    $drawing_total_marks,
                    200
                ); ?>
            </td>

            <td>
                --
            </td>

        </tr>

        </tbody>

    </table>


    <!-- =====================================================
         SIGNATURE TABLE
    ====================================================== -->

    <table class="signature-table">

        <thead>

        <tr>

            <th style="width:28%;">
                EXAMINATION
            </th>

            <th style="width:36%;">
                Guardian Signature with Date
            </th>

            <th style="width:36%;">
                Principal Signature with Date
            </th>

        </tr>

        </thead>

        <tbody>

        <tr>

            <td>
                1st Unit (Jan to April)
            </td>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

        </tr>

        <tr>

            <td>
                2nd Unit (May to August)
            </td>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

        </tr>

        <tr>

            <td>
                3rd Unit (September to December)
            </td>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

        </tr>

        <tr>

            <td>
                Teacher Signature with date and comment
            </td>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

        </tr>

        </tbody>

    </table>


    <div class="report-note">

        Please report any discrepancy in the marksheet to the school authority.

    </div>

</div>


</body>

</html>