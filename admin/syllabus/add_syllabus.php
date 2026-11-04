<?php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = $_GET['id'] ?? null;
$row = [];

if ($id) {
  $id = intval($id);
  $sql = "SELECT * FROM rrsv_syllabus WHERE id=$id";
  $res = mysqli_query($myDB, $sql);
  $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
}

// Fetch classes
$classRes = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class ORDER BY class_name");

// Fetch subjects
$subjectRes = mysqli_query($myDB, "SELECT id, sub_name FROM rrsv_subject ORDER BY sub_name");
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card" style="margin-left: 2px;">
          <div class="card-body">
            <h4 class="card-title"><?= $id ? "Edit Syllabus" : "Add Syllabus" ?></h4>
            <div id="alert"></div>

            <form id="syllabusForm" class="forms-sample" action="add_syllabus_post.php" method="post"
              enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $row['id'] ?? ''; ?>" />
              <input type="hidden" name="token" value="<?= $token; ?>" />
              <div class="form-group row">
                <!-- Session -->
                <div class="col-md-2">
                  <label>Session<span class="text-red"> * </span> </label>
                  <?php $selectedSession = !empty($row['session']) ? $row['session'] : date("Y"); ?>
                  <select class="form-control" name="scl_session" id="scl_session" required>
                    <option value="">-Select-</option>
                    <?php for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) { ?>
                      <option value="<?= $i; ?>" <?= ($selectedSession == $i) ? 'selected' : ''; ?>>
                        <?= $i; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-2">
                  <label>Unit:<span class="text-red"> * </span></label>
                  <select name="unit" class="form-control" required>
                    <option value="">-- Select Unit --</option>
                    <option value="1" <?php if (isset($row['unit']) && $row['unit'] == 1)
                      echo 'selected'; ?>>1st Unit
                    </option>
                    <option value="2" <?php if (isset($row['unit']) && $row['unit'] == 2)
                      echo 'selected'; ?>>2nd Unit
                    </option>
                    <option value="3" <?php if (isset($row['unit']) && $row['unit'] == 3)
                      echo 'selected'; ?>>3rd Unit
                    </option>
                  </select>
                </div>
                <!-- Class -->
                <div class="col-md-2">
                  <label>Class<span class="text-red"> * </span></label>
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
                <div class="col-md-2">
                  <label>Subject<span class="text-red"> * </span></label>
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
                <!-- File -->
                <!-- <div class="col-md-3">
                  <div class="form-group">
                    <label>Syllabus File (PDF)</label>
                    <input type="file" class="form-control" name="syllabus_file" accept=".pdf">
                    <?php if (!empty($row['syllabus_file'])) { ?>
                      <p>Current: <a href="<?= $row['syllabus_file'] ?>" target="_blank">Download</a></p>
                    <?php } ?>
                  </div>
                </div> -->

              </div>


              <!-- Chapters -->
              <h4>Chapters</h4>
              <table id="chapterTable" border="1" cellpadding="5" style="width:100%">
                <tr>
                  <th>Chapter<span class="text-red"> * </span></th>
                  <th>Description<span class="text-red"> * </span></th>
                  <th>Page No.<span class="text-red"> * </span></th>
                  <th><button type="button" class="btn btn-primary" onclick="addRow()">+</button></th>
                </tr>
                <?php
                if ($id) {
                  $chapRes = mysqli_query($myDB, "SELECT * FROM rrsv_syllabus_details WHERE syllabus_id=$id");
                  while ($ch = mysqli_fetch_assoc($chapRes)) {
                    echo "<tr>
              <td><input class='form-control' type='text' name='chapter[]' value='{$ch['chapter']}' required></td>
              <td><input class='form-control' type='text' name='description[]' value='{$ch['description']}' required></td>
              <td><input class='form-control' type='text' name='page_no[]' value='{$ch['page_no']}' required></td>
              <td><button class='form-controlntn btn-danger ' type='button' onclick='removeRow(this)'>x</button></td>
            </tr>";
                  }
                } else {
                  echo "<tr>
            <td><input class='form-control' type='text' name='chapter[]' required></td>
            <td><input class='form-control' type='text' name='description[]'></td>
            <td><input class='form-control' type='text' name='page_no[]'></td>
            <td><button type='button' class='btn btn-danger' onclick='removeRow(this)'>x</button></td>
          </tr>";
                }
                ?>
              </table>






              <button type="submit" class="btn btn-primary me-2">Submit</button>
              <a href="manage_syllabus.php" class="btn btn-light">Back</a>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<?php include('../../include/footer.php'); ?>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
  var quillEn = new Quill('#editor_en', { theme: 'snow' });
  var quillBn = new Quill('#editor_bn', { theme: 'snow' });

  // On form submit, copy HTML content into hidden inputs
  document.getElementById('syllabusForm').onsubmit = function () {
    document.querySelector('input[name=chapter]').value = quillEn.root.innerHTML;
    document.querySelector('input[name=chapter]').value = quillBn.root.innerHTML;
  };
</script>

<script>
  function addRow() {
    let table = document.getElementById("chapterTable");
    let row = table.insertRow(-1);

    row.innerHTML = `
    <td><input class="form-control" type="text" name="chapter[]" required></td>
    <td><input class="form-control" type="text" name="description[]"></td>
    <td><input class="form-control" type="text" name="page_no[]"></td>
    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">x</button></td>
  `;
  }

  function removeRow(btn) {
    let row = btn.closest("tr");
    row.remove();
  }
</script>

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
  $('#syllabusForm').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      type: "POST",
      url: "add_syllabus_post.php",
      data: formData,
      processData: false,
      contentType: false,
      success: function (val) {
        if (val == 0) {
          $('#alert').html('<div class="alert alert-success">Syllabus Saved Successfully!</div>');
          $('#syllabusForm')[0].reset();
        }
        if (val == 1) {
          $('#alert').html('<div class="alert alert-success">Syllabus Updated Successfully!</div>');
        }
        if (val == 2) {
          $('#alert').html('<div class="alert alert-danger">This syllabus already exists!</div>');
        }
      }
    });
  });
</script>