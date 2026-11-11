<?php
include_once __DIR__ . '/../../include/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../include/dbcon.php';

// Auth check
$mid = isset($_SESSION['mid']) ? $_SESSION['mid'] : '';
$token = isset($_SESSION['_token']) ? $_SESSION['_token'] : '';
if ((empty($token) || empty($mid)) && !isset($_SESSION['user_data'])) {
    die("Unauthorized access. Please login first.");
}

// ---------------------- 1. GET FILTER VALUES ---------------------- //
$currentYear = date('Y');
$currentMonthNum = intval(date('n'));

$scl_session  = trim($_REQUEST['scl_session'] ?? $currentYear);
$scl_class    = trim($_REQUEST['scl_class'] ?? 'All');
$add_status   = trim($_REQUEST['add_status'] ?? 'All');
$upToMonth    = isset($_REQUEST['upToMonth']) ? intval($_REQUEST['upToMonth']) : $currentMonthNum;
if ($upToMonth < 1) $upToMonth = 1;
if ($upToMonth > 12) $upToMonth = 12;

$fromDate = trim($_REQUEST['fromDate'] ?? ($scl_session !== 'All' ? $scl_session . '-01-01' : date('Y-01-01')));
$endDate  = trim($_REQUEST['endDate'] ?? ($scl_session !== 'All' ? $scl_session . '-12-31' : date('Y-12-31')));
$search_text = trim($_REQUEST['search_text'] ?? '');

$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// ---------------------- 2. BUILD STUDENT QUERY ---------------------- //
$whereConditions = ["1=1"];

if ($scl_session !== 'All' && !empty($scl_session)) {
    $safe_sess = mysqli_real_escape_string($con, $scl_session);
    $whereConditions[] = "s.scl_session = '$safe_sess'";
}

if ($scl_class !== 'All' && !empty($scl_class)) {
    $safe_class = mysqli_real_escape_string($con, $scl_class);
    $whereConditions[] = "s.scl_class = '$safe_class'";
}

if ($add_status !== 'All' && !empty($add_status)) {
    if ($add_status === 'New Admission') {
        $whereConditions[] = "(s.add_status = 'New Admission' OR s.admission_fee_val > 0)";
    } elseif ($add_status === 'Re-Admission') {
        $whereConditions[] = "(s.add_status != 'New Admission' AND (s.admission_fee_val = 0 OR s.admission_fee_val IS NULL))";
    }
}

if (!empty($search_text)) {
    $safe_search = mysqli_real_escape_string($con, $search_text);
    $whereConditions[] = "(s.scl_name LIKE '%$safe_search%' OR s.scl_reg_no LIKE '%$safe_search%' OR s.scl_phone_no LIKE '%$safe_search%' OR s.father_phone_no LIKE '%$safe_search%')";
}

$whereSql = implode(' AND ', $whereConditions);

$students_sql = "
    SELECT 
        s.*,
        c.cost AS car_cost
    FROM rrsv_student_registration s
    LEFT JOIN rrsv_scl_car c ON s.scl_car_place = c.id
    WHERE $whereSql
    ORDER BY s.scl_class ASC, s.scl_roll_no ASC, s.id ASC
";
$students_result = mysqli_query($con, $students_sql);

// ---------------------- 3. PRE-FETCH PAYMENTS ---------------------- //
$sess_filter = ($scl_session !== 'All' && !empty($scl_session)) ? "AND scl_session = '" . mysqli_real_escape_string($con, $scl_session) . "'" : "";

