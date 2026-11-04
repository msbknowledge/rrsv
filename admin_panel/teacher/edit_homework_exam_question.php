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
$exam_id = $_GET['exam_id'] ?? 0;

// 🧠 Fetch question details
$query = $con->query("
  SELECT q.*, a.answer AS teacher_answer 
  FROM rrsv_homework_exam_questions q
  LEFT JOIN rrsv_teacher_answers a ON q.id = a.question_id
  WHERE q.id='$id'
");

if (!$query || $query->num_rows == 0) {
  die("<h3 style='color:red;text-align:center;'>Invalid Question ID!</h3>");
}

$question = $query->fetch_assoc();

// 📝 Update question
if (isset($_POST['update'])) {
  $question_type = mysqli_real_escape_string($con, $_POST['question_type']);
  $question_text = mysqli_real_escape_string($con, $_POST['question']);
  $correct_answer = mysqli_real_escape_string($con, $_POST['correct_answer'] ?? '');
  $teacher_answer = mysqli_real_escape_string($con, $_POST['description'] ?? '');
  $option_a = $option_b = $option_c = $option_d = '';

  // Handle options (text or images)
  if ($question_type === 'mcq' || $question_type === 'image') {
      if ($question_type === 'image' && isset($_FILES['option_a'])) {
          $upload_dir = "../../uploads/questions/";
          if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

          function uploadOptionImage($field, $upload_dir, $oldFile = null) {
              if (!empty($_FILES[$field]['name'])) {
                  $filename = time() . "_" . basename($_FILES[$field]['name']);
                  $target = $upload_dir . $filename;
                  if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
                      return $filename;
                  }
              }
              return $oldFile;
          }

          $option_a = uploadOptionImage('option_a', $upload_dir, $question['option_a']);
          $option_b = uploadOptionImage('option_b', $upload_dir, $question['option_b']);
          $option_c = uploadOptionImage('option_c', $upload_dir, $question['option_c']);
          $option_d = uploadOptionImage('option_d', $upload_dir, $question['option_d']);
      } else {
          $option_a = mysqli_real_escape_string($con, $_POST['option_a'] ?? '');
          $option_b = mysqli_real_escape_string($con, $_POST['option_b'] ?? '');
          $option_c = mysqli_real_escape_string($con, $_POST['option_c'] ?? '');
          $option_d = mysqli_real_escape_string($con, $_POST['option_d'] ?? '');
      }
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

  // 🧠 Manage descriptive teacher answer
  if ($question_type === 'descriptive') {
      $exists = mysqli_query($con, "SELECT id FROM rrsv_teacher_answers WHERE question_id='$id'");
      if (mysqli_num_rows($exists) > 0) {
          mysqli_query($con, "UPDATE rrsv_teacher_answers SET answer='$teacher_answer' WHERE question_id='$id'");
      } else {
          mysqli_query($con, "INSERT INTO rrsv_teacher_answers (question_id, answer) VALUES ('$id', '$teacher_answer')");
      }
  }

  if ($update) {
    echo "<script>
            alert('✅ Question updated successfully!');
            window.location='homework_exam_questions.php?exam_id=$exam_id';
          </script>";
    exit;
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
            <!-- Logo -->
            <a href="#" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>MSB</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg"
                        style="height:100%;width:44px;border-radius:50%;"></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <?php include '../top_navbar.php' ?>
        </header>
  <?php include '../left_navbar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h2>Edit Homework Exam Question</h2>
    </section>

    <section class="content">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Edit Question</h3>
        </div>

        <div class="box-body">
          <form method="POST" enctype="multipart/form-data">
            <!-- Type -->
            <div class="form-group">
              <label>Question Type</label>
              <select id="qtype" name="question_type" class="form-control" onchange="toggleOptions()" required>
                <?php
                $types = mysqli_query($con, "SELECT * FROM rrsv_question_type WHERE status='active' ORDER BY id ASC 
                    LIMIT 4");
                while ($row = mysqli_fetch_assoc($types)) {
                    $selected = (strtolower($question['question_type']) == strtolower($row['type_code'])) ? 'selected' : '';
                    echo "<option value='{$row['type_code']}' $selected>{$row['type_name']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label>Question</label>
              <textarea name="question" id="editor1" class="form-control ckeditor-content" rows="3" required><?= ($question['question']); ?></textarea>
            </div>

            <!-- MCQ -->
            <div id="optionsDiv" style="display:none;">
              <label><strong>Options</strong></label>
              <?php foreach (['A','B','C','D'] as $opt): ?>
              <div class="form-group">
                <label>Option <?= $opt ?></label>
                <input type="text" name="option_<?= strtolower($opt) ?>" class="form-control" 
                       value="<?= htmlspecialchars($question['option_'.strtolower($opt)]); ?>">
              </div>
              <?php endforeach; ?>
              <div class="form-group">
                <label>Correct Answer</label>
                <select name="correct_answer" id="correct_answer_text" class="form-control">
                  <option value="">-- Select Correct Answer --</option>
                  <?php foreach (['A','B','C','D'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($question['correct_answer'] == $opt) ? 'selected' : ''; ?>><?= $opt ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Image Options -->
            <div id="imageDiv" style="display:none;">
              <label><strong>Option Images</strong></label>
              <?php foreach (['A','B','C','D'] as $opt): 
                $col = 'option_'.strtolower($opt);
                $file = $question[$col];
              ?>
              <div class="form-group">
                <label>Option <?= $opt ?> Image</label>
                <?php if ($file): ?>
                  <p><img src="../../uploads/questions/<?= $file ?>" width="100"></p>
                <?php endif; ?>
                <input type="file" name="option_<?= strtolower($opt) ?>" class="form-control">
              </div>
              <?php endforeach; ?>
              <div class="form-group">
                <label>Correct Answer</label>
                <select name="correct_answer" id="correct_answer_image" class="form-control">
                  <?php foreach (['A','B','C','D'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($question['correct_answer'] == $opt) ? 'selected' : ''; ?>><?= $opt ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Descriptive -->
            <div id="tDiv" style="display:none;">
              <div class="form-group">
                <label>Descriptive Answer</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($question['teacher_answer']); ?></textarea>
              </div>
            </div>

            <button type="submit" name="update" class="btn btn-success">💾 Update</button>
            <a href="homework_exam_questions.php?exam_id=<?= $exam_id ?>" class="btn btn-secondary">⬅ Back</a>
          </form>
        </div>
      </div>
    </section>

    <?php include '../footer.php'; ?>
  </div>
</div>

<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script>
// function toggleOptions() {
//   const type = document.getElementById('qtype').value.toLowerCase();
//   document.getElementById('optionsDiv').style.display = (type === 'mcq') ? 'block' : 'none';
//   document.getElementById('tDiv').style.display = (type === 'descriptive') ? 'block' : 'none';
//   document.getElementById('imageDiv').style.display = (type === 'image') ? 'block' : 'none';
// }
function toggleOptions() {
  const type = document.getElementById('qtype').value.toLowerCase();

  // Show/Hide blocks
  document.getElementById('optionsDiv').style.display = (type === 'mcq') ? 'block' : 'none';
  document.getElementById('imageDiv').style.display = (type === 'image') ? 'block' : 'none';
  document.getElementById('tDiv').style.display = (type === 'descriptive') ? 'block' : 'none';

  // Disable all correct answer fields first
  document.getElementById('correct_answer_text').disabled = true;
  document.getElementById('correct_answer_image').disabled = true;

  // Enable only correct one
  if (type === 'mcq') {
      document.getElementById('correct_answer_text').disabled = false;
  }
  if (type === 'image') {
      document.getElementById('correct_answer_image').disabled = false;
  }
}

// Run once on load
document.addEventListener('DOMContentLoaded', toggleOptions);
</script>
<script>
CKEDITOR.replace('editor1', {
  customConfig: '<?php echo BASE_URL; ?>libray/ckeditor/ckeditor_config.js'
});
</script>

</body>
</html>
