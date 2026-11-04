<?php
session_start();

include('dbConnection.php');

	
	if(ISSET($_POST['save'])){
		$s_id = $_POST['s_id'];
		$q=mysqli_query($con,"select s_name from t_user where s_id='$s_id'");
$n=  mysqli_fetch_assoc($q);
$stname= $n['s_name'];
		$file_name = $_FILES['file']['name'];
		$file_type = $_FILES['file']['type'];
		$file_temp = $_FILES['file']['tmp_name'];
		$location = "files/".$stname."/".$file_name;
		date_default_timezone_set("Asia/Kolkata");
		$date = date("Y-m-d, h:i A");
		if(!file_exists("files/".$stname)){
			mkdir("files/".$stname);
		}
		
		if(move_uploaded_file($file_temp, $location)){
			mysqli_query($con, "INSERT INTO `storage` VALUES(null, '$file_name', '$file_type', '$date', '$s_id')") or die(mysqli_error($con));
			header('location: student_profile1.php');
		}
	}
?>