<?php
session_start();
require_once 'dbConnection.php';

if (
    !isset($_SESSION['chat_logged_in']) ||
    $_SESSION['chat_logged_in'] !== true
) {
    header("Location: chat_login.php");
    exit;
}

$user_id = (int) $_SESSION['chat_user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($old_password === '' || $new_password === '') {
        $error = 'Please enter all required fields.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New password and confirm password do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {

        // Get current password
        $stmt = mysqli_prepare($con, "
            SELECT password
            FROM chat_user
            WHERE id = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {

            $error = 'User not found.';

        } elseif ($old_password !== $user['password']) {

            $error = 'Current password is incorrect.';

        } else {

            // Current system uses plain-text password
            $update = mysqli_prepare($con, "
                UPDATE chat_user
                SET password = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $update,
                "si",
                $new_password,
                $user_id
            );

            if (mysqli_stmt_execute($update)) {
                $message = 'Password updated successfully.';
            } else {
                $error = 'Unable to update password.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 40px;
        }

        .box {
            width: 400px;
            max-width: 100%;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,.1);
        }

        h2 {
            margin-top: 0;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Change Password</h2>

    <?php if ($message): ?>
        <div class="success">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Current Password</label>
        <input
            type="password"
            name="old_password"
            required
        >

        <label>New Password</label>
        <input
            type="password"
            name="new_password"
            required
        >

        <label>Confirm New Password</label>
        <input
            type="password"
            name="confirm_password"
            required
        >

        <button type="submit">
            Update Password
        </button>

    </form>

</div>

</body>
</html>