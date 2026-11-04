
<!DOCTYPE html>
<html>
<?php
  
  include_once '../dbConnection.php';
  error_reporting(0); 
  session_start();
 if (!(isset($_SESSION["username"]))) {
    header("location:../index.php");
}
  ?>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Admin</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="../css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../css/font-awesome.min.css">
  <link rel="stylesheet" href="../css/font-awesome/css/font-awesome.min.css">
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
      <span class="logo-mini"><b>MSB</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="../css/download.png" style="height:100%;width:44px"></span>
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
             
              <?php
  
if (!(isset($_SESSION["username"]))) {
    header("location:../index.php");
} else {
    $name     = $_SESSION['name'];
    $username = $_SESSION['username'];
    
    echo '<img src="../css/download.png" class="user-image" alt="User Image">
    
			  <span class="hidden-xs">'.$username . '</span>';
}
?>
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../css/download.png" class="img-circle" alt="User Image">

                <p>
                  Welcome <?php echo $username;?>   
                </p>
              </li>
              <!-- Menu Body -->
             
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="logout.php?q=dashboard.php" class="btn btn-default btn-flat">Logout</a>
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
<form role="form" method="post" action="admin.php?q=index.php">
<div class="form-group">
<input type="text" name="uname" maxlength="20"  placeholder="Username" class="form-control"/> 
</div>
<div class="form-group">
<input type="password" name="password" maxlength="30" placeholder="Password" class="form-control"/>
</div>
<div class="form-group" align="center">
<input type="submit" name="login" value="Login" class="btn btn-primary" />
</div>
</form>
</div><div class="col-md-3"></div></div>
      </div>
    </div>
  </div>
</div>
<!-- modal admin panel-->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
     Admin
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Admin Management</a></li>
        <li class="active">Exam</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- Note Start -->
      <div class="col-md-12">
		<div class="box box-danger">
			<div class="box-header with-border">
      <div class="col-sm-3">
        <div class="alert bg-green">
            <h4>Class<i class="pull-right fa fa-building-o"></i></h4>
            <span class="d-block"> Class 101</span>                
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-blue">
            <h4>Department<i class="pull-right fa fa-graduation-cap"></i></h4>
            <span class="d-block"> Sample 101</span>                
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-yellow">
            <h4>Date<i class="pull-right fa fa-calendar"></i></h4>
            <span class="d-block"> Sunday, 19 Mar 2023</span>                
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-red">
            <h4>Hour<i class="pull-right fa fa-clock-o"></i></h4>
            <span class="d-block"> <span class="live-clock">20:51:53</span></span>                
        </div>
    </div>    
    
    </div>
		</div>
	  </div>
        <!-- Note End -->
        <!-- Send sms box start -->
	<div class="col-md-12">
          <div class="box box-primary">
           <!-- /.header-->
            <div class="box-header with-border">
              <h3 class="box-title">Admin</h3>
              
               <div style="text-align: right";>
             <h4 class="btn btn-success btn-sm"> 
 Registration  </h4>
                </div>
                
		 </div>
             <!-- /.header-->
            
			
              <div class="box-body">
               
				 <!-- /box-body-->
				 
  
