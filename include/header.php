<?php
// session_start();

// $mid = $_SESSION['mid'];
//  $token = $_SESSION['_token'];
// if ($_SERVER['HTTP_REFERER'] != 'http://rasulpuranathsamitykgschool.com/template/index.php' && !isset($token) && !isset($mid) || $mid =='') {

//     $cookie_name = "last_http";
//     $cookie_value = $_SERVER['SCRIPT_URI'];
//     setcookie($cookie_name, $cookie_value, time() + 500 , "/");


//     header("location:index.php");
// }


// if(!isset($_COOKIE["last_http"])) {
//     $lst = $_COOKIE["last_http"];
//     unset($_COOKIE["last_http"]);

//     header("location:".$lst);
// } 

//  include('left_nav.php'); 

include_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$mid = isset($_SESSION['mid']) ? $_SESSION['mid'] : '';
$token = isset($_SESSION['_token']) ? $_SESSION['_token'] : '';

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if (
  ($referer != 'http://rasulpuranathsamitykgschool.com/template/index.php')
  && (empty($token) || empty($mid))
) {

  $cookie_name = "last_http";
  $cookie_value = isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : $_SERVER['REQUEST_URI'];
  setcookie($cookie_name, $cookie_value, time() + 500, "/");

  $loginUrl = defined('BASE_URL') ? BASE_URL . 'login.php' : '/rrsv/login.php';
  header("location:" . $loginUrl);
  exit;
}

// check cookie properly
if (isset($_COOKIE["last_http"])) {
  $lst = $_COOKIE["last_http"];
  setcookie("last_http", "", time() - 3600, "/"); // clear cookie
  unset($_COOKIE["last_http"]);

  header("location:" . $lst);
  exit;
}
include_once __DIR__ . '/left_nav.php';
?>


<style>
  .navbar {
    font-weight: 400;
    transition: background $action-transition-duration $action-transition-timing-function;
    -webkit-transition: background $action-transition-duration $action-transition-timing-function;
    -moz-transition: background $action-transition-duration $action-transition-timing-function;
    -ms-transition: background $action-transition-duration $action-transition-timing-function;
    background: url(https://rrsv.in/libray/images/other/navbar-cover.jpeg) center center no-repeat;
    background-size: cover;
    height: $navbar-full-height;
    width: auto;
    opacity: 0.8;
  }

  .text-bg {
    display: inline-flex;
    font-size: 20px;
    background-color: #223e9c;
    color: #ffffff;
  }
</style>
<!--<link href='../../libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>-->

<!--<body onLoad="noBack();" onpageshow="if (event.persisted) noBack();" onUnload="">-->

<!--<script type="text/javascript">-->
<!--       function preventBack() {-->
<!--           window.history.forward(); -->
<!--       }-->

<!--       setTimeout("preventBack()", 0);-->

<!--       window.onunload = function () { null };-->
<!--   </script>-->
<div class="container-fluid page-body-wrapper">
  <!-- partial:./partials/_navbar.html -->
  <nav class="navbar col-lg-12 col-12 px-0 py-0 py-lg-4 d-flex flex-row">
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-menu"></span>
      </button>
      <div class="navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="home.php"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg"
            class="img" height="80" width="80" alt="image" /></a>
        <a class="navbar-brand brand-logo-mini" href="home.php"><img
            src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" class="img" alt="image" /></a>
      </div>
      <h4 class="font-weight-bold mb-0 d-none d-md-block mt-1">
        <p class="text-bg">WELCOME TO ADMIN
        <p> <br>
        <p class="text-bg">RASULPUR RAMKRISHNA SARADA VIDYAPITH, Reg. No. : SO196094, U-DISE Code : 19251610302, ESTD :
          2012</p><br>
        <p class="text-bg">Baidyadanga, Rasulpur, Memari, Purba Bardhaman - 713151 </p>
      </h4>
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item">
          <h4 class="mb-0 font-weight-bold d-none d-xl-block text-bg">
            <?php $timestamp = time();
            echo (date("F d, Y", $timestamp)); ?>
          </h4>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
        data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
    <!--<div class="navbar-menu-wrapper navbar-search-wrapper d-none d-lg-flex align-items-center">-->
    <!--  <ul class="navbar-nav mr-lg-2">-->
    <!--    <li class="nav-item nav-search d-none d-lg-block">-->
    <!--      <div class="input-group">-->
    <!--        <input type="text" class="form-control" placeholder="Student Name/Enroll No" aria-label="search" aria-describedby="search">-->
    <!--      </div>-->
    <!--    </li>-->
    <!--  </ul>-->
    <!--  <ul class="navbar-nav navbar-nav-right">-->
    <!--    <li class="nav-item nav-profile dropdown">-->
    <!--      <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">-->
    <!--        <img src="libray/images/faces/face5.jpg" alt="profile"/>-->
    <!--        <span class="nav-profile-name">RRSV</span>-->
    <!--      </a>-->
    <!--      <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">-->
    <!--        <a class="dropdown-item">-->
    <!--          <i class="mdi mdi-settings text-primary"></i>-->
    <!--          Settings-->
    <!--        </a>-->
    <!--        <a class="dropdown-item" href="logout.php">-->
    <!--          <i class="mdi mdi-logout text-primary"></i>-->
    <!--          Logout-->
    <!--        </a>-->
    <!--      </div>-->
    <!--    </li>-->
    <!--  </ul>-->
    <!--</div>-->
  </nav>
  </body>
  <!--      <script type="text/javascript">-->
  <!--    window.history.forward();-->
  <!--    function noBack()-->
  <!--    {-->
  <!--        window.history.forward();-->
  <!--    }-->
  <!--</script>-->

  <!-- partial -->