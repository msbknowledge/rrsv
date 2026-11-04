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


$exam_id = $_GET['exam_id'];
$student_id = $_GET['student_id'];

// Fetch exam details
$exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();
$student = $con->query("SELECT scl_name FROM rrsv_student_registration WHERE id='$student_id'")->fetch_assoc();

// Fetch all questions
$questions = $con->query("SELECT * FROM rrsv_homework_exam_questions WHERE exam_id='$exam_id'");
?>

<section class="content-header">
  
  <small>Student: <?= htmlspecialchars($student['scl_name']); ?></small>
  <small>Teacher Marks: <?= htmlspecialchars($student['scl_name']); ?></small>
  
  <h1>Answers - <?= htmlspecialchars($exam['title']); ?></h1>
</section>

<section class="content">
  <?php
  
// -------------------------------------------
// STEP 1: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
$fullList = $questions->fetch_all(MYSQLI_ASSOC);
$questions->data_seek(0); 
$headOfType = [];
foreach ($fullList as $row) {
    $type = strtolower($row['question_type']);
    $head = trim($row['question_head']);
    if ($head !== "" && $head !== "NULL" && !isset($headOfType[$type])) {
        $headOfType[$type] = $head;   // store only first head found
    }
}

// -------------------------------------------
// STEP 1: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
  $i = 1;

// -------------------------------------------
// STEP 2: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
$headLetterCount = [];
$printedHead = [];
$questionNumber = [];

// -------------------------------------------
// STEP 2: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
  while ($q = $questions->fetch_assoc()) {
    $qid = $q['id'];


// -------------------------------------------
// STEP 3: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
    $type = strtolower($q['question_type']);
    $head = $headOfType[$type] ?? "";  
    $showHead = false;
    if (!isset($questionNumber[$type])) {
        $questionNumber[$type] = 1;
    }

    if ($head !== "" && empty($printedHead[$type])) {

        if (!isset($headLetterCount[$exam_id])) {
            $headLetterCount[$exam_id] = 0;
        }

        $letter = chr(65 + $headLetterCount[$exam_id]++);
        $printedHead[$type] = $letter . ". " . $head;

        $showHead = true;
    }

    $currentQuestionNumber = $questionNumber[$type]++;
    
// -------------------------------------------
// STEP 3: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------

  if ($q['question_type'] == 'video') {
        continue;
    }


    $studentAnswer = $con->query("SELECT answer FROM rrsv_student_answers WHERE question_id='$qid' AND student_id='$student_id'")->fetch_assoc()['answer'] ?? '-';
    // Fetch correct answer from rrsv_homework_exam_questions
$q1 = $con->query("SELECT correct_answer FROM rrsv_homework_exam_questions WHERE id='$qid'");
if ($q1 && $q1->num_rows > 0) {
    $row = $q1->fetch_assoc();
    $correctAnswer = $row['correct_answer'];
}

// If not found or empty, fetch from rrsv_teacher_answers
if (empty($correctAnswer)) {
    $q2 = $con->query("SELECT answer FROM rrsv_teacher_answers WHERE question_id='$qid'");
    if ($q2 && $q2->num_rows > 0) {
        $row2 = $q2->fetch_assoc();
        $correctAnswer = $row2['answer'];
    }
}

  ?>
  <div class="box <?= (strtoupper(trim($studentAnswer)) == strtoupper(trim($correctAnswer))) ? 'box-success' : 'box-danger'; ?>">
    <div class="box-header with-border">
            <?php if ($showHead): ?>
               <div style="font-size:18px; color:#0073b7;">
                    <b><?= htmlspecialchars($printedHead[$type]); ?></b>
                </div>
            <?php endif; ?>

      <h3 class="box-title ckeditor-content">Q<?= $currentQuestionNumber; ?>. <?= $q['question']; ?></h3>
    </div>
    <div class="box-body">
      <p><strong>Student Answer:</strong> <?= htmlspecialchars($studentAnswer); ?></p>
      <!--<p><strong>Correct Answer:</strong> <?= htmlspecialchars($correctAnswer); ?></p>-->

      <?php if ($type === 'mcq'): ?>

            <!-- Text MCQ Options -->
            <ul>
                <li><strong>A.</strong> <?= htmlspecialchars($q['option_a']); ?></li>
                <li><strong>B.</strong> <?= htmlspecialchars($q['option_b']); ?></li>
                <li><strong>C.</strong> <?= htmlspecialchars($q['option_c']); ?></li>
                <li><strong>D.</strong> <?= htmlspecialchars($q['option_d']); ?></li>
            </ul>

        <?php elseif ($type === 'image'): ?>

            <!-- Image MCQ Options -->
            <ul>
                <li><strong>A.</strong><br>
                    <img src="../../uploads/questions/<?= $q['option_a']; ?>" width="150">
                </li>
                <li><strong>B.</strong><br>
                    <img src="../../uploads/questions/<?= $q['option_b']; ?>" width="150">
                </li>
                <li><strong>C.</strong><br>
                    <img src="../../uploads/questions/<?= $q['option_c']; ?>" width="150">
                </li>
                <li><strong>D.</strong><br>
                    <img src="../../uploads/questions/<?= $q['option_d']; ?>" width="150">
                </li>
            </ul>

        <?php elseif ($type === 'descriptive'): ?>

            <!-- Descriptive -->
            <p><i>Descriptive question</i></p>

        <?php elseif ($type === 'video'): ?>

            <!-- Video Question -->
            <video width="350" controls>
                <source src="../../uploads/questions/<?= $q['video_url']; ?>" type="video/mp4">
            </video>

        <?php endif; ?>

    </div>
  </div>
  <?php } ?>
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