<?php
if (@$_GET['q'] == 10) {
    
    $result = mysqli_query($con, "SELECT * FROM quiz ORDER BY date DESC") or die('Error');
    echo '<table id="example4" class="table table-bordered table-striped">
  <thead>

<tr>

<th style="vertical-align:middle"><b>S.N.</b></th>
<th style="vertical-align:middle"><b>Name</b></th>
<th style="vertical-align:middle"><b>Total question</b></th>
<th style="vertical-align:middle"><b>Marks</b></th>
<th style="vertical-align:middle"><b>Time limit</b></th>
<th style="vertical-align:middle"><b>Status</b></th>
<th style="vertical-align:middle"><b>Action</b></th>
</tr>
</thead><tbody>';
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $title   = $row['title'];
        $total   = $row['total'];
        $correct = $row['correct'];
        $time    = $row['time'];
        $eid     = $row['eid'];
        $status  = $row['status'];
        if ($status == "enabled") {
            echo '<tr><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td><td style="vertical-align:middle">Enabled</td>
  <td style="vertical-align:middle"><b><a href="update.php?deidquiz=' . $eid . '" class="btn logb" style="color:#FFFFFF;background:#ff0000;font-size:12px;padding:5px;">&nbsp;<span><b>Disable</b></span></a></b>
  <a title="Delete User" href="update.php?q=rmquiz&eid=' . $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a> 
   <b><a href="dashboard.php?q=6&eid=' . $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> </a></b>
  <a title="Edit User" href="update.php?eusername=' .$eid  . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></b></a>
  
  </td>
 
  </tr>';
        } else {
            echo '<tr><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td><td style="vertical-align:middle">Disabled</td>
  <td style="vertical-align:middle"><b><a href="update.php?eeidquiz=' . $eid . '" class="btn logb" style="color:#FFFFFF;background:darkgreen;font-size:12px;padding:5px;">&nbsp;<span><b>Enable</b></span></a></b>
  <a title="Delete User" href="update.php?q=rmquiz&eid=' . $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a> 
  <b><a href="dashboard.php?q=6&eid=' . $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> </a></b>
  <a title="Edit User" href="update.php?eusername=' .$eid  . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></b></a>
  </td>
  </tr>';
            
        }
       
    }
    echo '<tbody></table>';
}
if (@$_GET['q'] == 2) {
    if(isset($_GET['show'])){
        $show = $_GET['show'];
        $showfrom = (($show-1)*10) + 1;
        $showtill = $showfrom + 9;
    }
    else{
        $show = 1;
        $showfrom = 1;
        $showtill = 10;
    }
    $q = mysqli_query($con, "SELECT * FROM rank") or die('Error223');
    echo '<table id="example10" class="table table-bordered table-striped">
  <thead>

<tr>
<th style="vertical-align:middle"><b>Sl.NO</b></th>
<th style="vertical-align:middle"><b>Name</b></th>
<th style="vertical-align:middle"><b>Branch</b></th>
<th style="vertical-align:middle"><b>Username</b></th>
<th style="vertical-align:middle"><b>Roll number</b></th>
<th style="vertical-align:middle"><b>Gender</b></th>
<th style="vertical-align:middle"><b>Score</b></th>
<th style="vertical-align:middle"><b>View</b></th>
<th style="vertical-align:middle"><b>Delete</b></th>
</tr>
</thead><tbody>';
    $c = $showfrom-1;
    $total = mysqli_num_rows($q);
    if($total >= $showfrom){
      
        $q = mysqli_query($con, "SELECT * FROM rank ORDER BY score DESC, time ASC LIMIT ".($showfrom-1).",10") or die('Error223');
        while ($row = mysqli_fetch_array($q)) {
            
            $e = $row['username'];
            $s = $row['score'];
            $q12 = mysqli_query($con, "SELECT * FROM user WHERE username='$e' ") or die('Error231');
            while ($row = mysqli_fetch_array($q12)) {
                $name     = $row['name'];
                $branch   = $row['branch'];
                $username = $row['username'];
                $rollno     = $row['rollno'];
                $gender   = $row['gender'];
               // $pass     = $row['password'];
            }
            $result = mysqli_query($con, "SELECT * FROM history WHERE username='$e'") or die('Error98');
            while ($row = mysqli_fetch_array($result)) {
                  $eid     = $row['eid'];
               
            }
            $c++;
            echo '<tr><td style="vertical-align:middle">' . $c. '</td>';
            echo '</td><td style="vertical-align:middle">' . $name . '</td><td style="vertical-align:middle">' . $branch . '</td><td style="vertical-align:middle">' . $username . '</td><td style="vertical-align:middle">' . $rollno . '</td><td style="vertical-align:middle">' . $gender . '</td><td style="vertical-align:middle">' . $s . '</td> 
            <td style="vertical-align:middle">
  <a href="dashboard.php?q=result&eid=' . $eid . '&username=' . $username . '" class="btn" style="margin:0px;background:darkred;color:white">&nbsp;<span class="title1"><b>View Result</b></span></a> </td>
  <td style="vertical-align:middle"> <a title="Delete User" href="update.php?q=rmrah&username=' . $username . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a>
  </td>';
          
        }
        echo '</tbody></table>';
    }
    
    else{
    }
   
    echo '<table id="example10" class="table table-bordered table-striped">


<tr>';
    $total = round($total/10) + 1;
    if(isset($_GET['show'])){
        $show = $_GET['show'];
    }
    else{
        $show = 1;
    }
    if($show == 1 && $total==1){
    }
    else if($show == 1 && $total!=1){
        $i = 1;
        while($i<=$total){
            echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.$i.'">&nbsp;'.$i.'&nbsp;</a></td>';
            $i++;
        }
        echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.($show+1).'">&nbsp;>>&nbsp;</a></td>';
    }
    else if($show != 1 && $show==$total){
        echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.($show-1).'">&nbsp;<<&nbsp;</a></td>';

        $i = 1;
        while($i<=$total){
            echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.$i.'">&nbsp;'.$i.'&nbsp;</a></td>';
            $i++;
        }
    }
    else{
        echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.($show-1).'">&nbsp;<<&nbsp;</a></td>';
        $i = 1;
        while($i<=$total){
            echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.$i.'">&nbsp;'.$i.'&nbsp;</a></td>';
            $i++;
        }
        echo '<td style="vertical-align:middle;text-align:center"><a style="font-size:14px;font-family:typo;font-weight:bold" href="dashboard.php?q=2&show='.($show+1).'">&nbsp;>>&nbsp;</a></td>';
    }
    echo '</tr></tbody></table>';
}

