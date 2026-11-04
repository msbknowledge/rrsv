<?php
// print_r($_POST);
// die;
session_start();

$token = $_SESSION['_token'];
include('../../include/dbcon.php');
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$title = $myDB->escape_string(trim(addslashes($_POST['title'])));
$plan_date = $myDB->escape_string(trim(addslashes($_POST['plan_date'])));
$reminder_date  = $myDB->escape_string(trim(addslashes($_POST['reminder_date'])));
$description = $myDB->escape_string(trim(addslashes($_POST['description'])));
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));
$name = strtoupper($title);
$sub_file = "";
$uploadflg = 1;
$file_base = "event_doc";
$sub_file = $file_base;
if (is_dir($sub_file) == false) {	// Make Directory
	mkdir($sub_file, 0777);
}
$store_file_name = $_FILES['file']['name'];
$filename = $sub_file . "/" . $store_file_name;

$file_flag = 0;
if ($store_file_name == "") {
	//$store_file_name=$prvfilenm;
	$uploadflg = 0;
}
$prt_image = $_FILES['file']['name'];
$image_type = $_FILES['file']['type'];
if ($uploadflg == 1) {
	if ($file_flag == 0) {
		if (move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
			if (!file_exists($filename)) {
				echo 33;
				echo '<script language="javascript" type="text/javascript">';
				echo 'alert("file not transfer.");';
				echo 'window.history.go(-1);';
				echo '</script>';

			}

		}

	}
}

// $image = $store_file_name;
// echo $filename;
// die;

if (isset($tokenpost) == $token) {
	if ($id != 0) {
		$sqlUp = "update rrsv_calender_plan set
				title           	='".$title."',
				description         ='".$description."',
				plan_date         	='".$plan_date."',
				reminder_date 	    ='".$reminder_date."'
				where id	        ='".$id."'";
		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());
		if ($result) {
			echo '<script language="javascript" type="text/javascript">';
			echo 'alert("updated.");';
			echo 'window.location.href = "manage_event.php"';
			echo '</script>';
		}
	} else {	
		$sqlIn = "insert into rrsv_calender_plan set
					title	        ='".$title."',
    				description	    ='".$description."',	
    				plan_date       ='".$plan_date."',
    				reminder_date 	='".$reminder_date."'
				";
		$result = mysqli_query($myDB, $sqlIn);
		if ($result) {
			echo '<script language="javascript" type="text/javascript">';
			echo 'alert("submitted.");';
			echo 'window.location.href = "manage_event.php"';
			echo '</script>';
		}


	}
}
?>