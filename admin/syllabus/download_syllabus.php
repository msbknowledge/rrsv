<?php
include('../../include/dbcon.php');
require_once('../../libray/tcpdf/tcpdf.php');

// --- Filters from GET ---
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$session = isset($_GET['scl_session']) ? mysqli_real_escape_string($myDB, $_GET['scl_session']) : date("Y");
$unit = isset($_GET['unit']) ? mysqli_real_escape_string($myDB, $_GET['unit']) : '';

// --- Fetch syllabus ---
$sql = "SELECT s.*, sub.sub_name, c.class_name 
        FROM rrsv_syllabus s
        LEFT JOIN rrsv_class c ON s.class_id = c.id
        LEFT JOIN rrsv_subject sub ON s.subject_id = sub.id
        WHERE 1";
if ($class_id > 0) {
    $sql .= " AND s.class_id = $class_id";
}
if ($unit > 0){
  $sql .= " AND s.unit=$unit";
}
if (!empty($session)) {
    $sql .= " AND s.session = '$session'";
}
$sql .= " ORDER BY sub.sub_name ASC";

$result = mysqli_query($myDB, $sql);
if (!$result) {
    die("Query Error: " . mysqli_error($myDB));
}

// --- Custom TCPDF class ---
class MYPDF extends TCPDF
{
    public function Header()
    {
        // Border for landscape A4 page
        $this->Rect(5, 5, 287, 200); // X, Y, Width, Height
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Generated on ' . date('d-m-Y') . ' | RRSV School', 0, 0, 'C');
    }
}

// --- Create PDF ---
$pdf = new MYPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator('RRSV School');
$pdf->SetAuthor('RRSV School');
$pdf->SetTitle('Syllabus');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();
$fontPath = realpath('../../libray/tcpdf/fonts/Noto_Sans_Bengali/static/NotoSansBengali-Regular.ttf');
if (!file_exists($fontPath)) {
    die("Font not found: " . $fontPath);
}

$bengaliFont = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
$pdf->SetFont($bengaliFont ?: 'helvetica', '', 12, '', true);

// --- HEADER SECTION ---
$logoPath = realpath(__DIR__ . '/../../libray/images/logo.jpeg');
if (!file_exists($logoPath)) {
    die("Logo not found: " . $logoPath);
}
$firstRow = mysqli_fetch_assoc($result);
mysqli_data_seek($result, 0); // reset pointer
// Convert unit to text
$unitNumber1 = (int)$firstRow['unit'];
if ($unitNumber1 == 1) $unitText1 = '1st Unit';
elseif ($unitNumber1 == 2) $unitText1 = '2nd Unit';
elseif ($unitNumber1 == 3) $unitText1 = '3rd Unit';
else $unitText1 = $unitNumber1 . 'th Unit';
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
        <h3 style="margin:5px 0 0 0; font-size:12px; font-weight:bold;">Class - ' . htmlspecialchars($firstRow['class_name']) . '</h3>
        <h3 style="margin:5px 0 0 0; font-size:12px; font-weight:bold;">Unit - ' . htmlspecialchars($unitText1) . '</h3>
       
        <h3 style="margin:5px 0 0 0; font-size:12px; font-weight:bold;">Syllabus - ' . htmlspecialchars($session) . '</h3>
    </td>
</tr>
</table><br/><br/>
';

// --- TABLE SECTION ---
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size: 10px;">
<thead>
<tr style="background-color:#0a4a97; color:white; text-align:center; font-weight:bold;">
    <th style="width:15%;">Subject</th>
    <th style="width:15%;">Unit</th>
    <th style="width:55%;">Details</th>
    <th style="width:15%;">Page No</th>
</tr>
</thead>
<tbody>
';
$rowCount = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $syllabus_id = $row['id'];
    $detailsRes = mysqli_query($myDB, "SELECT * FROM rrsv_syllabus_details WHERE syllabus_id=$syllabus_id ORDER BY id ASC");

    $subjectName = htmlspecialchars($row['sub_name']);
    $unitNumber = (int)$row['unit'];

    // Convert unit number to text
    if ($unitNumber == 1) {
        $unitText = '1st Unit';
    } elseif ($unitNumber == 2) {
        $unitText = '2nd Unit';
    } elseif ($unitNumber == 3) {
        $unitText = '3rd Unit';
    } else {
        $unitText = $unitNumber . 'th Unit';
    }

    $detailCount = mysqli_num_rows($detailsRes);
    $rowspanPrinted = false;
    $bgcolor = ($rowCount % 2 == 0) ? '#ffffff' : '#f2f2f2';

    while ($d = mysqli_fetch_assoc($detailsRes)) {
        $chapter = htmlspecialchars($d['chapter']);
        $description = htmlspecialchars($d['description']);
        $page_no = htmlspecialchars($d['page_no']);

        $html .= '<tr bgcolor="' . $bgcolor . '">';

        // Print subject & unit once per subject block (with rowspan)
        if (!$rowspanPrinted) {
            $html .= '<td width="15%" rowspan="' . $detailCount . '" valign="top"><b>' . $subjectName . '</b></td>';
            $html .= '<td width="15%" rowspan="' . $detailCount . '" valign="top">' . $unitText . '</td>';
            $rowspanPrinted = true;
        }

        // Details and page numbers per chapter
        $html .= '<td width="55%" valign="top"><b>' . $chapter . ':</b> ' . $description . '</td>';
        $html .= '<td width="15%" valign="top">' . $page_no . '</td>';
        $html .= '</tr>';
    }

    $rowCount++;
}


$html .= '</tbody></table>';

// --- Render HTML ---
$pdf->setCellMargins(0, 0, 0, 0);
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->writeHTML($html, true, false, true, false, '');

// --- Output PDF ---
$pdf->Output("syllabus_" . $session . ".pdf", 'D');
exit;
?>
