<?php include(__DIR__ . '/../include/config.php');?>
<?php

session_start();

// If user is already logged in, redirect based on role
if (isset($_SESSION['user_data']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'teacher':
            header("Location: teacher/index.php");
            exit;
        case 'student':
            header("Location: student/index.php");
            exit;
        case 'admin':
            header("Location: admin/index.php");
            exit;
    }
}
?>
<?php include('head.php'); ?>
<link rel="stylesheet" href="popup_style.css">



<div id="main-wrapper">
    <div class="unix-login">
        <div class="container-fluid" style="background-image: url('uploadImage/Logo/');
 background-image: url('uploadImage/exam.png ">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="login-content card">
                        <div class="login-form">
                            <center><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="width:10%;"></center><br>
                            <form method="POST" action="auth.php">
                                <div class="form-group">
                                    <label>User ID</label>
                                    <input type="text" name="email" class="form-control" placeholder="User ID"
                                        required="">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Password"
                                        required="">
                                </div>
                                <!--<div class="checkbox">-->
                                <!--    <label class="pull-right">-->
                                <!--        <a href="forgot_password.php">Forgotten Password?</a>-->
                                <!--    </label>-->
                                <!--</div>-->
                                <button type="submit" name="btn_login"
                                    class="btn btn-primary btn-flat m-b-30 m-t-30">Sign in</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="assets/login/js/lib/jquery/jquery.min.js"></script>

<script src="assets/login/js/lib/bootstrap/js/popper.min.js"></script>
<script src="assets/login/js/lib/bootstrap/js/bootstrap.min.js"></script>

<script src="assets/login/js/jquery.slimscroll.js"></script>

<script src="assets/login/js/sidebarmenu.js"></script>

<script src="assets/login/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>

<script src="assets/login/js/custom.min.js"></script>

</body>

</html>