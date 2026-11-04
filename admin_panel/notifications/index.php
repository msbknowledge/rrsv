<?php
session_start();
include '../dbcon.php';

// ======================= HANDLE AJAX REQUESTS ======================= //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CREATE NOTIFICATION ---
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "INSERT INTO rrsv_notifications (receiver_type, receiver_name, message) 
                            VALUES ('$receiver_type', '$receiver_name', '$message')");
        echo "Notification Sent!";
        exit;
    }

    // --- EDIT (FETCH DATA) ---
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = intval($_POST['id']);
        $result = mysqli_query($con, "SELECT * FROM rrsv_notifications WHERE id=$id");
        echo json_encode(mysqli_fetch_assoc($result));
        exit;
    }

    // --- UPDATE NOTIFICATION ---
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = intval($_POST['id']);
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "UPDATE rrsv_notifications 
                            SET receiver_type='$receiver_type', receiver_name='$receiver_name', message='$message' 
                            WHERE id=$id");
        echo "Updated Successfully!";
        exit;
    }

    // --- DELETE SINGLE ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        mysqli_query($con, "DELETE FROM rrsv_notifications WHERE id=$id");
        echo "Deleted Successfully!";
        exit;
    }

    // --- DELETE MULTIPLE ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete_multiple') {
        $ids = implode(',', array_map('intval', $_POST['ids']));
        mysqli_query($con, "DELETE FROM rrsv_notifications WHERE id IN ($ids)");
        echo "Selected notifications deleted!";
        exit;
    }

    // --- LOAD CHAT ---
    if (isset($_POST['action']) && $_POST['action'] === 'load_chat') {
        $chat = mysqli_query($con, "SELECT * FROM rrsv_notifications ORDER BY created_at ASC LIMIT 100");
        while ($row = mysqli_fetch_assoc($chat)) {
            $sender = ucfirst($row['receiver_type']);
            echo "<div style='margin-bottom:8px;'>
                    <b>{$sender}:</b> " . htmlspecialchars($row['message']) . "
                    <span style='font-size:11px; color:#888; float:right;'>" . date('d-m h:i A', strtotime($row['created_at'])) . "</span>
                  </div>";
        }
        exit;
    }

    // --- SEND CHAT ---
    if (isset($_POST['action']) && $_POST['action'] === 'send_chat') {
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "INSERT INTO rrsv_notifications (receiver_type, receiver_name, message) 
                            VALUES ('$receiver_type', '$receiver_name', '$message')");
        echo "Message Sent!";
        exit;
    }
}

