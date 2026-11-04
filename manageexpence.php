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
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($myDB,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
	$searchQuery = " and (class_name like '%".$searchValue."%' or 
        section_name like '%".$searchValue."%'  ) ";//
}

## Total number of records without filtering
$sel = mysqli_query($myDB,"select count(*) as allcount from rrsv_section");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($myDB,"select count(*) as allcount from rrsv_section WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from rrsv_section  WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
//echo $empQuery;
$empRecords = mysqli_query($myDB, $empQuery);
$data = array();
//  print_r($empRecords);
//     die();
while ($row = mysqli_fetch_assoc($empRecords)) {
   
      $c++;
   
    $data[] = array(
        "sl_no"=>$c,
    		"class_name"=>$row['class_name'],
        	"section_name"=>$row['section_name'],
    		"action"=>'<a href="add_section.php?id='.$row['id'].'"><i class="mdi mdi-table-edit"></i></a><a href="manage_section.php?dId='.$row['id'].'"><i class="mdi mdi-delete"></i></a>'
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
