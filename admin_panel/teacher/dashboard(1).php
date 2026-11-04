
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
      
      




<button class="open-button" onclick="openForm()">Chat</button>
<style>
*, *:before, *:after {
  box-sizing: inherit;
}

html {
  box-sizing: border-box;
  height: 100%;
  margin: 0;
  padding: 0;
}

body {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-family: "Roboto", sans-serif;
  margin: 0;
  padding: 0;
  height: 100%;
}

.page {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.marvel-device .screen {
  text-align: left;
}

.screen-container {
  height: 100%;
}

/* Status Bar */

.status-bar {
  height: 25px;
  background: #004e45;
  color: #fff;
  font-size: 14px;
  padding: 0 8px;
}

.status-bar:after {
  content: "";
  display: table;
  clear: both;
}

.status-bar div {
  float: right;
  position: relative;
  top: 50%;
  transform: translateY(-50%);
  margin: 0 0 0 8px;
  font-weight: 600;
}

/* Chat */

.chat {
  height: calc(100% - 69px);
}

.chat-container {
  height: 100%;
}

/* User Bar */

.user-bar {
  height: 55px;
  background: #005e54;
  color: #fff;
  padding: 0 8px;
  font-size: 24px;
  position: relative;
  z-index: 1;
}

.user-bar:after {
  content: "";
  display: table;
  clear: both;
}

.user-bar div {
  float: left;
  transform: translateY(-50%);
  position: relative;
  top: 50%;
}

.user-bar .actions {
  float: right;
  margin: 0 0 0 20px;
}

.user-bar .actions.more {
  margin: 0 12px 0 32px;
}

.user-bar .actions.attachment {
  margin: 0 0 0 30px;
}

.user-bar .actions.attachment i {
  display: block;
  transform: rotate(-45deg);
}

.user-bar .avatar {
  margin: 0 0 0 5px;
  width: 36px;
  height: 36px;
}

.user-bar .avatar img {
  border-radius: 50%;
  box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1);
  display: block;
  width: 100%;
}

.user-bar .name {
  font-size: 17px;
  font-weight: 600;
  text-overflow: ellipsis;
  letter-spacing: 0.3px;
  margin: 0 0 0 8px;
  overflow: hidden;
  white-space: nowrap;
  width: 110px;
}

.user-bar .status {
  display: block;
  font-size: 13px;
  font-weight: 400;
  letter-spacing: 0;
}

/* Conversation */

.conversation {
  height: calc(100% - 12px);
  position: relative;
  background: #efe7dd url("https://cloud.githubusercontent.com/assets/398893/15136779/4e765036-1639-11e6-9201-67e728e86f39.jpg") repeat;
  z-index: 0;
}

.conversation ::-webkit-scrollbar {
  transition: all .5s;
  width: 5px;
  height: 1px;
  z-index: 10;
}

.conversation ::-webkit-scrollbar-track {
  background: transparent;
}

.conversation ::-webkit-scrollbar-thumb {
  background: #b3ada7;
}

.conversation .conversation-container {
  height: calc(100% - 68px);
  box-shadow: inset 0 10px 10px -10px #000000;
  overflow-x: hidden;
  padding: 0 16px;
  margin-bottom: 5px;
}

.conversation .conversation-container:after {
  content: "";
  display: table;
  clear: both;
}

/* Messages */

.message {
  color: #000;
  clear: both;
  line-height: 18px;
  font-size: 15px;
  padding: 8px;
  position: relative;
  margin: 8px 0;
  max-width: 85%;
  word-wrap: break-word;
  z-index: -1;
}

.message:after {
  position: absolute;
  content: "";
  width: 0;
  height: 0;
  border-style: solid;
}

.metadata {
  display: inline-block;
  float: right;
  padding: 0 0 0 7px;
  position: relative;
  bottom: -4px;
}

.metadata .time {
  color: rgba(0, 0, 0, .45);
  font-size: 11px;
  display: inline-block;
}

.metadata .tick {
  display: inline-block;
  margin-left: 2px;
  position: relative;
  top: 4px;
  height: 16px;
  width: 16px;
}

.metadata .tick svg {
  position: absolute;
  transition: .5s ease-in-out;
}

.metadata .tick svg:first-child {
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  -webkit-transform: perspective(800px) rotateY(180deg);
          transform: perspective(800px) rotateY(180deg);
}

.metadata .tick svg:last-child {
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  -webkit-transform: perspective(800px) rotateY(0deg);
          transform: perspective(800px) rotateY(0deg);
}

