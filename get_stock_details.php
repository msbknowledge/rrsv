<?php
include('include/dbcon.php');

$stockId = $_POST['stockId'];

$query = "SELECT purchase_value_per_unit,stock_after_sale FROM stock WHERE id = '$stockId'";
$result = mysqli_query($myDB, $query);
$data = mysqli_fetch_assoc($result);

echo json_encode($data);
?>