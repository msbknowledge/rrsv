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
$scl_session=date('Y');

if (isset($_POST['student']) && !empty($_POST['username'])&& !empty($_POST['password'])) {

$result = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no = '$username' and password = '$password' and scl_session = '$scl_session'") or die('Error');
$count = mysqli_num_rows($result);
//print_r($count);die();
if ($count >= 1) {
    while ($row = mysqli_fetch_array($result)) {
        //print_r($row);die();
        $scl_reg_no = $row['scl_reg_no'];
        $name = $row['scl_name'];
        $class = $row['scl_class'];
        $session = $row['scl_session'];
        $user_id = $row['id'];
		$d = $row['admission_due'];
    }
if($d !== '0'){
	
  echo '<br/><h1 style="background: #09bffd;padding:20px;border-radius:8px;"><p style="background: #ddeef8;padding:5px;border-radius:8px;width: 96%;padding:15px;margin:10px 0 0"> ('.$username.')Please clear your dues by the due date. THEN LOGIN ,<font color=red> <br/> Otherwise  not allowed to give examination.  </font> <br/>      OR IMMEDIATELY CONTACT WITH YOUR   PRINCIPAL SIR(+917479044737)</p><h1>';

}
 else
{
    $_SESSION["name"]     = $name;
    $_SESSION["username"] = $name;
    $_SESSION["class"]     = $class;
    $_SESSION["user_id"] = $user_id;
    $_SESSION["class"]     = $class;
    $_SESSION["session"] = $session;
 $_SESSION["scl_reg_no"] = $scl_reg_no;
    header("location:students/account.php?q=1");
}
}
 else 
{
    header("location:$ref?w=Wrong Username or Password");
}
}
//sudent
if (isset($_POST['admin']) && !empty($_POST['username'])&& !empty($_POST['password'])) {
    
    $result = mysqli_query($con, "SELECT * FROM rrsv_admin WHERE admin_user ='$username' and admin_pwd ='$password'") or die('Error');
    $count = mysqli_num_rows($result);
   // print_r($count);die();
    if ($count == 1) {
       
        $_SESSION["name"]= 'Admin';
        $_SESSION["key"]= '66552277A';
        $_SESSION["username"] = $username;
        header("location:dashboard.php?q=4");
    
    } 
    else{header("location:$ref?w=Warning : Access denied");}
}
if (isset($_POST['teacher']) && !empty($_POST['username'])&& !empty($_POST['password'])) {
    
$result = mysqli_query($con, "SELECT * FROM rrsv_teacher WHERE tech_phone_no = '$username' and password = '$password'") or die('Error');

$count = mysqli_num_rows($result);

if ($count == 1) {
    while ($row = mysqli_fetch_array($result)) {
        $name = $row['username'];
        $id = $row['id'];
        $branch_id = $row['branch_id'];
		$college_id = $row['college_id'];
    }
    $_SESSION["key"]      = '66552277T';
    $_SESSION["name"]     = 'teacher';
    $_SESSION["username"] = $username;
     $_SESSION["username"] = $username;

    header("location:teach/dash.php?q=10");

} else
{
    header("location:$ref?w=Warning : Access denied");
}
}
?>