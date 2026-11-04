
<!DOCTYPE html>
<html>
<?php
 error_reporting(0); 
 session_start();
include_once 'dbConnection.php';

if (!(isset($_SESSION["username"]))) {
    header("location:../index.php");
}

?>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Online Exame</title>
   <link rel="icon" type="image/x-icon" href="/../msb1.ico">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../css/bootstrap.min.css">
 <!-- Font Awesome -->
 <link rel="stylesheet" href="../css/font-awesome.min.css">
  <!-- <link rel="stylesheet" href="../css/font-awesome/css/font-awesome.min.css"> -->
  <!-- Ionicons -->
  <link rel="stylesheet" href="../css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../css/dataTables.bootstrap.min.js">
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
  
   <link rel="stylesheet" href="../css/jquery.dataTables_themeroller.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the ../css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  
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
  </style>


</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <!-- Header Start -->
  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>BSG</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="../css/download.png" style="height:100%;width:54px"></span>
	      </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
         
           <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../css/download.png" class="user-image" alt="User Image">
			  <?php
include_once 'dbConnection.php';
error_reporting(0); 
session_start();
if (!(isset($_SESSION['username']))) {
    header("location:index.php");
} else {
    $name     = $_SESSION['name'];
    $username = $_SESSION['username'];
    
    include_once 'dbConnection.php';
    echo '<span class="pull-right top title1" ></span> <span class="log log1" style="color:lightyellow">' . $username . '</span>';
}
?>  
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../css/download.png" class="img-circle" alt="User Image">
 <p>
                  Welcome <?= $username ;?>             <small></small>
                </p>
              </li>
              <!-- Menu Body -->
             
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="../logout.php?q=index.php" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
			
            
          </li>
          <!-- Control Sidebar Toggle Button -->
          
        </ul>
      </div>
    </nav>
  </header><!-- Header End -->
  <!-- Left side column. contains the logo and sidebar -->
<!-- Sidebar Start -->
   <?php
   
   include_once 'aslidebar.php';
   ?>
<!-- sidebar End -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $username;?>   
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Students Management</a></li>
        <li class="active">Students</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- Note Start -->
  <!--    <div class="col-md-12">-->
		<!--<div class="box box-danger">-->
		<!--	<div class="box-header with-border">-->
			           
  <!--            <h3 class="box-title"><i class="fa fa-warning text-yellow"></i> Note:</h3> Dont Send any Promotional sms in Transactional Route,Other wise your Account will Be Blocked.	-->
		<!--	</div>-->
		<!--</div>-->
	 <!-- </div>-->
        <!-- Note End -->
        <!-- Send sms box start -->
	<div class="col-md-12">
          <div class="box box-primary">
           <!-- /.header-->
            <div class="box-header with-border">
              <h3 class="box-title">Questions</h3>
              
               <div style="text-align: right";>
             <h4 class="btn btn-success btn-sm"> 
 </h4>
                </div>
                
		 </div>
             <!-- /.header-->
            
			
              <div class="box-body">
               
				 <!-- /box-body-->
				
         <?php

