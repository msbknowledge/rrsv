<?php
include('../../include/dbcon.php');
// Set default role name
$default_role = 'Admin';

// Get all table names from the current database
$query = "SHOW TABLES";
$result = mysqli_query($myDB, $query);

if (!$result) {
  die("Error fetching tables: " . mysqli_error($myDB));
}

$count = 0;
while ($row = mysqli_fetch_array($result)) {
  $table_name = $row[0];

  // Check if already exists in rrsv_permission
  $check_sql = "SELECT id FROM rrsv_permission WHERE role_name='$default_role' AND table_name='$table_name'";
  $check_res = mysqli_query($myDB, $check_sql);

  if (mysqli_num_rows($check_res) == 0) {
    // Insert default permission (view only)
    $insert_sql = "INSERT INTO rrsv_permission (role_name, table_name, can_view, can_insert, can_update, can_delete,can_display_landing,status)
                       VALUES ('$default_role', '$table_name', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes')";
    mysqli_query($myDB, $insert_sql);
    $count++;
  }
}

// ✅ Handle Delete
if (isset($_GET['delete'])) {
  $del_id = intval($_GET['delete']);
  mysqli_query($myDB, "DELETE FROM rrsv_permission WHERE id='$del_id'");
  $msg = "🗑️ Permission deleted successfully!";
}

// ✅ Fetch all permissions
$result = mysqli_query($myDB, "SELECT * FROM rrsv_permission ORDER BY role_name, table_name");

include('../../include/header.php');
?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
  <div class="content-wrapper">

    <?php if (!empty($msg)) { ?>
      <div class="alert alert-success text-center"><?= $msg; ?></div>
    <?php } ?>

    <div class="card">
      <div class="card-body">
        <h4 class="card-title">
          <i class="mdi mdi-lock"></i> Manage Permissions
        </h4>
      </div>
    </div>

    <div class="col-12 grid-margin">
      <div class="card p-4">

        <h5 class="text-center mt-4 mb-3">📋 Existing Permissions</h5>
        <table class="table table-bordered table-striped text-center align-middle pt-4" id="permissionTable">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Role</th>
              <th>Table</th>
              <!-- <th>View</th>
              <th>Insert</th>
              <th>Update</th>
              <th>Delete</th> -->
              <th>Display Landing</th>
              <!-- <th>Status</th> -->
              <!-- <th>Action</th> -->
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) {
              if (in_array($row['table_name'], ['rrsv_notice', 'rrsv_holiday', 'rrsv_routine', 'rrsv_admission', 'rrsv_syllabus'])) {
                ?>
                <tr>
                  <td><?= $row['id']; ?></td>
                  <td><?= htmlspecialchars($row['role_name']); ?></td>
                  <td><?php
                  if ($row['table_name'] == 'rrsv_notice') {
                    echo "Notice";
                  } elseif ($row['table_name'] == 'rrsv_holiday') {
                    echo "Holiday";
                  } elseif ($row['table_name'] == 'rrsv_routine') {
                    echo "Routine";
                  } elseif ($row['table_name'] == 'rrsv_admission') {
                    echo "Admission";
                  } elseif ($row['table_name'] == 'rrsv_syllabus') {
                    echo "Syllabus";
                  }
                  ?></td>
                  <!-- <td><input type="checkbox" <?= ($row['can_view'] == 'Yes') ? 'checked' : ''; ?>
                    onchange="updatePermission(<?= $row['id']; ?>, 'can_view', this.checked)">
                </td>
                <td><input type="checkbox" <?= ($row['can_insert'] == 'Yes') ? 'checked' : ''; ?>
                    onchange="updatePermission(<?= $row['id']; ?>, 'can_insert', this.checked)">
                </td>
                <td><input type="checkbox" <?= ($row['can_update'] == 'Yes') ? 'checked' : ''; ?>
                    onchange="updatePermission(<?= $row['id']; ?>, 'can_update', this.checked)">
                </td>
                <td><input type="checkbox" <?= ($row['can_delete'] == 'Yes') ? 'checked' : ''; ?>
                    onchange="updatePermission(<?= $row['id']; ?>, 'can_delete', this.checked)">
                </td> -->
                  <td><input type="checkbox" <?= ($row['can_display_landing'] == 'Yes') ? 'checked' : ''; ?>
                      onchange="updatePermission(<?= $row['id']; ?>, 'can_display_landing', this.checked)">
                  </td>
                  <!-- <td><input type="checkbox" <?= ($row['status'] == 'Yes') ? 'checked' : ''; ?>
                    onchange="updatePermission(<?= $row['id']; ?>, 'status', this.checked)">
                </td> -->

                  <!-- <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id']; ?>)">
                      🗑️ Delete</button>
                  </td> -->
                </tr>
              <?php
              }
            }
            ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include('../../include/footer.php'); ?>

<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>

<script>
  $(document).ready(function () {
    $('#permissionTable').DataTable({
      pageLength: 25,
      order: [[1, 'asc']]
    });
  });

  // ✅ AJAX checkbox update
  function updatePermission(id, column, value) {
    $.ajax({
      url: 'update_permission.php',
      type: 'POST',
      data: {
        id: id,
        column: column,
        value: value ? 1 : 0
      },
      success: function (res) {
        try {
          let data = JSON.parse(res);
          if (data.status === 'success') {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: data.message,
              showConfirmButton: false,
              timer: 1000
            });
          } else {
            Swal.fire('Error', data.message, 'error');
          }
        } catch (e) {
          console.error(res);
        }
      }
    });
  }

  function confirmDelete(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "This permission will be permanently deleted.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '?delete=' + id;
      }
    });
  }
</script>