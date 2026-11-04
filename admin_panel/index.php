<?php
include(__DIR__ . '/../include/config.php');
include 'dbcon.php';

session_start();

if (!isset($_SESSION['user_data']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] === 'teacher') {
    header("Location: teacher/index.php");
    exit;
}
if ($_SESSION['role'] === 'student') {
    header("Location: student/index.php");
    exit;
}
if ($_SESSION['role'] === 'icard') {
    header("Location: icard/index.php");
    exit;
}

$teacher = $_SESSION['user_data'];
?>
<!DOCTYPE html>
<html>
<?php include('header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <?php include 'top_navbar.php'; ?>
  </header>
  <?php include 'left_navbar.php'; ?>
  <?php include 'dashboard.php'; ?>
  <?php include 'footer.php'; ?>
</div>
</body>
</html>
