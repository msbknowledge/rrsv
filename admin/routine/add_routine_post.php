<?php
session_start();
include('../../include/dbcon.php');

$token = $_SESSION['_token'] ?? '';
$id = intval($_POST['id'] ?? 0);
$class_id = intval($_POST['class_id'] ?? 0);
$section_id = intval($_POST['section_id'] ?? 0);
$subject_id = intval($_POST['subject_id'] ?? 0);
$session = mysqli_real_escape_string($myDB, $_POST['scl_session'] ?? '');
$teacher_id = intval($_POST['teacher_id'] ?? 0);
$day_of_week = $myDB->real_escape_string($_POST['day_of_week'] ?? '');
$teacher_name = $myDB->real_escape_string($_POST['teacher_name'] ?? '');
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$tokenpost = $_POST['token'] ?? '';

// Token validation
if ($tokenpost != $token)
    exit();

// Duplicate check: same class, section, subject, day, and overlapping time
$dupQuery = "SELECT * FROM rrsv_routine WHERE session='$session' AND class_id=$class_id AND section_id=$section_id AND subject_id=$subject_id AND teacher_id=$teacher_id AND day_of_week='$day_of_week'";
if ($id > 0)
    $dupQuery .= " AND id != $id";

$dupRes = mysqli_query($myDB, $dupQuery);
$conflict = false;
while ($row = mysqli_fetch_assoc($dupRes)) {
    if (!($end_time <= $row['start_time'] || $start_time >= $row['end_time'])) {
        $conflict = true;
        break;
    }
}
if ($conflict) {
    echo 2; // Conflict exists
    exit();
}

// Update existing rrsv_routine
if ($id > 0) {
    $sql = "UPDATE rrsv_routine SET session='$session',
            class_id=$class_id, 
            section_id=$section_id, 
            subject_id=$subject_id,
            teacher_id=$teacher_id, 
            day_of_week='$day_of_week', 
            teacher_name='$teacher_name', 
            start_time='$start_time', 
            end_time='$end_time'
            WHERE id=$id";
    if (mysqli_query($myDB, $sql))
        echo 1; // Updated
}
// Insert new rrsv_routine
else {
    $sql = "INSERT INTO rrsv_routine SET session='$session',
            class_id=$class_id, 
            section_id=$section_id, 
            subject_id=$subject_id,
            teacher_id=$teacher_id,  
            day_of_week='$day_of_week', 
            teacher_name='$teacher_name', 
            start_time='$start_time', 
            end_time='$end_time'";
    if (mysqli_query($myDB, $sql))
        echo 0; // Inserted
}
?>