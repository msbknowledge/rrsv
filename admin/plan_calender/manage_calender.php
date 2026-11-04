<?php
error_reporting(1);
include('../../include/header.php');
include('../../include/dbcon.php');
$data = [];

?>
<style>
.details-btn {
    padding: 8px 15px;
    background: #007bff;
    color: #fff;
    text-decoration: none;
    margin-left: 10px;
    border-radius: 4px;
}
.date-row {
    display: flex;
    justify-content: space-between; /* left-right */
    margin-top: 10px;
    font-size: 16px;
}
    .modal {
    display: none;   /* ✅ keep hidden initially */
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);

    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #fff;
    width: 500px;
    max-width: 90%;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    animation: fadeIn 0.3s ease;
}

.modal-content h2 {
    margin-top: 0;
}

.modal-content p {
    line-height: 1.6;
    text-align: justify;
}

.close-btn {
    margin-top: 15px;
    padding: 8px 15px;
    border: none;
    background: #333;
    color: #fff;
    cursor: pointer;
}

@keyframes fadeIn {
    from {transform: scale(0.8); opacity: 0;}
    to {transform: scale(1); opacity: 1;}
}
</style>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


<div class="main-panel">
  <div class="content-wrapper">
    <div class="col-12 grid-margin">
      <div class="card">
       
        <div id="calendar"></div>
         
         
        </div>
      </div>
    </div>
  </div>
<div id="eventModal" class="modal">
  <div class="modal-content">
    
    <h2 id="eventTitle"></h2>
    
    <p id="eventDesc"></p>
    
    <div class="date-row">
    <div><b>Event Date:</b> <span id="eventDate"></span></div>
    <div><b>Reminder Date:</b> <span id="remDate"></span></div>
</div>

    <div style="margin-top:15px;">
        <button class="close-btn" onclick="closeModal()">Close</button>

        <a id="viewBtn" class="details-btn" href="#">
            View Details
        </a>
    </div>

  </div>
</div>
  <script>
      document.addEventListener('DOMContentLoaded', function () {

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',

        events: 'fetch_events.php',

        eventClick: function(info) {
            document.getElementById('eventTitle').innerText = info.event.title;
            document.getElementById('eventDesc').innerText = info.event.extendedProps.description;
            document.getElementById('eventDate').innerText = info.event.startStr;
             document.getElementById('remDate').innerText = 
        info.event.extendedProps.reminder_date ?? 'N/A';
         document.getElementById('viewBtn').href = 'event.php?id=' + info.event.id;
            document.getElementById('eventModal').style.display = 'flex';
           // window.location.href = 'event.php?id=' + info.event.id;
        }
    });

    calendar.render();
});
function closeModal(){
    document.getElementById('eventModal').style.display = 'none';
}
  </script>



  <?php
  include('../../include/footer.php');
  
  ?>