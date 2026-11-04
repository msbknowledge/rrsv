<?php
//print_r($_POST);
// die();
include('include/dbcon.php');

$type = null;
$sql = "";
$stock_name = "";
$stock_type = "";
$scl_class = "";
$session = "";

$stock_coloumn_name = "";
$stock_table_name = "";

if (isset($_POST['stock_type'])) {
  $stock_type = mysqli_real_escape_string($myDB, $_POST['stock_type']);
}

if (isset($_POST['stock_name'])) {
  $stock_name = mysqli_real_escape_string($myDB, $_POST['stock_name']);
}

if (isset($_POST['scl_class'])) {
  $scl_class = mysqli_real_escape_string($myDB, $_POST['scl_class']);
}

if (isset($_POST['scl_session'])) {
  $session = mysqli_real_escape_string($myDB, $_POST['scl_session']);
}

$sql = "select COUNT(a.id) AS count from stock as a left join";



if ($stock_type === "BOOK") {
  $stock_coloumn_name = "book_name";
  $stock_table_name = "rrsv_book";
} else if ($stock_type === "COPY") {
  $stock_coloumn_name = "copy_name";
  $stock_table_name = "rrsv_copy";
} else if ($stock_type === "OTHERS") {
  $stock_coloumn_name = "field_name";
  $stock_table_name = "stock_master";
}

$sql .= " $stock_table_name b on a.stock_master_id=b.id where a.session = '" . $session . "' and b.$stock_coloumn_name = '" . $stock_name . "'";

if ($stock_type === "BOOK" || $stock_type === "COPY") {

  $sql .= " and b.scl_class = '" . $scl_class . "'";
}
$sql .= " and a.activity_status = '1'";
//echo $sql;

$result = mysqli_query($myDB, $sql);

echo (mysqli_fetch_assoc($result)["count"] == 0) ? "false" : "true";
?>