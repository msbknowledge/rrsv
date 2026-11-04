
<?php
session_start();
require_once 'dbConnection.php';

/*
|--------------------------------------------------------------------------
| CHAT LOGIN CHECK
|--------------------------------------------------------------------------
*/
if (
    !isset($_SESSION['chat_logged_in']) ||
    $_SESSION['chat_logged_in'] !== true
) {
    header("Location: chat_login.php");
    exit;
}

$current_user_id = (int)$_SESSION['chat_user_id'];
$current_username = $_SESSION['chat_username'];
$current_role = $_SESSION['chat_role'];


/*
|--------------------------------------------------------------------------
| AJAX REQUESTS
|--------------------------------------------------------------------------
*/
if (isset($_GET['ajax'])) {

    header('Content-Type: application/json; charset=utf-8');

    $ajax = $_GET['ajax'];


    /*
    |--------------------------------------------------------------------------
    | GET USERS
    |--------------------------------------------------------------------------
    */
    if ($ajax === 'users') {

    $users = [];

    $sql = "
        SELECT
            id,
            username,
            role,
            status
        FROM chat_user
        WHERE id != ?
          AND status = 'active'
        ORDER BY username ASC
    ";

    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Prepare failed: ' . mysqli_error($con)
        ]);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $current_user_id
    );

    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => false,
            'message' => 'Execute failed: ' . mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Get result failed: ' . mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {

        $row['unread_count'] = 0;
        $row['last_message'] = '';
        $row['last_message_time'] = null;

        $users[] = $row;
    }

    echo json_encode([
        'success' => true,
        'current_user_id' => $current_user_id,
        'users' => $users
    ]);

    exit;
}


    /*
    |--------------------------------------------------------------------------
    | GET MESSAGES
    |--------------------------------------------------------------------------
    */
    if ($ajax === 'messages') {

        $receiver_id = (int)($_GET['user_id'] ?? 0);

        if ($receiver_id <= 0) {

            echo json_encode([
                'success' => false,
                'message' => 'Invalid user.'
            ]);

            exit;
        }


        /*
        | Mark received messages as read
        */
        $read_stmt = mysqli_prepare($con, "
            UPDATE chat_messages
            SET is_read = 1
            WHERE sender_id = ?
              AND receiver_id = ?
              AND is_read = 0
              AND status = 'sent'
        ");

        mysqli_stmt_bind_param(
            $read_stmt,
            "ii",
            $receiver_id,
            $current_user_id
        );

        mysqli_stmt_execute($read_stmt);


        /*
        | Get conversation
        */
        $stmt = mysqli_prepare($con, "
            SELECT
                m.id,
                m.sender_id,
                m.receiver_id,
                m.message,
                m.message_type,
                m.attachment,
                m.is_read,
                m.created_at,

                u.username AS sender_username,
                u.role AS sender_role

            FROM chat_messages m

            INNER JOIN chat_user u
                ON u.id = m.sender_id

            WHERE
                (
                    m.sender_id = ?
                    AND m.receiver_id = ?
                )
                OR
                (
                    m.sender_id = ?
                    AND m.receiver_id = ?
                )

            AND m.status = 'sent'

            ORDER BY m.created_at ASC, m.id ASC
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "iiii",
            $current_user_id,
            $receiver_id,
            $receiver_id,
            $current_user_id
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $messages = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }


        /*
        | Receiver information
        */
        $user_stmt = mysqli_prepare($con, "
            SELECT id, username, role, status
            FROM chat_user
            WHERE id = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param(
            $user_stmt,
            "i",
            $receiver_id
        );

        mysqli_stmt_execute($user_stmt);

        $user_result = mysqli_stmt_get_result($user_stmt);

        $receiver = mysqli_fetch_assoc($user_result);


        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'receiver' => $receiver
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | SEND MESSAGE
    |--------------------------------------------------------------------------
    */
    if ($ajax === 'send') {

    $receiver_id = (int)($_POST['receiver_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($receiver_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid receiver ID.'
        ]);
        exit;
    }

    if ($message === '') {
        echo json_encode([
            'success' => false,
            'message' => 'Message cannot be empty.'
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Check receiver
    |--------------------------------------------------------------------------
    */

    $check_sql = "
        SELECT id, username, role
        FROM chat_user
        WHERE id = ?
          AND status = 'active'
        LIMIT 1
    ";

    $check_stmt = mysqli_prepare($con, $check_sql);

    if (!$check_stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Receiver query prepare failed: ' . mysqli_error($con)
        ]);
        exit;
    }

    mysqli_stmt_bind_param(
        $check_stmt,
        "i",
        $receiver_id
    );

    if (!mysqli_stmt_execute($check_stmt)) {
        echo json_encode([
            'success' => false,
            'message' => 'Receiver query failed: ' . mysqli_stmt_error($check_stmt)
        ]);
        exit;
    }

    $check_result = mysqli_stmt_get_result($check_stmt);

    if (!$check_result) {
        echo json_encode([
            'success' => false,
            'message' => 'Receiver result failed: ' . mysqli_stmt_error($check_stmt)
        ]);
        exit;
    }

    $receiver = mysqli_fetch_assoc($check_result);

    if (!$receiver) {
        echo json_encode([
            'success' => false,
            'message' => 'Receiver not found or inactive.'
        ]);
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Insert message
    |--------------------------------------------------------------------------
    */

    $insert_sql = "
        INSERT INTO chat_messages
        (
            sender_id,
            receiver_id,
            message,
            message_type,
            is_read,
            status
        )
        VALUES
        (?, ?, ?, 'text', 0, 'sent')
    ";

    $stmt = mysqli_prepare($con, $insert_sql);

    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Insert prepare failed: ' . mysqli_error($con)
        ]);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iis",
        $current_user_id,
        $receiver_id,
        $message
    );

    if (!mysqli_stmt_execute($stmt)) {

        echo json_encode([
            'success' => false,
            'message' => 'Database insert failed: ' . mysqli_stmt_error($stmt)
        ]);

        exit;
    }

    echo json_encode([
        'success' => true,
        'message_id' => mysqli_insert_id($con),
        'message' => 'Message sent successfully.'
    ]);

    exit;
}


    /*
    |--------------------------------------------------------------------------
    | UNREAD TOTAL
    |--------------------------------------------------------------------------
    */
    if ($ajax === 'unread') {

        $stmt = mysqli_prepare($con, "
            SELECT COUNT(*) AS total
            FROM chat_messages
            WHERE receiver_id = ?
              AND is_read = 0
              AND status = 'sent'
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $current_user_id
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $row = mysqli_fetch_assoc($result);

        echo json_encode([
            'success' => true,
            'total' => (int)$row['total']
        ]);

        exit;
    }


    echo json_encode([
        'success' => false,
        'message' => 'Invalid AJAX request.'
    ]);

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Chat</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
        }

        .chat-wrapper {
            height: 100vh;
            display: flex;
            overflow: hidden;
            background: #fff;
        }


        /* =========================================================
           LEFT SIDEBAR
        ========================================================= */

        .sidebar {
            width: 330px;
            min-width: 330px;
            border-right: 1px solid #ddd;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            height: 70px;
            padding: 15px;
            background: #007bff;
            color: #fff;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .my-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .my-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #fff;
            color: #007bff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: bold;
        }

        .my-name {
            font-weight: bold;
            font-size: 15px;
        }

        .my-role {
            font-size: 12px;
            opacity: .85;
        }

        .logout {
            color: #fff;
            text-decoration: none;
            font-size: 13px;
        }


        /* Search */

        .search-box {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .search-box input {
            width: 100%;
            height: 38px;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 0 15px;
            outline: none;
        }


        /* User list */

        .user-list {
            flex: 1;
            overflow-y: auto;
        }

        .user-item {
            height: 72px;
            padding: 10px 15px;

            display: flex;
            align-items: center;

            cursor: pointer;

            border-bottom: 1px solid #f1f1f1;
        }

        .user-item:hover {
            background: #f5f7fa;
        }

        .user-item.active {
            background: #e9f3ff;
        }

        .avatar {
            width: 45px;
            height: 45px;

            min-width: 45px;

            border-radius: 50%;

            background: #007bff;
            color: #fff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: bold;
        }

        .user-info {
            margin-left: 12px;
            min-width: 0;
            flex: 1;
        }

        .user-name-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .user-role {
            font-size: 11px;
            color: #888;
            margin-top: 3px;
        }

        .last-message {
            color: #999;
            font-size: 12px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

            margin-top: 4px;
        }

        .unread {
            background: #dc3545;
            color: #fff;

            min-width: 20px;
            height: 20px;

            padding: 0 5px;

            border-radius: 10px;

            font-size: 11px;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        /* =========================================================
           CHAT AREA
        ========================================================= */

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .chat-header {
            height: 70px;

            padding: 10px 20px;

            border-bottom: 1px solid #ddd;

            display: flex;
            align-items: center;

            background: #fff;
        }

        .chat-header-avatar {
            width: 42px;
            height: 42px;

            border-radius: 50%;

            background: #007bff;
            color: #fff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: bold;
        }

        .chat-header-info {
            margin-left: 12px;
        }

        .chat-header-name {
            font-weight: bold;
            color: #333;
        }

        .chat-header-role {
            font-size: 12px;
            color: #888;
        }


        /* Messages */

        .messages {
            flex: 1;

            padding: 20px;

            overflow-y: auto;

            background: #f5f7fa;
        }

        .empty-chat {
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #999;
            text-align: center;
        }

        .message-row {
            display: flex;
            margin-bottom: 10px;
        }

        .message-row.mine {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;

            padding: 10px 13px;

            border-radius: 12px;

            background: #fff;

            box-shadow: 0 1px 2px rgba(0,0,0,.08);
        }

        .message-row.mine .message-bubble {
            background: #007bff;
            color: #fff;
            border-bottom-right-radius: 3px;
        }

        .message-row:not(.mine) .message-bubble {
            border-bottom-left-radius: 3px;
        }

        .message-text {
            font-size: 14px;
            line-height: 1.4;

            white-space: pre-wrap;
            word-break: break-word;
        }

        .message-time {
            font-size: 10px;
            margin-top: 5px;
            opacity: .65;
            text-align: right;
        }


        /* =========================================================
           SEND BOX
        ========================================================= */

        .send-box {
            min-height: 65px;

            padding: 10px;

            border-top: 1px solid #ddd;

            display: flex;
            gap: 10px;

            background: #fff;
        }

        .send-box textarea {
            flex: 1;

            height: 45px;

            resize: none;

            border: 1px solid #ddd;
            border-radius: 22px;

            padding: 12px 16px;

            outline: none;

            font-family: inherit;
        }

        .send-box button {
            width: 80px;
            height: 45px;

            border: none;
            border-radius: 22px;

            background: #007bff;
            color: #fff;

            cursor: pointer;

            font-weight: bold;
        }

        .send-box button:hover {
            background: #0069d9;
        }

        .send-box button:disabled {
            background: #aaa;
            cursor: not-allowed;
        }


        /* Mobile */

        @media (max-width: 700px) {

            .sidebar {
                width: 85px;
                min-width: 85px;
            }

            .sidebar-header {
                justify-content: center;
            }

            .my-user > div:not(.my-avatar) {
                display: none;
            }

            .logout {
                display: none;
            }

            .search-box {
                display: none;
            }

            .user-info {
                display: none;
            }

            .user-item {
                justify-content: center;
                padding: 10px;
            }

            .avatar {
                width: 45px;
                min-width: 45px;
            }

            .unread {
                position: absolute;
                margin-left: 30px;
                margin-top: -30px;
            }

            .message-bubble {
                max-width: 85%;
            }
        }

    </style>

</head>

<body>

<div class="chat-wrapper">


    <!-- =========================================================
         SIDEBAR
    ========================================================== -->

    <div class="sidebar">

        <div class="sidebar-header">

            <div class="my-user">

                <div class="my-avatar">
                    <?= strtoupper(substr($current_username, 0, 1)) ?>
                </div>

                <div>

                    <div class="my-name">
                        <?= htmlspecialchars($current_username) ?>
                    </div>

                    <div class="my-role">
                        <?= htmlspecialchars($current_role) ?>
                    </div>

                </div>

            </div>

            <a href="chat_logout.php"
               class="logout">
                Logout
            </a>

        </div>


        <div class="search-box">

            <input
                type="text"
                id="searchUser"
                placeholder="Search users..."
            >

        </div>


        <div
            class="user-list"
            id="userList">

            <div style="padding:30px;text-align:center;color:#999;">
                Loading...
            </div>

        </div>

    </div>


    <!-- =========================================================
         CHAT AREA
    ========================================================== -->

    <div class="chat-area">


        <!-- Chat Header -->

        <div
            class="chat-header"
            id="chatHeader">

            <div style="color:#999;">
                Select a user to start chatting
            </div>

        </div>


        <!-- Messages -->

        <div
            class="messages"
            id="messages">

            <div class="empty-chat">

                <div>
                    <div style="font-size:40px;">💬</div>
                    <div>
                        Select a user to start conversation
                    </div>
                </div>

            </div>

        </div>


        <!-- Send -->

        <form
            class="send-box"
            id="sendForm">

            <textarea
                id="messageInput"
                placeholder="Type a message..."
                disabled
            ></textarea>

            <button
                type="submit"
                id="sendButton"
                disabled>
                Send
            </button>

        </form>

    </div>

</div>


<script>

let currentUserId = <?= $current_user_id ?>;

let selectedUserId = 0;

let selectedUserName = '';

let lastMessageId = 0;


/*
|--------------------------------------------------------------------------
| HTML ESCAPE
|--------------------------------------------------------------------------
*/
function escapeHtml(text) {

    if (text === null || text === undefined) {
        return '';
    }

    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


/*
|--------------------------------------------------------------------------
| INITIAL
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', function () {

    loadUsers();

    setInterval(function () {

        loadUsers();

        if (selectedUserId > 0) {
            loadMessages(false);
        }

    }, 3000);

});


/*
|--------------------------------------------------------------------------
| LOAD USER LIST
|--------------------------------------------------------------------------
*/
function loadUsers() { fetch('chats_message.php?ajax=users') .then(async response => { const text = await response.text(); console.log('USERS RESPONSE:', text); try { return JSON.parse(text); } catch (e) { throw new Error( 'Server returned invalid JSON: ' + text ); } }) .then(data => { console.log('USERS DATA:', data); if (!data.success) { alert( data.message || 'Unable to load users' ); return; } renderUsers(data.users); }) .catch(error => { console.error('USER AJAX ERROR:', error); document.getElementById('userList').innerHTML = ` <div style=" padding:20px; color:red; font-size:13px;"> ${escapeHtml(error.message)} </div> `; }); }

/*
|--------------------------------------------------------------------------
| RENDER USER LIST
|--------------------------------------------------------------------------
*/
function renderUsers(users) {

    const userList = document.getElementById('userList');

    const search = document
        .getElementById('searchUser')
        .value
        .toLowerCase()
        .trim();

    let html = '';
    let found = false;

    users.forEach(function(user) {

        if (
            search !== '' &&
            !user.username.toLowerCase().includes(search) &&
            !user.role.toLowerCase().includes(search)
        ) {
            return;
        }

        found = true;

        let firstLetter =
            user.username.substring(0, 1).toUpperCase();

        let unread =
            parseInt(user.unread_count || 0);

        let lastMessage =
            user.last_message || '';

        if (lastMessage.length > 35) {
            lastMessage =
                lastMessage.substring(0, 35) + '...';
        }

        html += `
            <div
                class="user-item ${selectedUserId == user.id ? 'active' : ''}"
                data-id="${user.id}"
                data-username="${escapeHtml(user.username)}"
                data-role="${escapeHtml(user.role)}">

                <div class="avatar">
                    ${escapeHtml(firstLetter)}
                </div>

                <div class="user-info">

                    <div class="user-name-row">

                        <div class="user-name">
                            ${escapeHtml(user.username)}
                        </div>

                        ${
                            unread > 0
                            ? `<div class="unread">${unread}</div>`
                            : ''
                        }

                    </div>

                    <div class="user-role">
                        ${escapeHtml(user.role)}
                    </div>

                    <div class="last-message">
                        ${escapeHtml(lastMessage)}
                    </div>

                </div>

            </div>
        `;
    });


    if (!found) {

        html = `
            <div style="
                padding:30px;
                text-align:center;
                color:#999;">
                No users found
            </div>
        `;
    }

    userList.innerHTML = html;


    /*
    |--------------------------------------------------------------------------
    | USER CLICK
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.user-item').forEach(function(item) {

        item.addEventListener('click', function() {

            const userId =
                parseInt(this.dataset.id);

            const username =
                this.dataset.username;

            const role =
                this.dataset.role;

            selectUser(
                userId,
                username,
                role
            );

        });

    });
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/
document
    .getElementById('searchUser')
    .addEventListener('input', function () {

        loadUsers();

    });


/*
|--------------------------------------------------------------------------
| SELECT USER
|--------------------------------------------------------------------------
*/
function selectUser(userId, username, role) {

    selectedUserId = parseInt(userId);

    selectedUserName = username;

    lastMessageId = 0;


    document.getElementById('chatHeader').innerHTML = `

        <div class="chat-header-avatar">
            ${escapeHtml(username.substring(0, 1).toUpperCase())}
        </div>

        <div class="chat-header-info">

            <div class="chat-header-name">
                ${escapeHtml(username)}
            </div>

            <div class="chat-header-role">
                ${escapeHtml(role)}
            </div>

        </div>
    `;


    document.getElementById('messageInput').disabled = false;

    document.getElementById('sendButton').disabled = false;

    loadMessages(true);

    loadUsers();
}


/*
|--------------------------------------------------------------------------
| LOAD MESSAGES
|--------------------------------------------------------------------------
*/
function loadMessages(scrollBottom) {

    if (selectedUserId <= 0) {
        return;
    }

    fetch(
        'chats_message.php?ajax=messages&user_id=' +
        encodeURIComponent(selectedUserId)
    )

    .then(response => response.json())

    .then(data => {

        if (!data.success) {
            return;
        }

        renderMessages(data.messages, scrollBottom);

        /*
        | Refresh unread badges after messages are marked read
        */
        loadUsers();

    })

    .catch(error => {
        console.log(error);
    });
}


/*
|--------------------------------------------------------------------------
| RENDER MESSAGES
|--------------------------------------------------------------------------
*/
function renderMessages(messages, scrollBottom) {

    const container =
        document.getElementById('messages');

    if (messages.length === 0) {

        container.innerHTML = `

            <div class="empty-chat">

                <div>

                    <div style="font-size:40px;">
                        💬
                    </div>

                    <div>
                        No messages yet
                    </div>

                    <div style="
                        font-size:12px;
                        margin-top:5px;">
                        Start the conversation
                    </div>

                </div>

            </div>
        `;

        return;
    }


    let html = '';

    messages.forEach(function (msg) {

        const mine =
            parseInt(msg.sender_id) === currentUserId;

        const date =
            new Date(
                msg.created_at.replace(' ', 'T')
            );

        let time = '';

        if (!isNaN(date.getTime())) {

            time = date.toLocaleTimeString(
                [],
                {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );

        } else {

            time = msg.created_at;
        }


        html += `

            <div class="message-row ${mine ? 'mine' : ''}">

                <div class="message-bubble">

                    <div class="message-text">
                        ${escapeHtml(msg.message)}
                    </div>

                    <div class="message-time">
                        ${escapeHtml(time)}
                    </div>

                </div>

            </div>

        `;

        lastMessageId =
            Math.max(
                lastMessageId,
                parseInt(msg.id)
            );
    });


    container.innerHTML = html;


    if (scrollBottom) {

        container.scrollTop =
            container.scrollHeight;

    }
}


/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/
document
    .getElementById('sendForm')
    .addEventListener('submit', function (e) {

        e.preventDefault();

        if (selectedUserId <= 0) {
            return;
        }


        const input =
            document.getElementById('messageInput');

        const button =
            document.getElementById('sendButton');

        const message =
            input.value.trim();


        if (message === '') {
            return;
        }


        button.disabled = true;


        const formData = new FormData();

        formData.append(
            'receiver_id',
            selectedUserId
        );

        formData.append(
            'message',
            message
        );


        fetch(
            'chats_message.php?ajax=send',
            {
                method: 'POST',
                body: formData
            }
        )

        .then(response => response.json())

        .then(data => {

            if (data.success) {

                input.value = '';

                loadMessages(true);

                loadUsers();

            } else {

                alert(
                    data.message ||
                    'Unable to send message.'
                );

            }

        })

        .catch(error => {

            console.log(error);

            alert('Something went wrong.');

        })

        .finally(function () {

            button.disabled = false;

            input.focus();

        });

    });


/*
|--------------------------------------------------------------------------
| ENTER = SEND
| SHIFT + ENTER = NEW LINE
|--------------------------------------------------------------------------
*/
document
    .getElementById('messageInput')
    .addEventListener('keydown', function (e) {

        if (
            e.key === 'Enter' &&
            !e.shiftKey
        ) {

            e.preventDefault();

            document
                .getElementById('sendForm')
                .requestSubmit();
        }

    });

</script>

</body>
</html>