if (@$_GET['q'] == 7 && @$_GET['eid']) {

// $eid = @$_GET['eid'];
// $result = mysqli_query($con, "SELECT * FROM questions where eid='$eid' ORDER BY sn desc");
// $row = mysqli_fetch_assoc($result);
// $no = $row ? $row['sn']+1 : 1;
$eid = mysqli_real_escape_string($con, $_GET['eid']);

// Quiz- total question 
$quiz_result = mysqli_query($con, "SELECT total, title FROM quiz WHERE eid='$eid' LIMIT 1");

if (!$quiz_result || mysqli_num_rows($quiz_result) == 0) {
    die("Quiz not found.");
}

$quiz = mysqli_fetch_assoc($quiz_result);

$total_questions = (int)$quiz['total'];

// question count
$count_result = mysqli_query($con, "SELECT COUNT(*) AS question_count FROM questions WHERE eid='$eid'");
$count_row = mysqli_fetch_assoc($count_result);
$current_questions = (int)$count_row['question_count'];

// Limit check
if ($current_questions >= $total_questions) {
    echo '
    <div class="alert alert-danger text-center">
        <strong>Question Limit Reached!</strong><br>
        This quiz allows only <strong>'.$total_questions.'</strong> questions.
        <br>
        Already added: <strong>'.$current_questions.'</strong> questions.
    </div>';
    return;
}

// Next question number
$no = $current_questions + 1;

echo '
<div class="row">
<span class="title1" style="margin-left:40%;font-size:30px;"><b>Enter Question Details</b></span><br /><br />
<div class="col-md-3"></div>
<div class="col-md-6">

<form class="form-horizontal title1" method="POST" action="update.php?q=addqnsm&n='.$no.'&eid='.$eid.'&ch=4" enctype="multipart/form-data">

<fieldset>

<b>Question number '.$no.':</b><br>

<div class="form-group">
<div class="col-md-12">
<textarea name="qns1" class="form-control" placeholder="Enter Question"></textarea>
</div>
</div>

<!-- QUESTION TYPE -->
<div class="form-group">
<div class="col-md-12">
<select name="question_type" id="question_type" class="form-control">
<option value="mcq">MCQ</option>
<!--<option value="multi_select">Multi Select</option>
<option value="true_false">True/False</option>
<option value="short_answer">Short Answer</option>
<option value="fill_blank">Fill Blank</option>
<option value="description">Description</option>-->
<option value="image">Image</option>
<!--<option value="ordering">Ordering</option>-->
</select>
</div>
</div>

<!-- MCQ -->
<div id="mcq_box">
<input name="11" class="form-control" placeholder="Option A"><br>
<input name="12" class="form-control" placeholder="Option B"><br>
<input name="13" class="form-control" placeholder="Option C"><br>
<input name="14" class="form-control" placeholder="Option D"><br>

<select name="ans1" class="form-control">
<option value="a">Option A</option>
<option value="b">Option B</option>
<option value="c">Option C</option>
<option value="d">Option D</option>
</select>
</div>

<!-- MULTI -->
<div id="multi_select_box" style="display:none;">
<input name="m1" class="form-control" placeholder="Option A"><br>
<input name="m2" class="form-control" placeholder="Option B"><br>
<input name="m3" class="form-control" placeholder="Option C"><br>
<input name="m4" class="form-control" placeholder="Option D"><br>

<label><input type="checkbox" name="multi_ans[]" value="1"> A</label>
<label><input type="checkbox" name="multi_ans[]" value="2"> B</label>
<label><input type="checkbox" name="multi_ans[]" value="3"> C</label>
<label><input type="checkbox" name="multi_ans[]" value="4"> D</label>
</div>

<!-- TRUE FALSE -->
<div id="true_false_box" style="display:none;">
<select name="true_false" class="form-control">
<option value="True">True</option>
<option value="False">False</option>
</select>
</div>

<!-- SHORT -->
<div id="short_answer_box" style="display:none;">
<input name="short_answer" class="form-control" placeholder="Answer">
</div>

<!-- FILL -->
<div id="fill_blank_box" style="display:none;">
<input name="fill_blank" class="form-control" placeholder="Answer">
</div>

<!-- DESC -->
<div id="description_box" style="display:none;">
<textarea name="description" class="form-control"></textarea>
</div>

<!-- IMAGE -->
<div id="image_box" style="display:none;">
<label>Option A</label>
    <input type="file" name="img1" class="form-control"><br>

    <label>Option B</label>
    <input type="file" name="img2" class="form-control"><br>

    <label>Option C</label>
    <input type="file" name="img3" class="form-control"><br>

    <label>Option D</label>
    <input type="file" name="img4" class="form-control"><br>

    <select name="image_answer" class="form-control">
        <option value="a">Option A</option>
        <option value="b">Option B</option>
        <option value="c">Option C</option>
        <option value="d">Option D</option>
    </select>

</div>

<!-- ORDER -->
<div id="ordering_box" style="display:none;">
<input name="order1" class="form-control" placeholder="Step 1"><br>
<input name="order2" class="form-control" placeholder="Step 2"><br>
<input name="order3" class="form-control" placeholder="Step 3"><br>
<input name="order4" class="form-control" placeholder="Step 4"><br>
</div>

<br>

<div class="form-group">
<div class="col-md-12"> 
<input type="submit" style="margin-left:45%" class="btn btn-primary" value="Submit"/>
</div>
</div>

</fieldset>
</form>
</div>
</div>
';
}
?>		 
               
			
             
             
              
              
              
              <div class="box-footer">
                
				 <!-- /.footer-->
				
                </div>
              <!-- /.box-footer -->
             
          </div>
          
          
   
        <!-- Send sms box end -->
		
		
            
 	<div class="box-body"  style="background-color:#E6E6FA">
			 
	
	 </div>
          
          </div>
          <!-- /.box -->
		</div>
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
<script src="../js/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../js/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../js/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../js/demo.js"></script>
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

<script>
function showType(){
    var t = $('#question_type').val();

    $('#mcq_box,#multi_select_box,#true_false_box,#short_answer_box,#fill_blank_box,#description_box,#image_box,#ordering_box').hide();

    $('#'+t+'_box').show();
}

$(document).ready(function(){
    showType();
    $('#question_type').change(showType);
});
</script>


</body>
</html>