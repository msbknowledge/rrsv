<?php
include('include/header.php');
include('include/dbcon.php');

$rows = [
  'id' => '',
  'scl_session' => '',
  'class_name' => '',
  'name' => '',
  'phone' => '',
  'd_o_i' => '',
  'price' => ''
];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "SELECT * FROM rrsv_inquery WHERE id = $id";
  $res = mysqli_query($myDB, $sql);
  if ($res && mysqli_num_rows($res) > 0) {
    $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
  }
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card" style="margin-left: 188px;">
          <div class="card-body">
            <h4 class="card-title">Admission Form Sell</h4>
            <div id="alert"></div>

            <form class="forms-sample" id="admissionForm" method="post">
              <input type="hidden" name="id" value="<?= $rows['id']; ?>" />
              <input type="hidden" name="token" value="<?php echo $token; ?>" />

              <!-- Admission Type -->
              <div class="form-group">
                <label>Admission Type</label><br>
                <label><input type="radio" name="inquery_type" value="Admission" checked>New Admission</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="inquery_type" value="Readmission"> Readmission</label>
              </div>

              <!-- Readmission Box -->
              <div class="form-group" id="readmissionBox" style="display:none;">
                <label for="reg_no">Enter Student Registration No.</label>
                <div style="display:flex; align-items:center; gap:10px;">
                  <input type="text" class="form-control" id="reg_no" name="reg_no"
                    placeholder="Enter registration no.">
                  <span id="verifyStatus"></span>
                </div>
                <small id="reg_error" style="color:red;"></small>
              </div>

              <!-- Session -->
              <div class="form-group">
                <label>Session<span style="color:red">*</span></label>
                <select name="scl_session" class="form-control" id="scl_session" required>
                  <option value="">-Select Session-</option>
                  <?php
                  for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                    echo '<option value="' . $i . '"' . ($rows['scl_session'] == $i ? ' selected' : '') . '>' . $i . '</option>';
                  }
                  ?>
                </select>
                <span id="error" style="color:red;"></span>
              </div>

              <!-- Class Name -->
              <div class="form-group">
                <label>Class Name <span style="color:red">*</span></label>
                <select name="class_name" class="form-control" id="class_name" required>
                  <option value="">Select Class</option>
                  <?php
                  $sql = "SELECT * FROM rrsv_class ORDER BY id";
                  $res = mysqli_query($myDB, $sql);
                  while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    echo '<option value="' . $obj['class_name'] . '"' .
                      ($rows['class_name'] == $obj['class_name'] ? ' selected' : '') . '>' .
                      $obj['class_name'] . '</option>';
                  }
                  ?>
                </select>
                <span id="error1" style="color:red;"></span>
              </div>

              <!-- Student Name -->
              <div class="form-group">
                <label>Student Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $rows['name']; ?>">
                <span id="error2" style="color:red;"></span>
              </div>

              <!-- Phone -->
              <div class="form-group">
                <label>WhatsApp Phone No</label>
                <input type="number" class="form-control" id="phone" name="phone" value="<?= $rows['phone']; ?>">
                <span id="error3" style="color:red;"></span>
              </div>

              <!-- Date of Sell -->
              <div class="form-group">
                <label>Date of Sell<span style="color:red">*</span></label>
                <input type="date" class="form-control" id="d_o_i" name="d_o_i" value="<?= $rows['d_o_i']; ?>" required>
                <span id="error4" style="color:red;"></span>
              </div>

              <!-- Form Price -->
              <div class="form-group">
                <label>Admission Form Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?= $rows['price']; ?>" readonly>
              </div>

              <button type="submit" class="btn btn-primary me-2">Submit</button>
              <a href="manage_inquery.php"><button type="button" class="btn btn-light">Back</button></a>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('include/footer.php'); ?>

