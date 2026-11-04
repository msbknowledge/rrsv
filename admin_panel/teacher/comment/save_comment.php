<?php
session_start();
include '../../dbcon.php';

// Validate required fields
if (empty($_POST['behaviour_id']) || empty($_POST['comment']) || empty($_POST['user_type']) || empty($_POST['teacher_id'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit;
}

$behaviour_id = intval($_POST['behaviour_id']);
$user_type = mysqli_real_escape_string($con, $_POST['user_type']); // 'teacher' or 'student'
$user_id = intval($_POST['teacher_id']); // actually sender id
$student_id = intval($_POST['student_id']); // actually sender id
$comment = trim(mysqli_real_escape_string($con, $_POST['comment']));

// 1️⃣ Insert the new comment
$query = "
    INSERT INTO rrsv_behaviour_comments (behaviour_id, user_type, user_id, student_id, comment, created_at)
    VALUES ($behaviour_id, '$user_type', $user_id, $student_id, '$comment', NOW())
";

if (!mysqli_query($con, $query)) {
    echo "Error inserting comment: " . mysqli_error($con);
    exit;
}

$comment_id = mysqli_insert_id($con);

// 2️⃣ Determine the receiver
if ($user_type === 'teacher') {
    $result = mysqli_query($con, "SELECT student_id FROM rrsv_student_behaviour WHERE id = $behaviour_id");
    $data = mysqli_fetch_assoc($result);
    $receiver_id = $data['student_id'];
    $receiver_type = 'student';
} else {
    $result = mysqli_query($con, "SELECT teacher_id FROM rrsv_student_behaviour WHERE id = $behaviour_id");
    $data = mysqli_fetch_assoc($result);
    $receiver_id = $data['teacher_id'];
    $receiver_type = 'teacher';
}

// 3️⃣ Create notification message
$message = ($user_type === 'teacher') 
    ? "Teacher commented on your behaviour note."
    : "Student replied to your behaviour note.";

// 4️⃣ Insert into notifications
$notify = "
    INSERT INTO rrsv_notifications 
    (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, comment_id, message, seen, created_at)
    VALUES 
    ('$receiver_type', $receiver_id, '$user_type', $user_id, $behaviour_id, $comment_id, '$message', 0, NOW())
";

if (!mysqli_query($con, $notify)) {
    echo "Error inserting notification: " . mysqli_error($con);
    exit;
}

echo "success";
?>
