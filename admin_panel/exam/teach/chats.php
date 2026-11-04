<?php
error_reporting(0);
session_start();

include_once 'dbConnection.php';

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CURRENT USER
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Teacher login uses:
| $_SESSION['user_id']
|
*/

$current_user_id = 0;

if (isset($_SESSION['user_id'])) {
    $current_user_id = (int)$_SESSION['user_id'];
} elseif (isset($_SESSION['id'])) {
    // Backward compatibility
    $current_user_id = (int)$_SESSION['id'];
}

$current_role = isset($_SESSION['role'])
    ? strtolower(trim($_SESSION['role']))
    : 'teacher';

if (!in_array($current_role, array('student', 'teacher'), true)) {
    $current_role = 'teacher';
}

if ($current_user_id <= 0) {
    die('Invalid user session.');
}


/*
|--------------------------------------------------------------------------
| FUNCTIONS
|--------------------------------------------------------------------------
*/

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

function initials($name)
{
    $name = trim($name);

    if ($name == '') {
        return 'U';
    }

    $parts = preg_split('/\s+/', $name);

    if (count($parts) >= 2) {

        return strtoupper(
            substr($parts[0], 0, 1) .
            substr($parts[count($parts) - 1], 0, 1)
        );
    }

    return strtoupper(substr($name, 0, 2));
}


/*
|--------------------------------------------------------------------------
| AVATAR COLOR
|--------------------------------------------------------------------------
*/

function avatarColor($id)
{
    $colors = array(
        '#007bff',
        '#28a745',
        '#6f42c1',
        '#17a2b8',
        '#f39c12',
        '#dc3545',
        '#20c997'
    );

    return $colors[((int)$id) % count($colors)];
}


/*
|--------------------------------------------------------------------------
| FORMAT TIME
|--------------------------------------------------------------------------
*/

function messageTime($date)
{
    if (!$date) {
        return '';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '';
    }

    if (date('Y-m-d', $timestamp) == date('Y-m-d')) {
        return date('h:i A', $timestamp);
    }

    if (
        date('Y-m-d', $timestamp)
        ==
        date('Y-m-d', strtotime('-1 day'))
    ) {
        return 'Yesterday';
    }

    return date('d M Y', $timestamp);
}


/*
|--------------------------------------------------------------------------
| GET STUDENT
|--------------------------------------------------------------------------
*/

function getStudent($con, $id)
{
    static $cache = array();

    $id = (int)$id;

    if (isset($cache[$id])) {
        return $cache[$id];
    }

    $sql = "
        SELECT
            id,
            scl_name,
            scl_class,
            scl_section,
            scl_reg_no,
            scl_roll_no,
            scl_session
        FROM rrsv_student_registration
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        return array();
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = $result
        ? mysqli_fetch_assoc($result)
        : false;

    if (!$row) {

        $row = array(
            'id' => $id,
            'scl_name' => 'Student #' . $id,
            'scl_class' => '',
            'scl_section' => '',
            'scl_reg_no' => '',
            'scl_roll_no' => '',
            'scl_session' => ''
        );
    }

    $cache[$id] = $row;

    return $row;
}


/*
|--------------------------------------------------------------------------
| GET TEACHER
|--------------------------------------------------------------------------
*/

