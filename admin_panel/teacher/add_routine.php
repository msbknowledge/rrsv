<?php
include '../dbcon.php'; // adjust path if needed

// 🟢 Collect form data safely
$class_id     = mysqli_real_escape_string($con, $_POST['class_id'] ?? '');
$section_id   = mysqli_real_escape_string($con, $_POST['section_id'] ?? '');
$subject_id   = mysqli_real_escape_string($con, $_POST['subject_id'] ?? '');
$teacher_id   = mysqli_real_escape_string($con, $_POST['teacher_id'] ?? '');

$start_time   = mysqli_real_escape_string($con, $_POST['start_time'] ?? '');
$end_time     = mysqli_real_escape_string($con, $_POST['end_time'] ?? '');
$routine_date = mysqli_real_escape_string($con, $_POST['routine_date'] ?? '');
$session      = mysqli_real_escape_string($con, $_POST['session'] ?? '2025');
$is_break     = mysqli_real_escape_string($con, $_POST['is_break'] ?? 0);
$sub_name     = mysqli_real_escape_string($con, $_POST['sub_name'] ?? '');

$date = new DateTime($routine_date);
$day_of_week = $date->format('l');
// 🧠 Optional: fetch teacher name automatically
$teacher_name = '';
if ($teacher_id) {
    $tq = mysqli_query($con, "SELECT full_name FROM rrsv_teacher WHERE id='$teacher_id' LIMIT 1");
    if ($tr = mysqli_fetch_assoc($tq)) {
        $teacher_name = $tr['full_name'];
    }
}

// 🧩 Insert the routine record
$sql = "
INSERT INTO rrsv_routine 
(class_id, section_id, subject_id, teacher_id, day_of_week, start_time, end_time, routine_date, session, is_break, sub_name, teacher_name)
VALUES 
('$class_id', '$section_id', '$subject_id', '$teacher_id', '$day_of_week', '$start_time', '$end_time', '$routine_date', '$session', '$is_break', '$sub_name', '$teacher_name')
";

if (mysqli_query($con, $sql)) {
    echo "✅ Routine added successfully for $routine_date";
} else {
    echo "❌ Error: " . mysqli_error($con);
}
?>
