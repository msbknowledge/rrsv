<?php
session_start();
include('../../include/dbcon.php');

$id = intval($_POST['id'] ?? 0);
$name = $myDB->real_escape_string(trim($_POST['name'] ?? ''));
$mobile = $myDB->real_escape_string(trim($_POST['mobile'] ?? ''));
$email = $myDB->real_escape_string(trim($_POST['email'] ?? ''));
$address = $myDB->real_escape_string(trim($_POST['address'] ?? ''));
$description = $myDB->real_escape_string(trim($_POST['description'] ?? ''));
$status = $myDB->real_escape_string(trim($_POST['status'] ?? 'Active'));

if ($id > 0) {
	// Update
	$sqlUp = "UPDATE rrsv_vendor SET name='$name', mobile='$mobile', email='$email', address='$address', description='$description', status='$status' WHERE id=$id";
	$result = mysqli_query($myDB, $sqlUp);
	echo $result ? 1 : 3;
} else {
	// Check duplicate
	$check = mysqli_query($myDB, "SELECT id FROM rrsv_vendor WHERE name='$name'");
	if (mysqli_num_rows($check) > 0) {
		echo 2; // duplicate
		exit;
	}

	// Insert
	$sqlIn = "INSERT INTO rrsv_vendor (name,mobile,email,address, description, status) VALUES ('$name','$mobile', '$email','$address','$description', '$status')";
	$result = mysqli_query($myDB, $sqlIn);
	echo $result ? 0 : 3;
}
