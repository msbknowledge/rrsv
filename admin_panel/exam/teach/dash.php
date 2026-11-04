 
<!DOCTYPE html>
<html>
<?php
 error_reporting(0); 
 session_start();
include_once 'dbConnection.php';

if (!(isset($_SESSION["username"]))) {
    header("location:../index.php");
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
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Online Exam</title>
   <link rel="icon" type="image/x-icon" href="/../msb1.ico">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php include_once('../header.php');?>
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="../css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../css/font-awesome.min.css">
  <link rel="stylesheet" href="../css/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../css/ionicons.min.css">
  
  <!-- DataTables -->
  <link rel="stylesheet" href="../css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../css/dataTables.bootstrap.min.js">
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
  
   <link rel="stylesheet" href="../css/jquery.dataTables_themeroller.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the ../css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  
<script language="javascript">
function del_Users(uid)
{
	if(confirm("Are you sure want to delete this Contact?"))
	{
		document.location.href = 'groupcontacts?act=del&uid='+uid;
	}
}
</script>
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>

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

  <style>
  @media only screen and (max-width: 620px) {
  /* For mobile phones: */
  .menu,.sp,.main, .right {
    width: 100%;
  }
}
.d-none{
    display: none;
}
.sp {
   padding: 20px 30px 30px 20px;
    background: #09bffd;
    border-radius: 8px;
	text-transform:uppercase;
	font-size:15px;
	text-align:left; 
	padding-top:3px;
	padding-left:55px; 
	color:blue;
    
}
.footer{
  background-color:#3AA56A;
  color: #fff;
  text-align:center;
  font-size:20px;
  padding:10px;
  margin-top:37px;
  position: stiky;
  bottom: 0;
  width: 100%;
   }
   .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
   
}
.alert a{ text-decoration: none;}
.head {
    width: 390px;
    padding: 20px 30px 30px 30px;
    background: #09bffd;
    border-radius: 8px;
    display: table;
	margin: 50px;
    top:20%;
}
.br {
	
    font-size: 20px;
    line-height: 24px;
    font-weight: 100;
    text-align: center;
    color: #fff;
	border-radius: 8px;
    
}
 em {
    display: block;
    font-size: 25px;
    line-height: 33px;
    font-weight: 900px;
    text-transform: uppercase;
    letter-spacing: -1px;
}
.pure-form{
    background: #ddeef8;
    width: 100%;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0 0;
}

body { 
     font-family: 'Droid Sans', sans-serif;
     background:#E4D2B8;
     
    }

.heading {
	width:100%;
	height:96px;
	background:url(./image/banner_bg.gif);
	position:relative;
	border-radius: 8px;
	}
    .circle {
    border-radius: 50%!important;
    border-top-left-radius: 50%;
    border-top-right-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
    border-radius: 8px;
}

.btn-d {
 	background:#fd0963;
	
	height:32px;
	border:1px solid #fd0963;
	color:#FFFFFF;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
	padding:0 20px;
	border-radius: 8px;  
}
.btn-d:hover {
	border:1px solid #fd0963;
	padding:0 20px;
	color:#FFFFFF;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
	background:#fd0663;
	border-radius: 8px; 
	cursor:pointer;
}
.button a:link, .button a:visited, .button a:active { color:#lime; text-decoration:none; }
a {
	font-size:16px;
	color:#lime;
	font-weight:bold;
	text-decoration:none;
	}	

  #pageloader
{
background: url('') 30% 30% no-repeat rgba( 255, 255, 255, 0.8 );
  display: none;
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 2000;
  background-position:center;
 
}

#pageloader img
{
   
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
  text-align: center;
  width : 400px
}

#pageloader:after{
   content: "Please Wait- Don't Press Any Button While Uploading";
   font-weight: bold;
  display: block;
  position: absolute;
  top:60%;
  left: 50%;
  transform: translate(-50%, -50%)
}

