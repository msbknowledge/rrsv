<?php
require_once('../../libray/tcpdf/tcpdf.php');
include('../../include/dbcon.php');

// Filters
$where = [];
$scl_session = !empty($_GET['scl_session']) ? $_GET['scl_session'] : date('Y');
$sessionTitle = !empty($scl_session) ? "Session: $scl_session" : "All Sessions";
$where[] = "r.session='" . mysqli_real_escape_string($myDB, $scl_session) . "'";

if (!empty($_GET['class_id'])) {
    $where[] = "r.class_id=" . (int) $_GET['class_id'];
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Fetch routine data
$sql = "
SELECT r.*, c.class_name, s.sub_name, sec.section_name,t.full_name
FROM rrsv_routine r
JOIN rrsv_class c ON r.class_id = c.id
JOIN rrsv_subject s ON r.subject_id = s.id
JOIN rrsv_section sec ON r.section_id = sec.id
JOIN rrsv_teacher t ON r.teacher_id = t.id
$whereSQL
ORDER BY FIELD(r.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), r.start_time
";

$res = mysqli_query($myDB, $sql);
if (!$res) {
    die("Query Error: " . mysqli_error($myDB));
}

// Build timetable structure
$timetable = [];
$timeSlots = [];
$days = [];
$class_name='';
while ($row = mysqli_fetch_assoc($res)) {
    $class_name = $row['class_name'];
    $day = $row['day_of_week'];
    $slot = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));
    $days[$day] = $day;
    $timeSlots[$slot] = $slot;
    $timetable[$day][$slot] = '<b>' . htmlspecialchars($row['sub_name']) . '</b><br><small>(' . htmlspecialchars($row['full_name']) . ')</small>';
}

$daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$days = array_intersect($daysOrder, $days);
sort($timeSlots);

// ---- TCPDF Customization ----
class MYPDF extends TCPDF
{
    public function Header()
    {
        // Border around every page
        $this->Rect(5, 5, 287, 200);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Generated on ' . date('d-m-Y') . ' | RR School', 0, 0, 'C');
    }
}

// Create PDF
$pdf = new MYPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false); // Landscape for wide table
$pdf->SetCreator('RR School');
$pdf->SetAuthor('RR School');
$pdf->SetTitle('Class Routine');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// ---- HEADER CONTENT ----
$logoPath = realpath(__DIR__ . '/../../libray/images/logo.jpeg');
if (!file_exists($logoPath)) {
    die("Logo not found: " . $logoPath);
}

$html = '
<table width="100%" cellpadding="5" cellspacing="0" border="0" style="border-bottom:2px solid #000;">
<tr>
    <td width="15%" align="left"><img src="' . $logoPath . '" width="60" /></td>
    <td width="85%" align="center">
        <strong style="font-size:16px;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</strong><br>
        <span style="font-size:11px;">
            Reg. No.: SO196094, U-DISE Code: 19251610302, ESTD: 2012<br>
            Baidyadanga, Rasulpur, Memari, Bardhaman, Pin - 713151
        </span>
        <h3 style="margin:5px 0 0 0; font-size:12px; font-weight:bold;">'.$class_name.' Class Routine ' . $sessionTitle . '</h3>
    </td>
</tr>
</table><br/><br/>
';

// ---- TABLE HEADER ----
$html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="font-size: 10px;">
<thead>
<tr style="background-color:#0a4a97; color:white; text-align:center; font-weight:bold;">
    <th >Day</th>';
foreach ($timeSlots as $slot) {
    $html .= '<th>' . $slot . '</th>';
}
$html .= '</tr>
</thead>
<tbody>';

// ---- TABLE BODY ----
$rowIndex = 0;
foreach ($days as $day) {
    $bgcolor = ($rowIndex % 2 == 0) ? '#ffffff' : '#f2f2f2'; // zebra
    $html .= '<tr bgcolor="' . $bgcolor . '">';
    $html .= '<td align="center"><b>' . $day . '</b></td>';

    foreach ($timeSlots as $slot) {
        $html .= '<td align="center">' . ($timetable[$day][$slot] ?? '—') . '</td>';
    }

    $html .= '</tr>';
    $rowIndex++;
}

$html .= '</tbody></table>';

// ---- Render ----
$pdf->setCellMargins(0, 0, 0, 0);
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->writeHTML($html, true, false, true, false, '');

// ---- Output ----
$pdf->Output($class_name." Class_Routine_" . $scl_session . ".pdf", 'D');
?>