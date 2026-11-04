
<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Bulk-SMS-Gateway | User</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
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
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
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
      <span class="logo-lg"><img src="/use_logo/logo88065.jpg" style="height:100%;width:144px"></span>
	      </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            
          <!--  <span style="font-family:Calibri, Arial; font-size:10px; color:#FFFFFF;  padding-right:30px; float:right; margin-top:10px;">Support - Call Us :09246555511 <br><div align="right">040- 67978811 </div> </span> -->
            
          <!-- Messages: style can be found in dropdown.less-->
          
                 
          <!-- User Account: style can be found in dropdown.less -->
           <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="css/download.png" class="user-image" alt="User Image">
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
    echo '<span class="pull-right top title1" >&nbsp;&nbsp;&nbsp;&nbsp;Wellcome</span> <span class="log log1" style="color:lightyellow">' . $username . '</span>';
}
?>  
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="css/download.png" class="img-circle" alt="User Image">

                <p>
                  Welcome MSB                  <small></small>
                </p>
              </li>
              <!-- Menu Body -->
             
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="logout" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
			
            
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="logout">
			<img src="css/logout.png" class="img-circle" alt="Logout">
			 Logout</a>
          </li>
        </ul>
      </div>
    </nav>
  </header><!-- Header End -->
  <!-- Left side column. contains the logo and sidebar -->
<!-- Sidebar Start -->
   <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <!--<li ><a href="https://login.bulksmsgateway.in/userindex"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>-->
        <li class="treeview active">
          <a href="#">
            <i class="fa fa-hand-o-right"></i>
            <span>Credit Balance</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <li <?php
if (@$_GET['q'] == 2)
    echo 'class="active"';
?>><a href="login.php?q=2">Leaderboard</a></li>	
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Normal: 0</font></a></li>
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Priority: 5</font></a></li>
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Transactional: 5</font></a></li>
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Promotional: 5</font></a></li>
						
			 <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Voice Normal: 0</font></a></li>
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Voice Priority: 0</font></a></li>
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Voice Transactional: 0</font></a></li>
            <li><a href="#"><i class="fa fa-rupee"></i> <font color="#a0d0e0">Voice Promotional: 0</font></a></li>
          </ul>
        </li>
	
         <li class="treeview active">
          <a href="#">
            <i class="fa fa-envelope-o"></i>
            <span>SMS Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
		  
            <li class='active'><a href="https://login.bulksmsgateway.in/sendsms"><i class="fa fa-circle-o"></i> Send SMS</a></li>
			            <li ><a href="https://login.bulksmsgateway.in/buy_credits"><i class="fa fa-circle-o"></i> Buy Credits</a></li>
						
			 <li ><a href="https://login.bulksmsgateway.in/dlt_templates"><i class="fa fa-circle-o"></i> Add Approved Templates</a></li>
			
			
           <!-- <li ><a href="https://login.bulksmsgateway.in/longcodereport"><i class="fa fa-circle-o"></i> Long Code Report</a></li>-->
           
           
           <li ><a href="https://login.bulksmsgateway.in/quickwhtsappmessage"><i class="fa fa-circle-o"></i>Whatsapp SMS</a></li>
           
           
           
			<li ><a href="https://login.bulksmsgateway.in/voicesms"><i class="fa fa-circle-o"></i> Voice SMS</a></li>
			
		
			
			<li ><a href="https://login.bulksmsgateway.in/schedule"><i class="fa fa-circle-o"></i> Schedule SMS</a></li>
			<li ><a href="https://login.bulksmsgateway.in/dynamic_sms"><i class="fa fa-circle-o"></i> Dynamic SMS</a></li>
			<li ><a href="https://login.bulksmsgateway.in/addsenderid"><i class="fa fa-circle-o"></i> Add Sender ID</a></li>
			<li ><a href="https://login.bulksmsgateway.in/listsenderids"><i class="fa fa-circle-o"></i> List Sender ID</a></li>
			<li ><a href="https://login.bulksmsgateway.in/sent"><i class="fa fa-circle-o"></i> Sent Report</a></li>
									
			
						
			
			
				<!--	<li ><a href="shorturlreport"><i class="fa fa-circle-o"></i> ShortUrl Report</a></li> --> 	
	
			
			<li ><a href="https://login.bulksmsgateway.in/drafts"><i class="fa fa-circle-o"></i> Templates</a></li>
			<li ><a href="https://login.bulksmsgateway.in/editgroup"><i class="fa fa-circle-o"></i> Groups</a></li>
			
	    <!--	<li ><a href="voicefile"><i class="fa fa-circle-o"></i>Voicefiles</a></li>-->
			
			
			<li ><a href="phonebook"><i class="fa fa-circle-o"></i> Phone Book</a></li>
			
			
			
		  </ul>
        </li>
       
         <li class="treeview ">
          <a href="#">
            <i class="fa fa-file-code-o"></i>
            <span>Developer API</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="https://login.bulksmsgateway.in/apiurl"><i class="fa fa-circle-o"></i> API URL</a></li>
            
            <li ><a href="https://login.bulksmsgateway.in/whatsappapiurl"><i class="fa fa-circle-o"></i> Whatsapp SMS API URL</a></li>
            
            <li ><a href="https://login.bulksmsgateway.in/balancheckapi"><i class="fa fa-circle-o"></i> Balance Check API</a></li>
            <li ><a href="https://login.bulksmsgateway.in/schedulesms"><i class="fa fa-circle-o"></i> Schedule SMS API</a></li>
            <li ><a href="https://login.bulksmsgateway.in/languagesms"><i class="fa fa-circle-o"></i> Language SMS API</a></li>
            <li ><a href="https://login.bulksmsgateway.in/phpscript"><i class="fa fa-circle-o"></i> PHP</a></li>
            <li ><a href="https://login.bulksmsgateway.in/javascript"><i class="fa fa-circle-o"></i> JAVA</a></li>
			<li ><a href="https://login.bulksmsgateway.in/dontnetscript"><i class="fa fa-circle-o"></i> ASP.NET</a></li>
			<li ><a href="https://login.bulksmsgateway.in/vbscript"><i class="fa fa-circle-o"></i> VB.NET</a></li>
			<li ><a href="https://login.bulksmsgateway.in/cscript"><i class="fa fa-circle-o"></i> C#</a></li>
          </ul>
        </li>
       <li class="treeview ">
          <a href="#">
            <i class="fa fa-wrench"></i>
            <span>Personal Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="https://login.bulksmsgateway.in/cpass"><i class="fa fa-key"></i> Change Password</a></li>
			 <li ><a href="https://login.bulksmsgateway.in/emailupdate"><i class="fa fa-at"></i> Update Email</a></li>
            
          </ul>
        </li>
        
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
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
        Send SMS
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">SMS Management</a></li>
        <li class="active">Send SMS</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- Note Start -->
      <div class="col-md-12">
		<div class="box box-danger">
			<div class="box-header with-border">
			            <a href="" class="btn-sm btn btn-primary" data-toggle="modal" data-target="#login" style="margin-bottom:4px;" target="_blank">Admin Login</a>
						<a href="demovideos" class="btn btn-sm btn-danger" style="margin-bottom:4px;" target="_blank">Demo Videos</a><br>
			             
              <h3 class="box-title"><i class="fa fa-warning text-yellow"></i> Note:</h3> Dont Send any Promotional sms in Transactional Route,Other wise your Account will Be Blocked.	
			</div>
		</div>
	  </div>
        <!-- Note End -->
        <!-- Send sms box start -->
		<div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Send SMS</h3>
              
               <div style="text-align: right";>
             <h4 class="btn btn-success btn-sm"> 
