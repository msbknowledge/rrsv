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
$teacher_id = $teacher['id'];
include('../header.php');

// Fetch teacher record
$sql = "SELECT * FROM rrsv_teacher WHERE id = '$teacher_id'";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) == 0) {
  echo "<div class='alert alert-danger'>Teacher not found.</div>";
  exit;
}
$row = mysqli_fetch_assoc($result);

// --- Update Profile ---
if (isset($_POST['update_profile'])) {
  $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
  $password= mysqli_real_escape_string($con, $_POST['password']);
    $fathers_name = mysqli_real_escape_string($con, $_POST['fathers_name']);
    $mothers_name = mysqli_real_escape_string($con, $_POST['mothers_name']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob = mysqli_real_escape_string($con, $_POST['d_o_b']);
    $pin = mysqli_real_escape_string($con, $_POST['pin']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $village = mysqli_real_escape_string($con, $_POST['village']);
    $post = mysqli_real_escape_string($con, $_POST['post']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $designation = mysqli_real_escape_string($con, $_POST['designation']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $monthly_salary = mysqli_real_escape_string($con, $_POST['monthly_salary']);
    $aadhar_no = mysqli_real_escape_string($con, $_POST['aadhar_no']);
    $pan_no = mysqli_real_escape_string($con, $_POST['pan_no']);
    $bank_account_no = mysqli_real_escape_string($con, $_POST['bank_account_no']);
    $bank_brunch_name = mysqli_real_escape_string($con, $_POST['bank_brunch_name']);
    $bank_ifsc = mysqli_real_escape_string($con, $_POST['bank_ifsc']);
    $tech_phone_no = mysqli_real_escape_string($con, $_POST['tech_phone_no']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
  // Handle photo upload
  $photo = $row['image'];
  if (!empty($_FILES['photo']['name'])) {
    $target_dir = "../../teacher_image/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $file_name = time() . "_" . basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . $file_name;
    move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
    $photo = $file_name;
  }

  $update = "
    UPDATE rrsv_teacher SET 
          password='$password',
          full_name='$full_name',
            fathers_name='$fathers_name',
            mothers_name='$mothers_name',
            gender='$gender',
            d_o_b='$dob',
            pin='$pin',
            address='$address',
            village='$village',
            post='$post',
            email='$email',
            designation='$designation',
            qualification='$qualification',
            monthly_salary='$monthly_salary',
            aadhar_no='$aadhar_no',
            pan_no='$pan_no',
            bank_account_no='$bank_account_no',
            bank_brunch_name='$bank_brunch_name',
            bank_ifsc='$bank_ifsc',
            image='$image',
            status='$status',
            tech_phone_no='$tech_phone_no'
    WHERE id = '$teacher_id'
  ";

  if (mysqli_query($con, $update)) {
    echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
    exit;
  } else {
    echo "<div class='alert alert-danger'>Error: " . mysqli_error($con) . "</div>";
  }
}
?>

<!DOCTYPE html>
<html>
<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">
  <!-- Header -->
  <header class="main-header">
    <a href="#" class="logo">
      <span class="logo-mini"><b>MSB</b></span>
      <span class="logo-lg">
        <img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;">
      </span>
    </a>
    <?php include '../top_navbar.php'; ?>
  </header>

  <!-- Sidebar -->
  <?php include '../left_navbar.php'; ?>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Teacher Profile</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Profile</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Profile</h3>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                <div class="text-center mb-3">
                  <img src="<?php echo $image ?>" 
                       style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #007bff;">
                  <input type="file" name="photo" class="form-control mt-2" accept="image/*" style="width:50%;margin:auto;">
                </div>

              <div class="row">
        <div class="col-md-6">
          <div class="form-group"><label>Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= $row['full_name']; ?>" readonly></div>
            <div class="form-group"><label>Password</label>
            <input type="text" name="password" class="form-control" value="<?= $row['password']; ?>" required></div>

          <div class="form-group"><label>Father's Name</label>
            <input type="text" name="fathers_name" class="form-control" value="<?= $row['fathers_name']; ?>" readonly></div>

          <div class="form-group"><label>Mother's Name</label>
            <input type="text" name="mothers_name" class="form-control" value="<?= $row['mothers_name']; ?>" readonly></div>

          <div class="form-group"><label>Gender</label>
            <select name="gender" class="form-control" readonly>
              <option value="male" <?= ($row['gender']=='male')?'selected':''; ?>>Male</option>
              <option value="female" <?= ($row['gender']=='female')?'selected':''; ?>>Female</option>
              <option value="other" <?= ($row['gender']=='other')?'selected':''; ?>>Other</option>
            </select></div>

          <div class="form-group"><label>Date of Birth</label>
            <input type="date" name="d_o_b" class="form-control" value="<?= $row['d_o_b']; ?>" readonly></div>

          <div class="form-group"><label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= $row['email']; ?>" readonly></div>

          <div class="form-group"><label>Phone</label>
            <input type="text" name="tech_phone_no" class="form-control" value="<?= $row['tech_phone_no']; ?>" readonly></div>

          <div class="form-group"><label>Designation</label>
            <input type="text" name="designation" class="form-control" value="<?= $row['designation']; ?>" readonly></div>
        </div>

        <div class="col-md-6">
          <div class="form-group"><label>Qualification</label>
            <input type="text" name="qualification" class="form-control" value="<?= $row['qualification']; ?>" readonly></div>

          <div class="form-group"><label>Monthly Salary</label>
            <input type="number" name="monthly_salary" class="form-control" value="<?= $row['monthly_salary']; ?>" readonly></div>

          <div class="form-group"><label>Aadhar No</label>
            <input type="text" name="aadhar_no" class="form-control" value="<?= $row['aadhar_no']; ?>" readonly></div>

          <div class="form-group"><label>PAN No</label>
            <input type="text" name="pan_no" class="form-control" value="<?= $row['pan_no']; ?>" readonly></div>

          <div class="form-group"><label>Bank Account No</label>
            <input type="text" name="bank_account_no" class="form-control" value="<?= $row['bank_account_no']; ?>" readonly></div>

          <div class="form-group"><label>Bank Branch Name</label>
            <input type="text" name="bank_brunch_name" class="form-control" value="<?= $row['bank_brunch_name']; ?>" readonly></div>

          <div class="form-group"><label>Bank IFSC</label>
            <input type="text" name="bank_ifsc" class="form-control" value="<?= $row['bank_ifsc']; ?>" readonly></div>

          <div class="form-group"><label>Address</label>
            <textarea name="address" class="form-control" rows="2" readonly><?= $row['address']; ?></textarea></div>
<div class="form-group"><label>Pin No</label>
            <input type="text" name="pin" class="form-control" value="<?= $row['pin']; ?>" readonly></div>
          <!--<div class="form-group"><label>Status</label>-->
          <!--  <select name="status" class="form-control">-->
          <!--    <option value="active" <?= ($row['status']=='active')?'selected':''; ?>>Active</option>-->
          <!--    <option value="inactive" <?= ($row['status']=='inactive')?'selected':''; ?>>Inactive</option>-->
          <!--  </select></div>-->
        </div>
      </div>

      <div class="box-footer text-right mt-3">
        <button type="submit" name="update_profile" class="btn btn-success">Update</button>
        <a href="../index.php" class="btn btn-default">Cancel</a>
      </div>
    </form>
  </div>
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
<script src="../assets/js/jquery.slimscroll.min.js"></script>
<script src="../assets/js/fastclick.js"></script>
<script src="../assets/js/app.min.js"></script>
<script src="../assets/js/demo.js"></script>

</body>
</html>
