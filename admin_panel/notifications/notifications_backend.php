<?php
session_start();
include '../dbcon.php';

$currentUserRole = $_SESSION['role'];
$currentUserName = $_SESSION['user_data']['name'];
$currentUserClass = $_SESSION['user_data']['class'] ?? '';

// Insert message
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['message'])){
    $msg = mysqli_real_escape_string($con,$_POST['message']);
    $receiver_type = mysqli_real_escape_string($con,$_POST['receiver_type']);
    mysqli_query($con,"INSERT INTO rrsv_notifications (receiver_type, receiver_name, message) 
        VALUES ('$receiver_type','$currentUserName','$msg')");
    exit;
}

// Update typing status
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['typing'])){
    $_SESSION['typing'] = time();
    exit;
}

// Check typing
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['typing_check'])){
    $typing = 0;
    foreach($_SESSION as $k=>$v){
        if(strpos($k,'typing')!==false && time()-$v<3) $typing++;
    }
    echo json_encode(['typing'=>$typing]);
    exit;
}

// Fetch messages
$result = mysqli_query($con,"SELECT * FROM rrsv_notifications ORDER BY created_at ASC LIMIT 100");
$messages=[];
while($row=mysqli_fetch_assoc($result)){
    $messages[]=[
        'sender_name'=>$row['receiver_name'],
        'sender_role'=>$row['receiver_type'],
        'sender_class'=>$row['receiver_class'] ?? '',
        'message'=>$row['message'],
        'created_at'=>date('h:i A',strtotime($row['created_at']))
    ];
}
header('Content-Type: application/json');
echo json_encode($messages);
