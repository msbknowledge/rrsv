<?php
include('include/dbcon.php');

	$oldpassword=$_POST['oldpassword'];
	$newpassword=$_POST['newpassword'];


	$errcode = 0;	// used while validating the email id (unique)

	$checkUserSQL = "select * from rrsv_admin where admin_pwd='$oldpassword'";

	//echo $checkUserSQL;

	//exit();

	$checkUserResultSet = mysqli_query($myDB,$checkUserSQL) or die(mysql_error());

	if(mysqli_num_rows($checkUserResultSet) > 0)

	{

		$errcode = 1;	// Duplicate email id

	}

	
	if($errcode == 1)	// since the email id user has entered is duplicate, user has to be redirected to change the email id

	{

	$insert_query ="Update rrsv_admin set admin_pwd  ='$newpassword' where admin_pwd='$oldpassword'";

		if(mysqli_query($myDB,$insert_query)){

	
		//echo json_encode(0);
		 echo 0;
		}

	}

	else{

		echo "<script language='JavaScript'>";
		echo "window.location.href='add_chanepassword.php?retcode=0';";
		echo "</script>";
		exit();		

	}

?>