if (@$_GET['q'] == 1) {
    
    $result = mysqli_query($con, "SELECT * FROM user") or die('Error');
    echo '<table id="example3" class="table table-bordered table-striped">
  <thead>

<th style="vertical-align:middle"><b>S.N.</b></th>
<th style="vertical-align:middle"><b>Name</b></th>
<th style="vertical-align:middle"><b>Phno</b></th>
<th style="vertical-align:middle"><b>Gender</b></th>
<th style="vertical-align:middle"><b>Rollno</b></th>
<th style="vertical-align:middle"><b>Branch</b></th>
<th style="vertical-align:middle"><b>Username</b></th>
<th style="vertical-align:middle"><b>Password</b></th>
<th style="vertical-align:middle"><b>Due</b></td></th>
<th style="vertical-align:middle"><b>Status</b></th>
<th style="vertical-align:middle"><b>Action</b></th>
<td style="vertical-align:middle">Delete</td></th>
<th style="vertical-align:middle"><b>Edit</b></th>
</tr>
</thead><tbody>
';
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $name      = $row['name'];
        $phno      = $row['phno'];
        $gender    = $row['gender'];
        $rollno    = $row['rollno'];
        $branch    = $row['branch'];
        $username1 = $row['username'];
        $pass     = $row['password'];
        $d  = $row['due'];
        if ($d == "0") {
          echo '<tr><td style="vertical-align:middle">' . $c++ . '</td>';
            echo '<td style="vertical-align:middle">' .  $name  . '</td><td style="vertical-align:middle">' . $phno . '</td><td style="vertical-align:middle">' . $gender. '</td><td style="vertical-align:middle">' .  $rollno. '&nbsp;</td><td style="vertical-align:middle">' .$branch . '&nbsp;</td><td style="vertical-align:middle">' .  $username1. '&nbsp;</td><td style="vertical-align:middle">' . $pass . '</td><td style="vertical-align:middle">' .  $d . '&nbsp;Rs</td><td style="vertical-align:middle">Enabled</td>
  <td style="vertical-align:middle"><b><a href="update.php?ddue=' . $username1 . '" class="btn logb" style="color:#FFFFFF;background:#ff0000;font-size:12px;padding:5px;">&nbsp;<span><b>Disable</b></span></a></b></td>
  
  <td style="vertical-align:middle">
  <a title="Delete User" href="update.php?dusername=' . $eid  . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a> </td>
  <td style="vertical-align:middle"> <a title="Edit User" href="update.php?eusername=' .$eid  . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></b></a>
  </td>
  </tr>';
        } else {
            echo '<tr><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' .  $name  . '</td><td style="vertical-align:middle">' . $phno . '</td><td style="vertical-align:middle">' .  $gender . '</td><td style="vertical-align:middle">' .  $rollno. '&nbsp;</td><td style="vertical-align:middle">' .  $branch. '&nbsp;</td><td style="vertical-align:middle">' .  $username1. '&nbsp;</td><td style="vertical-align:middle">' . $pass . '</td><td style="vertical-align:middle">' .   $d . '&nbsp;Rs</td><td style="vertical-align:middle">Disabled</td>
  <td style="vertical-align:middle"><b><a href="update.php?edue=' . $username1 . '" class="btn logb" style="color:#FFFFFF;background:darkgreen;font-size:12px;padding:5px;">&nbsp;<span><b>Enable</b></span></a></b></td>
  
  <td style="vertical-align:middle">
  <a title="Delete User" href="update.php?dusername=' . $username1 . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a> </td>
  <td style="vertical-align:middle"> <a title="Edit User" href="update.php?eusername=' . $username1 . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></b></a>
  </td>
  
  </tr>';
            
        }
    
    }
   
    echo '</tbody><table>';
    
}


