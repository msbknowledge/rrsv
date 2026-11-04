
<?php
session_start();

require_once 'dbConnection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = 'Username and password are required.';

    } else {

        $stmt = mysqli_prepare($con, "
            SELECT id, username, password, role, status
            FROM chat_user
            WHERE username = ?
            LIMIT 1
        ");

        if (!$stmt) {

            $error = 'Database query error: ' . mysqli_error($con);

        } else {

            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if (!$result) {

                $error = 'Database result error: ' . mysqli_error($con);

            } else {

                $user = mysqli_fetch_assoc($result);

                if (!$user) {

                    $error = 'Invalid username or password.';

                } elseif ($user['status'] !== 'active') {

                    $error = 'Your account is inactive.';

                } elseif ($password !== $user['password']) {

                    // Plain password comparison
                    $error = 'Invalid username or password.';

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | LOGIN SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    session_regenerate_id(true);

                    $_SESSION['chat_logged_in'] = true;
                    $_SESSION['chat_user_id'] = (int)$user['id'];
                    $_SESSION['chat_username'] = $user['username'];
                    $_SESSION['chat_role'] = $user['role'];

                    header('Location: chats_message.php');
                    exit;
                }
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Chat Login</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: Arial, Helvetica, sans-serif;

            background: #f4f6f9;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-box {
            background: #fff;

            padding: 35px;

            border-radius: 10px;

            box-shadow:
                0 4px 20px rgba(0, 0, 0, .10);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 65px;
            height: 65px;

            margin: auto;

            border-radius: 50%;

            background: #007bff;

            color: #fff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 30px;
        }

        h2 {
            text-align: center;

            margin: 15px 0 5px;

            color: #333;
        }

        .subtitle {
            text-align: center;

            color: #777;

            margin-bottom: 25px;

            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;

            margin-bottom: 7px;

            color: #444;

            font-weight: 600;

            font-size: 14px;
        }

        .form-control {
            width: 100%;

            height: 45px;

            border: 1px solid #ddd;

            border-radius: 6px;

            padding: 0 13px;

            font-size: 15px;

            outline: none;
        }

        .form-control:focus {
            border-color: #007bff;

            box-shadow:
                0 0 0 2px rgba(0, 123, 255, .10);
        }

        .btn-login {
            width: 100%;

            height: 45px;

            border: none;

            border-radius: 6px;

            background: #007bff;

            color: #fff;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;
        }

        .btn-login:hover {
            background: #0069d9;
        }

        .error {
            background: #f8d7da;

            color: #721c24;

            border: 1px solid #f5c6cb;

            padding: 11px;

            margin-bottom: 18px;

            border-radius: 6px;

            font-size: 14px;
        }

        .footer {
            text-align: center;

            margin-top: 20px;

            color: #999;

            font-size: 12px;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <div class="logo">

            <div class="logo-icon">
                💬
            </div>

        </div>

        <h2>Chat Login</h2>

        <div class="subtitle">
            Sign in to continue
        </div>


        <?php if ($error !== ''): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form method="POST" autocomplete="off">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="Enter username"
                    autocomplete="username"
                    required
                    autofocus
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-login">
                Login
            </button>

        </form>


        <div class="footer">
            Chat Communication System
        </div>

    </div>

</div>

</body>

</html>

