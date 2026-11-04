<?php
session_start();
if (!empty($_SESSION['mid'])) {
	header("location:home.php");
	exit;
}
$token = $_SESSION['_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>RRSV</title>
    <!-- base:css -->
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="libray/css/style.css">
    <link rel="stylesheet" href="libray/css/custom.css">
    
    <!-- endinject -->
    <link rel="shortcut icon" href="libray/images/logo.jpeg" />
</head>

<body>
<div class="container-scroller d-flex">
    <div class="container-fluid page-body-wrapper full-page-wrapper d-flex">
        <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
            <div class="row flex-grow">
                <div class="col-lg-6 d-flex align-items-center justify-content-center">
                    <div class="auth-form-transparent text-left p-3">
                        <div class="brand-logo">
                            <a class="navbar-brand brand-logo" href="index2.html"><img class="img" src="libray/images/logo.jpeg" alt="logo" style="
    margin-left: 100px;
"/></a>
                        </div>
                        <h4>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h4>
                        <h6 style="
    margin-left: 25px;
">Reg. No. : SO196094, U-DISE Code : 19251610302</h6> <h6 style="
    margin-left: 119px;
"><br>ESTD : 2012</h6>
<h6 style="
    margin-left:-15px;
"><br>Baidyadanga, Rasulpur, Memari, Purba Bardhaman - 713151</h6>
<br>
                        <h6 class="font-weight-light">Welcome Back To Our Website. Happy To See You Again! <br><h6 style="
    margin-left: 120px;
">Thank You!</h6>
                        <div id="alert"></div>
                        <form class="pt-3">
                            <input type="hidden" name="_token" id="_token" value="<?php echo $token; ?>">
                            <div class="form-group">
                                <label for="exampleInputEmail">Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-account-outline text-primary"></i>
                      </span>
                                    </div>
                                    <input type="text" class="form-control form-control-lg border-left-0" name="email" id="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword">Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                                    </div>
                                    <input type="password" class="form-control form-control-lg border-left-0" name="password" id="password" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="my-2 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <label class="form-check-label text-muted">
                                        <input type="checkbox" class="form-check-input">
                                        <!--Keep me signed in-->
                                    </label>
                                </div>
                                <!--<a href="#" class="auth-link text-black">Forgot password?</a>-->
                            </div>
                            <div class="my-3">
                                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">LOGIN</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 register-half-bg d-none d-lg-flex flex-row">
                    <h4 class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2022  <a>iTech Solutions & Service</a> All rights reserved.</h4>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- base:js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<script src="libray/js/jquery.cookie.js" type="text/javascript"></script>
<!-- inject:js -->
<script src="libray/js/off-canvas.js"></script>
<script src="libray/js/hoverable-collapse.js"></script>
<script src="libray/js/template.js"></script>
<!-- endinject -->

<!--custom js-->
<script>
    $('form').submit(function(e){
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "loging_post.php",
            data: $('form').serialize(),
            success: function(data) {
                if(data == "success") {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Login success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    location.assign("home.php");
                }
                else if(data == "error"){
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Login Error!</strong> You should check in email/password.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                else {
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Login Error!</strong> Something went wrong.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            },
            error: function(data) {
                alert(JSON.stringify(data));
            }
        });

    });
</script>
<!--custom js-->
</body>

</html>
