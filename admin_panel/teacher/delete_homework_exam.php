<?php
include '../dbcon.php'; // DB connection

if (!isset($_GET['id'])) {
    die("No exam ID provided.");
}

$exam_id = (int)$_GET['id']; // sanitize input
// Check if there are student submissions
$check = mysqli_query($con, "SELECT COUNT(*) as total FROM rrsv_student_homework_summary WHERE exam_id = $exam_id");
$row = mysqli_fetch_assoc($check);

// Cannot delete because students have submitted
// if ($row['total'] > 0) {
//     header("Location: homework_exam.php?error=CannotDeleteStudentSubmissions");
//     exit;
// }


// Start transaction
mysqli_begin_transaction($con);

try {
    // 1. Get all question IDs for this exam
    $question_ids = [];
    $res = mysqli_query($con, "SELECT id FROM rrsv_homework_exam_questions WHERE exam_id = $exam_id");
    while ($row = mysqli_fetch_assoc($res)) {
        $question_ids[] = $row['id'];
    }

    if (!empty($question_ids)) {
        $ids_str = implode(',', $question_ids);

        // 2. Delete student answers linked to questions
        mysqli_query($con, "DELETE FROM rrsv_student_answers WHERE question_id IN ($ids_str)");
 mysqli_query($con, "DELETE FROM rrsv_teacher_answers WHERE question_id IN ($ids_str)");
       
    }
 // 3. Delete exam questions
        mysqli_query($con, "DELETE FROM rrsv_homework_exam_questions WHERE exam_id = $exam_id");
    // 4. Delete student homework summary
    mysqli_query($con, "DELETE FROM rrsv_student_homework_summary WHERE exam_id = $exam_id");

    // 5. Delete the exam itself
    mysqli_query($con, "DELETE FROM rrsv_homework_exam WHERE id = $exam_id");

    // Commit transaction
    mysqli_commit($con);

    header("Location: homework_exam.php?success=ExamDeleted");
    exit;

} catch (Exception $e) {
    mysqli_rollback($con);
    header("Location: homework_exam.php?error=DeleteFailed");
    exit;
}
?>
