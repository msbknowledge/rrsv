<?php
// Include your database connection file
include('include/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $session = $_POST['session'] ?? '';
    $class = $_POST['class'] ?? '';

    // Sanitize inputs
    $session = mysqli_real_escape_string($myDB, $session);
    $class = mysqli_real_escape_string($myDB, $class);

    // Query to fetch data based on session and class
    $query = "SELECT id, name FROM rrsv_inquery WHERE scl_session = '$session' AND class_name = '$class' AND status = 'Panding'";
    $result = mysqli_query($myDB, $query);

    $response = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response[] = $row;
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>