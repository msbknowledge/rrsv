<?php
error_reporting(1);
include('include/dbcon.php');

$id = $_POST['student_id'];
$dise = $_POST['dise_code'];

mysqli_query($myDB,"
UPDATE rrsv_student_registration
SET dise_code='$dise',
dise_date=NOW()
WHERE id='$id'
");

header("Location: home.php");
?>