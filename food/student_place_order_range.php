<?php
session_start();
include('includes/config.php');

$student_id = $_SESSION['student_id'];
$food_id    = $_POST['food_id'];
$meal_type  = $_POST['meal_type'];
$from       = $_POST['from_date'];
$to         = $_POST['to_date'];

/* 1️⃣ Get price */
$p = mysqli_fetch_assoc(mysqli_query($con,"
    SELECT price FROM rrsv_menu_package WHERE id='$food_id'
"));
$price = $p['price'];

/* 2️⃣ Build date loop */
$dates = [];
$start = new DateTime($from);
$end   = new DateTime($to);
$end->modify('+1 day');

while($start < $end){
    $dates[] = $start->format('Y-m-d');
    $start->modify('+1 day');
}

$total_amount = count($dates) * $price;

/* 3️⃣ Wallet check */
$w = mysqli_fetch_assoc(mysqli_query($con,"
    SELECT balance FROM rrsv_student_wallet WHERE student_id='$student_id'
"));

if($w['balance'] < $total_amount){
    $_SESSION['error'] = "Insufficient wallet balance";
    header('location:student_weekly_menu.php');
    exit;
}

/* 4️⃣ Loop insert + stock out */
foreach($dates as $date){

    // Prevent duplicate
    $chk = mysqli_query($con,"
        SELECT id FROM rrsv_food_orders
        WHERE student_id='$student_id'
        AND food_id='$food_id'
        AND meal_type='$meal_type'
        AND order_date='$date'
    ");

    if(mysqli_num_rows($chk)==0){

        mysqli_query($con,"
            INSERT INTO rrsv_food_orders
            (student_id,food_id,meal_type,order_date,price)
            VALUES
            ('$student_id','$food_id','$meal_type','$date','$price')
        ");

        /* Stock OUT */
        mysqli_query($con,"
            INSERT INTO rrsv_food_stock_ledger
            (food_id,type,ref_type,qty,ref_date)
            VALUES
            ('$food_id','OUT','SALE',1,'$date')
        ");
    }
}

/* 5️⃣ Wallet debit */
mysqli_query($con,"
    UPDATE rrsv_student_wallet
    SET balance = balance - $total_amount
    WHERE student_id='$student_id'
");

$_SESSION['success'] = "Meal ordered successfully";
header('location:rrsv_food_daily_menu.php');
