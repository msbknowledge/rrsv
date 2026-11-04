<?php
include('include/dbcon.php');
		$class_name=$myDB->escape_string(trim($_POST['class_name']));
	$scl_session=$myDB->escape_string(trim($_POST['scl_session']));
	$sql="select * from rrsv_fee where scl_class='$class_name' and scl_session='$scl_session' ";
	$res=mysqli_query($myDB,$sql);
	$rows=mysqli_fetch_array($res);
echo	$rr=$rows['ad_form_charge'];
	

?>