<?php
session_start();
include '../dbcon.php';
$teacher = $_SESSION['user_data'];
$current_session = date('Y');
$user_id = $teacher['id'];
// 🔒 Auth Check
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['teacher', 'student'])) {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}else{
    
     if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
     $user_role=$_SESSION['role'] ;
     }elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'teacher') {
         $user_role=$_SESSION['role'] ;
          $behaviour=mysqli_query($con,"SELECT  rrsv_student_behaviour.*,rrsv_teacher.full_name AS teacher_name,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_behaviour INNER JOIN rrsv_teacher ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id INNER JOIN rrsv_student_registration ON rrsv_student_behaviour.student_id = rrsv_student_registration.id WHERE rrsv_student_behaviour.scl_session = '$current_session' 
        AND rrsv_student_behaviour.teacher_id = $user_id
        ORDER BY rrsv_student_behaviour.id DESC");
     }elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'student'){
         $user_role=$_SESSION['role'] ;
         $student_ids = $_SESSION['student_ids'];
          
                if (!empty($student_ids)) {
                    $ids = implode(',', array_map('intval', $student_ids));
                } else {
                    $ids = 0; // fallback to prevent SQL error
                }
         if ($_POST['action'] === 'load_chat') {

            $behaviour_id = intval($_POST['behaviour_id']);
            if (!$behaviour_id) exit;
        
            $chat = mysqli_query($con, "
                SELECT c.*,
                       CASE 
                            WHEN c.user_type='teacher' THEN t.full_name
                            ELSE s.scl_name
                       END AS user_name
                FROM rrsv_behaviour_comments c
                LEFT JOIN rrsv_teacher t 
                    ON c.user_type='teacher' AND c.user_id=t.id
                LEFT JOIN rrsv_student_registration s 
                    ON c.user_type='student' AND c.user_id=s.id
                WHERE c.behaviour_id=$behaviour_id
                ORDER BY c.created_at ASC
            ");
        
            while ($row = mysqli_fetch_assoc($chat)) {
                $align = ($row['user_type'] === $_SESSION['role']) ? 'right' : 'left';
        
                echo "
                <div class='msg {$align}-msg'>
                    <div class='msg-bubble'>
                        <div class='msg-info-name'>{$row['user_name']}</div>
                        <div class='msg-text'>".htmlspecialchars($row['comment'])."</div>
                        <div class='msg-info-time'>".date('H:i', strtotime($row['created_at']))."</div>
                    </div>
                </div>";
            }
            exit;
        }



                
                  $behaviour=mysqli_query($con,"SELECT  rrsv_student_behaviour.*,rrsv_teacher.full_name AS teacher_name,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_behaviour INNER JOIN rrsv_teacher ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id INNER JOIN rrsv_student_registration ON rrsv_student_behaviour.student_id = rrsv_student_registration.id WHERE rrsv_student_behaviour.scl_session = '$current_session' 
        AND (rrsv_student_behaviour.student_id IN ($id_list) )
        ORDER BY rrsv_student_behaviour.id DESC");

      
     }else{
         
     }
}
    



// ======================= HANDLE AJAX REQUESTS ======================= //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
     if ($_POST['action'] === 'load_chat') {

        $behaviour_id = intval($_POST['behaviour_id']);
        if (!$behaviour_id) exit;

        $chat = mysqli_query($con, "
            SELECT c.*, 
                   CASE 
                        WHEN c.user_type='teacher' THEN t.full_name
                        ELSE s.scl_name
                   END AS user_name
            FROM rrsv_behaviour_comments c
            LEFT JOIN rrsv_teacher t 
                ON c.user_type='teacher' AND c.user_id=t.id
            LEFT JOIN rrsv_student_registration s 
                ON c.user_type='student' AND c.user_id=s.id
            WHERE c.behaviour_id = $behaviour_id
            ORDER BY c.created_at ASC
        ");

        while ($row = mysqli_fetch_assoc($chat)) {
            $align = ($row['user_type'] === $_SESSION['role']) ? 'right' : 'left';

            echo "
            <div class='msg {$align}-msg'>
                <div class='msg-bubble'>
                    <div class='msg-info-name'>{$row['user_name']}</div>
                    <div class='msg-text'>".htmlspecialchars($row['comment'])."</div>
                    <div class='msg-info-time'>".date('H:i', strtotime($row['created_at']))."</div>
                </div>
            </div>";
        }
        exit;
    }

    // --- CREATE NOTIFICATION ---
    if ($_POST['action'] === 'add') {
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "INSERT INTO rrsv_notifications (receiver_type, receiver_name, message) VALUES ('$receiver_type', '$receiver_name', '$message')");
        echo "Notification Sent!";
        exit;
    }

    // --- FETCH SINGLE NOTIFICATION ---
    if ($_POST['action'] === 'get') {
    $id = intval($_POST['id']);

    $q = mysqli_query($con, "
        SELECT 
            n.message,
            n.created_at,
            n.sender_type,

            CASE 
                WHEN n.sender_type='teacher' THEN t.full_name
                ELSE s.scl_name
            END AS sender_name,

            b.des AS behaviour_desc

        FROM rrsv_notifications n
        LEFT JOIN rrsv_teacher t 
            ON n.sender_type='teacher' AND n.sender_id=t.id
        LEFT JOIN rrsv_student_registration s 
            ON n.sender_type='student' AND n.sender_id=s.id
        LEFT JOIN rrsv_student_behaviour b 
            ON n.behaviour_id=b.id
        WHERE n.id=$id
    ");

    // mark as seen
   // mysqli_query($con, "UPDATE rrsv_notifications SET seen=1 WHERE id=$id");

    echo json_encode(mysqli_fetch_assoc($q));
    exit;
}

    // if ($_POST['action'] === 'get') {
    //     $id = intval($_POST['id']);
    //     $result = mysqli_query($con, "SELECT * FROM rrsv_notifications WHERE id=$id");
    //     echo json_encode(mysqli_fetch_assoc($result));
    //     exit;
    // }

    // --- UPDATE ---
    if ($_POST['action'] === 'update') {
        $id = intval($_POST['id']);
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "UPDATE rrsv_notifications SET receiver_type='$receiver_type', receiver_name='$receiver_name', message='$message' WHERE id=$id");
        echo "Updated Successfully!";
        exit;
    }

    // --- DELETE SINGLE ---
    if ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        mysqli_query($con, "DELETE FROM rrsv_notifications WHERE id=$id");
        echo "Deleted Successfully!";
        exit;
    }

    // --- DELETE MULTIPLE ---
    if ($_POST['action'] === 'delete_multiple') {
        $ids = implode(',', array_map('intval', $_POST['ids']));
        mysqli_query($con, "DELETE FROM rrsv_notifications WHERE id IN ($ids)");
        echo "Selected notifications deleted!";
        exit;
    }

    // --- LOAD CHAT ---
   

    // --- SEND CHAT ---
    if ($_POST['action'] === 'send_chat') {
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "INSERT INTO rrsv_notifications (receiver_type, receiver_name, message) VALUES ('$receiver_type', '$receiver_name', '$message')");
        echo "Message Sent!";
        exit;
    }
}

