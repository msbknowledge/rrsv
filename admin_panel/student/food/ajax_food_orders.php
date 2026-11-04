<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

include '../../../include/config.php';
include '../../dbcon.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'=>'error',
        'msg'=>'Invalid request method'
    ]);
    exit;
}

if (isset($_POST['ajax']) && $_POST['ajax'] === 'cancel_order') {

    $student_id = $_SESSION['scl_reg_no'] ?? 0;
    if (!$student_id) {
        echo json_encode(['status'=>'error','msg'=>'Unauthorized']);
        exit;
    }

    $id = (int)$_POST['id'];
    if (!$id) {
        echo json_encode(['status'=>'error','msg'=>'Invalid request']);
        exit;
    }
    
    
//   echo json_encode([
//     'status' => 'ok',
//     'msg'    => 'Request successful ID: '.$id.' Student: '.$student_id,
// ]);
// exit;
     
    // mysqli_begin_transaction($con);

    // try {
    //     $row = mysqli_fetch_assoc(mysqli_query($con,"
    //         SELECT oi.price, oi.quantity, oi.status, oi.order_date
    //         FROM rrsv_food_order_items oi
    //         JOIN rrsv_food_orders o ON o.id = oi.order_id
    //         WHERE oi.id='$id' AND o.student_id='$student_id'
    //         FOR UPDATE
    //     "));

    //     if (!$row) throw new Exception('Order not found');
    //     if (strtotime($row['order_date']) < strtotime(date('Y-m-d')))
    //         throw new Exception('Past orders cannot be cancelled');
    //     if ($row['status'] === 'Cancelled')
    //         throw new Exception('Already cancelled');

    //     $refund = $row['price'] * $row['quantity'];

    //     mysqli_query($con,"UPDATE rrsv_food_order_items SET status='Cancelled' WHERE id='$id'");
    //     mysqli_query($con,"UPDATE rrsv_food_wallet SET balance=balance+$refund WHERE student_id='$student_id'");
    //     mysqli_query($con,"INSERT INTO rrsv_food_wallet_ledger(student_id,amount,type,description)VALUES ('$student_id',$refund,'Credit','Food order cancelled')");

    //     mysqli_commit($con);

    //     echo json_encode(['status'=>'ok','msg'=>'Order cancelled successfully']);
    // } catch (Exception $e) {
    //     mysqli_rollback($con);
    //     echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
    // }
    
    mysqli_begin_transaction($con);

try {

$row = mysqli_fetch_assoc(mysqli_query($con,"
    SELECT oi.*
    FROM rrsv_food_order_items oi
    JOIN rrsv_food_orders o ON o.id = oi.order_id
    WHERE oi.id='$id'
    AND o.student_id='$student_id'
    FOR UPDATE
"));

if(!$row)
    throw new Exception('Order not found');

if($row['status']=='Cancelled'){
    mysqli_commit($con);
    echo json_encode(['status'=>'ok','msg'=>'Already cancelled']);
    exit;
}

$refund = $row['price'] * $row['quantity'];
$txn_id = 'TXN'.date('ymdHis').rand(10,99);

// ✅ check ledger already exists
$ledger = mysqli_fetch_assoc(mysqli_query($con,"
    SELECT id FROM rrsv_food_wallet_ledger
    WHERE order_item_id='$id'
"));

if(!$ledger){

    mysqli_query($con,"
        UPDATE rrsv_food_wallet
        SET balance = balance + $refund
        WHERE student_id='$student_id'
    ");

    mysqli_query($con,"INSERT INTO rrsv_food_wallet_ledger(student_id,tns_id,amount,type,description,created_at,is_delete)
    VALUES('$student_id','$txn_id','$refund','Credit','Food order cancelled',NOW(),'No')");
}

mysqli_query($con,"
    UPDATE rrsv_food_order_items
    SET status='Cancelled'
    WHERE id='$id'
");

mysqli_commit($con);

echo json_encode([
    'status'=>'ok',
    'msg'=>'Order cancelled successfully'
]);

} catch(Exception $e){
    //mysqli_rollback($con);
    echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
}
    exit;
}
