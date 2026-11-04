<?php
include('include/dbcon.php');

$class_id = $_POST['class_id'];
$stock_type = $_POST['stock_type'];
$session = $_POST['session'];

// Dynamically determine the table to join based on stock_type
switch ($stock_type) {
    case 'BOOK':
        $joinTable = 'rrsv_book';
        $nameColumn = 'book_name';
        $whereCondition = "WHERE s.class_id = '$class_id' AND s.stock_type = '$stock_type' AND s.session = '$session'";
        break;
    case 'COPY':
        $joinTable = 'rrsv_copy';
        $nameColumn = 'copy_name';
        $whereCondition = "WHERE s.class_id = '$class_id' AND s.stock_type = '$stock_type' AND s.session = '$session'";
        break;
    default: // For 'OTHERS'
        $joinTable = 'stock_master';
        $nameColumn = 'name';
        $whereCondition = ""; // No class_id or session filter for OTHERS
        break;
}
$whereCondition .= " AND s.activity_status = 1";
$query = "
  SELECT s.id, jt.$nameColumn AS name 
  FROM stock s
  JOIN $joinTable jt ON s.stock_master_id = jt.id
  $whereCondition
";

$result = mysqli_query($myDB, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>