.metadata .tick-animation svg:first-child {
  -webkit-transform: perspective(800px) rotateY(0);
          transform: perspective(800px) rotateY(0);
}

.metadata .tick-animation svg:last-child {
  -webkit-transform: perspective(800px) rotateY(-179.9deg);
          transform: perspective(800px) rotateY(-179.9deg);
}

.message:first-child {
  margin: 16px 0 8px;
}

.message.received {
  background: #fff;
  border-radius: 0px 5px 5px 5px;
  float: left;
}

.message.received .metadata {
  padding: 0 0 0 16px;
}

.message.received:after {
  border-width: 0px 10px 10px 0;
  border-color: transparent #fff transparent transparent;
  top: 0;
  left: -10px;
}

.message.sent {
  background: #e1ffc7;
  border-radius: 5px 0px 5px 5px;
  float: right;
}

.message.sent:after {
  border-width: 0px 0 10px 10px;
  border-color: transparent transparent transparent #e1ffc7;
  top: 0;
  right: -10px;
}

/* Compose */

.conversation-compose {
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  overflow: hidden;
  height: 50px;
  width: 100%;
  z-index: 2;
}

.conversation-compose div,
.conversation-compose input {
  background: #fff;
  height: 100%;
}

.conversation-compose .emoji {
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 5px 0 0 5px;
  flex: 0 0 auto;
  margin-left: 8px;
  width: 48px;
}

.conversation-compose .input-msg {
  border: 0;
  flex: 1 1 auto;
  font-size: 16px;
  margin: 0;
  outline: none;
  min-width: 50px;
}

.conversation-compose .photo {
  flex: 0 0 auto;
  border-radius: 0 0 5px 0;
  text-align: center;
  position: relative;
  width: 48px;
}

.conversation-compose .photo:after {
  border-width: 0px 0 10px 10px;
  border-color: transparent transparent transparent #fff;
  border-style: solid;
  position: absolute;
  width: 0;
  height: 0;
  content: "";
  top: 0;
  right: -10px;
}

.conversation-compose .photo i {
  display: block;
  color: #7d8488;
  font-size: 24px;
  transform: translate(-50%, -50%);
  position: relative;
  top: 50%;
  left: 50%;
}

.conversation-compose .send {
  background: transparent;
  border: 0;
  cursor: pointer;
  flex: 0 0 auto;
  margin-left: 8px;
  margin-right: 8px;
  padding: 0;
  position: relative;
  outline: none;
}

.conversation-compose .send .circle {
  background: #008a7c;
  border-radius: 50%;
  color: #fff;
  position: relative;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.conversation-compose .send .circle i {
  font-size: 24px;
  margin-left: 5px;
}

/* Small Screens */

@media (max-width: 768px) {
  .marvel-device.nexus5 {
    border-radius: 0;
    flex: none;
    padding: 0;
    max-width: none;
    overflow: hidden;
    height: 100%;
    width: 100%;
  }

  .marvel-device > .screen .chat {
    visibility: visible;
  }

  .marvel-device {
    visibility: hidden;
  }

  .marvel-device .status-bar {
    display: none;
  }

  .screen-container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }

  .conversation {
    height: calc(100vh - 55px);
  }
  .conversation .conversation-container {
    height: calc(100vh - 120px);
  }
}</style>
<style>

/* Button used to open the chat form - fixed at the bottom of the page */
.open-button {
  background-color: #555;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  opacity: 0.8;
  position: fixed;
  bottom: 23px;
  right: 28px;
  width: 280px;
}

/* The popup chat - hidden by default */
.chat-popup {
  display: none;
  position: fixed;
  bottom: 0;
  right: 15px;
  border: 3px solid #f1f1f1;
  z-index: 9;
}

/* Add styles to the form container */
.form-container {
  max-width: 500px;
  padding: 1px;
  background-color: #E5E4E2;
}

/* Full-width textarea */
.form-container textarea {
  width: 100%;
  padding: 1px;
  margin: 5px 0 22px 0;
  border: none;
  background: #f1f1f1;
  resize: none;
  min-height: 200px;
}

/* When the textarea gets focus, do something */
.form-container textarea:focus {
  background-color: #ddd;
  outline: none;
}

/* Set a style for the submit/send button */
.form-container .btn {
  background-color: #04AA6D;
  color: white;
  padding: 6px 10px;
  border: none;
  cursor: pointer;
  width: 100%;
  margin-bottom:10px;
  opacity: 0.8;
}

/* Add a red background color to the cancel button */
.form-container .cancel {
  background-color: red;
}

