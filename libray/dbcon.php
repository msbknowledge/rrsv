<?php
/*	$DB_NAME="shishukalyan";
	$DB_USER="root";
	$DB_PASS="";
	$DB_HOST="localhost";*/


	
	$DB_NAME="rasulpur_kgschool";
	$DB_USER="Rasulpur_kgschool";
	$DB_PASS="Rasulpur@1";
	$DB_HOST="localhost";

	if($myDB= mysqli_connect($DB_HOST,$DB_USER,$DB_PASS))
	{
		mysqli_select_db($myDB,$DB_NAME);	
	}
	else {
	    echo "db not connect";
	}



?>