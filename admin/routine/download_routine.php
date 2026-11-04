<?php
require_once('../../libray/tcpdf/tcpdf.php');
include('../../include/dbcon.php');

// ============ FILTERS ============
$where = [];
if (!empty($_POST['class_id']))
    $where[] = "r.class_id=" . (int) $_POST['class_id'];
if (!empty($_POST['section_id']))
    $where[] = "r.section_id=" . (int) $_POST['section_id'];
if (!empty($_POST['subject_id']))
    $where[] = "r.subject_id=" . (int) $_POST['subject_id'];
if (!empty($_POST['scl_session']))
    $where[] = "r.session='" . mysqli_real_escape_string($myDB, $_POST['scl_session']) . "'";

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// ============ QUERY ============
$sql = "
    SELECT r.*, 
           c.class_name, 
           s.sub_name, 
           sec.section_name
    FROM rrsv_routine r
    JOIN rrsv_class c ON r.class_id = c.id
    JOIN rrsv_subject s ON r.subject_id = s.id
    JOIN rrsv_section sec ON r.section_id = sec.id
    $whereSQL
    ORDER BY FIELD(r.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), r.start_time
";
$result = mysqli_query($myDB, $sql);

// ============ CHECK ============
if (!$result) {
    die("Query Error: " . mysqli_error($myDB));
}

// ============ BUILD TABLE ============
$timetable = [];
$timeSlots = [];
$days = [];

while ($row = mysqli_fetch_assoc($result)) {
    $day = $row['day_of_week'];
    $slot = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));

    $days[$day] = $day;
    $timeSlots[$slot] = $slot;
    $timetable[$day][$slot] = '<b>' . htmlspecialchars($row['sub_name']) . '</b><br><small>(' . htmlspecialchars($row['teacher_name']) . ')</small>';
}

$daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$days = array_intersect($daysOrder, $days);
sort($timeSlots);

// ============ TCPDF CONFIG ============
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("Class Routine");
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// ============ HEADER (Same as Holiday PDF) ============
$html = '
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <div style="text-align:left;">
        <b>RASULPUR RAMKRISHNA SARADA VIDYAPITH</b><br>
        Reg. No.: SO196094, U-DISE: 19251610302, ESTD: 2012<br>
        Baidyadanga, Rasulpur, Memari, Bardhaman,<br>
        Pin - 713151
    </div>
    <div>
        <img src="../../libray/images/logo.jpeg" width="80" height="80" />
    </div>
</div>

<h3 style="text-align:center; color:#0a4a97;">Class Routine</h3>
';

// ============ ROUTINE TABLE ============
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">
<thead style="background-color:#0a4a97; color:white; text-align:center;">
<tr>
    <th width="10%"><b>Day</b></th>';

$colWidth = 90 / max(count($timeSlots), 1); // auto adjust width

foreach ($timeSlots as $slot) {
    $html .= "<th width=\"$colWidth%\"><b>$slot</b></th>";
}
$html .= '</tr></thead><tbody>';

foreach ($days as $day) {
    $html .= "<tr><td><b>$day</b></td>";
    foreach ($timeSlots as $slot) {
        $cell = isset($timetable[$day][$slot]) ? $timetable[$day][$slot] : "—";
        $html .= "<td style='text-align:center;'>$cell</td>";
    }
    $html .= "</tr>";
}

$html .= '</tbody></table>';

// ============ OUTPUT ============
$pdf->SetFont('helvetica', '', 10);
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('class_routine.pdf', 'D');
?>