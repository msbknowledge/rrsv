<?php
include('../../include/dbcon.php');
require_once('../../libray/tcpdf/tcpdf.php');

// Get filters
$filterSession = $_GET['scl_session'] ?? '';
$filterClass = $_GET['scl_class'] ?? '';
$feeType = $_GET['fee_type'] ?? 'admission';
$sessionTitle = !empty($filterSession) ? "Session: $filterSession" : "All Sessions";
// Build WHERE clause
$where = [];
if ($filterSession != '')
    $where[] = "scl_session='" . mysqli_real_escape_string($myDB, $filterSession) . "'";
if ($filterClass != '')
    $where[] = "scl_class='" . mysqli_real_escape_string($myDB, $filterClass) . "'";
$whereSQL = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";
// ==============================
// 📘 Fetch total book rate
// ==============================

// ==============================
// 📘 Fetch total book rate
// ==============================
$query_book = "
SELECT 
    UPPER(scl_session) AS scl_session,
    UPPER(scl_class) AS scl_class,
    SUM(rate) AS total_book_rate
FROM rrsv_book
$whereSQL
GROUP BY scl_session, scl_class
";

$bookRates = [];
$result_book = mysqli_query($myDB, $query_book);
if ($result_book) {
  while ($row = mysqli_fetch_assoc($result_book)) {
    $key = $row['scl_session'] . '_' . $row['scl_class'];
    $bookRates[$key] = $row['total_book_rate'];
  }
}

// ==============================
// 📘 FETCH COPY COST
// ==============================
$query_copy = "
SELECT 
    UPPER(scl_session) AS scl_session,
    UPPER(scl_class) AS scl_class,
    SUM(rate) AS total_copy_cost
FROM rrsv_copy 
$whereSQL
GROUP BY scl_session, scl_class
";

$copyRates = [];
$result_copy = mysqli_query($myDB, $query_copy);
if ($result_copy) {
  while ($row = mysqli_fetch_assoc($result_copy)) {
    $key = $row['scl_session'] . '_' . $row['scl_class'];
    $copyRates[$key] = $row['total_copy_cost'];
  }
}
// Query data
$sql = "SELECT * FROM rrsv_fee $whereSQL ORDER BY id DESC";
$res = mysqli_query($myDB, $sql);


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
        $this->Cell(0, 10, 'Generated on ' . date('d-m-Y') . ' | RRSV School', 0, 0, 'C');
    }
}

// Create PDF
$pdf = new MYPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false); // Landscape for wide table
$pdf->SetCreator('RRSV School');
$pdf->SetAuthor('RRSV School');
$pdf->SetTitle('Admission Fees');
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
        <h3 style="margin:5px 0 0 0; font-size:12px; font-weight:bold;">Admission Fees ' . $sessionTitle . '</h3>
    </td>
</tr>
</table><br/><br/>
';

// Table header
$html .= '<table border="1" cellpadding="4">
<tr style="background-color:#f2f2f2;">
<th>SL.No</th>
<th>Class</th>
<th>Session</th>
<th>Monthly Fee</th>
<th>' . ucfirst($feeType) . ' Fee</th>
<th>Other Charges</th>
<th>Total Payable (Rs)</th>
</tr>';

// Table body
$sl = 1;
while ($fee = mysqli_fetch_assoc($res)) {
    $key = strtoupper($fee['scl_session']) . '_' . strtoupper($fee['scl_class']);
    $bookCharge = isset($bookRates[$key]) ? $bookRates[$key] : 0;
    $copyCharge = isset($copyRates[$key]) ? $copyRates[$key] : 0;

    // Collect all charge components
    $adForm = (float) $fee['ad_form_charge'];
    $uniform = (float) $fee['uniform'];
    $diary = (float) $fee['diary'];
    $icard = (float) $fee['icard'];
    $bag = (float) $fee['bag'];
    $sweater = (float) $fee['sweater'];
    $shoes = (float) $fee['shoes'];

    // Admission/Readmission fee logic
    $admissionFee = ($feeType === 'admission') ? (float) $fee['monthly_admission_fee'] : (float) $fee['readmission_fee'];
    $monthlyFee = (float) $fee['monthly_fee'];

    // 🧮 Calculate total payable
    $totalPayable = $monthlyFee + $admissionFee + $adForm + $bookCharge +$copyCharge+ $uniform + $diary + $icard + $bag + $sweater + $shoes;


    $other = "Admission Form: {$fee['ad_form_charge']}<br>";
    $other .= "Book: {$bookCharge}<br>";
    $other .= "Copies: {$copyCharge}<br>";
    $other .= "Uniform: {$fee['uniform']}<br>";
    $other .= "Diary: {$fee['diary']}<br>";
    $other .= "I-Card: {$fee['icard']}<br>";
    $other .= "Bag: {$fee['bag']}<br>";
    $other .= "Sweater: {$fee['sweater']}<br>";
    $other .= "Shoes & Socks: {$fee['shoes']}";

    $html .= '<tr>
        <td>' . $sl++ . '</td>
        <td>' . htmlspecialchars($fee['scl_class']) . '</td>
        <td>' . htmlspecialchars($fee['scl_session']) . '</td>
        <td>' . $fee['monthly_fee'] . '</td>
        <td>' . ($feeType === 'admission' ? $fee['monthly_admission_fee'] : $fee['readmission_fee']) . '</td>
        <td>' . $other . '</td>
         <td> ' . number_format($totalPayable, 2) . '</td>
    </tr>';
}

$html .= '</table>';

// ---- Render Table ----
$pdf->setCellMargins(0, 0, 0, 0);
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->writeHTML($html, true, false, true, false, '');

// ---- Output PDF ----
$pdf->Output("admission_fees_" . $filterSession . ".pdf", 'D');

exit;
