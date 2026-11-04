<?php

if (isset($_SESSION["username"])) {
    session_destroy();
    header("location:index.php"); 
exit();
}
error_reporting(0); 
session_start();
include_once 'dbConnection.php';
$ref      = @$_GET['q'];
$username = $_POST['username'];
$password = $_POST['password'];
if (isset($_POST['student']) && !empty($_POST['username'])&& !empty($_POST['password'])) {

$result = mysqli_query($con, "SELECT *, name,due FROM user WHERE username = '$username' and password = '$password'") or die('Error');
$count = mysqli_num_rows($result);
if ($count == 1) {
    while ($row = mysqli_fetch_array($result)) {
        $name = $row['name'];
        $user_id = $row['id'];
		$d = $row['due'];
    }
if($d !== '0'){
	
  echo '<br/><h1 style="background: #09bffd;padding:20px;border-radius:8px;"><p style="background: #ddeef8;padding:5px;border-radius:8px;width: 96%;padding:15px;margin:10px 0 0"> ('.$username.')Please clear your dues by the due date. THEN LOGIN ,<font color=red> <br/> Otherwise  not allowed to give examination.  </font> <br/>      OR IMMEDIATELY CONTACT WITH YOUR   PRINCIPAL SIR(+917479044737)</p><h1>';

}
 else
{
    $_SESSION["name"]     = $name;
    $_SESSION["username"] = $username;
    $_SESSION["user_id"] = $user_id;

    header("location:students/account.php?q=1");
}
}
 else 
{
    header("location:$ref?w=Wrong Username or Password");
}
}//sudent
if (isset($_POST['admin']) && !empty($_POST['username'])&& !empty($_POST['password'])) {

    $result = mysqli_query($con, "SELECT username FROM admin WHERE username = '$username' and password = '$password'") or die('Error');

    $count = mysqli_num_rows($result);
    
    if ($count == 1) {
       
        $_SESSION["name"]= 'Admin';
        $_SESSION["key"]= '66552277A';
        $_SESSION["username"] = $username;
       header("location:dashboard.php?q=4");
    
    } 
    else{header("location:$ref?w=Warning : Access denied");}
}
if (isset($_POST['teacher']) && !empty($_POST['username'])&& !empty($_POST['password'])) {
    
$result = mysqli_query($con, "SELECT username FROM teacher WHERE username = '$username' and password = '$password'") or die('Error');

$count = mysqli_num_rows($result);

if ($count == 1) {
   
    $_SESSION["key"]      = '66552277T';
    $_SESSION["name"]     = 'teacher';
    $_SESSION["username"] = $username;

    header("location:teach/dash.php?q=10");

} else
{
    header("location:$ref?w=Warning : Access denied");
}
}
?>