<script>
  $(document).ready(function () {
    let isVerified = false;

    // Admission / Readmission toggle
    $('input[name="inquery_type"]').on('change', function () {
      if ($(this).val() === 'Readmission') {
        $('#readmissionBox').slideDown();
      } else {
        $('#readmissionBox').slideUp();
        $('#reg_no').val('');
        $('#verifyStatus').html('');
        isVerified = false;
      }
    });

    // Fetch fee dynamically
    $('#class_name, #scl_session').change(function () {
      const class_name = $('#class_name').val();
      const scl_session = $('#scl_session').val();
      if (class_name && scl_session) {
        $.post('fee_ajax.php', { class_name, scl_session }, function (data) {
          $('#price').val(data);
        });
      }
    });

    // Verify Registration Number
    // $('#reg_no').on('keyup change', function () {
    //   const reg_no = $(this).val().trim();
    //   if (reg_no.length >= 3) {
    //     $.ajax({
    //       type: "POST",
    //       url: "verify_registration_ajax.php",
    //       data: { reg_no: reg_no },
    //       dataType: "json",
    //       beforeSend: function () {
    //         $('#verifyStatus').html('<i class="fa fa-spinner fa-spin text-info"></i>');
    //       },
    //       success: function (response) {
    //         if (response.status === 'success') {
    //            const d = response.data;
    //           isVerified = true;
    //           $('#verifyStatus').html('✅<i class="fa fa-check text-success"></i>');
    //           $('input[name="scl_name"]').val(d.name);
    //           $('select[name="scl_class"]').val(d.class_name);
    //           $('input[name="scl_phone_no"]').val(d.phone);
    //           $('select[name="scl_session"]').val(d.scl_session);
    //           $('input[name="scl_date"]').val(d.d_o_i);
    //           $('#reg_error').html('');
    //         } else {
    //           isVerified = false;
    //           $('#verifyStatus').html('❌<i class="fa fa-times text-danger"></i>');
    //           $('#reg_error').html(response.message || 'Registration number not found.');
    //         }
    //       },
    //       error: function () {
    //         isVerified = false;
    //         $('#verifyStatus').html('<i class="fa fa-times text-danger"></i>');
    //         $('#reg_error').html('Server error.');
    //       }
    //     });
    //   } else {
    //     $('#verifyStatus').html('');
    //     $('#reg_error').html('');
    //   }
    // });
    // ✅ Verify Registration Number (for Readmission)
    $('#reg_no').on('keyup change', function () {
      const reg_no = $(this).val().trim();
      if (reg_no.length >= 3) {
        $.ajax({
          type: "POST",
          url: "verify_registration_ajax.php",
          data: { reg_no: reg_no },
          dataType: "json",
          beforeSend: function () {
            $('#verifyStatus').html('<i class="fa fa-spinner fa-spin text-info"></i>');
          },
          success: function (response) {
            if (response.status === 'success') {
              const d = response.data;
              isVerified = true;

              // ✅ success icon
              $('#verifyStatus').html('✅<i class="fa fa-check text-success"></i>');

              // ✅ Auto-fill form fields with fetched student data
              $('input[name="name"]').val(d.scl_name);
              // $('select[name="class_name"]').val(d.scl_class).trigger('change');
              $('input[name="phone"]').val(d.scl_phone_no);
              $('select[name="scl_session"]').val('');

              $('#reg_error').html('');
            } else {
              isVerified = false;
              $('#verifyStatus').html('❌<i class="fa fa-times text-danger"></i>');
              $('#reg_error').html(response.message || 'Registration number not found.');
            }
          },
          error: function () {
            isVerified = false;
            $('#verifyStatus').html('<i class="fa fa-times text-danger"></i>');
            $('#reg_error').html('Server error.');
          }
        });
      } else {
        $('#verifyStatus').html('');
        $('#reg_error').html('');
      }
    });

    // Submit handler
    $('#admissionForm').submit(function (e) {
      e.preventDefault();

      if ($('input[name="inquery_type"]:checked').val() === 'Readmission' && !isVerified) {
        $('#reg_error').html('Please verify registration number before submitting.');
        return false;
      }

      $.ajax({
        type: "POST",
        url: "add_inquery_post.php",
        data: $(this).serialize(),
        success: function (val) {
          if (val == 0) {
            $('#alert').html('<div class="alert alert-success">Form Sold Successfully!</div>');
            $('#admissionForm')[0].reset();
            $('#readmissionBox').hide();
          } else if (val == 1) {
            $('#alert').html('<div class="alert alert-success">Form Updated Successfully!</div>');
          } else {
            $('#alert').html('<div class="alert alert-danger">Something went wrong.</div>');
          }
        }
      });
    });
  });
</script>