.switch {
    position: relative;
    display: inline-block;
    width: 55px;
    height: 28px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background:#ccc;
    transition:.4s;
}

.slider:before {
    position:absolute;
    content:"";
    height:20px;
    width:20px;
    left:4px;
    bottom:4px;
    background:white;
    transition:.4s;
}

input:checked + .slider {
    background:#28a745;
}

input:checked + .slider:before {
    transform:translateX(26px);
}

.slider.round {
    border-radius:34px;
}

.slider.round:before {
    border-radius:50%;
}
  </style>


</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <!-- Header Start -->
  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>MSB</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="../css/download.png" style="height:100%;width:44px"></span>
	      </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
       
           <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
             
              <?php
  

if (!isset($_SESSION['username']))

{

     
        header("location:../login.php");
        exit();

}


// if (!isset($_SESSION["username"])) {
//     header("location:../login.php");
// }
 else {
    $name     = $_SESSION['name'];
    $username = $_SESSION['username'];
     $id = $_SESSION['id'];
    
    
    include_once 'dbConnection.php';
    echo '<img src="../css/download.png" class="user-image" alt="User Image">
    
			  <span class="hidden-xs">'.$username . '</span>';
}
?>
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../css/download.png" class="img-circle" alt="User Image">

                <p>
                  Welcome <?php echo $username;?>   
                </p>
              </li>
              <!-- Menu Body -->
             
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="profile.php=q=7&id=<?php echo $id;?> " class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="../logout.php?q=dash.php" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
			
            
          </li>
          <!-- Control Sidebar Toggle Button -->
         
        </ul>
      </div>
    </nav>
  </header><!-- Header End -->
  <!-- Left side column. contains the logo and sidebar -->
<!-- Sidebar Start -->
   <?php
   
   include_once 'aslidebar.php';
   ?>
<!-- sidebar End -->

<!-- modal login panel-->
   <div class="modal fade" id="login">
  <div class="modal-dialog">
    <div class="head">
      <div class="">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
		<h1 class="br">
<span >Login to Admin<em> </em></span>
</h1>
      </div>
      <div class="modal-body title1">
<div class="row">
<div class="col-md-3"></div>
<div class="pure-form">
<form role="form" method="post" action="admin.php?q=index.php">
<div class="form-group">
<input type="text" name="uname" maxlength="20"  placeholder="Username" class="form-control"/> 
</div>
<div class="form-group">
<input type="password" name="password" maxlength="30" placeholder="Password" class="form-control"/>
</div>
<div class="form-group" align="center">
<input type="submit" name="login" value="Login" class="btn btn-primary" />
</div>
</form>
</div><div class="col-md-3"></div></div>
      </div>
    </div>
  </div>
</div>
<!-- modal admin panel-->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
 <?php echo $username;?>  
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Teacher's Management</a></li>
        <li class="active">Exam</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- Note Start -->
      <div class="col-md-12">
		<div class="box box-danger">
			<div class="box-header with-border">
      <div class="col-sm-3">
        <div class="alert bg-green">
            <h4> Student <i class="pull-right fa fa-users"></i> </h4>
            <span class="d-block"> 
            <?php
                
                $query = "SELECT id FROM rrsv_student_registration ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
           </span> 
           <a href="user.php?q=1" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>               
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-blue">
            <h4>Teachers<i class="pull-right fa fa-graduation-cap"></i></h4>
            <span class="d-block">
            <?php
                
                $query = "SELECT id FROM rrsv_teacher ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
            </span>  
            <a href="" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>              
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-yellow">
            <h4>Exam<i class="pull-right fa fa-key"></i></h4>
            <span class="d-block">
            <?php
                
                $query = "SELECT id FROM quiz ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
            </span> 
            <a href="quiz.php?q=10" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>               
        </div>
    </div>
    <div class="col-sm-3">
        <div class="alert bg-red">
            <h4>Results<i class="pull-right fa fa-building-o"></i></h4>
            <span class="d-block"> <span class="live-clock">
            <?php
                
                $query = "SELECT id FROM rank ORDER BY id";  
                $query_run = mysqli_query($con, $query);
                $row = mysqli_num_rows($query_run);
                echo '<h4>'.$row.'</h4>';
            ?>
            </span>
            <a href="" class="small-box-footer">
            More info <i class="fa fa-arrow-circle-right"></i>
        </a>
                       
        </div>
    </div>     
    
    </div>
		</div>
	  </div>
        <!-- Note End -->
        
