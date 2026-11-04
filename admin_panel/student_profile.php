<?php
include 'config.php'; // database connection
session_start();

// --- Ensure user is logged in ---
if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$email = $_SESSION['email'];
$user_id = $_SESSION['user_id']; // assuming set during login

// If teacher/admin wants to view a student profile (via ?id=)
if (($role == 'teacher' || $role == 'admin') && isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $student_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE id = $student_id");
} 
// If logged-in student wants to see their own profile
else {
    $student_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no = '$email' LIMIT 1");
}

if (mysqli_num_rows($student_q) == 0) {
    echo "<p>No student record found.</p>";
    exit();
}

$student = mysqli_fetch_assoc($student_q);

// --- Allow update only for STUDENT role ---
if ($role == 'student' && isset($_POST['update'])) {
    $scl_name = mysqli_real_escape_string($con, $_POST['scl_name']);
    $scl_class = mysqli_real_escape_string($con, $_POST['scl_class']);
    $scl_section = mysqli_real_escape_string($con, $_POST['scl_section']);
    $scl_roll_no = mysqli_real_escape_string($con, $_POST['scl_roll_no']);
    $scl_dob = mysqli_real_escape_string($con, $_POST['scl_dob']);
    $father_name = mysqli_real_escape_string($con, $_POST['father_name']);
    $mother_name = mysqli_real_escape_string($con, $_POST['mother_name']);
    $scl_contact = mysqli_real_escape_string($con, $_POST['scl_contact']);
    $scl_address = mysqli_real_escape_string($con, $_POST['scl_address']);

    $photo = $student['photo']; // default old photo

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/students/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo = $target_file;
    }

    // Update query
    $update_q = mysqli_query($con, "
        UPDATE rrsv_student_registration SET 
            scl_name = '$scl_name',
            scl_class = '$scl_class',
            scl_section = '$scl_section',
            scl_roll_no = '$scl_roll_no',
            scl_dob = '$scl_dob',
            father_name = '$father_name',
            mother_name = '$mother_name',
            scl_contact = '$scl_contact',
            scl_address = '$scl_address',
            photo = '$photo'
        WHERE scl_reg_no = '$email'
    ");

    if ($update_q) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: student_profile.php");
        exit();
    } else {
        $error = "Error updating profile: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .profile-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px auto;
            max-width: 700px;
        }
        .profile-card img {
            width: 120px; height: 120px; border-radius: 50%;
            object-fit: cover; border: 3px solid #007bff;
        }
        .edit-btn { float: right; }
    </style>
</head>
<body>
<div class="contain
