<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = 0;
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "SELECT * FROM rrsv_routine WHERE id=$id";
  $res = mysqli_query($myDB, $sql);
  $row = mysqli_fetch_assoc($res);
}
// Fetch classes
$classRes = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class ORDER BY class_name");

// Fetch subjects
$subjectRes = mysqli_query($myDB, "SELECT id, sub_name FROM rrsv_subject ORDER BY sub_name");
// Fetch classes, sections, subjects for dropdowns
$classes = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class");
$sections = mysqli_query($myDB, "SELECT id, section_name FROM rrsv_section");
$subjects = mysqli_query($myDB, "SELECT id, sub_name FROM rrsv_subject");
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card" style="margin-left: 188px;">
          <div class="card-body">
            <h4 class="card-title"><?= $id ? "Edit Routine" : "Add Routine" ?></h4>
            <div id="alert"></div>
            <form class="forms-sample" method="post">
              <input type="hidden" name="id" value="<?= $row['id'] ?? 0 ?>" />
              <input type="hidden" name="token" value="<?= $_SESSION['_token'] ?>" />
              <!-- Session -->
              <div class="form-group">
                <label>Session <span class="text-red"> * </span></label>
                <?php $selectedSession = !empty($row['session']) ? $row['session'] : date("Y"); ?>
                <select class="form-control" name="scl_session" id="scl_session" required>
                  <option value="">-Select a Session-</option>
                  <?php for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) { ?>
                    <option value="<?= $i; ?>" <?= ($selectedSession == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                  <?php } ?>
                </select>
              </div>
              <!-- Class -->
              <div class="form-group">
                <label>Class <span class="text-red"> * </span></label>
                <select class="form-control" name="class_id" id="class_id" required>
                  <option value="">Select Class</option>
                  <?php while ($c = mysqli_fetch_assoc($classRes)) { ?>
                    <option value="<?= $c['id'] ?>" <?= ($row['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                      <?= $c['class_name'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <!-- Subject -->
              <div class="form-group">
                <label>Subject <span class="text-red"> * </span></label>
                <select class="form-control" name="subject_id" id="subject_id" required>
                  <option value="">Select Subject</option>
                  <?php
                  if (!empty($row['subject_id'])) {
                    $subRes2 = mysqli_query($myDB, "SELECT id, sub_name FROM rrsv_subject WHERE id = " . $row['subject_id']);
                    if ($subRow = mysqli_fetch_assoc($subRes2)) {
                      echo "<option value='{$subRow['id']}' selected>{$subRow['sub_name']}</option>";
                    }
                  }
                  ?>
                </select>
              </div>

              <!-- Section -->
              <div class="form-group">
                <label>Section<span class="text-red"> * </span></label>
                <select class="form-control" name="section_id" id="section_id" required>
                  <option value="">Select Section</option>
                  <?php
                  if (!empty($row['section_id'])) {
                    $secRes2 = mysqli_query($myDB, "SELECT id, section_name FROM rrsv_section WHERE id = " . $row['section_id']);
                    if ($secRow = mysqli_fetch_assoc($secRes2)) {
                      echo "<option value='{$secRow['id']}' selected>{$secRow['section_name']}</option>";
                    }
                  }
                  ?>
                </select>
              </div>

              
              <div class="form-group">
                <label>Day of Week<span class="text-red"> * </span></label>
                <select class="form-control" name="day_of_week" id="day_of_week" required>
                  <option value="">Select Day</option>
                  <?php foreach ($days as $d): ?>
                    <option value="<?= $d ?>" <?= (isset($row['day_of_week']) && $row['day_of_week'] == $d) ? 'selected' : '' ?>>
                      <?= $d ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group row">
                <div class="col-sm-12">
                  <label>Teacher Name</label>
                  <select name="teacher_id" id="teacher_id" class="form-control minput">
                    <option value="">--Select Teacher Name--</option>
                    <?php
                    $sql = "SELECT id, full_name FROM rrsv_teacher";
                    $res = mysqli_query($myDB, $sql);
                    while ($teacher = mysqli_fetch_assoc($res)) {
                      $selected = (isset($row['teacher_id']) && $row['teacher_id'] == $teacher['id']) ? 'selected' : '';
                      echo '<option value="' . $teacher['id'] . '" ' . $selected . '>' . $teacher['full_name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- <div class="form-group">
                <label>Teacher Name</label>
                <input type="text" class="form-control" name="teacher_name" id="teacher_name" placeholder="Teacher Name"
                  value="<?= $row['teacher_name'] ?? '' ?>">
              </div> -->

              <div class="form-group">
                <label>Start Time<span class="text-red"> * </span></label>
                <input type="time" class="form-control" name="start_time" id="start_time"
                  value="<?= $row['start_time'] ?? '' ?>" required>
              </div>

              <div class="form-group">
                <label>End Time<span class="text-red"> * </span></label>
                <input type="time" class="form-control" name="end_time" id="end_time"
                  value="<?= $row['end_time'] ?? '' ?>" required>
              </div>

              <button type="submit" class="btn btn-primary me-2">Submit</button>
              <a href="manage_routine.php"><button type="button" class="btn btn-light">Back</button></a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include('../../include/footer.php'); ?>
  <script>
    $(document).ready(function () {
      // when class changes
      $('#class_id').on('change', function () {
        var classId = $(this).val();

        if (classId !== '') {
          $.ajax({
            url: 'get_options.php',
            type: 'POST',
            data: { class_id: classId },
            dataType: 'json',
            success: function (response) {
              // Reset and load Subjects
              $('#subject_id').html('<option value="">Select Subject</option>');
              $.each(response.subjects, function (i, sub) {
                $('#subject_id').append('<option value="' + sub.id + '">' + sub.sub_name + '</option>');
              });

              // Reset and load Sections
              $('#section_id').html('<option value="">Select Section</option>');
              $.each(response.sections, function (i, sec) {
                $('#section_id').append('<option value="' + sec.id + '">' + sec.section_name + '</option>');
              });
            }
          });
        } else {
          $('#subject_id').html('<option value="">Select Subject</option>');
          $('#section_id').html('<option value="">Select Section</option>');
        }
      });
    });
  </script>
  <script>
    $('form').submit(function (e) {
      e.preventDefault();
      let class_id = $('#class_id').val();
      let section_id = $('#section_id').val();
      let subject_id = $('#subject_id').val();
      let day_of_week = $('#day_of_week').val();
      let start_time = $('#start_time').val();
      let end_time = $('#end_time').val();

      if (!class_id || !section_id || !subject_id || !day_of_week || !start_time || !end_time) {
        alert('Please fill all required fields!');
        return false;
      }

      $.ajax({
        type: "POST",
        url: "add_routine_post.php",
        data: $('form').serialize(),
        success: function (response) {
          if (response == 0) {
            $('#alert').html('<div class="alert alert-success">Routine added successfully!</div>');
            $('form')[0].reset();
          } else if (response == 1) {
            $('#alert').html('<div class="alert alert-success">Routine updated successfully!</div>');
          } else if (response == 2) {
            $('#alert').html('<div class="alert alert-danger">Conflict: Routine overlaps with existing entry!</div>');
          } else {
            $('#alert').html('<div class="alert alert-danger">Something went wrong!</div>');
          }
        }
      });
    });
  </script>