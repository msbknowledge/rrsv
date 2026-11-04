<?php
session_start();
include '../dbcon.php';

header('Content-Type: application/json'); // ensures proper JSON header

if (isset($_POST['scl_session']) && isset($_POST['class'])) {
    $scl_session = mysqli_real_escape_string($con, $_POST['scl_session']);
    $class = mysqli_real_escape_string($con, $_POST['class']);

    $sql = "SELECT id, scl_name 
            FROM rrsv_student_registration 
            WHERE scl_session = '$scl_session' 
              AND scl_class = '$class'";

    $result = mysqli_query($con, $sql);
    $search_arr = [];

    if (mysqli_num_rows($result) > 0) {
        while ($fetch = mysqli_fetch_assoc($result)) {
            $search_arr[] = [
                "id" => $fetch['id'],
                "name" => $fetch['scl_name']
            ];
        }
    } else {
        $search_arr[] = ["id" => 0, "name" => "No records found"];
    }

    echo json_encode($search_arr);
} else {
    echo json_encode([["id" => 0, "name" => "Invalid request"]]);
}
?>
