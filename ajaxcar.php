<?php
//include 'config.php';

session_start();
 $token = $_SESSION['_token'];
include('include/dbcon.php');
$c=0;
if(isset($_GET['id'])) {
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}


## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
//$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnName = "id"; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($myDB,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (kilo like '%".$searchValue."%' ) ";//
}


## Total number of records without filtering
$sel = mysqli_query($myDB,"select count(*) as allcount from rrsv_car");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($myDB,"select count(*) as allcount from rrsv_car WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
 $empQuery = "select * from rrsv_car WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
//echo $empQuery;
$empRecords = mysqli_query($myDB, $empQuery);
$data = array();
//  print_r($empRecords);
//     die();
while ($row = mysqli_fetch_assoc($empRecords)) {
   $c++;
   $id=$row['id'];
    $data[] = array(
        "sl_no"=>$c,
        "scl_session"=>$row['scl_session'],
    		"kilo"=>$row['kilo'],
    		"cost"=>$row['fair'],
    		"action"=>'<a href="add_car.php?id='.$row['id'].'"><i class="btn btn-primary btn-sm">Edit</i></a> <a href="manage_car.php?dId='.$row['id'].'"><i class="btn btn-danger btn-sm">Delete</i></a>'
    	);
}
// if($id > 0) {
  
//   $sql="Delete from rrsv_class where id='".$id."'";
//   $res=mysqli_query($myDB,$sql);
  
//   }
## Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
