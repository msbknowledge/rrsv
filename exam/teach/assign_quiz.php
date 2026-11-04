<?php
include('dbConnection.php');

$quiz_id = $_POST['quiz_id'];
$students = $_POST['students'];
$class = $_POST['class'];
$session = $_POST['session'];
$unit = $_POST['unit'];

// Delete old
mysqli_query($con,"DELETE FROM quiz_assignments WHERE quiz_id='$quiz_id'");

// Insert new
foreach($students as $student_id){

    mysqli_query($con,"
        INSERT INTO quiz_assignments 
        (quiz_id, student_id, class, session, unit)
        VALUES 
        ('$quiz_id','$student_id','$class','$session','$unit')
    ");
}

echo "✅ Assignment Updated Successfully!";
?>