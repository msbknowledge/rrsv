<?php
session_start();
include('../../include/dbcon.php');

$c = $_POST['start']; // offset for serial number

$draw = intval($_POST['draw']);
$rowperpage = intval($_POST['length']);
$columnIndex = $_POST['order'][0]['column'];
$columnSortOrder = $_POST['order'][0]['dir'];
$searchValue = mysqli_real_escape_string($myDB, $_POST['search']['value']);
$class = $_POST['scl_class'] ?? '';
$session = $_POST['scl_session'] ?? '';

$where = " WHERE 1=1 ";
if ($searchValue != '') {
  $where .= " AND (
        r.day_of_week LIKE '%$searchValue%' OR
        r.teacher_name LIKE '%$searchValue%' OR
        c.class_name LIKE '%$searchValue%' OR
        s.section_name LIKE '%$searchValue%' OR
        sub.subject_name LIKE '%$searchValue%'
    )";
}
if ($class != '') {
  $where .= " AND c.class_name = '" . mysqli_real_escape_string($myDB, $class) . "'";
}
if ($session != '') {
  $where .= " AND r.session = '" . mysqli_real_escape_string($myDB, $session) . "'";
}

// Total records
$totalRecordsQuery = "SELECT COUNT(*) AS allcount FROM rrsv_routine";
$totalRecordsResult = mysqli_query($myDB, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['allcount'];

// Total filtered records
$totalFilterQuery = "
    SELECT COUNT(*) AS allcount
    FROM rrsv_routine r
    JOIN rrsv_class c ON r.class_id = c.id
    JOIN rrsv_section s ON r.section_id = s.id
    JOIN rrsv_subject sub ON r.subject_id = sub.id
    $where";
$totalFilteredResult = mysqli_query($myDB, $totalFilterQuery);
$totalRecordwithFilter = mysqli_fetch_assoc($totalFilteredResult)['allcount'];

// Fetch records
$empQuery = "
    SELECT r.*, c.class_name, s.section_name,t.full_name, sub.sub_name
    FROM rrsv_routine r
    JOIN rrsv_class c ON r.class_id = c.id
    JOIN rrsv_section s ON r.section_id = s.id
    JOIN rrsv_subject sub ON r.subject_id = sub.id
    JOIN rrsv_teacher t ON r.teacher_id = t.id
    $where
    ORDER BY r.id $columnSortOrder
    LIMIT $c, $rowperpage";
$empRecords = mysqli_query($myDB, $empQuery);

$data = [];
while ($row = mysqli_fetch_assoc($empRecords)) {
  $c++;
  $data[] = [
    "sl_no" => $c,
    "class_name" => $row['class_name'],
    "section_name" => $row['section_name'],
    "subject_name" => $row['sub_name'],
    "day_of_week" => $row['day_of_week'],
    "teacher_name" => $row['full_name'],
    "start_time" => $row['start_time'],
    "end_time" => $row['end_time'],
    "action" => '<a href="add_routine.php?id=' . $row['id'] . '" class="btn btn-primary btn-sm">Edit</a>
                     <a href="manage_routine.php?dId=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>'
  ];
}

$response = [
  "draw" => $draw,
  "iTotalRecords" => intval($totalRecords),
  "iTotalDisplayRecords" => intval($totalRecordwithFilter),
  "aaData" => $data
];

echo json_encode($response);
