<?php
include '../../include/config.php';
include '../dbcon.php';
$exam_id = $_POST['exam_id'];
$question = $_POST['question'];
$option_a = $_POST['option_a'] ?? null;
$option_b = $_POST['option_b'] ?? null;
$option_c = $_POST['option_c'] ?? null;
$option_d = $_POST['option_d'] ?? null;
$correct_answer = $_POST['correct_answer'] ?? null;
$teacher_answer = $_POST['teacher_answer'] ?? null;
$marks = $_POST['$marks'] ?? null;

// Insert question
$con->query("INSERT INTO rrsv_homework_exam_questions (exam_id, question, option_a, option_b, option_c, option_d, correct_answer,marks)
VALUES ('$exam_id', '$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer','$marks')");

$qid = $con->insert_id;

// If teacher model answer exists, insert it
if (!empty($teacher_answer)) {
    $con->query("INSERT INTO rrsv_teacher_answers (question_id, answer) VALUES ('$qid', '$teacher_answer')");
}

header("Location: homework_exam_questions.php?exam_id=$exam_id");
?>