$payments_total_q = mysqli_query($con, "
    SELECT pay_id, SUM(payment_recive) AS total_paid
    FROM rrsv_scl_studen_fee
    WHERE 1=1 $sess_filter
    GROUP BY pay_id
");
$payments_total = [];
if ($payments_total_q) {
    while ($pr = mysqli_fetch_assoc($payments_total_q)) {
        $payments_total[$pr['pay_id']]['total_paid'] = floatval($pr['total_paid']);
    }
}

// ---------------------- 4. PROCESS ROWS & TOTALS ---------------------- //
$reportRows = [];
$col_totals = [
    'count'               => 0,
    'admission_fee'       => 0,
    'readmission_fee'     => 0,
    'monthly_fee'         => 0,
    'yearly_tuition'      => 0,
    'car_count'           => 0,
    'yearly_car'          => 0,
    'total_items'         => 0,
    'total_package_gross' => 0,
    'total_paid'          => 0,
    'advance'             => 0,
    'current_due'         => 0,
    'year_end_due'        => 0
];

if ($students_result) {
    while ($row = mysqli_fetch_assoc($students_result)) {
        $sid = $row['id'];
        $isNew = ($row['add_status'] === 'New Admission' || floatval($row['admission_fee_val']) > 0);

        $admFee = $isNew ? floatval($row['admission_fee_val']) : 0;
        $readmFee = !$isNew ? floatval($row['re_admission_fee_val']) : 0;
        $monthlyFee = floatval($row['monthly_fee']);
        $fullYearTuition = $monthlyFee * 12;

        $hasCar = ($row['scl_car'] === 'Yes' || floatval($row['car_cost']) > 0);
        $carMonthly = floatval($row['car_cost']);
        $fullYearCar = $hasCar ? ($carMonthly * 12) : 0;

        $uniform   = floatval($row['uniformval']);
        $shoes     = floatval($row['shoessockesval']);
        $winter    = floatval($row['sweaterslaxcap_val']);
        $admForm   = floatval($row['admissionval']);
        $bag       = floatval($row['bagval']);
        $icard     = floatval($row['icardval']);
        $diary     = floatval($row['diaryval']);
        $books     = floatval($row['bookcost']);
        $copy      = floatval($row['copycost']);
        $totalItems = $uniform + $shoes + $winter + $admForm + $bag + $icard + $diary + $books + $copy;

        $grossWithoutCar = ($isNew ? $admFee : $readmFee) + $fullYearTuition + $totalItems;
        $totalGross      = $hasCar ? ($grossWithoutCar + $fullYearCar) : $grossWithoutCar;

        $feePaid = $payments_total[$sid]['total_paid'] ?? 0;
        $totalPaid = $feePaid + $admForm;

        $expectedUpToMonth = ($isNew ? $admFee : $readmFee) + $totalItems + ($upToMonth * $monthlyFee) + ($upToMonth * $carMonthly);
        if ($totalPaid >= $expectedUpToMonth) {
            $advance = $totalPaid - $expectedUpToMonth;
            $currentDue = 0;
        } else {
            $advance = 0;
            $currentDue = $expectedUpToMonth - $totalPaid;
        }
        $yearEndDue = max(0, $totalGross - $totalPaid);

        // Accumulate totals
        $col_totals['count']++;
        $col_totals['admission_fee']       += $admFee;
        $col_totals['readmission_fee']     += $readmFee;
        $col_totals['monthly_fee']         += $monthlyFee;
        $col_totals['yearly_tuition']      += $fullYearTuition;
        if ($hasCar) $col_totals['car_count']++;
        $col_totals['yearly_car']          += $fullYearCar;
        $col_totals['total_items']         += $totalItems;
        $col_totals['total_package_gross'] += $totalGross;
        $col_totals['total_paid']          += $totalPaid;
        $col_totals['advance']             += $advance;
        $col_totals['current_due']         += $currentDue;
        $col_totals['year_end_due']        += $yearEndDue;

        $reportRows[] = [
            'reg_no'        => $row['scl_reg_no'],
            'name'          => $row['scl_name'],
            'class'         => $row['scl_class'],
            'status'        => $isNew ? 'New' : 'Re-Adm',
            'adm_fee'       => ($isNew ? $admFee : $readmFee),
            'yearly_tuition'=> $fullYearTuition,
            'yearly_car'    => $fullYearCar,
            'total_items'   => $totalItems,
            'total_gross'   => $totalGross,
            'total_paid'    => $totalPaid,
            'advance'       => $advance,
            'current_due'   => $currentDue,
            'year_end_due'  => $yearEndDue
        ];
    }
}

// ---------------------- 5. GENERATE PDF WITH TCPDF ---------------------- //
require_once __DIR__ . '/../../libray/tcpdf/tcpdf.php';

$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('RRSV System');
$pdf->SetAuthor('Rasulpur Ramkrishna Sarada Vidyapith');
$pdf->SetTitle('Student Fee Master Money Report');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 7);