/* Add some hover effects to buttons */
.form-container .btn:hover, .open-button:hover {
  opacity: 1;
}
</style>
<div class="chat-popup1" id="myForm">
  <form action="/action_page.php" class="form-container">
   
<div class="page">
  <div class="marvel-device nexus5">
    <!--<div class="top-bar"></div>-->
    <!--<div class="sleep"></div>-->
    <!--<div class="volume"></div>-->
    <!--<div class="camera"></div>-->
    <div class="screen">
      <div class="screen-container">
        <!--<div class="status-bar">-->
        <!--  <div class="time"></div>-->
        <!--  <div class="battery">-->
        <!--    <i class="zmdi zmdi-battery"></i>-->
        <!--  </div>-->
        <!--  <div class="network">-->
        <!--    <i class="zmdi zmdi-network"></i>-->
        <!--  </div>-->
        <!--  <div class="wifi">-->
        <!--    <i class="zmdi zmdi-wifi-alt-2"></i>-->
        <!--  </div>-->
        <!--  <div class="star">-->
        <!--    <i class="zmdi zmdi-star"></i>-->
        <!--  </div>-->
        <!--</div>-->
        <div class="chat">
          <div class="chat-container">
            <div class="user-bar">
              <div class="back">
                <i class="zmdi zmdi-arrow-left"></i>
              </div>
              <div class="avatar">
                <img src="https://avatars2.githubusercontent.com/u/398893?s=128" alt="Avatar">
              </div>
              <div class="name">
                <span>Zeno Rocha</span>
                <span class="status">online</span>
              </div>
              <div class="actions more">
                <i class="zmdi zmdi-more-vert"></i>
              </div>
              <div class="actions attachment">
                <i class="zmdi zmdi-attachment-alt"></i>
              </div>
              <div class="actions">
                <i class="zmdi zmdi-phone"></i>
              </div>
            </div>
            <div class="conversation">
              <div class="conversation-container">
                <div class="message sent">
                  What happened last night?
                  <span class="metadata">
                      <span class="time"></span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"/></svg></span>
                  </span>
                </div>
                <div class="message received">
                  You were drunk.
                  <span class="metadata"><span class="time"></span></span>
                </div>
                <div class="message sent">
                  No I wasn't.
                  <span class="metadata">
                      <span class="time"></span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"/></svg></span>
                  </span>
                </div>
                <div class="message received">
                  <span id="random">You were hugging an old man with a beard screaming "DUMBLEDORE YOU'RE ALIVE!"</span>
                  <span class="metadata"><span class="time"></span></span>
                </div>
              </div>
              <form class="conversation-compose">
                <div class="emoji">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" id="smiley" x="3147" y="3209"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.153 11.603c.795 0 1.44-.88 1.44-1.962s-.645-1.96-1.44-1.96c-.795 0-1.44.88-1.44 1.96s.645 1.965 1.44 1.965zM5.95 12.965c-.027-.307-.132 5.218 6.062 5.55 6.066-.25 6.066-5.55 6.066-5.55-6.078 1.416-12.13 0-12.13 0zm11.362 1.108s-.67 1.96-5.05 1.96c-3.506 0-5.39-1.165-5.608-1.96 0 0 5.912 1.055 10.658 0zM11.804 1.01C5.61 1.01.978 6.034.978 12.23s4.826 10.76 11.02 10.76S23.02 18.424 23.02 12.23c0-6.197-5.02-11.22-11.216-11.22zM12 21.355c-5.273 0-9.38-3.886-9.38-9.16 0-5.272 3.94-9.547 9.214-9.547a9.548 9.548 0 0 1 9.548 9.548c0 5.272-4.11 9.16-9.382 9.16zm3.108-9.75c.795 0 1.44-.88 1.44-1.963s-.645-1.96-1.44-1.96c-.795 0-1.44.878-1.44 1.96s.645 1.963 1.44 1.963z" fill="#7d8489"/></svg>
                </div>
                <input class="input-msg" name="input" placeholder="Type a message" autocomplete="off" autofocus></input>
                <div class="photo">
                  <i class="zmdi zmdi-camera"></i>
                </div>
                <button class="send">
                    <div class="circle">
                      <i class="zmdi zmdi-mail-send"></i>
                    </div>
                  </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    <!--<label for="msg"><b>Message</b></label>-->
    <!--<textarea placeholder="Type message.." name="msg" required></textarea>-->

    <!--<button type="submit" class="btn">Send</button>-->
    <!--<button type="button" class="btn cancel" onclick="closeForm()">Close</button>-->
  </form>
</div>

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
