<?php
include('../../include/dbcon.php');

$data = [];

$q = mysqli_query($myDB, "SELECT * FROM rrsv_calender_plan WHERE status=1");

while($row = mysqli_fetch_assoc($q)){
    $data[] = [
        "id" => $row['id'],
        "title" => $row['title'],
        "start" => $row['plan_date'],
        "reminder_date" => $row['reminder_date'],
        "description" => $row['description']
    ];
}

echo json_encode($data);