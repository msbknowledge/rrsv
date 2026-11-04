<?php
include('../../include/dbcon.php');

// Get session from POST
$scl_session = isset($_POST['scl_session']) ? mysqli_real_escape_string($myDB, $_POST['scl_session']) : date("Y");

$where = "1"; // default
if (!empty($scl_session)) {
  $where .= " AND YEAR(d_o_h)  =". intval($scl_session);
}
// Run the query
$sql = "SELECT * FROM rrsv_holiday WHERE $where ORDER BY d_o_h ASC";
$result = mysqli_query($myDB, $sql);

// ✅ Debug check
if (!$result) {
  die("<div class='alert alert-danger'>Query Error: " . mysqli_error($myDB) . "</div>");
}

// Check if records found
if (mysqli_num_rows($result) > 0) {
  echo '<table class="table table-bordered table-striped">';
  echo '<thead class="bg-theme-colored1 text-white">';
  echo '<tr><th>Date</th><th>Day</th><th>Total Days</th><th>Occasion</th></tr>';
  echo '</thead><tbody>';

  while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' 
   . date('d M Y', strtotime($row['d_o_h'])) 
   . ($row['count_of_day'] > 1 ? ' To ' . date('d M Y', strtotime($row['end_date'])) : '') 
   . '</td>';

echo '<td>' 
   . date('l', strtotime($row['d_o_h'])) 
   . ($row['count_of_day'] > 1 ? ' To ' . date('l', strtotime($row['end_date'])) : '') 
   . '</td>';echo '<td>' . htmlspecialchars($row['count_of_day']) . '</td>';
    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
    echo '</tr>';
  }

  echo '</tbody></table>';
} else {
  echo '<div class="alert alert-warning">No holidays found for this session.</div>';
}
?>