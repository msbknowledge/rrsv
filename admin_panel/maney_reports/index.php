<?php
include(__DIR__ . '/../../include/config.php');
session_start();
include __DIR__ . '/../dbcon.php';

// Auth check
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['admin', 'teacher'])) {
    header("Location: ../login.php?error=" . urlencode("Please login to access Money Reports"));
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
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
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

if ($students_result) {
    while ($row = mysqli_fetch_assoc($students_result)) {
        $sid = $row['id'];

        $isNew = ($row['add_status'] === 'New Admission' || floatval($row['admission_fee_val']) > 0);
        if ($isNew) $count_new_adm++; else $count_readm++;

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

        // Payments
        $totalPaid = $payments_total[$sid]['total_paid'] ?? 0;
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

include(__DIR__ . '/../header.php');
?>

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
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.table-freeze {
    border-collapse: separate !important;
    border-spacing: 0;
    margin-bottom: 0 !important;
    width: 100%;
}

.table-freeze th, .table-freeze td {
    white-space: nowrap;
    vertical-align: middle !important;
    padding: 7px 10px;
    border-top: none;
    border-right: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
}

/* Sticky Columns */
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
    width: 65px;
    min-width: 65px;
    text-align: center;
}

.col-freeze-4 {
    position: sticky !important;
    left: 220px !important;
    z-index: 10 !important;
    background-color: #ffffff !important;
    width: 180px;
    min-width: 180px;
    box-shadow: 4px 0 6px -2px rgba(0,0,0,0.18) !important;
    font-weight: 600;
}

/* Sticky Headers */
thead th.col-freeze-1, thead th.col-freeze-2, thead th.col-freeze-3, thead th.col-freeze-4 {
    z-index: 25 !important;
    background-color: #ecf0f5 !important;
}

thead th {
    background-color: #ecf0f5 !important;
    font-size: 12px;
    text-transform: uppercase;
    color: #333;
}

/* Hover effect on sticky rows */
.table-freeze tbody tr:hover td {
    background-color: #f7faff !important;
}
.table-freeze tbody tr:hover td.col-freeze-1,
.table-freeze tbody tr:hover td.col-freeze-2,
.table-freeze tbody tr:hover td.col-freeze-3,
.table-freeze tbody tr:hover td.col-freeze-4 {
    background-color: #ebf3ff !important;
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

/* Top stat cards */
.stat-card-custom {
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    color: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}
.stat-card-custom h3 {
    margin: 0 0 5px 0;
    font-weight: 700;
    font-size: 24px;
}
.stat-card-custom p {
    margin: 0;
    font-size: 13px;
    opacity: 0.9;
}
</style>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <?php include __DIR__ . '/../top_navbar.php'; ?>
    </header>
    <?php include __DIR__ . '/../left_navbar.php'; ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <i class="fa fa-money text-green"></i> Money Report &amp; Fee Management
                <small>Session: <?= htmlspecialchars($scl_session) ?> | Due Benchmark: <?= $months[$upToMonth] ?></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?= BASE_URL ?>admin_panel"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Money Report</li>
            </ol>
        </section>

        <section class="content">

            <!-- ==============================================================
                 POINT 3: TOP SUMMARY STAT CARDS (New vs Re-Admission Separately)
                 ============================================================== -->
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="stat-card-custom bg-green">
                        <h3>₹<?= number_format($sum_actual_collection, 2) ?></h3>
                        <p><i class="fa fa-check-circle"></i> Total Actual Collection (Year)</p>
                        <small style="font-size:11px; opacity:0.85;">Selected Range: ₹<?= number_format($sum_range_collection, 2) ?></small>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6">
                    <div class="stat-card-custom bg-aqua">
                        <h3>₹<?= number_format($sum_new_adm_collection, 2) ?></h3>
                        <p><i class="fa fa-user-plus"></i> New Admission (<?= $count_new_adm ?>)</p>
                        <small style="font-size:11px; opacity:0.85;">Full Package Collection</small>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6">
                    <div class="stat-card-custom bg-blue">
                        <h3>₹<?= number_format($sum_readm_collection, 2) ?></h3>
                        <p><i class="fa fa-refresh"></i> Re-Admission (<?= $count_readm ?>)</p>
                        <small style="font-size:11px; opacity:0.85;">Tuition + Car + Items</small>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6">
                    <div class="stat-card-custom bg-yellow" style="color: #fff !important;">
                        <h3 style="color:#fff;">₹<?= number_format($sum_current_due, 2) ?></h3>
                        <p><i class="fa fa-clock-o"></i> End-of-Month Due (<?= $months[$upToMonth] ?>)</p>
                        <small style="font-size:11px; opacity:0.9;">Advance: ₹<?= number_format($sum_advance_amount, 2) ?></small>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="stat-card-custom bg-purple">
                        <h3>₹<?= number_format($sum_year_end_due, 2) ?></h3>
                        <p><i class="fa fa-calendar-check-o"></i> Total Year-End Due</p>
                        <small style="font-size:11px; opacity:0.85;">Total Students: <?= count($reportRows) ?></small>
                    </div>
                </div>
            </div>

            <!-- ==============================================================
                 POINT 8 & 11: ADVANCED SEARCH & DROPDOWN FILTER BAR
                 ============================================================== -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Search, Session &amp; Date-wise Filters</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form method="get" action="" id="filterForm">
                        <div class="row">
                            <!-- Session Dropdown -->
                            <div class="col-md-2 col-sm-4">
                                <div class="form-group">
                                    <label><i class="fa fa-calendar"></i> Session</label>
                                    <select name="scl_session" class="form-control input-sm" onchange="document.getElementById('filterForm').submit()">
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
                            <div class="col-md-2 col-sm-4">
                                <div class="form-group">
                                    <label><i class="fa fa-book"></i> Class</label>
                                    <select name="scl_class" class="form-control input-sm">
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
                            <div class="col-md-2 col-sm-4">
                                <div class="form-group">
                                    <label><i class="fa fa-tags"></i> Admission Type</label>
                                    <select name="add_status" class="form-control input-sm">
                                        <option value="All" <?= ($add_status === 'All') ? 'selected' : '' ?>>All (New + Re)</option>
                                        <option value="New Admission" <?= ($add_status === 'New Admission') ? 'selected' : '' ?>>1. New Admission</option>
                                        <option value="Readmission" <?= ($add_status === 'Readmission') ? 'selected' : '' ?>>2. Re-Admission</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Point 5: Auto-generated End of Month Due Benchmark Dropdown -->
                            <div class="col-md-2 col-sm-4">
                                <div class="form-group">
                                    <label><i class="fa fa-hourglass-half"></i> Due Up To Month</label>
                                    <select name="upToMonth" class="form-control input-sm">
                                        <?php foreach ($months as $num => $mname): ?>
                                            <option value="<?= $num ?>" <?= ($upToMonth == $num) ? 'selected' : '' ?>>
                                                <?= $num ?>. End of <?= $mname ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Date Range: From Date -->
                            <div class="col-md-2 col-sm-4">
                                <div class="form-group">
                                    <label><i class="fa fa-calendar-check-o"></i> From Date</label>
                                    <input type="date" name="fromDate" id="fromDateInput" class="form-control input-sm" value="<?= htmlspecialchars($fromDate) ?>">
                                </div>
                            </div>

                            <!-- Date Range: To Date -->
                            <div class="col-md-2 col-sm-4">
                                <div class="form-group">
                                    <label><i class="fa fa-calendar-check-o"></i> To Date</label>
                                    <input type="date" name="endDate" id="endDateInput" class="form-control input-sm" value="<?= htmlspecialchars($endDate) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Student Search Text / Datalist Dropdown -->
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label><i class="fa fa-search"></i> Search Student (Name / Reg No / Phone)</label>
                                    <input type="text" name="search_text" class="form-control input-sm" placeholder="Type Name or Reg. No..." value="<?= htmlspecialchars($search_text) ?>">
                                </div>
                            </div>

                            <!-- Quick Preset Date Buttons (Point 11: Day-wise quick filter) -->
                            <div class="col-md-5 col-sm-6" style="padding-top: 24px;">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm" onclick="setQuickDate('today')">Today</button>
                                    <button type="button" class="btn btn-default btn-sm" onclick="setQuickDate('yesterday')">Yesterday</button>
                                    <button type="button" class="btn btn-default btn-sm" onclick="setQuickDate('this_month')">This Month</button>
                                    <button type="button" class="btn btn-default btn-sm" onclick="setQuickDate('year')">Full Year</button>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
                                <a href="index.php" class="btn btn-default btn-sm">Reset</a>
                            </div>

                            <!-- Point 11: Day-Wise Save Snapshot & Export Options -->
                            <div class="col-md-3 col-sm-12 text-right" style="padding-top: 24px;">
                                <button type="button" class="btn btn-success btn-sm" onclick="saveDaySnapshot()" title="Save today's collection snapshot into database">
                                    <i class="fa fa-save"></i> Save Day Report
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="openSnapshotsModal()" title="View previously saved daily snapshots">
                                    <i class="fa fa-history"></i> Saved Reports
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="window.print()" title="Print complete report">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ==============================================================
                 COLUMN TOGGLE CHECKBOXES
                 ============================================================== -->
            <div class="box box-solid collapsed-box mb-2" style="margin-bottom: 10px;">
                <div class="box-header with-border" style="padding: 6px 15px;">
                    <h5 class="box-title" style="font-size: 13px; cursor: pointer;" data-widget="collapse">
                        <i class="fa fa-columns"></i> <strong>Show / Hide Columns (Click to Toggle)</strong>
                    </h5>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="display: none; padding: 10px 15px;">
                    <div class="row">
                        <?php
                        $colsList = [
                            5 => "Admission Fee", 6 => "Re-Admission Fee", 7 => "Monthly Tuition", 8 => "Yearly Tuition",
                            9 => "Car Opted", 10 => "Monthly Car", 11 => "Yearly Car", 12 => "Uniform", 13 => "Shoes",
                            14 => "Winter", 15 => "Form", 16 => "Bag", 17 => "I.Card", 18 => "Diary", 19 => "Books",
                            20 => "Copy", 21 => "Total Items", 22 => "Gross (No Car)", 23 => "Gross (With Car)",
                            24 => "Total Package Gross", 25 => "Range Paid", 26 => "Total Paid", 27 => "Advance",
                            28 => "Current Due", 29 => "Year-End Due", 30 => "Actions"
                        ];
                        foreach ($colsList as $idx => $cname):
                        ?>
                            <div class="col-md-2 col-sm-3 col-xs-6" style="font-size: 12px; margin-bottom: 5px;">
                                <label style="font-weight: normal; cursor: pointer;">
                                    <input type="checkbox" class="col-toggle-cb" data-target="<?= $idx ?>" checked> <?= $cname ?>
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
                    <div class="box-tools pull-right">
                        <button class="btn btn-default btn-xs" onclick="exportToCSV()"><i class="fa fa-file-excel-o text-green"></i> Export CSV</button>
                    </div>
                </div>
                <div class="box-body" style="padding: 0;">

                    <div class="table-freeze-container">
                        <table id="moneyReportTable" class="table table-bordered table-striped table-hover table-freeze text-sm">
                            <thead>
                                <tr>
                                    <!-- POINT 9: Frozen Columns on Left -->
                                    <th class="col-freeze-1">Sl.</th>
                                    <th class="col-freeze-2">Reg. No</th>
                                    <th class="col-freeze-3">Roll No</th>
                                    <th class="col-freeze-4">Student Name</th>
                                    
                                    <!-- Scrollable Columns -->
                                    <th class="col-item col-4">Class</th>
                                    <th class="col-item col-5" style="background:#eaf2f8 !important;">Admission Fee</th>
                                    <th class="col-item col-6" style="background:#eaf2f8 !important;">Re-Adm Fee</th>
                                    <th class="col-item col-7">Tuition/Mo</th>
                                    <th class="col-item col-8">Tuition/Yr</th>
                                    <th class="col-item col-9">Car?</th>
                                    <th class="col-item col-10">Car Rate/Mo</th>
                                    <th class="col-item col-11">Car Cost/Yr</th>
                                    <th class="col-item col-12">Uniform</th>
                                    <th class="col-item col-13">Shoes</th>
                                    <th class="col-item col-14">Winter</th>
                                    <th class="col-item col-15">Adm Form</th>
                                    <th class="col-item col-16">Bag</th>
                                    <th class="col-item col-17">I.Card</th>
                                    <th class="col-item col-18">Diary</th>
                                    <th class="col-item col-19">Books</th>
                                    <th class="col-item col-20">Copy</th>
                                    <th class="col-item col-21">Total Items</th>
                                    <th class="col-item col-22">Gross (w/o Car)</th>
                                    <th class="col-item col-23">Gross (with Car)</th>
                                    <th class="col-item col-24" style="background:#fef9e7 !important; font-weight:700;">Full Package Gross</th>
                                    <th class="col-item col-25">Range Paid</th>
                                    <th class="col-item col-26" style="background:#e8f8f5 !important; font-weight:700;">Total Paid (Yr)</th>
                                    <!-- POINT 6: Advance + Due after payment -->
                                    <th class="col-item col-27" style="background:#eafaf1 !important;">Advance</th>
                                    <!-- POINT 5: Auto generated end of month due -->
                                    <th class="col-item col-28" style="background:#fdedec !important;">Due (<?= $months[$upToMonth] ?>)</th>
                                    <!-- POINT 7: End of year due -->
                                    <th class="col-item col-29" style="background:#f4ecf7 !important;">Year-End Due</th>
                                    <!-- POINT 5 & 10: Guardian Send & Statement Print -->
                                    <th class="col-item col-30 text-center" style="min-width: 170px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reportRows)): ?>
                                    <?php $sl = 1; foreach ($reportRows as $row): ?>
                                        <tr>
                                            <!-- Sticky Columns -->
                                            <td class="col-freeze-1"><?= $sl++ ?></td>
                                            <td class="col-freeze-2"><code><?= htmlspecialchars($row['reg_no']) ?></code></td>
                                            <td class="col-freeze-3"><?= htmlspecialchars($row['roll_no'] ?: '-') ?></td>
                                            <td class="col-freeze-4" title="<?= htmlspecialchars($row['name']) ?>">
                                                <?= htmlspecialchars($row['name']) ?>
                                                <br>
                                                <small class="text-muted" style="font-size:10px;">
                                                    <span class="label <?= $row['is_new'] ? 'label-success' : 'label-primary' ?>" style="font-size:9px; padding:1px 4px;">
                                                        <?= $row['status_label'] ?>
                                                    </span>
                                                </small>
                                            </td>

                                            <!-- Scrollable Data Cells -->
                                            <td class="col-item col-4"><?= htmlspecialchars($row['class']) ?></td>
                                            <td class="col-item col-5"><?= ($row['admission_fee'] > 0) ? '₹' . number_format($row['admission_fee']) : '-' ?></td>
                                            <td class="col-item col-6"><?= ($row['readmission_fee'] > 0) ? '₹' . number_format($row['readmission_fee']) : '-' ?></td>
                                            <td class="col-item col-7">₹<?= number_format($row['monthly_fee']) ?></td>
                                            <td class="col-item col-8">₹<?= number_format($row['yearly_tuition']) ?></td>
                                            <td class="col-item col-9"><?= $row['has_car'] ? '<span class="label label-warning">Yes</span>' : '<span class="label label-default">No</span>' ?></td>
                                            <td class="col-item col-10"><?= ($row['car_monthly'] > 0) ? '₹' . number_format($row['car_monthly']) : '-' ?></td>
                                            <td class="col-item col-11"><?= ($row['yearly_car'] > 0) ? '₹' . number_format($row['yearly_car']) : '-' ?></td>
                                            <td class="col-item col-12"><?= ($row['uniform'] > 0) ? '₹' . number_format($row['uniform']) : '-' ?></td>
                                            <td class="col-item col-13"><?= ($row['shoes'] > 0) ? '₹' . number_format($row['shoes']) : '-' ?></td>
                                            <td class="col-item col-14"><?= ($row['winter'] > 0) ? '₹' . number_format($row['winter']) : '-' ?></td>
                                            <td class="col-item col-15"><?= ($row['adm_form'] > 0) ? '₹' . number_format($row['adm_form']) : '-' ?></td>
                                            <td class="col-item col-16"><?= ($row['bag'] > 0) ? '₹' . number_format($row['bag']) : '-' ?></td>
                                            <td class="col-item col-17"><?= ($row['icard'] > 0) ? '₹' . number_format($row['icard']) : '-' ?></td>
                                            <td class="col-item col-18"><?= ($row['diary'] > 0) ? '₹' . number_format($row['diary']) : '-' ?></td>
                                            <td class="col-item col-19"><?= ($row['books'] > 0) ? '₹' . number_format($row['books']) : '-' ?></td>
                                            <td class="col-item col-20"><?= ($row['copy'] > 0) ? '₹' . number_format($row['copy']) : '-' ?></td>
                                            <td class="col-item col-21">₹<?= number_format($row['total_items']) ?></td>
                                            <td class="col-item col-22">₹<?= number_format($row['gross_without_car']) ?></td>
                                            <td class="col-item col-23">₹<?= number_format($row['gross_with_car']) ?></td>
                                            <!-- POINT 1 & 4: Full Package Gross -->
                                            <td class="col-item col-24" style="font-weight:700; color:#b7950b;">₹<?= number_format($row['total_gross']) ?></td>
                                            <td class="col-item col-25">₹<?= number_format($row['range_paid']) ?></td>
                                            <!-- Total Paid -->
                                            <td class="col-item col-26" style="font-weight:700; color:#117a65;">₹<?= number_format($row['total_paid']) ?></td>
                                            <!-- POINT 6: Advance Amount -->
                                            <td class="col-item col-27">
                                                <?php if ($row['advance'] > 0): ?>
                                                    <span class="badge-advance">+₹<?= number_format($row['advance']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">₹0</span>
                                                <?php endif; ?>
                                            </td>
                                            <!-- POINT 5 & 6: Current Due End of Month -->
                                            <td class="col-item col-28">
                                                <?php if ($row['current_due'] > 0): ?>
                                                    <span class="badge-due">₹<?= number_format($row['current_due']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-green"><i class="fa fa-check"></i> Cleared</span>
                                                <?php endif; ?>
                                            </td>
                                            <!-- POINT 7: End of Year Due -->
                                            <td class="col-item col-29">
                                                <?php if ($row['year_end_due'] > 0): ?>
                                                    <span class="badge-yeardue">₹<?= number_format($row['year_end_due']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-green"><i class="fa fa-check-circle"></i> Paid</span>
                                                <?php endif; ?>
                                            </td>
                                            <!-- POINT 5 & 10: Guardian Notice & Statement Print -->
                                            <td class="col-item col-30 text-center">
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
                                        <td colspan="31" class="text-center py-4 text-muted" style="padding: 30px;">
                                            <i class="fa fa-info-circle fa-2x text-blue"></i>
                                            <p class="mt-2" style="font-size:15px; margin-top:10px;">No student records found for the selected session and filter criteria.</p>
                                            <a href="index.php?scl_session=<?= $scl_session ?>" class="btn btn-primary btn-sm">Reset Date Filter to Full Session</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </section>
    </div>

    <?php include __DIR__ . '/../footer.php'; ?>
</div>

<!-- ==============================================================
     POINT 10: 6-MONTH & MONTHLY PAYMENT STATEMENT MODAL
     ============================================================== -->
<div class="modal fade" id="statementModal" tabindex="-1" role="dialog" aria-labelledby="statementModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 85%; max-width: 950px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="color: #fff; background-color: #3c8dbc;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="statementModalLabel"><i class="fa fa-print"></i> Student Monthly Fee Statement &amp; Report</h4>
            </div>
            <div class="modal-body" id="statementModalContent">
                <div class="text-center py-4" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-2" style="margin-top:10px;">Loading student payment history...</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <div class="btn-group" id="periodButtonGroup">
                        <button type="button" class="btn btn-default btn-sm active" onclick="filterStatementPeriod('all')">Full Year</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="filterStatementPeriod('h1')">1st 6 Months (Jan - Jun)</button>
                        <button type="button" class="btn btn-default btn-sm" onclick="filterStatementPeriod('h2')">2nd 6 Months (Jul - Dec)</button>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="modalWhatsAppBtn" target="_blank"><i class="fa fa-whatsapp"></i> Send WhatsApp Notice</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="printStatementArea()"><i class="fa fa-print"></i> Print Statement</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     POINT 11: SAVED DAILY SNAPSHOTS MODAL
     ============================================================== -->
<div class="modal fade" id="snapshotsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-aqua" style="color: #fff; background-color: #00c0ef;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-history"></i> Saved Daily Money Report Snapshots</h4>
            </div>
            <div class="modal-body" id="snapshotsModalContent">
                <div class="text-center py-3"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     PRINTABLE STYLES FOR STATEMENT
     ============================================================== -->
<style media="print">
    body * { visibility: hidden; }
    #printableStatementArea, #printableStatementArea * { visibility: visible; }
    #printableStatementArea { position: absolute; left: 0; top: 0; width: 100%; }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<script>
// Column Toggle Logic
$('.col-toggle-cb').on('change', function(){
    var targetIdx = $(this).data('target');
    if ($(this).is(':checked')) {
        $('.col-' + targetIdx).show();
    } else {
        $('.col-' + targetIdx).hide();
    }
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
        data: { student_id: studentId },
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

    var html = '<div id="printableStatementArea">';
    html += '<div class="text-center" style="border-bottom: 2px solid #3c8dbc; padding-bottom: 10px; margin-bottom: 15px;">';
    html += '<h3 style="margin: 0; font-weight: bold; color: #2c3e50;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h3>';
    html += '<p style="margin: 3px 0; font-size: 11px;">Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012</p>';
    html += '<p style="margin: 0; font-size: 11px;">Baidyadanga, Rasulpur, Memari, Purba Bardhaman, Pin - 713151</p>';
    html += '<h4 style="margin: 10px 0 0 0; text-transform: uppercase; font-size: 14px; font-weight: bold; color: #3c8dbc;">Fee Statement (' + (period === 'h1' ? '1st 6 Months: Jan - Jun' : (period === 'h2' ? '2nd 6 Months: Jul - Dec' : 'Full Session ' + s.session)) + ')</h4>';
    html += '</div>';

    // Student info grid
    html += '<div class="row" style="margin-bottom: 15px; font-size: 13px; background: #f9f9f9; padding: 10px 5px; border-radius: 4px;">';
    html += '<div class="col-xs-6 col-sm-3"><strong>Name:</strong> ' + s.name + '</div>';
    html += '<div class="col-xs-6 col-sm-3"><strong>Reg. No:</strong> ' + s.reg_no + '</div>';
    html += '<div class="col-xs-6 col-sm-3"><strong>Class / Roll:</strong> ' + s.class + ' (Roll: ' + (s.roll_no || '-') + ')</div>';
    html += '<div class="col-xs-6 col-sm-3"><strong>Category:</strong> ' + s.type + '</div>';
    html += '<div class="col-xs-6 col-sm-3" style="margin-top:5px;"><strong>Father:</strong> ' + (s.father_name || '-') + '</div>';
    html += '<div class="col-xs-6 col-sm-3" style="margin-top:5px;"><strong>Guardian Ph:</strong> ' + (s.phone || 'N/A') + '</div>';
    html += '<div class="col-xs-6 col-sm-3" style="margin-top:5px;"><strong>Car Service:</strong> ' + s.has_car + (s.car_cost > 0 ? ' (Rs.' + s.car_cost + '/mo)' : '') + '</div>';
    html += '<div class="col-xs-6 col-sm-3" style="margin-top:5px;"><strong>Monthly Tuition:</strong> Rs. ' + s.monthly_fee + '</div>';
    html += '</div>';

    // Fee breakdown cards
    html += '<div class="row" style="margin-bottom: 15px;">';
    html += '<div class="col-xs-3 text-center"><div style="background:#eaf2f8; padding:8px; border-radius:4px;"><strong style="font-size:11px;">FULL PACKAGE</strong><br><span style="font-size:15px; font-weight:bold; color:#2980b9;">₹' + Number(s.full_year_gross).toLocaleString('en-IN') + '</span></div></div>';
    html += '<div class="col-xs-3 text-center"><div style="background:#e8f8f5; padding:8px; border-radius:4px;"><strong style="font-size:11px;">PERIOD PAID</strong><br><span style="font-size:15px; font-weight:bold; color:#27ae60;">₹' + Number(periodTotalPaid).toLocaleString('en-IN') + '</span></div></div>';
    html += '<div class="col-xs-3 text-center"><div style="background:#fdedec; padding:8px; border-radius:4px;"><strong style="font-size:11px;">CURRENT DUE</strong><br><span style="font-size:15px; font-weight:bold; color:#c0392b;">₹' + Number(s.current_due).toLocaleString('en-IN') + '</span></div></div>';
    html += '<div class="col-xs-3 text-center"><div style="background:#f4ecf7; padding:8px; border-radius:4px;"><strong style="font-size:11px;">YEAR-END DUE</strong><br><span style="font-size:15px; font-weight:bold; color:#8e44ad;">₹' + Number(s.year_end_due).toLocaleString('en-IN') + '</span></div></div>';
    html += '</div>';

    // Payment table
    html += '<h5 style="font-weight:bold; margin-bottom:8px;"><i class="fa fa-list"></i> Payment Transactions Breakdown:</h5>';
    html += '<table class="table table-bordered table-striped table-condensed text-sm" style="font-size:12px;">';
    html += '<thead><tr style="background:#eaeded;"><th>#</th><th>Date</th><th>Receipt No</th><th>Month</th><th>Purpose / Description</th><th>Mode</th><th class="text-right">Amount Paid</th></tr></thead>';
    html += '<tbody>';

    if (filteredPayments.length > 0) {
        for (var j = 0; j < filteredPayments.length; j++) {
            var row = filteredPayments[j];
            html += '<tr>';
            html += '<td>' + (j + 1) + '</td>';
            html += '<td>' + (row.date ? row.date.split('-').reverse().join('-') : '-') + '</td>';
            html += '<td><code>' + (row.recep_no || '-') + '</code></td>';
            html += '<td>' + (row.month || '-') + '</td>';
            html += '<td>' + (row.purpose || 'Fee Payment') + '</td>';
            html += '<td>' + (row.mode || 'Cash') + '</td>';
            html += '<td class="text-right font-weight-bold" style="font-weight:bold; color:#1e8449;">₹' + Number(row.paid).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>';
            html += '</tr>';
        }
        html += '<tr style="background:#f2f4f4; font-weight:bold;">';
        html += '<td colspan="6" class="text-right">TOTAL PAID FOR THIS PERIOD:</td>';
        html += '<td class="text-right" style="color:#1e8449; font-size:13px;">₹' + Number(periodTotalPaid).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>';
        html += '</tr>';
    } else {
        html += '<tr><td colspan="7" class="text-center text-muted" style="padding:15px;">No payment records found for this period.</td></tr>';
    }

    html += '</tbody></table>';

    // Signatures for printable statement
    html += '<div class="row" style="margin-top: 35px; padding-top: 20px; border-top: 1px dashed #ccc;">';
    html += '<div class="col-xs-6 text-center"><p style="margin-bottom:0;">___________________________</p><small>Guardian Signature</small></div>';
    html += '<div class="col-xs-6 text-center"><p style="margin-bottom:0;">___________________________</p><small>Authorized Signatory / Principal</small></div>';
    html += '</div>';

    html += '</div>'; // End printable area

    $('#statementModalContent').html(html);
}

function printStatementArea() {
    var printContent = document.getElementById('printableStatementArea').innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
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
        data: { action: 'list' },
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

// Export Table to CSV
function exportToCSV() {
    var csv = [];
    var rows = document.querySelectorAll("#moneyReportTable tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length - 1; j++) { // exclude action column
            var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
            row.push('"' + text.trim() + '"');
        }
        csv.push(row.join(","));
    }
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = "Money_Report_" + ($('#fromDateInput').val() || 'export') + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

</body>
</html>
