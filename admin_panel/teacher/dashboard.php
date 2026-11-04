
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
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
    <?php
session_start();
include '../dbcon.php'; // ✅ Added connection
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
  header("Location: ../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];
$teacher_id = $teacher['id'];
$teacher_name = $teacher['full_name'];
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
$sql = "
  SELECT r.*, c.class_name, s.sub_name, sec.section_name
  FROM rrsv_routine r
  JOIN rrsv_class c ON r.class_id = c.id
  JOIN rrsv_subject s ON r.subject_id = s.id
  JOIN rrsv_section sec ON r.section_id = sec.id
  ORDER BY FIELD(r.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), r.start_time
";
$routine = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($routine)) {
  // ✅ Make sure routine_date is valid
  if (!empty($row['routine_date']) && $row['routine_date'] != '0000-00-00') {
    $events[] = [
      'title' => $row['sub_name'] . " (" . $row['class_name'] . "-" . $row['section_name'] . ")",
      'start' => $row['routine_date'] . 'T' . $row['start_time'],
      'end'   => $row['routine_date'] . 'T' . $row['end_time'],
      'description' => "Teacher: " . $row['teacher_name'],
      'color' => '#28a745' // Green color for routine
    ];
  }
}
//print_r(json_encode($events));die();
?>
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
            <h3 class="box-title">📅Calendar</h3>

           
          </div>
          <!-- /.header-->


          <div class="box-body">

            <!-- /box-body-->


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
    <h2 class="text-center text-primary"></h2>
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
  
  
  
  <div id="routineModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Routine</h4>
      </div>

      <div class="modal-body">
        <form id="routineForm">
          <input type="hidden" name="routine_date" id="routine_date">

          <div class="row">
            <div class="col-md-6">
             <!-- Class -->
              <div class="form-group">
                <label>Class <span class="text-danger">*</span></label>
                <select name="class_id" class="form-control" id="class_id">
                  <option value="">-Select a Class-</option>
                  <?php
                  $sql = "select * from rrsv_class order by id";
                  $res = mysqli_query($con, $sql);
                  while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    ?>
                    <option value="<?php echo $obj['id']; ?>">
                      <?php echo $obj['class_name']; ?>
                    </option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <!-- Section -->
              <div class="form-group">
                <label>Section <span class="text-danger">*</span></label>
                <select class="form-control" name="section_id" id="section_id" required>

                                <option value="">-Select a Section-</option>
                                <?php
                                $id = 0;
                                $sql = "select * from  rrsv_section group by section_name  order by id ";
                                $res = mysqli_query($con, $sql);
                                while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                                    ?>
                                    <option value="<?php echo $obj['id']; ?>">
                                        <?php echo $obj['section_name']; ?>
                                    </option>
                                    <?php
                                }
                                ?>

                            </select>
              </div>

              <!-- Subject -->
              <div class="form-group">
                <label>Subject <span class="text-danger">*</span></label>
               <select class="form-control" name="subject_id" id="subject_id" required>
                          <option value="">Select Subject</option>
                        </select>
              </div>


              <div class="form-group">
                <label>Teacher ID</label>
                <input type="text" name="teacher_id" class="form-control"
                  value="<?php echo $teacher_id; ?>" readonly>
              </div>

             
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="start_time" class="form-control">
              </div>

              <div class="form-group">
                <label>End Time</label>
                <input type="time" name="end_time" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Session</label>
                <input type="text" name="session" value="2025" class="form-control" required>
              </div>

              <!--<div class="form-group">-->
              <!--  <label>Is Break</label>-->
              <!--  <select name="is_break" class="form-control">-->
              <!--    <option value="0">Class</option>-->
              <!--    <option value="1">Break</option>-->
              <!--  </select>-->
              <!--</div>-->
               <div class="form-group">
                <label>Teacher Name</label>
                <input type="text" name="teacher_name" class="form-control"
                  value="<?php echo $teacher_name; ?>" readonly>
              </div>

              <!--<div class="form-group">-->
              <!--  <label>Subject Name</label>-->
              <!--  <input type="text" name="sub_name" class="form-control">-->
              <!--</div>-->
            </div>
          </div>

          <div class="form-group text-center" style="margin-top: 15px;">
            <button type="submit" class="btn btn-success">💾 Save Routine</button>
            <button type="button" id="closeModal" class="btn btn-default" data-dismiss="modal">❌ Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- ✅ JS Libraries (in the correct order) -->
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>-->
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>-->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var events = <?php echo json_encode($events); ?>;

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    events: events,

    // 🟢 When a date is clicked — open Add Routine modal
    dateClick: function(info) {
      $('#routine_date').val(info.dateStr);
      $('#routineModal').modal('show'); // ✅ Proper Bootstrap modal show
    },

    // 🟢 When an event (routine or event) is clicked — show details
    eventClick: function(info) {
      var desc = info.event.extendedProps.description || '';
      var file = info.event.extendedProps.doc_path || '';

      $('#eventTitle').text(info.event.title);
      $('#eventDate').text(info.event.start.toLocaleDateString());
      $('#eventDesc').text(desc);

      if (file) {
        $('#eventDesc').append(`<br><a href="${file}" target="_blank" class="btn btn-primary btn-sm mt-2">📄 View Document</a>`);
      }

      $('#eventModal').modal('show'); // ✅ Use your existing event modal
    }
  });

  // 🟢 Render calendar
  calendar.render();

  // 🟢 Add Routine form submit
  $('#routineForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
      url: 'add_routine.php',
      method: 'POST',
      data: $(this).serialize(),
      success: function(response) {
        alert(response);

        // ✅ Close modal after save
        $('#routineModal').modal('hide');

        // ✅ Refresh the calendar to load new routine
        calendar.refetchEvents();
      },
      error: function() {
        alert("❌ Something went wrong. Please try again.");
      }
    });
  });

  // 🟢 Close button handler (for Cancel)
  $('#closeModal').on('click', function() {
    $('#routineModal').modal('hide');
  });
});
</script>


