<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
session_start();
require 'dbConnection.php';

if (!isset($_GET['id']) || !isset($_GET['username'])) {
    die("Invalid Request");
}

$eid ='69bd4d98a4ee8'; //mysqli_real_escape_string($con, $_GET['id']);
$username = mysqli_real_escape_string($con, $_GET['username']);

$output = "";

/* ===============================
   GET QUIZ DETAILS
================================ */
$quizQuery = mysqli_query($con, "SELECT total FROM quiz WHERE eid='$eid'");
$quizData = mysqli_fetch_assoc($quizQuery);

if (!$quizData) {
    die("Quiz not found");
}

$total = $quizData['total'];

/* ===============================
   GET USER RESULT
================================ */
$historyQuery = mysqli_query($con, 
    "SELECT score, wrong, correct, status 
     FROM history 
     WHERE eid='$eid' AND username='$username'"
);

$historyData = mysqli_fetch_assoc($historyQuery);

if (!$historyData || $historyData['status'] != "finished") {
    die("404 - Result not available");
}

$score   = $historyData['score'];
$wrong   = $historyData['wrong'];
$correct = $historyData['correct'];

/* ===============================
   GET OVERALL RANK SCORE
================================ */
$rankQuery = mysqli_query($con, 
    "SELECT score FROM rank WHERE username='$username' ORDER BY score DESC LIMIT 1"
);

$rankData = mysqli_fetch_assoc($rankQuery);
$overallScore = $rankData ? $rankData['score'] : 0;

/* ===============================
   HEADER OUTPUT
================================ */
$output .= '
<div class="panel">
<center>
<h2 style="color:#669933;">' . htmlspecialchars($username) . '</h2>
</center>

<table border="1" width="100%" cellpadding="8" cellspacing="0">
<tr>
<td><b>Total Questions:</b></td><td>' . $total . '</td>
<td><b>Correct:</b></td><td>' . $correct . '</td>
</tr>

<tr>
<td><b>Wrong:</b></td><td>' . $wrong . '</td>
<td><b>Unattempted:</b></td><td>' . ($total - $correct - $wrong) . '</td>
</tr>

<tr>
<td><b>Score:</b></td><td>' . $score . '</td>
<td><b>Overall Score:</b></td><td>' . $overallScore . '</td>
</tr>
</table>
</div>

<div class="panel">
<h3>Detailed Analysis</h3>
<ol>
';

/* ===============================
   GET QUESTIONS
================================ */
$questionQuery = mysqli_query($con, 
    "SELECT qid, qns FROM questions WHERE eid='$eid' ORDER BY sn ASC"
);

while ($question = mysqli_fetch_assoc($questionQuery)) {

    $qid = $question['qid'];
    $questionText = $question['qns'];

    /* USER ANSWER */
    $userAnsQuery = mysqli_query($con,
        "SELECT ans, correctans 
         FROM user_answer 
         WHERE eid='$eid' AND qid='$qid' AND username='$username'"
    );

    if (mysqli_num_rows($userAnsQuery) > 0) {

        $userAnsData = mysqli_fetch_assoc($userAnsQuery);
        $ansid = $userAnsData['ans'];
        $correctansid = $userAnsData['correctans'];

    } else {

        $correctQuery = mysqli_query($con,
            "SELECT ansid FROM answer WHERE qid='$qid'"
        );

        $correctData = mysqli_fetch_assoc($correctQuery);
        $correctansid = $correctData['ansid'];
        $ansid = null;
    }

    /* GET OPTION TEXTS */
    $ans = "Unanswered";
    $correctans = "";

    if (!empty($ansid)) {
        $ansQuery = mysqli_query($con,
            "SELECT `option` FROM options WHERE optionid='$ansid'"
        );
        $ansData = mysqli_fetch_assoc($ansQuery);
        if ($ansData) {
            $ans = $ansData['option'];
        } else {
            $ans = "Unanswered";
        }
    }

    $correctQuery = mysqli_query($con,
        "SELECT `option` FROM options WHERE optionid='$correctansid'"
    );
    $correctData = mysqli_fetch_assoc($correctQuery);
    if ($correctData) {
        $correctans = $correctData['option'];
    } else {
        $correctans = "";
    }

    /* RESULT COLOR */
    if ($ans == $correctans && $ans != "Unanswered") {
        $color = "green";
        $icon = "✔";
    } elseif ($ans == "Unanswered") {
        $color = "orange";
        $icon = "";
    } else {
        $color = "red";
        $icon = "✘";
    }

    $output .= '
    <li>
    <p><b>' . htmlspecialchars($questionText) . '</b> 
    <span style="color:' . $color . '">' . $icon . '</span></p>
    <p><b>Your Answer:</b> ' . htmlspecialchars($ans) . '</p>
    <p><b>Correct Answer:</b> ' . htmlspecialchars($correctans) . '</p>
    </li><br>';
}

$output .= '</ol></div>';

/* ===============================
   GENERATE PDF
================================ */
// create pdf of invoice
$invoiceFileName = 'Invoice-' . $username . '.pdf';

require_once __DIR__ . '/../dompdf/autoload.inc.php';
Dompdf\Autoloader::register();

$dompdf = new \Dompdf\Dompdf();

$dompdf->loadHtml(html_entity_decode($output));
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($invoiceFileName, ["Attachment" => false]);
exit;
?>