// ======================= FETCH FOR DISPLAY ======================= //
//$notifications = mysqli_query($con, "SELECT * FROM rrsv_notifications ORDER BY id DESC");
if ($user_role === 'student') {
    $where = "n.receiver_id IN ($ids)";
} else {
    $where = "n.receiver_id = $user_id";
}

$notifications = mysqli_query($con, "
    SELECT 
        n.id,
        n.message,
        n.created_at,
        n.sender_type,
        
        CASE 
            WHEN n.sender_type='teacher' THEN t.full_name
            ELSE s.scl_name
        END AS sender_name
    FROM rrsv_notifications n
    LEFT JOIN rrsv_teacher t 
        ON n.sender_type='teacher' AND n.sender_id=t.id
    LEFT JOIN rrsv_student_registration s 
        ON n.sender_type='student' AND n.sender_id=s.id
    WHERE $where
    ORDER BY n.created_at ASC
    LIMIT 100
");

?>

<?php include('../header.php'); ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Header -->
  <header class="main-header">
    <?php include '../top_navbar.php'; ?>
  </header>

  <!-- Sidebar -->
  <?php include '../left_navbar.php'; ?>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content">
      <div class="box box-primary">
        <div class="box-header with-border clearfix">
          <h3 class="box-title pull-left">Notifications Management</h3>
          <button class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#notificationModal" onclick="openAddModal()">+ Send Notification</button>
        </div>

        <div class="box-body">
          <button id="deleteSelectedBtn" class="btn btn-danger btn-sm">Delete Selected</button>
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th><input type="checkbox" id="select_all"></th>
                <th>#</th>
                <th>Sender Type</th>
                <th>Sender Name</th>
                <th>Message</th>
                <th>Created At</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; while($n=mysqli_fetch_assoc($notifications)){ ?>
              <tr>
                <td><input type="checkbox" class="row_checkbox" value="<?= $n['id']; ?>"></td>
                <td><?= $i++; ?></td>
                <td><?= ucfirst($n['sender_type']); ?></td>
                <td><?= htmlspecialchars($n['sender_name']); ?></td>
                <td><?= htmlspecialchars(substr($n['message'], 0, 40)); ?>...</td>
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

      <!-- Communication Panel -->
      <div class="box box-success">
        <div class="box-header with-border"><h3 class="box-title">Live Communication</h3></div>
        <div class="box-body">
          <div id="chatBox" data-behaviour-id="<?= $behaviour_id ?>" style="height:300px; overflow-y:auto; background:#f9f9f9; border:1px solid #ccc; padding:10px;">
              
              
          </div>
         
          <form id="chatForm" class="mt-3">
            <div class="row" style="margin-top:15px;">
              <div class="col-md-3">
                <select name="receiver_type" id="chat_receiver_type" class="form-control" required>
                  <option value="">Receiver Type</option>
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
                <input type="text" name="chat_message" id="chat_message" class="form-control" placeholder="Type message..." required>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-block">Send</button>
              </div>
            </div>
          </form>
          
           <?php
                    $i = 1;
                                    while ($b = mysqli_fetch_assoc($behaviour)) {
                    $comments = mysqli_query($con, "
                        SELECT c.*, 
                               CASE 
                                    WHEN c.user_type = 'teacher' THEN t.full_name
                                    WHEN c.user_type = 'student' THEN s.scl_name
                               END AS user_name
                        FROM rrsv_behaviour_comments AS c
                        LEFT JOIN rrsv_teacher AS t ON c.user_type = 'teacher' AND c.user_id = t.id
                        LEFT JOIN rrsv_student_registration AS s ON c.user_type = 'student' AND c.user_id = s.id
                        WHERE c.behaviour_id = {$b['id']}
                        ORDER BY c.created_at asc
                    ");
                    while ($c = mysqli_fetch_assoc($comments)):
                      $align = ($c['user_type'] == 'teacher') ? 'left' : 'right';
                    ?>
                      <div class="msg <?= $align ?>-msg">
                        <div class="msg-bubble">
                          <div class="msg-info">
                            <div class="msg-info-name"><?= ucfirst($c['user_name']) ?><br/></div>
                            
                          </div>
                          <div class="msg-info-time"><?= date('H:i', strtotime($c['created_at'])) ?></div>
                          <div class="msg-text"><?= htmlspecialchars($c['comment']) ?></div>
                        </div>
                      </div>
                    <?php endwhile; }?>
          
          
        </div>
      </div>

    </section>
  </div>

  <!-- Footer -->
  <?php include '../footer.php'; ?>

</div>

<!-- Notification Modal -->
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

<!-- Scripts -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script>
$(function() { $("#example1").DataTable(); });

// Add/Edit Modal
function openAddModal(){ $('#notify_id').val(''); $('#notificationForm')[0].reset(); $('.modal-title').text('Send Notification'); }

// Save/Update
$('#saveBtn').click(function(){
  let id = $('#notify_id').val();
  let data = {
    action: id ? 'update' : 'add',
    id: id,
    receiver_type: $('#receiver_type').val(),
    receiver_name: $('#receiver_name').val(),
    message: $('#message').val()
  };
  $.post('notifications.php', data, function(res){ alert(res); location.reload(); });
});

// Edit
function editNotification(id){
  $.post('notifications.php', {action:'get', id:id}, function(res){
    let d = JSON.parse(res);
    $('#notify_id').val(d.id);
    $('#receiver_type').val(d.receiver_type);
    $('#receiver_name').val(d.receiver_name);
    $('#message').val(d.message);
    $('.modal-title').text('Edit Notification');
    $('#notificationModal').modal('show');
  });
}

// View
function viewNotification(id){
  $.post('notifications.php', {action:'get', id:id}, function(res){
    let d = JSON.parse(res);
    alert("Type: "+d.sender_type+"\nTo: "+d.sender_name+"\n\nMessage:\n"+d.message);
  });
}

// Delete Single
function deleteNotification(id){
  if(confirm('Delete this notification?')){
    $.post('notifications.php', {action:'delete', id:id}, function(res){ alert(res); location.reload(); });
  }
}

// Bulk Delete
$('#deleteSelectedBtn').click(function(){
  let ids = $('.row_checkbox:checked').map(function(){return $(this).val();}).get();
  if(ids.length==0){ alert('No rows selected'); return; }
  if(confirm('Delete selected notifications?')){
    $.post('notifications.php', {action:'delete_multiple', ids:ids}, function(res){ alert(res); location.reload(); });
  }
});
function loadChat(){
    let behaviour_id = $('#chatBox').data('behaviour-id');

    if(!behaviour_id){
        $('#chatBox').html('<p class="text-muted">No behaviour available</p>');
        return;
    }

    $.post('notifications.php', {
        action: 'load_chat',
        behaviour_id: behaviour_id
    }, function(data){
        $('#chatBox').html(data);
        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
    });
}

// Load immediately
loadChat();

// Auto refresh every 4 seconds
setInterval(loadChat, 4000);

// Chat Auto Refresh
// function loadChat(){
//     let behaviour_id = $('#chatBox').data('behaviour-id');

//     $.post('notifications.php', {
//         action: 'load_chat',
//         behaviour_id: behaviour_id
//     }, function(data){
//         $('#chatBox').html(data);
//         $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
//     });
// }
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
loadChat();

// auto refresh every 4 seconds
setInterval(loadChat, 4000);
</script>
</body>
</html>
