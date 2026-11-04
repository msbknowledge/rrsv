<?php
include '../../include/config.php';
include '../dbcon.php';

$student_id = $_POST['student_id'];
$exam_id = $_POST['exam_id'];

$q = $con->query("SELECT remarks FROM rrsv_student_exam_result 
                  WHERE student_id='$student_id' 
                  AND exam_id='$exam_id'");

$row = $q->fetch_assoc();

echo $row['remarks'] ?? "";
