<?php
include('../../include/dbcon.php');

// Get POST values from AJAX
$filterSession = isset($_POST['scl_session']) ? $_POST['scl_session'] : '';
$filterClass = isset($_POST['scl_class']) ? $_POST['scl_class'] : '';
$feeType = isset($_POST['fee_type']) ? $_POST['fee_type'] : 'admission'; // admission or readmission

// Build WHERE clause
$where = [];
if ($filterSession != '') {
  $where[] = "scl_session='" . mysqli_real_escape_string($myDB, $filterSession) . "'";
}
if ($filterClass != '') {
  $where[] = "scl_class='" . mysqli_real_escape_string($myDB, $filterClass) . "'";
}
$whereSQL = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

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

// Query the table
$sql = "SELECT * FROM rrsv_fee $whereSQL ORDER BY id DESC";
$res = mysqli_query($myDB, $sql);

// Generate the table
if ($res && mysqli_num_rows($res) > 0) {
  echo '<table class="table table-bordered table-striped text-center">';
  echo '<thead class="bg-primary text-white">';
  echo '<tr>
            <th>SL.No</th>
            <th>Class Name</th>
            <th>Session Name</th>
            <th>Monthly Fee</th>';

  // Conditional column header
  if ($feeType === 'admission') {
    echo '<th>Admission Fee</th>';
  } else {
    echo '<th>Readmission Fee</th>';
  }

  echo '<th>Other Charges</th>
   <th>Total Payable (₹)</th>
  </tr></thead><tbody>';

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
    $totalPayable = $monthlyFee + $admissionFee + $adForm + $bookCharge + $copyCharge +$uniform + $diary + $icard + $bag + $sweater + $shoes;


    echo '<tr>';
    echo '<td>' . $sl++ . '</td>';
    echo '<td>' . htmlspecialchars($fee['scl_class']) . '</td>';
    echo '<td>' . htmlspecialchars($fee['scl_session']) . '</td>';
    echo '<td>' . $fee['monthly_fee'] . '</td>';

    // Conditional column value
    if ($feeType === 'admission') {
      echo '<td>' . $fee['monthly_admission_fee'] . '</td>';
    } else {
      echo '<td>' . $fee['readmission_fee'] . '</td>';
    }

    echo '<td class="text-left">
                <b>Admission Form:</b> ' . $fee['ad_form_charge'] . '<br>
                <b>Books:</b> ' . $bookCharge . '<br>
                <b>Copies:</b> ' . $copyCharge . '<br>
                <b>Uniform:</b> ' . $fee['uniform'] . '<br>
                <b>Diary:</b> ' . $fee['diary'] . '<br>
                <b>I-Card:</b> ' . $fee['icard'] . '<br>
                <b>Bag:</b> ' . $fee['bag'] . '<br>
                <b>Sweater:</b> ' . $fee['sweater'] . '<br>
                <b>Shoes & Socks:</b> ' . $fee['shoes'] . '
              </td>';
    echo '<td><b class="text-success">₹ ' . number_format($totalPayable, 2) . '</b></td>';
    echo '</tr>';
  }

  echo '</tbody></table><button type="button" id="downloadPdfBtn" class="btn btn-danger w-100">Download PDF</button>
';
} else {
  echo '<div class="alert alert-warning">No admission charges found</div>';
}
