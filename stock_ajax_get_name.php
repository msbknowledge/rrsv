<?php
// print_r($_POST);
// die();
include('include/dbcon.php');

$type = null;
$sql = "";
$name = "";
$stock_type = "";
$class_name = "";
$session = "";
if (isset($_POST['stock_type'])) {
  $stock_type = mysqli_real_escape_string($myDB, $_POST['stock_type']);
}

if (isset($_POST['class_name'])) {
  $class_name = mysqli_real_escape_string($myDB, $_POST['class_name']);
}

if (isset($_POST['session'])) {
  $session = mysqli_real_escape_string($myDB, $_POST['session']);
}


if ($stock_type) {
  if ($stock_type === "BOOK") {
    $name = "book_name";
    $sql = "SELECT id,$name FROM rrsv_book";
  } else if ($stock_type === "COPY") {
    $name = "copy_name";
    $sql = "SELECT id,$name FROM rrsv_copy";
  } else if ($stock_type === "OTHERS") {
    $name = "name";
    $sql = "SELECT id,$name FROM stock_master";
  }


  if ($stock_type === "BOOK" || $stock_type === "COPY") {

    $sql .= " where 1";

    if ($session != "") {
      $sql .= " and  scl_session = '" . $session . "'";
    }

    if ($class_name != "-Choose-") {
      $sql .= " and  scl_class = '" . $class_name . "'";
    }

    $sql .= " order by id ASC";
  }



  // and scl_class = '".$class."'";
  // echo $sql;
  // die();
  $result = mysqli_query($myDB, $sql);

  $search_arr = array();

  if (mysqli_num_rows($result) > 0) {
    while ($fetch = mysqli_fetch_assoc($result)) {
      $id = $fetch['id'];
      $value = $fetch[$name];

      $search_arr[] = array("id" => $id, "name" => $value);
    }
  }
  echo json_encode($search_arr);
} else {
  $search_arr[] = array("id" => 0, "name" => "no record found1");
  echo json_encode($search_arr);
}

?>