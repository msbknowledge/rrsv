<?php
session_start();
include('../../include/dbcon.php');
include('../../include/config.php');
$c = 0;

## Read DataTables POST variables
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'];
$columnSortOrder = $_POST['order'][0]['dir'];
$searchValue = mysqli_real_escape_string($myDB, $_POST['search']['value']);

## Search
$searchQuery = "";
if ($searchValue != '') {
  $searchQuery = " AND (
        title LIKE '%" . $searchValue . "%' OR
        description LIKE '%" . $searchValue . "%' OR
        notice_date LIKE '%" . $searchValue . "%' OR
        status LIKE '%" . $searchValue . "%'
    )";
}

## Total records without filtering
$totalRecordsResult = mysqli_query($myDB, "SELECT COUNT(*) AS allcount FROM rrsv_notice");
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['allcount'];

## Total records with filtering
$totalRecordwithFilterResult = mysqli_query($myDB, "SELECT COUNT(*) AS allcount FROM rrsv_notice WHERE 1 " . $searchQuery);
$totalRecordwithFilter = mysqli_fetch_assoc($totalRecordwithFilterResult)['allcount'];

## Fetch records
$query = "SELECT * FROM rrsv_notice WHERE 1 " . $searchQuery . " ORDER BY id " . $columnSortOrder . " LIMIT " . $row . "," . $rowperpage;
$result = mysqli_query($myDB, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $c++;
   $id = $row['id'];
  $fileLink = !empty($row['notice_file'])
    ? "<a href='" .BASE_URL .$row['notice_file'] . "' target='_blank'>Download</a>"
    : "No File";
$status = $row['status'] ?? 'Inactive';
  // Status button
  $statusBtn = '<a href="#" onclick="confirmsearch(' . $id . ', \'' . $status . '\')" 
                    title="Click to change this status" class="text">
                    <i data="' . $id . '" 
                       class="btn ' . ($status == 'Active' ? 'btn-info' : 'btn-danger') . '">' . $status . '</i>
                  </a>';
  $data[] = [
    "sl_no" => $c,
    "title" => $row['title'],
    "description" => $row['description'],
    // "notice_file" => $fileLink,
    "notice_date" => $row['notice_date'],
    "status" =>  $statusBtn ,
    "action" => '<a href="add_notice.php?id=' . $row['id'] . '"><i class="btn btn-primary btn-sm">Edit</i></a> 
                     <a href="manage_notice.php?dId=' . $row['id'] . '"><i class="btn btn-danger btn-sm">Delete</i></a>'
  ];
}

$response = [
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
];

echo json_encode($response);
