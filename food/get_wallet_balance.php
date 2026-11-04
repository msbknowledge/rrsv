<?php
session_start();
include '../dbcon.php';

if (!isset($_SESSION['user_data'])) {
    exit;
}

$student_id = $_SESSION['user_data']['id'];

$q = mysqli_query($con, "
    SELECT balance 
    FROM rrsv_wallet 
    WHERE student_id='$student_id'
");

$row = mysqli_fetch_assoc($q);

echo number_format($row['balance'] ?? 0, 2);