if (@$_GET['q'] == 3) {
    $result = mysqli_query($con, "SELECT * FROM `feedback` ORDER BY `feedback`.`date` DESC") or die('Error');
    echo '<table id="example1" class="table table-bordered table-striped">
  <thead>
<tr>
<th style="vertical-align:middle"><b>S.N.</b></th>
<th style="vertical-align:middle"><b>Subject</b></th>
<th style="vertical-align:middle"><b>Username</b></th>
<th style="vertical-align:middle"><b>Date</b></th>
<th style="vertical-align:middle"><b>Time</b></th>
<th style="vertical-align:middle"><b>By</b></th>
<th style="vertical-align:middle"></th>
<th style="vertical-align:middle"><b>Action</b></th>
</tr>
</thead>
<tbody>';
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $date      = $row['date'];
        $date      = date("d-m-Y", strtotime($date));
        $time      = $row['time'];
        $subject   = $row['subject'];
        $name      = $row['name'];
        $username1 = $row['username'];
        $id        = $row['id'];
        echo '<tr><td style="vertical-align:middle">' . $c++ . '</td>';
        echo '<td style="vertical-align:middle"><a title="Click to open feedback" href="dashboard.php?q=3&fid=' . $id . '">' . $subject . '</a></td><td style="vertical-align:middle">' . $username1 . '</td><td style="vertical-align:middle">' . $date . '</td><td style="vertical-align:middle">' . $time . '</td><td style="vertical-align:middle">' . $name . '</td>
  <td style="vertical-align:middle"><a title="Open Feedback" href="dashboard.php?q=3&fid=' . $id . '"><b><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span></b></a></td>';
        echo '<td style="vertical-align:middle"><a title="Delete Feedback" href="update.php?fdid=' . $id . '"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a></td>

  </tr>';
    }
    echo '<tbody></table>';
}
if (@$_GET['fid']) {
    echo '<br />';
    $id = @$_GET['fid'];
    $result = mysqli_query($con, "SELECT * FROM feedback WHERE id='$id' ") or die('Error');
    while ($row = mysqli_fetch_array($result)) {
        $name     = $row['name'];
        $subject  = $row['subject'];
        $date     = $row['date'];
        $date     = date("d-m-Y", strtotime($date));
        $time     = $row['time'];
        $feedback = $row['feedback'];
        
        echo '<div class="panel"<a title="Back to Archive" href="update.php?q1=2"><b><span class="glyphicon glyphicon-level-up" aria-hidden="true"></span></b></a><h2 style="text-align:center; margin-top:-15px;font-family: "Ubuntu", sans-serif;"><b>' . $subject . '</b></h1>';
        echo '<div class="mCustomScrollbar" data-mcs-theme="dark" style="margin-left:10px;margin-right:10px; max-height:450px; line-height:35px;padding:5px;"><span style="line-height:35px;padding:5px;">-&nbsp;<b>DATE:</b>&nbsp;' . $date . '</span>
<span style="line-height:35px;padding:5px;">&nbsp;<b>Time:</b>&nbsp;' . $time . '</span><span style="line-height:35px;padding:5px;">&nbsp;<b>By:</b>&nbsp;' . $name . '</span><br />' . $feedback . '</div></div>';
    }
}
if (@$_GET['q'] == 4 && !(@$_GET['step'])) {
    echo ' 
<div class="row">
<span class="title1" style="margin-left:40%;font-size:30px;"><b>Enter Quiz Details</b></span><br /><br />
 <div class="col-md-3"></div><div class="col-md-6">   <form class="form-horizontal title1" name="form" action="update.php?q=addquiz"  method="POST">
<fieldset>
<div class="form-group">
  <label class="col-md-12 control-label" for="name"></label>  
  <div class="col-md-12">
  <input id="name" name="name" placeholder="Enter Quiz title" class="form-control input-md" type="text">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="total"></label>  
  <div class="col-md-12">
  <input id="total" name="total" placeholder="Enter total number of questions" class="form-control input-md" type="number">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="right"></label>  
  <div class="col-md-12">
  <input id="right" name="right" placeholder="Enter marks on right answer" class="form-control input-md" min="0" type="number">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="wrong"></label>  
  <div class="col-md-12">
  <input id="wrong" name="wrong" placeholder="Enter minus marks on wrong answer without sign" class="form-control input-md" min="0" type="number">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="time"></label>  
  <div class="col-md-12">
  <input id="time" name="time" placeholder="Enter time limit for test in minute" class="form-control input-md" min="1" type="number">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-12 control-label" for=""></label>
  <div class="col-md-12"> 
    <input  type="submit" style="margin-left:45%" class="btn btn-primary" value="Submit" class="btn btn-primary"/>
  </div>
</div>

</fieldset>
</form></div>';
    
    
    
}
if (@$_GET['q'] == 4 && (@$_GET['step']) == 2) {
    echo ' 
<div class="row">
<span class="title1" style="margin-left:40%;font-size:30px;"><b>Enter Question Details</b></span><br /><br />
 <div class="col-md-3"></div><div class="col-md-6"><form class="form-horizontal title1" name="form" action="update.php?q=addqns&n=' . @$_GET['n'] . '&eid=' . @$_GET['eid'] . '&ch=4 "  method="POST">
<fieldset>
';
    
    for ($i = 1; $i <= @$_GET['n']; $i++) {
        echo '<b>Question number&nbsp;' . $i . '&nbsp;:</><br /><!-- Text input-->
<div class="form-group">
  <label class="col-md-12 control-label" for="qns' . $i . ' "></label>  
  <div class="col-md-12">
  <textarea rows="3" cols="5" name="qns' . $i . '" class="form-control" placeholder="Write question number ' . $i . ' here..."></textarea>  
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="' . $i . '1"></label>  
  <div class="col-md-12">
  <input id="' . $i . '1" name="' . $i . '1" placeholder="Enter option a" class="form-control input-md" type="text">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="' . $i . '2"></label>  
  <div class="col-md-12">
  <input id="' . $i . '2" name="' . $i . '2" placeholder="Enter option b" class="form-control input-md" type="text">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="' . $i . '3"></label>  
  <div class="col-md-12">
  <input id="' . $i . '3" name="' . $i . '3" placeholder="Enter option c" class="form-control input-md" type="text">
    
  </div>
</div>
<div class="form-group">
  <label class="col-md-12 control-label" for="' . $i . '4"></label>  
  <div class="col-md-12">
  <input id="' . $i . '4" name="' . $i . '4" placeholder="Enter option d" class="form-control input-md" type="text">
    
  </div>
</div>
<br />
<b>Correct answer</b>:<br />
<select id="ans' . $i . '" name="ans' . $i . '" placeholder="Choose correct answer " class="form-control input-md" >
   <option value="a">Select answer for question ' . $i . '</option>
  <option value="a">option a</option>
  <option value="b">option b</option>
  <option value="c">option c</option>
  <option value="d">option d</option> </select><br /><br />';
    }
    
    echo '<div class="form-group">
  <label class="col-md-12 control-label" for=""></label>
  <div class="col-md-12"> 
    <input  type="submit" style="margin-left:45%" class="btn btn-primary" value="Submit" class="btn btn-primary"/>
  </div>
</div>

</fieldset>
</form></div>';
    
    
    
}
if (@$_GET['q'] == 5) {
    
    $result = mysqli_query($con, "SELECT * FROM quiz ORDER BY date DESC") or die('Error');
    echo '<table id="example5" class="table table-bordered table-striped><thead>
<tr>
<th style="vertical-align:middle"><b>S.N.</b></th>
<th style="vertical-align:middle"><b>Topic</b></th>
<th style="vertical-align:middle"><b>Total question</b></th>
<th style="vertical-align:middle"><b>Marks</b></th>
<th style="vertical-align:middle"><b>Time limit</b></th>
<th style="vertical-align:middle"><b>Action</b></th>
</tr></thead><tbody>';
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $title   = $row['title'];
        $total   = $row['total'];
        $correct = $row['correct'];
        $time    = $row['time'];
        $eid     = $row['eid'];
        echo '<tr><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td>
  <td style="vertical-align:middle"><b><a href="update.php?q=rmquiz&eid=' . $eid . '" class="btn" style="margin:0px;background:red;color:white">&nbsp;<span class="title1"><b>Remove</b></span></a></b>
  <b><a href="dashboard.php?q=6&eid=' . $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> </a></b>
  <b><a href="dashboard.php?q=8&eid=' . $eid . '" class="btn" style="margin:0px;background:lightgreen;color:white"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> </a></b>
 
  </td></tr>';
    }
    $c = 0;
    echo '</tbody></table>';
    
}


