<?php
include('../../include/dbcon.php');

$student_id   = intval($_POST['student_id']);
$order_date   = date("Y-m-d");
$total_amount = floatval($_POST['total_amount']);

mysqli_query($myDB,"
INSERT INTO rrsv_uniform_orders
(student_id,order_date,total_amount)
VALUES
('$student_id','$order_date','$total_amount')
");

$order_id = mysqli_insert_id($myDB);

foreach($_POST['item_name'] as $k=>$item){

$item = trim($item);

if($item=='') continue;

$vendor = intval($_POST['vendor_id'][$k]);
$qty    = intval($_POST['qty'][$k]);
$rate   = floatval($_POST['rate'][$k]);

$amount = $qty * $rate;

mysqli_query($myDB,"
INSERT INTO rrsv_order_items
(order_id,vendor_id,item,quantity,rate,amount)
VALUES
('$order_id','$vendor','$item','$qty','$rate','$amount')
");

}

header("location:student_vendor_list.php");
exit;
?>