
<?php

session_start();

unset($_SESSION['chat_logged_in']);
unset($_SESSION['chat_user_id']);
unset($_SESSION['chat_username']);
unset($_SESSION['chat_role']);

session_destroy();

header("Location: chat_login.php");
exit;
?>