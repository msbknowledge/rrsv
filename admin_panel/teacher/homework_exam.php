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
$teacher_id=$teacher['id'];
include '../../include/config.php';
include('../header.php');
// Fetch all exams
// Fetch classes, sections, subjects for dropdowns
$classes = mysqli_query($con, "SELECT id, class_name FROM rrsv_class");
$sections = mysqli_query($con, "SELECT id, section_name FROM rrsv_section");
$subjects = mysqli_query($con, "SELECT id, sub_name FROM rrsv_subject");
//$result = $con->query("SELECT * FROM rrsv_homework_exam ORDER BY id DESC");
$result = $con->query("
   SELECT 
    he.id,
    he.title,
    he.session,
    he.unit,
    he.book_name,
    he.chapter_name,
    he.type,
    he.class_date,
    he.created_at,
    he.question_file,
he.status,
    c.class_name,
    s.sub_name,
    t.full_name AS teacher_name,

    COUNT(DISTINCT q.id) AS total_question_count,
    COUNT(DISTINCT shs.id) AS total_submit_count

FROM rrsv_homework_exam AS he

LEFT JOIN rrsv_class AS c 
       ON he.class_id = c.id

LEFT JOIN rrsv_subject AS s 
       ON he.subject_id = s.id

LEFT JOIN rrsv_teacher AS t
       ON he.teacher_id = t.id

LEFT JOIN rrsv_homework_exam_questions AS q
       ON q.exam_id = he.id

LEFT JOIN rrsv_student_homework_summary AS shs 
       ON shs.exam_id = he.id where he.teacher_id='$teacher_id'

GROUP BY 
    he.id,
    he.title,
    he.session,
    he.unit,
    he.book_name,
    he.chapter_name,
    he.type,
    he.class_date,
    he.created_at,
    
    c.class_name,
    s.sub_name,
    t.full_name 

ORDER BY he.id DESC;

");

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
        </header>
        <!-- Sidebar Start -->
        <?php include '../left_navbar.php' ?>
        <!-- sidebar End -->
        <div class="content-wrapper">


            <section class="content-header">
                <h1>Admin <small>Homework / Exam</small></h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Admin Exam</a></li>
                    <li class="active">Homework / Exam List</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Homework / Exam List</h3>
                                <div style="text-align: right;">
                                    <a href="#" class="btn-sm btn btn-primary" data-toggle="modal"
                                        data-target="#addExamModal" style="margin-bottom:4px;">Add Homework / Exam</a>
                                </div>
                            </div>

                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.NO</th>
                                            <!--<th>Teacher</th>-->
                                            <!--<th>Title</th>-->
                                            <th>Book</th>
                                            <th>Chapter</th>
                                            <!--<th>Type</th>-->
                                            <th>Class</th>
                                            <th>Subject</th>
                                            <th>Session</th>
                                            <th>Date</th>
                                            
                                            <th>Questions</th>
                                            

                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>File</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sn = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td><?= $sn++; ?></td>
                                                <!--<td><?= htmlspecialchars($row['teacher_name']); ?>-->
                                                <!--<td><?= htmlspecialchars($row['title']); ?></td>-->
                                                <td><?= htmlspecialchars($row['book_name']); ?></td>
                                                <td><?= htmlspecialchars($row['chapter_name']); ?></td>
                                                <!--<td><?= ucfirst($row['type']); ?></td>-->
                                                <td><?= $row['class_name']; ?></td>
                                                <td><?= htmlspecialchars($row['sub_name']); ?></td>
                                                <td><?= $row['session']; ?></td>
                                                <td><?= $row['class_date']; ?></td>
                                                <td><?= $row['total_question_count']; ?></td>
                                                <td><?= $row['total_submit_count']; ?></td>
                                                <td>
                                                    <button 
                                                        class="btn btn-xs <?= $row['status'] == 'active' ? 'btn-success' : 'btn-danger' ?>"
                                                        onclick="toggleStatus(<?= $row['id']; ?>, '<?= $row['status']; ?>')"
                                                        title="Toggle Status">
                                                        <?= $row['status'] == 'active' ? 'Active' : 'Inactive' ?>
                                                    </button>
                                                </td>
                                                <td>
                                                    <?php if(!empty($row['question_file'])): ?>
                                                        <a href="uploads/homework/<?= htmlspecialchars($row['question_file']); ?>" 
                                                           class="btn btn-xs btn-primary" 
                                                           target="_blank">
                                                           <i class="fa fa-download"></i> Download
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No File</span>
                                                    <?php endif; ?>
                                                <!--    <a href="uploads/homework/<?= htmlspecialchars($row['question_file']); ?>" -->
                                                <!--   class="btn btn-xs btn-primary"-->
                                                <!--   download>-->
                                                <!--   <i class="fa fa-download"></i> Download-->
                                                <!--</a>-->
                                                </td>
                                                <td>
                                                     <a href="home_work_results.php?exam_id=<?= $row['id']; ?>" 
                                                       class="btn btn-info btn-xs" title="View Results">
                                                        <i class="fa fa-bar-chart"></i>
                                                    </a>
                                                    <a href="print_homework_exam.php?exam_id=<?= $row['id']; ?>" 
                                                           class="btn btn-success btn-xs" target="_blank">
                                                           🖨 
                                                        </a>

                                                    <a href="homework_exam_questions.php?exam_id=<?= $row['id']; ?>"
                                                        class="btn btn-warning btn-xs"><i class="fa fa-eye"></i></a>
                                                    <a href="edit_homework_exam.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-info btn-xs"><i class="fa fa-edit"></i></a>
                                                    <a href="delete_homework_exam.php?id=<?= $row['id']; ?>"
                                                        class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Are you sure?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- Modal Add Homework/Exam -->
      
<!-- Modal Add Homework/Exam -->
<div class="modal fade" id="addExamModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg"> <!-- wider modal -->
    <form action="save_homework_exam.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add Homework / Exam</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Left Column -->
             <div class="col-md-6">
              <div class="form-group">
                <label>Class</label>
                <select name="class_id" class="form-control" id="class_id">
                  <option value="">-Select a Class-</option>
                  <?php
                  $sql = "select * from rrsv_class order by id";
                  $res = mysqli_query($con, $sql);
                  while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    ?>
                    <option value="<?php echo $obj['id']; ?>">
                      <?php echo $obj['class_name']; ?>
                    </option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label>Subject <span class="text-danger">*</span></label>
                 <select class="form-control" name="subject_id" id="subject_id" required>
                          <option value="">Select Subject</option>
                        </select>
               
              </div>
              <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control">
                  <option value="homework">Homework</option>
                  <option value="examination">Examination</option>
                  <option value="project_file">Project File</option>
                   <option value="class_test">Class Test</option>
                </select>
              </div>
            <select name="book_name" id="book_name" class="form-control" required>
              <option value="">Select Class & Session First</option>
            </select>
              <!--<div class="form-group">-->
              <!--  <label>Book</label>-->
              <!--  <input type="text" name="book_name" required class="form-control">-->
              <!--</div>-->
            
            </div>
             <!-- Right Column -->
            <div class="col-md-6">
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
                <label>Unit</label>
                <select name="unit" class="form-control" id="unit">
                  <?php
                  $selectedUnit = $_POST['unit'] ?? 1; // default to 1st Unit
                  for ($u = 1; $u <= 3; $u++) {
                    $unitLabel = ($u == 1) ? '1st Unit' :
                      (($u == 2) ? '2nd Unit' :
                        (($u == 3) ? '3rd Unit' : $u . 'th Unit'));
                    $sel = ($selectedUnit == $u) ? 'selected' : '';
                    echo "<option value='$u' $sel>$unitLabel</option>";
                  }
                  ?>
                </select>
              </div>
            
            <div class="form-group">
              <label>Date <span class="text-danger">*</span></label>
              <input type="date" name="class_date" id="class_date" required class="form-control" autocomplete="off">
            </div>
              

              <div class="form-group">
                <label>Chapter</label>
                <input type="text" name="chapter_name" required class="form-control">
              </div>
            </div>
            <div class="col-md-12">
             <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="editor" class="form-control" rows="5"></textarea>
              </div>

             </div>
           <div class="col-md-12">
             <div class="form-group">
                <label>Question</label>
                <input type="file" name="question_file" id="question_file" class="form-control">
              </div>

             </div>
          </div>
          <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?? ''; ?>">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

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
            $("#example1").DataTable({
        "order": [[6, "desc"]], // class_date DESC
        "pageLength": 10
    });
            $('#example2').DataTable({
                 "responsive": true,
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
    // $(document).ready(function () {
    //   $('#class_id').on('change', function () {
    //     var classId = $(this).val();
    //     alert();
    //     if (classId) {
    //       $.ajax({
    //         url: 'get_subjects.php',
    //         type: 'POST',
    //         data: { class_id: classId },
    //         success: function (response) {
    //           $('#subject_id').html(response);
    //         }
    //       });
    //     } else {
    //       $('#subject_id').html('<option value="">Select Subject</option>');
    //     }
    //   });
    // });
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
  
  $("#class_id, #session").on("change", function () {
        let class_id = $('#class_id option:selected').text();
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
<script>
function toggleStatus(id, currentStatus) {
    let newStatus = currentStatus === 'active' ? 'inactive' : 'active';

    if (!confirm("Change status to " + newStatus + "?")) return;

    $.ajax({
        url: 'toggle_homework_exam_status.php',
        type: 'POST',
        data: {
            id: id,
            status: newStatus
        },
        success: function (response) {
            if (response.trim() === 'success') {
                location.reload(); // simplest + safe
            } else {
                alert('Status update failed');
            }
        }
    });
}
</script>

</body>

</html>