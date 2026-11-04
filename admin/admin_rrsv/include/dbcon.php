<?php
//echo 8475784;
	$DB_NAME="rrsv";
	$DB_USER="root";
	$DB_PASS="";
	$DB_HOST="localhost";

if($myDB= mysqli_connect($DB_HOST,$DB_USER,$DB_PASS))
	{
		mysqli_select_db($myDB,$DB_NAME);	
	}
	else {
	    echo "db not connect";
	}



?>