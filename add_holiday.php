<?php
include('include/header.php');
include('include/dbcon.php');

if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
  $sql = "select * from rrsv_holiday where id=$id";
  $res = mysqli_query($myDB, $sql);
  $rows = mysqli_fetch_array($res, MYSQLI_ASSOC);
}
?>
<!-- partial -->
<div class="main-panel">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title"> <a href='manage_holiday.php'><button type="button"
            class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
    </div>
  </div>
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card" style="margin-left: 188px;">
          <div class="card-body">
            <h4 class="card-title">Add Holiday</h4>
            <p class="card-description">
            <div id="alert"></div>
            </p>
            <form class="forms-sample" action='' method='' name="holiday">
              <input type="hidden" name="id" value="<?= $rows['id']; ?>" />
              <input type="hidden" name="token" value="<?php echo $token; ?>" />

              <div class="form-group">
                <label for="exampleInputUsername1">Holiday Name</label>
                <input type="text" class="form-control" id="name" placeholder="Holiday  Name" name="name"
                  value='<?= $rows['name']; ?>'>
                <span id="error" style="color:red;"></span>
              </div>
              <div class="form-group">
                <label for="exampleInputUsername1">Date of Holiday</label>
                <input type="date" class="form-control" id="d_o_h" placeholder="Holiday  Date of Holoday" name="d_o_h"
                  value='<?= $rows['d_o_h']; ?>'>
                <span id="error1" style="color:red;"></span>
              </div>
              <div class="form-group">
                <label for="exampleInputUsername1">Date of Holiday</label>
                <input type="date" class="form-control" id="end_date" placeholder="Holiday  Date of Holoday"
                  name="end_date" value='<?= $rows['end_date']; ?>'>
                <span id="error1" style="color:red;"></span>
              </div>
              <button type="submit" class="btn btn-primary me-2">Submit</button>

            </form>
            <a href="manage_holiday.php"><button class="btn btn-light"
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
    $('form').submit(function (e) {
      e.preventDefault();
      var name = $('#name').val();
      if (name == '') {
        $('#error').html('Please Enter Holiday  Name.');
        return false;
      }
      var d_o_h = $('#d_o_h').val();
      if (d_o_h == '') {
        $('#error1').html('Please Enter Date of Holiday.');
        return false;
      }
      ajax_fun();

    });


    function ajax_fun() {

      $.ajax({
        type: "POST",
        url: "add_holiday_post.php",
        data: $('form').serialize(),
        success: function (val) {

          if (val == 0) {
            $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Holiday Add Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            $('#error').html('');
            $('#name').val("");
          }
          if (val == 1) {

            $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Holiday Edit Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
          }
          // if(val == 2) {
          //     $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>The Class Name User has given is already Saved!!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
          // }
        }
      });

    }    
  </script>