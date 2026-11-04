<?php
include('include/header.php');
include('include/dbcon.php');
$id = 0;
$event_name = "";
$event_desc = "";
$d_o_e = "";

if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
  $sql = "select * from rrsv_event where id=$id";
  $res = mysqli_query($myDB, $sql);
  $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
  $event_name = $rows["event_name"];
  $event_desc = $rows["event_desc"];
  $d_o_e = $rows["d_o_e"];
  // print_r($rows);
  // die;
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
    height: 580PX;
">
            <h4 class="card-title">Add School Events</h4>
            <p class="card-description">
            <div id="alert"></div>
            </p>
            <form class="forms-sample" action='add_event_post.php' method='post' onsubmit="return validateForm()"
              enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $id; ?>" />
              <input type="hidden" name="token" value="" />

              <div class="form-group">
                <label for="exampleInputUsername1">Event Title </label>
                <input type="text" class="form-control" id="event_name" placeholder="Event Title" name="event_name"
                  value='<?= $event_name ?>'>
                <span id="error" class="err" style="color:red;"></span>
              </div>
              <div class="form-group">
                <label for="exampleInputUsername1">Date of Event</label>
                <input type="date" class="form-control" id="d_o_e" placeholder="Date of Event" name="d_o_e"
                  value='<?= $d_o_e; ?>'>
                <span id="error1" class="err" style="color:red;"></span>
              </div>
              <div class="form-group">
                <label for="exampleInputUsername1">Attachment</label>
                <input type="file" class="form-control" id="file" placeholder="Date of Expence" name="file" value=''>
                <span id="error1" style="color:red;"></span>
              </div>
              <div class="form-group">
                <label for="exampleInputUsername1">Event Details</label>
                <textarea id="event_desc" name="event_desc" class="form-control" style="
    height: 199px;
">
<?= $event_desc; ?>
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
  include('include/footer.php');
  ?>
  <script>
    function validateForm() {
      // alert(11);
      $('.err').html('');

      var event_name = $('#event_name').val();
      if (event_name == '') {
        $('#error').html('Please Enter Event  Name.');
        return false;
      }
      var d_o_e = $('#d_o_e').val();
      if (d_o_e == '') {
        $('#error1').html('Please Enter Date of Event.');
        return false;
      }
      $('form')[0].submit();
      //return false;
    }

  </script>