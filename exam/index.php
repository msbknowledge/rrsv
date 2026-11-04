
<!DOCTYPE html>
<html>
  <?php
session_start();
 
if (isset($_SESSION["username"])) {
  session_destroy();
  unset($_SESSION["username"]);
  header("location:index.php"); 
  
}
if(isset($_SESSION['username']) && (!isset($_SESSION['key']))){
   header('location:students/account.php?q=1');
}
if(isset($_SESSION['username']) && isset($_SESSION['key']) && $_SESSION['key'] == '66552277A'){
  $username=$_SESSION['username'];
  $_SESSION["username"] = $username;
   header('location:dashboard.php?q=4');
}
if(isset($_SESSION['username']) && isset($_SESSION['key']) && $_SESSION['key'] == '66552277T'){
  header('location:teach/dash.php?q=10');
}

?>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Online Exam</title>
   <link rel="icon" type="image/x-icon" href="/../msb1.ico">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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

<script language="javascript">
function del_Users(uid)
{
	if(confirm("Are you sure want to delete this Contact?"))
	{
		document.location.href = 'groupcontacts?act=del&uid='+uid;
	}
}
</script>
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>
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


</head>
<body>
   <!-- Header Start -->
   <header class="main-header">
    <!-- Logo -->
    <h1>EXAM</h1>
    
  </header><!-- Header End -->
<div class="wrapper">
 <!-- modal -->

 
<!-- Teacher Login Panel End-->
<div class="modal fade" id="teacher">
  <div class="modal-dialog">
    <div class="head">
      <div class="">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
		<h1 class="br">
<span >Login to teacher<em> </em></span>
</h1>
      </div>
      <div class="modal-body title1">
<div class="row">
<div class="col-md-3"></div>
<div class="pure-form">
<form role="form" method="post" action="login.php?q=index.php">
<div class="form-group">
<input type="text" name="username" maxlength="20"  placeholder="Username" class="form-control"/> 
</div>
<div class="form-group">
<input type="password" name="password" maxlength="30" placeholder="Password" class="form-control"/>
</div>
<div class="form-group" align="center">
<input type="submit" name="teacher" value="Login" class="btn btn-primary" />
</div>
</form>
</div><div class="col-md-3"></div></div>
      </div>
    </div>
  </div>
</div>


 </div>
<!-- middle panel-->
<!-- modal login panel-->
   <div class="modal fade" id="login">
  <div class="modal-dialog">
    <div class="head">
      <div class="">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
		<h1 class="br">
<span >Login to Admin<em> </em></span>
</h1>
      </div>
      <div class="modal-body title1">
<div class="row">
<div class="col-md-3"></div>
<div class="pure-form">
<form role="form" method="post" action="login.php?q=index.php">
<div class="form-group">
<input type="text" name="username" maxlength="20"  placeholder="Username" class="form-control"/> 
</div>
<div class="form-group">
<input type="password" name="password" maxlength="30" placeholder="Password" class="form-control"/>
</div>
<div class="form-group" align="center">
<input type="submit" name="admin" value="Login" class="btn btn-primary" />
</div>
</form>
</div><div class="col-md-3"></div></div>
      </div>
    </div>
  </div>
</div>
<!-- modal admin panel-->
<!-- modal student panel-->
 <div class="modal fade" id="student">
  <div class="modal-dialog">
    <div class="head">
      <div class="">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
		<h1 class="br">
<span >Login to Student<em> </em></span>
</h1>
      </div>
      <div class="modal-body title1">
<div class="row">
<div class="col-md-3"></div>
<div class="pure-form">
<form role="form" method="post" action="login.php?q=index.php">
<div class="form-group">
<input type="text" name="username" maxlength="20"  placeholder="Username" class="form-control"/> 
</div>
<div class="form-group">
<input type="password" name="password" maxlength="30" placeholder="Password" class="form-control"/>
</div>
<div class="form-group" align="center">
<input type="submit" name="student" value="Login" class="btn btn-primary" />
</div>
</form>
</div><div class="col-md-3"></div></div>
      </div>
    </div>
  </div>
</div>
<!-- modal student panel-->
 <!-- modal -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
     
    <div class="col-md-3">
         <div class="box box-primary">
            
            <!-- /.box-header -->
			
              <div class="box-body">
  	<!-- left panel-->
    <div class="left">
   <h2>About</h2>
    <p>
        1. আপনার ডিফল্ট ব্যবহারকারী নামটি আপনার নাম
(ব্লক লেটার) EXp: BENU ROY<br/><br/>

