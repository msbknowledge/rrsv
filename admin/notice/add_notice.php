<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = 0;
$rows = [];
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "SELECT * FROM rrsv_notice WHERE id = $id";
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
            <h4 class="card-title"><?= $id ? "Edit notice/Notice" : "Add notice/Notice"; ?></h4>
            <div id="alert"></div>
            <form class="forms-sample" id="noticeForm" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $rows['id'] ?? 0; ?>" />
              <input type="hidden" name="token" value="<?php echo $_SESSION['_token']; ?>" />

              <div class="form-group">
                <label>Title<span class="text-red"> * </span></label>
                <input type="text" class="form-control" name="title" value="<?= $rows['title'] ?? ''; ?>" required>
              </div>

              <div class="form-group">
                <label>Description<span class="text-red"> * </span></label>
                <textarea class="form-control" name="description" rows="3" required><?= $rows['description'] ?? ''; ?></textarea>
              </div>

              <!-- <div class="form-group">
                <label>Notice File</label>
                <input type="file" class="form-control" name="notice_file">
                <?php if (!empty($rows['notice_file'])): ?>
                  <a href="<?= BASE_URL . $rows['notice_file']; ?>" target="_blank">View File</a>
                <?php endif; ?>
              </div> -->

              <div class="form-group">
                <label>Notice Date<span class="text-red"> * </span></label>
                <input type="date" class="form-control" name="notice_date"
                  value="<?= $rows['notice_date'] ?? date('Y-m-d'); ?>" required>
              </div>

              <div class="form-group">
                <label>Status<span class="text-red"> * </span></label>
                <select class="form-control" name="status" required>
                  <option value="Active" <?= ($rows['status'] ?? '') == 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= ($rows['status'] ?? '') == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary">Submit</button>
              <a href="manage_notice.php" class="btn btn-light">Back</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include('../../include/footer.php'); ?>

<script>
  $("#noticeForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      type: "POST",
      url: "add_notice_post.php",
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        if (res == 0) {
          $("#alert").html('<div class="alert alert-success">notice Saved Successfully!</div>');
          $("#noticeForm")[0].reset();
        } else if (res == 1) {
          $("#alert").html('<div class="alert alert-success">notice Updated Successfully!</div>');
        } else {
          $("#alert").html('<div class="alert alert-danger">' + res + '</div>');
        }
      }
    });
  });
</script>