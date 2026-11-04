<?php
require_once('../../include/dbcon.php');
require_once('../../libray/tcpdf/tcpdf.php');

// Get selected session
$scl_session = isset($_REQUEST['scl_session']) ? mysqli_real_escape_string($myDB, $_REQUEST['scl_session']) : '';
$sessionTitle = !empty($scl_session) ? "Session: $scl_session" : "All Sessions";

// Fetch holidays
$where = "1";
if (!empty($scl_session)) {
    $where .= " AND YEAR(d_o_h) =" . intval($scl_session);
}
$sql = "SELECT * FROM rrsv_holiday WHERE $where ORDER BY d_o_h ASC";
$result = mysqli_query($myDB, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($myDB));
}

// Custom TCPDF Class to add border
class MYPDF extends TCPDF
{
    public function Header()
    {
        // Draw border for every page
        $this->Rect(5, 5, 200, 287); // X, Y, Width, Height
    }
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 6);
        $this->Cell(0, 10, 'Generated on ' . date('d-m-Y') . ' | RRSV School', 0, 0, 'C');
    }
}

// Create PDF
$pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator('RRSV School');
$pdf->SetAuthor('RRSV School');
$pdf->SetTitle('Academic Holiday Calendar');
$pdf->SetMargins(15, 5, 15);
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
    <td width="20%" align="left"><img src="' . $logoPath . '" width="60" /></td>
    <td width="80%" align="center">
        <strong style="font-size:16px;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</strong><br>
        <span style="font-size:11px;">
            Reg. No.: SO196094, U-DISE Code: 19251610302, ESTD: 2012<br>
            Baidyadanga, Rasulpur, Memari, Bardhaman, Pin - 713151
        </span>
        <h3 style="margin:5px 0 0 0; font-size:12px; font-weight:bold;">Holiday List ' . $sessionTitle . '</h3>
    </td>
</tr>
</table><br/><br/>
';

// ---- TABLE HEADER ----
$html .= '
<table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size: 10px;">
<thead>
<tr style="background-color:#0a4a97; color:white; text-align:center; font-weight:bold;">
    <th width="20%">Date</th>
    <th width="20%">Day</th>
    <th width="15%">Total Days</th>
    <th width="45%">Occasion</th>
</tr>
</thead>
<tbody>
';

// ---- TABLE BODY ----
if (mysqli_num_rows($result) > 0) {
    $rowCount = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $bgcolor = ($rowCount % 2 == 0) ? '#ffffff' : '#f2f2f2'; // zebra rows
        // Conditional date range
        $dateRange = date('d M Y', strtotime($row['d_o_h'])) .
                     ($row['count_of_day'] > 1 ? ' To ' . date('d M Y', strtotime($row['end_date'])) : '');

        // Conditional day range
        $dayRange = date('l', strtotime($row['d_o_h'])) .
                    ($row['count_of_day'] > 1 ? ' To ' . date('l', strtotime($row['end_date'])) : '');

        $html .= '
        <tr bgcolor="' . $bgcolor . '">
            <td width="20%">' . htmlspecialchars($dateRange) . '</td>
            <td width="20%">' . htmlspecialchars($dayRange) . '</td>
            <td width="15%" style="text-align:center;">' . htmlspecialchars($row['count_of_day']) . '</td>
            <td width="45%">' . htmlspecialchars($row['name']) . '</td>
        </tr>';
        $rowCount++;
    }
} else {
    $html .= '
    <tr>
        <td colspan="3" align="center">No holidays found for this session.</td>
    </tr>';
}

$html .= '
</tbody>
</table>
';

// ---- Render Table ----
$pdf->setCellMargins(0, 0, 0, 0);
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->writeHTML($html, true, false, true, false, '');

// ---- Output PDF ----
$pdf->Output("Holiday_Calendar_" . $scl_session . ".pdf", 'D');
?>