function getTeacher($con, $id)
{
    static $cache = array();

    $id = (int)$id;

    if (isset($cache[$id])) {
        return $cache[$id];
    }

    $sql = "
        SELECT *
        FROM rrsv_teacher
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {

        return array(
            'id' => $id,
            'name' => 'Teacher #' . $id
        );
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = $result
        ? mysqli_fetch_assoc($result)
        : false;

    if (!$row) {

        $row = array(
            'id' => $id,
            'name' => 'Teacher #' . $id
        );

        $cache[$id] = $row;

        return $row;
    }

    /*
    |--------------------------------------------------------------------------
    | Try common teacher name fields
    |--------------------------------------------------------------------------
    */

    $name = '';

    $name_fields = array(
        'full_name',
        'name',
        'teacher_name',
        'teachername',
        'fullname',
        'username',
        'user_name'
    );

    foreach ($name_fields as $field) {

        if (
            isset($row[$field])
            &&
            trim($row[$field]) != ''
        ) {

            $name = $row[$field];
            break;
        }
    }

    if ($name == '') {
        $name = 'Teacher #' . $id;
    }

    $row['name'] = $name;

    $cache[$id] = $row;

    return $row;
}


/*
|--------------------------------------------------------------------------
| GET USER NAME
|--------------------------------------------------------------------------
*/

function getUserName($con, $id, $role)
{
    if ($role == 'student') {

        $student = getStudent($con, $id);

        return isset($student['scl_name'])
            ? $student['scl_name']
            : 'Student #' . $id;
    }

    $teacher = getTeacher($con, $id);

    return isset($teacher['name'])
        ? $teacher['name']
        : 'Teacher #' . $id;
}


/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    &&
    isset($_POST['send_message'])
) {

    $receiver_id = isset($_POST['receiver_id'])
        ? (int)$_POST['receiver_id']
        : 0;

    $receiver_role = isset($_POST['receiver_role'])
        ? strtolower(trim($_POST['receiver_role']))
        : '';

    $subject = isset($_POST['subject'])
        ? trim($_POST['subject'])
        : 'Student / Teacher Communication';

    $message = isset($_POST['message'])
        ? trim($_POST['message'])
        : '';

    /*
    |--------------------------------------------------------------------------
    | Validate receiver
    |--------------------------------------------------------------------------
    */

    if ($receiver_id <= 0) {
        die('Invalid receiver.');
    }

    if (
        !in_array(
            $receiver_role,
            array('student', 'teacher'),
            true
        )
    ) {
        die('Invalid receiver role.');
    }

    /*
    |--------------------------------------------------------------------------
    | Student <-> Teacher only
    |--------------------------------------------------------------------------
    */

    if ($receiver_role == $current_role) {
        die('Communication is allowed only between student and teacher.');
    }

    /*
    |--------------------------------------------------------------------------
    | Self message
    |--------------------------------------------------------------------------
    */

    if (
        $receiver_id == $current_user_id
        &&
        $receiver_role == $current_role
    ) {
        die('You cannot send a message to yourself.');
    }

    /*
    |--------------------------------------------------------------------------
    | Message required
    |--------------------------------------------------------------------------
    */

    if ($message == '') {
        die('Message cannot be empty.');
    }


    /*
    |--------------------------------------------------------------------------
    | ATTACHMENT
    |--------------------------------------------------------------------------
    */

    $attachment = null;

    if (
        isset($_FILES['attachment'])
        &&
        $_FILES['attachment']['error'] == UPLOAD_ERR_OK
    ) {

        $upload_dir = __DIR__ . '/uploads/messages/';

        if (!is_dir($upload_dir)) {

            if (!mkdir($upload_dir, 0755, true)) {
                die('Unable to create upload directory.');
            }
        }

        $original_name =
            $_FILES['attachment']['name'];

        $tmp_name =
            $_FILES['attachment']['tmp_name'];

        $file_size =
            (int)$_FILES['attachment']['size'];

        /*
        |--------------------------------------------------------------------------
        | Maximum 5 MB
        |--------------------------------------------------------------------------
        */

        if ($file_size > 5 * 1024 * 1024) {
            die('Maximum attachment size is 5 MB.');
        }

        /*
        |--------------------------------------------------------------------------
        | Allowed extensions
        |--------------------------------------------------------------------------
        */

        $allowed = array(
            'jpg',
            'jpeg',
            'png',
            'gif',
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'txt'
        );

        $extension = strtolower(
            pathinfo(
                $original_name,
                PATHINFO_EXTENSION
            )
        );

        if (!in_array($extension, $allowed, true)) {
            die('Invalid attachment type.');
        }

        /*
        |--------------------------------------------------------------------------
        | Secure filename
        |--------------------------------------------------------------------------
        */

        try {

            $random_name = bin2hex(
                random_bytes(8)
            );

        } catch (Exception $e) {

            $random_name = md5(
                uniqid(
                    (string)mt_rand(),
                    true
                )
            );
        }

        $new_filename =
            date('YmdHis')
            . '_'
            . $random_name
            . '.'
            . $extension;

        $destination =
            $upload_dir . $new_filename;

        if (
            !move_uploaded_file(
                $tmp_name,
                $destination
            )
        ) {
            die('Attachment upload failed.');
        }

        $attachment =
            'uploads/messages/' . $new_filename;
    }


    /*
    |--------------------------------------------------------------------------
    | INSERT MESSAGE
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO student_teacher_messages
        (
            sender_id,
            sender_role,
            receiver_id,
            receiver_role,
            subject,
            message,
            attachment,
            is_read
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, 0
        )
    ";

    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {

        die(
            'Database error: '
            . mysqli_error($con)
        );
    }

    mysqli_stmt_bind_param(
        $stmt,
        'isissss',
        $current_user_id,
        $current_role,
        $receiver_id,
        $receiver_role,
        $subject,
        $message,
        $attachment
    );

    if (!mysqli_stmt_execute($stmt)) {

        die(
            'Message could not be sent: '
            . mysqli_stmt_error($stmt)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect to chat
    |--------------------------------------------------------------------------
    */

    header(
        'Location: chats.php'
        . '?chat_user_id='
        . $receiver_id
        . '&chat_role='
        . urlencode($receiver_role)
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| SELECTED CHAT
|--------------------------------------------------------------------------
*/

$chat_user_id = isset($_GET['chat_user_id'])
    ? (int)$_GET['chat_user_id']
    : 0;

$chat_role = isset($_GET['chat_role'])
    ? strtolower(trim($_GET['chat_role']))
    : '';

if (
    !in_array(
        $chat_role,
        array('student', 'teacher'),
        true
    )
) {
    $chat_role = '';
}


/*
|--------------------------------------------------------------------------
| LOAD ALL CHAT USERS
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This is the main fix.
|
| Teacher -> all students
| Student -> all teachers
|
| Even if there is NO message, the user will appear.
|--------------------------------------------------------------------------
*/

$conversations = array();


if ($current_role == 'teacher') {

    /*
    |--------------------------------------------------------------------------
    | TEACHER -> STUDENTS
    |--------------------------------------------------------------------------
    */

    $sql_users = "
        SELECT
            id,
            scl_name,
            scl_class,
            scl_section,
            scl_reg_no,
            scl_roll_no,
            scl_session
        FROM rrsv_student_registration
        WHERE id IS NOT NULL
        ORDER BY
            scl_name ASC,
            id DESC
    ";

} else {

    /*
    |--------------------------------------------------------------------------
    | STUDENT -> TEACHERS
    |--------------------------------------------------------------------------
    */

    $sql_users = "
        SELECT *
        FROM rrsv_teacher
        WHERE id IS NOT NULL
        ORDER BY full_name ASC, id ASC
    ";
}


$users_result = mysqli_query($con, $sql_users);


if ($users_result) {

    while ($user = mysqli_fetch_assoc($users_result)) {

        /*
        |--------------------------------------------------------------------------
        | Determine other user
        |--------------------------------------------------------------------------
        */

        if ($current_role == 'teacher') {

            $other_id =
                (int)$user['id'];

            $other_role =
                'student';

            $other_name =
                !empty($user['scl_name'])
                ? $user['scl_name']
                : 'Student #' . $other_id;

        } else {

            $other_id =
                (int)$user['id'];

            $other_role =
                'teacher';

            $other_name = '';

            $name_fields = array(
                'full_name',
                'name',
                'teacher_name',
                'teachername',
                'fullname',
                'username',
                'user_name'
            );

            foreach ($name_fields as $field) {

                if (
                    isset($user[$field])
                    &&
                    trim($user[$field]) != ''
                ) {

                    $other_name =
                        $user[$field];

                    break;
                }
            }

            if ($other_name == '') {
                $other_name =
                    'Teacher #' . $other_id;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Find latest message with this user
        |--------------------------------------------------------------------------
        */

        $last_message = '';
        $last_created = '';
        $unread = 0;

        $sql_last = "
            SELECT
                id,
                message,
                attachment,
                created_at,
                sender_id,
                sender_role,
                receiver_id,
                receiver_role,
                is_read
            FROM student_teacher_messages
            WHERE
            (
                sender_id = ?
                AND sender_role = ?
                AND receiver_id = ?
                AND receiver_role = ?
            )
            OR
            (
                sender_id = ?
                AND sender_role = ?
                AND receiver_id = ?
                AND receiver_role = ?
            )
            ORDER BY created_at DESC, id DESC
            LIMIT 1
        ";

        $stmt_last =
            mysqli_prepare(
                $con,
                $sql_last
            );

        if ($stmt_last) {

            mysqli_stmt_bind_param(
                $stmt_last,
                'isisisis',
                $current_user_id,
                $current_role,
                $other_id,
                $other_role,

                $other_id,
                $other_role,
                $current_user_id,
                $current_role
            );

            mysqli_stmt_execute($stmt_last);

            $last_result =
                mysqli_stmt_get_result(
                    $stmt_last
                );

            if ($last_result) {

                $last_row =
                    mysqli_fetch_assoc(
                        $last_result
                    );

                if ($last_row) {

                    $last_message =
                        $last_row['message'];

                    if (
                        $last_message == ''
                        &&
                        !empty(
                            $last_row['attachment']
                        )
                    ) {
                        $last_message =
                            'Attachment';
                    }

                    $last_created =
                        $last_row['created_at'];
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Unread count
        |--------------------------------------------------------------------------
        */

        $sql_unread = "
            SELECT COUNT(*) AS total
            FROM student_teacher_messages
            WHERE
                sender_id = ?
                AND sender_role = ?
                AND receiver_id = ?
                AND receiver_role = ?
                AND is_read = 0
        ";

        $stmt_unread =
            mysqli_prepare(
                $con,
                $sql_unread
            );

        if ($stmt_unread) {

            mysqli_stmt_bind_param(
                $stmt_unread,
                'isis',
                $other_id,
                $other_role,
                $current_user_id,
                $current_role
            );

            mysqli_stmt_execute(
                $stmt_unread
            );

            $unread_result =
                mysqli_stmt_get_result(
                    $stmt_unread
                );

            if ($unread_result) {

                $unread_row =
                    mysqli_fetch_assoc(
                        $unread_result
                    );

                if ($unread_row) {

                    $unread =
                        (int)$unread_row['total'];
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Add conversation
        |--------------------------------------------------------------------------
        */

        $conversations[] = array(

            'user_id' =>
                $other_id,

            'role' =>
                $other_role,

            'name' =>
                $other_name,

            'message' =>
                $last_message,

            'created_at' =>
                $last_created,

            'unread' =>
                $unread,

            'class' =>
                isset($user['scl_class'])
                ? $user['scl_class']
                : '',

            'section' =>
                isset($user['scl_section'])
                ? $user['scl_section']
                : '',

            'roll_no' =>
                isset($user['scl_roll_no'])
                ? $user['scl_roll_no']
                : '',

            'reg_no' =>
                isset($user['scl_reg_no'])
                ? $user['scl_reg_no']
                : '',

            'session' =>
                isset($user['scl_session'])
                ? $user['scl_session']
                : ''
        );
    }
}


/*
|--------------------------------------------------------------------------
| Sort:
| Existing conversations with latest messages first.
| New users after them.
|--------------------------------------------------------------------------
*/

usort(
    $conversations,
    function ($a, $b) {

        if (
            $a['created_at'] == ''
            &&
            $b['created_at'] == ''
        ) {

            return strcasecmp(
                $a['name'],
                $b['name']
            );
        }

        if ($a['created_at'] == '') {
            return 1;
        }

        if ($b['created_at'] == '') {
            return -1;
        }

        return strcmp(
            $b['created_at'],
            $a['created_at']
        );
    }
);


/*
|--------------------------------------------------------------------------
| SELECTED USER
|--------------------------------------------------------------------------
*/

$selected_name = '';

$selected_student = array();

$selected_teacher = array();


if (
    $chat_user_id > 0
    &&
    $chat_role != ''
) {

    if ($chat_role == 'student') {

        $selected_student =
            getStudent(
                $con,
                $chat_user_id
            );

        $selected_name =
            isset(
                $selected_student['scl_name']
            )
            ? $selected_student['scl_name']
            : 'Student #' . $chat_user_id;

    } else {

        $selected_teacher =
            getTeacher(
                $con,
                $chat_user_id
            );

        $selected_name =
            isset(
                $selected_teacher['name']
            )
            ? $selected_teacher['name']
            : 'Teacher #' . $chat_user_id;
    }
}


/*
|--------------------------------------------------------------------------
| MARK MESSAGES AS READ
|--------------------------------------------------------------------------
*/

if (
    $chat_user_id > 0
    &&
    $chat_role != ''
) {

    $sql_read = "
        UPDATE student_teacher_messages
        SET is_read = 1
        WHERE
            sender_id = ?
            AND sender_role = ?
            AND receiver_id = ?
            AND receiver_role = ?
    ";

    $stmt_read =
        mysqli_prepare(
            $con,
            $sql_read
        );

    if ($stmt_read) {

        mysqli_stmt_bind_param(
            $stmt_read,
            'isis',
            $chat_user_id,
            $chat_role,
            $current_user_id,
            $current_role
        );

        mysqli_stmt_execute(
            $stmt_read
        );
    }
}


/*
|--------------------------------------------------------------------------
| LOAD CHAT MESSAGES
|--------------------------------------------------------------------------
*/

$chat_messages = array();


if (
    $chat_user_id > 0
    &&
    $chat_role != ''
) {

    $sql_chat = "
        SELECT *
        FROM student_teacher_messages
        WHERE
        (
            sender_id = ?
            AND sender_role = ?
            AND receiver_id = ?
            AND receiver_role = ?
        )
        OR
        (
            sender_id = ?
            AND sender_role = ?
            AND receiver_id = ?
            AND receiver_role = ?
        )
        ORDER BY created_at ASC, id ASC
    ";

    $stmt_chat =
        mysqli_prepare(
            $con,
            $sql_chat
        );

    if ($stmt_chat) {

        mysqli_stmt_bind_param(
            $stmt_chat,
            'isisisis',

            $current_user_id,
            $current_role,
            $chat_user_id,
            $chat_role,

            $chat_user_id,
            $chat_role,
            $current_user_id,
            $current_role
        );

        mysqli_stmt_execute(
            $stmt_chat
        );

        $chat_result =
            mysqli_stmt_get_result(
                $stmt_chat
            );

        if ($chat_result) {

            while (
                $row =
                mysqli_fetch_assoc(
                    $chat_result
                )
            ) {

                $chat_messages[] =
                    $row;
            }
        }
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible"
      content="IE=edge">

<title>
    Student / Teacher Communication
</title>

<meta content="width=device-width,
               initial-scale=1,
               maximum-scale=1,
               user-scalable=no"
      name="viewport">


<link rel="stylesheet"
      href="../css/bootstrap.min.css">

<link rel="stylesheet"
      href="../css/font-awesome.min.css">

<link rel="stylesheet"
      href="../css/AdminLTE.min.css">

<link rel="stylesheet"
      href="../css/_all-skins.min.css">


<style>

/*
|--------------------------------------------------------------------------
| CHAT LAYOUT
|--------------------------------------------------------------------------
*/

.chat-container {
    background:#fff;
    border:1px solid #ddd;
    border-radius:3px;
    overflow:hidden;
}

.chat-left {
    border-right:1px solid #ddd;
    height:600px;
    background:#fff;
}

.chat-right {
    height:600px;
    background:#ecf0f5;
}

.chat-search {
    padding:15px;
    border-bottom:1px solid #ddd;
}

.chat-list {
    height:530px;
    overflow-y:auto;
}

.chat-user {
    display:block;
    padding:13px 12px;
    border-bottom:1px solid #eee;
    color:#333;
    text-decoration:none;
}

.chat-user:hover {
    background:#f5f5f5;
    text-decoration:none;
}

.chat-user.active {
    background:#337ab7;
    color:#fff;
}

.chat-user.active small,
.chat-user.active strong {
    color:#fff;
}

.chat-avatar {
    width:45px;
    height:45px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-weight:bold;
    font-size:15px;
}

.chat-avatar-large {
    width:50px;
    height:50px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-weight:bold;
    font-size:17px;
}

.chat-header {
    background:#fff;
    padding:15px;
    border-bottom:1px solid #ddd;
}

.chat-header h4 {
    margin-top:3px;
    margin-bottom:5px;
}

.chat-messages {
    height:440px;
    overflow-y:auto;
    padding:20px;
}

.chat-compose {
    background:#fff;
    padding:15px;
    border-top:1px solid #ddd;
}

.direct-chat-text {
    font-size:14px;
    line-height:22px;
}

.empty-chat {
    height:600px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:#999;
}

.file-name {
    margin-top:5px;
}

.new-chat-text {
    color:#aaa;
    font-size:11px;
}

.chat-user.active .new-chat-text {
    color:#eee;
}

@media(max-width:767px) {

    .chat-left,
    .chat-right {
        height:auto;
    }

    .chat-list {
        height:300px;
    }

    .chat-messages {
        height:400px;
    }

}

</style>

</head>


<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">


<!-- ==========================================================
     HEADER
========================================================== -->

<header class="main-header">

    <a href="#"
       class="logo">

        <span class="logo-mini">
            <b>MSB</b>
        </span>

        <span class="logo-lg">

            <img src="../css/download.png"
                 style="height:100%;width:44px;">

        </span>

    </a>


    <nav class="navbar navbar-static-top">

        <a href="#"
           class="sidebar-toggle"
           data-toggle="offcanvas">

            <span class="sr-only">
                Toggle navigation
            </span>

        </a>


        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <li class="dropdown user user-menu">

                    <a href="#"
                       class="dropdown-toggle"
                       data-toggle="dropdown">

                        <img src="../css/download.png"
                             class="user-image">

                        <span class="hidden-xs">

                            <?php

                            echo h(
                                isset(
                                    $_SESSION['username']
                                )
                                ? $_SESSION['username']
                                : ''
                            );

                            ?>

                        </span>

                    </a>

                </li>

            </ul>

        </div>

    </nav>

</header>


<!-- ==========================================================
     SIDEBAR
========================================================== -->

<?php

include_once 'aslidebar.php';

?>


<!-- ==========================================================
     CONTENT
========================================================== -->

<div class="content-wrapper">


<section class="content-header">

    <h1>
        Student / Teacher Communication
    </h1>

    <ol class="breadcrumb">

        <li>

            <a href="#">

                <i class="fa fa-dashboard"></i>

                Home

            </a>

        </li>

        <li class="active">
            Communication
        </li>

    </ol>

</section>


<section class="content">


<div class="box box-primary">

    <div class="box-header with-border">

        <h3 class="box-title">

            <i class="fa fa-comments"></i>

            Chats

        </h3>

    </div>


    <div class="box-body"
         style="padding:0;">


        <div class="row chat-container"
             style="margin:0;">


            <!-- ==================================================
                 LEFT CHAT LIST
            ================================================== -->

            <div class="col-md-4 chat-left"
                 style="padding:0;">


                <div class="chat-search">

                    <div class="input-group">

                        <input type="text"
                               id="chatSearch"
                               class="form-control"
                               placeholder="Search student or teacher"
                               autocomplete="off">

                        <span class="input-group-btn">

                            <button class="btn btn-primary"
                                    type="button">

                                <i class="fa fa-search"></i>

                            </button>

                        </span>

                    </div>

                </div>


                <div class="chat-list"
                     id="chatList">


                    <?php

                    if (empty($conversations)) {

                    ?>

                        <div class="text-center text-muted"
                             style="padding:50px 15px;">

                            <i class="fa fa-users fa-3x"></i>

                            <br><br>

                            No students or teachers found.

                        </div>

                    <?php

                    }


                    foreach (
                        $conversations
                        as $conversation
                    ) {

                        $active =
                            (
                                $chat_user_id
                                ==
                                $conversation['user_id']
                                &&
                                $chat_role
                                ==
                                $conversation['role']
                            );


                        $color =
                            avatarColor(
                                $conversation['user_id']
                            );


                        $initial =
                            initials(
                                $conversation['name']
                            );

                    ?>


                    <a class="chat-user <?php

                        echo $active
                            ? 'active'
                            : '';

                    ?>"
                       href="chats.php?chat_user_id=<?php

                           echo (int)
                               $conversation['user_id'];

                       ?>&chat_role=<?php

                           echo urlencode(
                               $conversation['role']
                           );

                       ?>"
                       data-name="<?php

                           echo h(
                               $conversation['name']
                           );

                       ?>">


                        <div class="row">


                            <div class="col-xs-2">

                                <div class="chat-avatar"
                                     style="background:<?php

                                     echo $color;

                                     ?>;">

                                    <?php

                                    echo h(
                                        $initial
                                    );

                                    ?>

                                </div>

                            </div>


                            <div class="col-xs-10">


                                <strong>

                                    <?php

                                    echo h(
                                        $conversation['name']
                                    );

                                    ?>

                                </strong>


                                <?php

                                if (
                                    !empty(
                                        $conversation[
                                            'created_at'
                                        ]
                                    )
                                ) {

                                ?>

                                    <span class="pull-right"
                                          style="font-size:11px;">

                                        <?php

                                        echo h(
                                            messageTime(
                                                $conversation[
                                                    'created_at'
                                                ]
                                            )
                                        );

                                        ?>

                                    </span>

                                <?php

                                }

                                ?>


                                <br>


                                <small>

                                    <?php

                                    echo ucfirst(
                                        h(
                                            $conversation['role']
                                        )
                                    );

                                    ?>


                                    <?php

                                    if (
                                        $conversation['role']
                                        ==
                                        'student'
                                    ) {

                                        if (
                                            !empty(
                                                $conversation['class']
                                            )
                                        ) {

                                        ?>

                                            &nbsp;|&nbsp;

                                            <?php

                                            echo h(
                                                $conversation['class']
                                            );

                                            ?>

                                        <?php

                                        }

                                        if (
                                            !empty(
                                                $conversation['section']
                                            )
                                        ) {

                                        ?>

                                            -
                                            <?php

                                            echo h(
                                                $conversation['section']
                                            );

                                            ?>

                                        <?php

                                        }
                                    }


                                    if (
                                        $conversation['unread']
                                        > 0
                                    ) {

                                    ?>

                                        <span class="label label-danger pull-right">

                                            <?php

                                            echo (int)
                                                $conversation[
                                                    'unread'
                                                ];

                                            ?>

                                        </span>

                                    <?php

                                    }

                                    ?>

                                </small>


                                <br>


                                <?php

                                if (
                                    !empty(
                                        $conversation['message']
                                    )
                                ) {

                                ?>

                                    <small style="
                                        display:block;
                                        white-space:nowrap;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                    ">

                                        <?php

                                        echo h(
                                            $conversation[
                                                'message'
                                            ]
                                        );

                                        ?>

                                    </small>

                                <?php

                                } else {

                                ?>

                                    <small class="new-chat-text">

                                        <i class="fa fa-plus-circle"></i>

                                        Start new conversation

                                    </small>

                                <?php

                                }

                                ?>


                            </div>

                        </div>

                    </a>


                    <?php

                    }

                    ?>

                </div>

            </div>


            <!-- ==================================================
                 RIGHT CHAT
            ================================================== -->

            <div class="col-md-8 chat-right"
                 style="padding:0;">


                <?php

                if (
                    $chat_user_id > 0
                    &&
                    $chat_role != ''
                ) {

                ?>


                <!-- CHAT HEADER -->

                <div class="chat-header">

                    <div class="row">


                        <div class="col-xs-2 col-sm-1">

                            <div class="chat-avatar-large"
                                 style="background:<?php

                                 echo avatarColor(
                                     $chat_user_id
                                 );

                                 ?>;">

                                <?php

                                echo h(
                                    initials(
                                        $selected_name
                                    )
                                );

                                ?>

                            </div>

                        </div>


                        <div class="col-xs-10 col-sm-11">

                            <h4>

                                <?php

                                echo h(
                                    $selected_name
                                );

                                ?>

                            </h4>


                            <small class="text-success">

                                <i class="fa fa-circle"></i>

                                <?php

                                echo ucfirst(
                                    h($chat_role)
                                );

                                ?>


                                <?php

                                if (
                                    $chat_role
                                    ==
                                    'student'
                                    &&
                                    !empty(
                                        $selected_student
                                    )
                                ) {

                                ?>

                                    &nbsp; • &nbsp;

                                    <?php

                                    echo h(
                                        $selected_student[
                                            'scl_class'
                                        ]
                                    );

                                    ?>


                                    <?php

                                    if (
                                        !empty(
                                            $selected_student[
                                                'scl_section'
                                            ]
                                        )
                                    ) {

                                    ?>

                                        -
                                        <?php

                                        echo h(
                                            $selected_student[
                                                'scl_section'
                                            ]
                                        );

                                        ?>

                                    <?php

                                    }

                                    if (
                                        !empty(
                                            $selected_student[
                                                'scl_roll_no'
                                            ]
                                        )
                                    ) {

                                    ?>

                                        &nbsp; | Roll:
                                        <?php

                                        echo h(
                                            $selected_student[
                                                'scl_roll_no'
                                            ]
                                        );

                                        ?>

                                    <?php

                                    }

                                }

                                ?>

                            </small>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     MESSAGES
                ================================================== -->

                <div id="chatMessages"
                     class="chat-messages direct-chat-messages">


                    <?php

                    if (
                        empty($chat_messages)
                    ) {

                    ?>

                        <div class="text-center text-muted"
                             style="padding-top:150px;">

                            <i class="fa fa-comments-o fa-3x"></i>

                            <br><br>

                            No messages yet.

                            <br>

                            Start the conversation.

                        </div>

                    <?php

                    }


                    foreach (
                        $chat_messages
                        as $msg
                    ) {

                        $sent =
                            (
                                (int)$msg['sender_id']
                                ==
                                $current_user_id
                                &&
                                $msg['sender_role']
                                ==
                                $current_role
                            );


                        $sender_name =
                            getUserName(
                                $con,
                                (int)$msg['sender_id'],
                                $msg['sender_role']
                            );


                        $sender_initial =
                            initials(
                                $sender_name
                            );

                    ?>


                    <div class="direct-chat-msg <?php

                        echo $sent
                            ? 'right'
                            : '';

                    ?>">


                        <div class="direct-chat-info clearfix">


                            <span class="direct-chat-name <?php

                                echo $sent
                                    ? 'pull-right'
                                    : 'pull-left';

                            ?>">

                                <?php

                                echo h(
                                    $sender_name
                                );

                                ?>

                            </span>


                            <span class="direct-chat-timestamp <?php

                                echo $sent
                                    ? 'pull-left'
                                    : 'pull-right';

                            ?>">

                                <?php

                                echo h(
                                    messageTime(
                                        $msg['created_at']
                                    )
                                );

                                ?>

                            </span>


                        </div>


                        <span class="direct-chat-img"
                              style="
                              display:flex;
                              align-items:center;
                              justify-content:center;
                              background:<?php

                              echo avatarColor(
                                  $msg['sender_id']
                              );

                              ?>;
                              color:#fff;
                              font-weight:bold;
                              ">

                            <?php

                            echo h(
                                $sender_initial
                            );

                            ?>

                        </span>


                        <div class="direct-chat-text">

                            <?php

                            echo nl2br(
                                h(
                                    $msg['message']
                                )
                            );

                            ?>


                            <?php

                            if (
                                !empty(
                                    $msg['attachment']
                                )
                            ) {

                            ?>

                                <div style="
                                    margin-top:8px;
                                    padding-top:8px;
                                    border-top:1px solid #ddd;
                                ">

                                    <i class="fa fa-paperclip"></i>

                                    <a href="<?php

                                        echo h(
                                            $msg['attachment']
                                        );

                                    ?>"
                                       target="_blank"
                                       rel="noopener">

                                        View Attachment

                                    </a>

                                </div>

                            <?php

                            }

                            ?>

                        </div>


                    </div>


                    <?php

                    }

                    ?>

                </div>


                <!-- ==================================================
                     COMPOSE
                ================================================== -->

                <div class="chat-compose">


                    <form method="POST"
                          action="chats.php?chat_user_id=<?php

                              echo (int)$chat_user_id;

                          ?>&chat_role=<?php

                              echo urlencode(
                                  $chat_role
                              );

                          ?>"
                          enctype="multipart/form-data">


                        <input type="hidden"
                               name="receiver_id"
                               value="<?php

                               echo (int)$chat_user_id;

                               ?>">


                        <input type="hidden"
                               name="receiver_role"
                               value="<?php

                               echo h($chat_role);

                               ?>">


                        <input type="hidden"
                               name="subject"
                               value="Student / Teacher Communication">


                        <div class="input-group">


                            <span class="input-group-btn">

                                <label class="btn btn-default"
                                       style="margin:0;">

                                    <i class="fa fa-paperclip"></i>

                                    <input type="file"
                                           name="attachment"
                                           style="display:none;"
                                           id="attachment"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                                           onchange="
                                           $('#fileName').text(
                                               this.files.length
                                               ?
                                               this.files[0].name
                                               :
                                               ''
                                           );
                                           ">

                                </label>

                            </span>


                            <input type="text"
                                   name="message"
                                   class="form-control"
                                   placeholder="Type your message..."
                                   autocomplete="off"
                                   required>


                            <span class="input-group-btn">

                                <button type="submit"
                                        name="send_message"
                                        class="btn btn-primary">

                                    <i class="fa fa-paper-plane"></i>

                                    Send

                                </button>

                            </span>


                        </div>


                        <small id="fileName"
                               class="file-name text-muted">
                        </small>


                    </form>

                </div>


                <?php

                } else {

                ?>


                    <div class="empty-chat">

                        <div>

                            <i class="fa fa-comments-o fa-4x"></i>

                            <h3>
                                Student / Teacher Communication
                            </h3>

                            <p>
                                Select a student or teacher
                                from the left side.
                            </p>

                        </div>

                    </div>


                <?php

                }

                ?>


            </div>

        </div>

    </div>

</div>


</section>

</div>


<!-- ==========================================================
     FOOTER
========================================================== -->

<footer class="main-footer"
        style="text-align:center;">

    <strong>
        Copyright &copy; 2013-2021
    </strong>

    All rights reserved.

</footer>


</div>


<script src="../js/jquery-2.2.3.min.js"></script>

<script src="../js/bootstrap.min.js"></script>

<script src="../js/jquery.slimscroll.min.js"></script>

<script src="../js/fastclick.js"></script>

<script src="../js/app.min.js"></script>

<script src="../js/demo.js"></script>


<script>

/*
|--------------------------------------------------------------------------
| SEARCH STUDENT / TEACHER
|--------------------------------------------------------------------------
*/

$(document).on(
    'keyup',
    '#chatSearch',
    function () {

        var search =
            $.trim(
                $(this).val()
            ).toLowerCase();


        $('.chat-user').each(
            function () {

                var name =
                    String(
                        $(this).attr(
                            'data-name'
                        )
                    ).toLowerCase();


                if (
                    name.indexOf(search)
                    !==
                    -1
                ) {

                    $(this).show();

                } else {

                    $(this).hide();

                }

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| SCROLL CHAT TO BOTTOM
|--------------------------------------------------------------------------
*/

$(document).ready(
    function () {

        var chat =
            $('#chatMessages');


        if (
            chat.length
        ) {

            chat.scrollTop(
                chat[0].scrollHeight
            );

        }

    }
);

</script>


</body>

</html>