if (@$_GET['q'] == 6 && @$_GET['eid']) {
    $eid = @$_GET['eid'];
    $result = mysqli_query($con, "SELECT * FROM questions where eid='$eid'") or die('Error');
	echo '<div class="panel" style="margin-right:5%;margin-left:5%;margin-top:10px;border-radius:10px">';
	
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $eids      = $row['eid'];
        $qids      = $row['qid'];
        $qnss    = $row['qns'];
		echo '<b><pre style="background-color:white"><div style="font-size:20px;font-weight:bold;font-family:calibri;margin:10px">' . $c++ . ' : ' . $qnss. ' </div></pre></b>';
		
		
		$result1 = mysqli_query($con, "SELECT * FROM options where qid='$qids'") or die('Error');
		
        //$row1 = mysqli_fetch_array($result1);
		
		$n = 'A';
		while ($row1 = mysqli_fetch_array($result1)) {
			$qid=$row1['qid'];
			$opt=$row1['optionid'];
                $option   = stripslashes($row1['option']);
                echo '<div class="pure-form"><div class="funkyradio-success" style="vertical-align:middle">' . $n++ . ' :&nbsp;&nbsp;' . $option . '</div></div>';


    }
	 $n = 0;
	$result2 = mysqli_query($con, "SELECT * FROM answer where qid='$qids'") or die('Error');
        while ($row2 = mysqli_fetch_array($result2)) {
		$ans   = stripslashes($row2['ansid']);
		$q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$ans'") or die('Error197');
                $row3       = mysqli_fetch_array($q4);
                $correctans = $row3['option'];
				$P='Ans' ;
                
		}
		echo '<div class="funkyradio"><div class="pure-form"><font style="color:green;font-size:18px;font-weight:bold">'  .$P . ' :&nbsp;' .$correctans. '<a title="Delete User" href="update.php?eid=' . $qids . ' &optid='.$ans.'"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a>    </font></div></div>';
	}
	
    $c = 0;
    echo '</div>';
    
}
//result

