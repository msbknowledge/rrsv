<!DOCTYPE html>
<html>
<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'student') {
  header("Location: ../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];
include('../header.php');

?>

<body class="hold-transition skin-blue sidebar-mini">


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
      <?php include '../top_navbar.php' ?>
    </header><!-- Header End -->
    <!-- Left side column. contains the logo and sidebar -->

    <!-- Sidebar Start -->
    <?php include '../left_navbar.php' ?>
    <!-- sidebar End -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <?php


      $teacher_id = $_SESSION['user_data']['id'];

      // Fetch all exams created by this teacher
      $exams = $con->query("SELECT * FROM rrsv_homework_exam WHERE teacher_id='$teacher_id' ORDER BY id DESC");
      ?>

      <section class="content-header">
        <h1>Student Results</h1>
      </section>



        <?php if (isset($_GET['exam_id'])):
          $exam_id = $_GET['exam_id'];
          
          $exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();
          
          $results = $con->query("SELECT s.*, u.scl_name AS student_name FROM rrsv_student_homework_summary s 
                             JOIN rrsv_student_registration u ON u.id = s.student_id 
                             WHERE s.exam_id='$exam_id' and u.id='$teacher_id' ORDER BY s.score DESC");
                           //  print_r($results->fetch_assoc() );die();
          ?>
          <div class="row">
            <div class="col-md-12">
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title">Results for: <?= htmlspecialchars($exam['title']); ?></h3>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Total Questions</th>
                        <!--<th>Correct</th>-->
                        <!--<th>Score (%)</th>-->
                        <th>Remarks</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $i = 1;
                      while ($r = $results->fetch_assoc()) { ?>
                        <tr>
                          <td><?= $i++; ?></td>
                          <td><?= htmlspecialchars($r['student_name']); ?></td>
                          <td><?= $r['total_questions']; ?></td>
                          <!--<td><?= $r['correct_answers']; ?></td>-->
                          <!--<td><?= $r['score']; ?>%</td>-->
                           <td><?= $r['remarks']; ?></td>
                          <td><?= $r['submitted_at']; ?></td>
                          <td><a
                              href="view_student_answers.php?exam_id=<?= $exam_id; ?>&student_id=<?= $r['student_id']; ?>"
                              class="btn btn-info btn-sm">View Answers</a></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </section>

      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Footer Start -->
    <?php include '../footer.php' ?>
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




</body>

</html>