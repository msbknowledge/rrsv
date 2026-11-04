<?php
include('../../include/dbcon.php');

if (isset($_POST['id'], $_POST['column'], $_POST['value'])) {
  $id = intval($_POST['id']);
  $column = $_POST['column'];
  $value = intval($_POST['value']);

  // Whitelist columns for safety
  $allowed = ['can_view', 'can_insert', 'can_update', 'can_delete', 'can_display_landing', 'status'];

  if (in_array($column, $allowed)) {
    $sql = "UPDATE rrsv_permission SET $column = $value WHERE id = $id";
    if (mysqli_query($myDB, $sql)) {
      echo json_encode(['status' => 'success', 'message' => ucfirst(str_replace('_', ' ', $column)) . ' updated']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid column']);
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
}
?>