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
// ✅ Calculate count_of_day
if ($end_date != '0000-00-00' && !empty($end_date)) {
	// inclusive difference (+1)
	$date1 = new DateTime($d_o_h);
	$date2 = new DateTime($end_date);
	$diff = $date1->diff($date2)->days + 1;
} else {
	$diff = 1;
}
if (isset($tokenpost) == $token) {
	if ($id > 0) {
		$sqlUp = "update rrsv_holiday  set
				name           	='" . $name . "',
				d_o_h         	='" . $d_o_h . "',
				end_date         	='" . $end_date . "',
				count_of_day = '" . $diff . "'
                WHERE id = '" . $id . "'";
		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());

		if ($result) {
			echo 1;

		}

	} else {	

		$sqlIn = "insert into rrsv_holiday set
				name	='" . $name . "',
				d_o_h ='" . $d_o_h . "',
				end_date    = '" . $end_date . "',
                count_of_day = '" . $diff . "'";
		$result = mysqli_query($myDB, $sqlIn);
		if ($result) {
			echo 0;
			
		}


	}
}
?>