<script>



/* Meme */

var memes = [
  'Dude, you smashed my turtle saying "I\'M MARIO BROS!"',
  'Dude, you grabed seven oranges and yelled "I GOT THE DRAGON BALLS!"',
  'Dude, you threw my hamster across the room and said "PIKACHU I CHOOSE YOU!"',
  'Dude, you congratulated a potato for getting a part in Toy Story',
  'Dude, you were hugging an old man with a beard screaming "DUMBLEDORE YOU\'RE ALIVE!"',
  'Dude, you were cutting all my pinapples yelling "SPONGEBOB! I KNOW YOU\'RE THERE!"',
];

var random = document.querySelector('#random');

random.innerHTML = memes[Math.floor(Math.random() * memes.length)];

/* Time */

var deviceTime = document.querySelector('.status-bar .time');
var messageTime = document.querySelectorAll('.message .time');

deviceTime.innerHTML = moment().format('h:mm');

setInterval(function() {
  deviceTime.innerHTML = moment().format('h:mm');
}, 1000);

for (var i = 0; i < messageTime.length; i++) {
  messageTime[i].innerHTML = moment().format('h:mm A');
}

/* Message */

var form = document.querySelector('.conversation-compose');
var conversation = document.querySelector('.conversation-container');

form.addEventListener('submit', newMessage);

function newMessage(e) {
  var input = e.target.input;

  if (input.value) {
    var message = buildMessage(input.value);
    conversation.appendChild(message);
    animateMessage(message);
  }

  input.value = '';
  conversation.scrollTop = conversation.scrollHeight;

  e.preventDefault();
}

function buildMessage(text) {
  var element = document.createElement('div');

  element.classList.add('message', 'sent');

  element.innerHTML = text +
    '<span class="metadata">' +
      '<span class="time">' + moment().format('h:mm A') + '</span>' +
      '<span class="tick tick-animation">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck" x="2047" y="2061"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#92a58c"/></svg>' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"/></svg>' +
      '</span>' +
    '</span>';

  return element;
}

function animateMessage(message) {
  setTimeout(function() {
    var tick = message.querySelector('.tick');
    tick.classList.remove('tick-animation');
  }, 500);
}
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
      
      




<script>
function openForm() {
  document.getElementById("myForm").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
}
</script>

    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