২. আপনার ডিফল্ট পাসওয়ার্ড আপনার নিবন্ধকরণ। আই.ই.
আপনার নিবন্ধকরণ যদি আর হয় না ()খন আপনার পাসওয়ার্ডটি () হয়।
পরীক্ষার জন্য
<br/><br/>
1. আপনি পরীক্ষা শুরু করলেই টাইমারটি শুরু হবে।<br/><br/>
২. পরীক্ষাটি শুরু করতে 'স্টার্ট' বাটনে ক্লিক করুন।
 একবার পরীক্ষা শুরু হয়ে গেলে টাইমারটি আপনার লগ ইন বা আমাদের স্থিতি নির্বিশেষে চলবে।
 সুতরাং পরীক্ষা শেষ হওয়ার আগে লগআউট না করার পরামর্শ দেওয়া হয়।
 <br/><br/>
৩. কোনও উত্তর চিহ্নিত করতে আপনার উত্তরটি নির্বাচন করতে হবে এবং প্রতিটি প্রশ্নের জন্য 'লক' বোতাম টিপুন।

 <br/><br/>
 ৪. লক করার পরে, নির্বাচিত উত্তরটি প্রশ্নের উপরে প্রদর্শিত হবে।
"চিহ্নিত না করলে" এবং 'লক' বোতাম না  press করলে এমন প্রশ্নের জন্য 0 নম্বর দেওয়া হবে।
<br/><br/>
      <span style="color:red;font-size:20px;" class="glyphicon glyphicon-remove">If you get some error or any problem send feedback here, you can contact us via </span>.<br/><br/>
<a href=feedback.php> <span style="color:lime;font-size:20px;">Feedback</span>.</a>.</p>
  </div>
<!-- left panel-->

              </div>       
        <!-- /.col -->
		
      </div></div>
    <div class="col-md-6">
         <div class="box box-primary">
            
            <!-- /.box-header -->
			
              <div class="box-body">
              <div class="sp" width="120px">
  <h1 class="br">
  
<span > <em> Student Login</em></span>
</h1>


<div class="pure-form">
<p>
 1.Your default <font color="red">Username</font> is your Name <br/>
(Block Letters  example: RENU ROY). as your Log-in ID.<br/><br/>

2. Your default <font color="red">password </font>is your Registration . i.e<br/>
if your  <font color="red">Registration no is () then your Password is ()</font>.<br/>

</p>

<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#student" style="color:lightyellow">Student Login</a>
<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#teacher" style="text-align:center" />Teacher Login </a>
<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#login" style="color:lightyellow">Admin Login</a>
</div>

</div>

              </div>       
        <!-- /.col -->
		
      </div></div>
      <div class="col-md-3">
         <div class="box box-primary">
           
            <!-- /.box-header -->
			
              <div class="box-body">


<!-- right panel-->
<div class="right">
    
    <h2>Result</h2>
  <p>

      <marquee height="200" direction="up" scrollamount="1"  style="color:lime">Check your Online exam results <br/><br/> .
Results published System<br/><br/></marquee>
    </p>
    <p><span style="color:#e74c3c"><strong>FOR LOGIN</strong></span></p>

<p><span style="color:#ffffff">1.YOUR DEFAULT USERNAME IS YOUR NAME<br />
(BLOCK LETTERS EXAMPLE: RENU ROY). AS YOUR LOG-IN ID.</span></p>

<p><span style="color:#ffffff">2. YOUR DEFAULT PASSWORD IS YOUR REGISTRATION . I.E<br />
IF YOUR REGISTRATION NO IS () THEN YOUR PASSWORD IS ().</span></p>

<p><span style="color:#ffffff">FOR EXAM</span></p>

<p><span style="color:#ffffff">1.The timer would start only when you start the test.&nbsp;<br />
2.To start the test, click on &#39;Start&#39; button.<br />
&nbsp;Once the test is started the timer would run irrespective of your logged in or logged our status.<br />
&nbsp;So it is recommended not to logout before test completion.<br />
3.To mark an answer you need to select it and press the &#39;Lock&#39; button for every questions.&nbsp;<br />
4. Upon locking, the selected answer would be displayed above the question.<br />
0 marks would be awarded for the questions that are &quot;not marked&quot; and not &nbsp;press the &#39;Lock&#39; button.</span><br />
&nbsp;</p>
</div>
<!-- right panel-->
              </div>       
        <!-- /.col -->
		
      </div></div>




		<!-- template message box end-->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Footer Start -->
  <footer class="main-footer" style="text-align:center;">

    

    <strong>Copyright &copy; 2013-2021 </strong> All rights

    reserved.

  </footer>  <!-- Footer End -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
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
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>




</body>
</html>