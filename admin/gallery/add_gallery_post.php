<?php
session_start();
include('../../include/dbcon.php');

$id = intval($_POST['id'] ?? 0);
$title = mysqli_real_escape_string($myDB, $_POST['title']);
$category = mysqli_real_escape_string($myDB, $_POST['category']);
$status = mysqli_real_escape_string($myDB, $_POST['status']);
$token = $_POST['token'] ?? '';

if ($token != $_SESSION['_token']) {
	echo "Invalid Token";
	exit;
}
$filePath = "";
if (!empty($_FILES['image_path']['name'])) {
	$allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
	$fileExt = strtolower(pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION));

	if (!in_array($fileExt, $allowedExts)) {
		echo "Invalid image format. Allowed: jpg, jpeg, png, gif";
		exit;
	}
	if ($_FILES['image_path']['size'] > 2 * 1024 * 1024) { // 2MB
		echo "Image size must be less than 2MB";
		exit;
	}

	$uploadDir = "../../uploads/gallery/";
	if (!is_dir($uploadDir))
		mkdir($uploadDir, 0777, true);

	$fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9\.\-_]/", "", basename($_FILES['image_path']['name']));
	$targetFile = $uploadDir . $fileName;

	if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile)) {
		$filePath = "uploads/gallery/" . $fileName;
	} else {
		echo "Failed to upload image";
		exit;
	}
}

if ($id > 0) {
	// update
	$sql = "UPDATE rrsv_gallery SET 
                title='$title',
                category='$category',
                status='$status' ";
	if ($filePath)
		$sql .= ", image_path='$filePath' ";
	$sql .= "WHERE id=$id";
	$res = mysqli_query($myDB, $sql);
	echo $res ? 1 : "Error updating gallery";
} else {
	// insert
	$sql = "INSERT INTO rrsv_gallery (title, image_path, category, uploaded_on, status) 
            VALUES ('$title', '$filePath', '$category', NOW(), '$status')";
	$res = mysqli_query($myDB, $sql);
	echo $res ? 0 : "Error inserting gallery";
}
