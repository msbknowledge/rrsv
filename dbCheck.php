<?php
$DB_NAME="rasulpur_kgschool";
	$DB_USER="Rasulpur_kgschool";
	$DB_PASS="Rasulpur@1";
	$DB_HOST="localhost";
	
$connect = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$select="select * from scl_admin where admin_email='mainak.dutta93@gmail.com' and admin_pwd='45645645'";
   // echo $select;
    $query=mysqli_query($connect,$select);
    $total=mysqli_num_rows($query);



            $row=mysqli_fetch_array($query,MYSQLI_ASSOC);
            print_r($row['admin_id']);
?>