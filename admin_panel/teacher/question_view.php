<!DOCTYPE html>
<html>
<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
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
        <span class="logo-lg"><img src="assets/css/download.png" style="height:100%;width:44px"></span>
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

      $exam_id = $_GET['exam_id']??9;
      $student_id = $_SESSION['user_data']['id'];

      // Fetch exam info
      $exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();

      // Fetch questions
      $questions = $con->query("SELECT * FROM rrsv_homework_exam_questions WHERE exam_id='$exam_id'");
      ?>

      <section class="content-header">
        <h1><?= htmlspecialchars($exam['title']); ?> <small>(<?= ucfirst($exam['type']); ?>)</small></h1>
      </section>

      <section class="content">
        <form action="submit_exam.php" method="POST">
          <input type="hidden" name="exam_id" value="<?= $exam_id; ?>">

          <?php
          $i = 1;
          while ($q = $questions->fetch_assoc()) { ?>
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Q<?= $i++; ?>. <?= htmlspecialchars($q['question']); ?></h3>
              </div>
              <div class="box-body">
                <?php if ($q['option_a']): ?>
                  <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="A">
                    <?= htmlspecialchars($q['option_a']); ?></label><br>
                  <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="B">
                    <?= htmlspecialchars($q['option_b']); ?></label><br>
                  <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="C">
                    <?= htmlspecialchars($q['option_c']); ?></label><br>
                  <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="D">
                    <?= htmlspecialchars($q['option_d']); ?></label><br>
                <?php else: ?>
                  <textarea name="answers[<?= $q['id']; ?>]" class="form-control"
                    placeholder="Write your answer here..."></textarea>
                <?php endif; ?>
              </div>
            </div>
          <?php } ?>

          <button type="submit" class="btn btn-success">Submit Answers</button>
        </form>
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