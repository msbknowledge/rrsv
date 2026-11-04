<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../dbcon.php';
include '../../include/config.php';

// 🔒 Allow only logged-in teachers
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

if ($id <= 0 || $exam_id <= 0) {
    header("Location: homework_exam_questions.php?exam_id=$exam_id&error=Invalid Request");
    exit;
}

$q = mysqli_query($con, "SELECT * FROM rrsv_homework_exam_questions WHERE id='$id'");
$question = mysqli_fetch_assoc($q);

if ($question) {

    // 🧹 Delete image options
    if ($question['question_type'] === 'image') {
        $paths = [
            "../../uploads/questions/" . $question['option_a'],
            "../../uploads/questions/" . $question['option_b'],
            "../../uploads/questions/" . $question['option_c'],
            "../../uploads/questions/" . $question['option_d'],
        ];

        foreach ($paths as $file) {
            if (!empty($file) && file_exists($file)) {
                unlink($file);
            }
        }
    }

    // 🧹 Delete uploaded video (ignore YouTube)
    if (!empty($question['video_path']) && strpos($question['video_path'], 'youtube') === false) {
        $videoPath = "../../uploads/videos/" . $question['video_path'];
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }
    }

    // ❌ Delete teacher answer
    mysqli_query($con, "DELETE FROM rrsv_teacher_answers WHERE question_id='$id'");

    // ❌ Delete question
    mysqli_query($con, "DELETE FROM rrsv_homework_exam_questions WHERE id='$id'");

    header("Location: homework_exam_questions.php?exam_id=$exam_id&success=Question deleted");
    exit;
}

header("Location: homework_exam_questions.php?exam_id=$exam_id&error=Question not found");
exit;
