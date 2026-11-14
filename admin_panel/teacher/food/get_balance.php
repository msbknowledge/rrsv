<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
include '../../../include/config.php';
include '../../dbcon.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['balance' => 0]);
    exit;
}

$teacher_id = intval($_SESSION['user_id'] ?? ($_SESSION['user_data']['id'] ?? 0));
$teacher_code = 'T-' . $teacher_id;

$q = mysqli_query($con, "
    SELECT balance 
    FROM rrsv_food_wallet 
    WHERE student_id = '$teacher_code' OR student_id = '$teacher_id'
    LIMIT 1
");

$row = mysqli_fetch_assoc($q);
echo json_encode([
    'balance' => (float)($row['balance'] ?? 0)
]);
