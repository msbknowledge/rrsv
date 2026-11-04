<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'student') {
  header("Location: ../index.php?error=Unauthorized Access");
  exit;
}

$teacher = $_SESSION['user_data'];

$student_id = $_SESSION['user_data']['id'];
$exam_id = $_POST['exam_id'];
$answers = $_POST['answers'] ?? [];

$total_questions = count($answers);
$correct_answers = 0;

foreach ($answers as $qid => $ans) {
  // Save student answer
  $con->query("INSERT INTO rrsv_student_answers (student_id, question_id, answer)
                  VALUES ('$student_id', '$qid', '$ans')");

  // Fetch correct answer
  $getCorrect = $con->query("SELECT correct_answer FROM rrsv_homework_exam_questions WHERE id='$qid'")->fetch_assoc();
  $correct = strtoupper(trim($getCorrect['correct_answer'] ?? ''));

  if (!empty($correct) && strtoupper(trim($ans)) == $correct) {
    $correct_answers++;
  }
}

// Calculate score percentage
$score = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0;
$check = $con->query("
    SELECT id 
    FROM rrsv_student_homework_summary 
    WHERE student_id='$student_id' 
      AND exam_id='$exam_id'
");

if ($check->num_rows > 0) {
    header("Location: homework_exam_list.php?error=already_submitted");
    exit;
}
// Insert summary
$con->query("INSERT INTO rrsv_student_homework_summary (student_id, exam_id, total_questions, correct_answers, score)
              VALUES ('$student_id', '$exam_id', '$total_questions', '$correct_answers', '$score')");

header("Location: homework_exam_list.php?success=1");
exit;
?>