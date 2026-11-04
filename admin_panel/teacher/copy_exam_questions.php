<?php

session_start();
include '../dbcon.php';

if (!isset($_POST['question_ids']) || empty($_POST['question_ids'])) {
    exit('No question selected.');
}

$exam_id = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;

if ($exam_id <= 0) {
    exit('Invalid Exam ID.');
}

$success = 0;
$failed = 0;

foreach ($_POST['question_ids'] as $qid) {

    $qid = intval($qid);

    $sql = mysqli_query($con, "
        SELECT *
        FROM rrsv_homework_exam_questions
        WHERE id = '$qid'
    ");

    if (!$sql) {
        die("Select Error: " . mysqli_error($con));
    }

    if ($row = mysqli_fetch_assoc($sql)) {

        $question        = mysqli_real_escape_string($con, $row['question']);
        $question_head   = mysqli_real_escape_string($con, $row['question_head']);
        $question_type   = mysqli_real_escape_string($con, $row['question_type']);
        $option_a        = mysqli_real_escape_string($con, $row['option_a']);
        $option_b        = mysqli_real_escape_string($con, $row['option_b']);
        $option_c        = mysqli_real_escape_string($con, $row['option_c']);
        $option_d        = mysqli_real_escape_string($con, $row['option_d']);
        $video_path      = mysqli_real_escape_string($con, $row['video_path']);
        $marks           = (int)$row['marks'];
        $correct_answer  = mysqli_real_escape_string($con, $row['correct_answer']);

        $insert = mysqli_query($con, "
            INSERT INTO rrsv_homework_exam_questions
            (
                exam_id,
                question,
                question_head,
                question_type,
                option_a,
                option_b,
                option_c,
                option_d,
                video_path,
                marks,
                correct_answer
            )
            VALUES
            (
                '$exam_id',
                '$question',
                '$question_head',
                '$question_type',
                '$option_a',
                '$option_b',
                '$option_c',
                '$option_d',
                '$video_path',
                '$marks',
                '$correct_answer'
            )
        ");

        if ($insert) {
            $success++;
        } else {
            $failed++;
            echo "Insert Error for Question ID {$qid}: " . mysqli_error($con) . "<br>";
        }
    }
}

echo "<br>";
echo "Questions copied successfully.<br>";
// echo "Success: " . $success . "<br>";
// echo "Failed: " . $failed;
?>