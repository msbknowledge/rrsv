<?php
session_start();


$token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$income = $myDB->escape_string(trim(addslashes($_POST['income'])));
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));
$income = strtoupper($income);
if (isset($tokenpost) == $token) {



	if ($id > 0) {


		$sqlUp = "update  rrsv_income_head  set
				income	='" . $income . "'
				

	where id							='" . $id . "'";

		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());



		if ($result) {
			echo 1;

		}

	} else {		// Add Mode






		$sqlIn = "insert into  rrsv_income_head 
						set
				income	='" . $income . "'";



		$result = mysqli_query($myDB, $sqlIn);


		if ($result) {

			echo 0;


		}
	}
}
?>