<?php
include('include/dbcon.php');

// Get POST data
$student_id  = $_POST['student_id'];
$class       = $_POST['scl_class'];
$session     = $_POST['scl_session'];
$unit        = $_POST['unit'];

$body_weight = trim($_POST['body_weight']);
$home_project = trim($_POST['home_project']);
$grade = trim($_POST['grade']);

$subjects = $_POST['marksheet']['subject'];
$unit_marks = $_POST['marksheet']['unit_marks'];
$unit_hm = $_POST['marksheet']['unit_hm'];

// Determine unit column prefix
$unit_prefix = '';
if ($unit == 1) $unit_prefix = '1st_unit';
elseif ($unit == 2) $unit_prefix = '2nd_unit';
elseif ($unit == 3) $unit_prefix = '3rd_unit';

// ===== Update or Insert rrsv_marksheet_unit =====
$check_unit_sql = "SELECT id,unit FROM rrsv_marksheet_unit WHERE student_id = '$student_id' AND session = '$session' AND class = '$class'";
$check_unit_res = mysqli_query($myDB, $check_unit_sql);
$current_unit_status = "";
if (mysqli_num_rows($check_unit_res) > 0) {
//print_r(mysqli_fetch_assoc($check_unit_res));
$rrsv_marksheet_unit_array = mysqli_fetch_array($check_unit_res,MYSQLI_ASSOC);
$current_unit_status = $rrsv_marksheet_unit_array['unit'];

    // Update
    $update_unit_sql = "UPDATE rrsv_marksheet_unit SET 
        {$unit_prefix}_body_weight = '$body_weight',
        {$unit_prefix}_home_project = '$home_project',
        {$unit_prefix}_grade = '$grade'
        WHERE student_id = '$student_id' AND session = '$session' AND class = '$class'";
    mysqli_query($myDB, $update_unit_sql);
} else {
    // Insert
    $insert_unit_sql = "INSERT INTO rrsv_marksheet_unit (
        student_id, session, class, unit,
        1st_unit_body_weight, 1st_unit_home_project, 1st_unit_grade,
        2nd_unit_body_weight, 2nd_unit_home_project, 2nd_unit_grade,
        3rd_unit_body_weight, 3rd_unit_home_project, 3rd_unit_grade
    ) VALUES (
        '$student_id', '$session', '$class', '2nd_unit',
        " . (($unit == 1) ? "'$body_weight','$home_project','$grade'" : "'','',''") . ",
        " . (($unit == 2) ? "'$body_weight','$home_project','$grade'" : "'','',''") . ",
        " . (($unit == 3) ? "'$body_weight','$home_project','$grade'" : "'','',''") . "
    )";
    mysqli_query($myDB, $insert_unit_sql);
}

// ===== Process Each Subject =====
for ($i = 0; $i < count($subjects); $i++) {
    $subject = $myDB->real_escape_string(trim($subjects[$i]));
    $marks = (int) $unit_marks[$i];
    $hm = (int) $unit_hm[$i];

    // Check if subject exists
    $check_sql = "SELECT * FROM rrsv_marksheet WHERE student_id = '$student_id' AND class = '$class' AND session = '$session' AND subject = '$subject'";
    $check_res = mysqli_query($myDB, $check_sql);

    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);

        // Update unit marks
        $update_sql = "UPDATE rrsv_marksheet SET 
            {$unit_prefix}_marks = '$marks',
            {$unit_prefix}_hm = '$hm' 
            WHERE student_id = '$student_id' AND class = '$class' AND session = '$session' AND subject = '$subject'";
        mysqli_query($myDB, $update_sql);

        
    } else {
        // Insert new subject
        $insert_sql = "INSERT INTO rrsv_marksheet (
            student_id, class, session, subject,
            {$unit_prefix}_marks, {$unit_prefix}_hm
        ) VALUES (
            '$student_id', '$class', '$session', '$subject',
            '$marks', '$hm'
        )";
        mysqli_query($myDB, $insert_sql);
    }

    if($current_unit_status == 1) {
        // Recalculate totals
        $m1 = (int)$row['1st_unit_marks'];
        $m2 = (int)$row['2nd_unit_marks'];
        $m3 = (int)$row['3rd_unit_marks'];

        $hm1 = (int)$row['1st_unit_hm'];
        $hm2 = (int)$row['2nd_unit_hm'];
        $hm3 = (int)$row['3rd_unit_hm'];

        // Overwrite the current unit with new value
        if ($unit == 1) { $m1 = $marks; $hm1 = $hm; }
        if ($unit == 2) { $m2 = $marks; $hm2 = $hm; }
        if ($unit == 3) { $m3 = $marks; $hm3 = $hm; }

        $total = $m1 + $m2 + $m3;
        $total_hm = $hm1 + $hm2 + $hm3;
        $percent = ($total > 0) ? round(($total / 200) * 100, 2) : 0;

        $update_total = "UPDATE rrsv_marksheet SET 
            total = '$total',
            total_hm = '$total_hm',
            percent = '$percent'
            WHERE student_id = '$student_id' AND class = '$class' AND session = '$session' AND subject = '$subject'";
        mysqli_query($myDB, $update_total);
    }
}

// Redirect after processing
echo "<script>alert('Marksheet updated successfully!'); window.location.href='manage_marksheet.php';</script>";
exit;
?>
