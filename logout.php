<?php
	session_start();
	$_SESSION['mid'] = "";
	$_SESSION['shop_userlogedin']="";
	$_SESSION['admin_email']="";
	session_unset();	
?>

<script language="javascript" type="text/javascript">
	//alert('You have been logged out !');
	window.location.href="index.php";
</script>