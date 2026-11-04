
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
  <?php include_once('../header.php');?>
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
   .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
   
}
.alert a{ text-decoration: none;}
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
  

if (!isset($_SESSION['username']))

{

     
        header("location:../login.php");
        exit();

}


// if (!isset($_SESSION["username"])) {
//     header("location:../login.php");
// }
 else {
    $name     = $_SESSION['name'];
    $username = $_SESSION['username'];
     $id = $_SESSION['id'];
    
    
    include_once 'dbConnection.php';
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
                  <a href="profile.php=q=7&id=<?php echo $id;?> " class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="../logout.php?q=dash.php" class="btn btn-default btn-flat">Logout</a>
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
  Teacher's
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Teacher's Management</a></li>
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
            <h4> Student <i class="pull-right fa fa-users"></i> </h4>
            <span class="d-block"> 
            <?php
                
                $query = "SELECT id FROM rrsv_student_registration ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
           </span> 
           <a href="user.php?q=1" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>               
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-blue">
            <h4>Teachers<i class="pull-right fa fa-graduation-cap"></i></h4>
            <span class="d-block">
            <?php
                
                $query = "SELECT id FROM rrsv_teacher ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
            </span>  
            <a href="" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>              
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-yellow">
            <h4>Exam<i class="pull-right fa fa-key"></i></h4>
            <span class="d-block">
            <?php
                
                $query = "SELECT id FROM quiz ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
            </span> 
            <a href="quiz.php?q=10" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>               
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-red">
            <h4>Results<i class="pull-right fa fa-building-o"></i></h4>
            <span class="d-block"> <span class="live-clock">
            <?php
                
                $query = "SELECT id FROM rank ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
            </span>
            <a href="" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>
                       
        </div>
    </div>     
    
    </div>
		</div>
	  </div>
        <!-- Note End -->
        
        
        
			
                    <?php
                   
                         if (@$_GET['q'] == 2) {
                             
                             ?>
                             
                             
                                
        <!-- Send sms box start -->
	<div class="col-md-12">
          <div class="box box-primary">
           <!-- /.header-->
            <div class="box-header with-border">
              <h3 class="box-title">Teacher's</h3>
              
               <div style="text-align: right";>
             <h4 class="btn btn-success btn-sm"> 
  </h4>
                </div>
                
		 </div>
             <!-- /.header-->
            
			
              <div class="box-body">
               
				 <!-- /box-body-->
             <?php
// Number of records per page
$limit = 10;

// Current page
$page = isset($_GET['show']) && is_numeric($_GET['show']) ? (int)$_GET['show'] : 1;
if ($page < 1) {
    $page = 1;
}

// Offset
$offset = ($page - 1) * $limit;

// Get total records
$totalQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM rank");
$totalRow = mysqli_fetch_assoc($totalQuery);

$totalRecords = $totalRow['total'];

// Main Query with JOIN
$sql = "
SELECT 
    r.score,
    r.time,
    r.username AS rank_username,
    r.class,
    r.date,
    s.scl_name,
    s.scl_phone_no,
    s.scl_class,
    s.scl_roll_no,
    s.scl_session,
    s.scl_reg_no,
    s.id AS user_id,
    h.eid
FROM rank r
LEFT JOIN rrsv_student_registration s 
    ON s.id = (
        SELECT MAX(id) 
        FROM rrsv_student_registration 
        WHERE scl_reg_no = r.username
    )
LEFT JOIN history h 
    ON r.username = h.username
ORDER BY r.score DESC, r.time ASC
LIMIT $offset, $limit
";

$q = mysqli_query($con, $sql) or die(mysqli_error($con));

// Start Table
echo '<table id="example1" class="table table-bordered table-striped">
<thead>
<tr>
    <th>S.NO</th>
    <th>Name</th>
    <th>Branch</th>
    <th>Class</th>
    <th>Username</th>
    <th>Roll No</th>
    <th>Score</th>
    <th>Results</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>';

$c = $offset;

while ($row = mysqli_fetch_assoc($q)) {
    $c++;

    $name = $row['scl_name'] ?? '';
    $branch = $row['scl_session'] ?? '';
    $class = $row['scl_class'] ?? '';
    $username = $row['scl_reg_no'] ?? '';
    $rollno = $row['scl_roll_no'] ?? '';
    $score = $row['score'] ?? '';
    $eid = $row['eid'] ?? '';
    $user_id = $row['user_id'] ?? '';

    echo '<tr>
        <td>' . $c . '</td>
        <td>' . $name . '</td>
        <td>' . $branch . '</td>
        <td>' . $class . '</td>
        <td>' . $username . '</td>
        <td>' . $rollno . '</td>
        <td>' . $score . '</td>

        <td>
            <a href="results.php?q=result&eid=' . $eid . '&username=' . $username . '" 
               class="btn" style="background:darkred;color:white">
               <b>View Result</b>
            </a>
        </td>

        <td>
            <a href="update.php?q=rmrah&username=' . $username . '" 
               class="btn" style="background:skyblue;color:white"
               onclick="return confirm(\'Are you sure?\')">
               <i class="fa fa-trash"></i>
            </a>

            <a href="results.php?q=result&eid=' . $eid . '&user_id=' . $user_id . '" 
               class="btn btn-warning btn-xs">
               <i class="fa fa-eye"></i>
            </a>

            <a href="print_invoice.php?username=' . $username . '&id=' . $eid . '" 
               class="btn btn-success btn-xs">
               <i class="fa fa-print"></i>
            </a>
        </td>
    </tr>';
}

echo '</tbody></table>';
?>

<!-- Pagination -->
<div style="text-align:center; margin-top:15px;">
<?php
if ($totalPages > 1) {

    // Previous button
    if ($page > 1) {
        echo '<a class="btn btn-primary" href="?show=' . ($page - 1) . '">Previous</a> ';
    }

    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $page) {
            echo '<span class="btn btn-success">' . $i . '</span> ';
        } else {
            echo '<a class="btn btn-default" href="?show=' . $i . '">' . $i . '</a> ';
        }
    }

    // Next button
    if ($page < $totalPages) {
        echo '<a class="btn btn-primary" href="?show=' . ($page + 1) . '">Next</a>';
    }
}
?>
</div>
       <?php
       }
       ?>       
      <?php
