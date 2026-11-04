
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

body {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-image: var(--body-bg);
  font-family: Helvetica, sans-serif;
}

.msger {
  display: flex;
  flex-flow: column wrap;
  justify-content: space-between;
  width: 100%;
  max-width: 867px;
  margin: 25px 10px;
  height: calc(100% - 50px);
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
.msger-chat::-webkit-scrollbar {
  width: 6px;
}
.msger-chat::-webkit-scrollbar-track {
  background: #ddd;
}
.msger-chat::-webkit-scrollbar-thumb {
  background: #bdbdbd;
}
.msg {
  display: flex;
  align-items: flex-end;
  margin-bottom: 10px;
}
.msg:last-of-type {
  margin: 0;
}
.msg-img {
  width: 50px;
  height: 50px;
  margin-right: 10px;
  background: #ddd;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  border-radius: 50%;
}
.msg-bubble {
  max-width: 450px;
  padding: 15px;
  border-radius: 15px;
  background: var(--left-msg-bg);
}
.msg-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}
.msg-info-name {
  margin-right: 10px;
  font-weight: bold;
}
.msg-info-time {
  font-size: 0.85em;
}

.left-msg .msg-bubble {
  border-bottom-left-radius: 0;
}

.right-msg {
  flex-direction: row-reverse;
}
.right-msg .msg-bubble {
  background: var(--right-msg-bg);
  color: #fff;
  border-bottom-right-radius: 0;
}
.right-msg .msg-img {
  margin: 0 0 0 10px;
}

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
.msger-send-btn:hover {
  background: rgb(0, 180, 50);
}


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
  max-width: 300px;
  padding: 10px;
  background-color: white;
}

