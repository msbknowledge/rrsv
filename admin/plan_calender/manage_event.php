<?php
error_reporting(1);
include('../../include/header.php');
include('../../include/dbcon.php');
if (isset($_GET['id'])) {
  $id = $myDB->escape_string($_GET['id']);
}

if (isset($_POST['scl_session'])) {
  $scl_session = $myDB->escape_string(trim(addslashes($_POST['scl_session'])));
}
if (isset($_POST['class_name'])) {
  $class_name = $myDB->escape_string(trim(addslashes($_POST['class_name'])));
}
if (isset($_POST['txtsearch'])) {
  $txtsearch = $myDB->escape_string(trim(addslashes($_POST['txtsearch'])));
}
if ($retcode == 3) {
  $msg = "subject has been deleted successfully";
}
if ($id > 0) {

  $sql = "Delete from rrsv_calender_plan where id='" . $id . "'";
  $res = mysqli_query($myDB, $sql);
  if ($res) {
    echo '<script language="javascript" type="text/javascript">';
    echo 'window.location.href="manage_event.php?retcode=3";';
    echo '</script>';
  }
}

if (isset($_GET['status_id']) && isset($_GET['status_val'])) {
    $status_id = $myDB->escape_string($_GET['status_id']);
    $status_val = $myDB->escape_string($_GET['status_val']);

    mysqli_query($myDB, "UPDATE rrsv_calender_plan SET status='$status_val' WHERE id='$status_id'");

    echo "<script>window.location='manage_event.php';</script>";
}
?>
<script language="javascript" type="text/javascript">



  function confirmdel(id) {
    if (confirm("Are you sure to delecte this Information?")) {
      window.location.href = "manage_event.php?id=" + id;
      return true;
    }
  }
</script>
<style>
  .button.btn.btn-primary.button {
    margin-left: 541px;
    margin-top: -71px;
  }

  .addstatus {
    margin-top: -43px;
  }
</style>
 <!-- jQuery already loaded -->
<link href='/libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="card">
      <div class="card-body">
        <h4 class="card-title"> <a href='add_event.php'><button type="button"
              class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add Plan
              Events</button></a></h4>
      </div>
    </div>


    <div class="col-12 grid-margin">
      <div class="card">
          <!-- Table -->
         <div class="card-body">

			<div class="panel-body table-responsive">
			<table id="itemTable" class="table table-striped table-bordered">
              <thead>
          
          <tr>
            <th>SL.No</th>

            <th>Event Title</th>
            <th>Date of Event</th>
            <th>Date of Reminder</th>
            <th>Details</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          </thead>
          <?php
          $bgcolor = "";
          $c = 0;


          if (isset($_GET['page'])) {
            $page = $_GET['page'];
          } else {
            $page = 1;
          }
          $perpage = 100;
          $lowerlimit = ($page - 1) * $perpage;

          $sql = "select * from rrsv_calender_plan where 1 ";
          //$sql="select a.*,b.class_name from rrsv_student_registration as a,class as b where a.scl_class=b.id ";
          
          if ($txtsearch != "") {
            $sql .= " and (title ='" . $txtsearch . "' )";

          }

          /*if($luid!='1')
          {
          $sql.=" and (created_user='$luid' or updated_user='$luid')";
          }*/

          $result = mysqli_query($myDB, $sql);
          $totalrecord = mysqli_num_rows($result);
          $totalpage = ceil($totalrecord / $perpage);
          $sql .= "  order by id desc limit $lowerlimit,$perpage";
          $result1 = mysqli_query($myDB, $sql);
          $l = mysqli_num_rows($result1);
          $result = mysqli_query($myDB, $sql);

          if ($l > 0) {

            while ($rows = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {


              $c++;
              $id = $rows['id'];
              $status = $rows['status'];
              ?>

              <tr>
                <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c; ?></td>


                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['title']; ?></td>
                <td class="text" valign="center" style="padding-left:10px;"><?= date('d-m-Y', strtotime($rows['plan_date'])); ?>
                </td>
                 <td class="text" valign="center" style="padding-left:10px;"><?= date('d-m-Y', strtotime($rows['reminder_date'])); ?>
                </td>
                <td class="text" valign="center" style="padding-left:10px;width: 321px;"><?= $rows['description']; ?></td>
                <td class="text" valign="center">
                     <?php if ($rows['status'] == 1) { ?>
    <span class="badge badge-success">Active</span>
  <?php } else { ?>
    <span class="badge badge-danger">Inactive</span>
  <?php } ?>
                </td>


                <td class="text" valign="center" style="padding-left: -50px;">
  <!--                   <?php if ($rows['status'] == 1) { ?>-->
  <!--  <a href="manage_event.php?status_id=<?= $rows['id']; ?>&status_val=0"-->
  <!--     class="btn btn-danger btn-sm">Deactivate</a>-->
  <!--<?php } else { ?>-->
  <!--  <a href="manage_event.php?status_id=<?= $rows['id']; ?>&status_val=1"-->
  <!--     class="btn btn-success btn-sm">Activate</a>-->
  <!--<?php } ?>-->

                  <a class="btn btn-primary btn-sm" href="add_event.php?id=<?= $rows['id']; ?>"
                    title="Click to Student Events Edit" class="btn btn-primary">Edit
                  </a>

                  <a href="#" onclick="confirmdel(<?= $rows['id']; ?>);" title="Click to delete Student Events"
                    class="btn btn-danger btn-sm">Delete</a>
                  <!--<a class="btn btn-primary" href="event.php?id=<?= $rows['id']; ?>" title="Click to Student Events"-->
                  <!--  class="btn btn-primary">Event Print-->
                  <!--</a>-->
                  


                </td>
              </tr>

              <?php

            }
          } else {
            echo "<tr>";
            echo "<td class='errtext' align='center' colspan=6>Records Not Found</td>";
            echo "</tr>";
          }

          ?>


          </table>
            </div>
        </div>
          <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
              <li class="page-item disabled">
                <?php

                if ($page > 1) {
                  ?>
                <li class="page-item"> <a class="page-link" href="manage_event.php?page=<?= ($page - 1); ?>"
                    tabindex="-1">Previous</a>
                </li>
                <?php
                }
                ?>
              <?php
              for ($i = 1; $i <= $totalpage; $i++) {
                ?>
                <li class="page-item"><a class="page-link" href="manage_event.php?page=<?= $i; ?>"><?php echo $i ?></a>
                </li>
                <?php
              }
              ?>

              <?php
              if ($totalpage > $page) {
                ?>
                <li class="page-item">
                  <a class="page-link" href="manage_event.php?page=<?= ($page + 1); ?>">Next</a>
                </li>
                <?php
              }

              ?>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
<script>
$(document).ready(function () {
    $('#itemTable').DataTable({
        paging: true,       // pagination
        searching: true,    // search box
        ordering: true,     // column sorting
        info: true,         // "Showing X of Y"
        lengthChange: true, // page length dropdown
        pageLength: 10,     // default rows per page
        order: [[0, 'desc']] // sort by ID desc
    });
});
</script>	
  <script>
      
    $(document).ready(function () {
      $('#frmsearch').on('submit', function () {
        var fromDate = $('#fromDate').val();
        var endDate = $('#endDate').val();
        if (fromDate != '' && endDate == '') {
          alert('Input Enddate');
          return false;
        }

      });
    });

  </script>

<?php include('../../include/footer.php'); ?>

  