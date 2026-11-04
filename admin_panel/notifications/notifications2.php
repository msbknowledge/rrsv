<style>
.msger-inputarea * {
    padding: 10px;
    border: none;
    border-radius: 3px;
    font-size: 1em;
}

.msger-inputarea {
    display: flex;
    padding: 10px;
    border-top: var(--border);
    background: #eee;
}
    .msger-send-btn {
    margin-left: 10px;
    background: rgb(0, 196, 65);
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.23s;
}
</style>

<?php
/*************************************************
 *  notifications.php
 *  Optimized & Clean Version
 *************************************************/
session_start();
include '../dbcon.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['teacher','student'])) {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}
$teacher = $_SESSION['user_data'];
/* ================= USER CONTEXT ================= */
$user        = $_SESSION['user_data'];
$user_id     = (int)$user['id'];
$user_role   = $_SESSION['role'];
$session     = date('Y');

$student_ids = $_SESSION['student_ids'] ?? [];
$ids         = $student_ids ? implode(',', array_map('intval', $student_ids)) : '0';
if ($_POST['action'] === 'send_chat') {
    // Debug only for AJAX
    // file_put_contents('chat_debug.txt', print_r($_POST,true)."\n", FILE_APPEND);

    $sender_type   = mysqli_real_escape_string($con, $_POST['sender_type']);
    $sender_id     = intval($_POST['sender_id']);
    $receiver_type =$_POST['receiver_type'];
    $receiver_id   = intval($_POST['receiver_id']);
    $behaviour_id  = intval($_POST['behaviour_id']);
    $message       = mysqli_real_escape_string($con, $_POST['message']);
    $session       = date('Y'); 

    if($message && $behaviour_id){
        
       
$sql = "
            SELECT id 
            FROM rrsv_student_registration
            WHERE scl_class = '$receiver_type'
            AND scl_session = '$session'
        ";

        $result = mysqli_query($con, $sql);

        while ($row = mysqli_fetch_assoc($result)) {

            $stu_id = (int)$row['id'];

            /* Insert comment */
            mysqli_query($con, "
                INSERT INTO rrsv_behaviour_comments
                (behaviour_id, user_type, user_id, student_id, comment, created_at)
                VALUES
                ($behaviour_id, '$sender_type', $sender_id, $stu_id, '$message', NOW())
            ");

            $comment_id = mysqli_insert_id($con);

            /* Notification */
            mysqli_query($con, "
                INSERT INTO rrsv_notifications
                (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, comment_id, message, seen, created_at)
                VALUES
                ('student', $stu_id, '$sender_type', $sender_id, $behaviour_id, $comment_id, '$message', 0, NOW())
            ");
        }
        // mysqli_query($con, "
        //     INSERT INTO rrsv_notifications 
        //     (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, message, created_at)
        //     VALUES 
        //     ('$receiver_type', $receiver_id, '$sender_type', $sender_id, $behaviour_id, '$message', NOW())
        // ");
    }
    exit;
}

/* ================= AJAX HANDLERS ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* -------- LOAD CHAT (AUTO) -------- */
    if ($_POST['action'] === 'load_chat') {

        $behaviour_id = (int)($_POST['behaviour_id'] ?? 0);
        if (!$behaviour_id) exit;

        $sql = "
            SELECT c.comment, c.created_at, c.user_type,
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
        ";

        $res = mysqli_query($con, $sql);

        while ($row = mysqli_fetch_assoc($res)) {
            $align = ($row['user_type'] === $user_role) ? 'right' : 'left';

            echo "
            <div class='msg {$align}-msg'>
                <div class='msg-bubble'>
                    <div class='msg-info-name'>".htmlspecialchars($row['user_name'])."</div>
                    <div class='msg-text'>".htmlspecialchars($row['comment'])."</div>
                    <div class='msg-info-time'>".date('H:i',strtotime($row['created_at']))."</div>
                </div>
            </div>";
        }
        exit;
    }
    if ($_POST['action'] === 'add') {
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_name = mysqli_real_escape_string($con, $_POST['receiver_name']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        mysqli_query($con, "INSERT INTO rrsv_notifications (receiver_type, receiver_name, message) VALUES ('$receiver_type', '$receiver_name', '$message')");
        echo "Notification Sent!";
        exit;
    }

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

    // Fetch comment_id first
    $query = mysqli_query($con, "SELECT comment_id FROM rrsv_notifications WHERE id = $id");
    $data = mysqli_fetch_assoc($query);
    $comment_id = $data['comment_id'] ?? null;

    if ($comment_id) {
        mysqli_query($con, "DELETE FROM rrsv_behaviour_comments WHERE id = $comment_id");
    }

    mysqli_query($con, "DELETE FROM rrsv_notifications WHERE id = $id");

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
      // print_r($_POST);die();
    $sender_type   = mysqli_real_escape_string($con, $_POST['sender_type']);
        $sender_id     = intval($_POST['sender_id']);
        $receiver_type = mysqli_real_escape_string($con, $_POST['receiver_type']);
        $receiver_id   = '';
        $behaviour_id  = intval($_POST['behaviour_id']);
        $message       = mysqli_real_escape_string($con, $_POST['message']);

    if($message && $behaviour_id){
        mysqli_query($con, "
            INSERT INTO rrsv_notifications 
            (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, message, created_at)
            VALUES 
            ('$receiver_type', $receiver_id, '$sender_type', $sender_id, $behaviour_id, '$message', NOW())
        ");
    }
    exit;
}

    /* -------- SEND CHAT -------- */
    // if ($_POST['action'] === 'send_chat') {
    //     $msg = mysqli_real_escape_string($con, $_POST['message'] ?? '');
    //     if ($msg !== '') {
    //         mysqli_query($con,"
    //             INSERT INTO rrsv_notifications (message, sender_type, sender_id, created_at)
    //             VALUES ('$msg','$user_role',$user_id,NOW())
    //         ");
    //     }
    //     exit;
    // }

    /* -------- DELETE NOTIFICATION -------- */
    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        mysqli_query($con,"DELETE FROM rrsv_notifications WHERE id=$id");
        echo "Deleted";
        exit;
    }
}

/* ================= FETCH BEHAVIOUR ================= */
if ($user_role === 'teacher') {
    $bq = mysqli_query($con,"
        SELECT id FROM rrsv_student_behaviour
        WHERE teacher_id=$user_id AND scl_session='$session'
        ORDER BY id DESC LIMIT 1
    ");
} else {
    $bq = mysqli_query($con,"
        SELECT id FROM rrsv_student_behaviour
        WHERE student_id IN ($ids) AND scl_session='$session'
        ORDER BY id DESC LIMIT 1
    ");
}
$br = mysqli_fetch_assoc($bq);
$behaviour_id = $br['id'] ?? 0;

/* ================= FETCH NOTIFICATIONS ================= */
$where = ($user_role==='student')
    ? "n.receiver_id IN ($ids)"
    : "n.receiver_id=$user_id";

$notifications = mysqli_query($con,"
    SELECT n.*, 
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
    ORDER BY n.created_at DESC
    LIMIT 100
");
?>

<?php include('../header.php'); ?>
<style>
    .msg {
    display: flex;
    margin-bottom: 10px;
}

.left-msg {
    justify-content: flex-start;
}

.right-msg {
    justify-content: flex-end;
}

.msg-bubble {
    max-width: 70%;
    padding: 8px 12px;
    border-radius: 15px;
    position: relative;
    font-size: 14px;
    line-height: 1.4;
}

.left-msg .msg-bubble {
    background: #ffffff;
    border: 1px solid #ddd;
    border-bottom-left-radius: 0;
}

.right-msg .msg-bubble {
    background: #0084ff;
    color: #fff;
    border-bottom-right-radius: 0;
}

.msg-info-name {
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 3px;
}

.msg-info-time {
    font-size: 10px;
    opacity: 0.7;
    margin-top: 4px;
    text-align: right;
}

</style>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<header class="main-header">
    <?php include '../top_navbar.php'; ?>
</header>

<?php include '../left_navbar.php'; ?>

<div class="content-wrapper">
<section class="content">

<!-- ================= NOTIFICATIONS TABLE ================= -->
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Notifications</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sender</th>
                    <th>Message</th>
                    <th>Time</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php $i=1; while($n=mysqli_fetch_assoc($notifications)){ ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($n['sender_name']) ?></td>
                    <td><?= htmlspecialchars($n['message']) ?></td>
                    <td><?= date('d-m-Y h:i A',strtotime($n['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-danger btn-xs"
                            onclick="deleteNotification(<?= $n['id'] ?>)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= LIVE CHAT ================= -->

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Live Communication</h3>
    </div>
    <div class="box-body">

        <div id="chatBox"
             data-behaviour-id="<?= $behaviour_id ?>"
             style="height:300px; overflow-y:auto; background:#f9f9f9; padding:10px;">
        </div>
<?php if ($user_role=='teacher'){?>
 <div class="chat-input">
            <input type="hidden" id="chat_sender_type" value="<?= $user_role ?>">
            <input type="hidden" id="chat_sender_id" value="<?= $user_id ?>">
           

                      <div class="form-group">
                        <label>Class</label>
                        <select name="chat_receiver_type" class="form-control" id="chat_receiver_type" required>
                          <option value="">-Select Class-</option>
                          <?php
                          $res = mysqli_query($con, "SELECT * FROM rrsv_class ORDER BY id");
                          while ($obj = mysqli_fetch_assoc($res)) {
                            echo "<option value='{$obj['class_name']}'>{$obj['class_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                     
            <div class="form-group msger-inputarea">
            <input type="hidden" id="chat_receiver_id" placeholder="Receiver ID" >
            
            <textarea type="text" id="chat_message"  rows="2" style="width: 100%;" class="form-group msger-input" placeholder="Type message..." required></textarea>
            </div>
            <button id="sendBtn" class="msger-send-btn">Send</button>
        </div>
<?php } ?>
    </div>
</div>

</section>
</div>

<?php include('../footer.php'); ?>
</div>

<!-- ================= SCRIPTS ================= -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>

<script>
function loadChat(){
    let bid = $('#chatBox').data('behaviour-id');
    if(!bid){
        $('#chatBox').html('<p class="text-muted text-center">No chat available</p>');
        return;
    }
    $.post('notifications.php',{
        action:'load_chat',
        behaviour_id:bid
    },function(data){
        $('#chatBox').html(data)
                     .scrollTop($('#chatBox')[0].scrollHeight);
    });
}

$(document).ready(function(){
    loadChat();
    setInterval(loadChat,4000);
});

$(document).ready(function(){
    loadChat();
    setInterval(loadChat,4000);

    $('#sendBtn').click(function(){
        let currentBehaviour = $('#chatBox').data('behaviour-id');
        let sender_type   = $('#chat_sender_type').val();
        let sender_id     = $('#chat_sender_id').val();
        let receiver_type = $('#chat_receiver_type').val();
       // let receiver_id   = '';//$('#chat_receiver_id').val();
        let message       = $('#chat_message').val();

        if(currentBehaviour && message && receiver_type ){
            $.post('notifications.php',{
                action:'send_chat',
                behaviour_id: currentBehaviour,
                sender_type: sender_type,
                sender_id: sender_id,
                receiver_type: receiver_type,
                message: message
            }, function(){
                $('#chat_message').val('');
                loadChat();
            });
        } else {
            alert('Fill all fields');
        }
    });
});


function deleteNotification(id){
    if(confirm('Delete notification?')){
        $.post('notifications.php',{action:'delete',id:id},function(){
            location.reload();
        });
    }
}
</script>
</body>
</html>
