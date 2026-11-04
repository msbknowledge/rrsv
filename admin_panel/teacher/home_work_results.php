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
<style>.mailbox-outlook {
    height: 100%;
    background: #f7f7f7;
}

.mailbox-left {
    background: #fff;
    height: calc(100vh - 50px);
    padding: 15px;
    overflow-y: auto;
    border-right: 1px solid #ddd;
}

.mailbox-nav li a {
    padding: 10px;
    font-size: 13px;
}

.mailbox-middle {
    background: #fff;
    height: calc(100vh - 50px);
    border-right: 1px solid #ddd;
    padding: 0;
    overflow-y: auto;
}

.mailbox-header {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    background: #f0f0f0;
}

.mailbox-title {
    margin: 0;
    font-size: 16px;
}

.mailbox-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mailbox-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: 0.2s;
}

.mailbox-item:hover {
    background: #f5f5f5;
}

.mailbox-item.active {
    background: #e3f2fd;
    border-left: 4px solid #0073e6;
}

.mailbox-item .from {
    font-weight: bold;
    font-size: 13px;
}

.mailbox-item .subject {
    font-size: 12px;
    color: #555;
}

.mailbox-item .time {
    font-size: 11px;
    color: #999;
    text-align: right;
}

.mailbox-right {
    background: #fff;
    height: calc(100vh - 50px);
    padding: 0;
    overflow-y: auto;
}

.mailbox-read-header {
    padding: 15px;
    border-bottom: 1px solid #ddd;
}

.mailbox-read-time {
    color: #888;
    font-size: 12px;
}

.mailbox-read-body {
    padding: 20px;
    min-height: 300px;
    font-size: 14px;
}

.mailbox-read-actions {
    padding: 15px;
    border-top: 1px solid #ddd;
    background: #fafafa;
}

</style>
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

      <section class="content">
        <div class="row hidden">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Select Homework / Exam</h3>
              </div>
              <div class="box-body">
                <form method="GET">
                  <div class="form-group">
                    <label>Select Exam:</label>
                    <select name="exam_id" class="form-control" required>
                      <option value="">-- Select Exam --</option>
                      <?php while ($exam = $exams->fetch_assoc()) { ?>
                        <option value="<?= $exam['id']; ?>" <?= (isset($_GET['exam_id']) && $_GET['exam_id'] == $exam['id']) ? 'selected' : ''; ?>>
                          <?= htmlspecialchars($exam['title']); ?> (<?= ucfirst($exam['type']); ?>)
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary">View Results</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_GET['exam_id'])):
          $exam_id = $_GET['exam_id'];
          
          $exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();
          
          $results = $con->query("SELECT s.*, u.scl_name AS student_name FROM rrsv_student_homework_summary s 
                             JOIN rrsv_student_registration u ON u.id=(SELECT MAX(id) FROM rrsv_student_registration  WHERE id = s.student_id)
                             WHERE s.exam_id='$exam_id' ORDER BY s.score DESC");
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
                              class="btn btn-info btn-sm">View Answers</a>
                             <button class="btn btn-primary btn-remarks"
                                    data-student="<?= $r['student_id']; ?>"
                                    data-exam="<?= $exam_id; ?>"
                                    data-name="<?= htmlspecialchars($r['student_name']); ?>">
                                Remarks
                            </button>

                              </td>
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




<!-- Remarks Modal -->
<div id="remarksModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <form method="POST" action="save_remarks.php">
        <div class="modal-header">
          <h4 class="modal-title">Student Remarks</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="exam_id" id="modal_exam_id">
          <input type="hidden" name="student_id" id="modal_student_id">

          <label id="studentLabel" style="font-weight:bold;"></label>
  <div class="form-group">
                        <label>Remarks</label>
                        <input type="text" name="remarks" id="modal_remarks">
                         <!--<textarea name="remarks" id="modal_remarks" class="form-control" rows="4"-->
          <!--  placeholder="Write remark..."></textarea>-->
                      </div>
         
         
         
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>

      </form>
    </div>

  </div>
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
    const outlookMails = {
        1: {
            title: "Tomorrow meeting at 1:00 PM",
            time: "Today 10:30 AM",
            body: "A meeting is scheduled tomorrow at 1:00 PM regarding new campus maintenance planning."
        },
        2: {
            title: "New academic session notice",
            time: "Yesterday 5:45 PM",
            body: "The new academic session will start from Monday. All teachers must update their timetables."
        },
        3: {
            title: "Updated exam schedule",
            time: "2 days ago",
            body: "Please note that the exam schedule has been updated. New dates are uploaded on the portal."
        }
    };

    function openOutlookMail(id) {
        // Update right panel
        document.getElementById("read-title").innerText = outlookMails[id].title;
        document.getElementById("read-time").innerText = outlookMails[id].time;
        document.getElementById("read-body").innerText = outlookMails[id].body;

        // Highlight selected email
        document.querySelectorAll(".mailbox-item").forEach(e => e.classList.remove("active"));
        event.currentTarget.classList.add("active");
    }
</script>


<script>
$(".btn-remarks").click(function() {

    let student_id = $(this).data("student");
    let exam_id = $(this).data("exam");
    let student_name = $(this).data("name");

    $("#modal_exam_id").val(exam_id);
    $("#modal_student_id").val(student_id);
    $("#studentLabel").text("Student: " + student_name);

    // Load existing remarks
    $.ajax({
        url: "load_remarks.php",
        type: "POST",
        data: { student_id, exam_id },
        success: function(data) {
            $("#modal_remarks").val(data);
        }
    });
     $("#remarksModal").modal("show");

});
</script>


</body>

</html>