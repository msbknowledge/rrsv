<?php
include('../../include/dbcon.php');

$id = $_GET['student_id'];

mysqli_query($myDB,"
DELETE FROM rrsv_student_vendor_items
WHERE student_id='$id'
");

header("location:student_vendor_list.php");