// ======================= FETCH DATA FOR DISPLAY ======================= //
$notifications = mysqli_query($con, "SELECT * FROM rrsv_notifications ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RR School - Notifications</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    body { background: #f5f6fa; padding: 20px; }
    .box { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .box-header { padding: 15px 20px; border-bottom: 1px solid #eee; }
    .box-body { padding: 20px; }
    .modal .form-control { margin-bottom: 10px; }
  </style>
</head>
<body>

<section class="content">
  <!-- Notification Box -->
  <div class="box box-primary">
    <div class="box-header with-border clearfix">
      <h3 class="box-title pull-left">Notifications</h3>
      <button class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#notificationModal" onclick="openAddModal()">+ Send Notification</button>
    </div>

    <div class="box-body">
      <button id="deleteSelectedBtn" class="btn btn-danger btn-sm mb-2">Delete Selected</button>

      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th><input type="checkbox" id="select_all"></th>
            <th>#</th>
            <th>Receiver Type</th>
            <th>Receiver Name</th>
            <th>Message</th>
            <th>Created At</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; while($n=mysqli_fetch_assoc($notifications)){ ?>
          <tr data-id="<?= $n['id']; ?>">
            <td><input type="checkbox" class="row_checkbox" value="<?= $n['id']; ?>"></td>
            <td><?= $i++; ?></td>
            <td><?= ucfirst($n['receiver_type']); ?></td>
            <td><?= htmlspecialchars($n['receiver_name']); ?></td>
            <td><?= htmlspecialchars(substr($n['message'], 0, 50)); ?>...</td>
            <td><?= date('d-m-Y h:i A', strtotime($n['created_at'])); ?></td>
            <td class="text-center">
              <button class="btn btn-info btn-xs" onclick="viewNotification(<?= $n['id']; ?>)"><i class="fa fa-eye"></i></button>
              <button class="btn btn-warning btn-xs" onclick="editNotification(<?= $n['id']; ?>)"><i class="fa fa-edit"></i></button>
              <button class="btn btn-danger btn-xs" onclick="deleteNotification(<?= $n['id']; ?>)"><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Live Chat -->
  <div class="box box-success">
    <div class="box-header"><h3 class="box-title">Live Communication Panel</h3></div>
    <div class="box-body">
      <div id="chatBox" style="height:300px; overflow-y:auto; border:1px solid #ccc; padding:10px; background:#f9f9f9;"></div>
      <form id="chatForm" class="mt-3">
        <div class="row" style="margin-top:15px;">
          <div class="col-md-3">
            <select name="receiver_type" id="chat_receiver_type" class="form-control" required>
              <option value="">Select Receiver Type</option>
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
              <option value="class">Class</option>
              <option value="all">All</option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="text" name="receiver_name" id="chat_receiver_name" class="form-control" placeholder="Receiver Name (optional)">
          </div>
          <div class="col-md-4">
            <input type="text" name="chat_message" id="chat_message" class="form-control" placeholder="Type your message..." required>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success btn-block">Send</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- Modal -->
<div id="notificationModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title">Send Notification</h4></div>
      <div class="modal-body">
        <form id="notificationForm">
          <input type="hidden" id="notify_id">
          <select id="receiver_type" class="form-control" required>
            <option value="">Select Receiver Type</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="class">Class</option>
            <option value="all">All</option>
          </select>
          <input type="text" id="receiver_name" class="form-control" placeholder="Receiver Name (optional)">
          <textarea id="message" class="form-control" placeholder="Enter Message" required></textarea>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="saveBtn">Save</button>
        <button class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function openAddModal(){
  $('#notify_id').val('');
  $('#notificationForm')[0].reset();
  $('.modal-title').text('Send Notification');
}

// Save or Update Notification
$('#saveBtn').click(function(){
  let id = $('#notify_id').val();
  let data = {
    action: id ? 'update' : 'add',
    id: id,
    receiver_type: $('#receiver_type').val(),
    receiver_name: $('#receiver_name').val(),
    message: $('#message').val()
  };
  $.post('notifications.php', data, function(res){
    alert(res);
    location.reload();
  });
});

// Edit
function editNotification(id){
  $.post('notifications.php', {action:'get', id:id}, function(res){
    let data = JSON.parse(res);
    $('#notify_id').val(data.id);
    $('#receiver_type').val(data.receiver_type);
    $('#receiver_name').val(data.receiver_name);
    $('#message').val(data.message);
    $('.modal-title').text('Edit Notification');
    $('#notificationModal').modal('show');
  });
}

// View
function viewNotification(id){
  $.post('notifications.php', {action:'get', id:id}, function(res){
    let d = JSON.parse(res);
    alert("Type: " + d.receiver_type + "\nTo: " + d.receiver_name + "\n\nMessage:\n" + d.message);
  });
}

// Delete Single
function deleteNotification(id){
  if(confirm('Delete this notification?')){
    $.post('notifications.php', {action:'delete', id:id}, function(res){
      alert(res); location.reload();
    });
  }
}

// Select All Checkbox
$('#select_all').click(function(){
  $('.row_checkbox').prop('checked', this.checked);
});

// Delete Multiple
$('#deleteSelectedBtn').click(function(){
  let ids = $('.row_checkbox:checked').map(function(){return $(this).val();}).get();
  if(ids.length == 0){ alert('No rows selected'); return; }
  if(confirm('Delete selected notifications?')){
    $.post('notifications.php', {action:'delete_multiple', ids:ids}, function(res){
      alert(res); location.reload();
    });
  }
});

// Chat Load & Send
function loadChat(){
  $.post('notifications.php', {action:'load_chat'}, function(data){
    $('#chatBox').html(data);
    $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
  });
}
$('#chatForm').submit(function(e){
  e.preventDefault();
  $.post('notifications.php', {
    action:'send_chat',
    receiver_type:$('#chat_receiver_type').val(),
    receiver_name:$('#chat_receiver_name').val(),
    message:$('#chat_message').val()
  }, function(){
    $('#chat_message').val('');
    loadChat();
  });
});
setInterval(loadChat, 5000);
loadChat();
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
