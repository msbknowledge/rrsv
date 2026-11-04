<?php
session_start();



if (!empty($_SESSION['mid'])) {
	header("location:home.php");
	exit;
}

if ($_POST) {
include('include/dbcon.php');

// print_r($_POST['_token']);
// die();

	$select="select * from rrsv_admin where admin_email='".trim(mysqli_real_escape_string($myDB,$_POST['email']))."' and admin_pwd='".trim(mysqli_real_escape_string($myDB,$_POST['password']))."'";

    $query=mysqli_query($myDB,$select);
    $total=mysqli_num_rows($query);


    if ($total > 0) {
        $row=mysqli_fetch_array($query,MYSQLI_ASSOC);

        $_SESSION['mid']=$row['admin_id'];
        $_SESSION['_token'] = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32); // 32 characters, without /=+

       
        echo "success";
    }
    else{
        echo "error";
    }
}
?>