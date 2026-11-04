<?php

include('dbConnection.php');

$class = isset($_POST['class'])
    ? mysqli_real_escape_string($con, $_POST['class'])
    : '';

$unit = isset($_POST['unit'])
    ? (int)$_POST['unit']
    : 0;


if ($class == '' || $unit == 0) {

    echo '<option value="">-- No Quiz Found --</option>';

    exit;
}


$sql = "SELECT id, title, total, time, status
        FROM quiz
        WHERE class = '$class'
        AND unit = '$unit'
        
        ORDER BY id DESC";


$result = mysqli_query($con, $sql);


if (mysqli_num_rows($result) > 0) {

    echo '<option value="">-- Select Quiz --</option>';

    while ($row = mysqli_fetch_assoc($result)) {

        echo '<option value="' . (int)$row['id'] . '">'
            . htmlspecialchars($row['title'])
            . ' | Marks: ' . (int)$row['total']
            . ' | Time: ' . (int)$row['time'] . ' min'
            . '</option>';
    }

} else {

    echo '<option value="">-- No Quiz Found --</option>';
}