<!--feedback start-->
<?php if(@$_GET['q']==3) {
$result = mysqli_query($con,"SELECT * FROM `feedback` ORDER BY `feedback`.`date` DESC") or die('Error');
echo  '<div class="panel"><table class="table table-striped title1">
<tr><td><b>S.N.</b></td><td><b>Subject</b></td><td><b>Email</b></td><td><b>Date</b></td><td><b>Time</b></td><td><b>By</b></td><td></td><td></td></tr>';
$c=1;
while($row = mysqli_fetch_array($result)) {
	$date = $row['date'];
	$date= date("d-m-Y",strtotime($date));
	$time = $row['time'];
	$subject = $row['subject'];
	$name = $row['name'];
	$email = $row['email'];
	$id = $row['id'];
	 echo '<tr><td>'.$c++.'</td>';
	echo '<td><a title="Click to open feedback" href="dash.php?q=3&fid='.$id.'">'.$subject.'</a></td><td>'.$email.'</td><td>'.$date.'</td><td>'.$time.'</td><td>'.$name.'</td>
	<td><a title="Open Feedback" href="dash.php?q=3&fid='.$id.'"><b><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span></b></a></td>';
	echo '<td><a title="Delete Feedback" href="update.php?fdid='.$id.'"><b><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></b></a></td>

	</tr>';
}
echo '</table></div>';
}
?>
<!--feedback closed-->
<div class="row">
<!--feedback reading portion start-->
<?php if(@$_GET['fid']) {
echo '<br />';
$id=@$_GET['fid'];
$result = mysqli_query($con,"SELECT * FROM feedback WHERE id='$id' ") or die('Error');
while($row = mysqli_fetch_array($result)) {
	$name = $row['name'];
	$subject = $row['subject'];
	$date = $row['date'];
	$date= date("d-m-Y",strtotime($date));
	$time = $row['time'];
	$feedback = $row['feedback'];
	
echo '<div class="col-md-6">';
    
    
 
echo '<div class="panel panel-default">

    <div class="panel-heading">

        <a
            title="Back to Archive"
            href="dash.php?q=3"
            class="btn btn-default btn-sm"
        >
            <span class="glyphicon glyphicon-level-up"></span>
            Back
        </a>

        <a
            title="Reply to Feedback"
            href="dash.php?q=3&fid='. $id.'&reply=1"
            class="btn btn-primary btn-sm"
        >
            <span class="glyphicon glyphicon-share-alt"></span>
            Reply
        </a>

        <h2 style="text-align:center; margin-top:-15px;">
            <b>' .$subject.'</b>
        </h2>

    </div>

    <div
        class="mCustomScrollbar"
        data-mcs-theme="dark"
        style="
            margin-left:10px;
            margin-right:10px;
            max-height:450px;
            line-height:35px;
            padding:10px;
        "
    >

        <div>
            <b>DATE:</b>'.$date.'
        </div>

        <div>
            <b>TIME:</b>'.$time.'
        </div>

        <div>
            <b>BY:</b>'.$name.'
        </div>

        <div>
            
            
        </div>

        <hr>

        <div style="line-height:28px;">
            
        </div>

    </div>
</div>
</div>';


}
}

