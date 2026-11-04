<?php
include '../../dbcon.php';
session_start();

$behaviour_id = $_POST['behaviour_id'];
$comment = mysqli_real_escape_string($con, $_POST['comment']);
$user_type = $_SESSION['role']; // 'teacher' or 'student'
$user_id = $_SESSION['user_data']['id'];
$parent_id = $_POST['parent_id'] ?? NULL;

// Insert comment
mysqli_query($con, "
  INSERT INTO rrsv_behaviour_comments (behaviour_id, user_type, user_id, parent_id, comment)
  VALUES ('$behaviour_id', '$user_type', '$user_id', " . ($parent_id ?: "NULL") . ", '$comment')
");

$comment_id = mysqli_insert_id($con);

// Fetch the related record for notification
$res = mysqli_query($con, "SELECT teacher_id, student_id FROM rrsv_student_behaviour WHERE id = '$behaviour_id'");
$row = mysqli_fetch_assoc($res);

// Determine receiver
if ($user_type == 'teacher') {
  $receiver_type = 'student';
  $receiver_id = $row['student_id'];
  $message = "New comment from your teacher.";
} else {
  $receiver_type = 'teacher';
  $receiver_id = $row['teacher_id'];
  $message = "New reply from student.";
}

// Insert notification
mysqli_query($con, "
  INSERT INTO rrsv_notifications (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, comment_id, message)
  VALUES ('$receiver_type', '$receiver_id', '$user_type', '$user_id', '$behaviour_id', '$comment_id', '$message')
");

echo "success";
?>