/* Full-width textarea */
.form-container textarea {
  width: 100%;
  padding: 15px;
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
  padding: 16px 20px;
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
<?php
session_start();
include '../../../include/config.php';
include '../../dbcon.php';

// 🔒 Access Control
if (!isset($_SESSION['user_data']) || $_SESSION['role'] == 'teacher||student') {
  header("Location: ../../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];
$teacher_id = $teacher['id'];

// 🧠 Get class & session info
$student_query = mysqli_query($con, "SELECT scl_class, scl_session FROM rrsv_student_registration WHERE id='$student_id'");
$student_data = mysqli_fetch_assoc($student_query);
$class_name = $student_data['scl_class'];
$session = $student_data['scl_session'];

// 🎯 Get class ID
$class_query = mysqli_query($con, "SELECT id FROM rrsv_class WHERE class_name='$class_name' LIMIT 1");
$class_row = mysqli_fetch_assoc($class_query);
$class_id = $class_row['id'];

// 🗨️ Fetch all behaviour comments (top-level)
$comments = mysqli_query($con, "
    SELECT * FROM rrsv_student_behaviour 
    WHERE student_id='$student_id' AND parent_id=0 
    ORDER BY created_at ASC
");

// 💬 Get replies
function getReplies($parent_id, $con) {
  $sql = "SELECT * FROM rrsv_student_behaviour WHERE parent_id='$parent_id' ORDER BY created_at ASC";
  $res = mysqli_query($con, $sql);
  $data = [];
  while ($r = mysqli_fetch_assoc($res)) {
    $data[] = $r;
  }
  return $data;
}
$current_session = date('Y'); 
$behaviours = mysqli_query($con, "SELECT 
    b.*,
    s.scl_name AS student_name,
    s.scl_class AS student_class,
    t.full_name AS teacher_name
FROM rrsv_student_behaviour AS b
LEFT JOIN rrsv_student_registration AS s ON b.student_id = s.id
LEFT JOIN rrsv_teacher AS t ON b.teacher_id = t.id
WHERE b.scl_session = '$current_session'
ORDER BY b.id DESC");
?>

<!DOCTYPE html>
<html>
<?php include('../../header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Header -->
  <header class="main-header">
    <a href="#" class="logo">
      <span class="logo-mini"><b>MSB</b></span>
      <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
    </a>
    <?php include '../../top_navbar.php'; ?>
  </header>

  <!-- Sidebar -->
  <?php include '../../left_navbar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Student Behaviour Comments</h1>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary direct-chat direct-chat-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Behaviour Chat</h3>
            </div>

            <div class="box-body">
                

           <div class="panel-heading"><a class="pull-right" href="#">View all</a> <h4>Student</h4></div>
   			<div class="panel-body">
   			      <?php while ($b = mysqli_fetch_assoc($behaviours)): ?>
             <?= htmlspecialchars($b['des']) ?>
              <hr>
              <div class="well well-sm">
                <div class="media">
                    <a href="#" class="thumbnail pull-left">
                        <img src="//placehold.it/80" class="media-object">
                    </a>
                    <div class="media-body">
                         <h4 class="media-heading"><?= htmlspecialchars($b['des']) ?></h4>
                         <small class="text-muted">
                              <strong>Student:</strong> <?= htmlspecialchars($b['student_name']) ?> 
        <strong>Date:</strong> <?= htmlspecialchars($b['date']) ?> |
        <strong>Unit:</strong> <?= htmlspecialchars($b['unit']) ?> |
        <strong>Session:</strong> <?= htmlspecialchars($b['scl_session']) ?>
      </small>
                		<p><span class="label label-info">10 photos</span> <span class="label label-primary">89 followers</span></p>
                        <p>
                            
                            <a class="btn btn-xs btn-default" href="#" onclick="openForm(<?= $b['id'] ?>)"><span class="glyphicon glyphicon-comment"></span> Message</a>
                            <a class="btn btn-xs btn-default" href="#"><span class="glyphicon glyphicon-heart"></span> Favorite</a>
                        </p>
                    </div>
                </div>
               </div>
               
               
               
<!--<button class="open-button" onclick="openForm()">Chat</button>-->

<div class="chat-popup" id="myForm">
<form action="save_comment.php" method="POST">
     <section class="msger">
  <header class="msger-header">
    <div class="msger-header-title">
      <i class="fas fa-comment-alt"></i> <?= htmlspecialchars($b['des']) ?>
    </div>
    <div class="msger-header-options">
      <span><i class="fas fa-cog"></i></span>
    </div>
  </header>

        <main class="msger-chat">
          <?php
          $comments = mysqli_query($con, "SELECT * FROM rrsv_behaviour_comments WHERE behaviour_id = {$b['id']} ORDER BY created_at ASC");
          while ($c = mysqli_fetch_assoc($comments)):
              $align = ($c['user_type'] == 'teacher') ? 'left' : 'right';
          ?>
            <div class="msg <?= $align ?>-msg">
              <div class="msg-bubble">
                <div class="msg-info">
                  <div class="msg-info-name"><?= ucfirst($c['user_type']) ?></div>
                  <div class="msg-info-time"><?= date('H:i', strtotime($c['created_at'])) ?></div>
                </div>
                <div class="msg-text"><?= htmlspecialchars($c['comment']) ?></div>
              </div>
            </div>
          <?php endwhile; ?>
        </main>
  <!--<main class="msger-chat">-->
  <!--  <div class="msg left-msg">-->
  <!--    <div-->
  <!--     class="msg-img"-->
  <!--     style="background-image: url(https://image.flaticon.com/icons/svg/327/327779.svg)"-->
  <!--    ></div>-->

  <!--    <div class="msg-bubble">-->
  <!--      <div class="msg-info">-->
  <!--        <div class="msg-info-name">BOT</div>-->
  <!--        <div class="msg-info-time">12:45</div>-->
  <!--      </div>-->

  <!--      <div class="msg-text">-->
  <!--        Hi, welcome to SimpleChat! Go ahead and send me a message. 😄-->
  <!--      </div>-->
  <!--    </div>-->
  <!--  </div>-->

  <!--  <div class="msg right-msg">-->
  <!--    <div-->
  <!--     class="msg-img"-->
  <!--     style="background-image: url(https://image.flaticon.com/icons/svg/145/145867.svg)"-->
  <!--    ></div>-->

  <!--    <div class="msg-bubble">-->
  <!--      <div class="msg-info">-->
  <!--        <div class="msg-info-name">Sajad</div>-->
  <!--        <div class="msg-info-time">12:46</div>-->
  <!--      </div>-->

  <!--      <div class="msg-text">-->
  <!--        You can change your name in JS section!-->
  <!--      </div>-->
  <!--    </div>-->
  <!--  </div>-->
  <!--</main>-->

  <div class="msger-inputarea">
      <input type="hidden" name="behaviour_id" value="<?= $b['id'] ?>">
          <input type="hidden" name="user_type" value="<?= $_SESSION['role'] ?>"> <!-- teacher/student -->
          <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
          <input type="hidden" name="student_id" value="<?=  $b['student_id'] ?>">
    <textarea type="text" name="comment" class="msger-input" placeholder="Enter your message..."></textarea>
    <button type="submit" class="msger-send-btn">Send</button>
  </div>
</section>
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
            <?php endwhile; ?>
            </div>
      			                    
                
             
              
            </div>







            <div class="box-footer">
              <form id="addCommentForm">
                <div class="input-group">
                  <input type="text" name="comment_text" class="form-control" placeholder="Type your comment..." required>
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat">Send</button>
                  </span>
                </div>
              </form>
            </div>
          </div>
        </div> <!-- /.col -->
      </div> <!-- /.row -->
    </section>
  </div>

  <?php include '../../footer.php'; ?>
  <div class="control-sidebar-bg"></div>
</div>

<!-- Scripts -->
<script src="../../assets/js/jquery-2.2.3.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
<script src="../../assets/js/app.min.js"></script>

<script>
$(document).on('submit', '#addCommentForm', function(e){
  e.preventDefault();
  $.post('save_comment.php', $(this).serialize(), function(){
    $('#chatBox').load(location.href + ' #chatBox>*');
    $('#addCommentForm')[0].reset();
  });
});

$(document).on('submit', '.replyForm', function(e){
  e.preventDefault();
  const parentId = $(this).data('parent');
  const text = $(this).find('.replyInput').val();
  $.post('save_comment.php', { parent_id: parentId, comment_text: text }, function(){
    $('#chatBox').load(location.href + ' #chatBox>*');
  });
});



const msgerForm = get(".msger-inputarea");
const msgerInput = get(".msger-input");
const msgerChat = get(".msger-chat");

const BOT_MSGS = [
  "Hi, how are you?",
  "Ohh... I can't understand what you trying to say. Sorry!",
  "I like to play games... But I don't know how to play!",
  "Sorry if my answers are not relevant. :))",
  "I feel sleepy! :("
];

// Icons made by Freepik from www.flaticon.com
const BOT_IMG = "https://image.flaticon.com/icons/svg/327/327779.svg";
const PERSON_IMG = "https://image.flaticon.com/icons/svg/145/145867.svg";
const BOT_NAME = "BOT";
const PERSON_NAME = "Sajad";

msgerForm.addEventListener("submit", (event) => {
  event.preventDefault();

  const msgText = msgerInput.value;
  if (!msgText) return;

  appendMessage(PERSON_NAME, PERSON_IMG, "right", msgText);
  msgerInput.value = "";

  botResponse();
});

function appendMessage(name, img, side, text) {
  //   Simple solution for small apps
  const msgHTML = `
    <div class="msg ${side}-msg">
      <div class="msg-img" style="background-image: url(${img})"></div>

      <div class="msg-bubble">
        <div class="msg-info">
          <div class="msg-info-name">${name}</div>
          <div class="msg-info-time">${formatDate(new Date())}</div>
        </div>

        <div class="msg-text">${text}</div>
      </div>
    </div>
  `;

  msgerChat.insertAdjacentHTML("beforeend", msgHTML);
  msgerChat.scrollTop += 500;
}

function botResponse() {
  const r = random(0, BOT_MSGS.length - 1);
  const msgText = BOT_MSGS[r];
  const delay = msgText.split(" ").length * 100;

  setTimeout(() => {
    appendMessage(BOT_NAME, BOT_IMG, "left", msgText);
  }, delay);
}

// Utils
function get(selector, root = document) {
  return root.querySelector(selector);
}

function formatDate(date) {
  const h = "0" + date.getHours();
  const m = "0" + date.getMinutes();

  return `${h.slice(-2)}:${m.slice(-2)}`;
}

function random(min, max) {
  return Math.floor(Math.random() * (max - min) + min);
}

</script>

</body>
</html>
