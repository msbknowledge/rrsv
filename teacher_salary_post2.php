<?php
include('include/dbcon.php');
$teacher_id = $_POST['teacher_id'];
$frist_date = date('Y-m-01');
$last_date = date('Y-m-t');

// echo "<pre>";
// print_r($_POST);

$sql = "SELECT count(*) AS count FROM `rrsv_teacher_attendence` where date between '$frist_date' and '$last_date' and teacher_id=$teacher_id";
// echo $sql;
// echo "<br>";
$qr = mysqli_query($myDB, $sql);
$count = mysqli_fetch_array($qr, MYSQLI_ASSOC);
$presents_days = $count['count'];
echo $presents_days;
echo "<br>";

$sql2 = "SELECT count(is_late) AS late FROM `rrsv_teacher_attendence` where date between '$frist_date' and '$last_date' and teacher_id=$teacher_id and is_late=2";
echo $sql2;
echo "<br>";
$qr2 = mysqli_query($myDB, $sql2);
$count2 = mysqli_fetch_array($qr2, MYSQLI_ASSOC);
//print_r($count2);

$presents_days2 = $count2['late'];
echo "TOTAL LATE".$presents_days2;

?>