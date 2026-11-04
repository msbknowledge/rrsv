<?php
include '../dbcon.php';
 $class_id = trim($_POST['class_id']);
 $class_id = mysqli_real_escape_string($con, $class_id);
$session = mysqli_real_escape_string($con, $_POST['session']);

$q = "SELECT * FROM rrsv_book 
      WHERE scl_class='$class_id' 
      AND scl_session='$session'
      ORDER BY book_name ASC";

$res = mysqli_query($con, $q);

echo "<option value=''>Select Book</option>";

while ($row = mysqli_fetch_assoc($res)) {
    echo "<option value='".$row['book_name']."'>".$row['book_name']." (".$row['book_pub'].")</option>";
}
?>
