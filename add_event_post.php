<?php
//print_r($_POST);
// die;
session_start();


$token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$event_name = $myDB->escape_string(trim(addslashes($_POST['event_name'])));
$d_o_e = $myDB->escape_string(trim(addslashes($_POST['d_o_e'])));
$event_desc = $myDB->escape_string(trim(addslashes($_POST['event_desc'])));
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));
$name = strtoupper($event_name);
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

		/*$strsql="select * from class where class_name='".$class_name."' and id<>'".$id."'";
																																																																							$result=mysqli_query($myDB,$strsql) or die("Error into selecting category info by name and id post:".mysqli_connect_error());
																																																																							if(mysqli_num_rows($result) > 0) {
																																																																							
																																																																								$errcode=2;		//Duplicate check
																																																																								echo '<script language="javascript" type="text/javascript">';
																																																																								echo 'alert(" name you have entered is duplicate!.");';
																																																																								echo 'window.history.go(-1);';
																																																																								echo '</script>';
																																																																								exit();
																																																																							}*/

		$sqlUp = "update rrsv_event  set
				event_name           	='" . $event_name . "',
				event_desc              ='" . $event_desc . "',
				d_o_e         	='" . $d_o_e . "'
				
			

		    	where id							='" . $id . "'";

		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());

		if ($result) {
			echo '<script language="javascript" type="text/javascript">';
			echo 'alert("updated.");';
			echo 'window.location.href = "manage_event.php"';
			echo '</script>';

		}

	} else {		// Add Mode

		// 		$strsql="select * from rrsv_holiday where name='".$name."' and d_o_h!='".$d_o_h."'";
// 		$result=mysqli_query($myDB,$strsql) or die("Error into selecting course info by Class name post:".mysqli_connect_error());
// 		if(mysqli_num_rows($result) > 0) {

		//	$errcode=1;		//Duplicate check
// 			echo '<script language="javascript" type="text/javascript">';
// 			echo 'alert("name you have entired is duplicate.");';
// 			echo 'window.history.go(-1);';
// 			echo '</script>';
//         echo 2;
// 		exit();

		// 		}

		$sqlIn = "insert into rrsv_event 
						set
					event_name	='" . $event_name . "',
				event_desc	='" . $event_desc . "',	
				d_o_e ='" . $d_o_e . "',
				doc_path	='" . $filename . "'
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