<?php
session_start();
if (isset($_SESSION["username"])) {
    session_destroy();
}
include_once 'dbConnection.php';

$ref      = @$_GET['q'];

$username = $_POST['uname'];

$password = $_POST['password'];



$username = stripslashes($username);

$username = addslashes($username);

$password = stripslashes($password);

$password = addslashes($password);

$result = mysqli_query($con, "SELECT username FROM teacher WHERE username = '$username' and password = '$password'") or die('Error');

$count = mysqli_num_rows($result);

if ($count == 1) {
    if (isset($_SESSION['username'])) {

        session_unset();

    }
    while ($row = mysqli_fetch_array($result)) {
        $name = $row['name'];
        $user_id = $row['id'];
		$d = $row['due'];
    }
 
    $_SESSION["key"]      = '66552277T';
    $_SESSION["name"]     = 'teacher';
    $_SESSION["username"] = $username;

    header("location:teach/dash.php?q=10");

} else

    header("location:$ref?w=Warning : Access denied");

?>