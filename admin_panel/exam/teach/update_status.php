<?php
 error_reporting(0); 
 session_start();
include_once 'dbConnection.php';

$id=$_POST['id'];
$status=$_POST['status'];

mysqli_query($con,"UPDATE quiz_assignments
SET status='$status'
WHERE id='$id'");

echo "success";
?>