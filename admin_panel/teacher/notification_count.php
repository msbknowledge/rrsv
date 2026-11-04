<?php
session_start();
include '../dbcon.php'; // Adjust the path to your actual DB connection file

// Validate session
if (!isset($_SESSION['user_data']) || !isset($_SESSION['role'])) {
    echo 0;
    exit;
}

$user = $_SESSION['user_data'];
$user_id = $user['id'] ?? null;
$user_role = $_SESSION['role'] ?? '';

if (!$user_id || !$user_role) {
    echo 0;
    exit;
}

// 🔔 Count unread notifications
$query = "
    SELECT COUNT(*) AS unread_count
    FROM rrsv_notifications
    WHERE receiver_id = '$user_id'
      AND receiver_type = '$user_role'
      AND seen = 0
";

$result = mysqli_query($con, $query);
$data = mysqli_fetch_assoc($result);

// Return the count
echo (int)($data['unread_count'] ?? 0);
?>
