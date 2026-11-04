<?php
session_start();
include '../dbcon.php';

if (!isset($_POST['id'], $_POST['status'])) {
    echo 'error';
    exit;
}

$id = (int) $_POST['id'];
$status = $_POST['status'] === 'active' ? 'active' : 'inactive';

$sql = "UPDATE rrsv_homework_exam SET status = ? WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
