<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
include '../../../include/config.php';
include '../../dbcon.php';

if (!isset($_SESSION['scl_reg_no'])) {
    echo json_encode(['balance' => 0]);
    exit;
}

$student_id =$_SESSION['scl_reg_no'];

$q = mysqli_query($con, "
    SELECT balance 
    FROM rrsv_food_wallet 
    WHERE student_id = '$student_id'
");

$row = mysqli_fetch_assoc($q);
echo json_encode([
    'balance' => $row['balance'] ?? 0
]);
