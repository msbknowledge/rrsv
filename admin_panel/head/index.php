<?php
session_start();
include '../dbcon.php';

// Auth check
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['teacher', 'student'])) {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}

$teacher = $_SESSION['user_data'];
$user_id = $teacher['id'];

$types = mysqli_query($con, "SELECT * FROM rrsv_question_type ORDER BY id DESC");
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
        <div class="box-header with-border">
          <h3 class="box-title">Question Type Management</h3>
          <div class="text-right">
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#typeModal" onclick="openAddModal()">+ Add Type</button>
          </div>
        </div>

        <div class="box-body">
         <div class="mb-2">
  <!--<button id="deleteSelectedBtn" class="btn btn-danger btn-sm">Delete Selected</button>-->
</div>

<table id="example1" class="table table-bordered table-striped">
  <thead>
    <tr>
      <!--<th><input type="checkbox" id="select_all"></th>-->
      <th>#</th>
      <th>Name</th>
      <th class="text-center">Action</th>
    </tr>
  </thead>
  <tbody id="typeTable">
    <?php $i = 1; while ($t = mysqli_fetch_assoc($types)) { ?>
      <tr data-id="<?= $t['id']; ?>">
        <!--<td><input type="checkbox" class="row_checkbox" value="<?= $t['id']; ?>"></td>-->
        <td><?= $i++; ?></td>
        <td class="type-name"><?= htmlspecialchars($t['type_name']); ?></td>
        <td class="text-center">
          <?php if (!in_array(strtolower($t['type_code']), ['image', 'video', 'mcq', 'descriptive'])) { ?>
            <button class="btn btn-info btn-xs"
              onclick="openEditModal(<?= $t['id']; ?>, '<?= htmlspecialchars(addslashes($t['type_name'])); ?>')">
              <i class="fa fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-xs" onclick="deleteType(<?= $t['id']; ?>)">
              <i class="fa fa-trash"></i>
            </button>
          <?php } ?>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>
        </div>
      </div>
    </section>
  </div>

  <?php include '../footer.php'; ?>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="typeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="typeForm">
        <div class="modal-header">
          <h4 class="modal-title" id="modalTitle">Add Type</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="type_id" name="id">
          <div class="form-group">
            <label>Type Name</label>
            <input type="text" name="type_name" id="type_name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script>
$(function () {
  $("#example1").DataTable();
});

// Open Add Modal
function openAddModal() {
  $('#typeForm')[0].reset();
  $('#type_id').val('');
  $('#modalTitle').text('Add Type');
}

// Open Edit Modal
function openEditModal(id, name) {
  $('#type_id').val(id);
  $('#type_name').val(name);
  $('#modalTitle').text('Edit Type');
  $('#typeModal').modal('show');
}

// Save (Add/Edit)
$('#typeForm').submit(function(e){
  e.preventDefault();
  $.ajax({
    url: 'save_action.php',
    type: 'POST',
    data: $(this).serialize(),
    success: function(response){
      alert(response);
      location.reload();
    }
  });
});

// Delete Type
function deleteType(id) {
  if (confirm('Are you sure you want to delete this type?')) {
    $.ajax({
      url: 'save_action.php',
      type: 'POST',
      data: { delete_id: id },
      success: function(response){
        alert(response);
        location.reload();
      }
    });
  }
}
</script>
<script>
$(document).ready(function(){

  // Select/Deselect all checkboxes
  $('#select_all').on('click', function() {
    $('.row_checkbox').prop('checked', this.checked);
  });

  // Update master checkbox when individual boxes are toggled
  $(document).on('change', '.row_checkbox', function() {
    $('#select_all').prop('checked', $('.row_checkbox:checked').length === $('.row_checkbox').length);
  });

  // Bulk delete selected
  $('#deleteSelectedBtn').on('click', function() {
    var ids = [];
    $('.row_checkbox:checked').each(function() {
      ids.push($(this).val());
    });

    if (ids.length === 0) {
      alert('Please select at least one type to delete.');
      return;
    }

    if (!confirm('Are you sure you want to delete the selected types?')) return;

    $.ajax({
      url: 'delete_multiple_type.php',
      type: 'POST',
      data: { ids: ids },
      success: function(response) {
        alert(response);
        location.reload();
      },
      error: function() {
        alert('Error deleting selected records.');
      }
    });
  });

});
</script>
</body>
</html>