?>
<?php
 if (isset($_GET['reply']) && $_GET['reply'] == '1') {

            echo '<div class="col-md-6">
            <div class="panel panel-primary">

                <div class="panel-heading">
                    <b>
                        <span class="glyphicon glyphicon-envelope"></span>
                        Reply to Feedback
                    </b>
                </div>

                <div class="panel-body">

                    <form method="post" action="feedback_reply.php">

                        <input
                            type="hidden"
                            name="feedback_id"
                            value="'.$id.'"
                        >

                        <div class="form-group">
                            <label>To Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="'.$email.'"
                                readonly
                            >
                        </div>

                        <div class="form-group">
                            <label>Subject</label>

                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                value="Re: '.$subject.'"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Message</label>

                            <textarea
                                name="message"
                                class="form-control"
                                rows="8"
                                placeholder="Write your reply..."
                                required
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            name="send_reply"
                            class="btn btn-success"
                        >
                            <span class="glyphicon glyphicon-send"></span>
                            Send Reply
                        </button>

                        <a
                            href="dash.php?q=3&fid='.$id.'"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>

                    </form>

                </div>

            </div></div>';
        }

?>

</div>






<?php

if (@$_GET['q']==4)

   {

    echo ' 


<div class="col-md-12">
         <div class="box box-primary">
            
            <!-- /.box-header -->
			
              <div class="box-body">

<div class="head" style="min-height:430px;">

  <h1 class="br"><span> <em>FEEDBACK </em></span></h1>

<div class="pure-form" style="font-size:14px;margin-top:20px"><br />
<form role="form"  method="post" action="feed.php?q=3">

<div class="row">

<div class="col-md-3"><b>your Trade:</b><br /><br /><br /><b>Your Problem:</b></div>

<div class="col-md-9">

<!-- Text input-->

<div class="form-group">

  <input id="name" name="name" placeholder="Enter your name" class="form-control input-md" type="text"><br />    

   <input id="name" name="subject" placeholder="Enter short description of your feedback" class="form-control input-md" type="text">    



</div>

</div>

</div><!--End of row-->



<div class="row">

<div class="col-md-3"><b>E-Mail ID:</b></div>

<div class="col-md-9">

<!-- Text input-->

<div class="form-group">

  <input id="email" name="email" placeholder="Enter your e-mail" class="form-control input-md" type="email">    

 </div>

</div>

</div><!--End of row-->



<div class="form-group"> 

<textarea rows="5" cols="8" name="feedback" class="form-control" placeholder="Write feedback here. Keep it clean and simple."></textarea>

</div>

<div class="form-group" align="center">

<font style="font-size:12px;font-weight:bold"><input type="submit" name="submit" value="Send My Feedback" class="btn btn-primary" /></font>

</div>

</form>
</div>

<div class="col-md-3"></div></div>

</div></div>

</div>
';

}

?>


<!--Feedback reading portion closed-->

        
			
