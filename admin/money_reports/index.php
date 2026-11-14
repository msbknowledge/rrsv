<?php
include_once __DIR__ . '/../../include/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../include/dbcon.php';

$mid = isset($_SESSION['mid']) ? $_SESSION['mid'] : '';
$token = isset($_SESSION['_token']) ? $_SESSION['_token'] : '';
// Auth check
if (empty($token) || empty($mid)) {
    $loginUrl = defined('BASE_URL') ? BASE_URL . 'login.php' : '/rrsv/login.php';
    header("Location: " . $loginUrl . "?error=" . urlencode("Please login to access Money Reports"));
    exit;
}

// ---------------------- 1. GET FILTER VALUES ---------------------- //
$currentYear = date('Y');
$currentMonthNum = intval(date('n'));

$scl_session  = trim($_REQUEST['scl_session'] ?? $currentYear);
$scl_class    = trim($_REQUEST['scl_class'] ?? 'All');
$add_status   = trim($_REQUEST['add_status'] ?? 'All');
$search_text  = trim($_REQUEST['search_text'] ?? '');
$upToMonth    = intval($_REQUEST['upToMonth'] ?? $currentMonthNum);
if ($upToMonth < 1 || $upToMonth > 12) $upToMonth = $currentMonthNum;

$fromDate = trim($_REQUEST['fromDate'] ?? '');
$endDate  = trim($_REQUEST['endDate'] ?? '');

// If user clicked quick presets or first visit
if (empty($fromDate) && empty($endDate)) {
    // Default to the full year of the selected session so backdated reports never show empty
    $fromDate = $scl_session . '-01-01';
    $endDate  = $scl_session . '-12-31';
} elseif (!empty($fromDate) && empty($endDate)) {
    $endDate = $fromDate;
}

// Store in session for export / persistence
$_SESSION['m_fromDate']    = $fromDate;
$_SESSION['m_endDate']     = $endDate;
$_SESSION['m_scl_session'] = $scl_session;
$_SESSION['m_scl_class']   = $scl_class;

// Available sessions from DB
$sessionsList = [];
$sess_q = mysqli_query($con, "SELECT DISTINCT scl_session FROM rrsv_student_registration WHERE scl_session != '' ORDER BY scl_session DESC");
if ($sess_q) {
    while ($sr = mysqli_fetch_row($sess_q)) {
        if (!empty($sr[0])) $sessionsList[] = $sr[0];
    }
}
if (empty($sessionsList)) $sessionsList = ['2026', '2025', '2024', '2023', '2022'];
if (!in_array($scl_session, $sessionsList)) array_unshift($sessionsList, $scl_session);

// Available classes
$classesList = [];
$cls_q = mysqli_query($con, "SELECT DISTINCT scl_class FROM rrsv_student_registration WHERE scl_class != '' ORDER BY scl_class ASC");
if ($cls_q) {
    while ($cr = mysqli_fetch_row($cls_q)) {
        if (!empty($cr[0])) $classesList[] = $cr[0];
    }
}
if (empty($classesList)) $classesList = ['NURSERY', 'KINDER GARDEN', 'STANDERD 1', 'STANDERD 2', 'STANDERD 3', 'STANDERD 4', 'STANDERD 5'];

// Months Array
$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

// ---------------------- 2. QUERY STUDENTS & FEES ---------------------- //
$whereConditions = ["1=1"];

if ($scl_session !== 'All' && !empty($scl_session)) {
    $safe_sess = mysqli_real_escape_string($con, $scl_session);
    $whereConditions[] = "s.scl_session = '$safe_sess'";
}

if ($scl_class !== 'All' && !empty($scl_class)) {
    $safe_cls = mysqli_real_escape_string($con, $scl_class);
    $whereConditions[] = "s.scl_class = '$safe_cls'";
}

if ($add_status === 'New Admission') {
    $whereConditions[] = "(s.add_status = 'New Admission' OR s.admission_fee_val > 0)";
} elseif ($add_status === 'Readmission') {
    $whereConditions[] = "(s.add_status = 'Readmission' OR s.re_admission_fee_val > 0)";
}

if (!empty($search_text)) {
    $safe_srch = mysqli_real_escape_string($con, $search_text);
    $whereConditions[] = "(s.scl_name LIKE '%$safe_srch%' OR s.scl_reg_no LIKE '%$safe_srch%' OR s.scl_roll_no LIKE '%$safe_srch%' OR s.scl_phone_no LIKE '%$safe_srch%')";
}

$whereSQL = implode(" AND ", $whereConditions);

$students_sql = "
    SELECT s.*
    FROM rrsv_student_registration s
    WHERE $whereSQL
    ORDER BY s.scl_class ASC, CAST(s.scl_roll_no AS UNSIGNED) ASC, s.scl_name ASC
";
$students_result = mysqli_query($con, $students_sql);

// Batch load all payment summaries for efficiency
$payments_range = [];
$payments_total = [];

$safe_from = mysqli_real_escape_string($con, $fromDate);
$safe_to   = mysqli_real_escape_string($con, $endDate);

