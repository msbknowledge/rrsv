<?php
include('../../include/dbcon.php');

if (isset($_POST['class_id'])) {
  $classId = intval($_POST['class_id']);

  // Get class_name from id
  $classRes = mysqli_query($myDB, "SELECT class_name FROM rrsv_class WHERE id = $classId LIMIT 1");
  $classRow = mysqli_fetch_assoc($classRes);
  $className = $classRow['class_name'];

  // Fetch subjects
  $subjects = [];
  $subRes = mysqli_query($myDB, "SELECT id, sub_name FROM rrsv_subject WHERE class_name = '$className'");
  while ($row = mysqli_fetch_assoc($subRes)) {
    $subjects[] = $row;
  }

  // Fetch sections
  $sections = [];
  $secRes = mysqli_query($myDB, "SELECT id, section_name FROM rrsv_section WHERE class_name = '$className'");
  while ($row = mysqli_fetch_assoc($secRes)) {
    $sections[] = $row;
  }

  echo json_encode([
    'subjects' => $subjects,
    'sections' => $sections
  ]);
}
