<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
include('../../include/dbcon.php');

$student_id = trim($_GET['student_id'] ?? '');
if (empty($student_id) && isset($_GET['teacher_id'])) {
    $student_id = 'T-' . trim($_GET['teacher_id']);
}
$safe_id = mysqli_real_escape_string($myDB, $student_id);

$q = mysqli_query($myDB, "
    SELECT balance 
    FROM rrsv_food_wallet 
    WHERE student_id = '$safe_id'
");

$row = mysqli_fetch_assoc($q);

echo json_encode([
    'balance' => $row['balance'] ?? 0
]);
