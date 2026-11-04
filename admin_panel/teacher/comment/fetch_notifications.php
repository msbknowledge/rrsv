<?php
include '../../dbcon.php';
session_start();
$user_type = $_SESSION['role'];
$user_id = $_SESSION['user_data']['id'];

$res = mysqli_query($con, "
  SELECT * FROM rrsv_notifications 
  WHERE receiver_type='$user_type' AND receiver_id='$user_id' AND seen=0 
  ORDER BY id DESC
");

$notifications = [];
while($row = mysqli_fetch_assoc($res)){
  $notifications[] = $row;
}

echo json_encode($notifications);
?>
