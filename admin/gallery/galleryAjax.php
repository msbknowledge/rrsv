<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
include('../../include/config.php');
include('../../include/dbcon.php');
$c = 0;

// DataTables request values
$draw = intval($_POST['draw'] ?? 1);
$row = intval($_POST['start'] ?? 0);
$rowperpage = intval($_POST['length'] ?? 10);
$columnIndex = $_POST['order'][0]['column'] ?? 0;
$columnSortOrder = $_POST['order'][0]['dir'] ?? 'asc';
$searchValue = mysqli_real_escape_string($myDB, $_POST['search']['value'] ?? '');

// Column mapping (index → column name in DB)
$columns = ["id", "title", "category", "image_path", "uploaded_on", "status"];
$columnName = $columns[$columnIndex] ?? "id";

// Search filter
$searchQuery = "";
if ($searchValue != '') {
  $searchQuery = " AND (title LIKE '%" . $searchValue . "%' OR category LIKE '%" . $searchValue . "%')";
}

// Total records
$totalRecordsQuery = mysqli_query($myDB, "SELECT COUNT(*) AS allcount FROM rrsv_gallery");
$totalRecords = mysqli_fetch_assoc($totalRecordsQuery)['allcount'] ?? 0;

// Total records with filter
$filterRecordsQuery = mysqli_query($myDB, "SELECT COUNT(*) AS allcount FROM rrsv_gallery WHERE 1 " . $searchQuery);
$totalRecordwithFilter = mysqli_fetch_assoc($filterRecordsQuery)['allcount'] ?? 0;

// Fetch records
$empQuery = "SELECT * FROM rrsv_gallery WHERE 1 " . $searchQuery .
  " ORDER BY " . $columnName . " " . $columnSortOrder .
  " LIMIT " . $row . "," . $rowperpage;

$empRecords = mysqli_query($myDB, $empQuery);

$data = [];
while ($row = mysqli_fetch_assoc($empRecords)) {
  $c++;
  $id = $row['id'];
  $status = $row['status'] ?? 'Inactive';
  // Status button
  $statusBtn = '<a href="#" onclick="confirmsearch(' . $id . ', \'' . $status . '\')" 
                    title="Click to change this status" class="text">
                    <i data="' . $id . '" 
                       class="btn ' . ($status == 'Active' ? 'btn-info' : 'btn-danger') . '">' . $status . '</i>
                  </a>';
  // Action buttons
  $actions = '
        <a href="add_gallery.php?id=' . $id . '" class="btn btn-primary btn-sm">Edit</a> 
        <a href="manage_gallery.php?dId=' . $id . '" class="btn btn-danger btn-sm" 
           onclick="return confirm(\'Delete this record?\')">Delete</a>';
  // Safe image preview
  $imagePreview = !empty($row['image_path'])
    ? "<img src='" . BASE_URL . htmlspecialchars($row['image_path']) . "' alt='Gallery Image' width='60'>"
    : "No Image";

  $data[] = [
    "sl_no" => $c,
    "title" => $row['title'] ?? '',
    "category" => $row['category'] ?? '',
    "image" => $imagePreview,
    "status" =>$statusBtn,
    "uploaded_on" => $row['uploaded_on'] ?? '',
    "action" => $actions
  ];
}

// Response in DataTables format
$response = [
  "draw" => $draw,
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
];

echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
exit;
