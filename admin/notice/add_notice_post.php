<?php
session_start();
include('../../include/dbcon.php');

$id = intval($_POST['id'] ?? 0);
$title = mysqli_real_escape_string($myDB, $_POST['title']);
$description = mysqli_real_escape_string($myDB, $_POST['description']);
$notice_date = mysqli_real_escape_string($myDB, $_POST['notice_date']);
$status = mysqli_real_escape_string($myDB, $_POST['status']);
$token = $_POST['token'] ?? '';

if ($token != $_SESSION['_token']) {
	echo "Invalid Token";
	exit;
}

$filePath = "";
if (!empty($_FILES['notice_file']['name'])) {
	$uploadDir = "../../uploads/gallery/";
	if (!is_dir($uploadDir))
		mkdir($uploadDir, 0777, true);
	$fileName = time() . "_" . basename($_FILES['notice_file']['name']);
	$targetFile = $uploadDir . $fileName;
	if (move_uploaded_file($_FILES['notice_file']['tmp_name'], $targetFile)) {
		$filePath = "uploads/gallery/" . $fileName;
	}
}

if ($id > 0) {
	// update
	$sql = "UPDATE rrsv_notice SET 
                title='$title',
                description='$description',
                notice_date='$notice_date',
                status='$status' ";
	if ($filePath)
		$sql .= ", notice_file='$filePath' ";
	$sql .= "WHERE id=$id";
	$res = mysqli_query($myDB, $sql);
	echo $res ? 1 : "Error updating notice";
} else {
	// insert
	$sql = "INSERT INTO rrsv_notice (title, description, notice_file, notice_date, status) 
            VALUES ('$title', '$description', '$filePath', '$notice_date', '$status')";
	$res = mysqli_query($myDB, $sql);
	echo $res ? 0 : "Error inserting notice";
}
