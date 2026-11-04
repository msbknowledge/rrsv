<?php

include('../../include/dbcon.php');

// ✅ Handle Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_permission'])) {
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  $role_name = mysqli_real_escape_string($myDB, trim($_POST['role_name']));
  $table_name = mysqli_real_escape_string($myDB, trim($_POST['table_name']));
  $can_view = isset($_POST['can_view']) ? 'Yes' : 'No';
  $can_insert = isset($_POST['can_insert']) ? 'Yes' : 'No';
  $can_update = isset($_POST['can_update']) ? 'Yes' : 'No';
  $can_delete = isset($_POST['can_delete']) ? 'Yes' : 'No';
  $status = isset($_POST['status']) ? mysqli_real_escape_string($myDB, $_POST['status']) : 'Active';

  if ($id > 0) {
    // Update existing record
    $sql = "UPDATE rrsv_permission SET 
                    role_name='$role_name',
                    table_name='$table_name',
                    can_view='$can_view',
                    can_insert='$can_insert',
                    can_update='$can_update',
                    status='$status',
                    can_delete='$can_delete'
                WHERE id='$id'";
    mysqli_query($myDB, $sql);
    $msg = "✅ Permission updated successfully!";
  } else {
    // Insert new permission
    $sql = "INSERT INTO rrsv_permission (role_name, table_name, can_view, can_insert, can_update, can_delete, status)
                VALUES ('$role_name', '$table_name', '$can_view', '$can_insert', '$can_update', '$can_delete', '$status')";
    mysqli_query($myDB, $sql);
    $msg = "✅ New permission added successfully!";
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

// ✅ For edit mode
$editData = null;
if (isset($_GET['edit'])) {
  $edit_id = intval($_GET['edit']);
  $editData = mysqli_fetch_assoc(mysqli_query($myDB, "SELECT * FROM rrsv_permission WHERE id='$edit_id'"));
}
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

        <!-- ✅ Permission Form -->
        <form method="POST" class="row g-3 mb-4">
          <input type="hidden" name="id" value="<?= $editData['id'] ?? ''; ?>">

          <div class="col-md-3">
            <label class="form-label fw-bold">Role Name</label>
            <select name="role_name" class="form-control">
              <option value="Active" <?= (($editData['role_name'] ?? '') == 'Admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Table Name</label>
            <select name="table_name" class="form-control" required>
              <option value="">-- Select Table --</option>
              <?php
              $table = "SHOW TABLES";
              $tables = mysqli_query($myDB, $table);
              while ($t_row = mysqli_fetch_array($tables)) {
                $table_name = $t_row[0];
                $selected = (isset($editData['table_name']) && $editData['table_name'] == $table_name) ? 'selected' : '';
                echo "<option value='$table_name' $selected>$table_name</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-control">
              <option value="Active" <?= (($editData['status'] ?? '') == 'Active') ? 'selected' : ''; ?>>Active</option>
              <option value="Inactive" <?= (($editData['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>>Inactive
              </option>
            </select>
          </div>

          <div class="col-md-6 d-flex align-items-center justify-content-around mt-4">
            <div><input type="checkbox" name="can_view" id="view" <?= ($editData['can_view'] ?? '') === 'Yes' ? 'checked' : ''; ?>> <label for="view">View</label></div>
            <div><input type="checkbox" name="can_insert" id="insert" <?= ($editData['can_insert'] ?? '') === 'Yes' ? 'checked' : ''; ?>> <label for="insert">Insert</label></div>
            <div><input type="checkbox" name="can_update" id="update" <?= ($editData['can_update'] ?? '') === 'Yes' ? 'checked' : ''; ?>> <label for="update">Update</label></div>
            <div><input type="checkbox" name="can_delete" id="delete" <?= ($editData['can_delete'] ?? '') === 'Yes' ? 'checked' : ''; ?>> <label for="delete">Delete</label></div>
          </div>

          <div class="col-md-12 text-center mt-3">
            <button type="submit" name="save_permission" class="btn btn-primary btn-rounded">
              💾 <?= isset($_GET['edit']) ? 'Update Permission' : 'Save Permission'; ?>
            </button>
            <?php if (isset($_GET['edit'])) { ?>
              <a href="manage_permission.php" class="btn btn-secondary btn-rounded ms-2">Cancel</a>
            <?php } ?>
          </div>
        </form>

        <!-- ✅ Permission List -->
        <h5 class="text-center mt-4 mb-3">📋 Existing Permissions</h5>
        <table class="table table-bordered table-striped text-center align-middle pt-4" id="permissionTable">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Role</th>
              <th>Table</th>
              <th>View</th>
              <th>Insert</th>
              <th>Update</th>
              <th>Delete</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['role_name']); ?></td>
                <td><?= htmlspecialchars($row['table_name']); ?></td>
                <td><?= $row['can_view']; ?></td>
                <td><?= $row['can_insert']; ?></td>
                <td><?= $row['can_update']; ?></td>
                <td><?= $row['can_delete']; ?></td>
                <td>
                  <span class="badge status-toggle <?= ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary'; ?>"
                    data-id="<?= $row['id']; ?>" data-status="<?= $row['status']; ?>" style="cursor:pointer;">
                    <?= $row['status']; ?>
                  </span>
                </td>

                <td>
                  <a href="?edit=<?= $row['id']; ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                  <!-- <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id']; ?>)">🗑️
                    Delete</button> -->
                </td>
              </tr>
            <?php } ?>
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
<script>
  $(document).on('click', '.status-toggle', function () {
    let el = $(this);
    let id = el.data('id');
    let current = el.data('status');
    let newStatus = (current === 'Active') ? 'Inactive' : 'Active';

    Swal.fire({
      title: 'Change Status?',
      text: `Switch to ${newStatus}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'status_permission.php',
          type: 'POST',
          dataType: 'json',
          data: { id: id, status: newStatus },
          success: function (res) {
            if (res.status === 1) {
              el.data('status', newStatus);
              el
                .removeClass('bg-success bg-secondary')
                .addClass(newStatus === 'Active' ? 'bg-success' : 'bg-secondary')
                .text(newStatus);

              // ✅ Show success popup
              Swal.fire({
                icon: 'success',
                title: 'Status Updated',
                text: `Permission set to ${newStatus}`,
                timer: 1500,
                showConfirmButton: false
              });
            } else {
              Swal.fire('Error', 'Update failed', 'error');
            }
          }
        });
      }
    });
  });



</script>