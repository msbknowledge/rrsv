<?php
//print_r($_POST);die();
include '../../include/config.php';
include '../dbcon.php';

// Collect inputs safely
$exam_id         = mysqli_real_escape_string($con, $_POST['exam_id']);
$question        = mysqli_real_escape_string($con, $_POST['question']);
$question_type   = mysqli_real_escape_string($con, $_POST['question_type']); // added
$correct_answer  = $_POST['correct_answer'] ?? null;
$teacher_answer  = $_POST['teacher_answer'] ?? null;
$marks           = $_POST['marks'] ?? null;

// Initialize option values
$option_a = $option_b = $option_c = $option_d = null;

// Handle MCQ or IMAGE-based inputs
if ($question_type === 'mcq' || $question_type === 'image') {
    // If image type, handle file uploads
    if ($question_type === 'image' && isset($_FILES['option_a'])) {
        $upload_dir = "../../uploads/questions/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        function uploadOptionImage($field, $upload_dir) {
            if (!empty($_FILES[$field]['name'])) {
                $filename = time() . "_" . basename($_FILES[$field]['name']);
                $target = $upload_dir . $filename;
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
                    return $filename;
                }
            }
            return null;
        }

        $option_a = uploadOptionImage('option_a', $upload_dir);
        $option_b = uploadOptionImage('option_b', $upload_dir);
        $option_c = uploadOptionImage('option_c', $upload_dir);
        $option_d = uploadOptionImage('option_d', $upload_dir);
    } else {
        // Regular text options
        $option_a = mysqli_real_escape_string($con, $_POST['option_a'] ?? '');
        $option_b = mysqli_real_escape_string($con, $_POST['option_b'] ?? '');
        $option_c = mysqli_real_escape_string($con, $_POST['option_c'] ?? '');
        $option_d = mysqli_real_escape_string($con, $_POST['option_d'] ?? '');
    }
}


// Insert question
$query = "INSERT INTO rrsv_homework_exam_questions 
          (exam_id, question, question_type, option_a, option_b, option_c, option_d, correct_answer, marks) 
          VALUES 
          ('$exam_id', '$question', '$question_type', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer', '$marks')";

if ($con->query($query)) {
    $qid = $con->insert_id;

    // Insert teacher descriptive answer if provided
    if (!empty($teacher_answer)) {
        $teacher_answer = mysqli_real_escape_string($con, $teacher_answer);
        $con->query("INSERT INTO rrsv_teacher_answers (question_id, answer) VALUES ('$qid', '$teacher_answer')");
    }

    header("Location: homework_exam_questions.php?exam_id=$exam_id&success=1");
    exit;
} else {
    echo "Error: " . $con->error;
}
?>
