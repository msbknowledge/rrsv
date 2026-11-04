<?php
session_start();
include 'dbcon.php';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $session       = date('Y');
   //print_r($email);die();
    // --- Check Student table ---
    $student_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_session='$session' AND scl_reg_no='$email' AND password='$password'");
    if (mysqli_num_rows($student_q) > 0) {
        // $students = mysqli_query($con, "SELECT id FROM rrsv_student_registration WHERE scl_session='$session' AND scl_reg_no='$email'");

        // $student_ids = [];
        // while ($row = mysqli_fetch_assoc($students)) {
        //     $student_ids[] = $row['id'];
        // }
        $student = mysqli_fetch_assoc($student_q);
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['username'] = $student['scl_name'];
        $_SESSION['scl_reg_no'] = $email;
        $_SESSION['student_ids'] = $student_id;
        $data =$student;
        $_SESSION['user_data'] = $data;
        $_SESSION['role'] = 'student';
        header("Location: student/index.php");
        exit;
    }

    // --- Check Teacher table ---
    $teacher_q = mysqli_query($con, "SELECT * FROM rrsv_teacher WHERE tech_phone_no='$email' AND password='$password'");
    if (mysqli_num_rows($teacher_q) > 0) {
         $data = mysqli_fetch_assoc($teacher_q);
        $_SESSION['username'] = $data['full_name'];
        $_SESSION['student_ids'] = [];
        $_SESSION['user_data'] = $data;
        $_SESSION['role'] = 'teacher';
        $_SESSION["user_id"]  = $data['id'];
        $_SESSION["key"] ='66552277T';
        header("Location: teacher/index.php");
        exit;
    }

    
    // --- Check Admin table ---
    $admin_q = mysqli_query($con, "SELECT * FROM rrsv_admin WHERE admin_user='$email' AND admin_pwd='$password' and admin_type='0'");
    if (mysqli_num_rows($admin_q) > 0) {
        $data = mysqli_fetch_assoc($admin_q);
        $_SESSION['user_data'] = $data;
        $_SESSION['role'] = 'admin';
        header("Location: admin/index.php");
        exit;
    }
     $admin_q = mysqli_query($con, "SELECT * FROM rrsv_admin WHERE admin_user='$email' AND admin_pwd='$password' and admin_type='3'");
    if (mysqli_num_rows($admin_q) > 0) {
        $data = mysqli_fetch_assoc($admin_q);
        $_SESSION['user_data'] = $data;
        $_SESSION['role'] = 'icard';
        header("Location: icard/index.php");
        exit;
    }
    // --- If none match ---
    header("Location: login.php?error=Invalid Login Details");
    exit;

} else {
    header("Location: login.php?error=Please Enter Email and Password");
    exit;
}
?>
