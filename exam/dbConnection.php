<?php
//	$con= new mysqli('localhost','u353140863_exam','BR66MK55@msb','u353140863_exam')or die("Could not connect to mysql".mysqli_error($con));
$con = new mysqli('localhost', 'u353140863_exam', 'BR66MK55@msb', 'u353140863_exam');

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
