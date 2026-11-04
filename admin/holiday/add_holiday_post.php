<?php
session_start();


$token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$name = $myDB->escape_string(trim(addslashes($_POST['name'])));
$d_o_h = $myDB->escape_string(trim(addslashes($_POST['d_o_h'])));

$end_date = $myDB->escape_string(trim(addslashes($_POST['end_date'])));
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));
$name = strtoupper($name);
if (isset($tokenpost) == $token) {



	if ($id > 0) {

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

		$sqlUp = "update rrsv_holiday  set
				name           	='" . $name . "',
				
				d_o_h         	='" . $d_o_h . "',
				end_date         	='" . $end_date . "'
				
			

		    	where id							='" . $id . "'";

		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());

		if ($result) {
			echo 1;
			// echo '<script language="javascript" type="text/javascript">';
			// echo 'alert("updated.");';
			// echo 'window.location.href = "add_holiday.php";';
			// echo '</script>';

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

		$sqlIn = "insert into rrsv_holiday 
						set
					name	='" . $name . "',
				d_o_h ='" . $d_o_h . "',
				
				end_date         	='" . $end_date . "'";




		$result = mysqli_query($myDB, $sqlIn);

		if ($result) {
			//echo json_encode(0);
			echo 0;
			// echo '<script language="javascript" type="text/javascript">';
			// echo 'alert("inserted.");';
			// echo 'window.location.href = "add_holiday.php";';
			// echo '</script>';
		}


	}
}
?>