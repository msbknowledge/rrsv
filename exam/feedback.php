<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<link rel="icon" href="favicon.ico" type="image/icon" sizes="16x16">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Online Exam </title>

   <!-- Bootstrap 3.3.6 -->
   <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="css/dataTables.bootstrap.min.js">
  <link rel="stylesheet" href="css/jquery.dataTables.min.css">
  
   <link rel="stylesheet" href="css/jquery.dataTables_themeroller.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <style>
  @media only screen and (max-width: 620px) {
  /* For mobile phones: */
  .menu,.sp,.main, .right {
    width: 100%;
  }
}
.content-wrapper, .right-side, .main-footer{margin-left:0px;}
.sp {
   padding: 20px 30px 30px 20px;
    background: #09bffd;
    border-radius: 8px;
	text-transform:uppercase;
	font-size:15px;
	text-align:left; 
	padding-top:3px;
	padding-left:55px; 
	color:blue;
    
}
.footer{
  background-color:#3AA56A;
  color: #fff;
  text-align:center;
  font-size:20px;
  padding:10px;
  margin-top:37px;
  position: stiky;
  bottom: 0;
  width: 100%;
   }

.head {
    width: 390px;
    padding: 20px 30px 30px 30px;
    background: #09bffd;
    border-radius: 8px;
    display: table;
	margin: 50px;
    top:20%;
}
.br {
	
    font-size: 20px;
    line-height: 24px;
    font-weight: 100;
    text-align: center;
    color: #fff;
	border-radius: 8px;
    
}
 em {
    display: block;
    font-size: 25px;
    line-height: 33px;
    font-weight: 900px;
    text-transform: uppercase;
    letter-spacing: -1px;
}
.pure-form{
    background: #ddeef8;
    width: 100%;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0 0;
}
.right {
    background-color: #18271b;
    color: #fff;
    float: left;
    
    padding: 15px;
    margin-top: 7px;
    text-align: left;
    border-radius: 8px;
    font-size: 12px;
}
.left {
    margin-left: 7px;
    margin-top: 7px;
    background-color: #184b5d;
    color: #fff;
    float: left;
  
    text-align: left;
    border-radius: 8px;
    padding: 15px;
    font-size: 12px;
}
body { 
     font-family: 'Droid Sans', sans-serif;
     background:#E4D2B8;
     font-size: 1.000em; 
    }

.heading {
	width:100%;
	height:96px;
	background:url(./image/banner_bg.gif);
	position:relative;
	border-radius: 8px;
	}
    .circle {
    border-radius: 50%!important;
    border-top-left-radius: 50%;
    border-top-right-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
    border-radius: 8px;
}

.btn-d {
 	background:#fd0963;
	
	height:32px;
	border:1px solid #fd0963;
	color:#FFFFFF;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
	padding:0 20px;
	border-radius: 8px;  
}
.btn-d:hover {
	border:1px solid #fd0963;
	padding:0 20px;
	color:#FFFFFF;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
	background:#fd0663;
	border-radius: 8px; 
	cursor:pointer;
}
.button a:link, .button a:visited, .button a:active { color:#lime; text-decoration:none; }
a {
	font-size:16px;
	color:#lime;
	font-weight:bold;
	text-decoration:none;
	}	

  #pageloader
{
background: url('') 30% 30% no-repeat rgba( 255, 255, 255, 0.8 );
  display: none;
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 2000;
  background-position:center;
 
}

#pageloader img
{
   
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
  text-align: center;
  width : 400px
}

#pageloader:after{
   content: "Please Wait- Don't Press Any Button While Uploading";
   font-weight: bold;
  display: block;
  position: absolute;
  top:60%;
  left: 50%;
  transform: translate(-50%, -50%)
}
.main-header {
    display: flex;
    align-items: center;
    justify-content: space-around;
    flex-wrap: wrap;
    width: 100%;
    background-color: #3AA56A;
    color: #fff;
    padding: 2px;
    text-align:right;
    text-transform: uppercase;
}
  </style>

<?php

if (@$_GET['w']) {

    echo '<script>alert("' . @$_GET['w'] . '");</script>';

}

?>

</head>

<body>
   <!-- Header Start -->
   <header class="main-header">
    <!-- Logo -->
    <h1>EXAM</h1>
    
  </header><!-- Header End -->
<div class="wrapper">
 <!-- modal -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
     
    <div class="col-md-12">
         <div class="box box-primary">
            
            <!-- /.box-header -->
			
              <div class="box-body">




<a href="index.php" class="pull-right btn logb btn-primary" style="color:white"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;<font style="font-size:12px;font-weight:bold">Home</font></a>&nbsp;

</div></div></div>

<div class="col-md-12">
         <div class="box box-primary">
            
            <!-- /.box-header -->
			
              <div class="box-body">

<div class="head" style="min-height:430px;">

  <h1 class="br"><span> <em>FEEDBACK </em></span></h1>

<div class="pure-form" style="font-size:14px;margin-top:20px"><br />

<?php

if (@$_GET['q'])

    echo '<span style="font-size:18px;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;' . @$_GET['q'] . '</span>';

else {

    echo ' 



<form role="form"  method="post" action="feed.php?q=feedback.php">

<div class="row">

<div class="col-md-3"><b>your Trade:</b><br /><br /><br /><b>Your Problem:</b></div>

<div class="col-md-9">

<!-- Text input-->

<div class="form-group">

  <input id="name" name="name" placeholder="Enter your name" class="form-control input-md" type="text"><br />    

   <input id="name" name="subject" placeholder="Enter short description of your feedback" class="form-control input-md" type="text">    



</div>

</div>

</div><!--End of row-->



<div class="row">

<div class="col-md-3"><b>E-Mail ID:</b></div>

<div class="col-md-9">

<!-- Text input-->

<div class="form-group">

  <input id="email" name="email" placeholder="Enter your e-mail" class="form-control input-md" type="email">    

 </div>

</div>

</div><!--End of row-->



<div class="form-group"> 

<textarea rows="5" cols="8" name="feedback" class="form-control" placeholder="Write feedback here. Keep it clean and simple."></textarea>

</div>

<div class="form-group" align="center">

<font style="font-size:12px;font-weight:bold"><input type="submit" name="submit" value="Send My Feedback" class="btn btn-primary" /></font>

</div>

</form>';

}

?>

</div>

<div class="col-md-3"></div></div>

</div></div>

</div>


<!-- jQuery 2.2.3 -->
<script src="js/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="js/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="js/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="js/demo.js"></script>
<!-- page script -->
</body>

</html>