<?php

     if (@$_GET['q'] == 2) {
         
         ?>
         
         
            
<!-- Send sms box start -->
<div class="col-md-12">
<div class="box box-primary">
<!-- /.header-->
<div class="box-header with-border">
  <h3 class="box-title">Teacher's</h3>
  
   <div style="text-align: right";>
 <h4 class="btn btn-success btn-sm"> 
</h4>
    </div>
    
</div>
 <!-- /.header-->


<div class="box-body">

<!-- /box-body-->
<?php
// Number of records per page
$limit = 10;

// Current page
$page = isset($_GET['show']) && is_numeric($_GET['show']) ? (int)$_GET['show'] : 1;
if ($page < 1) {
$page = 1;
}

// Offset
$offset = ($page - 1) * $limit;

// Get total records
$totalQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM rank");
$totalRow = mysqli_fetch_assoc($totalQuery);

$totalRecords = $totalRow['total'];

// Main Query with JOIN
$sql = "
SELECT
r.score,
r.time,
r.username AS rank_username,
r.class,
r.date,
s.scl_name,
s.scl_phone_no,
s.scl_class,
s.scl_roll_no,
s.scl_session,
s.scl_reg_no,
s.id AS user_id,
h.eid
FROM rank r
LEFT JOIN rrsv_student_registration s
ON s.id = (
SELECT MAX(id)
FROM rrsv_student_registration
WHERE scl_reg_no = r.username
)
LEFT JOIN history h
ON h.id = (
SELECT MAX(id)
FROM history
WHERE username = r.username
)
ORDER BY r.score DESC, r.time ASC
LIMIT $offset,$limit;
";

$q = mysqli_query($con, $sql) or die(mysqli_error($con));

// Start Table
echo '<table id="example1" class="table table-bordered table-striped">
<thead>
<tr>
<th>S.NO</th>
<th>Name</th>
<th>Branch</th>
<th>Class</th>
<th>Username</th>
<th>Roll No</th>
<th>Score</th>
<th>Results</th>
<th>Actions</th>
</tr>
</thead>
<tbody>';

$c = $offset;

while ($row = mysqli_fetch_assoc($q)) {
$c++;

$name = $row['scl_name'] ?? '';
$branch = $row['scl_session'] ?? '';
$class = $row['scl_class'] ?? '';
$username = $row['scl_reg_no'] ?? '';
$rollno = $row['scl_roll_no'] ?? '';
$score = $row['score'] ?? '';
$eid = $row['eid'] ?? '';
$user_id = $row['user_id'] ?? '';

echo '<tr>
<td>' . $c . '</td>
<td>' . $name . '</td>
<td>' . $branch . '</td>
<td>' . $class . '</td>
<td>' . $username . '</td>
<td>' . $rollno . '</td>
<td>' . $score . '</td>

<td>
<a href="results.php?q=review&eid=' . $eid . '&username=' . $username . '" 
class="btn d-none" style="background:darkred;color:white">
<b>View Result</b>
</a>
</td>

<td>
<a href="update.php?q=rmrah&eid=' . $eid . '&username=' . $username . '" 
class="btn" style="background:skyblue;color:white"
onclick="return confirm(\'Are you sure?\')">
<i class="fa fa-trash"></i>
</a>

<a href="results.php?q=result&eid=' . $eid . '&username=' . $username . '" 
class="btn btn-warning btn-xs">
<i class="fa fa-eye"></i>
</a>

<a href="print_invoice.php?username=' . $username . '&id=' . $eid . '" 
class="btn btn-success btn-xs d-none">
<i class="fa fa-print"></i>
</a>
</td>
</tr>';
}

echo '</tbody></table>';
?>

<!-- Pagination -->
<div style="text-align:center; margin-top:15px;">
<?php
if ($totalPages > 1) {

// Previous button
if ($page > 1) {
echo '<a class="btn btn-primary" href="?show=' . ($page - 1) . '">Previous</a> ';
}

// Page numbers
for ($i = 1; $i <= $totalPages; $i++) {
if ($i == $page) {
echo '<span class="btn btn-success">' . $i . '</span> ';
} else {
echo '<a class="btn btn-default" href="?show=' . $i . '">' . $i . '</a> ';
}
}

// Next button
if ($page < $totalPages) {
echo '<a class="btn btn-primary" href="?show=' . ($page + 1) . '">Next</a>';
}
}
?>
</div>
<?php
}
   ?>       
                      <?php
