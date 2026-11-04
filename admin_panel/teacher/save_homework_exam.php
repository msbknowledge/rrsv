<?php
include '../../include/config.php';
include '../dbcon.php';

// Collect and sanitize input
$title         = mysqli_real_escape_string($con, $_POST['title'] ?? '');
$class_id      = mysqli_real_escape_string($con, $_POST['class_id'] ?? '');
$subject_id    = mysqli_real_escape_string($con, $_POST['subject_id'] ?? '');
$teacher_id    = mysqli_real_escape_string($con, $_POST['teacher_id'] ?? '');
$session       = mysqli_real_escape_string($con, $_POST['session'] ?? '');
$class_date    = mysqli_real_escape_string($con, $_POST['class_date'] ?? '');
$unit          = mysqli_real_escape_string($con, $_POST['unit'] ?? '');
$chapter_name  = mysqli_real_escape_string($con, $_POST['chapter_name'] ?? '');
$book_name     = mysqli_real_escape_string($con, $_POST['book_name'] ?? '');
$description   = mysqli_real_escape_string($con, $_POST['description'] ?? '');
$type          = mysqli_real_escape_string($con, $_POST['type'] ?? '');

 $file_name = "";

    // ✅ File Upload
    if(isset($_FILES['question_file']) && $_FILES['question_file']['error'] == 0){

        $upload_dir = "uploads/homework/";
        
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp  = $_FILES['question_file']['tmp_name'];
        $file_ext  = strtolower(pathinfo($_FILES['question_file']['name'], PATHINFO_EXTENSION));

        // allowed file types
        $allowed = ['pdf','doc','docx','jpg','jpeg','png'];

        if(in_array($file_ext, $allowed)){

            $file_name = time() . "_" . rand(1000,9999) . "." . $file_ext;
            move_uploaded_file($file_tmp, $upload_dir . $file_name);

        } else {
            echo "Invalid file type!";
            exit;
        }
    }

// ✅ Fixed SQL syntax
$sql = "
INSERT INTO rrsv_homework_exam 
(title, class_id, subject_id, teacher_id, session, unit, class_date, book_name, chapter_name, description, type,question_file)
VALUES 
('$title', '$class_id', '$subject_id', '$teacher_id', '$session', '$unit', '$class_date', '$book_name', '$chapter_name', '$description', '$type','$file_name')
";

// ✅ Run query
if (mysqli_query($con, $sql)) {
    header("Location: homework_exam.php?msg=added");
    exit;
} else {
    echo "Error: " . mysqli_error($con);
}
?>
