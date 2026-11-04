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

// Fetch exam details
$exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();

// Fetch all questions for this exam
$questions = $con->query("SELECT q.*, a.answer AS teacher_answer 
                           FROM rrsv_homework_exam_questions q
                           LEFT JOIN rrsv_teacher_answers a ON q.id = a.question_id
                           WHERE q.exam_id='$exam_id'");
include('../header.php');
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Homework Exam Question</title>
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
      <h2>Add Homework Exam Question</h2>
      <h1>
                    <?= htmlspecialchars($exam['book_name']); ?><?= htmlspecialchars($exam['chapter_name']); ?>
                    <small>(<?= ucfirst($exam['type']); ?>)</small>
                </h1>
    </section>

    <section class="content">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Add Question</h3>
        </div>

        <div class="box-body">
          <form action="save_homework_exam_question.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <h4 class="modal-title">Add Question</h4>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <input type="hidden" name="exam_id" value="<?= $exam_id; ?>">

          <!-- Question -->
          <div class="form-group">
            <label>Question</label>
            <textarea name="question" id="editor1" rows="4" class="form-control" required></textarea>
          </div>
          <!-- Question -->
          <div class="form-group">
            <label>Question Head</label>
            <select id="question_head" name="question_head" class="form-control">
                <?php
                $heads = mysqli_query($con, "
                    SELECT * FROM rrsv_question_type 
                    WHERE status='active' 
                    ORDER BY id ASC 
                    LIMIT 999 OFFSET 4
                ");
        
                while ($row = mysqli_fetch_assoc($heads)) {
                    echo "<option value='{$row['type_name']}'>{$row['type_name']}</option>";
                }
                ?>
            </select>
        </div>


          <!-- Question Type -->
          <div class="form-group">
            <label>Type of Question</label>
            <select id="qtype" name="question_type" class="form-control" onchange="toggleOptions()" required>
                <?php
                $types = mysqli_query($con, "
                    SELECT * FROM rrsv_question_type 
                    WHERE status='active' 
                    ORDER BY id ASC 
                    LIMIT 4
                ");
        
                $default_type = 'mcq';
        
                while ($row = mysqli_fetch_assoc($types)) {
                    $selected = ($row['type_code'] == $default_type) ? 'selected' : '';
                    echo "<option value='{$row['type_code']}' $selected>{$row['type_name']}</option>";
                }
                ?>
            </select>
        </div>


          <!-- MCQ Options -->
          <div id="optionsDiv">
            <div class="form-group"><label>Option A</label><input type="text" name="option_a" class="form-control"></div>
            <div class="form-group"><label>Option B</label><input type="text" name="option_b" class="form-control"></div>
            <div class="form-group"><label>Option C</label><input type="text" name="option_c" class="form-control"></div>
            <div class="form-group"><label>Option D</label><input type="text" name="option_d" class="form-control"></div>

            <div class="form-group">
              <label>Correct Answer</label>
              <select id="correct_answer_text" name="correct_answer" class="form-control">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
              </select>
            </div>
          </div>

          <!-- Descriptive Type -->
          <div class="form-group" id="tDiv">
            <label>Descriptive Answer</label>
            <textarea name="teacher_answer" rows="5" class="form-control"></textarea>
          </div>

          <!-- Image Type -->
          <div class="form-group" id="imageDiv">
            <div class="form-group">
              <label>Option Image A</label>
              <input type="file" name="option_a" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_a')">
              <img id="preview_a" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Option Image B</label>
              <input type="file" name="option_b" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_b')">
              <img id="preview_b" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Option Image C</label>
              <input type="file" name="option_c" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_c')">
              <img id="preview_c" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Option Image D</label>
              <input type="file" name="option_d" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_d')">
              <img id="preview_d" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Correct Answer</label>
              <select id="correct_answer_image" name="correct_answer" class="form-control">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
              </select>
            </div>
          </div>

          <!-- Video Type -->
          <div class="form-group" id="vDiv">
            <label>Upload Video</label>
            <input type="url" 
         name="youtube_link" 
         id="youtubeLink" 
         class="form-control" 
         placeholder="Enter YouTube video link (e.g. https://www.youtube.com/watch?v=XXXX)"
         oninput="previewYouTubeLink(this.value)">
  
          <div id="youtubePreview" style="display:none; margin-top:10px;">
            <iframe width="300" height="180" 
                    frameborder="0" 
                    allowfullscreen></iframe>
          </div>
            <!--<input type="file" name="video_file" class="form-control" accept="video/*" onchange="previewVideo(this)">-->
            <!--<video id="videoPreview" controls style="display:none; margin-top:10px; max-width:200px;"></video>-->
            
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Question</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
        </div>
      </div>
    </section>

    <?php include '../footer.php'; ?>
  </div>
</div>

<script src="../assets/js/jquery-2.2.3.min.js"></script>
    <script>
         function previewImage(input, previewId) {
          const preview = document.getElementById(previewId);
          const file = input.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
              preview.src = e.target.result;
              preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
          } else {
            preview.src = '';
            preview.style.display = 'none';
          }
        }
        function previewVideo(input) {
          const videoPreview = document.getElementById('videoPreview');
          const file = input.files[0];
          if (file) {
            const url = URL.createObjectURL(file);
            videoPreview.src = url;
            videoPreview.style.display = 'block';
          } else {
            videoPreview.src = '';
            videoPreview.style.display = 'none';
          }
        }
        
        function toggleOptions() {
                var type = document.getElementById('qtype').value;
            
                // Hide all sections
                document.getElementById('optionsDiv').style.display = 'none';
                document.getElementById('tDiv').style.display = 'none';
                document.getElementById('imageDiv').style.display = 'none';
                document.getElementById('vDiv').style.display = 'none';
            
                // Disable all correct answer selects
                document.getElementById('correct_answer_text').disabled = true;
                document.getElementById('correct_answer_image').disabled = true;
            
                // Show & enable based on type
                if (type === 'mcq') {
                    document.getElementById('optionsDiv').style.display = 'block';
                    document.getElementById('correct_answer_text').disabled = false;
                }
                else if (type === 'image') {
                    document.getElementById('imageDiv').style.display = 'block';
                    document.getElementById('correct_answer_image').disabled = false;
                }
                else if (type === 'video') {
                    document.getElementById('vDiv').style.display = 'block';
                }
                else {
                    document.getElementById('tDiv').style.display = 'block';
                }
            }

        // Run once on modal load
        document.addEventListener('DOMContentLoaded', toggleOptions);

        // Also re-run when modal opens (Bootstrap event)
        $('#addQuestionModal').on('shown.bs.modal', function () {
            toggleOptions();
        });
        function openAddModal() {

            $('#modalTitle').text('Add Type');
        }
    </script>

<script>
CKEDITOR.replace('editor1', {
  customConfig: '<?php echo BASE_URL; ?>libray/ckeditor/ckeditor_config.js'
});
</script>

</body>
</html>
