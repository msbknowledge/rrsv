<?php
include('../../include/dbcon.php');

$id = $_POST['id'] ?? '';
$value = trim($_POST['value'] ?? '');

if (empty($id)) {
    die("Invalid ID");
}

// Update teacher name or subject dynamically
// You can adjust to detect what field was edited (using JS data attributes)
$sql = "UPDATE rrsv_routine SET teacher_name = ? WHERE id = ?";
$stmt = mysqli_prepare($myDB, $sql);
mysqli_stmt_bind_param($stmt, "si", $value, $id);
if (mysqli_stmt_execute($stmt)) {
    echo "Updated successfully!";
} else {
    echo "Error updating record: " . mysqli_error($myDB);
}
?>