// Payments in selected date range (for day-wise or period collection)
if (!empty($safe_from) && !empty($safe_to)) {
    $pq = mysqli_query($con, "
        SELECT pay_id, SUM(payment_recive) as range_paid
        FROM rrsv_scl_studen_fee
        WHERE scl_date BETWEEN '$safe_from' AND '$safe_to'
        GROUP BY pay_id
    ");
    if ($pq) {
        while ($pr = mysqli_fetch_assoc($pq)) {
            $payments_range[$pr['pay_id']] = floatval($pr['range_paid']);
        }
    }
}

// All payments in this session/year (to calculate exact advance and dues)
$sess_filter = ($scl_session !== 'All' && !empty($scl_session)) ? "AND scl_session = '$safe_sess'" : "";
$pq_all = mysqli_query($con, "
    SELECT pay_id, SUM(payment_recive) as total_paid, MAX(scl_date) as last_date, COUNT(*) as tx_count
    FROM rrsv_scl_studen_fee
    WHERE 1=1 $sess_filter
    GROUP BY pay_id
");
if ($pq_all) {
    while ($pr = mysqli_fetch_assoc($pq_all)) {
        $payments_total[$pr['pay_id']] = [
            'total_paid' => floatval($pr['total_paid']),
            'last_date' => $pr['last_date'],
            'tx_count' => intval($pr['tx_count'])
        ];
    }
}

// ---------------------- 3. PROCESS ROWS & AGGREGATE SUMMARY ---------------------- //
$reportRows = [];

$sum_actual_collection = 0; // Total payment received across all students
$sum_new_adm_collection = 0; // New admission actual collection
$sum_readm_collection = 0;  // Re-admission actual collection
$sum_range_collection = 0;  // Period / Day collection
$sum_current_due = 0;       // Auto generated end of month due
$sum_year_end_due = 0;      // Due amount at the end of the year
$sum_advance_amount = 0;    // Total advance
$count_new_adm = 0;
$count_readm = 0;

// Per-Column Totals for Table Footer
$col_totals = [
    'count'               => 0,
    'admission_fee'       => 0,
    'readmission_fee'     => 0,
    'monthly_fee'         => 0,
    'yearly_tuition'      => 0,
    'car_count'           => 0,
    'car_monthly'         => 0,
    'yearly_car'          => 0,
    'uniform'             => 0,
    'shoes'               => 0,
    'winter'              => 0,
    'adm_form'            => 0,
    'bag'                 => 0,
    'icard'               => 0,
    'diary'               => 0,
    'books'               => 0,
    'copy'                => 0,
    'total_items'         => 0,
    'gross_no_car'        => 0,
    'gross_with_car'      => 0,
    'total_package_gross' => 0,
    'range_paid'          => 0,
    'total_paid'          => 0,
    'advance'             => 0,
    'current_due'         => 0,
    'year_end_due'        => 0
];

if ($students_result) {
    while ($row = mysqli_fetch_assoc($students_result)) {
        $sid = $row['id'];

        $isNew = ($row['add_status'] === 'New Admission' || floatval($row['admission_fee_val']) > 0);
        if ($isNew) $count_new_adm++;
        else $count_readm++;

        // Admission & Re-Admission Fee
        $admFee = $isNew ? floatval($row['admission_fee_val']) : 0;
        $readmFee = !$isNew ? floatval($row['re_admission_fee_val']) : 0;

        // Tuition Fee (Point 1 & 2: Variable tuition fees)
        $monthlyFee = floatval($row['monthly_fee']);
        $fullYearTuition = $monthlyFee * 12;

        // Car Cost (Point 2: Variable as per KM)
        $hasCar = ($row['scl_car'] === 'Yes' || floatval($row['car_cost']) > 0);
        $carMonthly = floatval($row['car_cost']);
        $fullYearCar = $hasCar ? ($carMonthly * 12) : 0;

        // Items (Point 2: Variable Uniform, Shoes, Winter, Bag, Books, Copy, etc.)
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

        // Dynamic Gross Amount (Point 4: Gross may change throughout year)
        $grossWithoutCar = ($isNew ? $admFee : $readmFee) + $fullYearTuition + $totalItems;
        $grossWithCar    = $grossWithoutCar + $fullYearCar;
        $totalGross      = $hasCar ? $grossWithCar : $grossWithoutCar;

        // Payments: Note that admissionval (Admission Form fee) is already paid when admission was taken
        $feePaid = $payments_total[$sid]['total_paid'] ?? 0;
        $totalPaid = $feePaid + $admForm;
        $rangePaid = $payments_range[$sid] ?? 0;

        // Point 5: Auto-generated End of Month Due calculation
        // Expected up to selected month:
        $expectedUpToMonth = ($isNew ? $admFee : $readmFee) + $totalItems + ($upToMonth * $monthlyFee) + ($upToMonth * $carMonthly);

        // Point 6: Advance + Due after payment
        if ($totalPaid >= $expectedUpToMonth) {
            $advance = $totalPaid - $expectedUpToMonth;
            $currentDue = 0;
        } else {
            $advance = 0;
            $currentDue = $expectedUpToMonth - $totalPaid;
        }

        // Point 7: Year-End Due amount
        $yearEndDue = max(0, $totalGross - $totalPaid);

        // Accumulate Top Cards (Point 3)
        $sum_actual_collection += $totalPaid;
        $sum_range_collection  += $rangePaid;
        $sum_current_due       += $currentDue;
        $sum_year_end_due      += $yearEndDue;
        $sum_advance_amount    += $advance;

        if ($isNew) {
            $sum_new_adm_collection += $totalPaid;
        } else {
            $sum_readm_collection   += $totalPaid;
        }

        // Accumulate Column Totals for Table Footer
        $col_totals['count']++;
        $col_totals['admission_fee']       += $admFee;
        $col_totals['readmission_fee']     += $readmFee;
        $col_totals['monthly_fee']         += $monthlyFee;
        $col_totals['yearly_tuition']      += $fullYearTuition;
        if ($hasCar) $col_totals['car_count']++;
        $col_totals['car_monthly']         += $carMonthly;
        $col_totals['yearly_car']          += $fullYearCar;
        $col_totals['uniform']             += $uniform;
        $col_totals['shoes']               += $shoes;
        $col_totals['winter']              += $winter;
        $col_totals['adm_form']            += $admForm;
        $col_totals['bag']                 += $bag;
        $col_totals['icard']               += $icard;
        $col_totals['diary']               += $diary;
        $col_totals['books']               += $books;
        $col_totals['copy']                += $copy;
        $col_totals['total_items']         += $totalItems;
        $col_totals['gross_no_car']        += $grossWithoutCar;
        $col_totals['gross_with_car']      += $grossWithCar;
        $col_totals['total_package_gross'] += $totalGross;
        $col_totals['range_paid']          += $rangePaid;
        $col_totals['total_paid']          += $totalPaid;
        $col_totals['advance']             += $advance;
        $col_totals['current_due']         += $currentDue;
        $col_totals['year_end_due']        += $yearEndDue;

        // Guardian phone
        $phone = trim($row['scl_phone_no'] ?: ($row['father_phone_no'] ?: ''));
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;

        $reportRows[] = [
            'id'                  => $sid,
            'reg_no'              => $row['scl_reg_no'],
            'roll_no'             => $row['scl_roll_no'],
            'name'                => $row['scl_name'],
            'class'               => $row['scl_class'],
            'session'             => $row['scl_session'],
            'is_new'              => $isNew,
            'status_label'        => $isNew ? 'New Admission' : 'Re-Admission',
            'admission_fee'       => $admFee,
            'readmission_fee'     => $readmFee,
            'monthly_fee'         => $monthlyFee,
            'yearly_tuition'      => $fullYearTuition,
            'has_car'             => $hasCar,
            'car_monthly'         => $carMonthly,
            'yearly_car'          => $fullYearCar,
            'uniform'             => $uniform,
            'shoes'               => $shoes,
            'winter'              => $winter,
            'adm_form'            => $admForm,
            'bag'                 => $bag,
            'icard'               => $icard,
            'diary'               => $diary,
            'books'               => $books,
            'copy'                => $copy,
            'total_items'         => $totalItems,
            'gross_without_car'   => $grossWithoutCar,
            'gross_with_car'      => $grossWithCar,
            'total_gross'         => $totalGross,
            'fee_paid'            => $feePaid,
            'range_paid'          => $rangePaid,
            'total_paid'          => $totalPaid,
            'advance'             => $advance,
            'current_due'         => $currentDue,
            'year_end_due'        => $yearEndDue,
            'phone'               => $phone,
            'clean_phone'         => $cleanPhone,
            'last_date'           => $payments_total[$sid]['last_date'] ?? ''
        ];
    }
}
include_once __DIR__ . '/../../include/header.php';
?>
<!-- Font Awesome Icons for buttons and badges -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>admin_panel/assets/css/font-awesome.min.css">

<style>
    /* ==========================================================================
   POINT 9: FREEZE PANES (Sticky Columns after Student Name)
   ========================================================================== */
    .table-freeze-container {
        position: relative;
        overflow-x: auto;
        max-width: 100%;
        border: 1px solid #d2d6de;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-freeze {
        border-collapse: separate !important;
        border-spacing: 0;
        margin-bottom: 0 !important;
        width: 100%;
    }

    .table-freeze th,
    .table-freeze td {
        white-space: nowrap;
        vertical-align: middle !important;
        padding: 7px 10px;
        border-top: none;
        border-right: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
    }

    /* Sticky Columns on Desktop (Screens >= 769px) */
    @media (min-width: 769px) {
        .col-freeze-1 {
            position: sticky !important;
            left: 0px !important;
            z-index: 10 !important;
            background-color: #ffffff !important;
            width: 45px;
            min-width: 45px;
            text-align: center;
        }

        .col-freeze-2 {
            position: sticky !important;
            left: 45px !important;
            z-index: 10 !important;
            background-color: #ffffff !important;
            width: 110px;
            min-width: 110px;
        }

        .col-freeze-3 {
            position: sticky !important;
            left: 155px !important;
            z-index: 10 !important;
            background-color: #ffffff !important;
            width: 185px;
            min-width: 185px;
            box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.18) !important;
            font-weight: 600;
        }

        /* Sticky Headers & Footers on Desktop */
        thead th.col-freeze-1,
        thead th.col-freeze-2,
        thead th.col-freeze-3,
        tfoot td.col-freeze-1,
        tfoot td.col-freeze-2,
        tfoot td.col-freeze-3 {
            z-index: 25 !important;
            background-color: #e2e8f0 !important;
        }

        /* Hover effect on sticky rows */
        .table-freeze tbody tr:hover td.col-freeze-1,
        .table-freeze tbody tr:hover td.col-freeze-2,
        .table-freeze tbody tr:hover td.col-freeze-3 {
            background-color: #ebf3ff !important;
        }
    }

    /* Table Footer Styling */
    tfoot tr {
        font-weight: bold;
        background-color: #f1f5f9;
        border-top: 3px solid #1e3c72 !important;
        border-bottom: 3px solid #1e3c72 !important;
    }
    tfoot td {
        vertical-align: middle !important;
        padding: 9px 10px !important;
        border-right: 1px solid #cbd5e1 !important;
        white-space: nowrap;
        font-size: 12px;
    }

    /* Mobile screens (<= 768px): Smooth Horizontal Scroll without sticky blocking view */
    @media (max-width: 768px) {
        .col-freeze-1,
        .col-freeze-2,
        .col-freeze-3 {
            position: static !important;
            left: auto !important;
            box-shadow: none !important;
            z-index: 1 !important;
        }

        .table-freeze-container {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
    }

    thead th {
        background-color: #ecf0f5 !important;
        font-size: 12px;
        text-transform: uppercase;
        color: #333;
    }

    .table-freeze tbody tr:hover td {
        background-color: #f7faff !important;
    }

    /* Badge styling */
    .badge-advance {
        background-color: #00a65a;
        color: #fff;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .badge-due {
        background-color: #dd4b39;
        color: #fff;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .badge-yeardue {
        background-color: #605ca8;
        color: #fff;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    /* Box and Card styling for Spica / Material Admin */
    .box {
        position: relative;
        border-radius: 6px;
        background: #ffffff;
        border: 1px solid #e5e9f2;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }
    .box.box-primary {
        border-top: 3px solid #1a337e;
    }
    .box.box-solid {
        border: 1px solid #d2d6de;
    }
    .box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 18px;
        border-bottom: 1px solid #edf2f9;
        background: #ffffff;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .box-header .box-title {
        font-size: 14px;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .box-tools {
        margin-left: auto;
    }
    .box-tools .btn-box-tool {
        padding: 3px 8px;
        font-size: 13px;
        background: transparent;
        color: #7f8c8d;
        border: none;
        cursor: pointer;
    }
    .box-tools .btn-box-tool:hover {
        color: #2c3e50;
    }
    .box-body {
        padding: 18px;
    }

    /* Complete Form Control Override (Removes red glow from left_nav.php) */
    .form-control, input.form-control, select.form-control, textarea.form-control {
        border: 1px solid #d1d5db !important;
        box-shadow: none !important;
        border-radius: 6px !important;
        height: 38px !important;
        font-size: 13px !important;
        color: #1f2937 !important;
        background-color: #ffffff !important;
        padding: 6px 12px !important;
        outline: none !important;
    }
    .form-control:focus, input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
        border-color: #1a337e !important;
        box-shadow: 0 0 0 3px rgba(26, 51, 126, 0.12) !important;
        outline: none !important;
    }
    select.form-control {
        cursor: pointer;
    }
    .form-group label {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        margin-bottom: 5px !important;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Input Group styling */
    .input-group .form-control {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .input-group .btn {
        height: 38px !important;
        line-height: 24px !important;
        padding: 6px 14px !important;
        font-size: 13px !important;
    }

    /* Button Enhancements for Spica */
    .btn-default, .btn-outline-secondary {
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
    }
    .btn-default:hover, .btn-outline-secondary:hover {
        background-color: #e2e8f0 !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }
    .btn-primary {
        background-color: #1a337e !important;
        border-color: #1a337e !important;
        color: #ffffff !important;
    }
    .btn-primary:hover {
        background-color: #132760 !important;
        border-color: #132760 !important;
    }
    .btn-success {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
    }
    .btn-success:hover {
        background-color: #059669 !important;
        border-color: #059669 !important;
    }
    .btn-info {
        background-color: #0ea5e9 !important;
        border-color: #0ea5e9 !important;
        color: #ffffff !important;
    }
    .btn-info:hover {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
    }
    .btn-warning {
        background-color: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #1e293b !important;
    }
    .btn-warning:hover {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
    }

    /* Badges & Labels */
    .label {
        display: inline-block;
        padding: 3px 7px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }
    .label-success { background-color: #28a745; }
    .label-primary { background-color: #007bff; }
    .label-warning { background-color: #ffc107; color: #212529 !important; }
    .label-default { background-color: #6c757d; }
    .label-info { background-color: #17a2b8; }
    .label-danger { background-color: #dc3545; }

    /* Stat Cards Container - 5 Equal Columns */
    .stat-cards-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }
    .stat-card-wrapper {
        flex: 1 1 calc(20% - 12px);
        min-width: 190px;
        display: flex;
    }
    .stat-card-custom {
        flex: 1;
        border-radius: 8px;
        padding: 16px 18px;
        color: #fff !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 110px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.14);
    }
    .stat-card-custom h3 {
        margin: 0 0 6px 0;
        font-weight: 700;
        font-size: 21px;
        line-height: 1.2;
        color: #fff !important;
        letter-spacing: -0.3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stat-card-custom p {
        margin: 0 0 4px 0;
        font-size: 13px;
        opacity: 0.95;
        color: #fff !important;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .stat-card-custom small {
        font-size: 11px;
        opacity: 0.88;
        color: #fff !important;
        white-space: nowrap;
    }

    .bg-green { background: linear-gradient(135deg, #1e8449, #27ae60) !important; color: #fff !important; }
    .bg-aqua { background: linear-gradient(135deg, #0e86d4, #00b4d8) !important; color: #fff !important; }
    .bg-blue { background: linear-gradient(135deg, #1f4e79, #2980b9) !important; color: #fff !important; }
    .bg-yellow { background: linear-gradient(135deg, #d35400, #e67e22) !important; color: #fff !important; }
    .bg-purple { background: linear-gradient(135deg, #6c3483, #8e44ad) !important; color: #fff !important; }

    /* Modal Dialog & Header Modern Styling */
    .modal-content {
        border-radius: 8px !important;
        overflow: hidden;
        border: none !important;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.22) !important;
    }
    .modal-header {
        border-bottom: none !important;
        padding: 14px 20px !important;
    }
    .modal-header .close,
    button.close,
    .modal-header button.close {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #ffffff !important;
        font-size: 26px !important;
        font-weight: 300 !important;
        line-height: 1 !important;
        padding: 0 !important;
        margin: 0 !important;
        opacity: 0.85 !important;
        cursor: pointer !important;
        float: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 32px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 4px !important;
        text-shadow: none !important;
    }
    .modal-header .close:hover,
    button.close:hover,
    .modal-header button.close:hover {
        opacity: 1 !important;
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }
    .modal-footer {
        border-top: 1px solid #edf2f7 !important;
        padding: 12px 20px !important;
        background: #f8fafc !important;
    }
    #periodButtonGroup .btn {
        font-size: 12px;
        padding: 6px 12px;
        font-weight: 500;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }
    #periodButtonGroup .btn.active,
    #periodButtonGroup .btn:hover {
        background: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
    }
</style>
<!-- Dynamic Column Visibility Style Tag -->
<style id="dynamic-column-css"></style>

<div class="main-panel">
    <div class="content-wrapper">

        <!-- Page Header / Breadcrumb Card -->
        <div class="d-flex align-items-center justify-content-between mb-4 bg-white p-3 rounded shadow-sm border" style="border-radius: 6px;">
            <div>
                <h4 class="font-weight-bold mb-1" style="color: #1a337e;">
                    <i class="fa fa-money text-success"></i> Money Report &amp; Fee Management Ledger
                </h4>
                <p class="text-muted mb-0 font-weight-normal" style="font-size: 13px;">
                    Active Session: <strong><?= htmlspecialchars($scl_session) ?></strong> | 
                    Due Calculation Benchmark: <strong><?= $months[$upToMonth] ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>home.php" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-dashboard"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- ==============================================================
             POINT 3: TOP SUMMARY STAT CARDS (New vs Re-Admission Separately)
             ============================================================== -->
        <div class="stat-cards-container">
            <div class="stat-card-wrapper">
                <div class="stat-card-custom bg-green">
                    <h3>₹<?= number_format($sum_actual_collection, 2) ?></h3>
                    <p><i class="fa fa-check-circle"></i> Actual Collection</p>
                    <small>Selected Range: ₹<?= number_format($sum_range_collection, 2) ?></small>
                </div>
            </div>

            <div class="stat-card-wrapper">
                <div class="stat-card-custom bg-aqua">
                    <h3>₹<?= number_format($sum_new_adm_collection, 2) ?></h3>
                    <p><i class="fa fa-user-plus"></i> New Admission (<?= $count_new_adm ?>)</p>
                    <small>Full Package Collection</small>
                </div>
            </div>

            <div class="stat-card-wrapper">
                <div class="stat-card-custom bg-blue">
                    <h3>₹<?= number_format($sum_readm_collection, 2) ?></h3>
                    <p><i class="fa fa-refresh"></i> Re-Admission (<?= $count_readm ?>)</p>
                    <small>Tuition + Car + Items</small>
                </div>
            </div>

            <div class="stat-card-wrapper">
                <div class="stat-card-custom bg-yellow">
                    <h3>₹<?= number_format($sum_current_due, 2) ?></h3>
                    <p><i class="fa fa-clock-o"></i> Due (<?= $months[$upToMonth] ?>)</p>
                    <small>Advance: ₹<?= number_format($sum_advance_amount, 2) ?></small>
                </div>
            </div>

            <div class="stat-card-wrapper">
                <div class="stat-card-custom bg-purple">
                    <h3>₹<?= number_format($sum_year_end_due, 2) ?></h3>
                    <p><i class="fa fa-calendar-check-o"></i> Year-End Due</p>
                    <small>Total Students: <?= count($reportRows) ?></small>
                </div>
            </div>
        </div>

        <!-- ==============================================================
             POINT 8 & 11: ADVANCED SEARCH & DROPDOWN FILTER BAR
             ============================================================== -->
        <div class="box box-primary" id="filterBox" style="margin-bottom: 20px;">
            <div class="box-header with-border" style="cursor: pointer;" onclick="toggleFilterBox(event)">
                <h3 class="box-title"><i class="fa fa-filter text-primary"></i> Search, Session &amp; Date-wise Filters</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" id="filterCollapseBtn" onclick="toggleFilterBox(event)"><i class="fa fa-minus" id="filterCollapseIcon"></i></button>
                </div>
            </div>
            <div class="box-body" id="filterBoxBody" style="display: block !important; padding: 16px 20px;">
                <form method="get" action="" id="filterForm">
                    <!-- Row 1: Primary Dropdown Selectors -->
                    <div class="row">
                        <!-- Session Dropdown -->
                        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-calendar text-primary"></i> Session</label>
                                <select name="scl_session" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                    <option value="All" <?= ($scl_session === 'All') ? 'selected' : '' ?>>All Sessions</option>
                                    <?php foreach ($sessionsList as $sess): ?>
                                        <option value="<?= htmlspecialchars($sess) ?>" <?= ($scl_session == $sess) ? 'selected' : '' ?>>
                                            Session <?= htmlspecialchars($sess) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Class Dropdown -->
                        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-book text-primary"></i> Class</label>
                                <select name="scl_class" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                    <option value="All" <?= ($scl_class === 'All') ? 'selected' : '' ?>>All Classes</option>
                                    <?php foreach ($classesList as $cls): ?>
                                        <option value="<?= htmlspecialchars($cls) ?>" <?= ($scl_class == $cls) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cls) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Admission Type Dropdown -->
                        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-tags text-primary"></i> Admission Type</label>
                                <select name="add_status" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                    <option value="All" <?= ($add_status === 'All') ? 'selected' : '' ?>>All (New + Re)</option>
                                    <option value="New Admission" <?= ($add_status === 'New Admission') ? 'selected' : '' ?>>1. New Admission</option>
                                    <option value="Readmission" <?= ($add_status === 'Readmission') ? 'selected' : '' ?>>2. Re-Admission</option>
                                </select>
                            </div>
                        </div>

                        <!-- Point 5: Auto-generated End of Month Due Benchmark Dropdown -->
                        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-hourglass-half text-primary"></i> Due Up To Month</label>
                                <select name="upToMonth" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                    <?php foreach ($months as $num => $mname): ?>
                                        <option value="<?= $num ?>" <?= ($upToMonth == $num) ? 'selected' : '' ?>>
                                            <?= $num ?>. <?= $mname ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Date Range: From Date -->
                        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-calendar-check-o text-primary"></i> From Date</label>
                                <input type="date" name="fromDate" id="fromDateInput" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
                            </div>
                        </div>

                        <!-- Date Range: To Date -->
                        <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-calendar-check-o text-primary"></i> To Date</label>
                                <input type="date" name="endDate" id="endDateInput" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Search, Quick Dates & Action Buttons Toolbar -->
                    <div class="row align-items-end pt-2" style="border-top: 1px dashed #e2e8f0;">
                        <!-- Student Search input group -->
                        <div class="col-lg-4 col-md-5 col-12 mb-lg-0 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-search text-primary"></i> Search Student (Name / Reg No / Phone)</label>
                                <div class="input-group">
                                    <input type="text" name="search_text" class="form-control" placeholder="Type Name, Reg. No, Phone..." value="<?= htmlspecialchars($search_text) ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary" title="Search"><i class="fa fa-search"></i> Search</button>
                                        <a href="index.php" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa fa-refresh"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Date Preset Buttons -->
                        <div class="col-lg-4 col-md-7 col-12 mb-lg-0 mb-3">
                            <div class="form-group mb-0">
                                <label><i class="fa fa-clock-o text-primary"></i> Quick Date Range</label>
                                <div class="btn-group d-flex w-100" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickDate('today')">Today</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickDate('yesterday')">Yesterday</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickDate('this_month')">This Month</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" onclick="setQuickDate('year')">Full Year</button>
                                </div>
                            </div>
                        </div>

                        <!-- Point 11: Day-Wise Save Snapshot & Print Options -->
                        <div class="col-lg-4 col-md-12 col-12 text-lg-right text-left">
                            <div class="form-group mb-0">
                                <label class="d-none d-lg-block" style="color: transparent;">Actions</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="saveDaySnapshot()" title="Save today's collection snapshot into database">
                                        <i class="fa fa-save"></i> Save Day Report
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm font-weight-bold text-white" onclick="openSnapshotsModal()" title="View previously saved daily snapshots">
                                        <i class="fa fa-history"></i> Saved Reports
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm font-weight-bold text-white" onclick="downloadMoneyReportPDF()" title="Download PDF of Money Report">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm font-weight-bold text-dark" onclick="printMoneyReport()" title="Print complete report">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

                <!-- ==============================================================
                 COLUMN TOGGLE CHECKBOXES (CLICK HEADER TO EXPAND / COLLAPSE)
                 ============================================================== -->
                <div class="box box-solid collapsed-box mb-2" id="colToggleBox" style="margin-bottom: 12px; border: 1px solid #d2d6de; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                    <div class="box-header with-border" id="colToggleHeader" style="padding: 9px 15px; cursor: pointer; background: #fdfefe; user-select: none;">
                        <h5 class="box-title" style="font-size: 13px; margin: 0; color: #2c3e50; font-weight: 600;">
                            <i class="fa fa-columns text-primary"></i> <strong>Show / Hide Columns (Click to Toggle)</strong>
                            <span id="colCountBadge" class="badge bg-green" style="font-size: 11px; margin-left: 8px;">All 27 Columns Visible</span>
                        </h5>
                        <div class="box-tools pull-right">
                            <span class="text-muted hidden-xs" style="font-size: 11px; margin-right: 6px;">Click to expand/collapse</span>
                            <button type="button" class="btn btn-box-tool" id="colToggleBtn" style="padding: 2px 6px;">
                                <i class="fa fa-plus text-primary" id="colToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body" id="colToggleBody" style="display: none; padding: 12px 15px; background: #fbfcfc; border-top: 1px solid #eee;">
                        <!-- Quick Preset Buttons -->
                        <div style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px dashed #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                            <div class="btn-group btn-group-sm">
                                <span style="font-weight: 600; margin-right: 8px; line-height: 30px; font-size: 12px; color: #475569;"><i class="fa fa-sliders text-primary"></i> Quick Views:</span>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyColPreset('all')"><i class="fa fa-check-square-o text-success"></i> Show All</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyColPreset('dues')"><i class="fa fa-money text-info"></i> Dues &amp; Payments Only</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyColPreset('breakdown')"><i class="fa fa-list text-purple"></i> Detailed Fee Items</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="applyColPreset('hide_all')"><i class="fa fa-square-o text-danger"></i> Hide All</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetColDefaults()"><i class="fa fa-refresh"></i> Reset Default</button>
                            </div>
                        </div>

                        <div class="row">
                            <?php
                            $colsList = [
                                4 => "Class",
                                5 => "Admission Fee",
                                6 => "Re-Admission Fee",
                                7 => "Monthly Tuition",
                                8 => "Yearly Tuition",
                                9 => "Car Opted",
                                10 => "Monthly Car",
                                11 => "Yearly Car",
                                12 => "Uniform",
                                13 => "Shoes",
                                14 => "Winter",
                                15 => "Adm Form",
                                16 => "Bag",
                                17 => "I.Card",
                                18 => "Diary",
                                19 => "Books",
                                20 => "Copy",
                                21 => "Total Items",
                                22 => "Gross (No Car)",
                                23 => "Gross (With Car)",
                                24 => "Total Package Gross",
                                25 => "Range Paid",
                                26 => "Total Paid",
                                27 => "Advance",
                                28 => "Current Due",
                                29 => "Year-End Due",
                                30 => "Actions"
                            ];
                            foreach ($colsList as $idx => $cname):
                            ?>
                                <div class="col-md-2 col-sm-3 col-xs-6" style="font-size: 12px; margin-bottom: 6px;">
                                    <label style="font-weight: normal; cursor: pointer; margin-bottom: 0; user-select: none; display: flex; align-items: center;">
                                        <input type="checkbox" class="col-toggle-cb" data-target="<?= $idx ?>" checked style="margin: 0 6px 0 0; cursor: pointer;"> <?= $cname ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ==============================================================
                 POINT 9: MONEY REPORT DATA TABLE WITH FREEZE PANES
                 ============================================================== -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-table"></i> Student Fee Master Ledger
                            <span class="badge bg-blue"><?= count($reportRows) ?> Students Found</span>
                        </h3>
                        <div class="box-tools pull-right" style="display: flex; gap: 6px; align-items: center;">
                            <button type="button" class="btn btn-danger btn-xs font-weight-bold" onclick="downloadMoneyReportPDF()" title="Download PDF of Money Report"><i class="fa fa-file-pdf-o"></i> Download PDF</button>
                            <button type="button" class="btn btn-warning btn-xs font-weight-bold text-dark" onclick="printMoneyReport()" title="Print Money Report Ledger"><i class="fa fa-print"></i> Print Report</button>
                            <button type="button" class="btn btn-success btn-xs font-weight-bold" onclick="exportToCSV()" title="Export CSV Spreadsheet"><i class="fa fa-file-excel-o"></i> Export CSV</button>
                        </div>
                    </div>
                    <div class="box-body" style="padding: 0;">
                        <!-- Mobile swipe hint -->
                        <div class="d-md-none text-muted px-3 py-1" style="font-size: 11.5px; background: #f8fafc; border-bottom: 1px solid #edf2f7;">
                            <i class="fa fa-arrows-h text-primary"></i> <strong>Tip:</strong> Scroll horizontally to view all columns
                        </div>

                        <div class="table-freeze-container">
                            <table id="moneyReportTable" class="table table-bordered table-striped table-hover table-freeze text-sm">
                                <thead>
                                    <tr>
                                        <!-- POINT 9: Frozen Columns on Left (Desktop) -->
                                        <th class="col-freeze-1">Sl.</th>
                                        <th class="col-freeze-2">Reg. No</th>
                                        <th class="col-freeze-3">Student Name</th>

                                        <!-- Scrollable Columns -->
                                        <th class="col-item tbl-col-4">Class</th>
                                        <th class="col-item tbl-col-5" style="background:#eaf2f8 !important;">Admission Fee</th>
                                        <th class="col-item tbl-col-6" style="background:#eaf2f8 !important;">Re-Adm Fee</th>
                                        <th class="col-item tbl-col-7">Tuition/Mo</th>
                                        <th class="col-item tbl-col-8">Tuition/Yr</th>
                                        <th class="col-item tbl-col-9">Car?</th>
                                        <th class="col-item tbl-col-10">Car Rate/Mo</th>
                                        <th class="col-item tbl-col-11">Car Cost/Yr</th>
                                        <th class="col-item tbl-col-12">Uniform</th>
                                        <th class="col-item tbl-col-13">Shoes</th>
                                        <th class="col-item tbl-col-14">Winter</th>
                                        <th class="col-item tbl-col-15" title="Admission Form Fee - Already Paid when Admission taken">Adm Form <small class="text-green" style="font-weight:bold;">(Paid)</small></th>
                                        <th class="col-item tbl-col-16">Bag</th>
                                        <th class="col-item tbl-col-17">I.Card</th>
                                        <th class="col-item tbl-col-18">Diary</th>
                                        <th class="col-item tbl-col-19">Books</th>
                                        <th class="col-item tbl-col-20">Copy</th>
                                        <th class="col-item tbl-col-21">Total Items</th>
                                        <th class="col-item tbl-col-22">Gross (w/o Car)</th>
                                        <th class="col-item tbl-col-23">Gross (with Car)</th>
                                        <th class="col-item tbl-col-24" style="background:#fef9e7 !important; font-weight:700;">Full Package Gross</th>
                                        <th class="col-item tbl-col-25">Range Paid</th>
                                        <th class="col-item tbl-col-26" style="background:#e8f8f5 !important; font-weight:700;">Total Paid (Yr)</th>
                                        <!-- POINT 6: Advance + Due after payment -->
                                        <th class="col-item tbl-col-27" style="background:#eafaf1 !important;">Advance</th>
                                        <!-- POINT 5: Auto generated end of month due -->
                                        <th class="col-item tbl-col-28" style="background:#fdedec !important;">Due (<?= $months[$upToMonth] ?>)</th>
                                        <!-- POINT 7: End of year due -->
                                        <th class="col-item tbl-col-29" style="background:#f4ecf7 !important;">Year-End Due</th>
                                        <!-- POINT 5 & 10: Guardian Send & Statement Print -->
                                        <th class="col-item tbl-col-30 text-center" style="min-width: 170px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($reportRows)): ?>
                                        <?php $sl = 1;
                                        foreach ($reportRows as $row): ?>
                                            <tr>
                                                <!-- Sticky Columns on Desktop -->
                                                <td class="col-freeze-1"><?= $sl++ ?></td>
                                                <td class="col-freeze-2"><code><?= htmlspecialchars($row['reg_no']) ?></code></td>
                                                <td class="col-freeze-3" title="<?= htmlspecialchars($row['name']) ?>">
                                                    <?= htmlspecialchars($row['name']) ?>
                                                    <br>
                                                    <small class="text-muted" style="font-size:10px;">
                                                        <span class="label <?= $row['is_new'] ? 'label-success' : 'label-primary' ?>" style="font-size:9px; padding:1px 4px;">
                                                            <?= $row['status_label'] ?>
                                                        </span>
                                                    </small>
                                                </td>

                                                <!-- Scrollable Data Cells -->
                                                <td class="col-item tbl-col-4"><?= htmlspecialchars($row['class']) ?></td>
                                                <td class="col-item tbl-col-5"><?= ($row['admission_fee'] > 0) ? '₹' . number_format($row['admission_fee']) : '-' ?></td>
                                                <td class="col-item tbl-col-6"><?= ($row['readmission_fee'] > 0) ? '₹' . number_format($row['readmission_fee']) : '-' ?></td>
                                                <td class="col-item tbl-col-7">₹<?= number_format($row['monthly_fee']) ?></td>
                                                <td class="col-item tbl-col-8">₹<?= number_format($row['yearly_tuition']) ?></td>
                                                <td class="col-item tbl-col-9"><?= $row['has_car'] ? '<span class="label label-warning">Yes</span>' : '<span class="label label-default">No</span>' ?></td>
                                                <td class="col-item tbl-col-10"><?= ($row['car_monthly'] > 0) ? '₹' . number_format($row['car_monthly']) : '-' ?></td>
                                                <td class="col-item tbl-col-11"><?= ($row['yearly_car'] > 0) ? '₹' . number_format($row['yearly_car']) : '-' ?></td>
                                                <td class="col-item tbl-col-12"><?= ($row['uniform'] > 0) ? '₹' . number_format($row['uniform']) : '-' ?></td>
                                                <td class="col-item tbl-col-13"><?= ($row['shoes'] > 0) ? '₹' . number_format($row['shoes']) : '-' ?></td>
                                                <td class="col-item tbl-col-14"><?= ($row['winter'] > 0) ? '₹' . number_format($row['winter']) : '-' ?></td>
                                                <td class="col-item tbl-col-15"><?= ($row['adm_form'] > 0) ? '₹' . number_format($row['adm_form']) . ' <span class="label label-success" style="font-size:9px; padding:1px 4px;" title="Already paid when admission taken"><i class="fa fa-check"></i> Paid</span>' : '-' ?></td>
                                                <td class="col-item tbl-col-16"><?= ($row['bag'] > 0) ? '₹' . number_format($row['bag']) : '-' ?></td>
                                                <td class="col-item tbl-col-17"><?= ($row['icard'] > 0) ? '₹' . number_format($row['icard']) : '-' ?></td>
                                                <td class="col-item tbl-col-18"><?= ($row['diary'] > 0) ? '₹' . number_format($row['diary']) : '-' ?></td>
                                                <td class="col-item tbl-col-19"><?= ($row['books'] > 0) ? '₹' . number_format($row['books']) : '-' ?></td>
                                                <td class="col-item tbl-col-20"><?= ($row['copy'] > 0) ? '₹' . number_format($row['copy']) : '-' ?></td>
                                                <td class="col-item tbl-col-21">₹<?= number_format($row['total_items']) ?></td>
                                                <td class="col-item tbl-col-22">₹<?= number_format($row['gross_without_car']) ?></td>
                                                <td class="col-item tbl-col-23">₹<?= number_format($row['gross_with_car']) ?></td>
                                                <!-- POINT 1 & 4: Full Package Gross -->
                                                <td class="col-item tbl-col-24" style="font-weight:700; color:#b7950b;">₹<?= number_format($row['total_gross']) ?></td>
                                                <td class="col-item tbl-col-25">₹<?= number_format($row['range_paid']) ?></td>
                                                <!-- Total Paid -->
                                                <td class="col-item tbl-col-26" style="font-weight:700; color:#117a65;">
                                                    ₹<?= number_format($row['total_paid']) ?>
                                                    <?php if ($row['adm_form'] > 0): ?>
                                                        <br><small class="text-muted" style="font-size:10px; font-weight:normal;" title="Includes ₹<?= number_format($row['adm_form']) ?> Admission Form fee paid at admission">(inc. Form)</small>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- POINT 6: Advance Amount -->
                                                <td class="col-item tbl-col-27">
                                                    <?php if ($row['advance'] > 0): ?>
                                                        <span class="badge-advance">+₹<?= number_format($row['advance']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">₹0</span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- POINT 5 & 6: Current Due End of Month -->
                                                <td class="col-item tbl-col-28">
                                                    <?php if ($row['current_due'] > 0): ?>
                                                        <span class="badge-due">₹<?= number_format($row['current_due']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-green"><i class="fa fa-check"></i> Cleared</span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- POINT 7: End of Year Due -->
                                                <td class="col-item tbl-col-29">
                                                    <?php if ($row['year_end_due'] > 0): ?>
                                                        <span class="badge-yeardue">₹<?= number_format($row['year_end_due']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-green"><i class="fa fa-check-circle"></i> Paid</span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- POINT 5 & 10: Guardian Notice & Statement Print -->
                                                <td class="col-item tbl-col-30 text-center">
                                                    <!-- WhatsApp Send (Point 5) -->
                                                    <?php if (!empty($row['clean_phone'])): ?>
                                                        <?php
                                                        $waText = "Dear Guardian, Fee Notice for " . $row['name'] . " (Reg: " . $row['reg_no'] . ", Class: " . $row['class'] . "):\n"
                                                            . "- Up to " . $months[$upToMonth] . " Total Paid: Rs. " . number_format($row['total_paid']) . "\n"
                                                            . ($row['current_due'] > 0 ? "- Current Due: Rs. " . number_format($row['current_due']) . "\n" : "- Dues: Cleared\n")
                                                            . ($row['advance'] > 0 ? "- Advance Balance: Rs. " . number_format($row['advance']) . "\n" : "")
                                                            . "- Full Year Balance: Rs. " . number_format($row['year_end_due']) . "\n"
                                                            . "Rasulpur Ramkrishna Sarada Vidyapith";
                                                        $waUrl = "https://api.whatsapp.com/send?phone=" . $row['clean_phone'] . "&text=" . urlencode($waText);
                                                        ?>
                                                        <a href="<?= $waUrl ?>" target="_blank" class="btn btn-xs btn-success" title="Send WhatsApp Due Notice to Guardian">
                                                            <i class="fa fa-whatsapp"></i> Send
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-xs btn-default disabled" title="No phone registered"><i class="fa fa-whatsapp"></i></button>
                                                    <?php endif; ?>

                                                    <!-- 6-Month / Monthly Statement & Print (Point 10) -->
                                                    <button type="button" class="btn btn-xs btn-info" onclick="openStudentStatement(<?= $row['id'] ?>)" title="View 6-Month &amp; Monthly Payment Statement">
                                                        <i class="fa fa-file-text-o"></i> 6-Mo
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="30" class="text-center py-4 text-muted" style="padding: 30px;">
                                                <i class="fa fa-info-circle fa-2x text-blue"></i>
                                                <p class="mt-2" style="font-size:15px; margin-top:10px;">No student records found for the selected session and filter criteria.</p>
                                                <a href="index.php?scl_session=<?= $scl_session ?>" class="btn btn-primary btn-sm">Reset Date Filter to Full Session</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($reportRows)): ?>
                                <tfoot>
                                    <tr style="background: #f1f5f9; font-weight: 800; border-top: 3px solid #1e3c72; border-bottom: 3px solid #1e3c72;">
                                        <!-- Sticky Columns on Desktop -->
                                        <td class="col-freeze-1 text-center" style="font-weight: 900; background: #e2e8f0 !important; color: #1e3a8a;">TOTAL</td>
                                        <td class="col-freeze-2 text-center" style="font-weight: 700; background: #e2e8f0 !important; color: #1e3a8a;">-</td>
                                        <td class="col-freeze-3" style="font-weight: 900; background: #e2e8f0 !important; color: #1e3a8a;"><?= count($reportRows) ?> Students</td>

                                        <!-- Scrollable Data Cells matching tbl-col-4 to tbl-col-30 -->
                                        <td class="col-item tbl-col-4 text-center" style="font-weight: 700;">-</td>
                                        <td class="col-item tbl-col-5" style="background:#e0effa !important; font-weight:800; color:#1e40af;"><?= ($col_totals['admission_fee'] > 0) ? '₹' . number_format($col_totals['admission_fee']) : '-' ?></td>
                                        <td class="col-item tbl-col-6" style="background:#e0effa !important; font-weight:800; color:#1e40af;"><?= ($col_totals['readmission_fee'] > 0) ? '₹' . number_format($col_totals['readmission_fee']) : '-' ?></td>
                                        <td class="col-item tbl-col-7" style="font-weight:700;">₹<?= number_format($col_totals['monthly_fee']) ?></td>
                                        <td class="col-item tbl-col-8" style="font-weight:700;">₹<?= number_format($col_totals['yearly_tuition']) ?></td>
                                        <td class="col-item tbl-col-9 text-center" style="font-weight:700;"><?= $col_totals['car_count'] ?> Cars</td>
                                        <td class="col-item tbl-col-10" style="font-weight:700;"><?= ($col_totals['car_monthly'] > 0) ? '₹' . number_format($col_totals['car_monthly']) : '-' ?></td>
                                        <td class="col-item tbl-col-11" style="font-weight:700;"><?= ($col_totals['yearly_car'] > 0) ? '₹' . number_format($col_totals['yearly_car']) : '-' ?></td>
                                        <td class="col-item tbl-col-12" style="font-weight:700;"><?= ($col_totals['uniform'] > 0) ? '₹' . number_format($col_totals['uniform']) : '-' ?></td>
                                        <td class="col-item tbl-col-13" style="font-weight:700;"><?= ($col_totals['shoes'] > 0) ? '₹' . number_format($col_totals['shoes']) : '-' ?></td>
                                        <td class="col-item tbl-col-14" style="font-weight:700;"><?= ($col_totals['winter'] > 0) ? '₹' . number_format($col_totals['winter']) : '-' ?></td>
                                        <td class="col-item tbl-col-15" style="font-weight:700;"><?= ($col_totals['adm_form'] > 0) ? '₹' . number_format($col_totals['adm_form']) : '-' ?></td>
                                        <td class="col-item tbl-col-16" style="font-weight:700;"><?= ($col_totals['bag'] > 0) ? '₹' . number_format($col_totals['bag']) : '-' ?></td>
                                        <td class="col-item tbl-col-17" style="font-weight:700;"><?= ($col_totals['icard'] > 0) ? '₹' . number_format($col_totals['icard']) : '-' ?></td>
                                        <td class="col-item tbl-col-18" style="font-weight:700;"><?= ($col_totals['diary'] > 0) ? '₹' . number_format($col_totals['diary']) : '-' ?></td>
                                        <td class="col-item tbl-col-19" style="font-weight:700;"><?= ($col_totals['books'] > 0) ? '₹' . number_format($col_totals['books']) : '-' ?></td>
                                        <td class="col-item tbl-col-20" style="font-weight:700;"><?= ($col_totals['copy'] > 0) ? '₹' . number_format($col_totals['copy']) : '-' ?></td>
                                        <td class="col-item tbl-col-21" style="font-weight:800; background:#f8fafc !important;">₹<?= number_format($col_totals['total_items']) ?></td>
                                        <td class="col-item tbl-col-22" style="font-weight:700;">₹<?= number_format($col_totals['gross_no_car']) ?></td>
                                        <td class="col-item tbl-col-23" style="font-weight:700;">₹<?= number_format($col_totals['gross_with_car']) ?></td>
                                        <td class="col-item tbl-col-24" style="background:#fef3c7 !important; font-weight:900; color:#92400e;">₹<?= number_format($col_totals['total_package_gross']) ?></td>
                                        <td class="col-item tbl-col-25" style="font-weight:800; color:#166534;">₹<?= number_format($col_totals['range_paid']) ?></td>
                                        <td class="col-item tbl-col-26" style="background:#dcfce7 !important; font-weight:900; color:#15803d;">₹<?= number_format($col_totals['total_paid']) ?></td>
                                        <td class="col-item tbl-col-27" style="background:#ecfdf5 !important; font-weight:800; color:#047857;"><?= ($col_totals['advance'] > 0) ? '+₹' . number_format($col_totals['advance']) : '₹0' ?></td>
                                        <td class="col-item tbl-col-28" style="background:#fee2e2 !important; font-weight:900; color:#b91c1c;"><?= ($col_totals['current_due'] > 0) ? '₹' . number_format($col_totals['current_due']) : 'Cleared' ?></td>
                                        <td class="col-item tbl-col-29" style="background:#f3e8ff !important; font-weight:900; color:#7e22ce;"><?= ($col_totals['year_end_due'] > 0) ? '₹' . number_format($col_totals['year_end_due']) : 'Paid' ?></td>
                                        <td class="col-item tbl-col-30 text-center">-</td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>

                    </div>
                </div>

    <!-- ==============================================================
     POINT 10: 6-MONTH & MONTHLY PAYMENT STATEMENT MODAL
     ============================================================== -->
    <div class="modal fade" id="statementModal" tabindex="-1" role="dialog" aria-labelledby="statementModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 950px; margin: 30px auto;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 12px 36px rgba(0,0,0,0.22);">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: none;">
                    <h4 class="modal-title" id="statementModalLabel" style="font-size: 15px; font-weight: 600; margin: 0; color: #fff; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-print"></i> Student Monthly Fee Statement &amp; Report
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #fff !important; background: transparent !important; border: none !important; font-size: 26px; line-height: 1; opacity: 0.85; cursor: pointer; padding: 0 4px !important; margin: 0 !important; box-shadow: none !important;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="statementModalContent" style="padding: 22px 24px; max-height: calc(85vh - 130px); overflow-y: auto;">
                    <div class="text-center py-4" style="padding: 40px;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-2" style="margin-top:10px;">Loading student payment history...</p>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; background: #f8fafc; border-top: 1px solid #edf2f7; padding: 12px 20px;">
                    <div class="btn-group" id="periodButtonGroup" role="group">
                        <button type="button" class="btn btn-default btn-sm active" onclick="filterStatementPeriod('all')"><i class="fa fa-calendar"></i> Full Year</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="filterStatementPeriod('h1')"><i class="fa fa-calendar-check-o"></i> 1st 6 Months (Jan - Jun)</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="filterStatementPeriod('h2')"><i class="fa fa-calendar-check-o"></i> 2nd 6 Months (Jul - Dec)</button>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="button" class="btn btn-success btn-sm" id="modalWhatsAppBtn" target="_blank" style="font-weight: 600;"><i class="fa fa-whatsapp"></i> Send WhatsApp Notice</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="printStatementArea()" style="font-weight: 600;"><i class="fa fa-print"></i> Print Statement</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
     POINT 11: SAVED DAILY SNAPSHOTS MODAL
     ============================================================== -->
    <div class="modal fade" id="snapshotsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 950px; margin: 30px auto;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 12px 36px rgba(0,0,0,0.22);">
                <div class="modal-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: none;">
                    <h4 class="modal-title" style="font-size: 15px; font-weight: 600; margin: 0; color: #fff; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-history"></i> Saved Daily Money Report Snapshots
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #fff !important; background: transparent !important; border: none !important; font-size: 26px; line-height: 1; opacity: 0.85; cursor: pointer; padding: 0 4px !important; margin: 0 !important; box-shadow: none !important;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="snapshotsModalContent" style="padding: 20px; max-height: calc(85vh - 130px); overflow-y: auto;">
                    <div class="text-center py-3"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i></div>
                </div>
                <div class="modal-footer" style="display: flex; justify-content: flex-end; background: #f8fafc; border-top: 1px solid #edf2f7; padding: 12px 20px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
     PRINTABLE STYLES FOR STATEMENT
     ============================================================== -->
    <style media="print">
        body * {
            visibility: hidden;
        }

        #printableStatementArea,
        #printableStatementArea * {
            visibility: visible;
        }

        #printableStatementArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    </style>

    </div>
    <!-- content-wrapper ends -->
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>

    <script>
        // ==============================================================
        // COLUMN TOGGLE SYSTEM (BOX COLLAPSE + FAST DYNAMIC CSS TOGGLE)
        // ==============================================================
        function toggleColumnBox() {
            var body = $('#colToggleBody');
            var icon = $('#colToggleIcon');
            body.slideToggle(200, function() {
                if (body.is(':visible')) {
                    $('#colToggleBox').removeClass('collapsed-box');
                    icon.removeClass('fa-plus').addClass('fa-minus');
                } else {
                    $('#colToggleBox').addClass('collapsed-box');
                    icon.removeClass('fa-minus').addClass('fa-plus');
                }
            });
        }

        function applyColumnStyles() {
            var hiddenCols = [];
            var totalCols = $('.col-toggle-cb').length;
            var visibleCount = 0;

            $('.col-toggle-cb').each(function() {
                var colIdx = $(this).data('target');
                if (!$(this).is(':checked')) {
                    hiddenCols.push(colIdx);
                } else {
                    visibleCount++;
                }
            });

            // Generate dynamic CSS rules to hide unselected columns cleanly (strictly scoped to #moneyReportTable)
            var css = '';
            if (hiddenCols.length > 0) {
                var selectors = [];
                for (var i = 0; i < hiddenCols.length; i++) {
                    selectors.push('#moneyReportTable .tbl-col-' + hiddenCols[i]);
                }
                css = selectors.join(', ') + ' { display: none !important; }';
            }

            var styleTag = document.getElementById('dynamic-column-css');
            if (!styleTag) {
                styleTag = document.createElement('style');
                styleTag.id = 'dynamic-column-css';
                document.head.appendChild(styleTag);
            }
            styleTag.innerHTML = css;

            // Update count badge
            if (hiddenCols.length === 0) {
                $('#colCountBadge').text('All ' + totalCols + ' Columns Visible').attr('class', 'badge bg-green');
            } else {
                $('#colCountBadge').text(visibleCount + ' / ' + totalCols + ' Columns Visible').attr('class', 'badge bg-yellow');
            }

            // Save preference to localStorage (version 2 key to avoid legacy collision)
            try {
                localStorage.setItem('rrsv_money_report_hidden_cols_v2', JSON.stringify(hiddenCols));
            } catch (e) {}
        }

        function applyColPreset(type) {
            if (type === 'all') {
                $('.col-toggle-cb').prop('checked', true);
            } else if (type === 'hide_all') {
                $('.col-toggle-cb').prop('checked', false);
            } else if (type === 'dues') {
                // Dues & Payments only: Class (4), Gross (24), Range Paid (25), Total Paid (26), Advance (27), Due (28), Year-End Due (29), Actions (30)
                var duesCols = [4, 24, 25, 26, 27, 28, 29, 30];
                $('.col-toggle-cb').each(function() {
                    var idx = parseInt($(this).data('target'));
                    $(this).prop('checked', duesCols.indexOf(idx) !== -1);
                });
            } else if (type === 'breakdown') {
                // Detailed Fee Items: hide intermediate gross subtotals
                var hideCols = [22, 23, 25];
                $('.col-toggle-cb').each(function() {
                    var idx = parseInt($(this).data('target'));
                    $(this).prop('checked', hideCols.indexOf(idx) === -1);
                });
            }
            applyColumnStyles();
        }

        function resetColDefaults() {
            try {
                localStorage.removeItem('rrsv_money_report_hidden_cols');
                localStorage.removeItem('rrsv_money_report_hidden_cols_v2');
            } catch (e) {}
            $('.col-toggle-cb').prop('checked', true);
            applyColumnStyles();
        }

        // Dedicated toggle for Filter box
        function toggleFilterBox(e) {
            if (e) {
                // Do not collapse if clicked inside form inputs, buttons or labels
                if ($(e.target).closest('input, select, button:not(#filterCollapseBtn), a, label').length) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
            }
            var body = $('#filterBoxBody');
            var icon = $('#filterCollapseIcon');
            body.slideToggle(200, function() {
                if (body.is(':visible')) {
                    $('#filterBox').removeClass('collapsed-box');
                    icon.removeClass('fa-plus').addClass('fa-minus');
                } else {
                    $('#filterBox').addClass('collapsed-box');
                    icon.removeClass('fa-minus').addClass('fa-plus');
                }
            });
        }

        $(function() {
            // Clean up old legacy localStorage key that might have contained conflicting indices
            try {
                localStorage.removeItem('rrsv_money_report_hidden_cols');
                var savedHidden = localStorage.getItem('rrsv_money_report_hidden_cols_v2');
                if (savedHidden) {
                    var hiddenArray = JSON.parse(savedHidden);
                    if (Array.isArray(hiddenArray) && hiddenArray.length > 0) {
                        $('.col-toggle-cb').each(function() {
                            var idx = parseInt($(this).data('target'));
                            if (hiddenArray.indexOf(idx) !== -1) {
                                $(this).prop('checked', false);
                            }
                        });
                    }
                }
            } catch (e) {}

            // Apply styles on page load
            applyColumnStyles();

            // Checkbox change listener
            $('.col-toggle-cb').on('change', function() {
                applyColumnStyles();
            });

            // Header click to expand / collapse
            $('#colToggleHeader').on('click', function(e) {
                if ($(e.target).closest('button, input, label').length && !$(e.target).closest('#colToggleBtn').length) {
                    return;
                }
                toggleColumnBox();
            });
        });

        // Quick Date Range helper
        function setQuickDate(type) {
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');
            var todayStr = yyyy + '-' + mm + '-' + dd;

            if (type === 'today') {
                $('#fromDateInput').val(todayStr);
                $('#endDateInput').val(todayStr);
            } else if (type === 'yesterday') {
                var yest = new Date();
                yest.setDate(yest.getDate() - 1);
                var ydd = String(yest.getDate()).padStart(2, '0');
                var ymm = String(yest.getMonth() + 1).padStart(2, '0');
                var ystr = yest.getFullYear() + '-' + ymm + '-' + ydd;
                $('#fromDateInput').val(ystr);
                $('#endDateInput').val(ystr);
            } else if (type === 'this_month') {
                $('#fromDateInput').val(yyyy + '-' + mm + '-01');
                var lastDay = new Date(yyyy, today.getMonth() + 1, 0).getDate();
                $('#endDateInput').val(yyyy + '-' + mm + '-' + String(lastDay).padStart(2, '0'));
            } else if (type === 'year') {
                var sessVal = $('select[name="scl_session"]').val();
                if (sessVal === 'All' || !sessVal) sessVal = yyyy;
                $('#fromDateInput').val(sessVal + '-01-01');
                $('#endDateInput').val(sessVal + '-12-31');
            }
            $('#filterForm').submit();
        }

        // Global active statement student
        var currentStatementData = null;
        var currentStatementPeriod = 'all';

        function openStudentStatement(studentId) {
            $('#statementModal').modal('show');
            $('#statementModalContent').html('<div class="text-center py-4" style="padding: 40px;"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p style="margin-top:10px;">Loading student payment history...</p></div>');
            currentStatementPeriod = 'all';
            $('#periodButtonGroup button').removeClass('active');
            $('#periodButtonGroup button:first-child').addClass('active');

            $.ajax({
                url: 'get_statement.php',
                type: 'GET',
                data: {
                    student_id: studentId
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        currentStatementData = resp;
                        renderStatementView(resp, 'all');
                    } else {
                        $('#statementModalContent').html('<div class="alert alert-danger">' + resp.error + '</div>');
                    }
                },
                error: function() {
                    $('#statementModalContent').html('<div class="alert alert-danger">Failed to load statement details.</div>');
                }
            });
        }

        function filterStatementPeriod(period) {
            currentStatementPeriod = period;
            $('#periodButtonGroup button').removeClass('active');
            if (period === 'all') $('#periodButtonGroup button:eq(0)').addClass('active');
            if (period === 'h1') $('#periodButtonGroup button:eq(1)').addClass('active');
            if (period === 'h2') $('#periodButtonGroup button:eq(2)').addClass('active');

            if (currentStatementData) {
                renderStatementView(currentStatementData, period);
            }
        }

        function renderStatementView(data, period) {
            var s = data.student;
            var allPayments = data.payments || [];
            var h1Months = ['January', 'February', 'March', 'April', 'May', 'June'];
            var h2Months = ['July', 'August', 'September', 'October', 'November', 'December'];

            var filteredPayments = [];
            var periodTotalPaid = 0;

            for (var i = 0; i < allPayments.length; i++) {
                var p = allPayments[i];
                var m = (p.month || '').trim();
                var include = false;

                if (period === 'all') {
                    include = true;
                } else if (period === 'h1') {
                    if (h1Months.indexOf(m) !== -1) include = true;
                    else if (p.date && parseInt(p.date.split('-')[1]) <= 6) include = true;
                } else if (period === 'h2') {
                    if (h2Months.indexOf(m) !== -1) include = true;
                    else if (p.date && parseInt(p.date.split('-')[1]) > 6) include = true;
                }

                if (include) {
                    filteredPayments.push(p);
                    periodTotalPaid += parseFloat(p.paid || 0);
                }
            }

            // Set WhatsApp link
            if (data.whatsapp_url) {
                $('#modalWhatsAppBtn').show().attr('onclick', "window.open('" + data.whatsapp_url + "', '_blank')");
            } else {
                $('#modalWhatsAppBtn').hide();
            }

            var periodLabel = (period === 'h1' ? '1st 6 Months: Jan - Jun' : (period === 'h2' ? '2nd 6 Months: Jul - Dec' : 'Full Session ' + s.session));

            var html = '<div id="printableStatementArea" style="font-family: inherit;">';

            // School Letterhead Header
            html += '<div style="text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 14px; margin-bottom: 18px;">';
            html += '  <h3 style="margin: 0; font-size: 19px; font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h3>';
            html += '  <p style="margin: 3px 0; font-size: 11.5px; color: #475569;">Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012</p>';
            html += '  <p style="margin: 0; font-size: 11px; color: #64748b;">Baidyadanga, Rasulpur, Memari, Purba Bardhaman, Pin - 713151</p>';
            html += '  <div style="margin-top: 10px;">';
            html += '    <span style="display: inline-block; padding: 4px 16px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 20px; font-weight: 700; font-size: 12px; letter-spacing: 0.3px;">';
            html += '      <i class="fa fa-file-text-o"></i> FEE STATEMENT (' + periodLabel.toUpperCase() + ')';
            html += '    </span>';
            html += '  </div>';
            html += '</div>';

            // Student Information Grid (Modern CSS Grid with 4 columns on desktop, responsive)
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; margin-bottom: 18px;">';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Student Name</span><strong style="font-size:13.5px; color:#0f172a;">' + s.name + '</strong></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Registration No</span><code style="font-size:12.5px; color:#b45309; background:#fef3c7; padding:2px 6px; border-radius:4px; font-weight:600;">' + s.reg_no + '</code></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Class</span><strong style="font-size:13px; color:#0f172a;">' + s.class + (s.roll_no ? ' (Roll: ' + s.roll_no + ')' : '') + '</strong></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Admission Category</span><span class="badge" style="background:' + (s.type === 'New Admission' ? '#10b981' : '#3b82f6') + '; color:#fff; font-size:11px; padding:3px 8px; border-radius:10px;">' + s.type + '</span></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Father / Guardian</span><strong style="font-size:13px; color:#0f172a;">' + (s.father_name || '-') + '</strong></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Guardian Phone</span><strong style="font-size:13px; color:#0f172a;">' + (s.phone || 'N/A') + '</strong></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Car Service</span><strong style="font-size:13px; color:#0f172a;">' + s.has_car + (s.car_cost > 0 ? ' (₹' + Number(s.car_cost).toLocaleString('en-IN') + '/mo)' : '') + '</strong></div>';
            html += '  <div><span style="display:block; font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Monthly Tuition</span><strong style="font-size:13px; color:#0f172a;">₹' + Number(s.monthly_fee).toLocaleString('en-IN') + '</strong></div>';
            html += '</div>';

            // 4 Key Summary Stat Cards (Side-by-side 4-column cards)
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">';
            
            // Full Package
            html += '  <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px 14px; text-align: center;">';
            html += '    <div style="font-size: 11px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">Full Package</div>';
            html += '    <div style="font-size: 20px; font-weight: 800; color: #1d4ed8; margin-top: 3px;">₹' + Number(s.full_year_gross).toLocaleString('en-IN') + '</div>';
            html += '    <div style="font-size: 10.5px; color: #3b82f6; margin-top: 2px;">Entire Academic Year</div>';
            html += '  </div>';

            // Period Paid
            html += '  <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 14px; text-align: center;">';
            html += '    <div style="font-size: 11px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.5px;">Period Paid</div>';
            html += '    <div style="font-size: 20px; font-weight: 800; color: #15803d; margin-top: 3px;">₹' + Number(periodTotalPaid).toLocaleString('en-IN') + '</div>';
            html += '    <div style="font-size: 10.5px; color: #22c55e; margin-top: 2px;">Total Collected to Date</div>';
            html += '  </div>';

            // Current Due
            html += '  <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; text-align: center;">';
            html += '    <div style="font-size: 11px; font-weight: 700; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px;">Current Due</div>';
            html += '    <div style="font-size: 20px; font-weight: 800; color: #b91c1c; margin-top: 3px;">₹' + Number(s.current_due).toLocaleString('en-IN') + '</div>';
            html += '    <div style="font-size: 10.5px; color: #ef4444; margin-top: 2px;">As of Current Month' + (s.advance_amount > 0 ? ' (Adv: ₹' + Number(s.advance_amount).toLocaleString('en-IN') + ')' : '') + '</div>';
            html += '  </div>';

            // Year-End Due
            html += '  <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 8px; padding: 12px 14px; text-align: center;">';
            html += '    <div style="font-size: 11px; font-weight: 700; color: #6b21a8; text-transform: uppercase; letter-spacing: 0.5px;">Year-End Due</div>';
            html += '    <div style="font-size: 20px; font-weight: 800; color: #7e22ce; margin-top: 3px;">₹' + Number(s.year_end_due).toLocaleString('en-IN') + '</div>';
            html += '    <div style="font-size: 10.5px; color: #a855f7; margin-top: 2px;">Full Session Balance</div>';
            html += '  </div>';

            html += '</div>';

            // Payment Transactions Table
            html += '<div style="margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">';
            html += '  <h5 style="font-weight: 700; font-size: 13.5px; color: #1e293b; margin: 0;"><i class="fa fa-list text-primary"></i> Payment Transactions Breakdown:</h5>';
            html += '  <span style="font-size: 12px; color: #64748b;"><strong>' + filteredPayments.length + '</strong> receipt(s) found</span>';
            html += '</div>';

            html += '<div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 22px;">';
            html += '<table class="table table-bordered table-striped" style="margin-bottom: 0; font-size: 12px; width: 100%; border-collapse: collapse;">';
            html += '  <thead>';
            html += '    <tr style="background: #f1f5f9; color: #334155; font-weight: 600;">';
            html += '      <th style="padding: 9px 10px; width: 40px; text-align: center;">#</th>';
            html += '      <th style="padding: 9px 10px; width: 95px;">Date</th>';
            html += '      <th style="padding: 9px 10px; width: 110px;">Receipt No</th>';
            html += '      <th style="padding: 9px 10px; width: 100px;">Month</th>';
            html += '      <th style="padding: 9px 10px;">Purpose / Description</th>';
            html += '      <th style="padding: 9px 10px; width: 85px; text-align: center;">Mode</th>';
            html += '      <th style="padding: 9px 12px; width: 115px; text-align: right;">Amount Paid</th>';
            html += '    </tr>';
            html += '  </thead>';
            html += '  <tbody>';

            if (filteredPayments.length > 0) {
                for (var j = 0; j < filteredPayments.length; j++) {
                    var row = filteredPayments[j];
                    html += '  <tr>';
                    html += '    <td style="padding: 8px 10px; text-align: center;">' + (j + 1) + '</td>';
                    html += '    <td style="padding: 8px 10px;">' + (row.date ? row.date.split('-').reverse().join('-') : '-') + '</td>';
                    html += '    <td style="padding: 8px 10px;"><code style="font-size: 11.5px; color: #0284c7; background: #e0f2fe; padding: 2px 5px; border-radius: 3px;">' + (row.recep_no || '-') + '</code></td>';
                    html += '    <td style="padding: 8px 10px; font-weight: 500;">' + (row.month || '-') + '</td>';
                    html += '    <td style="padding: 8px 10px;">' + (row.purpose || 'Fee Payment') + '</td>';
                    html += '    <td style="padding: 8px 10px; text-align: center;"><span class="badge" style="background: #e2e8f0; color: #334155; font-size: 10.5px; padding: 2px 6px;">' + (row.mode || 'Cash') + '</span></td>';
                    html += '    <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #15803d; font-size: 12.5px;">₹' + Number(row.paid).toLocaleString('en-IN', { minimumFractionDigits: 2 }) + '</td>';
                    html += '  </tr>';
                }
                html += '  <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1;">';
                html += '    <td colspan="6" style="padding: 10px 12px; text-align: right; font-size: 12.5px; color: #1e293b;">TOTAL PAID FOR THIS PERIOD:</td>';
                html += '    <td style="padding: 10px 12px; text-align: right; color: #15803d; font-size: 13.5px; font-weight: 800;">₹' + Number(periodTotalPaid).toLocaleString('en-IN', { minimumFractionDigits: 2 }) + '</td>';
                html += '  </tr>';
            } else {
                html += '  <tr><td colspan="7" class="text-center text-muted" style="padding: 22px; font-size: 13px;">No payment records found for this period.</td></tr>';
            }

            html += '  </tbody>';
            html += '</table>';
            html += '</div>';

            // Signatures (For printed statement)
            html += '<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 30px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">';
            html += '  <div style="text-align: center; width: 220px;">';
            html += '    <div style="border-bottom: 1px solid #475569; height: 32px; margin-bottom: 6px;"></div>';
            html += '    <small style="color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 10.5px;">Guardian Signature</small>';
            html += '  </div>';
            html += '  <div style="text-align: center; width: 220px;">';
            html += '    <div style="border-bottom: 1px solid #475569; height: 32px; margin-bottom: 6px;"></div>';
            html += '    <small style="color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 10.5px;">Authorized Signatory / Principal</small>';
            html += '  </div>';
            html += '</div>';

            html += '</div>'; // End printable area

            $('#statementModalContent').html(html);
        }

        function printStatementArea() {
            var printContent = document.getElementById('printableStatementArea');
            if (!printContent) return;
            var printWindow = window.open('', '_blank', 'width=900,height=800');
            printWindow.document.write('<!DOCTYPE html><html><head><title>Student Fee Statement</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; padding: 25px; color: #1e293b; background: #fff; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 15px 0; }');
            printWindow.document.write('th, td { border: 1px solid #cbd5e1; padding: 7px 10px; font-size: 12px; }');
            printWindow.document.write('th { background: #f1f5f9; text-align: left; font-weight: 600; }');
            printWindow.document.write('.text-right { text-align: right; }');
            printWindow.document.write('.text-center { text-align: center; }');
            printWindow.document.write('@page { size: auto; margin: 12mm; }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write(printContent.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(function() {
                printWindow.print();
                printWindow.close();
            }, 400);
        }

        // Point 11: Save Daily Snapshot
        function saveDaySnapshot() {
            var rDate = $('#fromDateInput').val() || new Date().toISOString().substr(0, 10);
            var sess = $('select[name="scl_session"]').val() || '<?= $currentYear ?>';

            if (!confirm('Are you sure you want to save the daily money report snapshot for date: ' + rDate + ' (Session: ' + sess + ')?')) {
                return;
            }

            $.ajax({
                url: 'save_daily_snapshot.php',
                type: 'POST',
                data: {
                    action: 'save',
                    report_date: rDate,
                    scl_session: sess,
                    total_students: <?= count($reportRows) ?>,
                    new_admission_collection: <?= $sum_new_adm_collection ?>,
                    readmission_collection: <?= $sum_readm_collection ?>,
                    total_collection: <?= $sum_actual_collection ?>,
                    notes: 'Saved snapshot from Money Report screen for ' + rDate
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        alert(res.message);
                    } else {
                        alert('Error: ' + res.error);
                    }
                },
                error: function() {
                    alert('Server error while saving daily snapshot.');
                }
            });
        }

        function openSnapshotsModal() {
            $('#snapshotsModal').modal('show');
            $('#snapshotsModalContent').html('<div class="text-center py-3"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading snapshots...</div>');

            $.ajax({
                url: 'save_daily_snapshot.php',
                type: 'GET',
                data: {
                    action: 'list'
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.data.length > 0) {
                        var h = '<table class="table table-bordered table-striped text-sm">';
                        h += '<thead><tr><th>Report Date</th><th>Session</th><th>Total Collection</th><th>New Adm.</th><th>Re-Adm.</th><th>Saved By</th><th>Created At</th><th>Action</th></tr></thead><tbody>';
                        for (var i = 0; i < res.data.length; i++) {
                            var it = res.data[i];
                            h += '<tr>';
                            h += '<td><strong>' + it.report_date.split('-').reverse().join('-') + '</strong></td>';
                            h += '<td>' + it.scl_session + '</td>';
                            h += '<td style="color:#27ae60; font-weight:bold;">₹' + Number(it.total_collection).toLocaleString('en-IN') + '</td>';
                            h += '<td>₹' + Number(it.new_admission_collection).toLocaleString('en-IN') + '</td>';
                            h += '<td>₹' + Number(it.readmission_collection).toLocaleString('en-IN') + '</td>';
                            h += '<td>' + it.saved_by + '</td>';
                            h += '<td style="font-size:11px;">' + it.created_at + '</td>';
                            h += '<td><a href="index.php?scl_session=' + it.scl_session + '&fromDate=' + it.report_date + '&endDate=' + it.report_date + '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View Day</a></td>';
                            h += '</tr>';
                        }
                        h += '</tbody></table>';
                        $('#snapshotsModalContent').html(h);
                    } else {
                        $('#snapshotsModalContent').html('<p class="text-muted text-center" style="padding:20px;">No snapshots saved yet. Click "Save Day Report" to record today\'s snapshot.</p>');
                    }
                },
                error: function() {
                    $('#snapshotsModalContent').html('<div class="alert alert-danger">Failed to load saved snapshots.</div>');
                }
            });
        }

        // Export Table to CSV (Exports visible columns, excluding action column)
        function exportToCSV() {
            var csv = [];
            var rows = document.querySelectorAll("#moneyReportTable tr");
            for (var i = 0; i < rows.length; i++) {
                var row = [],
                    cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length - 1; j++) { // exclude action column
                    if (window.getComputedStyle(cols[j]).display === 'none') {
                        continue; // Skip hidden columns
                    }
                    var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
                    row.push('"' + text.trim() + '"');
                }
                if (row.length > 0) {
                    csv.push(row.join(","));
                }
            }
            var csvFile = new Blob([csv.join("\n")], {
                type: "text/csv;charset=utf-8;"
            });
            var downloadLink = document.createElement("a");
            downloadLink.download = "Money_Report_" + ($('#fromDateInput').val() || 'export') + ".csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

        // Download PDF of Money Report
        function downloadMoneyReportPDF() {
            var form = document.getElementById('filterForm');
            var formData = new FormData(form);
            var params = new URLSearchParams(formData).toString();
            window.location.href = 'export_pdf.php?' + params;
        }

        // Print Money Report Ledger (Landscape Printable View with Header & Signatures)
        function printMoneyReport() {
            var printTable = document.getElementById('moneyReportTable');
            if (!printTable) return;

            // Clone table to manipulate without affecting live DOM
            var tableClone = printTable.cloneNode(true);

            // Remove action column (last column) in the clone
            var allRows = tableClone.querySelectorAll('tr');
            for (var r = 0; r < allRows.length; r++) {
                var cells = allRows[r].querySelectorAll('th, td');
                if (cells.length > 0) {
                    cells[cells.length - 1].remove(); // remove Actions column
                }
            }

            var sessVal = $('select[name="scl_session"]').val() || 'All';
            var classVal = $('select[name="scl_class"]').val() || 'All';
            var fromVal = $('#fromDateInput').val() || '';
            var toVal = $('#endDateInput').val() || '';

            var printWin = window.open('', '_blank', 'width=1100,height=800');
            printWin.document.write('<!DOCTYPE html><html><head><title>Money Report - ' + sessVal + '</title>');
            printWin.document.write('<style>');
            printWin.document.write('@page { size: landscape; margin: 8mm; }');
            printWin.document.write('body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; padding: 10px; color: #1e293b; background: #fff; }');
            printWin.document.write('.header { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; margin-bottom: 12px; }');
            printWin.document.write('.header h2 { margin: 0; font-size: 18px; color: #1e3a8a; text-transform: uppercase; font-weight: 800; }');
            printWin.document.write('.header p { margin: 2px 0; font-size: 11px; color: #475569; }');
            printWin.document.write('.meta-bar { font-size: 11.5px; background: #f8fafc; padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 12px; display: flex; justify-content: space-between; }');
            printWin.document.write('table { width: 100%; border-collapse: collapse; font-size: 10px; }');
            printWin.document.write('th, td { border: 1px solid #cbd5e1; padding: 5px 6px; }');
            printWin.document.write('thead th { background: #1e3c72; color: #fff; text-align: left; font-size: 9.5px; }');
            printWin.document.write('tfoot tr { background: #e2e8f0; font-weight: bold; border-top: 2px solid #1e3c72; font-size: 10px; }');
            printWin.document.write('tfoot td { background: #e2e8f0 !important; font-weight: bold; }');
            printWin.document.write('.text-center { text-align: center; }');
            printWin.document.write('.text-right { text-align: right; }');
            printWin.document.write('.badge-advance, .badge-due, .badge-yeardue, .label { display: inline-block; padding: 1px 4px; border-radius: 3px; font-weight: 700; font-size: 9px; }');
            printWin.document.write('.signatures { display: flex; justify-content: space-between; margin-top: 35px; padding-top: 15px; }');
            printWin.document.write('.sig-box { text-align: center; width: 200px; font-size: 11px; border-top: 1px solid #475569; padding-top: 4px; font-weight: 600; }');
            printWin.document.write('</style></head><body>');

            // Header
            printWin.document.write('<div class="header">');
            printWin.document.write('<h2>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2>');
            printWin.document.write('<p>Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012 | Baidyadanga, Rasulpur, Memari, Purba Bardhaman</p>');
            printWin.document.write('<h3 style="margin: 4px 0 0 0; font-size: 13px; color: #0284c7;">STUDENT FEE MASTER LEDGER & MONEY REPORT</h3>');
            printWin.document.write('</div>');

            // Metadata bar
            printWin.document.write('<div class="meta-bar">');
            printWin.document.write('<span><strong>Session:</strong> ' + sessVal + ' | <strong>Class:</strong> ' + classVal + '</span>');
            printWin.document.write('<span><strong>Date Range:</strong> ' + (fromVal || 'Start') + ' to ' + (toVal || 'End') + '</span>');
            printWin.document.write('<span><strong>Printed On:</strong> ' + new Date().toLocaleString() + '</span>');
            printWin.document.write('</div>');

            // Table
            printWin.document.write(tableClone.outerHTML);

            // Signatures
            printWin.document.write('<div class="signatures">');
            printWin.document.write('<div class="sig-box">Prepared By</div>');
            printWin.document.write('<div class="sig-box">Accountant / Cashier</div>');
            printWin.document.write('<div class="sig-box">Principal / Headmaster</div>');
            printWin.document.write('</div>');

            printWin.document.write('</body></html>');
            printWin.document.close();
            printWin.focus();
            setTimeout(function() {
                printWin.print();
                printWin.close();
            }, 500);
        }
    </script>