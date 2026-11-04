<?php
include('include/dbcon.php');
header('Content-Type: application/json');

// Detect request type based on POST keys
$admission_code = isset($_POST['admission_code']) ? trim($_POST['admission_code']) : '';
$reg_no = isset($_POST['reg_no']) ? trim($_POST['reg_no']) : '';

if ($admission_code !== '') {
    // 🔹 ADMISSION VALIDATION
    $admission_code = $myDB->real_escape_string($admission_code);

    $sql = "SELECT id, name, class_name, phone, scl_session, from_no, d_o_i
            FROM rrsv_inquery
            WHERE admission_code = '$admission_code'
              AND status = 'Panding'
              AND is_valid = 'Yes'
            LIMIT 1";

    $res = $myDB->query($sql);

    if ($res && $res->num_rows > 0) {
        $data = $res->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'type' => 'admission',
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'type' => 'admission',
            'message' => 'Invalid or already used admission code.'
        ]);
    }

} elseif ($reg_no !== '') {
    // 🔹 READMISSION VALIDATION
    $reg_no = $myDB->real_escape_string($reg_no);

    $sql = "SELECT scl_name,scl_phone_no,scl_session,scl_class  FROM  rrsv_student_registration WHERE scl_reg_no = '$reg_no' LIMIT 1";
    $res = $myDB->query($sql);

    if ($res && $res->num_rows > 0) {
        $data = $res->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'type' => 'readmission',
            'data' => $data ,
            'message' => 'Valid registration number.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'type' => 'readmission',
            'message' => 'Registration number not found.'
        ]);
    }

} else {
    // 🔸 No input provided
    echo json_encode([
        'status' => 'error',
        'message' => 'No admission code or registration number provided.'
    ]);
}
?>