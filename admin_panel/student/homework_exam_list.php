<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';
// ðŸ”’ Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'student') {
  header("Location: ../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];
$scl_reg_no = $_SESSION['scl_reg_no'];
$student_id = $_SESSION['user_data']['id'];

//  Fetch student's class and session
// $student_query = mysqli_query(
//     $con,
//     "SELECT DISTINCT scl_class, scl_session 
//      FROM rrsv_student_registration 
//      WHERE id IN ($ids)"
// );

// $classNames = [];
// $session = null;

// while ($row = mysqli_fetch_assoc($student_query)) {
//     $classNames[] = "'" . mysqli_real_escape_string($con, $row['scl_class']) . "'";
//     $session = $row['scl_session']; // same session assumed
// }

// $classNamesStr = implode(',', $classNames);

// $class_query = mysqli_query(
//     $con,
//     "SELECT id, class_name 
//      FROM rrsv_class 
//      WHERE class_name IN ($classNamesStr)"
// );

// $class_ids = [];
// while ($row = mysqli_fetch_assoc($class_query)) {
//     $class_ids[] = $row['id'];
// }



// 🧠 Fetch student's class and session

$student_query = mysqli_query($con, "
    SELECT *
    FROM rrsv_student_registration
    WHERE scl_reg_no = '$scl_reg_no'
    ORDER BY scl_session DESC, id DESC
    LIMIT 1
");


$student_data = mysqli_fetch_assoc($student_query);

/* 📘 Class & Session */
$class_name = $student_data['scl_class'];
$session    = $student_data['scl_session'];
$class_query = mysqli_query($con, "SELECT id FROM rrsv_class WHERE class_name='$class_name' LIMIT 1");
$class_row   = mysqli_fetch_assoc($class_query);
$class_id    = $class_row['id'];







//print_r($class_id);die();
// ðŸ“š Fetch exams for student's class and session
// $exams = mysqli_query($con, "
//     SELECT he.*, s.sub_name 
//     FROM rrsv_homework_exam AS he
//     LEFT JOIN rrsv_subject AS s ON he.subject_id = s.id
//     WHERE he.class_id='$class_id' 
//     AND he.session='$session'
//     ORDER BY he.id DESC
// ");
//$exams = $con->query("SELECT * FROM rrsv_homework_exam WHERE class_id='$class_id' ORDER BY id DESC");


$exams = $con->query("
    SELECT he.id,
          he.session,
          he.unit,
          he.book_name,
          he.chapter_name,
          he.type,
          he.class_date,
          he.created_at,
          he.status,
          c.class_name,
          s.sub_name,
          t.full_name AS teacher_name
    FROM rrsv_homework_exam AS he
    LEFT JOIN rrsv_class AS c ON he.class_id = c.id
    LEFT JOIN rrsv_subject AS s ON he.subject_id = s.id
    LEFT JOIN rrsv_teacher AS t
       ON he.teacher_id = t.id
    WHERE class_id='$class_id' AND he.status='Active' ORDER BY id DESC
");


//print_r($exam = mysqli_fetch_assoc($exams));die();

// $exams = $con->query("
//     SELECT he.id,
//           he.session,
//           he.unit,
//           he.book_name,
//           he.chapter_name,
//           he.type,
//           he.class_date,
//           he.created_at,
//           c.class_name,
//           s.sub_name
//     FROM rrsv_homework_exam AS he
//     LEFT JOIN rrsv_class AS c ON he.class_id = c.id
//     LEFT JOIN rrsv_subject AS s ON he.subject_id = s.id
//     WHERE he.class_id IN ($class_ids_str)
//     ORDER BY he.id DESC
// ");

?>

<!DOCTYPE html>
<html>
<?php include('../header.php'); ?>
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
            <h1>Homework / Exam List</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Available Exams</h3>
                        </div>

                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Teacher</th>
                                        <!--<th>Title</th>-->
                                        <th>Type</th>
                                        <th>Subject</th>
                                         <th>Book</th>
                                          <th>Chapter</th>
                                         <th>
                                               status
                                            </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($exam = mysqli_fetch_assoc($exams)) {
                                        // Check if already submitted
                                        $check = mysqli_query($con, "
                                            SELECT id 
                                            FROM rrsv_student_homework_summary 
                                            WHERE student_id='$student_id' 
                                            AND exam_id='{$exam['id']}'
                                        ");
                                        $submitted = mysqli_num_rows($check) > 0;
                                    ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= htmlspecialchars($exam['teacher_name']); ?>
                                            <!--<td><?= htmlspecialchars($exam['title']); ?></td>-->
                                            <td><?= ucfirst($exam['type']); ?></td>
                                            <td><?= htmlspecialchars($exam['sub_name']); ?></td>
                                             <td><?= htmlspecialchars($exam['book_name']); ?></td>
                                              <td><?= htmlspecialchars($exam['chapter_name']); ?></td>
                                             <td> <?php if ($submitted): ?>
                                               
                                                    <span class="label label-success">Submitted</span>
                                                <?php else: ?>
                                                    <span class="label label-danger">Not Submitted</span>
                                                <?php endif; ?>  </td>
                                            <td>
                                                <?php if ($submitted): ?>
                                                <a href="view_results.php?exam_id=<?= $exam['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                                    <!--<span class="label label-success">Submitted</span>-->
                                                <?php else: ?>
                                                    <a href="start_exam.php?exam_id=<?= $exam['id']; ?>" class="btn btn-primary btn-sm">Start</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div> <!-- /.box-body -->
                    </div> <!-- /.box -->
                </div>
            </div>
        </section>
    </div>

    <?php include '../footer.php'; ?>
    <div class="control-sidebar-bg"></div>
</div>

<!-- Scripts -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script src="../assets/js/app.min.js"></script>
<script>
$(function () {
  $("#example1").DataTable();
});
</script>

</body>
</html>
