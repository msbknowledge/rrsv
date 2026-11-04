<?php
include('../../include/dbcon.php');

$term = $_GET['term'];

$q = mysqli_query($myDB,"
SELECT *
FROM rrsv_student_registration
WHERE scl_name LIKE '%$term%'
OR scl_reg_no LIKE '%$term%'
LIMIT 10
");

$data = [];

while($row=mysqli_fetch_assoc($q)){

$data[] = [

"label"=>$row['scl_name']." (".$row['scl_session'].")",

"value"=>$row['scl_name'],

"id"=>$row['id'],

"name"=>$row['scl_name'],

"reg_no"=>$row['scl_reg_no'],

"class"=>$row['scl_class'],

"session"=>$row['scl_session']

];

}

echo json_encode($data);