<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}

$item_id = (int)$_GET['item_id'];

$q = mysqli_query($con,"
    SELECT quantity 
    FROM rrsv_food_stock 
    WHERE item_id='$item_id'
");

if(mysqli_num_rows($q)){
    $row = mysqli_fetch_assoc($q);
    echo json_encode(['stock' => $row['quantity']]);
} else {
    echo json_encode(['stock' => 0]);
}
