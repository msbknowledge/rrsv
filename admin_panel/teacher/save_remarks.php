<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';

$exam_id = $_POST['exam_id'];
$student_id = $_POST['student_id'];
$remarks = $_POST['remarks'];

$exists = $con->query("SELECT id FROM rrsv_student_homework_summary 
    WHERE exam_id='$exam_id' AND student_id='$student_id'");

if ($exists->num_rows > 0) {
    $con->query("UPDATE rrsv_student_homework_summary 
        SET remarks='$remarks' 
        WHERE exam_id='$exam_id' AND student_id='$student_id'");
} else {
    $con->query("INSERT INTO rrsv_student_homework_summary 
        (student_id, exam_id, remarks) 
        VALUES ('$student_id', '$exam_id', '$remarks')");
}

header("Location: home_work_results.php?exam_id=$exam_id&remarks_saved=1");
exit;
?>
