<?php
session_start();
include '../dbcon.php';

// 🔒 Auth Check
if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['teacher', 'student'])) {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}

$currentUserRole = $_SESSION['role'];
$currentUserName = $_SESSION['user_data']['name']; // stored in session
$currentUserClass = $_SESSION['user_data']['class'] ?? ''; // student class
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Classroom Chat</title>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

<style>
:root {
    --header-bg: #0f7a66;
    --accent: #00a884;
    --teacher-bg: #dcf8c6;
    --student-bg: #fff;
    --muted: #8a8a8a;
    --chat-bg: #e5ddd5 url("https://cdn.jsdelivr.net/gh/zenorocha/clipboard-copy@master/demo/assets/whatsapp-bg.png") repeat;
    --max-width: 900px;
}

* { box-sizing: border-box; }
html, body { margin:0; height:100%; font-family: Roboto, system-ui, -apple-system, Segoe UI, Arial, sans-serif; background: #f3f3f3; }

.container { max-width: var(--max-width); margin:20px auto; border-radius:6px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,.12); background:#fff; display:flex; flex-direction:column; }

.chat-header { display:flex; align-items:center; padding:12px 14px; background: var(--header-bg); color:#fff; }
.chat-header .avatar { width:46px; height:46px; border-radius:50%; overflow:hidden; margin-right:12px; flex:0 0 46px; }
.chat-header .avatar img { width:100%; height:100%; object-fit:cover; }
.chat-header .meta { flex:1; }
.chat-header .meta .name { font-weight:700; font-size:16px; }
.chat-header .meta .status { font-size:12px; color:rgba(255,255,255,.85); }

.conversation { flex:1; background: var(--chat-bg); padding:18px; overflow-y:auto; display:flex; flex-direction:column; }
.message { max-width:70%; padding:10px 12px; border-radius:8px; margin:6px 0; line-height:1.32; position:relative; word-wrap:break-word; }
.message .sender { font-weight:600; font-size:12px; margin-bottom:2px; display:block; }
.message .meta { display:flex; justify-content:flex-end; font-size:11px; color: var(--muted); margin-top:4px; gap:6px; align-items:center; }
.message.sent { align-self:flex-end; border-radius:8px 0 8px 8px; text-align:right; background: var(--teacher-bg); }
.message.received { align-self:flex-start; border-radius:0 8px 8px 8px; text-align:left; background: var(--student-bg); }
.tick { width:16px; height:12px; display:inline-block; vertical-align:middle; margin-left:4px; }

#typingIndicator { font-size:12px; color:#666; margin:4px 12px; display:none; }

.composer { display:flex; padding:12px; border-top:1px solid #ddd; gap:10px; background:#fff; }
.composer input[type="text"] { flex:1; padding:10px 12px; border-radius:20px; border:1px solid #ddd; outline:none; font-size:14px; }
.composer button { background:var(--accent); border:none; color:#fff; padding:10px 14px; border-radius:20px; cursor:pointer; font-weight:600; }

@media(max-width:600px){ .container{margin:8px;border-radius:0;} .conversation{height:60vh;} }
</style>

<script src="../assets/js/jquery-2.2.3.min.js"></script>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="chat-header">
        <div class="avatar">
            <img src="https://i.pravatar.cc/150?img=12" alt="Avatar">
        </div>
        <div class="meta">
            <div class="name">Classroom Chat</div>
            <div class="status">online</div>
        </div>
    </div>

    <!-- Conversation -->
    <div class="conversation" id="conversation">
        <div id="messages"></div>
        <div id="typingIndicator">Someone is typing...</div>
    </div>

    <!-- Composer -->
    <form class="composer" id="composer" onsubmit="return false;">
        <input type="text" id="messageInput" placeholder="Type a message" required>
        <button type="submit" id="sendBtn">Send</button>
    </form>
</div>

<script>
function scrollToBottom(){ 
    const conv = document.getElementById('conversation');
    conv.scrollTop = conv.scrollHeight;
}

function addMessage(sender, text, type='received', time=null){
    const container = document.getElementById('messages');
    const msg = document.createElement('div');
    msg.className = 'message ' + type;

    const senderEl = document.createElement('span');
    senderEl.className = 'sender';
    senderEl.textContent = sender;
    msg.appendChild(senderEl);

    const textNode = document.createTextNode(text);
    msg.appendChild(textNode);

    const meta = document.createElement('span');
    meta.className = 'meta';
    const tspan = document.createElement('span');
    tspan.className = 'time';
    tspan.textContent = time || new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
    meta.appendChild(tspan);

    if(type==='sent'){
        const tick = document.createElement('span');
        tick.className='tick';
        tick.innerHTML=`<svg viewBox="0 0 20 14" width="16" height="12">
            <g fill="none" stroke="#4fc3f7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 8.5 6 13.5 11.5 8"></path>
                <path d="M7 8.5 12.5 13.5 19 7"></path>
            </g>
        </svg>`;
        meta.appendChild(tick);
    }

    msg.appendChild(meta);
    container.appendChild(msg);
    scrollToBottom();
}

// Load chat messages with classroom filtering
function loadChat(){
    $.getJSON('notifications_backend.php', function(data){
        $('#messages').html('');
        let typing=false;
        data.forEach(msg => {
            // Classroom filter: show message if receiver_type matches role/class/all
            let show=false;
            if(msg.sender_role==='all') show=true;
            if('<?php echo $currentUserRole;?>'==='teacher') show=true;
            if('<?php echo $currentUserRole;?>'==='student' && (msg.sender_role==='student' && msg.sender_class==='<?php echo $currentUserClass;?>')) show=true;

            if(show){
                let type = 'received';
                if(msg.sender_name==='<?php echo $currentUserName;?>') type='sent';
                addMessage(msg.sender_name, msg.message, type, msg.created_at);
            }
        });

        $('#typingIndicator').toggle(typing);
    });
}

// Send message
function sendMessage(){
    const text = $('#messageInput').val().trim();
    if(!text) return;

    // typing indicator off
    $('#typingIndicator').hide();

    $.post('notifications_backend.php', { message: text, receiver_type:'all' }, function(){
        $('#messageInput').val('');
        loadChat();
    });
}

$('#sendBtn').click(sendMessage);
$('#messageInput').keydown(function(e){
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }

    // notify server user is typing
    $.post('notifications_backend.php', { typing:1 });
});

// Auto refresh and typing status
setInterval(loadChat, 2000);
setInterval(function(){ $.post('notifications_backend.php', { typing_check:1 }, function(res){
    $('#typingIndicator').toggle(res.typing>0);
},'json');}, 1500);

loadChat();
</script>
</body>
</html>
