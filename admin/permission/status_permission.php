<?php
include('../../include/dbcon.php');
if (isset($_POST['id']) && isset($_POST['status'])) {
  header('Content-Type: application/json');
  $id = intval($_POST['id']);
  $status = ($_POST['status'] === 'Active') ? 'Active' : 'Inactive';

  $query = "UPDATE rrsv_permission SET status='$status' WHERE id='$id'";
  if (mysqli_query($myDB, $query)) {
    echo json_encode(['status' => 1]); // ✅ success
  } else {
    echo json_encode(['status' => 0]); // ❌ failure
  }
  exit; // ✅ Stop further output for AJAX request
}
?>