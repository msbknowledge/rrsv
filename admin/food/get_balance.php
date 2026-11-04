<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
include('../../include/dbcon.php');

$student_id = ($_GET['student_id'] ?? 0);

$q = mysqli_query($myDB, "
    SELECT balance 
    FROM rrsv_food_wallet 
    WHERE student_id = '$student_id'
");

$row = mysqli_fetch_assoc($q);

echo json_encode([
    'balance' => $row['balance'] ?? 0
]);
