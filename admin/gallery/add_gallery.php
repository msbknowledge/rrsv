<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = 0;
$rows = [];
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "SELECT * FROM rrsv_gallery WHERE id = $id";
  $res = mysqli_query($myDB, $sql);
$rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
}
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card" style="margin-left:188px;">
          <div class="card-body">
            <h4 class="card-title"><?= $id ? "Edit Gallery" : "Add Gallery"; ?></h4>
            <div id="alert"></div>
            <form class="forms-sample" id="galleryForm" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $rows['id'] ?? 0; ?>" />
              <input type="hidden" name="token" value="<?php echo $_SESSION['_token']; ?>" />

              <div class="form-group">
                <label>Title <span class="text-red"> * </span></label>
                <input type="text" class="form-control" name="title" value="<?= $rows['title'] ?? ''; ?>" required>
              </div>

              <div class="form-group">
                <label>Category<span class="text-red"> * </span></label>
                <select class="form-control" name="category" required>
                  <option value="Banner" <?= ($rows['category'] ?? '') == 'Banner' ? 'selected' : '' ?>>Banner</option>
                  <option value="Class" <?= ($rows['category'] ?? '') == 'Class' ? 'selected' : '' ?>>Class</option>
                  <option value="Gallery" <?= ($rows['category'] ?? '') == 'Gallery' ? 'selected' : '' ?>>Gallery</option>
                  <option value="Bank" <?= ($rows['category'] ?? '') == 'Bank' ? 'selected' : '' ?>>Bank</option>
                  <option value="Contact" <?= ($rows['category'] ?? '') == 'Contact' ? 'selected' : '' ?>>Contact</option>
                  <option value="About" <?= ($rows['category'] ?? '') == 'About' ? 'selected' : '' ?>>About</option>
                </select>
              </div>

              <div class="form-group">
                <label>Image <span class="text-red"> * </span> <small class="form-text text-muted">Allowed formats: JPG,
                    PNG, GIF | Max size: 2MB</small></label>
                <input type="file" class="form-control" name="image_path" accept="image/*" <?= empty($rows['image_path']) ? 'required' : '' ?>>
                <?php if (!empty($rows['image_path'])): ?>
                  <br><img src="<?= BASE_URL . $rows['image_path']; ?>" width="100">
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label>Status <span class="text-red"> * </span></label>
                <select class="form-control" name="status" required>
                  <option value="Active" <?= ($rows['status'] ?? '') == 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= ($rows['status'] ?? '') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary">Submit</button>
              <a href="manage_gallery.php" class="btn btn-light">Back</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include('../../include/footer.php'); ?>

<script>
  $("#galleryForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      type: "POST",
      url: "add_gallery_post.php",
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        if (res == 0) {
          $("#alert").html('<div class="alert alert-success">Gallery Saved Successfully!</div>');
          $("#galleryForm")[0].reset();
        } else if (res == 1) {
          $("#alert").html('<div class="alert alert-success">Gallery Updated Successfully!</div>');
        } else {
          $("#alert").html('<div class="alert alert-danger">' + res + '</div>');
        }
      }
    });
  });
</script>