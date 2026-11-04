<?php
//print_r($_POST);
// die();
include('include/dbcon.php');

$sql = "";
$stock_master_id = 0;
$stock_type = "";
$class_id = 0;
$session = "";

$stock_coloumn_name = "";
$stock_table_name = "";

if (isset($_POST['stock_type'])) {
  $stock_type = mysqli_real_escape_string($myDB, $_POST['stock_type']);
}

if (isset($_POST['stock_master_id'])) {
  $stock_master_id = mysqli_real_escape_string($myDB, $_POST['stock_master_id']);
}

if (isset($_POST['class_id'])) {
  $class_id = mysqli_real_escape_string($myDB, $_POST['class_id']);
}

if (isset($_POST['session'])) {
  $session = mysqli_real_escape_string($myDB, $_POST['session']);
}

if ($stock_type === "OTHERS") {
  $class_id = 0;
}
$sql = "select COUNT(id) AS count from stock where session = '" . $session . "' and stock_master_id = '" . $stock_master_id . "' and class_id = '" . $class_id . "' and stock_type = '" . $stock_type . "'";
$sql .= " and activity_status = '1'";
//die;

$result = mysqli_query($myDB, $sql);

echo (mysqli_fetch_assoc($result)["count"] == 0) ? "false" : "true";
?>