if (@$_GET['q'] == 'assign_quiz') {
?>
<div class="col-md-12">
<div class="box box-primary">

<div class="box-header with-border">
    <h3 class="box-title">Assign Quiz to Students</h3>
</div>

<div class="box-body">

<div class="row">

    <!-- Select Quiz -->
    <div class="col-md-4">
        <div class="form-group">
            <label>Select Quiz</label>
            <select id="quiz_id" class="form-control">
                <option value="">-- Select Quiz --</option>
                <?php
                $q = mysqli_query($con,"SELECT id,title FROM quiz");
                while($row = mysqli_fetch_assoc($q)){
                    echo "<option value='{$row['id']}'>{$row['title']}</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Select Class -->
    <div class="col-md-4">
        <div class="form-group">
            <label>Select Class</label>
            <select id="class" class="form-control">
                <option value="">-- Select Class --</option>
                <?php
                $q = mysqli_query($con,"SELECT DISTINCT scl_class FROM rrsv_student_registration");
                while($row = mysqli_fetch_assoc($q)){
                    echo "<option value='{$row['scl_class']}'>{$row['scl_class']}</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Select Session -->
    <div class="col-md-4">
        <div class="form-group">
            <label>Select Session</label>
            <select id="session" class="form-control">
                <option value="">-- Select Session --</option>
                <?php
                $q = mysqli_query($con,"SELECT DISTINCT scl_session FROM rrsv_student_registration");
                while($row = mysqli_fetch_assoc($q)){
                    echo "<option value='{$row['scl_session']}'>{$row['scl_session']}</option>";
                }
                ?>
            </select>
        </div>
    </div>

</div>
<!-- Student List -->
<div id="student_list"></div>

<br>

<button id="assignBtn" class="btn btn-success">
    <i class="fa fa-check"></i> Assign Quiz
</button>

</div>
</div>
</div>

    <?php
}
?>
              
              <div class="box-footer">
                
				 <!-- /.footer-->
				
                </div>
              <!-- /.box-footer -->
             
          </div>
          
          
   
        <!-- Send sms box end -->
		
		
            
 	<!--<div class="box-body"  style="background-color:#E6E6FA">-->
			 
	
	 <!--</div>-->
          
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
$(document).ready(function(){
function loadStudents(){
    var class_name = $('#class').val();
    var session = $('#session').val();
    var quiz_id = $('#quiz_id').val();

    if(class_name != '' && session != '' && quiz_id != ''){
        $.ajax({
            url: 'get_students.php',
            type: 'POST',
            data: {
                class: class_name,
                session: session,
                quiz_id: quiz_id
            },
            success: function(data){
                $('#student_list').html(data);
            }
        });
    }
}

$('#class, #session, #quiz_id').change(function(){
    loadStudents();
});

  $('#assignBtn').click(function(){

    var quiz_id = $('#quiz_id').val();
    var class_name = $('#class').val();
    var session = $('#session').val();
    var unit = $('#unit').val(); // if dropdown

    var students = [];
    var table = $('#studentTable').DataTable();

    table.$('.student_checkbox:checked').each(function(){
        students.push($(this).val());
    });

    if(quiz_id == '' || students.length == 0){
        alert('Select quiz and students');
        return;
    }

    $.ajax({
        url: 'assign_quiz.php',
        type: 'POST',
        data: {
            quiz_id: quiz_id,
            students: students,
            class: class_name,
            session: session,
            unit: unit
        },
        success: function(res){
            alert(res);
        }
    });

});

});
$('#studentTable').DataTable({
    "pageLength": 10,
    "lengthMenu": [10, 25, 50, 100],
    "paging": true,
    "searching": true,
    "ordering": true,
    "destroy": true // IMPORTANT when reloading via AJAX
});
</script>

</body>
</html>