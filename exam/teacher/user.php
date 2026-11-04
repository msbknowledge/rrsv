
	  <?php

error_reporting(0); 
session_start();
include_once 'dbConnection.php';
if (!isset($_SESSION['username']))

{
        header("location:../index.php");
     

} 
?>

<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Online Exame</title>
   <link rel="icon" type="image/x-icon" href="/../msb1.ico">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <!-- Font Awesome -->
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
  
  
  
  
$(function () {
    $(document).on( 'scroll', function(){
        console.log('scroll top : ' + $(window).scrollTop());
        if($(window).scrollTop()>=$(".logo").height())
        {
             $(".navbar").addClass("navbar-fixed-top");
        }

        if($(window).scrollTop()<$(".logo").height())
        {
             $(".navbar").removeClass("navbar-fixed-top");
        }
    });
});

  
// form display 
$(document).ready(function(){
	
	$('#add_button').click(function(){
		$('#modal_title').text('Add Student');
		$('#button_action').val('Add');
		$('#action').val('Add');
		$('#formModal').modal('show');
		clear_field();
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

    
    
    
    
    
    
    
<div class="modal" id="formModal">
  <div class="modal-dialog">
  	<form action="update.php?q=std" method="post" id="student_form">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="modal_title">Add Student</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Student Name <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="name" id="student_name" class="form-control" />
                <span id="error_student_name" class="text-danger"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Roll No. <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="rollno" id="student_roll_number" class="form-control" />
                <span id="error_student_roll_number" class="text-danger"></span>
              </div>
            </div>
          </div>
         

          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Branch<span class="text-danger">*</span></label>
              <div class="col-md-8">
                <select id="branch" name="branch" placeholder="Select your branch" class="form-control input-md" >
   <option value="">Select Branch</option>
  <option value="COPA">COPA</option>
  <option value="Electrician" >Electrician</option>
  <option value="Fitter">Fitter</option>
  <option value="Surveyor">Surveyor</option>
  <option value="Wireman">Wireman</option>
  <option value="Welder">Welder</option> 
  <option value="Diploma">Diploma</option> 
  <option value="B.TECH">B.TECH</option> 
  <option value="IMSC">IMSC</option> </select>
              <span id="error_student_grade_id" class="text-danger"></span>
              </div>
            </div>
          </div>

           <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">user name <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="username" id="student_dob" class="form-control" />
                <span id="error_student_dob" class="text-danger"></span>
              </div>
            </div>
          </div>

           <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">password<span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="password" id="student_dob" class="form-control" />
                <span id="error_student_dob" class="text-danger"></span>
              </div>
            </div>
          </div>

        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
         <!--	<input type="hidden" name="student_id" id="student_id" />
        	<input type="hidden" name="action" id="action" value="Add" />-->
        	<input type="submit" name="button_action" id="button_action" class="btn btn-success btn-sm" value="Add" />
          	<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>

      </div>
  </form>
  </div>
</div>


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
            
          <!--  <span style="font-family:Calibri, Arial; font-size:10px; color:#FFFFFF;  padding-right:30px; float:right; margin-top:10px;">Support - Call Us :09246555511 <br><div align="right">040- 67978811 </div> </span> -->
            
          <!-- Messages: style can be found in dropdown.less-->
          
                 
          <!-- User Account: style can be found in dropdown.less -->
           <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../css/download.png" class="user-image" alt="User Image">
			  <?php

if (!isset($_SESSION['username']))

{
        header("location:../index.php");
     

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
                  <a href="logout.php" class="btn btn-default btn-flat">Logout</a>
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
  Teacher's
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Student Exam</a></li>
        <li class="active">Student</li>
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
              <h3 class="box-title">Students</h3>
              
               <div style="text-align: right";>
               <a href="#" class="btn-sm btn btn-primary" data-toggle="modal" data-target="#formModal" style="margin-bottom:4px;" target="_blank">Add Student</a>
		
 
                </div>
                
		 </div>
             <!-- /.header-->
            
			
              <div class="box-body">
               
				 <!-- /box-body-->
				
                 <?php
                         if (@$_GET['q'] == 1) {
    
    $result = mysqli_query($con, "SELECT * FROM user") or die('Error');
    echo '<table id="example1" class="table table-bordered table-striped" >
                <thead>
                <tr>
                  <th>S.NO</th>
				  
                  <th> Name</th>
				  
                                  <th>roll</th>
                                  <th>Branch</th>
                                   <th>user</th>
                                  <th>pass</th>
                                   <th>due</th>
                                  <th>status</th>
                                  <th>Actions</th>
                               
                 
                </tr>
                </thead>
                <tbody>';
    $c = 1;
    while ($row = mysqli_fetch_array($result)) {
        $name      = $row['name'];
        $phno      = $row['phno'];
        $gender    = $row['gender'];
        $rollno    = $row['rollno'];
        $branch    = $row['branch'];
        $username = $row['username'];
        $pass     = $row['password'];
        $d  = $row['due'];
        if ($d == "0") {
            echo '<tr>
		 <td>' . $c++. '</td>
                  <td>' . $name . '</td>
		
                  <td>' . $rollno . '</td>
                   <td>' . $branch . '</td>
                   <td>' . $username . '</td>
                   <td>' . $pass . '</td>
                    
                    <td>' .  $d . '</td>
                  
              <td style="vertical-align:middle">Enabled</td>
  <td style="vertical-align:middle">
  <a href="update.php?ddue='.$username.'" class="btn btn-success btn-xs" ><i class="fa fa-minus-square" aria-hidden="true"></i></a>
 
  <a title="Veiw User" href="update.php?dusername=' . $usernam . '"  class="btn btn-warning btn-xs" ><i class="fa fa-eye" aria-hidden="true"></i></a>
   <a title="Edit User" href="update.php?eusername=' .$eid  . '"  class="btn btn-info btn-xs" ><i class="fa fa-edit" aria-hidden="true"></i></a>
    <a title="Delete User" href="update.php?dusername=' . $username . '"  class="btn btn-danger btn-xs" ><i class="fa fa-trash" aria-hidden="true"></i></a> 
  
  </td>
  </tr>';
        }else {
            echo '<tr> <td>' . $c . '</td>
                  <td>' . $name . '</td>
		
                  <td>' . $rollno . '</td>
                   <td>' . $branch . '</td>
                   <td>' . $username . '</td>
                   <td>' . $pass . '</td>
                    
                    <td>' .  $d . '</td>
                    <td style="vertical-align:middle">Disabled</td>
  <td style="vertical-align:middle">
  <a href="update.php?edue=' . $username. '" class="btn btn-danger btn-xs" ><i class="fa fa-minus-square" aria-hidden="true"></i></a> 
 
  <a title="veiw User" href="update.php?eusername=' . $username. '"  class="btn btn-warning btn-xs" ><i class="fa fa-eye" aria-hidden="true"></i></a>
 
  <a title="Edit User" href="update.php?eusername=' .$eid  . '"  class="btn btn-info btn-xs" ><i class="fa fa-edit" aria-hidden="true"></i></a>
   <a title="Delete User" href="update.php?dusername=' . $username. '"  class="btn btn-info btn-xs" ><i class="fa fa-trash" aria-hidden="true"></i></a> 
  
  </td>
  </tr>';
            
        }
       }
    $c = 0;					
    echo ' </tbody>
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