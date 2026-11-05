<?php
session_start();
include __DIR__ . '/../dbcon.php';

header('Content-Type: application/json; charset=utf-8');

$student_id = intval($_GET['student_id'] ?? ($_POST['student_id'] ?? 0));
$period = $_GET['period'] ?? ($_POST['period'] ?? 'all'); // 'all', 'h1' (Jan-Jun), 'h2' (Jul-Dec)

if ($student_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid student ID']);
    exit;
}

$st_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE id = $student_id LIMIT 1");
if (!$st_q || mysqli_num_rows($st_q) === 0) {
    echo json_encode(['success' => false, 'error' => 'Student not found']);
    exit;
}

$student = mysqli_fetch_assoc($st_q);

// Fee components
$isNew = ($student['add_status'] === 'New Admission' || floatval($student['admission_fee_val']) > 0);
$admFee = $isNew ? floatval($student['admission_fee_val']) : 0;
$readmFee = !$isNew ? floatval($student['re_admission_fee_val']) : 0;
$monthlyFee = floatval($student['monthly_fee']);
$yearlyTuition = $monthlyFee * 12;
$carCost = floatval($student['car_cost']);
$yearlyCar = ($student['scl_car'] === 'Yes' || $carCost > 0) ? ($carCost * 12) : 0;

$uniform = floatval($student['uniformval']);
$shoes = floatval($student['shoessockesval']);
$winter = floatval($student['sweaterslaxcap_val']);
$bag = floatval($student['bagval']);
$books = floatval($student['bookcost']);
$copy = floatval($student['copycost']);
$diary = floatval($student['diaryval']);
$icard = floatval($student['icardval']);
$admForm = floatval($student['admissionval']);

$totalItems = $uniform + $shoes + $winter + $bag + $books + $copy + $diary + $icard + $admForm;
$fullYearGross = ($isNew ? $admFee : $readmFee) + $yearlyTuition + $yearlyCar + $totalItems;