$filterText = "Session: $scl_session | Class: $scl_class | Status: $add_status | Due Month: " . $months[$upToMonth] . " | Generated: " . date('d-m-Y H:i');

$html = '
<div style="text-align: center; border-bottom: 1px solid #1e3a8a; padding-bottom: 4px; margin-bottom: 6px;">
    <h2 style="margin: 0; font-size: 13px; font-weight: bold; color: #1e3c72; text-transform: uppercase;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2>
    <p style="margin: 1px 0; font-size: 7.5px; color: #333;">Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012 | Baidyadanga, Rasulpur, Memari, Purba Bardhaman</p>
    <h3 style="margin: 3px 0 0 0; font-size: 10px; color: #0284c7; font-weight: bold;">STUDENT FEE MASTER LEDGER &amp; MONEY REPORT</h3>
    <p style="margin: 1px 0; font-size: 7px; color: #555;">' . htmlspecialchars($filterText) . '</p>
</div>

<!-- Summary Cards Bar -->
<table cellpadding="3" cellspacing="2" style="width: 100%; margin-bottom: 5px; font-size: 7.5px; text-align: center;">
    <tr>
        <td style="background-color: #dcfce7; border: 1px solid #86efac; font-weight: bold;">
            Total Collection: <span style="color:#15803d; font-size:8.5px;">Rs. ' . number_format($col_totals['total_paid']) . '</span>
        </td>
        <td style="background-color: #e0f2fe; border: 1px solid #7dd3fc; font-weight: bold;">
            Total Gross Package: <span style="color:#0369a1; font-size:8.5px;">Rs. ' . number_format($col_totals['total_package_gross']) . '</span>
        </td>
        <td style="background-color: #fee2e2; border: 1px solid #fca5a5; font-weight: bold;">
            Current Month Due: <span style="color:#b91c1c; font-size:8.5px;">Rs. ' . number_format($col_totals['current_due']) . '</span>
        </td>
        <td style="background-color: #f3e8ff; border: 1px solid #d8b4fe; font-weight: bold;">
            Year-End Due: <span style="color:#7e22ce; font-size:8.5px;">Rs. ' . number_format($col_totals['year_end_due']) . '</span>
        </td>
        <td style="background-color: #fef3c7; border: 1px solid #fde047; font-weight: bold;">
            Total Students: <span style="color:#b45309; font-size:8.5px;">' . count($reportRows) . '</span>
        </td>
    </tr>
</table>

<!-- Ledger Table -->
<table border="1" cellpadding="2.5" cellspacing="0" style="width: 100%; font-size: 6.5px; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #1e3c72; color: #ffffff; font-weight: bold; text-align: center;">
            <th width="3%">#</th>
            <th width="8%">Reg No</th>
            <th width="16%" align="left"> Student Name</th>
            <th width="8%">Class</th>
            <th width="6%">Type</th>
            <th width="7%">Adm/Re Fee</th>
            <th width="7%">Tuition/Yr</th>
            <th width="6%">Car/Yr</th>
            <th width="7%">Items</th>
            <th width="8%">Gross Fee</th>
            <th width="8%">Total Paid</th>
            <th width="7%">Advance</th>
            <th width="7%">Current Due</th>
            <th width="8%">Year-End Due</th>
        </tr>
    </thead>
    <tbody>';

