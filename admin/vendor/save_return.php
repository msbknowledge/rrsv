<?php
include('../../include/dbcon.php');

if ($_POST) {

    $id = (int)$_POST['id'];
    
    
    $billSQL = "SELECT * FROM rrsv_vendor_bills WHERE id = '$id' LIMIT 1";
    $billQuery = mysqli_query($myDB, $billSQL);
    $bill = mysqli_fetch_assoc($billQuery);
    $paid_amount = $bill['paid_amount'];  
    
    $vendor_id = (int)$_POST['vendor_id'];
    $bill_no = mysqli_real_escape_string($myDB, $_POST['bill_no']);
    $bill_date = mysqli_real_escape_string($myDB, $_POST['bill_date']);
    $total_amount = (float)$_POST['total_amount'];
    $balance_amount=(float)($total_amount-$paid_amount);
    // Update bill
    mysqli_query($myDB, "
        UPDATE rrsv_vendor_bills SET 
            vendor_id='$vendor_id', 
            bill_no='$bill_no', 
            bill_date='$bill_date', 
            total_amount='$total_amount',
            balance_amount='$balance_amount' 
        WHERE id='$id'
    ");

    // Update each item row
    foreach ($_POST['item_name'] as $i => $item) {

        $item    = mysqli_real_escape_string($myDB, $item);
        $qty     = (float)$_POST['qty'][$i];
        $amount  = (float)$_POST['amount'][$i];

        // Update Bill Item
        mysqli_query($myDB, "
            UPDATE rrsv_vendor_bill_items SET 
                qty       = '$qty',
                amount    = '$amount'
            WHERE bill_id='$id'
        ");

        // UPDATE expense entry (one per item)
        // mysqli_query($myDB, "
        //     UPDATE rrsv_expence SET 
        //         expence_name = '$item',
        //         amount       = '$amount'
        //         d_o_e        = '$bill_date',
        //         exp_desc     = 'Vendor Bill Expense'
        //     WHERE from_no = '$item_id'
        // ");

        // UPDATE PL entry
        // mysqli_query($myDB, "
        //     UPDATE rrsv_scl_pl SET 
        //         pl_date    = '$bill_date',
        //         exp_amount = '$amount'
        //         purpose    = '$item'
        //     WHERE bill = '$item_id'
        // ");
    }

    echo "<script>alert('Bill updated successfully'); window.location='manage_vendor_bill.php';</script>";
}
?>
