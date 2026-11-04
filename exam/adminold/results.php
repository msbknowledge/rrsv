
<!DOCTYPE html>
<html>
<?php
 error_reporting(0); 
 session_start();
include_once 'dbConnection.php';


?>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>MSB EXAM</title>
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

if (!(isset($_SESSION['username']))) {
    header("location:index.php");
} else {
    $name     = $_SESSION['name'];
    $username = $_SESSION['username'];
    
    echo '<span class="pull-right top title1" ></span> <span class="log log1" style="color:lightyellow">' . $username . '</span>';
}
?>  
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../css/download.png" class="img-circle" alt="User Image">

                <p>
                 <?php echo 'Welcome '.$username; ?>              
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
        <li><a href="#">Exam Management</a></li>
        <li class="active">Result</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- Note Start -->
      <div class="col-md-12">
		<div class="box box-danger">
			<div class="box-header with-border">
			           
              <h3 class="box-title"><i class="fa fa-warning text-yellow"></i> Note:</h3> Dont Send any Promotional sms in Transactional Route,Other wise your Account will Be Blocked.	
			</div>
		</div>
	  </div>
        <!-- Note End -->
        <!-- Send sms box start -->
	<div class="col-md-12">
          <div class="box box-primary">
           <!-- /.header-->
            <div class="box-header with-border">
              <h3 class="box-title">Score Board</h3>
              
               <div style="text-align: right";>
             <h4 class="btn btn-success btn-sm"> 
DLT Registration  </h4>
                </div>
                
		 </div>
             <!-- /.header-->
            
			
              <div class="box-body">
               
				 <!-- /box-body-->
                                 <?php
                                 
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

<center><p class="title" style="color:#669933"> ' . $username . '</p><center><br />
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

	?>			
                 <?php
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
			
			echo '<table id="example1" class="table table-bordered table-striped" >
                <thead>
                <tr>
                  <th>S.NO</th>
				  
                  <th>Name</th>
				  <th>Branch</th>
                                  <th>Gender</th>
                                  <th>username</th>
                                  <th>Rollno</th>
                                  <th>Score</th>
                                  
                                  <th>Edit</th>
                                  <th>Delete</th>
                 
                </tr>
                </thead>
                <tbody>'; 
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
           
	 echo '      <tr>
		 <td>' . $c . '</td>
                  <td>' . $name . '</td>
		 <td>' . $branch . '</td>
                  <td>' .$gender . '</td>
                   <td>' . $username . '</td>
                    <td>' . $rollno . '</td>
                    <td>' .  $s . '</td>
                  
              <td style="vertical-align:middle">
  <a href="dash.php?q=result&eid=' . $eid . '&username=' . $username . '" class="btn" style="margin:0px;background:darkred;color:white">&nbsp;<span class="title1"><b>View Result</b></span></a> </td>
  <td style="vertical-align:middle"> <a title="Delete User" href="update.php?q=rmrah&username=' . $username . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="fa fa-trash" aria-hidden="true"></span></b></a>
  </td>
                </tr>';
                 }
    }
    echo '					</tbody>
				</table>';
	
        }?>
				 
               
			
             
             
              
              
              
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




</body>
</html>