<?php
include(__DIR__ . '/../include/config.php');

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
        case 'icard':
            header("Location: icard/index.php");
            exit;
        case 'admin':
            header("Location: index.php");
            exit;
    }
}
?>
<?php include('head.php'); ?>

<div id="main-wrapper">
    <div class="unix-login">
        <div class="container-fluid" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
            <div class="row justify-content-center w-100">
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="login-content card shadow" style="border-radius: 8px; padding: 25px; background: rgba(255, 255, 255, 0.95);">
                        <div class="login-form">
                            <div class="text-center mb-3">
                                <img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" alt="RRSV Logo" style="width: 80px; height: 80px; object-fit: contain; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <h4 class="mt-2" style="font-weight: 600; color: #333;">RRSV Portal Login</h4>
                                <p class="text-muted" style="font-size: 13px;">Admin &bull; Teacher &bull; Student &bull; Staff</p>
                            </div>

                            <?php if (!empty($_GET['error'])): ?>
                                <div class="alert alert-danger text-center py-2" role="alert" style="font-size: 14px;">
                                    <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($_GET['msg'])): ?>
                                <div class="alert alert-success text-center py-2" role="alert" style="font-size: 14px;">
                                    <?php echo htmlspecialchars($_GET['msg']); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="auth.php">
                                <div class="form-group">
                                    <label style="font-weight: 500;">User ID / Mobile / Reg No</label>
                                    <input type="text" name="email" class="form-control" placeholder="Enter User ID, Mobile or Reg No" required autocomplete="username">
                                </div>
                                <div class="form-group">
                                    <label style="font-weight: 500;">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required autocomplete="current-password">
                                </div>
                                <button type="submit" name="btn_login" class="btn btn-primary btn-flat btn-block m-t-20" style="padding: 10px; font-weight: 600; border-radius: 4px;">Sign in</button>
                            </form>
                            
                            <div class="text-center mt-3" style="font-size: 12px; color: #777;">
                                <a href="<?php echo BASE_URL; ?>" class="text-muted"><i class="fa fa-arrow-left"></i> Back to Website</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/lib/jquery/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/lib/bootstrap/js/popper.min.js"></script>
<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/lib/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/jquery.slimscroll.js"></script>
<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/sidebarmenu.js"></script>
<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
<script src="<?php echo BASE_URL; ?>admin_panel/assets/login/js/custom.min.js"></script>

</body>
</html>
