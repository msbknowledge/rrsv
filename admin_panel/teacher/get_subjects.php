<?php
include '../../include/config.php';
include '../dbcon.php';
if (isset($_POST['class_name'])) {
  $class_name = trim($_POST['class_name']);
 $class_name = mysqli_real_escape_string($con, $class_name);
  $query = "SELECT * FROM rrsv_subject WHERE class_name='$class_name' ORDER BY sub_name";
  $result = mysqli_query($con, $query);
  if (mysqli_num_rows($result) > 0) {
    echo '<option value="">Select Subject</option>';
    while ($row = mysqli_fetch_assoc($result)) {
      echo '<option value="' . $row['id'] . '">' . $row['sub_name'] . '</option>';
    }
  } else {
    echo '<option value="">No subjects1 found</option>';
  }
}
// if (isset($_POST['class_id'])) {
//   $class_id = intval($_POST['class_id']);
//   $query = "SELECT * FROM rrsv_subject WHERE id='$class_id' ORDER BY sub_name";
//   $result = mysqli_query($con, $query);

//   if (mysqli_num_rows($result) > 0) {
//     echo '<option value="">Select Subject</option>';
//     while ($row = mysqli_fetch_assoc($result)) {
//       echo '<option value="' . $row['id'] . '">' . $row['sub_name'] . '</option>';
//     }
//   } else {
//     echo '<option value="">No subjects found</option>';
//   }
// }
?>