if (@$_GET['q'] == 'assign_quiz') {
?>
<div class="col-md-12">
<div class="box box-primary">

<div class="box-header with-border">
    <h3 class="box-title">Assign Quiz to Students</h3>
</div>

<div class="box-body">

<div class="row">

<?php

$sql = "SELECT
            qa.id,
            qa.quiz_id,
            qa.student_id,
            qa.class,
            qa.session,
            qa.unit,
            qa.assigned_at,
            qa.status,
            qa.r_status,
            s.scl_name,
            s.scl_reg_no
        FROM quiz_assignments qa
        LEFT JOIN rrsv_student_registration s
            ON qa.student_id = s.id
        ORDER BY qa.id DESC";

$result = mysqli_query($con, $sql);
?>

<table id="example1" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Registration No</th>
            <th>Quiz ID</th>
            <th>Class</th>
            <th>Session</th>
            <th>Unit</th>
            <th>Assigned At</th>
            <th>Status</th>
            <th>Result Status</th>
        </tr>
    </thead>

    <tbody>

<?php
while($row = mysqli_fetch_assoc($result))
{
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['scl_name']; ?></td>
    <td><?php echo $row['scl_reg_no']; ?></td>
    <td><?php echo $row['quiz_id']; ?></td>
    <td><?php echo $row['class']; ?></td>
    <td><?php echo $row['session']; ?></td>
    <td><?php echo $row['unit']; ?></td>
    <td><?php echo $row['assigned_at']; ?></td>
    <!-- Exam Status -->
<td>
    <label class="switch">
        <input type="checkbox"
               class="toggle-status"
               data-id="<?php echo $row['id']; ?>"
               data-type="exam"
               <?php echo ($row['status'] == 'active') ? 'checked' : ''; ?>>
        <span class="slider round"></span>
    </label>
</td>

<!-- Result Status -->
<td>
    <label class="switch">
        <input type="checkbox"
               class="toggle-status"
               data-id="<?php echo $row['id']; ?>"
               data-type="result"
               <?php echo ($row['r_status'] == 'Active') ? 'checked' : ''; ?>>
        <span class="slider round"></span>
    </label>
</td>
    
</tr>
<?php
}
?>

    </tbody>
</table>
    

    <!-- Select Class -->
    <div class="col-md-3">
        <div class="form-group">
            <label>Select Class</label>
            <select id="class" class="form-control" required>
                <option value="">-- Select Class --</option>
                <?php
                $q = mysqli_query($con,"SELECT DISTINCT scl_class FROM rrsv_student_registration");
                while($row = mysqli_fetch_assoc($q)){
                    echo "<option value='{$row['scl_class']}'>{$row['scl_class']}</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <!-- Select Unit -->
    <div class="col-md-3">
        <div class="form-group">
            <label>Select Unit</label>
            <select id="unit" class="form-control" required>
                <option value="1">1st Unit</option>
                <option value="2">2nd Unit</option>
                <option value="3">3rd Unit</option>
               
            </select>
        </div>
    </div>

    <!-- Select Session -->
    <div class="col-md-3">
        <div class="form-group">
            <label>Select Session</label>
            <select id="session" class="form-control" required>
                <option value="">-- Select Session --</option>
                <?php
                $q = mysqli_query($con,"SELECT DISTINCT scl_session FROM rrsv_student_registration");
                while($row = mysqli_fetch_assoc($q)){
                    echo "<option value='{$row['scl_session']}'>{$row['scl_session']}</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <!-- Select Quiz -->
    <div class="col-md-3">
        <div class="form-group">
            <label>Select Quiz</label>
            <select id="quiz_id" class="form-control" required>
                <option value="">-- Select Quiz --</option>
                
            </select>
        </div>
    </div>

</div>
<!-- Student List -->
<div id="student_list"></div>

<br>

<button id="assignBtn" class="btn btn-success">
    <i class="fa fa-check"></i> Assign Quiz
</button>

</div>
</div>
</div>

    <?php
}
?>
              
              <div class="box-footer">
                
				 <!-- /.footer-->
				
                </div>
              <!-- /.box-footer -->
             
          </div>
          
          
   
        <!-- Send sms box end -->
		
		
            
 	<!--<div class="box-body"  style="background-color:#E6E6FA">-->
			 
	
	 <!--</div>-->
          
          </div>
          <!-- /.box -->
		</div>
		<!-- template message box end-->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Footer Start -->
  <footer class="main-footer" style="text-align:center;">

    

    <strong>Copyright &copy; 2013-2021 </strong> All rights

    reserved.

  </footer>  <!-- Footer End -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<?php include_once('../footer.php');?>


<!-- ./wrapper -->
<!-- jQuery 2.2.3 -->
<script src="../js/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../js/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../js/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>

<script>
$(document).ready(function(){
function loadStudents(){
    var class_name = $('#class').val();
    var session = $('#session').val();
    var quiz_id = $('#quiz_id').val();

    if(class_name != '' && session != '' && quiz_id != ''){
        $.ajax({
            url: 'get_students.php',
            type: 'POST',
            data: {
                class: class_name,
                session: session,
                quiz_id: quiz_id
            },
            success: function(data){
                $('#student_list').html(data);
            }
        });
    }
}

$('#class, #session, #quiz_id').change(function(){
    loadStudents();
});

 $('#assignBtn').click(function () {

    var class_name = $('#class').val();
    var unit       = $('#unit').val();
    var session    = $('#session').val();
    var quiz_id    = $('#quiz_id').val();

    // Class validation
    if (class_name === '') {
        alert('Please Select Class');
        $('#class').focus();
        return false;
    }

    // Unit validation
    if (unit === '') {
        alert('Please Select Unit');
        $('#unit').focus();
        return false;
    }

    // Session validation
    if (session === '') {
        alert('Please Select Session');
        $('#session').focus();
        return false;
    }

    // Quiz validation
    if (quiz_id === '') {
        alert('Please Select Quiz');
        $('#quiz_id').focus();
        return false;
    }

    // Student selection
    var students = [];

    var table = $('#studentTable').DataTable();

    table.$('.student_checkbox:checked').each(function () {
        students.push($(this).val());
    });

    // Student validation
    if (students.length === 0) {
        alert('Please Select At Least One Student');
        return false;
    }

    // All validation passed
    $.ajax({
        url: 'assign_quiz.php',
        type: 'POST',
        data: {
            quiz_id: quiz_id,
            students: students,
            class: class_name,
            session: session,
            unit: unit
        },

        beforeSend: function () {
            $('#assignBtn')
                .prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Assigning...');
        },

        success: function (res) {

            alert(res);

            $('#assignBtn')
                .prop('disabled', false)
                .html('<i class="fa fa-check"></i> Assign Quiz');

        },

        error: function () {

            alert('Something went wrong. Please try again.');

            $('#assignBtn')
                .prop('disabled', false)
                .html('<i class="fa fa-check"></i> Assign Quiz');
        }
    });

});

});
$('#studentTable').DataTable({
    "pageLength": 10,
    "lengthMenu": [10, 25, 50, 100],
    "paging": true,
    "searching": true,
    "ordering": true,
    "destroy": true // IMPORTANT when reloading via AJAX
});


</script>
<script>
$(document).on('change', '.toggle-status', function () {

    var id = $(this).data('id');
    var type = $(this).data('type');

    var status;

    if (type == 'exam') {
        status = $(this).is(':checked') ? 'active' : 'inactive';
    } else {
        status = $(this).is(':checked') ? 'Active' : 'Inactive';
    }

    $.ajax({
        url: 'update.php?' + type + '=1',
        type: 'POST',
        data: {
            id: id,
            status: status
        },
        success: function (response) {
            console.log(response);
        },
        error: function () {
            alert('Update failed.');
        }
    });

});
</script>
<script>
$(document).ready(function () {

    function loadQuiz() {

        var class_name = $('#class').val();
        var unit = $('#unit').val();

        $('#quiz_id').html(
            '<option value="">Loading Quiz...</option>'
        );

        if (class_name === '' || unit === '') {

            $('#quiz_id').html(
                '<option value="">-- Select Class & Unit First --</option>'
            );

            return;
        }

        $.ajax({
            url: 'get_quiz.php',
            type: 'POST',
            data: {
                class: class_name,
                unit: unit
            },

            success: function (response) {

                $('#quiz_id').html(response);

            },

            error: function () {

                $('#quiz_id').html(
                    '<option value="">Unable to load quiz</option>'
                );

            }
        });
    }


    $('#class, #unit').on('change', function () {
        loadQuiz();
    });

});
</script>

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