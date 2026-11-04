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
.toggle-students-btn {
    background: none;
    border: none;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    margin-top: 5px;
}

.students-list {
    margin-top: 5px;
    font-size: 0.85em;
    line-height: 1.2em;
    color: #f0f0f0; /* lighter for blue bubble */
}

</style>
<style>
 :root {
  --body-bg: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  --msger-bg: #fff;
  --border: 2px solid #ddd;
  --left-msg-bg: #ececec;
  --right-msg-bg: #579ffb;
}

html {
  box-sizing: border-box;
}

*,
*:before,
*:after {
  margin: 0;
  padding: 0;
  box-sizing: inherit;
}

.msger {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 100%;
  max-width: 867px;
  height: 90vh;
  margin: 25px 10px;
  border: var(--border);
  border-radius: 5px;
  background: var(--msger-bg);
  box-shadow: 0 15px 15px -5px rgba(0, 0, 0, 0.2);
}

.msger-header {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: var(--border);
  background: #eee;
  color: #666;
}

.msger-chat {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
}
.msger-chat::-webkit-scrollbar { width: 6px; }
.msger-chat::-webkit-scrollbar-track { background: #ddd; }
.msger-chat::-webkit-scrollbar-thumb { background: #bdbdbd; }

.msg {
  display: flex;
  align-items: flex-end;
  margin-bottom: 10px;
}
.msg:last-of-type { margin: 0; }

.msg-bubble {
  max-width: 450px;
  padding: 15px;
  border-radius: 15px;
  background: var(--left-msg-bg);
}

.msg-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
.msg-info-name { font-weight: bold; }
.msg-info-time { font-size: 0.85em; }

.left-msg .msg-bubble { border-bottom-left-radius: 0; }
.right-msg { flex-direction: row-reverse; }
.right-msg .msg-bubble {
  background: var(--right-msg-bg);
  color: #fff;
  border-bottom-right-radius: 0;
}
.right-msg .msg-img { margin-left: 10px; }

.msger-inputarea {
  display: flex;
  padding: 10px;
  border-top: var(--border);
  background: #eee;
}
.msger-inputarea * {
  padding: 10px;
  border: none;
  border-radius: 3px;
  font-size: 1em;
}
.msger-input {
  flex: 1;
  background: #ddd;
}
.msger-send-btn {
  margin-left: 10px;
  background: rgb(0, 196, 65);
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.23s;
}
.msger-send-btn:hover { background: rgb(0, 180, 50); }

.chat-popup {
  display: none;
  position: fixed;
  bottom: 0;
  right: 15px;
  border: 3px solid #f1f1f1;
  z-index: 999;
  background: #fff;
}

.form-container {
  max-width: 400px;
  background-color: white;
  border-radius: 6px;
  overflow: hidden;
}
</style>
<?php
/*************************************************
 *  notifications.php
 *  Optimized & Clean Version
 *************************************************/

session_start();
include '../dbcon.php';
//print_r($_GET['notif_id']);die();
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
     file_put_contents('chat_debug.txt', print_r($_POST,true)."\n", FILE_APPEND);
//print_r($_POST,true);die();
    $sender_type   = mysqli_real_escape_string($con, $_POST['sender_type']);
    $sender_id     = intval($_POST['sender_id']);
    $receiver_type =$_POST['receiver_type'];
    $receiver_id   = intval($_POST['receiver_id']);
    $behaviour_id  = intval($_POST['behaviour_id'])??'';
    $message       = mysqli_real_escape_string($con, $_POST['message']);
    $session       = date('Y'); 
  if($sender_type=='teacher' && $receiver_type==''){
        print_r(1);die();
         /* Insert comment */
             /* Insert comment */
           mysqli_query($con, "
                INSERT INTO rrsv_behaviour_comments
                (behaviour_id, user_type, user_id, student_id, comment, created_at)
                VALUES
                (NULL, '$sender_type', $sender_id, NULL, '$message', NOW())
            ");


            $comment_id = mysqli_insert_id($con);

            /* Notification */
            mysqli_query($con, "
                INSERT INTO rrsv_notifications
                (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, comment_id, message, seen, created_at)
                VALUES
                ('', '', '$sender_type', $sender_id, '', $comment_id, '$message', 0, NOW())
            ");
    }
    if($sender_type=='teacher' && $receiver_type==''){
        print_r(2);die();
        /* Insert comment */
            mysqli_query($con, "
                INSERT INTO rrsv_behaviour_comments
                (behaviour_id, user_type, user_id, student_id, comment, created_at)
                VALUES
                (NULL, '$sender_type', $sender_id, NULL, '$message', NOW())
            ");

            $comment_id = mysqli_insert_id($con);

            /* Notification */
            mysqli_query($con, "
                INSERT INTO rrsv_notifications
                (receiver_type, receiver_id, sender_type, sender_id, behaviour_id, comment_id, message, seen, created_at)
                VALUES
                ('', '', '$sender_type', $sender_id, '', $comment_id, '$message', 0, NOW())
            ");
            
    }
    
   if (!empty($message) && $receiver_type !== 'teacher' && $receiver_type !== 'student') {
           // print_r(3);die();
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
if(isset($_GET['notif_id'])){
    $notif_id = intval($_GET['notif_id']);

    mysqli_query($con, "UPDATE rrsv_notifications 
        SET seen = 1 WHERE id = '$notif_id'");
}
/* ================= AJAX HANDLERS ================= */
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if ($_POST['action'] === 'load_chat') {

//          $behaviour_id = intval($_POST['behaviour_id'] ?? 0);

//     if ($behaviour_id > 0) {
//         $sql = "
//             SELECT c.comment, c.created_at, c.user_type,s.scl_name,s.scl_reg_no,s.scl_class,
//             CASE 
//                 WHEN c.user_type='teacher' THEN t.full_name
//                 ELSE s.scl_name
//             END AS user_name
//             FROM rrsv_behaviour_comments c
//             LEFT JOIN rrsv_teacher t 
//                 ON c.user_type='teacher' AND c.user_id=t.id
//             LEFT JOIN rrsv_student_registration s 
//                 ON c.user_type='student' AND c.user_id=s.id
//             WHERE c.behaviour_id=$behaviour_id OR (c.behaviour_id IS NULL OR c.behaviour_id = 0)
//             ORDER BY c.created_at ASC
//         ";
//     } else {
//       $sql = "
//     SELECT 
//         c.comment,
//         c.created_at,
//         c.user_type,
//         s.scl_name,s.scl_reg_no,s.scl_class,
//         CASE 
//             WHEN c.user_type='teacher' THEN t.full_name
//             ELSE s.scl_name
//         END AS user_name
//     FROM rrsv_behaviour_comments c
//     LEFT JOIN rrsv_teacher t 
//         ON c.user_type='teacher' AND c.user_id=t.id
//     LEFT JOIN rrsv_student_registration s 
//         ON c.user_type='student' AND c.user_id=s.id
//     WHERE (c.behaviour_id IS NULL OR c.behaviour_id = 0)
//     ORDER BY c.created_at ASC
// ";

//     }


//         $res = mysqli_query($con, $sql);
//         while ($row = mysqli_fetch_assoc($res)) {
           
//             if ($row['user_type'] === 'teacher') {
//                 $uniqueKey = 'teacher|' . $row['user_name'] . '|' . $row['comment'] . '|' . $row['created_at'];
            
//                 if (isset($shownMessages[$uniqueKey])) {
//                     continue;
//                 }
//                 $shownMessages[$uniqueKey] = true;
//             }


//             $align = ($row['user_type'] === $user_role) ? 'right' : 'left';

//             echo "
//             <div class='msg {$align}-msg'>
//                 <div class='msg-bubble'>
//                     <div class='msg-info-name'>".htmlspecialchars($row['user_name'])."</div>
//                     <div class='msg-text'>".htmlspecialchars($row['comment'])."</div>
//                     <div class='msg-info-time'>".date('H:i',strtotime($row['created_at']))."</div>
//                 </div>
//             </div>";
//         }
//         exit;
//     }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'load_chat') {

        /* ---------- USER CONTEXT ---------- */
        // $user_role = 'teacher' | 'student'
        // $user_id   = logged in user id

        /* ---------- WHERE CONDITION ---------- */
        if ($user_role === 'student') {
            $where = "
                (
                    (n.sender_type='student' AND n.sender_id=$user_id)
                    OR
                    (n.receiver_type='student' AND n.receiver_id=$user_id)
                )
            ";
        } else {
            $where = "
                (
                    (n.sender_type='teacher' AND n.sender_id=$user_id)
                    OR
                    (n.receiver_type='teacher' AND n.receiver_id=$user_id)
                )
            ";
        }

        /* ---------- SQL ---------- */
        $sql = "
            SELECT 
                n.id,
                n.message,
                n.created_at,
                n.sender_type,
                n.sender_id,
                n.receiver_type,
                n.receiver_id,

                ts.full_name AS teacher_sender,
                ss.scl_name  AS student_sender,
                sr.scl_class,
                sr.scl_reg_no,
                ss.scl_class as class,
                ss.scl_reg_no as reg,

                tr.full_name AS teacher_receiver,
                sr.scl_name  AS student_receiver

            FROM rrsv_notifications n

            LEFT JOIN rrsv_teacher ts
                ON n.sender_type='teacher' AND n.sender_id=ts.id

            LEFT JOIN rrsv_student_registration ss
                ON n.sender_type='student' AND n.sender_id=ss.id

            LEFT JOIN rrsv_teacher tr
                ON n.receiver_type='teacher' AND n.receiver_id=tr.id

            LEFT JOIN rrsv_student_registration sr
                ON n.receiver_type='student' AND n.receiver_id=sr.id

            WHERE $where
            ORDER BY n.created_at ASC
        ";

        $res = mysqli_query($con, $sql);

        /* ---------- GROUPED MESSAGE STORE ---------- */
        $messages = [];

        while ($row = mysqli_fetch_assoc($res)) {

            /* ===== GROUP ONLY TEACHER MESSAGES ===== */
            if ($row['sender_type'] === 'teacher') {

                // Unique key for same teacher message
                $msgKey = $row['sender_id'] . '|' . $row['message'] . '|' . $row['created_at'];

                if (!isset($messages[$msgKey])) {
                    $messages[$msgKey] = [
                        'teacher'  => $row['teacher_sender'],
                        'comment'  => $row['message'],
                        'time'     => $row['created_at'],
                        'students' => []
                    ];
                }

                // Attach students
                if (!empty($row['student_receiver'])) {
                    $messages[$msgKey]['students'][] = [
                        'name'  => $row['student_receiver'],
                        'class' => $row['scl_class'],
                        'reg'   => $row['scl_reg_no']
                    ];
                }

            } else {
                /* ===== STUDENT → TEACHER OR STUDENT CHAT ===== */
                $msgKey = 'single|' . $row['id'];

                $messages[$msgKey] = [
                    'sender_type' => $row['sender_type'],
                    'sender_id'   => $row['sender_id'],
                    'sender'      => $row['student_sender'],
                    'class'       => $row['class'] ?? '',
                     'reg'         => $row['reg'] ?? '',
                    'comment'     => $row['message'],
                    'time'        => $row['created_at']
                ];
            }
        }
//print_r($messages);die();
        /* ---------- RENDER CHAT ---------- */
       foreach ($messages as $msg) {

    /* ===== TEACHER GROUP MESSAGE ===== */
    if (isset($msg['students'])) {

        $studentCount = count($msg['students']);
        $studentsHtml = '';
        foreach ($msg['students'] as $stu) {
            $studentsHtml .= '• ' . htmlspecialchars($stu['name']) .
                             ' (Class: ' . htmlspecialchars($stu['class']) .
                             ', Reg: ' . htmlspecialchars($stu['reg']) . ")<br>";
        }

        echo "
        <div class='msg right-msg'>
            <div class='msg-bubble'>

                <div class='msg-info-name'>
                    <strong>Teacher:</strong> " . htmlspecialchars($msg['teacher']) . "
                </div>

                <div class='msg-text'>
                    " . nl2br(htmlspecialchars($msg['comment'])) . "
                </div>

                <div class='msg-info-time'>
                    " . date('d-m-Y h:i A', strtotime($msg['time'])) . "
                </div>

                <div class='msg-students'>
                    <button class='toggle-students-btn'>Sent To ({$studentCount}) ▼</button>
                    <div class='students-list' style='display:none;'>
                        {$studentsHtml}
                    </div>
                </div>

            </div>
        </div>";
        
    } else {
        /* ===== NORMAL MESSAGE ===== */
        $align = (
            $msg['sender_type'] === $user_role &&
            intval($msg['sender_id']) === intval($user_id)
        ) ? 'right' : 'left';

        echo "
        <div class='msg {$align}-msg'>
            <div class='msg-bubble'>

                <div class='msg-info-name'>
                    " . htmlspecialchars($msg['sender']) . "
                    " . (!empty($msg['class']) ? "
                        <span class='text-xs text-gray-500'>
                            ({$msg['class']} - {$msg['reg']})
                        </span>
                    " : "") . "
                </div>

                <div class='msg-text'>
                    " . nl2br(htmlspecialchars($msg['comment'])) . "
                </div>

                <div class='msg-info-time'>
                    " . date('H:i', strtotime($msg['time'])) . "
                </div>

            </div>
        </div>";
    }
}


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
$where = ($user_role==='student')? "n.receiver_id IN ($ids)": "n.receiver_id=$user_id";

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
// OR (n.behaviour_id IS NULL OR n.behaviour_id = 0)
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
            <?php $i=1; while($n=mysqli_fetch_assoc($notifications)){
           // print_r($n);die();
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($n['sender_name']) ?></td>
                    <td><?= htmlspecialchars($n['message']) ?></td>
                    <td><?= date('d-m-Y h:i A',strtotime($n['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-xs btn-primary" onclick="openForm(<?= $n['id'] ?>)">Message</button> 
                                            <!-- Chat Popup -->
                                            <div class="chat-popup" id="form_<?= $n['id'] ?>">
                                             <form action="save_comment.php" method="POST" class="form-container">
                <section class="msger">
                  <header class="msger-header">
                    <div class="msger-header-title"><?= htmlspecialchars($n['message']) ?></div>
                    <button type="button" class="btn btn-danger btn-xs" onclick="closeForm(<?= $n['id'] ?>)">X</button>
                  </header>

                  <main class="msger-chat">
                    <?php
                  if ($_SESSION['role']=== 'teacher') {
                        $teacher_id = $user_id ; 
                        $student_id = $n['sender_id'] ; 
                    } else {
                        $teacher_id = $n['sender_id'] ;
                        $student_id = $user_id ; 
                    }
                    $comments = mysqli_query($con, "
                        SELECT c.*, 
                               CASE 
                                    WHEN c.user_type = 'teacher' THEN t.full_name
                                    WHEN c.user_type = 'student' THEN s.scl_name
                               END AS user_name
                        FROM rrsv_behaviour_comments AS c
                        LEFT JOIN rrsv_teacher AS t ON c.user_type = 'teacher' AND c.user_id = t.id
                        LEFT JOIN rrsv_student_registration AS s ON c.user_type = 'student' AND c.user_id = s.id
                        WHERE c.behaviour_id = {$n['id']}
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
                          <div class="msg-info-time"><?= date('h:i A', strtotime($c['created_at'])) ?></div>
                          <div class="msg-text"><?= htmlspecialchars($c['comment']) ?></div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </main>

                  <div class="msger-inputarea">
                    <input type="hidden" name="behaviour_id" value="<?= $n['id'] ?>">
                    <input type="hidden" name="user_type" value="<?= $_SESSION['role'] ?>">
                    <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                    <input type="hidden" name="student_id" value="<?=$student_id ?>">
                    <textarea name="comment" class="msger-input" placeholder="Enter your message..." required></textarea>
                    <button type="submit" class="msger-send-btn">Send</button>
                  </div>
                </section>
              </form>
             </div>
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

     <div class="chat-input">
            <input type="hidden" id="chat_sender_type" value="<?= $user_role ?>">
            <input type="hidden" id="chat_sender_id" value="<?= $user_id ?>">
           
                <?php if ($user_role=='teacher'){?>
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
             <?php } ?>
        </div>

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
    //setInterval(loadChat,4000);
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

        if( message && sender_type ){
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

function openForm(id) {
  closeAllForms();
  document.getElementById("form_" + id).style.display = "block";
}
function closeForm(id) {
  document.getElementById("form_" + id).style.display = "none";
}
function closeAllForms() {
  document.querySelectorAll('.chat-popup').forEach(el => el.style.display = 'none');
}
</script>
<script>
document.addEventListener('click', function(e) {
    if(e.target.matches('.toggle-students-btn')) {
        const list = e.target.nextElementSibling;
        if(list.style.display === 'none') {
            list.style.display = 'block';
            e.target.textContent = e.target.textContent.replace('▼','▲');
        } else {
            list.style.display = 'none';
            e.target.textContent = e.target.textContent.replace('▲','▼');
        }
    }
});
</script>

</body>
</html>
