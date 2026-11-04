<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = 0;
$vendor = ['name' => '', 'description' => '', 'status' => 'Active'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "SELECT * FROM rrsv_vendor WHERE id=$id";
  $res = mysqli_query($myDB, $sql);
  $vendor = mysqli_fetch_array($res, MYSQLI_ASSOC);
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title"><?= $id > 0 ? 'Edit Vendor' : 'Add Vendor' ?></h4>
            <div id="alert"></div>
            
            <form id="vendorForm">
            <div class="row">
              <input type="hidden" name="id" value="<?= $vendor['id'] ?? 0; ?>" />
              
              <div class="form-group col-md-6">
                <label for="vendor_name">Name <span class="text-red"> * </span></label>
                <input type="text" class="form-control" id="vendor_name" name="name" 
                       value="<?= htmlspecialchars($vendor['name'] ?? '', ENT_QUOTES); ?>" required>
                <span id="error_name" style="color:red;"></span>
              </div>
              <div class="form-group col-md-6">
                <label for="vendor_name">Mobile No <span class="text-red"> * </span></label>
                <input type="text" class="form-control" id="mobile" name="mobile" 
                       value="<?= htmlspecialchars($vendor['mobile'] ?? '', ENT_QUOTES); ?>" required>
                <span id="error_name" style="color:red;"></span>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label for="vendor_name">Email </label>
                <input type="text" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($vendor['email'] ?? '', ENT_QUOTES); ?>" required>
                <span id="error_name" style="color:red;"></span>
              </div>
           
              <div class="form-group col-md-6">
                <label for="vendor_description">Address<span class="text-red"> * </span></label>
                <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($vendor['address'] ?? '', ENT_QUOTES); ?></textarea>
                <span id="error_description" style="color:red;"></span>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <label for="vendor_description">Description</label>
                <textarea class="form-control" id="vendor_description" name="description"><?= htmlspecialchars($vendor['description'] ?? '', ENT_QUOTES); ?></textarea>
                <span id="error_description" style="color:red;"></span>
              </div>

              <div class="form-group col-md-6">
                <label for="vendor_status">Status <span class="text-red"> * </span></label>
                <select name="status" class="form-control" id="vendor_status" required>
                  <option value="Active" <?= ($vendor['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option>
                  <option value="Inactive" <?= ($vendor['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
            </div>
              <button type="submit" class="btn btn-primary me-2">Submit</button>
              <a href="manage_vendor.php" class="btn btn-light">Back</a>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../../include/footer.php'); ?>

<script>
$('#vendorForm').submit(function(e) {
  e.preventDefault();

  let valid = true;
  if ($('#vendor_name').val().trim() === '') {
    $('#error_name').text('Please enter vendor name.');
    valid = false;
  } else {
    $('#error_name').text('');
  }

  if ($('#vendor_description').val().trim() === '') {
    $('#error_description').text('Please enter description.');
    valid = false;
  } else {
    $('#error_description').text('');
  }

  if (!valid) return false;

  $.ajax({
    type: "POST",
    url: "add_vendor_post.php",
    data: $('#vendorForm').serialize(),
    success: function(response) {
      if (response == 0) {
        $('#alert').html('<div class="alert alert-success">Vendor Added Successfully!</div>');
        $('#vendorForm')[0].reset();
      } else if (response == 1) {
        $('#alert').html('<div class="alert alert-success">Vendor Updated Successfully!</div>');
      } else if (response == 2) {
        $('#alert').html('<div class="alert alert-danger">Vendor with this name already exists!</div>');
      } else {
        $('#alert').html('<div class="alert alert-danger">Something went wrong.</div>');
      }
    }
  });
});
</script>