if (@$_GET['q'] == 'result' && @$_GET['eid']) {
    $eid = @$_GET['eid'];
    $username = @$_GET['username'];
    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error157');
    while ($row = mysqli_fetch_array($q)) {
        $total = $row['total'];
    }
    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$username' ") or die('Error157');
    
    while ($row = mysqli_fetch_array($q)) {
        $s      = $row['score'];
        $w      = $row['wrong'];
        $r      = $row['correct'];
        $status = $row['status'];
    }
    if ($status == "finished") {
        echo '<div class="panel">
<center><h1 class="title" style="color:#660033">Result</h1><center><br />
<center><h1 class="title" style="color:#669933"> ' . $username . '</h1><center><br />
<table class="table table-striped title1" style="font-size:20px;font-weight:1000;">';
        echo '<tr style="color:darkblue"><td style="vertical-align:middle">Total Questions</td><td style="vertical-align:middle">' . $total . '</td></tr>
      <tr style="color:darkgreen"><td style="vertical-align:middle">Correct Answer&nbsp;<span class="glyphicon glyphicon-ok-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $r . '</td></tr> 
    <tr style="color:red"><td style="vertical-align:middle">Wrong Answer&nbsp;<span class="glyphicon glyphicon-remove-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $w . '</td></tr>
    <tr style="color:orange"><td style="vertical-align:middle">Unattempted&nbsp;<span class="glyphicon glyphicon-ban-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($total - $r - $w) . '</td></tr>
    <tr style="color:darkblue"><td style="vertical-align:middle">Score&nbsp;<span class="glyphicon glyphicon-star" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $s . '</td></tr>';
        $q = mysqli_query($con, "SELECT * FROM rank WHERE  username='$username' ") or die('Error157');
        while ($row = mysqli_fetch_array($q)) {
            $s = $row['score'];
            echo '<tr style="color:#990000"><td style="vertical-align:middle">Overall Score&nbsp;<span class="glyphicon glyphicon-stats" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $s . '</td></tr>';
        }
        echo '<tr></tr></table></div><div class="panel"><br /><h3 align="center" style="font-family:calibri">:: Detailed Analysis ::</h3><br /><ol style="font-size:20px;font-weight:bold;font-family:calibri;margin-top:20px">';
        $q = mysqli_query($con, "SELECT * FROM questions WHERE eid='$_GET[eid]'") or die('Error197');
        while ($row = mysqli_fetch_array($q)) {
            $question = $row['qns'];
            $qid      = $row['qid'];
            $q2 = mysqli_query($con, "SELECT * FROM user_answer WHERE eid='$_GET[eid]' AND qid='$qid' AND username='$username'") or die('Error197');
            if (mysqli_num_rows($q2) > 0) {
                $row1         = mysqli_fetch_array($q2);
                $ansid        = $row1['ans'];
                $correctansid = $row1['correctans'];
                $q3 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$ansid'") or die('Error197');
                $q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$correctansid'") or die('Error197');
                $row2       = mysqli_fetch_array($q3);
                $row3       = mysqli_fetch_array($q4);
                $ans        = $row2['option'];
                $correctans = $row3['option'];
            } else {
                $q3 = mysqli_query($con, "SELECT * FROM answer WHERE qid='$qid'") or die('Error197');
                $row1         = mysqli_fetch_array($q3);
                $correctansid = $row1['ansid'];
                $q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$correctansid'") or die('Error197');
                $row2       = mysqli_fetch_array($q4);
                $correctans = $row2['option'];
                $ans        = "Unanswered";
            }
            if ($correctans == $ans && $ans != "Unanswered") {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:lightgreen;padding:10px;word-wrap:break-word;border:2px solid darkgreen;border-radius:10px;">' . $question . ' <span class="glyphicon glyphicon-ok" style="color:darkgreen"></span></div><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Your Answer: </b></font><font style="font-size:14px;">' . $ans . '</font><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font><br />';
            } 
            else if ($ans == "Unanswered") {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f7f576;padding:10px;word-wrap:break-word;border:2px solid #b75a0e;border-radius:10px;">' . $question . ' </div><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font><br />';
            } 
            else {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f99595;padding:10px;word-wrap:break-word;border:2px solid darkred;border-radius:10px;">' . $question . ' <span class="glyphicon glyphicon-remove" style="color:red"></span></div><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Your Answer: </b></font><font style="font-size:14px;">' . $ans . '</font><br />';
                echo '<font style="font-size:14px;color:red"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font><br />';
                
            }
            echo "<br /></li>";
        }
        echo '</ol>';
        echo "</div>";
    
        
    } else {
        die("Thats a 404 Error bro. You are trying to access a wrong page");
    }
   
}


