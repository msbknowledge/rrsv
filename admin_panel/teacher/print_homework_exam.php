<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Question Paper</title>

<style>
    body { font-family: Arial, sans-serif; margin: 30px; }
    h2, h3 { margin: 0; padding: 0; }
    .paper-header { text-align: center; margin-bottom: 20px; }
    .question { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px dashed #bbb; }
    .options { margin-left: 20px; margin-top: 10px; }
    .image-option { max-width: 150px; margin-top: 6px; }
    .video-note { color: #d9534f; margin-top: 5px; font-size: 14px; }
    @media print {
        .no-print { display: none !important; }
        body { margin: 0; padding: 20px; }
        .question { page-break-inside: avoid; }
    }
        .header-table {
    width: 90%;              /* not full width → looks centered */
    margin: 15px auto 0;     /* centers the table */
    font-size: 14px;
}

.header-table td {
    width: 50%;
    vertical-align: top;
}

.header-table .left {
    text-align: left;
}

.header-table .right {
    text-align: right;
}
.bold {
    font-weight: bold;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}
.image-options-row {
    display: flex;
    gap: 5px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.option-item {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 8px;
}

.option-item img {
    max-width: 100px;
    height: auto;
    display: block;
    border-radius: 6px;
}

/* A, B, C label on top-left */
.option-label {
    position: absolute;
    top: 6px;
    left: 6px;
    background: #000;
    color: #fff;
    font-weight: bold;
    padding: 1px 1px;
    border-radius: 1px;
    font-size: 14px;
}

</style>
</head>

<body>

<?php
session_start();
include '../dbcon.php';

// 🔒 Allow only teacher accounts
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
    die("Unauthorized Access");
}

$exam_id = $_GET['exam_id'] ?? 0;

// Fetch exam info
$exam = $con->query("SELECT e.*,c.class_name,s.sub_name as subject_name FROM rrsv_homework_exam e LEFT JOIN rrsv_class c ON c.id = e.class_id LEFT JOIN rrsv_subject s ON s.id = e.subject_id WHERE e.id='$exam_id'")
          ->fetch_assoc();
// $exam = $con->query("
//     SELECT 
//         e.*,
//         c.class_name,
//         s.subject_name
//     FROM rrsv_homework_exam e
//     LEFT JOIN rrsv_class c ON c.id = e.class_id
//     LEFT JOIN rrsv_subject s ON s.id = e.subject_id
//     WHERE e.id ='$exam_id'")->fetch_assoc();
//print_r($exam);die();
// Fetch questions + teacher's answer
$questions = $con->query("
    SELECT q.*, a.answer AS teacher_answer 
    FROM rrsv_homework_exam_questions q
    LEFT JOIN rrsv_teacher_answers a ON q.id = a.question_id
    WHERE q.exam_id='$exam_id'
");
?>

<!-- PRINT & BACK BUTTONS -->
<div class="no-print" style="text-align:right; margin-bottom:20px;">
    <button onclick="window.print()" 
        style="padding:8px 15px; font-size:16px; background:#007bff; color:#fff; border:none; border-radius:4px;">
        🖨 Print
    </button>

    <a href="homework_exam_questions.php?exam_id=<?= $exam_id ?>" 
       style="padding:8px 15px; font-size:16px; background:#28a745; color:#fff; border:none; border-radius:4px; text-decoration:none;">
       ⬅ Back
    </a>
</div>

<!-- HEADER -->
<div class="paper-header">
    <div class="school-name bold">
        RASULPUR RAMKRISHNA SARADA VIDYAPITH
    </div>

    <div class="exam-subtitle bold">
        <?= $exam['unit'] ?><?= $exam['unit']==1?'st':($exam['unit']==2?'nd':'rd') ?> Semester  Class – <?= htmlspecialchars($exam['class_name']) ?>
        (Sub – <?= htmlspecialchars($exam['subject_name']) ?>)
    </div>

    <div class="exam-subtitle bold">
        Mode of Examination <?= ucfirst($exam['type']) ?>
        <!--<?= ucfirst($exam['type']) ?>-->
         
    </div>

    <table class="header-table">
        <tr>
            <td class="left exam-subtitle">
                <b>F.M.:</b> ______________________<br><br>
                <!--<?= $exam['unit']==1?'60':($exam['unit']==2?'80':'60') ?>  -->
                <b>Name:</b> ______________________
            </td>

            <td class="right exam-subtitle">
                <b>Date:</b>_______________________<br><br>
                <!--<?= date('d-m-Y') ?> -->
                <b>Time:</b> <?= htmlspecialchars($exam['exam_time'] ?? '______________________') ?><br><br>
                <b>Roll No:</b> ____________________
            </td>
        </tr>
    </table>
</div>
<hr>
<?php 

// -------------------------------------------
// STEP 1: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
$fullList = $questions->fetch_all(MYSQLI_ASSOC);
$questions->data_seek(0); 
$headOfType = [];
foreach ($fullList as $row) {
    $type = strtolower($row['question_type']);
    $head = trim($row['question_head']);
    if ($head !== "" && $head !== "NULL" && !isset($headOfType[$type])) {
        $headOfType[$type] = $head;   // store only first head found
    }
}

// -------------------------------------------
// STEP 1: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------

$sn = 1;

// -------------------------------------------
// STEP 2: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
$headLetterCount = [];
$printedHead = [];
$questionNumber = [];

// -------------------------------------------
// STEP 2: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
while ($q = $questions->fetch_assoc()):
   
// -------------------------------------------
// STEP 3: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
    $type = strtolower($q['question_type']);
    $head = $headOfType[$type] ?? "";  
    $showHead = false;
    if (!isset($questionNumber[$type])) {
        $questionNumber[$type] = 1;
    }

    if ($head !== "" && empty($printedHead[$type])) {
        if (!isset($headLetterCount[$exam_id])) {
            $headLetterCount[$exam_id] = 0;
        }
        $letter = chr(65 + $headLetterCount[$exam_id]++);
        $printedHead[$type] = $letter . ". " . $head;

        $showHead = true;
    }
    $currentQuestionNumber = $questionNumber[$type]++;
    
// -------------------------------------------
// STEP 3: PRE-SCAN HEADS PER QUESTION TYPE
// -------------------------------------------
    if ($q['question_type'] == 'video') {
        continue;
    }
?>
<div class="question">
            <?php if ($showHead): ?>
        <div style="font-size:18px; color:#0073b7;">
            <b><?= htmlspecialchars($printedHead[$type]); ?></b>
        </div>
    <?php else: ?>
        <?php if (!empty($q['question_head'])): ?>
            <div style="font-size:16px; color:#555;">
                <b><?= htmlspecialchars($q['question_head']); ?></b>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <b>Q<?= $currentQuestionNumber ?>..</b> <?= ($q['question']) ?>

    <!-- ==================== MCQ TEXT ======================= -->
    <?php if ($q['question_type'] == 'mcq'): ?>
        <div class="options">
            <?php if ($q['option_a']): ?><div>A. <?= htmlspecialchars($q['option_a']) ?></div><?php endif; ?>
            <?php if ($q['option_b']): ?><div>B. <?= htmlspecialchars($q['option_b']) ?></div><?php endif; ?>
            <?php if ($q['option_c']): ?><div>C. <?= htmlspecialchars($q['option_c']) ?></div><?php endif; ?>
            <?php if ($q['option_d']): ?><div>D. <?= htmlspecialchars($q['option_d']) ?></div><?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ==================== IMAGE MCQ ======================= -->
    

<?php if ($q['question_type'] == 'image'): ?>
    <div class="image-options-row">
        
        <?php if ($q['option_a']): ?>
            <div class="option-item">
                <strong>A.</strong>
                <img src="../../uploads/questions/<?= $q['option_a'] ?>">
            </div>
        <?php endif; ?>

        <?php if ($q['option_b']): ?>
            <div class="option-item">
                <strong>B.</strong>
                <img src="../../uploads/questions/<?= $q['option_b'] ?>">
            </div>
        <?php endif; ?>

        <?php if ($q['option_c']): ?>
            <div class="option-item">
                <strong>C.</strong>
                <img src="../../uploads/questions/<?= $q['option_c'] ?>">
            </div>
        <?php endif; ?>

        <?php if ($q['option_d']): ?>
            <div class="option-item">
                <strong>D.</strong>
                <img src="../../uploads/questions/<?= $q['option_d'] ?>">
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>

    <!--<?php if ($q['question_type'] == 'image'): ?>-->
    <!--    <div class="options">-->
    <!--        <?php if ($q['option_a']): ?><div>A. <img src="../../uploads/questions/<?= $q['option_a'] ?>" class="image-option"></div><?php endif; ?>-->
    <!--        <?php if ($q['option_b']): ?><div>B. <img src="../../uploads/questions/<?= $q['option_b'] ?>" class="image-option"></div><?php endif; ?>-->
    <!--        <?php if ($q['option_c']): ?><div>C. <img src="../../uploads/questions/<?= $q['option_c'] ?>" class="image-option"></div><?php endif; ?>-->
    <!--        <?php if ($q['option_d']): ?><div>D. <img src="../../uploads/questions/<?= $q['option_d'] ?>" class="image-option"></div><?php endif; ?>-->
    <!--    </div>-->
    <!--<?php endif; ?>-->

    <!-- ==================== DESCRIPTIVE ======================= -->
    <?php if ($q['question_type'] == 'descriptive'): ?>
        <div style="margin-top:10px;">
           
        </div>
    <?php endif; ?>

</div>

<?php endwhile; ?>


</body>
</html>
