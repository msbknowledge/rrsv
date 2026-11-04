<?php
session_start();


$token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$name = $myDB->escape_string(trim(addslashes($_POST['name'])));
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));
//$name = strtoupper($name);
if (isset($tokenpost) == $token) {



	if ($id > 0) {


		$sqlUp = "update stock_master set
				name	='" . $name . "'
				

	where id							='" . $id . "'";

		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());



		if ($result) {
			echo 1;

		}

	} else {		// Add Mode






		$sqlIn = "insert into  stock_master 
						set
				name	='" . $name . "',
				field_name	='" . $name . "',
				status = '0'";



		$result = mysqli_query($myDB, $sqlIn);


		if ($result) {

			echo 0;


		}
	}
}
?>