<?php
include('../../include/header.php');
include('../../include/dbcon.php');
$id = 0;
$title = "";
$description = "";
$plan_date = "";
$reminder_date = "";

if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
  $sql = "select * from rrsv_calender_plan where id=$id";
  $res = mysqli_query($myDB, $sql);
  $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
  $title = $rows["title"];
  $description = $rows["description"];
  $plan_date = $rows["plan_date"];
  $reminder_date  = $rows["reminder_date"];
//   print_r($rows);
//   die;
}
?>
<!-- partial -->
<div class="main-panel">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title"> <a href='manage_event.php'><button type="button"
            class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
    </div>
  </div>
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card" style="margin-left: 188px;">
          <div class="card-body" style="
    height: 680PX;
">
            <h4 class="card-title">Add School Events</h4>
            <p class="card-description">
            <div id="alert"></div>
            </p>
            <form class="forms-sample" action='add_event_post.php' method='post' onsubmit="return validateForm()"
              enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $id; ?>" />
              <input type="hidden" name="token" value="<?= $_SESSION['_token']; ?>" />

              <div class="form-group">
                <label for="exampleInputUsername1">Event Title </label>
                <input type="text" class="form-control" id="title" placeholder="Event Title" name="title"
                  value='<?= $title ?>'>
                <span id="error" class="err" style="color:red;"></span>
              </div>
              
              <div class="form-group">
                <label for="exampleInputUsername1">Date of Event</label>
                <input type="date" class="form-control" id="plan_date" placeholder="Date of Event" name="plan_date"
                  value='<?= $plan_date; ?>'>
                <span id="error1" class="err" style="color:red;"></span>
              </div>
              <div class="form-group">
                <label for="exampleInputUsername1">Date of reminder</label>
                <input type="date" class="form-control" id="reminder_date" placeholder="Date of reminder" name="reminder_date"
                  value='<?= $reminder_date; ?>'>
                <span id="error1" class="err" style="color:red;"></span>
              </div>
              
              <div class="form-group">
                <label for="exampleInputUsername1">Event Details</label>
                <textarea id="description" name="description" class="form-control" style=" height: 199px;">
                     <?= $description; ?>
                </textarea>
                <span id="error2" style="color:red;"></span>
              </div>

              <button type="submit" class="btn btn-primary me-2">Submit</button>

            </form>
            <a href="manage_event.php"><button class="btn btn-light"
                style="margin-left: 97px;margin-top: -68px;">Back</button></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
  include('../../include/footer.php');
  ?>
  <script>
    function validateForm() {
      // alert(11);
      $('.err').html('');
      var title = $('#title').val();
      if (title == '') {
        $('#error').html('Please Enter Event  Name.');
        return false;
      }
      var plan_date = $('#plan_date').val();
      if (plan_date == '') {
        $('#error1').html('Please Enter Date of Event.');
        return false;
      }
      $('form')[0].submit();
      //return false;
    }

  </script>