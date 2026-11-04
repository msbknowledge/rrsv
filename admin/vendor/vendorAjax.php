<?php
session_start();
$token = $_SESSION['_token'];
include('../../include/dbcon.php');

$c = 0;
if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
}

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
//$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnName = "id"; // For now fixed to ID
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($myDB, $_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " and (name like '%" . $searchValue . "%' or description like '%" . $searchValue . "%' or status like '%" . $searchValue . "%') ";
}

## Total number of records without filtering
$sel = mysqli_query($myDB, "select count(*) as allcount from rrsv_vendor");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($myDB, "select count(*) as allcount from rrsv_vendor WHERE 1 " . $searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from rrsv_vendor WHERE 1 " . $searchQuery . " order by " . $columnName . " " . $columnSortOrder . " limit " . $row . "," . $rowperpage;
//echo $empQuery;
$empRecords = mysqli_query($myDB, $empQuery);

$data = array();
while ($row = mysqli_fetch_assoc($empRecords)) {
  $c++;
  $id = $row['id'];
  // Status button
  $status = $row['status'] ?? 'Inactive';
  $statusBtn = '<a href="#" onclick="confirmsearch(' . $id . ', \'' . $status . '\')" 
                    title="Click to change this status" class="text">
                    <i data="' . $id . '" 
                       class="btn ' . ($status == 'Active' ? 'btn-info' : 'btn-danger') . '">' . $status . '</i>
                  </a>';
  $data[] = array(
    "sl_no" => $c,
    "name" => $row['name'],
    "mobile" => $row['mobile'],
    "email" => $row['email'],
    "address" => $row['address'],
    "status" =>   $statusBtn ,
    "action" => '<a href="add_vendor.php?id=' . $row['id'] . '"><i class="btn btn-primary btn-sm">Edit</i></a> 
    <a href="final_bill_report.php?id=' . $row['id'] . '"><i class="btn btn-primary btn-sm">Bill</i></a> 
                '
  );
}
//  <a href="manage_vendor.php?dId=' . $row['id'] . '" onclick="return confirm(\'Are you sure?\')"><i class="btn btn-danger btn-sm">Delete</i></a>
## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
