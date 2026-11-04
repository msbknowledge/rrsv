<?php
include('../../include/dbcon.php');
if ($_POST) {
$vendor_id = $_POST['vendor_id'];
$bill_no = $_POST['bill_no'];
$bill_date = $_POST['bill_date'];
$total_amount = $_POST['total_amount'];
$remarks = $_POST['remarks'];

mysqli_query($myDB, "INSERT INTO rrsv_vendor_bills(vendor_id,bill_no,bill_date,total_amount,balance_amount,remarks)
VALUES('$vendor_id','$bill_no','$bill_date','$total_amount','$total_amount','$remarks')");
$bill_id = mysqli_insert_id($myDB);

// mysqli_query($myDB, "INSERT INTO rrsv_expence(vendor_id,bill_no,bill_date,total_amount,balance_amount,remarks)
// VALUES('$vendor_id','$bill_no','$bill_date','$total_amount','$total_amount','$remarks')");
// $bill_id = mysqli_insert_id($myDB);

foreach ($_POST['item_name'] as $i => $item) {
$qty = $_POST['qty'][$i];
$rate = $_POST['rate'][$i];
$amount = $_POST['amount'][$i];
mysqli_query($myDB, "INSERT INTO rrsv_vendor_bill_items(bill_id,item_name,qty,rate,amount)
VALUES('$bill_id', '$item', '$qty', '$rate', '$amount')");
}
echo "<script>alert('Bill saved successfully!'); window.location='manage_vendor_bill.php';</script>";
}
?>