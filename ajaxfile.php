<?php
//include 'config.php';

session_start();


 $token = $_SESSION['_token'];
include('include/dbcon.php');


## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
//$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnName = "id"; // Column name
$columnSortOrder = "desc";//$_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($myDB,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (full_name like '%".$searchValue."%' or 
        email like '%".$searchValue."%' or 
        monthly_salary like'%".$searchValue."%' ) ";//
}

## Total number of records without filtering
$sel = mysqli_query($myDB,"select count(*) as allcount from rrsv_teacher");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($myDB,"select count(*) as allcount from rrsv_teacher WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from rrsv_teacher WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
//echo $empQuery;
$empRecords = mysqli_query($myDB, $empQuery);
$data = array();
//  print_r($empRecords);
//     die();
while ($row = mysqli_fetch_assoc($empRecords)) { 
   
    $data[] = array(
            "emp_image"=>$row['image'] == "" ? '<img src="https://thumbs.dreamstime.com/b/avatar-icon-avatar-flat-symbol-isolated-white-avatar-icon-avatar-flat-symbol-isolated-white-background-avatar-simple-icon-124920496.jpg" hight="50" width="50" style="border-radius: 50%;" alt="image">' : '<img src="http://rrsv.in/teacher_image/'.$row['image'].'" hight="50" width="50" style="border-radius: 50%;" alt="image">',
    		"emp_name"=>$row['full_name'],
    		"password"=>$row['password'],
    		"phone_no"=>$row['tech_phone_no'],
    		"email"=>$row['email'],
    		"gender"=>$row['gender'],
    		"salary"=>$row['monthly_salary'],
    		"action"=>'<a href="view_teacher.php?vId='.$row['id'].'"><i class="btn btn-success">View</i></a>&nbsp;<a href="add_teachers.php?eId='.$row['id'].'"><i class="btn btn-warning">Edit</i></a><a href="manage_teacher.php?dId='.$row['id'].'"><i class="btn btn-danger">Delete</i></a>'
    	);
}

## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
?>

