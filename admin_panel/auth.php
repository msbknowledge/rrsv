<?php
session_start();
include 'dbcon.php';

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $safe_email = mysqli_real_escape_string($con, $email);
    $safe_password = mysqli_real_escape_string($con, $password);

    // --- 1. Check Admin table ---
    $admin_q = mysqli_query($con, "SELECT * FROM rrsv_admin WHERE (admin_user='$safe_email' OR admin_email='$safe_email') AND admin_pwd='$safe_password' LIMIT 1");
    if ($admin_q && mysqli_num_rows($admin_q) > 0) {
        $data = mysqli_fetch_assoc($admin_q);

        if ($data['admin_type'] == '3') {
            $_SESSION['user_id'] = $data['admin_id'];
            $_SESSION['username'] = $data['admin_user'];
            $_SESSION['user_data'] = $data;
            $_SESSION['role'] = 'icard';
            header("Location: icard/index.php");
            exit;
        } else {
            $_SESSION['mid'] = $data['admin_id'];
            $_SESSION['_token'] = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32);
            $_SESSION['user_id'] = $data['admin_id'];
            $_SESSION['username'] = $data['admin_user'];
            $_SESSION['user_data'] = $data;
            $_SESSION['role'] = 'admin';
            header("Location: index.php");
            exit;
        }
    }

    // --- 2. Check Teacher table ---
    $teacher_q = mysqli_query($con, "SELECT * FROM rrsv_teacher WHERE (tech_phone_no='$safe_email' OR email='$safe_email') AND password='$safe_password' LIMIT 1");
    if ($teacher_q && mysqli_num_rows($teacher_q) > 0) {
        $data = mysqli_fetch_assoc($teacher_q);
        $_SESSION['username'] = $data['full_name'];
        $_SESSION['student_ids'] = [];
        $_SESSION['user_data'] = $data;
        $_SESSION['role'] = 'teacher';
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['key'] = '66552277T';
        header("Location: teacher/index.php");
        exit;
    }

    // --- 3. Check Student table (find latest active record by registration number) ---
    $student_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no='$safe_email' AND password='$safe_password' ORDER BY scl_session DESC, id DESC LIMIT 1");
    if ($student_q && mysqli_num_rows($student_q) > 0) {
        $student = mysqli_fetch_assoc($student_q);
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['username'] = $student['scl_name'];
        $_SESSION['scl_reg_no'] = $student['scl_reg_no'];
        $_SESSION['student_ids'] = [$student['id']];
        $_SESSION['user_data'] = $student;
        $_SESSION['role'] = 'student';
        header("Location: student/index.php");
        exit;
    }

    // --- If none match ---
    header("Location: login.php?error=Invalid Login Details");
    exit;

} else {
    header("Location: login.php?error=Please Enter User ID and Password");
    exit;
}
?>
