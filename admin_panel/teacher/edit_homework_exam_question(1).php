<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';

// 🔒 Access Control
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
  header("Location: ../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];
$id = $_GET['id'] ?? 0;
$exam_id= $_GET['exam_id']??0;
// 🧠 Fetch question details
// $query = mysqli_query($con, "SELECT * FROM rrsv_homework_exam_questions WHERE id='$id'");
$query = $con->query("SELECT q.*, a.answer AS teacher_answer 
                           FROM rrsv_homework_exam_questions q
                           LEFT JOIN rrsv_teacher_answers a ON q.id = a.question_id
                           WHERE q.id='$id'");
if (!$query || mysqli_num_rows($query) == 0) {
  die("<h3 style='color:red;text-align:center;'>Invalid Question ID!</h3>");
}
$question = mysqli_fetch_assoc($query);
//print_r($question['option_a']);
// 📝 Update question
if (isset($_POST['update'])) {
  $question_type = mysqli_real_escape_string($con, $_POST['question_type']);
  $question_text = mysqli_real_escape_string($con, $_POST['question']);
  $option_a = mysqli_real_escape_string($con, $_POST['option_a'] ?? '');
  $option_b = mysqli_real_escape_string($con, $_POST['option_b'] ?? '');
  $option_c = mysqli_real_escape_string($con, $_POST['option_c'] ?? '');
  $option_d = mysqli_real_escape_string($con, $_POST['option_d'] ?? '');
  $correct_answer = mysqli_real_escape_string($con, $_POST['correct_answer'] ?? '');
  $teacher_answer = mysqli_real_escape_string($con, $_POST['description'] ?? '');

  // 🧩 For descriptive, clear MCQ fields
  if ($question_type === 'descriptive') {
    $option_a = $option_b = $option_c = $option_d = $correct_answer = '';
  }

  // 🛠️ Update main question
  $update = mysqli_query($con, "
      UPDATE rrsv_homework_exam_questions 
      SET 
        question='$question_text', 
        option_a='$option_a', 
        option_b='$option_b', 
        option_c='$option_c', 
        option_d='$option_d', 
        correct_answer='$correct_answer'
      WHERE id='$id'
  ");

  // 🧠 Store / Update teacher answer for descriptive
  if ($question_type === 'descriptive') {
      $exists = mysqli_query($con, "SELECT id FROM rrsv_teacher_answers WHERE question_id='$id'");
      if (mysqli_num_rows($exists) > 0) {
         $update = mysqli_query($con, "UPDATE rrsv_teacher_answers SET answer='$teacher_answer' WHERE question_id='$id'");
      } else {
         $update = mysqli_query($con, "INSERT INTO rrsv_teacher_answers (question_id, answer) VALUES ('$id', '$teacher_answer')");
      }
  }

  if ($update) {
    echo "<script>
            alert('✅ Question updated successfully!');
           window.location='homework_exam_questions.php?exam_id=$exam_id';
          </script>";
  } else {
    echo "<script>alert('❌ Failed to update question.');</script>";
  }
}

include('../header.php');
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Homework Exam Question</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <a href="#" class="logo">
      <span class="logo-mini"><b>MSB</b></span>
      <span class="logo-lg">
        <img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;">
      </span>
    </a>
    <?php include '../top_navbar.php'; ?>
  </header>

  <?php include '../left_navbar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h2>Edit Homework Exam Question</h2>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Question</h3>
            </div>

            <div class="box-body">
              <form method="POST">
                <div class="form-group">
                  <label>Question Type</label>
                  <select id="qtype" name="question_type" class="form-control" onchange="toggleOptions()" required>
                    <option value="mcq" <?= ($question['option_a']) ? 'selected' : ''; ?>>Multiple Choice</option>
                    <option value="descriptive" <?= ($question['option_a']== '')? 'selected' : ''; ?>>Descriptive</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Question</label>
                  <textarea name="question" class="form-control" rows="3" required><?= htmlspecialchars($question['question']); ?></textarea>
                </div>

                <!-- 🟢 MCQ Options -->
                <div id="optionsDiv" style="display: <?= ($question['option_a']) ? 'block' : 'none'; ?>;">
                  <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="option_a" class="form-control" value="<?= htmlspecialchars($question['option_a']); ?>">
                  </div>
                  <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="option_b" class="form-control" value="<?= htmlspecialchars($question['option_b']); ?>">
                  </div>
                  <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="option_c" class="form-control" value="<?= htmlspecialchars($question['option_c']); ?>">
                  </div>
                  <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="option_d" class="form-control" value="<?= htmlspecialchars($question['option_d']); ?>">
                  </div>
                  <div class="form-group">
                    <label>Correct Answer</label>
                    <select name="correct_answer" class="form-control">
                      <option value="">-- Select Correct Answer --</option>
                      <option value="A" <?= ($question['correct_answer'] == 'A') ? 'selected' : ''; ?>>A</option>
                      <option value="B" <?= ($question['correct_answer'] == 'B') ? 'selected' : ''; ?>>B</option>
                      <option value="C" <?= ($question['correct_answer'] == 'C') ? 'selected' : ''; ?>>C</option>
                      <option value="D" <?= ($question['correct_answer'] == 'D') ? 'selected' : ''; ?>>D</option>
                    </select>
                  </div>
                </div>

                <!-- 🟣 Descriptive -->
                <div id="tDiv" style="display: <?= ($question['option_a']=='')? 'block' : 'none'; ?>;">
                  <div class="form-group">
                    <label>Descriptive Answer</label>
                    <textarea name="description" class="form-control" placeholder="Enter descriptive answer..."><?= htmlspecialchars($question['teacher_answer'] ?? ''); ?></textarea>
                  </div>
                </div>

                <button type="submit" name="update" class="btn btn-success">💾 Update Question</button>
                <a href="homework_exam_questions.php" class="btn btn-secondary">⬅ Back</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php include '../footer.php'; ?>
  </div>
</div>

<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script>
function toggleOptions() {
  const type = document.getElementById('qtype').value;
  document.getElementById('optionsDiv').style.display = (type === 'mcq') ? 'block' : 'none';
  document.getElementById('tDiv').style.display = (type === 'descriptive') ? 'block' : 'none';
}
</script>
</body>
</html>
