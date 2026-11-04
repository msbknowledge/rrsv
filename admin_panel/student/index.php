<!DOCTYPE html>
<html>

<?php
session_start();
include '../dbcon.php'; // ✅ Added connection
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'student') {
  header("Location: ../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];
// 🧠 Fetch events from database
$events = [];
$query = mysqli_query($con, "SELECT * FROM rrsv_event");
while ($row = mysqli_fetch_assoc($query)) {
  $events[] = [
    'title' => $row['event_name'],
    'start' => $row['d_o_e'],
    'description' => $row['event_desc'],
    'doc_path' => $row['doc_path']
  ];
}


?>
<?php include('../header.php'); ?>

<body class="hold-transition skin-blue sidebar-mini">


  <div class="wrapper">
    <!-- Header Start -->
    <header class="main-header">
      <!-- Logo -->
      <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>MSB</b></span>
        <!-- logo for regular state and mobile devices -->
         <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <?php include '../top_navbar.php' ?>
    </header><!-- Header End -->
    <!-- Left side column. contains the logo and sidebar -->

    <!-- Sidebar Start -->
    <?php include '../left_navbar.php' ?>
    <!-- sidebar End -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->

      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Admin
          <small></small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Admin Management</a></li>
          <li class="active">Exam</li>
        </ol>
      </section>
      <!-- Main content -->
      <section class="content">
        <div class="row">
          <!-- Note Start -->
          <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <div class="col-sm-3">
              <div class="alert bg-green">
                <h4> Class Work <i class="pull-right fa fa-users"></i> </h4>
                <span class="d-block"> 500</span>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="alert bg-blue">
                <h4>Teacher’s Comment<i class="pull-right fa fa-graduation-cap"></i></h4>
                <span class="d-block">10</span>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="alert bg-yellow">
                <h4>Exam<i class="pull-right fa fa-key"></i></h4>
                <span class="d-block"> 5</span>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="alert bg-red">
                <h4>Guardian’s Comment<i class="pull-right fa fa-building-o"></i></h4>
                <span class="d-block"> <span class="live-clock">8</span></span>
              </div>
            </div>
          </div>
        </div>
      </div>
          <!-- Note End -->
          <!-- Send sms box start -->
          <div class="col-md-12">
            <div class="box box-primary">
              <!-- /.header-->
              <div class="box-header with-border">
                <h3 class="box-title">📅  Calendar</h3>

                <div style="text-align: right" ;>
                  <h4 class="btn btn-success btn-sm">
                    </h4>
                </div>

              </div>
              <!-- /.header-->


              <div class="box-body">
                  
                  

  <style>
    #calendar {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      min-height: 600px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
  </style>
 <div class="container">
    <h2 class="text-center text-primary"> </h2>
    <div id="calendar"></div>
  </div>

  <!-- ✅ Bootstrap Modal -->
  <div id="eventModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Event Details</h4>
        </div>
        <div class="modal-body">
          <p><strong>Title:</strong> <span id="eventTitle"></span></p>
          <p><strong>Date:</strong> <span id="eventDate"></span></p>
          <p><strong>Description:</strong></p>
          <p id="eventDesc" class="text-muted"></p>
        </div>
      </div>
    </div>
  </div>

<!-- ✅ JS Libraries (in the correct order) -->
 
 <script>
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    // 🧠 PHP to JS: Convert PHP array ($events) into JSON
    var events = '';//<?php echo json_encode($events); ?>;

    // ✅ Initialize Calendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 'auto',
      events: events, // <— dynamic data from database
      eventClick: function (info) {
        $('#eventTitle').text(info.event.title);
        $('#eventDate').text(info.event.start.toLocaleDateString());
        $('#eventDesc').text(info.event.extendedProps.description);

        // if document path exists, show download link
        if (info.event.extendedProps.doc_path) {
          $('#eventDesc').append(
            `<br><a href="${info.event.extendedProps.doc_path}" target="_blank" class="btn btn-sm btn-primary mt-2">
              📄 View Document
            </a>`
          );
        }

        $('#eventModal').modal('show');
      }
    });

    calendar.render();
  });
</script>





                <div class="box-footer">

                  <!-- /.footer-->

                </div>
                <!-- /.box-footer -->

              </div>



              <!-- Send sms box end -->



              <div class="box-body" style="background-color:#E6E6FA">


              </div>

            </div>
            <!-- /.box -->
          </div>
          <!-- template message box end-->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->

      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Footer Start -->
    <?php include '../footer.php' ?>
    <div class="control-sidebar-bg"></div>
  </div>











  <!-- ./wrapper -->
  <!-- jQuery 2.2.3 -->
  <script src="../assets/js/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap 3.3.6 -->
  <script src="../assets/js/bootstrap.min.js"></script>
  <!-- DataTables -->
  <script src="../assets/js/jquery.dataTables.min.js"></script>
  <script src="../assets/js/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="../assets/js/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="../assets/js/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="../assets/js/app.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../assets/js/demo.js"></script>
  <!-- page script -->
  <script>
    $(function () {
      $("#example1").DataTable();
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

 
<!-- Dependent Dropdown Script -->
  <script>
    $(document).ready(function () {
      $('#class_id').on('change', function () {
        var classId = $(this).val();
        if (classId) {
          $.ajax({
            url: 'get_subjects.php',
            type: 'POST',
            data: { class_id: classId },
            success: function (response) {
              $('#subject_id').html(response);
            }
          });
        } else {
          $('#subject_id').html('<option value="">Select Subject</option>');
        }
      });
    });
  </script>


</body>

</html>