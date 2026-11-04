<?php
include('../../include/dbcon.php');

$order_id = intval($_POST['order_id']);

mysqli_query($myDB,"
DELETE FROM rrsv_order_items
WHERE order_id='$order_id'
");

$total = 0;

foreach($_POST['item_name'] as $k=>$item){

$item = trim($item);

if($item=='') continue;

$vendor = intval($_POST['vendor_id'][$k]);
$qty    = intval($_POST['qty'][$k]);
$rate   = floatval($_POST['rate'][$k]);

$amount = $qty * $rate;

$total += $amount;

mysqli_query($myDB,"
INSERT INTO rrsv_order_items
(order_id,vendor_id,item,quantity,rate,amount)
VALUES
('$order_id','$vendor','$item','$qty','$rate','$amount')
");

}

mysqli_query($myDB,"
UPDATE rrsv_uniform_orders
SET total_amount='$total'
WHERE id='$order_id'
");

header("location:student_vendor_list.php");
exit;