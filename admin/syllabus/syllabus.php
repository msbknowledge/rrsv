<?php
session_start();
include('../../include/dbcon.php');
include('../../include/config.php');

header('Content-Type: application/json'); // important

$c = 0;
$draw = $_POST['draw'] ?? 1;
$row = $_POST['start'] ?? 0;
$rowperpage = $_POST['length'] ?? 10;
$columnIndex = $_POST['order'][0]['column'] ?? 0;
$columnSortOrder = $_POST['order'][0]['dir'] ?? "asc";
$searchValue = mysqli_real_escape_string($myDB, $_POST['search']['value'] ?? '');
$class = $_POST['scl_class'] ?? '';
$session = $_POST['scl_session'] ?? '';
$unit = $_POST['unit'] ?? '';

$columns = [
  0 => "s.id",
  1 => "c.class_name",
  2 => "sub.sub_name",
  3 => "s.syllabus_file"
];
$columnName = $columns[$columnIndex] ?? "s.id";

$where = " WHERE 1=1 ";
if ($searchValue != '')
  $where .= " AND (c.class_name LIKE '%$searchValue%' OR sub.sub_name LIKE '%$searchValue%')";
if ($class != '')
  $where .= " AND c.class_name = '" . mysqli_real_escape_string($myDB, $class) . "'";
if ($session != '')
  $where .= " AND s.session = '" . mysqli_real_escape_string($myDB, $session) . "'";
if ($unit != '')
  $where .= " AND s.unit = '" . mysqli_real_escape_string($myDB, $unit) . "'";

// Total records
$totalRecords = mysqli_fetch_assoc(mysqli_query($myDB, "SELECT COUNT(*) as allcount FROM rrsv_syllabus"))['allcount'];
$totalRecordwithFilter = mysqli_fetch_assoc(mysqli_query($myDB, "SELECT COUNT(*) as allcount FROM rrsv_syllabus s LEFT JOIN rrsv_class c ON s.class_id=c.id LEFT JOIN rrsv_subject sub ON s.subject_id=sub.id $where"))['allcount'];

// Fetch data
$empQuery = "SELECT s.id, c.class_name, sub.sub_name,s.unit,s.session, s.syllabus_file
             FROM rrsv_syllabus s
             LEFT JOIN rrsv_class c ON s.class_id=c.id
             LEFT JOIN rrsv_subject sub ON s.subject_id=sub.id
             $where
             ORDER BY $columnName $columnSortOrder
             LIMIT $row, $rowperpage";

$empRecords = mysqli_query($myDB, $empQuery);
$data = [];
$unitMap = [
  1 => "1st Unit",
  2 => "2nd Unit",
  3 => "3rd Unit"
];
while ($row = mysqli_fetch_assoc($empRecords)) {
  $c++;
  $data[] = [
    "sl_no" => $c,
    "class_name" => $row['class_name'] ?? "—",
    "subject_name" => $row['sub_name'] ?? "—",
    "unit" => isset($row['unit']) && isset($unitMap[$row['unit']]) ? $unitMap[$row['unit']] : "—",
     "session" => $row['session'] ?? "—",
    // "syllabus_file" => !empty($row['syllabus_file']) ? "<a href='" . BASE_URL . $row['syllabus_file'] . "' target='_blank'>Download</a>" : "No File",
    "action" => '
    <a href="add_syllabus.php?id=' . $row['id'] . '" class="btn btn-primary btn-sm">Edit</a> 
   
                     <a href="manage_syllabus.php?dId=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this syllabus?\')">Delete</a>'
  ];
}
// <a href="download_syllabus.php?id=' . $row['id'] . '" class="btn btn-primary btn-sm">download</a>
//<button class="btn btn-sm btn-info viewBtn" data-id="' . $row['id'] . '">View</button>
// Response
$response = [
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
];

echo json_encode($response);
exit;

