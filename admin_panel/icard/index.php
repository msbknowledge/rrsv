<?php
session_start();
include '../dbcon.php';

// Auth check
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['teacher', 'student', 'icard', 'admin'])) {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}

$teacher = $_SESSION['user_data'];
$user_id = $teacher['id'];

// Search + filters
$txtsearch = $_POST['txtsearch'] ?? '';

// Fetch students
$sql = "SELECT * FROM rrsv_student_registration";
if ($txtsearch != "") {
    $txtsearch = $con->real_escape_string($txtsearch);
    $sql .= " AND (scl_name LIKE '%$txtsearch%' OR scl_reg_no LIKE '%$txtsearch%')";
}
$sql .= " ORDER BY id DESC";
$result = mysqli_query($con, $sql);
?>

<?php include('../header.php'); ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <?php include '../top_navbar.php'; ?>
  </header>
  <?php include '../left_navbar.php'; ?>

  <div class="content-wrapper">
    <section class="content">
      <div class="box box-primary">
        <div class="box-header with-border d-flex justify-content-between align-items-center">
          <h3 class="box-title">🎓 I-Card Management</h3>
        </div>

        <div class="box-body">
          <form method="post" action="manage_icard.php" class="form-inline mb-3">
            <input type="text" name="txtsearch" value="<?= htmlspecialchars($txtsearch); ?>" class="form-control" placeholder="Search by Name or Reg No" style="width: 250px;">
            <button type="submit" class="btn btn-primary ml-2">Search</button>
            <a href="manage_icard.php" class="btn btn-secondary ml-2">Reset</a>
          </form>

          <form id="icardForm" method="post" action="print_icard_a3.php" target="_blank">
            <div class="mb-3 text-right">
              <button type="submit" class="btn btn-success btn-sm">🖨️ Print Selected I-Cards</button>
               <!--<button type="submit" class="btn btn-success btn-sm">🖨️ Print Selected I-Cards</button>-->
            </div>

            <table id="example1" class="table table-bordered table-striped">
              <thead class="thead-dark">
                <tr>
                  <th><input type="checkbox" id="select_all"></th>
                  <th>#</th>
                  <th>Photo</th>
                  <th>Registration No</th>
                  <th>Student Name</th>
                  <th>Class</th>
                  <th>Status</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $count = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                  <tr>
                    <td><input type="checkbox" class="row_checkbox" name="student_ids[]" value="<?= $row['id']; ?>"></td>
                    <td><?= $count++; ?></td>
                    <td>
                      <?php if (!empty($row['image'])): ?>
                        <img src="../../student_reg_image/<?= htmlspecialchars($row['image']); ?>" width="60" height="60" style="border-radius: 5px;">
                      <?php else: ?>
                        <span class="text-muted">No Image</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['scl_reg_no']); ?></td>
                    <td><?= htmlspecialchars($row['scl_name']); ?></td>
                    <td><?= htmlspecialchars($row['scl_class']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td class="text-center">
                      <a href="print_card.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-primary btn-sm">Single I-Card</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </form>
        </div>
      </div>
    </section>
  </div>

  <?php include '../footer.php'; ?>
</div>

<!-- JS Libraries -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>

<script>
$(function () {
  $("#example1").DataTable({
    "pageLength": 32,
     "lengthMenu": [[32, 50, 100, -1], [32, 50, 100, "All"]],
    "order": [[1, "asc"]]
  });
});

// Select all checkboxes
$('#select_all').on('click', function() {
  $('.row_checkbox').prop('checked', this.checked);
});

// Uncheck master checkbox if any box is unchecked
$(document).on('change', '.row_checkbox', function() {
  $('#select_all').prop('checked', $('.row_checkbox:checked').length === $('.row_checkbox').length);
});

// Validate selection before printing
$('#icardForm').on('submit', function() {
  if ($('.row_checkbox:checked').length === 0) {
    alert('Please select at least one student to print.');
    return false;
  }
});
</script>

</body>
</html>
