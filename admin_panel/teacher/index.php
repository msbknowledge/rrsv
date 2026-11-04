
<!DOCTYPE html>
<html>
    
     <?php
    session_start();
    include '../dbcon.php';
    // 🔒 Access Control: Only logged-in teachers can access
    if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
        header("Location: ../index.php?error=Unauthorized Access");
        exit;
    }
    
    $teacher = $_SESSION['user_data'];
?>
 <?php include('../header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
    <link rel="chat/stylesheet" href="<?=BASE_URL?>chat/assets/css/style.css" />
 <script src="<?=BASE_URL?>chat/assets/js/main.js"></script>
<script src="<?=BASE_URL?>chat/assets/js/chat.js"></script>
    
    
    
    
    
    
 

<div class="wrapper">
  <!-- Header Start -->
  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>MSB</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
	      </a>
    <!-- Header Navbar: style can be found in header.less -->
   <?php include '../top_navbar.php'?>
  </header><!-- Header End -->
  <!-- Left side column. contains the logo and sidebar -->
  
  

  
  
  
  
<!-- Sidebar Start -->
   <?php include '../left_navbar.php'?>
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
   <?php include('dashboard.php'); ?>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Footer Start -->
  <?php include '../footer.php'?>
  <div class="control-sidebar-bg"></div>
</div>



 

  
  
  

<!-- ./wrapper -->
<!-- jQuery 2.2.3 -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../assets/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../assets/js/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../assets/js/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../assets/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../assets/js/demo.js"></script>
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
$(document).ready(function () {
  $('#class_id').on('change', function () {
    var class_name = $('#class_id option:selected').text(); // ✅ Get selected class name
   
    if (class_name !== "") {
      $.ajax({
        url: 'get_subjects.php',
        type: 'POST',
        data: { class_name: class_name },
        success: function (response) {
          $('#subject_id').html(response);
        }
      });
    } else {
      $('#subject_id').html('<option value="">Select Subject</option>');
    }
  });
});
</script>



</body>
</html>