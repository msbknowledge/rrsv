<?php
include('../../include/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // List of allowed fields
    $fields = [
        'scl_roll_no','scl_reg_no','scl_name','scl_father_name','scl_father_quli',
        'scl_father_occu','scl_father_inc','scl_mother_name','scl_mother_quli',
        'scl_mother_occu','scl_mother_inc','scl_dob','scl_date','scl_address',
        'scl_phone_no','scl_religion','scl_gender','scl_class','scl_session',
        'scl_section','scl_car','bank_name','branch_name','bank_ac_no','bank_ifsc_code',
        'scl_nationality','scl_category','scl_pre_name','scl_pre_class','scl_tcn',
        'scl_tc_date','scl_blood','scl_dist','scl_po','scl_aadhar','scl_location',
        'scl_block','scl_email','scl_bpl','scl_disa','scl_language','scl_ide',
        'scl_pos','scl_pol','scl_dest','alt_phone','scl_mu','scl_state','scl_pin',
        'monthly_fee','nationality','religion'
    ];

    // Sanitize and collect POST data
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = mysqli_real_escape_string($myDB, $_POST[$field] ?? '');
    }

    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        // Build update query dynamically
        $set_parts = [];
        foreach ($data as $key => $val) {
            $set_parts[] = "`$key`='$val'";
        }
        $set_sql = implode(',', $set_parts);

        $query = "UPDATE rrsv_admission SET $set_sql WHERE id='$id'";
        if (mysqli_query($myDB, $query)) {
             echo "<script>alert('Admission Success'); window.location='manage_admission.php';</script>";
        } else {
           echo "<script>alert('Admission failed'); window.location='manage_admission.php';</script>"; // failed
        }
    } else {
       echo "<script>alert('Admission invalid ID'); window.location='manage_admission.php';</script>";  // invalid ID
    }
}
?>
