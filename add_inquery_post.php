<?php
session_start();
error_reporting(1);
include('include/dbcon.php');
$token = $_SESSION['_token'];
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$class_name = $myDB->escape_string(trim(addslashes($_POST['class_name'])));
$scl_session = $myDB->escape_string(trim(addslashes($_POST['scl_session'])));
$name = $myDB->escape_string(trim(addslashes($_POST['name'])));
$phone = $myDB->escape_string(trim(addslashes($_POST['phone'])));
$price = $myDB->escape_string(trim(addslashes($_POST['price'])));
$d_o_i = $myDB->escape_string(trim(addslashes($_POST['d_o_i'])));
$reg_no = $myDB->escape_string(trim(addslashes($_POST['reg_no']))) ?? '';
$inquery_type = $myDB->escape_string(trim(addslashes($_POST['inquery_type'])));
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));

// Generate admission code: RRSV + 4-digit padded number
// Get last ID from rrsv_inquery
$sqlLast = "SELECT id FROM rrsv_inquery ORDER BY id DESC LIMIT 1";
$resultLast = mysqli_query($myDB, $sqlLast);
$rowLast = mysqli_fetch_assoc($resultLast);
$nextId = ($rowLast) ? $rowLast['id'] + 1 : 1;

$digits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
$letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
$admission_code = "RRSV_{$digits}_{$letters}";

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

		$sqlUp = "update rrsv_inquery 		set
		class_name	='" . $class_name . "',
				scl_session         ='" . $scl_session . "',
				name	            ='" . $name . "',
				phone               ='" . $phone . "',
				price               ='" . $price . "'
		    	where id			='" . $id . "'";

		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());

		if ($result) {
			echo 1;

		}

	} else {

		//	$currenr=date('Y-m-d');

		$sqlIn = "insert into rrsv_inquery 
						set
				class_name	='" . $class_name . "',
				scl_session ='" . $scl_session . "',
				name	='" . $name . "',
				phone  ='" . $phone . "',
				d_o_i  ='" . $d_o_i . "',
				price ='" . $price . "',
				reg_no ='" . $reg_no . "',
          		admission_code ='" . $admission_code . "'";

		$result = mysqli_query($myDB, $sqlIn);
		$lstid = $myDB->insert_id;
		//	$regid=$scl_session.'-'.$class_name.'/'.$lstid;
		$curmonth = date('m');
		$curyear = date('Y');

		$regid = "RRSV" . '-' . $curmonth . '-' . $curyear . '-' . $lstid;
		$Inpl = "insert into rrsv_scl_pl set 	pl_date='" . $d_o_i . "',bill='" . $regid . "',income_amount='" . $price . "',session='" . $scl_session . "',purpose='From Sell'";
		$res = mysqli_query($myDB, $Inpl);

		$Upsql = "update rrsv_inquery set from_no='" . $regid . "', inquery_type='" . $inquery_type . "' where id=$lstid";
		$Upres = mysqli_query($myDB, $Upsql);
		if ($result) {
			echo 0;
		}
	}
}
?>