if (@$_GET['q'] == 8) {
    
    $result = mysqli_query($con, "SELECT * FROM history") or die('Error');
    echo '<div class="panel"><table id="example6" class="table table-bordered table-striped>
<tr><td style="vertical-align:middle"><b>S.N.</b></td><td style="vertical-align:middle"><b>Username</b></td><td style="vertical-align:middle"><b>Correct</b></td><td style="vertical-align:middle"><b>Wrong</b></td><td style="vertical-align:middle"><b>Score</b></td><td style="vertical-align:middle"><b>Date</b></td><td style="vertical-align:middle"><b>Time</b></td></td><td style="vertical-align:middle"><b>Status</b></td><td style="vertical-align:middle">Delete</td></td><td style="vertical-align:middle"><b>Edit</b></td></tr>';
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $username = $row['username'];
        $correct   = $row['correct'];
        $wrong    = $row['wrong'];
        $score    = $row['score'];
        $status    = $row['status'];
        $date    = $row['date'];
        $time    = $row['time'];
         $eid    = $row['eid'];
       
     
     echo '<tr><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' .$username. '</td><td style="vertical-align:middle">' . $correct . '</td><td style="vertical-align:middle">' . $wrong  . '</td><td style="vertical-align:middle">' .  $score  . '</td><td style="vertical-align:middle">' . $date . '</td><td style="vertical-align:middle">' . $time . '</td><td style="vertical-align:middle">' . $status . '</td>
  <td style="vertical-align:middle">
  <a title="Delete User" href="dashboard.php?q=result&eid=' . $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a>
  <a title="Edit User" href="dashboard.php?q=result&eid=' .  $eid . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></b></a>
  <a title="Delete User" href="update.php?q=rmrah&username=' . $username . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a>
  </td></tr>';
    }
    $c = 0;
    echo '</table>';
    
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
<?php include_once('../footer.php');?>


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
    $('#example1').DataTable()
    $('#example3').DataTable()
    $('#example4').DataTable()
    $('#example5').DataTable()
    $('#example6').DataTable()
    $('#example7').DataTable()
    $('#example8').DataTable()
    $('#example9').DataTable()
    $('#example10').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>

</body>
</html>