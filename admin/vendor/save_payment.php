<?php
include('../../include/dbcon.php');

$bill_id     = $_POST['bill_id'];
$pay_date    = $_POST['pay_date'];
$paid_amount = $_POST['paid_amount'];
$remarks     = $_POST['remarks'];
     $bill = mysqli_fetch_assoc(mysqli_query($myDB, "
      SELECT * FROM rrsv_vendor_bills WHERE id='$bill_id'
    "));
    $total_amount = $bill['total_amount'];
    $item_name = $bill['item_name'];
    $exp_amount = $bill['paid_amount']+$paid_amount;
    $session = $bill['session'];
    
if($exp_amount<= $total_amount){
    mysqli_query($myDB, "
      INSERT INTO rrsv_vendor_bill_payments (bill_id, pay_date, paid_amount, remarks)
      VALUES ('$bill_id', '$pay_date', '$paid_amount', '$remarks')
    ");
    
    
    $query = mysqli_query($myDB, "
      SELECT SUM(paid_amount) AS total_paid
      FROM rrsv_vendor_bill_payments
      WHERE bill_id='$bill_id'
    ");
    
    $data = mysqli_fetch_assoc($query);
    $total_paid = $data['total_paid'];
    
   
    
    
    $balance = $total_amount - $total_paid;
    
    
    mysqli_query($myDB, "
      UPDATE rrsv_vendor_bills 
      SET paid_amount='$total_paid', balance_amount='$balance'
      WHERE id='$bill_id'
    ");
    
    /*---------------------------------------------------
      5. Update rrsv_expence.amount (Add paid_amount)
         Matching ----> from_no = bill_id
    ---------------------------------------------------*/
    $expRow = mysqli_fetch_assoc(mysqli_query($myDB, "
        SELECT bill_no 
        FROM rrsv_vendor_bills
        WHERE id = '$bill_id'
    "));
$rawBill = $expRow['bill_no'];   // VB926
 $expense_id = (int) preg_replace('/\D/', '', $rawBill); // 926
    //$expense_id = $expRow['bill_no'];
//print_r($expense_id );die();
    if($expense_id ){
        /*---------------------------------------------------
          6. Update rrsv_expence.amount
        ---------------------------------------------------*/
        mysqli_query($myDB, "
            UPDATE rrsv_expence
            SET amount = amount + $paid_amount, d_o_e='$pay_date',exp_desc='$item_name'
            WHERE id = '$expense_id'
        ");
        
        /*---------------------------------------------------
          7. Update rrsv_scl_pl.exp_amount
        ---------------------------------------------------*/
          mysqli_query($myDB, "
                INSERT INTO rrsv_scl_pl
                SET
                    pl_date    = '$pay_date',
                    exp_amount = '$paid_amount',
                    bill       = '$rawBill',
                    purpose    = '$item_name',
                    session    = '$session'
            ");
        // mysqli_query($myDB, "
        //     UPDATE rrsv_scl_pl
        //     SET exp_amount = exp_amount + $paid_amount ,
        //             pl_date    = '$pay_date'
        //     WHERE bill = '$expense_id'
        // ");
        echo "<script>
        alert('Payment added successfully!');
        window.location='manage_vendor_bill.php';
        </script>";
    }else{
        echo "<script>
        alert('Payment fail !');
        window.location='manage_vendor_bill.php';
        </script>";
    }
}else{
        echo "<script>
        alert('Payment exceeds bill amount !');
        window.location='manage_vendor_bill.php';
        </script>";
    }

?>
