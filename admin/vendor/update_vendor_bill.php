<?php
include('../../include/dbcon.php');

if ($_POST) {

    $id = (int)$_POST['id'];
    $vendor_id = (int)$_POST['vendor_id'];
    $bill_no = mysqli_real_escape_string($myDB, $_POST['bill_no']);
    $bill_date = mysqli_real_escape_string($myDB, $_POST['bill_date']);
    $total_amount = (float)$_POST['total_amount'];

    // Update main bill
    mysqli_query($myDB, "UPDATE rrsv_vendor_bills SET 
        vendor_id='$vendor_id', 
        bill_no='$bill_no', 
        bill_date='$bill_date', 
        total_amount='$total_amount', 
        balance_amount='$total_amount' 
        WHERE id='$id'");

    // Remove old items
    mysqli_query($myDB, "DELETE FROM rrsv_vendor_bill_items WHERE bill_id='$id'");

    // Insert new items
    foreach ($_POST['item_name'] as $i => $item) {
        $item = mysqli_real_escape_string($myDB, $item);
        $qty = (float)$_POST['qty'][$i];
        $rate = (float)$_POST['rate'][$i];
        $amount = (float)$_POST['amount'][$i];
        $gst = isset($_POST['gst_rate'][$i]) ? (float)$_POST['gst_rate'][$i] : 0;

        mysqli_query($myDB, "INSERT INTO rrsv_vendor_bill_items 
            (bill_id, item_name, qty, rate, amount) 
            VALUES ('$id', '$item', '$qty', '$rate', '$amount')");
    }

    echo "<script>alert('Bill updated'); window.location='manage_vendor_bill.php';</script>";
}
?>