// Fetch payments
$pay_q = mysqli_query($con, "
    SELECT * FROM rrsv_scl_studen_fee 
    WHERE pay_id = $student_id 
    ORDER BY scl_date ASC, id ASC
");

$payments = [];
$totalPaid = 0;
$h1Paid = 0;
$h2Paid = 0;

// Note: admissionval was already paid when admission was taken
if ($admForm > 0) {
    $totalPaid += $admForm;
    $h1Paid += $admForm;
    $admDate = (!empty($student['scl_date']) && $student['scl_date'] !== '0000-00-00') ? $student['scl_date'] : ($student['scl_session'] . '-01-01');
    $payments[] = [
        'id' => 'adm_form_' . $student_id,
        'recep_no' => 'ADM-FORM',
        'date' => $admDate,
        'month' => 'Admission',
        'purpose' => 'Admission Form Fee (Paid at Admission)',
        'paid' => $admForm,
        'mode' => 'Cash',
        'monthly_fee' => 0,
        'car_fee' => 0,
        'advance_due' => 0,
        'val_ad_du' => 'Paid'
    ];
}

$h1Months = ['January', 'February', 'March', 'April', 'May', 'June'];
$h2Months = ['July', 'August', 'September', 'October', 'November', 'December'];

while ($p = mysqli_fetch_assoc($pay_q)) {
    $amt = floatval($p['payment_recive']);
    $totalPaid += $amt;

    $pMonth = trim($p['scl_month']);
    if (in_array($pMonth, $h1Months)) {
        $h1Paid += $amt;
    } elseif (in_array($pMonth, $h2Months)) {
        $h2Paid += $amt;
    } else {
        // Fallback by scl_date month
        if (!empty($p['scl_date']) && $p['scl_date'] !== '0000-00-00') {
            $mNum = intval(date('n', strtotime($p['scl_date'])));
            if ($mNum <= 6) $h1Paid += $amt; else $h2Paid += $amt;
        }
    }

    $payments[] = [
        'id' => $p['id'],
        'recep_no' => $p['recep_no'],
        'date' => $p['scl_date'],
        'month' => $p['scl_month'],
        'purpose' => $p['purpose'] ?: 'Fee Payment',
        'paid' => $amt,
        'mode' => $p['usingtype'] ?: 'Cash',
        'monthly_fee' => floatval($p['monthly_fee_val']),
        'car_fee' => floatval($p['car_fee']),
        'advance_due' => floatval($p['advance_due']),
        'val_ad_du' => $p['val_ad_du']
    ];
}

// Current month calculation
$currentMonthNum = intval(date('n'));
$expectedUpToNow = ($isNew ? $admFee : $readmFee) + $totalItems + ($currentMonthNum * $monthlyFee) + ($currentMonthNum * $carCost);

$currentDue = max(0, $expectedUpToNow - $totalPaid);
$advanceAmount = max(0, $totalPaid - $expectedUpToNow);
$yearEndDue = max(0, $fullYearGross - $totalPaid);

// Guardian contact
$phone = $student['scl_phone_no'] ?: ($student['father_phone_no'] ?: '');
$cleanPhone = preg_replace('/[^0-9]/', '', $phone);
if (strlen($cleanPhone) === 10) {
    $cleanPhone = '91' . $cleanPhone;
}

$monthName = date('F');
$waMessage = "Dear Guardian,\nFee Statement for " . $student['scl_name'] . " (Reg: " . $student['scl_reg_no'] . ", Class: " . $student['scl_class'] . "):\n"
    . "- Full Year Package: Rs. " . number_format($fullYearGross, 2) . "\n"
    . "- Total Paid to date: Rs. " . number_format($totalPaid, 2) . "\n"
    . "- Current Due (as of " . $monthName . "): Rs. " . number_format($currentDue, 2) . "\n"
    . ($advanceAmount > 0 ? "- Advance Balance: Rs. " . number_format($advanceAmount, 2) . "\n" : "")
    . "- Year-End Balance: Rs. " . number_format($yearEndDue, 2) . "\n"
    . "Rasulpur Ramkrishna Sarada Vidyapith. Ph: 7699364592";

echo json_encode([
    'success' => true,
    'student' => [
        'id' => $student['id'],
        'reg_no' => $student['scl_reg_no'],
        'roll_no' => $student['scl_roll_no'],
        'name' => $student['scl_name'],
        'class' => $student['scl_class'],
        'section' => $student['scl_section'] ?? '',
        'session' => $student['scl_session'],
        'father_name' => $student['scl_father_name'],
        'phone' => $phone,
        'clean_phone' => $cleanPhone,
        'type' => $isNew ? 'New Admission' : 'Re-Admission',
        'has_car' => ($student['scl_car'] === 'Yes' || $carCost > 0) ? 'Yes' : 'No',
        'monthly_fee' => $monthlyFee,
        'car_cost' => $carCost,
        'full_year_gross' => $fullYearGross,
        'total_paid' => $totalPaid,
        'h1_paid' => $h1Paid,
        'h2_paid' => $h2Paid,
        'current_due' => $currentDue,
        'advance_amount' => $advanceAmount,
        'year_end_due' => $yearEndDue,
        'components' => [
            'admission_fee' => $admFee,
            'readmission_fee' => $readmFee,
            'yearly_tuition' => $yearlyTuition,
            'yearly_car' => $yearlyCar,
            'uniform' => $uniform,
            'shoes' => $shoes,
            'winter' => $winter,
            'bag' => $bag,
            'books' => $books,
            'copy' => $copy,
            'diary' => $diary,
            'icard' => $icard,
            'adm_form' => $admForm
        ]
    ],
    'payments' => $payments,
    'whatsapp_message' => $waMessage,
    'whatsapp_url' => $cleanPhone ? "https://api.whatsapp.com/send?phone=" . $cleanPhone . "&text=" . urlencode($waMessage) : ""
]);