$sl = 1;
foreach ($reportRows as $r) {
    $bg = ($sl % 2 == 0) ? '#f8fafc' : '#ffffff';
    $html .= '
        <tr style="background-color: ' . $bg . ';">
            <td align="center" width="3%">' . $sl++ . '</td>
            <td align="center" width="8%">' . htmlspecialchars($r['reg_no']) . '</td>
            <td width="16%"> ' . htmlspecialchars($r['name']) . '</td>
            <td align="center" width="8%">' . htmlspecialchars($r['class']) . '</td>
            <td align="center" width="6%">' . $r['status'] . '</td>
            <td align="right" width="7%">' . ($r['adm_fee'] > 0 ? number_format($r['adm_fee']) : '-') . '</td>
            <td align="right" width="7%">' . number_format($r['yearly_tuition']) . '</td>
            <td align="right" width="6%">' . ($r['yearly_car'] > 0 ? number_format($r['yearly_car']) : '-') . '</td>
            <td align="right" width="7%">' . number_format($r['total_items']) . '</td>
            <td align="right" width="8%" style="font-weight:bold;">' . number_format($r['total_gross']) . '</td>
            <td align="right" width="8%" style="font-weight:bold; color:#15803d;">' . number_format($r['total_paid']) . '</td>
            <td align="right" width="7%">' . ($r['advance'] > 0 ? '+' . number_format($r['advance']) : '-') . '</td>
            <td align="right" width="7%" style="font-weight:bold; color:#b91c1c;">' . ($r['current_due'] > 0 ? number_format($r['current_due']) : 'Cleared') . '</td>
            <td align="right" width="8%" style="font-weight:bold; color:#7e22ce;">' . ($r['year_end_due'] > 0 ? number_format($r['year_end_due']) : 'Paid') . '</td>
        </tr>';
}

// LAST ROW: COLUMN TOTALS (POINT REQUESTED BY USER)
$html .= '
        <tr style="background-color: #e2e8f0; font-weight: bold; border-top: 2px solid #1e3c72;">
            <td align="center" width="3%"><b>TOT</b></td>
            <td align="center" width="8%"><b>-</b></td>
            <td width="16%"><b> ' . count($reportRows) . ' Students</b></td>
            <td align="center" width="8%"><b>-</b></td>
            <td align="center" width="6%"><b>-</b></td>
            <td align="right" width="7%"><b>' . number_format($col_totals['admission_fee'] + $col_totals['readmission_fee']) . '</b></td>
            <td align="right" width="7%"><b>' . number_format($col_totals['yearly_tuition']) . '</b></td>
            <td align="right" width="6%"><b>' . number_format($col_totals['yearly_car']) . '</b></td>
            <td align="right" width="7%"><b>' . number_format($col_totals['total_items']) . '</b></td>
            <td align="right" width="8%"><b>' . number_format($col_totals['total_package_gross']) . '</b></td>
            <td align="right" width="8%" style="color:#15803d;"><b>' . number_format($col_totals['total_paid']) . '</b></td>
            <td align="right" width="7%"><b>' . number_format($col_totals['advance']) . '</b></td>
            <td align="right" width="7%" style="color:#b91c1c;"><b>' . number_format($col_totals['current_due']) . '</b></td>
            <td align="right" width="8%" style="color:#7e22ce;"><b>' . number_format($col_totals['year_end_due']) . '</b></td>
        </tr>
    </tbody>
</table>

<!-- Signatures Section -->
<br><br>
<table style="width: 100%; font-size: 7.5px; margin-top: 15px;">
    <tr>
        <td width="33%" align="center">
            ___________________________<br>
            <b>Prepared By</b>
        </td>
        <td width="34%" align="center">
            ___________________________<br>
            <b>Accountant / Cashier</b>
        </td>
        <td width="33%" align="center">
            ___________________________<br>
            <b>Principal / Headmaster</b>
        </td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$fileName = 'Money_Report_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $scl_session) . '_' . date('d-m-Y') . '.pdf';
$pdf->Output($fileName, 'D');
