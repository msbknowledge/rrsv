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

// 🧠 Handle form submission
if (isset($_POST['save_exam'])) {
  $book_name = mysqli_real_escape_string($con, $_POST['book_name']);
  $chapter_name = mysqli_real_escape_string($con, $_POST['chapter_name']);
  $description = mysqli_real_escape_string($con, $_POST['description']);
  $unit = intval($_POST['unit']);
  $type = mysqli_real_escape_string($con, $_POST['type']);
  $session = mysqli_real_escape_string($con, $_POST['session']);
  $class_id = intval($_POST['class_id']);
  $subject_id = intval($_POST['subject_id']);
  $teacher_id = intval($teacher['id']);

  $insert = "INSERT INTO rrsv_homework_exam (book_name, chapter_name, description, unit, type, session, class_id, subject_id, teacher_id)
             VALUES ('$book_name', '$chapter_name', '$description', '$unit', '$type', '$session', '$class_id', '$subject_id', '$teacher_id')";

  if (mysqli_query($con, $insert)) {
    echo "<script>alert('Homework / Exam added successfully!'); window.location='homework_exam.php';</script>";
    exit;
  } else {
    echo "<div class='alert alert-danger'>Error saving record: " . mysqli_error($con) . "</div>";
  }
}
?>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <!-- Header -->
    <header class="main-header">
      <a href="#" class="logo">
        <span class="logo-mini"><b>MSB</b></span>
       <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
      </a>
      <?php include '../top_navbar.php'; ?>
    </header>

    <!-- Sidebar -->
    <?php include '../left_navbar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Add Homework / Exam</h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Homework / Exam</a></li>
          <li class="active">Add</li>
        </ol>
      </section>

      <!-- Main Content -->
      <section class="content">
        <div class="row">
          <div class="col-md-10">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h4 class="box-title">Create Homework / Exam</h4>
              </div>

              <form action="" method="POST">
                <div class="box-body">
                  <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Book</label>
                        <input type="text" name="book_name" required class="form-control">
                      </div>
                        <!--<div class="form-group">-->
                        <!--  <label>Book</label>-->
                        <!--  <select name="book_name" id="book_name" class="form-control" required>-->
                        <!--    <option value="">Select Class & Session First</option>-->
                        <!--  </select>-->
                        <!--</div>-->

                      <div class="form-group">
                        <label>Chapter</label>
                        <input type="text" name="chapter_name" required class="form-control">
                      </div>

                      <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                      </div>

                      <div class="form-group">
                        <label>Unit</label>
                        <select name="unit" class="form-control" id="unit">
                          <?php
                          $selectedUnit = $_POST['unit'] ?? 1;
                          for ($u = 1; $u <= 3; $u++) {
                            $unitLabel = ($u == 1) ? '1st Unit' :
                              (($u == 2) ? '2nd Unit' : '3rd Unit');
                            $sel = ($selectedUnit == $u) ? 'selected' : '';
                            echo "<option value='$u' $sel>$unitLabel</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control">
                          <option value="homework">Homework</option>
                          <option value="exam">Exam</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>Session</label>
                        <select name="session" id="session" class="form-control">
                          <?php
                          $currentYear = date('Y');
                          echo "<option value='$currentYear' selected>$currentYear</option>";
                          for ($y = $currentYear - 1; $y >= ($currentYear - 5); $y--) {
                            echo "<option value='$y'>$y</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>Class</label>
                        <select name="class_id" class="form-control" id="class_id" required>
                          <option value="">-Select Class-</option>
                          <?php
                          $res = mysqli_query($con, "SELECT * FROM rrsv_class ORDER BY id");
                          while ($obj = mysqli_fetch_assoc($res)) {
                            echo "<option value='{$obj['id']}'>{$obj['class_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>Subject <span class="text-danger">*</span></label>
                        <select class="form-control" name="subject_id" id="subject_id" required>
                          <option value="">Select Subject</option>
                          <?php
                          $res2 = mysqli_query($con, "SELECT * FROM rrsv_subject ORDER BY id");
                          while ($obj2 = mysqli_fetch_assoc($res2)) {
                            echo "<option value='{$obj2['id']}'>{$obj2['sub_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="box-footer">
                  <button type="submit" name="save_exam" class="btn btn-success">Save</button>
                  <a href="homework_exam.php" class="btn btn-default">Cancel</a>
                </div>
              </form>

            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>
    <div class="control-sidebar-bg"></div>
  </div>

  <!-- JS -->
  <script src="../assets/js/jquery-2.2.3.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/js/jquery.dataTables.min.js"></script>
  <script src="../assets/js/dataTables.bootstrap.min.js"></script>
  <script src="../assets/js/jquery.slimscroll.min.js"></script>
  <script src="../assets/js/fastclick.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/js/demo.js"></script>
<script>
$(document).ready(function () {

    // When class or session changes → load books
    $("#class_id, #session").on("change", function () {
        let class_id = $("#class_id").val();
        let session = $("#session").val();

        if (class_id !== "" && session !== "") {
            $.ajax({
                url: "fetch_books.php",
                type: "POST",
                data: { class_id: class_id, session: session },
                success: function (response) {
                    $("#book_name").html(response);
                }
            });
        }
    });

});
</script>

</body>
</html>
