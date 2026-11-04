<?php
include('include/dbcon.php');

header('Content-Type: application/json');

$admission_code = $myDB->escape_string(trim($_POST['admission_code']));

$sql = "SELECT id, name,reg_no, class_name,inquery_type, phone, scl_session, from_no, d_o_i
        FROM rrsv_inquery
        WHERE admission_code = '$admission_code'
          AND status = 'Panding'
          AND is_valid = 'Yes'
        LIMIT 1";

$res = mysqli_query($myDB, $sql);

if ($res && mysqli_num_rows($res) > 0) {
  $data1 = mysqli_fetch_assoc($res);
  if ($data1['inquery_type'] == 'Admission') {

    echo json_encode(['status' => 'success', 'admission' => $data1]);
  } else {
    
$reg_no = mysqli_real_escape_string($myDB, $data1['reg_no']);
    $sql2 = "SELECT * FROM rrsv_student_registration WHERE scl_reg_no = '$reg_no' LIMIT 1";
    $res2 = mysqli_query($myDB, $sql2);
    $data2 = mysqli_fetch_assoc($res2);
    echo json_encode(['status' => 'success', 'readmission' => $data2,'admission' => $data1]);

  }

} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid or already used admission code.']);
}
?>