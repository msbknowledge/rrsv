<?php

session_start();

require_once 'dbConnection.php';


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['chat_logged_in']) ||
    $_SESSION['chat_logged_in'] !== true
) {
    header("Location: chat_login.php");
    exit;
}


$current_user_id = (int)($_SESSION['chat_user_id'] ?? 0);
$current_username = $_SESSION['chat_username'] ?? '';
$current_role = $_SESSION['chat_role'] ?? '';


if ($current_user_id <= 0) {
    header("Location: chat_login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| JSON RESPONSE
|--------------------------------------------------------------------------
*/

function json_response($data)
{
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| AJAX
|--------------------------------------------------------------------------
*/

if (isset($_GET['ajax'])) {

    $ajax = $_GET['ajax'];


    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'users') {

        $users = [];


        $sql = "
            SELECT
                u.id,
                u.username,
                u.role,
                u.status,

                (
                    SELECT COUNT(*)
                    FROM chat_messages m
                    WHERE m.sender_id = u.id
                      AND m.receiver_id = ?
                      AND m.is_read = 0
                      AND m.status = 'sent'
                ) AS unread_count,

                (
                    SELECT m.message
                    FROM chat_messages m
                    WHERE
                    (
                        m.sender_id = ?
                        AND m.receiver_id = u.id
                    )
                    OR
                    (
                        m.sender_id = u.id
                        AND m.receiver_id = ?
                    )
                    ORDER BY m.created_at DESC, m.id DESC
                    LIMIT 1
                ) AS last_message,

                (
                    SELECT m.created_at
                    FROM chat_messages m
                    WHERE
                    (
                        m.sender_id = ?
                        AND m.receiver_id = u.id
                    )
                    OR
                    (
                        m.sender_id = u.id
                        AND m.receiver_id = ?
                    )
                    ORDER BY m.created_at DESC, m.id DESC
                    LIMIT 1
                ) AS last_message_time

            FROM chat_user u

            WHERE u.id != ?
              AND u.status = 'active'

            ORDER BY
                unread_count DESC,
                last_message_time DESC,
                u.username ASC
        ";


        $stmt = mysqli_prepare($con, $sql);


        if (!$stmt) {

            json_response([
                'success' => false,
                'message' => mysqli_error($con)
            ]);
        }


        mysqli_stmt_bind_param(
            $stmt,
            "iiiiii",
            $current_user_id,
            $current_user_id,
            $current_user_id,
            $current_user_id,
            $current_user_id,
            $current_user_id
        );


        if (!mysqli_stmt_execute($stmt)) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        $result = mysqli_stmt_get_result($stmt);


        if (!$result) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        while ($row = mysqli_fetch_assoc($result)) {

            $users[] = $row;
        }


        json_response([
            'success' => true,
            'users' => $users
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GROUPS
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'groups') {

        $groups = [];


        $sql = "
            SELECT
                g.id,
                g.group_name,
                g.description,
                g.created_by,
                g.status,
                g.created_at,

                (
                    SELECT COUNT(*)
                    FROM chat_group_member gm2
                    WHERE gm2.group_id = g.id
                      AND gm2.status = 'active'
                ) AS member_count,

                (
                    SELECT COUNT(*)
                    FROM chat_group_message gm3
                    WHERE gm3.group_id = g.id
                      AND gm3.sender_id != ?
                      AND gm3.status = 'sent'
                      AND NOT EXISTS (
                          SELECT 1
                          FROM chat_group_message_read r
                          WHERE r.message_id = gm3.id
                            AND r.user_id = ?
                      )
                ) AS unread_count,

                (
                    SELECT gm4.message
                    FROM chat_group_message gm4
                    WHERE gm4.group_id = g.id
                      AND gm4.status = 'sent'
                    ORDER BY gm4.created_at DESC, gm4.id DESC
                    LIMIT 1
                ) AS last_message,

                (
                    SELECT gm5.created_at
                    FROM chat_group_message gm5
                    WHERE gm5.group_id = g.id
                      AND gm5.status = 'sent'
                    ORDER BY gm5.created_at DESC, gm5.id DESC
                    LIMIT 1
                ) AS last_message_time

            FROM chat_group g

            INNER JOIN chat_group_member gm
                ON gm.group_id = g.id

            WHERE gm.user_id = ?
              AND gm.status = 'active'
              AND g.status = 'active'

            ORDER BY
                unread_count DESC,
                last_message_time DESC,
                g.group_name ASC
        ";


        $stmt = mysqli_prepare($con, $sql);


        if (!$stmt) {

            json_response([
                'success' => false,
                'message' => mysqli_error($con)
            ]);
        }


        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $current_user_id,
            $current_user_id,
            $current_user_id
        );


        if (!mysqli_stmt_execute($stmt)) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        $result = mysqli_stmt_get_result($stmt);


        if (!$result) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        while ($row = mysqli_fetch_assoc($result)) {

            $groups[] = $row;
        }


        json_response([
            'success' => true,
            'groups' => $groups
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE MESSAGES
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'messages') {

        $receiver_id = (int)($_GET['user_id'] ?? 0);


        if ($receiver_id <= 0) {

            json_response([
                'success' => false,
                'message' => 'Invalid user.'
            ]);
        }


        /*
        | Mark as read
        */

        $read_sql = "
            UPDATE chat_messages
            SET is_read = 1
            WHERE sender_id = ?
              AND receiver_id = ?
              AND is_read = 0
              AND status = 'sent'
        ";


        $read_stmt = mysqli_prepare(
            $con,
            $read_sql
        );


        mysqli_stmt_bind_param(
            $read_stmt,
            "ii",
            $receiver_id,
            $current_user_id
        );


        mysqli_stmt_execute($read_stmt);


        /*
        | Conversation
        */

        $sql = "
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
                (
                    m.sender_id = ?
                    AND m.receiver_id = ?
                )
                OR
                (
                    m.sender_id = ?
                    AND m.receiver_id = ?
                )
            )

            AND m.status = 'sent'

            ORDER BY
                m.created_at ASC,
                m.id ASC
        ";


        $stmt = mysqli_prepare(
            $con,
            $sql
        );


        if (!$stmt) {

            json_response([
                'success' => false,
                'message' => mysqli_error($con)
            ]);
        }


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
        | Receiver
        */

        $user_stmt = mysqli_prepare(
            $con,
            "
            SELECT id, username, role, status
            FROM chat_user
            WHERE id = ?
            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $user_stmt,
            "i",
            $receiver_id
        );


        mysqli_stmt_execute($user_stmt);


        $user_result = mysqli_stmt_get_result(
            $user_stmt
        );


        $receiver = mysqli_fetch_assoc(
            $user_result
        );


        json_response([
            'success' => true,
            'messages' => $messages,
            'receiver' => $receiver
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SEND PRIVATE MESSAGE
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'send') {

        $receiver_id =
            (int)($_POST['receiver_id'] ?? 0);

        $message =
            trim($_POST['message'] ?? '');


        if ($receiver_id <= 0) {

            json_response([
                'success' => false,
                'message' => 'Invalid receiver.'
            ]);
        }


        if ($message === '') {

            json_response([
                'success' => false,
                'message' => 'Message cannot be empty.'
            ]);
        }


        /*
        | Check receiver
        */

        $check_stmt = mysqli_prepare(
            $con,
            "
            SELECT id
            FROM chat_user
            WHERE id = ?
              AND status = 'active'
            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $check_stmt,
            "i",
            $receiver_id
        );


        mysqli_stmt_execute(
            $check_stmt
        );


        $check_result =
            mysqli_stmt_get_result(
                $check_stmt
            );


        if (!mysqli_fetch_assoc($check_result)) {

            json_response([
                'success' => false,
                'message' => 'Receiver not found.'
            ]);
        }


        /*
        | Insert
        */

        $stmt = mysqli_prepare(
            $con,
            "
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
            "
        );


        if (!$stmt) {

            json_response([
                'success' => false,
                'message' => mysqli_error($con)
            ]);
        }


        mysqli_stmt_bind_param(
            $stmt,
            "iis",
            $current_user_id,
            $receiver_id,
            $message
        );


        if (!mysqli_stmt_execute($stmt)) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        json_response([
            'success' => true,
            'message_id' => mysqli_insert_id($con)
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE GROUP
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'create_group') {

        $group_name =
            trim($_POST['group_name'] ?? '');

        $description =
            trim($_POST['description'] ?? '');


        if ($group_name === '') {

            json_response([
                'success' => false,
                'message' => 'Group name is required.'
            ]);
        }


        mysqli_begin_transaction($con);


        try {

            /*
            | Create group
            */

            $stmt = mysqli_prepare(
                $con,
                "
                INSERT INTO chat_group
                (
                    group_name,
                    description,
                    created_by,
                    status
                )
                VALUES
                (?, ?, ?, 'active')
                "
            );


            if (!$stmt) {
                throw new Exception(
                    mysqli_error($con)
                );
            }


            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $group_name,
                $description,
                $current_user_id
            );


            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(
                    mysqli_stmt_error($stmt)
                );
            }


            $group_id =
                mysqli_insert_id($con);


            /*
            | Add creator as admin
            */

            $member_stmt = mysqli_prepare(
                $con,
                "
                INSERT INTO chat_group_member
                (
                    group_id,
                    user_id,
                    member_role,
                    status
                )
                VALUES
                (?, ?, 'admin', 'active')
                "
            );


            if (!$member_stmt) {
                throw new Exception(
                    mysqli_error($con)
                );
            }


            mysqli_stmt_bind_param(
                $member_stmt,
                "ii",
                $group_id,
                $current_user_id
            );


            if (!mysqli_stmt_execute($member_stmt)) {
                throw new Exception(
                    mysqli_stmt_error($member_stmt)
                );
            }


            mysqli_commit($con);


            json_response([
                'success' => true,
                'group_id' => $group_id,
                'message' => 'Group created successfully.'
            ]);

        } catch (Exception $e) {

            mysqli_rollback($con);


            json_response([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GROUP MESSAGES
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'group_messages') {

        $group_id =
            (int)($_GET['group_id'] ?? 0);


        if ($group_id <= 0) {

            json_response([
                'success' => false,
                'message' => 'Invalid group.'
            ]);
        }


        /*
        | Check membership
        */

        $member_stmt = mysqli_prepare(
            $con,
            "
            SELECT
                gm.member_role,
                g.group_name,
                g.description,
                g.created_by

            FROM chat_group_member gm

            INNER JOIN chat_group g
                ON g.id = gm.group_id

            WHERE gm.group_id = ?
              AND gm.user_id = ?
              AND gm.status = 'active'
              AND g.status = 'active'

            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $member_stmt,
            "ii",
            $group_id,
            $current_user_id
        );


        mysqli_stmt_execute(
            $member_stmt
        );


        $member_result =
            mysqli_stmt_get_result(
                $member_stmt
            );


        $group_info =
            mysqli_fetch_assoc(
                $member_result
            );


        if (!$group_info) {

            json_response([
                'success' => false,
                'message' => 'You are not a member of this group.'
            ]);
        }


        /*
        | Messages
        */

        $stmt = mysqli_prepare(
            $con,
            "
            SELECT
                gm.id,
                gm.group_id,
                gm.sender_id,
                gm.message,
                gm.message_type,
                gm.attachment,
                gm.created_at,

                u.username AS sender_username,
                u.role AS sender_role

            FROM chat_group_message gm

            INNER JOIN chat_user u
                ON u.id = gm.sender_id

            WHERE gm.group_id = ?
              AND gm.status = 'sent'

            ORDER BY
                gm.created_at ASC,
                gm.id ASC
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $group_id
        );


        mysqli_stmt_execute(
            $stmt
        );


        $result =
            mysqli_stmt_get_result(
                $stmt
            );


        $messages = [];


        while ($row = mysqli_fetch_assoc($result)) {

            $messages[] = $row;
        }


        /*
        | Mark unread group messages as read
        */

        foreach ($messages as $msg) {

            if (
                (int)$msg['sender_id']
                !== $current_user_id
            ) {

                $read_stmt = mysqli_prepare(
                    $con,
                    "
                    INSERT IGNORE INTO chat_group_message_read
                    (
                        group_id,
                        message_id,
                        user_id
                    )
                    VALUES
                    (?, ?, ?)
                    "
                );


                mysqli_stmt_bind_param(
                    $read_stmt,
                    "iii",
                    $group_id,
                    $msg['id'],
                    $current_user_id
                );


                mysqli_stmt_execute(
                    $read_stmt
                );
            }
        }


        json_response([
            'success' => true,
            'group' => $group_info,
            'messages' => $messages
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SEND GROUP MESSAGE
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'send_group') {

        $group_id =
            (int)($_POST['group_id'] ?? 0);

        $message =
            trim($_POST['message'] ?? '');


        if ($group_id <= 0) {

            json_response([
                'success' => false,
                'message' => 'Invalid group.'
            ]);
        }


        if ($message === '') {

            json_response([
                'success' => false,
                'message' => 'Message cannot be empty.'
            ]);
        }


        /*
        | Check member
        */

        $stmt = mysqli_prepare(
            $con,
            "
            SELECT id
            FROM chat_group_member
            WHERE group_id = ?
              AND user_id = ?
              AND status = 'active'
            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $group_id,
            $current_user_id
        );


        mysqli_stmt_execute(
            $stmt
        );


        $result =
            mysqli_stmt_get_result(
                $stmt
            );


        if (!mysqli_fetch_assoc($result)) {

            json_response([
                'success' => false,
                'message' => 'You are not a member of this group.'
            ]);
        }


        /*
        | Insert group message
        */

        $stmt = mysqli_prepare(
            $con,
            "
            INSERT INTO chat_group_message
            (
                group_id,
                sender_id,
                message,
                message_type,
                status
            )
            VALUES
            (?, ?, ?, 'text', 'sent')
            "
        );


        if (!$stmt) {

            json_response([
                'success' => false,
                'message' => mysqli_error($con)
            ]);
        }


        mysqli_stmt_bind_param(
            $stmt,
            "iis",
            $group_id,
            $current_user_id,
            $message
        );


        if (!mysqli_stmt_execute($stmt)) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        json_response([
            'success' => true,
            'message_id' => mysqli_insert_id($con)
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GROUP MEMBERS
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'group_members') {

        $group_id =
            (int)($_GET['group_id'] ?? 0);


        if ($group_id <= 0) {

            json_response([
                'success' => false,
                'message' => 'Invalid group.'
            ]);
        }


        $stmt = mysqli_prepare(
            $con,
            "
            SELECT
                gm.id,
                gm.user_id,
                gm.member_role,
                gm.joined_at,
                u.username,
                u.role

            FROM chat_group_member gm

            INNER JOIN chat_user u
                ON u.id = gm.user_id

            WHERE gm.group_id = ?
              AND gm.status = 'active'

            ORDER BY
                gm.member_role DESC,
                u.username ASC
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $group_id
        );


        mysqli_stmt_execute(
            $stmt
        );


        $result =
            mysqli_stmt_get_result(
                $stmt
            );


        $members = [];


        while ($row = mysqli_fetch_assoc($result)) {

            $members[] = $row;
        }


        json_response([
            'success' => true,
            'members' => $members
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | AVAILABLE USERS FOR GROUP
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'available_users') {

        $group_id =
            (int)($_GET['group_id'] ?? 0);


        if ($group_id <= 0) {

            json_response([
                'success' => false,
                'message' => 'Invalid group.'
            ]);
        }


        $stmt = mysqli_prepare(
            $con,
            "
            SELECT
                u.id,
                u.username,
                u.role

            FROM chat_user u

            WHERE u.status = 'active'

              AND u.id NOT IN
              (
                  SELECT user_id
                  FROM chat_group_member
                  WHERE group_id = ?
                    AND status = 'active'
              )

            ORDER BY u.username ASC
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $group_id
        );


        mysqli_stmt_execute(
            $stmt
        );


        $result =
            mysqli_stmt_get_result(
                $stmt
            );


        $users = [];


        while ($row = mysqli_fetch_assoc($result)) {

            $users[] = $row;
        }


        json_response([
            'success' => true,
            'users' => $users
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ADD GROUP MEMBER
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'add_member') {

        $group_id =
            (int)($_POST['group_id'] ?? 0);

        $user_id =
            (int)($_POST['user_id'] ?? 0);


        if (
            $group_id <= 0 ||
            $user_id <= 0
        ) {

            json_response([
                'success' => false,
                'message' => 'Invalid group or user.'
            ]);
        }


        /*
        | Check current user is group admin
        */

        $admin_stmt = mysqli_prepare(
            $con,
            "
            SELECT id
            FROM chat_group_member
            WHERE group_id = ?
              AND user_id = ?
              AND member_role = 'admin'
              AND status = 'active'
            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $admin_stmt,
            "ii",
            $group_id,
            $current_user_id
        );


        mysqli_stmt_execute(
            $admin_stmt
        );


        $admin_result =
            mysqli_stmt_get_result(
                $admin_stmt
            );


        if (!mysqli_fetch_assoc($admin_result)) {

            json_response([
                'success' => false,
                'message' => 'Only group admin can add members.'
            ]);
        }


        /*
        | Add member
        */

        $stmt = mysqli_prepare(
            $con,
            "
            INSERT INTO chat_group_member
            (
                group_id,
                user_id,
                member_role,
                status
            )
            VALUES
            (?, ?, 'member', 'active')

            ON DUPLICATE KEY UPDATE
                status = 'active'
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $group_id,
            $user_id
        );


        if (!mysqli_stmt_execute($stmt)) {

            json_response([
                'success' => false,
                'message' => mysqli_stmt_error($stmt)
            ]);
        }


        json_response([
            'success' => true,
            'message' => 'Member added successfully.'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE GROUP MEMBER
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'remove_member') {

        $group_id =
            (int)($_POST['group_id'] ?? 0);

        $user_id =
            (int)($_POST['user_id'] ?? 0);


        if (
            $group_id <= 0 ||
            $user_id <= 0
        ) {

            json_response([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }


        /*
        | Check admin
        */

        $admin_stmt = mysqli_prepare(
            $con,
            "
            SELECT id
            FROM chat_group_member
            WHERE group_id = ?
              AND user_id = ?
              AND member_role = 'admin'
              AND status = 'active'
            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $admin_stmt,
            "ii",
            $group_id,
            $current_user_id
        );


        mysqli_stmt_execute(
            $admin_stmt
        );


        $admin_result =
            mysqli_stmt_get_result(
                $admin_stmt
            );


        if (!mysqli_fetch_assoc($admin_result)) {

            json_response([
                'success' => false,
                'message' => 'Only group admin can remove members.'
            ]);
        }


        /*
        | Don't remove yourself through this action
        */

        if ($user_id === $current_user_id) {

            json_response([
                'success' => false,
                'message' => 'Admin cannot remove himself.'
            ]);
        }


        $stmt = mysqli_prepare(
            $con,
            "
            UPDATE chat_group_member
            SET status = 'inactive'
            WHERE group_id = ?
              AND user_id = ?
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $group_id,
            $user_id
        );


        mysqli_stmt_execute($stmt);


        json_response([
            'success' => true,
            'message' => 'Member removed.'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL UNREAD
    |--------------------------------------------------------------------------
    */

    if ($ajax === 'unread') {

        /*
        | Private unread
        */

        $stmt = mysqli_prepare(
            $con,
            "
            SELECT COUNT(*) AS total
            FROM chat_messages
            WHERE receiver_id = ?
              AND is_read = 0
              AND status = 'sent'
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $current_user_id
        );


        mysqli_stmt_execute($stmt);


        $result =
            mysqli_stmt_get_result($stmt);


        $row =
            mysqli_fetch_assoc($result);


        $private_unread =
            (int)$row['total'];


        /*
        | Group unread
        */

        $group_unread = 0;


        $stmt = mysqli_prepare(
            $con,
            "
            SELECT COUNT(*) AS total

            FROM chat_group_message gm

            INNER JOIN chat_group_member member
                ON member.group_id = gm.group_id
               AND member.user_id = ?
               AND member.status = 'active'

            WHERE gm.sender_id != ?
              AND gm.status = 'sent'

              AND NOT EXISTS
              (
                  SELECT 1
                  FROM chat_group_message_read r
                  WHERE r.message_id = gm.id
                    AND r.user_id = ?
              )
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $current_user_id,
            $current_user_id,
            $current_user_id
        );


        mysqli_stmt_execute($stmt);


        $result =
            mysqli_stmt_get_result($stmt);


        $row =
            mysqli_fetch_assoc($result);


        $group_unread =
            (int)$row['total'];


        json_response([
            'success' => true,
            'private' => $private_unread,
            'group' => $group_unread,
            'total' =>
                $private_unread +
                $group_unread
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | INVALID
    |--------------------------------------------------------------------------
    */

    json_response([
        'success' => false,
        'message' => 'Invalid AJAX request.'
    ]);
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
   SIDEBAR
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

    padding: 12px 15px;

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

    font-size: 15px;

    font-weight: bold;
}


.my-role {

    font-size: 11px;

    opacity: .85;
}


.logout {

    color: #fff;

    text-decoration: none;

    font-size: 12px;
}


/* SEARCH */

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


/* TABS */

.chat-tabs {

    display: flex;

    border-bottom: 1px solid #ddd;
}


.chat-tab {

    flex: 1;

    border: 0;

    background: #fff;

    padding: 12px 5px;

    cursor: pointer;

    font-weight: bold;

    color: #777;
}


.chat-tab.active {

    color: #007bff;

    border-bottom: 2px solid #007bff;
}


/* USER LIST */

.user-list {

    flex: 1;

    overflow-y: auto;
}


.user-item {

    min-height: 72px;

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


.group-avatar {

    background: #28a745;
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

    padding: 0 5px;

    border-radius: 10px;

    font-size: 11px;

    display: flex;

    align-items: center;

    justify-content: center;
}


/* CREATE GROUP BUTTON */

.create-group-btn {

    margin: 10px;

    height: 40px;

    border: 0;

    border-radius: 5px;

    background: #28a745;

    color: #fff;

    cursor: pointer;

    font-weight: bold;
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
    padding: 15px 20px; /* Increased padding */
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: center;
    background: #fff;
    position: relative;
}

.chat-header-avatar {
    width: 45px; /* Added explicit width */
    height: 45px; /* Added explicit height */
    min-width: 45px; 
    border-radius: 50%;
    background: #007bff;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px; /* Added for better icon sizing */
    font-weight: bold;
}

.chat-header-avatar.group {
    background: #28a745;
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


.header-action {
    margin-left: auto;
    padding: 8px 15px; /* Increased padding */
    border: 1px solid #ddd;
    background: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.2s;
}

.header-action:hover {
    background: #f1f1f1;
}


/* MESSAGES */

/* MESSAGES */
.messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #efeae2; /* WhatsApp web background color */
}

.empty-chat {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    text-align: center;
    height: 100%;
}

.message-row {
    display: flex;
    margin-bottom: 12px;
    align-items: flex-start; /* Aligns avatar and bubble to the top */
}

.message-row.mine {
    justify-content: flex-end;
}

/* New Avatar class for incoming messages */
.msg-avatar {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 50%;
    background: #607d8b;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: bold;
    margin-right: 8px;
    flex-shrink: 0;
}

.message-bubble {
    padding: 6px 9px 8px 9px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 1px 0.5px rgba(11,20,26,.13);
    max-width: 65%;
    position: relative; /* Required for the tail and timestamp */
    display: flex;
    flex-direction: column;
}

/* Draws the left-pointing tail */
.message-row:not(.mine) .message-bubble::before {
    content: "";
    position: absolute;
    top: 0;
    left: -8px; /* Pushes the tail outside the bubble */
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 8px 10px 0;
    border-color: transparent #ffffff transparent transparent;
}

/* WhatsApp-style bubble tails and colors */
.message-row:not(.mine) .message-bubble {
    border-top-left-radius: 0;
}

/* =======================================
   OUTGOING MESSAGES (Green bubble, right tail)
   ======================================= */
.message-row.mine .message-bubble {
    background: #d9fdd3;
    border-top-right-radius: 0; /* Flattens the corner for the tail */
}



.sender-name {
    font-size: 13px;
    font-weight: bold;
    color: #029d00; /* WhatsApp teal/green */
    margin-bottom: 2px;
}

.message-body {
    position: relative;
    display: inline-block;
    min-width: 90px; /* Forces enough width so the timestamp never overlaps short text */
}

.message-text {
    font-size: 14px;
    line-height: 19px;
    color: #111b21;
    white-space: pre-wrap;
    word-break: break-word;
    display: inline-block; /* Crucial: allows padding to work properly on the span */
    padding-right: 75px; /* Reserves physical space on the right for the time and ticks */
    padding-bottom: 2px;
}

.message-time {
    font-size: 11px;
    color: #667781;
    position: absolute;
    bottom: -2px;
    right: 0;
    white-space: nowrap;
}


.sender-name {

    font-size: 11px;

    font-weight: bold;

    color: #007bff;

    margin-bottom: 4px;
}


.message-row.mine .sender-name {

    color: rgba(255,255,255,.8);
}




/* SEND */

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


.send-box button:disabled {

    background: #aaa;

    cursor: not-allowed;
}


/* =========================================================
   MODAL
========================================================= */

.modal {

    display: none;

    position: fixed;

    z-index: 9999;

    left: 0;

    top: 0;

    width: 100%;

    height: 100%;

    background: rgba(0,0,0,.5);

    align-items: center;

    justify-content: center;
}


.modal-box {

    background: #fff;

    width: 450px;

    max-width: 95%;

    border-radius: 8px;

    padding: 20px;

    max-height: 90vh;

    overflow-y: auto;
}


.modal-title {

    font-size: 18px;

    font-weight: bold;

    margin-bottom: 15px;
}


.form-group {

    margin-bottom: 15px;
}


.form-group label {

    display: block;

    margin-bottom: 5px;

    font-size: 13px;

    font-weight: bold;
}


.form-group input,
.form-group textarea,
.form-group select {

    width: 100%;

    border: 1px solid #ddd;

    border-radius: 5px;

    padding: 10px;

    outline: none;
}


.modal-buttons {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 15px;
}


.btn {

    border: 0;

    border-radius: 5px;

    padding: 9px 15px;

    cursor: pointer;
}


.btn-primary {

    background: #007bff;

    color: #fff;
}


.btn-success {

    background: #28a745;

    color: #fff;
}


.btn-danger {

    background: #dc3545;

    color: #fff;
}


.btn-secondary {

    background: #6c757d;

    color: #fff;
}


.member-row {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 8px;

    border-bottom: 1px solid #eee;
}


.member-info {

    display: flex;

    align-items: center;

    gap: 8px;
}


/* MOBILE */

@media(max-width:700px) {

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

    .chat-tabs button {

        font-size: 10px;
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

    .create-group-btn {

        font-size: 10px;

        padding: 2px;
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
========================================================= -->

<div class="sidebar">


    <div class="sidebar-header">

        <div class="my-user">

           <a href="change_password.php" class="my-avatar-link" title="My Profile / Change Password">
    <div class="my-avatar">
        <?= htmlspecialchars(
            strtoupper(substr($current_username, 0, 1))
        ) ?>
    </div>
</a>
            <div>

                <div class="my-name">

                    <?= htmlspecialchars(
                        $current_username
                    ) ?>

                </div>

                <div class="my-role">

                    <?= htmlspecialchars(
                        $current_role
                    ) ?>

                </div>

            </div>

        </div>


        <a
            href="chat_logout.php"
            class="logout">
            Logout
        </a>

    </div>


    <div class="search-box">

        <input
            type="text"
            id="searchBox"
            placeholder="Search..."
        >

    </div>


    <div class="chat-tabs">

        <button
            type="button"
            class="chat-tab active"
            id="privateTab"
            onclick="showTab('private')">
            Users
        </button>

        <button
            type="button"
            class="chat-tab"
            id="groupTab"
            onclick="showTab('groups')">
            Groups
        </button>

    </div>


    <button
        type="button"
        class="create-group-btn"
        onclick="openCreateGroup()">

        + Create Group

    </button>


    <div
        class="user-list"
        id="userList">

        <div
            style="
                padding:30px;
                text-align:center;
                color:#999;">

            Loading...

        </div>

    </div>


</div>


<!-- =========================================================
     CHAT AREA
========================================================= -->

<div class="chat-area">


    <div
        class="chat-header"
        id="chatHeader">

        <div style="color:#999;">

            Select a user or group to start chatting

        </div>

    </div>


    <div
        class="messages"
        id="messages">

        <div class="empty-chat">

            <div>

                <div style="font-size:40px;">
                    💬
                </div>

                <div>
                    Select a user or group
                </div>

            </div>

        </div>

    </div>


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


<!-- =========================================================
     CREATE GROUP MODAL
========================================================= -->

<div
    class="modal"
    id="createGroupModal">

    <div class="modal-box">

        <div class="modal-title">

            Create Group

        </div>


        <form id="createGroupForm">

            <div class="form-group">

                <label>
                    Group Name
                </label>

                <input
                    type="text"
                    id="groupName"
                    required
                    placeholder="Example: All Teachers">

            </div>


            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    id="groupDescription"
                    rows="3"
                    placeholder="Group description"></textarea>

            </div>


            <div class="modal-buttons">

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="closeModals()">

                    Cancel

                </button>

                <button
                    type="submit"
                    class="btn btn-success">

                    Create Group

                </button>

            </div>

        </form>

    </div>

</div>


<!-- =========================================================
     GROUP MEMBERS MODAL
========================================================= -->

<div
    class="modal"
    id="membersModal">

    <div class="modal-box">

        <div class="modal-title">

            Group Members

        </div>


        <div id="membersList">

            Loading...

        </div>


        <hr>


        <div class="modal-title"
             style="font-size:15px;">

            Add Member

        </div>


        <select
            id="availableUserSelect">

            <option value="">
                Select user
            </option>

        </select>


        <div class="modal-buttons">

            <button
                type="button"
                class="btn btn-secondary"
                onclick="closeModals()">

                Close

            </button>

            <button
                type="button"
                class="btn btn-primary"
                onclick="addMember()">

                Add Member

            </button>

        </div>

    </div>

</div>


<script>


/* =========================================================
   VARIABLES
========================================================= */

let currentUserId =
    <?= $current_user_id ?>;


let selectedType = '';

let selectedId = 0;

let selectedName = '';

let currentGroupIsAdmin = false;


/* =========================================================
   DOM READY
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        loadUsers();

        setInterval(
            function () {

                loadUsers();

                if (
                    selectedType === 'private' &&
                    selectedId > 0
                ) {

                    loadPrivateMessages(false);
                }


                if (
                    selectedType === 'group' &&
                    selectedId > 0
                ) {

                    loadGroupMessages(false);
                }

            },
            3000
        );

    }
);


/* =========================================================
   ESCAPE HTML
========================================================= */

function escapeHtml(text) {

    if (
        text === null ||
        text === undefined
    ) {

        return '';
    }


    return String(text)

        .replace(
            /&/g,
            '&amp;'
        )

        .replace(
            /</g,
            '&lt;'
        )

        .replace(
            />/g,
            '&gt;'
        )

        .replace(
            /"/g,
            '&quot;'
        )

        .replace(
            /'/g,
            '&#039;'
        );
}


/* =========================================================
   TAB
========================================================= */

let activeTab = 'private';


function showTab(tab) {

    activeTab = tab;


    document
        .getElementById('privateTab')
        .classList.toggle(
            'active',
            tab === 'private'
        );


    document
        .getElementById('groupTab')
        .classList.toggle(
            'active',
            tab === 'groups'
        );


    loadUsers();
}


/* =========================================================
   LOAD USERS / GROUPS
========================================================= */

function loadUsers() {

    if (activeTab === 'private') {

        fetch(
            'chats_message.php?ajax=users'
        )

        .then(response =>
            response.json()
        )

        .then(data => {

            if (!data.success) {

                console.error(
                    data.message
                );

                return;
            }


            renderUsers(
                data.users
            );

        })

        .catch(error => {

            console.error(
                'Users error:',
                error
            );

        });

    } else {

        fetch(
            'chats_message.php?ajax=groups'
        )

        .then(response =>
            response.json()
        )

        .then(data => {

            if (!data.success) {

                console.error(
                    data.message
                );

                return;
            }


            renderGroups(
                data.groups
            );

        })

        .catch(error => {

            console.error(
                'Groups error:',
                error
            );

        });
    }
}


/* =========================================================
   RENDER USERS
========================================================= */

function renderUsers(users) {

    const list =
        document.getElementById(
            'userList'
        );


    const search =
        document
            .getElementById('searchBox')
            .value
            .toLowerCase()
            .trim();


    let html = '';

    let found = false;


    users.forEach(
        function (user) {

            if (
                search !== '' &&
                !user.username
                    .toLowerCase()
                    .includes(search) &&
                !user.role
                    .toLowerCase()
                    .includes(search)
            ) {

                return;
            }


            found = true;


            const unread =
                parseInt(
                    user.unread_count || 0
                );


            let last =
                user.last_message || '';


            if (last.length > 35) {

                last =
                    last.substring(
                        0,
                        35
                    ) + '...';
            }


            html += `

                <div
                    class="user-item ${
                        selectedType === 'private' &&
                        selectedId == user.id
                        ? 'active'
                        : ''
                    }"
                    data-id="${user.id}"
                    data-name="${escapeHtml(
                        user.username
                    )}"
                    data-role="${escapeHtml(
                        user.role
                    )}">

                    <div class="avatar">

                        ${escapeHtml(
                            user.username
                                .substring(0,1)
                                .toUpperCase()
                        )}

                    </div>


                    <div class="user-info">

                        <div class="user-name-row">

                            <div class="user-name">

                                ${escapeHtml(
                                    user.username
                                )}

                            </div>


                            ${
                                unread > 0
                                ?
                                `<div class="unread">
                                    ${unread}
                                </div>`
                                :
                                ''
                            }

                        </div>


                        <div class="user-role">

                            ${escapeHtml(
                                user.role
                            )}

                        </div>


                        <div class="last-message">

                            ${escapeHtml(last)}

                        </div>

                    </div>

                </div>

            `;
        }
    );


    if (!found) {

        html = `

            <div
                style="
                    padding:30px;
                    text-align:center;
                    color:#999;">

                No users found

            </div>

        `;
    }


    list.innerHTML = html;


    document
        .querySelectorAll('.user-item')
        .forEach(
            function (item) {

                item.onclick =
                    function () {

                        selectPrivateUser(
                            parseInt(
                                this.dataset.id
                            ),
                            this.dataset.name,
                            this.dataset.role
                        );

                    };

            }
        );
}


/* =========================================================
   RENDER GROUPS
========================================================= */

function renderGroups(groups) {

    const list =
        document.getElementById(
            'userList'
        );


    const search =
        document
            .getElementById('searchBox')
            .value
            .toLowerCase()
            .trim();


    let html = '';

    let found = false;


    groups.forEach(
        function (group) {

            if (
                search !== '' &&
                !group.group_name
                    .toLowerCase()
                    .includes(search)
            ) {

                return;
            }


            found = true;


            const unread =
                parseInt(
                    group.unread_count || 0
                );


            let last =
                group.last_message || '';


            if (last.length > 35) {

                last =
                    last.substring(
                        0,
                        35
                    ) + '...';
            }


            html += `

                <div
                    class="user-item ${
                        selectedType === 'group' &&
                        selectedId == group.id
                        ? 'active'
                        : ''
                    }"
                    data-id="${group.id}"
                    data-name="${escapeHtml(
                        group.group_name
                    )}">

                    <div class="avatar group-avatar">

                        👥

                    </div>


                    <div class="user-info">

                        <div class="user-name-row">

                            <div class="user-name">

                                ${escapeHtml(
                                    group.group_name
                                )}

                            </div>


                            ${
                                unread > 0
                                ?
                                `<div class="unread">
                                    ${unread}
                                </div>`
                                :
                                ''
                            }

                        </div>


                        <div class="user-role">

                            ${group.member_count}
                            members

                        </div>


                        <div class="last-message">

                            ${escapeHtml(last)}

                        </div>

                    </div>

                </div>

            `;
        }
    );


    if (!found) {

        html = `

            <div
                style="
                    padding:30px;
                    text-align:center;
                    color:#999;">

                No groups found

            </div>

        `;
    }


    list.innerHTML = html;


    document
        .querySelectorAll('.user-item')
        .forEach(
            function (item) {

                item.onclick =
                    function () {

                        selectGroup(
                            parseInt(
                                this.dataset.id
                            ),
                            this.dataset.name
                        );

                    };

            }
        );
}


/* =========================================================
   PRIVATE USER
========================================================= */

function selectPrivateUser(
    userId,
    username,
    role
) {

    selectedType = 'private';

    selectedId =
        parseInt(userId);

    selectedName =
        username;

    currentGroupIsAdmin = false;


    document
        .getElementById('chatHeader')
        .innerHTML = `

            <div class="chat-header-avatar">

                ${escapeHtml(
                    username
                        .substring(0,1)
                        .toUpperCase()
                )}

            </div>


            <div class="chat-header-info">

                <div class="chat-header-name">

                    ${escapeHtml(
                        username
                    )}

                </div>


                <div class="chat-header-role">

                    ${escapeHtml(
                        role
                    )}

                </div>

            </div>

        `;


    enableMessageBox();


    loadPrivateMessages(true);
}


/* =========================================================
   GROUP SELECT
========================================================= */

function selectGroup(
    groupId,
    groupName
) {

    selectedType = 'group';

    selectedId =
        parseInt(groupId);

    selectedName =
        groupName;


    currentGroupIsAdmin = false;


    document
        .getElementById('chatHeader')
        .innerHTML = `

            <div class="chat-header-avatar group">

                👥

            </div>


            <div class="chat-header-info">

                <div class="chat-header-name">

                    ${escapeHtml(
                        groupName
                    )}

                </div>


                <div
                    class="chat-header-role"
                    id="groupHeaderRole">

                    Group

                </div>

            </div>


            <button
                type="button"
                class="header-action"
                onclick="openMembers()">

                Members

            </button>

        `;


    enableMessageBox();


    loadGroupMessages(true);
}


/* =========================================================
   ENABLE SEND
========================================================= */

function enableMessageBox() {

    document
        .getElementById(
            'messageInput'
        )
        .disabled = false;


    document
        .getElementById(
            'sendButton'
        )
        .disabled = false;
}


/* =========================================================
   PRIVATE MESSAGES
========================================================= */

function loadPrivateMessages(
    scrollBottom
) {

    if (
        selectedType !== 'private' ||
        selectedId <= 0
    ) {

        return;
    }


    fetch(
        'chats_message.php?ajax=messages&user_id=' +
        encodeURIComponent(selectedId)
    )

    .then(response =>
        response.json()
    )

    .then(data => {

        if (!data.success) {

            console.error(
                data.message
            );

            return;
        }


        renderPrivateMessages(
            data.messages,
            scrollBottom
        );

    })

    .catch(error => {

        console.error(
            'Private messages error:',
            error
        );

    });
}


/* =========================================================
   RENDER PRIVATE
========================================================= */

function renderPrivateMessages(
    messages,
    scrollBottom
) {

    const container =
        document.getElementById(
            'messages'
        );


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

                    <div
                        style="
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


    messages.forEach(
        function (msg) {

            const mine =
                parseInt(
                    msg.sender_id
                ) === currentUserId;


            const date =
                new Date(
                    msg.created_at
                        .replace(
                            ' ',
                            'T'
                        )
                );


            let time =
                msg.created_at;


            if (
                !isNaN(
                    date.getTime()
                )
            ) {

                time =
                    date.toLocaleTimeString(
                        [],
                        {
                            hour: '2-digit',
                            minute: '2-digit'
                        }
                    );
            }


           html += `
    <div class="message-row ${mine ? 'mine' : ''}">
        ${!mine ? `
        <div class="msg-avatar">
            ${escapeHtml(msg.sender_username.substring(0,1).toUpperCase())}
        </div>
        ` : ''}

        <div class="message-bubble">
            ${!mine ? `<div class="sender-name">${escapeHtml(msg.sender_username)}</div>` : ''}
            <div class="message-body">
                <span class="message-text">${escapeHtml(msg.message)}</span>
                <span class="message-time">
                    ${escapeHtml(time)}
                    ${mine ? `
                    <span class="message-tick">
                        <svg viewBox="0 0 16 15" width="16" height="15">
                            <path fill="#667781" d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
                        </svg>
                    </span>` : ''}
                </span>
            </div>
        </div>
    </div>
`;
        }
    );


    container.innerHTML = html;


    if (scrollBottom) {

        container.scrollTop =
            container.scrollHeight;
    }
}


/* =========================================================
   GROUP MESSAGES
========================================================= */

function loadGroupMessages(
    scrollBottom
) {

    if (
        selectedType !== 'group' ||
        selectedId <= 0
    ) {

        return;
    }


    fetch(
        'chats_message.php?ajax=group_messages&group_id=' +
        encodeURIComponent(selectedId)
    )

    .then(response =>
        response.json()
    )

    .then(data => {

        if (!data.success) {

            console.error(
                data.message
            );

            return;
        }


        currentGroupIsAdmin =
            data.group.member_role === 'admin';


        document
            .getElementById(
                'groupHeaderRole'
            )
            .textContent =
                data.group.member_role === 'admin'
                ? 'Group Admin'
                : 'Group';


        renderGroupMessages(
            data.messages,
            scrollBottom
        );

    })

    .catch(error => {

        console.error(
            'Group messages error:',
            error
        );

    });
}


/* =========================================================
   RENDER GROUP
========================================================= */

function renderGroupMessages(
    messages,
    scrollBottom
) {

    const container =
        document.getElementById(
            'messages'
        );


    if (messages.length === 0) {

        container.innerHTML = `

            <div class="empty-chat">

                <div>

                    <div style="font-size:40px;">
                        👥
                    </div>

                    <div>
                        No group messages yet
                    </div>

                    <div
                        style="
                            font-size:12px;
                            margin-top:5px;">

                        Start the group conversation

                    </div>

                </div>

            </div>

        `;

        return;
    }


    let html = '';


    messages.forEach(
        function (msg) {

            const mine =
                parseInt(
                    msg.sender_id
                ) === currentUserId;


            const date =
                new Date(
                    msg.created_at
                        .replace(
                            ' ',
                            'T'
                        )
                );


            let time =
                msg.created_at;


            if (
                !isNaN(
                    date.getTime()
                )
            ) {

                time =
                    date.toLocaleTimeString(
                        [],
                        {
                            hour: '2-digit',
                            minute: '2-digit'
                        }
                    );
            }

html += `
    <div class="message-row ${mine ? 'mine' : ''}">
        <div class="message-bubble">
            <div class="message-body">
                <span class="message-text">${escapeHtml(msg.message)}</span>
                <span class="message-time">
                    ${escapeHtml(time)}
                    ${mine ? `
                    <span class="message-tick">
                        <svg viewBox="0 0 16 15" width="16" height="15">
                            <path fill="#667781" d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
                        </svg>
                    </span>` : ''}
                </span>
            </div>
        </div>
    </div>
`;
        }
    );


    container.innerHTML = html;


    if (scrollBottom) {

        container.scrollTop =
            container.scrollHeight;
    }
}


/* =========================================================
   SEND
========================================================= */

document
    .getElementById(
        'sendForm'
    )
    .addEventListener(
        'submit',
        function (e) {

            e.preventDefault();


            if (
                selectedId <= 0 ||
                selectedType === ''
            ) {

                alert(
                    'Select a user or group first.'
                );

                return;
            }


            const input =
                document.getElementById(
                    'messageInput'
                );


            const button =
                document.getElementById(
                    'sendButton'
                );


            const message =
                input.value.trim();


            if (message === '') {

                return;
            }


            button.disabled = true;


            const formData =
                new FormData();


            formData.append(
                'message',
                message
            );


            let url = '';


            if (
                selectedType === 'private'
            ) {

                url =
                    'chats_message.php?ajax=send';


                formData.append(
                    'receiver_id',
                    selectedId
                );

            } else {

                url =
                    'chats_message.php?ajax=send_group';


                formData.append(
                    'group_id',
                    selectedId
                );
            }


            fetch(
                url,
                {
                    method: 'POST',
                    body: formData
                }
            )

            .then(async response => {

                const text =
                    await response.text();


                console.log(
                    'SEND RESPONSE:',
                    text
                );


                try {

                    return JSON.parse(
                        text
                    );

                } catch (e) {

                    throw new Error(
                        text
                    );
                }

            })

            .then(data => {

                if (!data.success) {

                    alert(
                        data.message ||
                        'Message failed.'
                    );

                    return;
                }


                input.value = '';


                if (
                    selectedType ===
                    'private'
                ) {

                    loadPrivateMessages(
                        true
                    );

                } else {

                    loadGroupMessages(
                        true
                    );
                }


                loadUsers();

            })

            .catch(error => {

                console.error(
                    'SEND ERROR:',
                    error
                );


                alert(
                    'Send error: ' +
                    error.message
                );

            })

            .finally(
                function () {

                    button.disabled =
                        false;

                    input.focus();

                }
            );

        }
    );


/* =========================================================
   ENTER SEND
========================================================= */

document
    .getElementById(
        'messageInput'
    )
    .addEventListener(
        'keydown',
        function (e) {

            if (
                e.key === 'Enter' &&
                !e.shiftKey
            ) {

                e.preventDefault();


                document
                    .getElementById(
                        'sendForm'
                    )
                    .requestSubmit();
            }

        }
    );


/* =========================================================
   SEARCH
========================================================= */

document
    .getElementById(
        'searchBox'
    )
    .addEventListener(
        'input',
        function () {

            loadUsers();

        }
    );


/* =========================================================
   CREATE GROUP
========================================================= */

function openCreateGroup() {

    document
        .getElementById(
            'createGroupModal'
        )
        .style.display = 'flex';


    document
        .getElementById(
            'groupName'
        )
        .focus();
}


document
    .getElementById(
        'createGroupForm'
    )
    .addEventListener(
        'submit',
        function (e) {

            e.preventDefault();


            const groupName =
                document
                    .getElementById(
                        'groupName'
                    )
                    .value
                    .trim();


            const description =
                document
                    .getElementById(
                        'groupDescription'
                    )
                    .value
                    .trim();


            if (!groupName) {

                alert(
                    'Enter group name.'
                );

                return;
            }


            const formData =
                new FormData();


            formData.append(
                'group_name',
                groupName
            );


            formData.append(
                'description',
                description
            );


            fetch(
                'chats_message.php?ajax=create_group',
                {
                    method: 'POST',
                    body: formData
                }
            )

            .then(response =>
                response.json()
            )

            .then(data => {

                if (!data.success) {

                    alert(
                        data.message
                    );

                    return;
                }


                alert(
                    'Group created successfully.'
                );


                document
                    .getElementById(
                        'groupName'
                    )
                    .value = '';


                document
                    .getElementById(
                        'groupDescription'
                    )
                    .value = '';


                closeModals();


                showTab('groups');

            })

            .catch(error => {

                console.error(error);

                alert(
                    'Unable to create group.'
                );

            });

        }
    );


/* =========================================================
   MEMBERS
========================================================= */

function openMembers() {

    if (
        selectedType !== 'group' ||
        selectedId <= 0
    ) {

        return;
    }


    document
        .getElementById(
            'membersModal'
        )
        .style.display = 'flex';


    loadMembers();

    loadAvailableUsers();
}


/* =========================================================
   LOAD MEMBERS
========================================================= */

function loadMembers() {

    fetch(
        'chats_message.php?ajax=group_members&group_id=' +
        encodeURIComponent(selectedId)
    )

    .then(response =>
        response.json()
    )

    .then(data => {

        if (!data.success) {

            alert(
                data.message
            );

            return;
        }


        let html = '';


        data.members.forEach(
            function (member) {

                const isMe =
                    parseInt(
                        member.user_id
                    ) === currentUserId;


                html += `

                    <div class="member-row">

                        <div class="member-info">

                            <div class="avatar"
                                 style="
                                    width:35px;
                                    height:35px;
                                    min-width:35px;
                                 ">

                                ${escapeHtml(
                                    member.username
                                        .substring(0,1)
                                        .toUpperCase()
                                )}

                            </div>


                            <div>

                                <strong>

                                    ${escapeHtml(
                                        member.username
                                    )}

                                </strong>

                                <div
                                    style="
                                        font-size:11px;
                                        color:#888;">

                                    ${escapeHtml(
                                        member.role
                                    )}

                                    -
                                    ${escapeHtml(
                                        member.member_role
                                    )}

                                </div>

                            </div>

                        </div>


                        ${
                            currentGroupIsAdmin &&
                            !isMe &&
                            member.member_role !== 'admin'
                            ?
                            `<button
                                type="button"
                                class="btn btn-danger"
                                onclick="removeMember(${member.user_id})">

                                Remove

                            </button>`
                            :
                            ''
                        }

                    </div>

                `;
            }
        );


        document
            .getElementById(
                'membersList'
            )
            .innerHTML =
                html ||
                '<div>No members.</div>';

    })

    .catch(error => {

        console.error(error);

    });
}


/* =========================================================
   AVAILABLE USERS
========================================================= */

function loadAvailableUsers() {

    fetch(
        'chats_message.php?ajax=available_users&group_id=' +
        encodeURIComponent(selectedId)
    )

    .then(response =>
        response.json()
    )

    .then(data => {

        if (!data.success) {

            return;
        }


        const select =
            document.getElementById(
                'availableUserSelect'
            );


        let html =
            '<option value="">Select user</option>';


        data.users.forEach(
            function (user) {

                html += `

                    <option value="${user.id}">

                        ${escapeHtml(
                            user.username
                        )}
                        -
                        ${escapeHtml(
                            user.role
                        )}

                    </option>

                `;
            }
        );


        select.innerHTML = html;


        select.disabled =
            !currentGroupIsAdmin;

    })

    .catch(error => {

        console.error(error);

    });
}


/* =========================================================
   ADD MEMBER
========================================================= */

function addMember() {

    if (!currentGroupIsAdmin) {

        alert(
            'Only group admin can add members.'
        );

        return;
    }


    const userId =
        parseInt(
            document
                .getElementById(
                    'availableUserSelect'
                )
                .value
        );


    if (!userId) {

        alert(
            'Select a user.'
        );

        return;
    }


    const formData =
        new FormData();


    formData.append(
        'group_id',
        selectedId
    );


    formData.append(
        'user_id',
        userId
    );


    fetch(
        'chats_message.php?ajax=add_member',
        {
            method: 'POST',
            body: formData
        }
    )

    .then(response =>
        response.json()
    )

    .then(data => {

        if (!data.success) {

            alert(
                data.message
            );

            return;
        }


        loadMembers();

        loadAvailableUsers();

        loadUsers();

    })

    .catch(error => {

        console.error(error);

        alert(
            'Unable to add member.'
        );

    });
}


/* =========================================================
   REMOVE MEMBER
========================================================= */

function removeMember(userId) {

    if (
        !confirm(
            'Remove this member from group?'
        )
    ) {

        return;
    }


    const formData =
        new FormData();


    formData.append(
        'group_id',
        selectedId
    );


    formData.append(
        'user_id',
        userId
    );


    fetch(
        'chats_message.php?ajax=remove_member',
        {
            method: 'POST',
            body: formData
        }
    )

    .then(response =>
        response.json()
    )

    .then(data => {

        if (!data.success) {

            alert(
                data.message
            );

            return;
        }


        loadMembers();

        loadAvailableUsers();

        loadUsers();

    })

    .catch(error => {

        console.error(error);

    });
}


/* =========================================================
   CLOSE MODALS
========================================================= */

function closeModals() {

    document
        .querySelectorAll('.modal')
        .forEach(
            function (modal) {

                modal.style.display =
                    'none';

            }
        );
}


window.addEventListener(
    'click',
    function (e) {

        document
            .querySelectorAll('.modal')
            .forEach(
                function (modal) {

                    if (
                        e.target === modal
                    ) {

                        modal.style.display =
                            'none';
                    }

                }
            );

    }
);

</script>

</body>

</html>