DLT Registration  </h4>
                </div>
                
			 			   			   </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal">
			<div style="display:none">
			<input type="hidden" name="bulk_test_name" value="e4f87ca11495dc0bc72d205815695efc">
		</div>
              <div class="box-body">
               <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label"></label>
				  <div class="col-sm-8">
				 <!-- /.header-->
				 
				 h
				 
                </div>
				</div>
			
			
			 <!-- /.content-->
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
				  <th>Group Name</th>
                  <th>Name</th>
				  <th>Phone</th>
                  <th style="text-align: center">Action</th>
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
           
				      echo '      <tr>
				  <td>' . $c . '</td>
                  <td>' . $name . '</td>
				  <td>' . $branch . '</td>
                  
              <td style="vertical-align:middle">
  <a href="dash.php?q=result&eid=' . $eid . '&username=' . $username . '" class="btn" style="margin:0px;background:darkred;color:white">&nbsp;<span class="title1"><b>View Result</b></span></a> </td>
  <td style="vertical-align:middle"> <a title="Delete User" href="update.php?q=rmrah&username=' . $username . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a>
  </td>
                </tr>';
                 }
    }
    echo '					</tbody>
				</table>';
	
        }?>
			 
				
		
			
              </div>
              <!-- /.box-body
              
              <td style="text-align: center"><a href="addphonenumbers?contact_id=Mzc2OTE5Mw__&group_id=MTY4MzQ_" data-toggle="tooltip" title="Edit" class="btn btn-info"><i class="fa fa-edit"></i></a>
				 <a href="javascript:del_Users('.Mzc2OTE5Mw__.')" data-toggle="tooltip" title="Delete" class="btn btn-danger"><i class="fa fa-trash"></i></a>
             </td>
              -->
              <div class="box-footer">
                
				 <!-- /.footer-->
				
                </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!-- Send sms box end -->
		<!-- Instructions box start-->
		<div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Instruction To User</h3>
				<div class="box-tools pull-right">
				</div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="timeline-body">
					Enter Mobile Numbers one by one( without 91)<br>
					
                </div>
			 </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
		</div>
		<!-- Instructions box end-->
		<!-- template message box start-->
		<div class="col-md-4">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Template Messages</h3>
              <div class="box-tools pull-right">
              </div>
            </div>
            <!-- /.box-header -->
 			<div class="box-body"  style="background-color:#E6E6FA">
			 
				
				
				             
        
			 </div>
            <!-- /.box-body -->
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