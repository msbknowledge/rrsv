
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
    <style>
     
        .profile-card img {
            width: 120px; height: 120px; border-radius: 50%;
            object-fit: cover; border: 3px solid #007bff;
        }
        .edit-btn { float: right; }
    </style>
</head>
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


$role = $_SESSION['role'];
$scl_reg_no = $teacher['scl_reg_no'];
$user_id = $_SESSION['user_id']; // assuming set during login
//print_r($scl_reg_no);die();
// If teacher/admin wants to view a student profile (via ?id=)
if (($role == 'student' || $role == 'admin') && isset($_GET['scl_reg_no'])) {
    $scl_reg_no = intval($_GET['$scl_reg_no']);
    $student_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no = $scl_reg_no");
} 
// If logged-in student wants to see their own profile
else {
    $student_q = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no = '$scl_reg_no' LIMIT 1");
}

if (mysqli_num_rows($student_q) == 0) {
    echo "<p>No student record found.</p>";
    exit();
}

$student = mysqli_fetch_assoc($student_q);

// --- Allow update only for STUDENT role ---
if ($role == 'student' && isset($_POST['update'])) {
    $scl_name = mysqli_real_escape_string($con, $_POST['scl_name']);
    $scl_class = mysqli_real_escape_string($con, $_POST['scl_class']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $scl_section = mysqli_real_escape_string($con, $_POST['scl_section']);
    $scl_roll_no = mysqli_real_escape_string($con, $_POST['scl_roll_no']);
    $scl_dob = mysqli_real_escape_string($con, $_POST['scl_dob']);
    $father_name = mysqli_real_escape_string($con, $_POST['father_name']);
    $mother_name = mysqli_real_escape_string($con, $_POST['mother_name']);
    $scl_contact = mysqli_real_escape_string($con, $_POST['scl_contact']);
    $scl_address = mysqli_real_escape_string($con, $_POST['scl_address']);

    $photo = $student['image']; // default old photo

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../../student_reg_image/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo = $photo_name;
    }

    // Update query
    $update_q = mysqli_query($con, "
        UPDATE rrsv_student_registration SET 
            scl_name = '$scl_name',
            password = '$password',
            scl_class = '$scl_class',
            scl_section = '$scl_section',
            scl_roll_no = '$scl_roll_no',
            scl_dob = '$scl_dob',
            scl_father_name = '$father_name',
            scl_mother_name = '$mother_name',
            scl_phone_no = '$scl_contact',
            scl_address = '$scl_address',
            image = '$photo'
        WHERE scl_reg_no = '$scl_reg_no'
    ");

    if ($update_q) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $error = "Error updating profile: " . mysqli_error($con);
    }
}
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
 <section class="content">
      <div class="row">
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Profile</h3>
              <?php if($role == 'student'): ?>
                <button class="btn btn-sm btn-primary edit-btn" id="editBtn">Edit</button>
            <?php endif; ?>
            </div>
    <div class="profile-card">
        
        <hr>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div id="viewMode">
            <div class="text-center mb-3">
                  <img src="<?php echo $image ?>" alt="Student Photo">
                <h4 class="mt-2"><?php echo htmlspecialchars($student['scl_name']); ?></h4>
                <p class="text-muted">Reg No: <?php echo htmlspecialchars($student['scl_reg_no']); ?></p>
            </div>
            <table class="table table-bordered">
                <?php
                $pass = $student['password'];
                $masked = substr($pass, 0, 4) . str_repeat('*', max(strlen($pass) - 4, 0));
                ?>
                <tr><th>Password</th><td><?php echo $masked; ?></td></tr>
                <tr><th>Class</th><td><?php echo $student['scl_class']; ?></td></tr>
                <tr><th>Section</th><td><?php echo $student['scl_section']; ?></td></tr>
                <tr><th>Roll No</th><td><?php echo $student['scl_roll_no']; ?></td></tr>
                <tr><th>Date of Birth</th><td><?php echo $student['scl_dob']; ?></td></tr>
                <tr><th>Father’s Name</th><td><?php echo $student['scl_father_name']; ?></td></tr>
                <tr><th>Mother’s Name</th><td><?php echo $student['scl_mother_name']; ?></td></tr>
                <tr><th>Contact</th><td><?php echo $student['scl_phone_no']; ?></td></tr>
                <tr><th>Address</th><td><?php echo $student['scl_address']; ?></td></tr>
            </table>
        </div>

        <?php if($role == 'student'): ?>
        <form id="editForm" method="POST" enctype="multipart/form-data" style="display:none;">
             <div class="box-body">
            <div class="mb-3 text-center">
                <img src="<?php echo $image ?>" alt="Student Photo">
                <input type="file" name="photo" class="form-control mt-2" accept="image/*">
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Name</label>
                    <input type="text" name="scl_name" class="form-control" value="<?php echo $student['scl_name']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Class</label>
                    <input type="text" name="scl_class" class="form-control" value="<?php echo $student['scl_class']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Section</label>
                    <input type="text" name="scl_section" class="form-control" value="<?php echo $student['scl_section']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Roll No</label>
                    <input type="text" name="scl_roll_no" class="form-control" value="<?php echo $student['scl_roll_no']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Date of Birth</label>
                    <input type="date" name="scl_dob" class="form-control" value="<?php echo $student['scl_dob']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact No</label>
                    <input type="text" name="scl_contact" class="form-control" value="<?php echo $student['scl_phone_no']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Password</label>
                    <input type="text" name="password" class="form-control" value="<?php echo $student['password']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Father’s Name</label>
                    <input type="text" name="father_name" class="form-control" value="<?php echo $student['scl_father_name']; ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Mother’s Name</label>
                    <input type="text" name="mother_name" class="form-control" value="<?php echo $student['scl_mother_name']; ?>" readonly>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Address</label>
                    <textarea name="scl_address" class="form-control" readonly><?php echo $student['scl_address']; ?></textarea>
                </div>
            </div>
             </div>
 <div class="box-footer text-right">
            <button type="submit" name="update" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
             </div>
        </form>
        <?php endif; ?>
    </div>
    </div>
        </div>
      </div>
    </section>
</div>



      <!-- /.content -->
    </div>
 <?php include '../footer.php' ?>
    <div class="control-sidebar-bg"></div>
  </div>

<script>
<?php if($role == 'student'): ?>
    document.getElementById("editBtn").onclick = function() {
        document.getElementById("viewMode").style.display = "none";
        document.getElementById("editForm").style.display = "block";
        this.style.display = "none";
    };
    document.getElementById("cancelEdit").onclick = function() {
        document.getElementById("viewMode").style.display = "block";
        document.getElementById("editForm").style.display = "none";
        document.getElementById("editBtn").style.display = "inline-block";
    };
<?php endif; ?>
</script>











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