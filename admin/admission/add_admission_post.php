<?php
ob_clean();
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
include('../../include/dbcon.php');

$response = ['status' => 'error', 'type' => 'admission', 'message' => 'Invalid or Student admitted.'];

function getPost($key, $db, $default = '')
{
	return $db->real_escape_string(trim($_POST[$key] ?? $default));
}

function uploadFile($fileKey, $uploadDir)
{
	if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK)
		return '';
	if (!is_dir($uploadDir))
		mkdir($uploadDir, 0777, true);
	$filename = basename($_FILES[$fileKey]['name']);
	$targetFile = $uploadDir . '/' . $filename;
	if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
		chmod($targetFile, 0777);
		return $filename;
	}
	return '';
}

// Get student ID
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$student = [];
if ($id > 0) {
	$stmt = $myDB->prepare("SELECT * FROM rrsv_student_registration WHERE id='$id' LIMIT 1");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0)
		$student = $result->fetch_assoc();
	$stmt->close();
}
//   echo json_encode([
//             'status' => 'success',
//             'type' => 'readmission',
//             'data' => $student ,
//             'message' => 'Valid registration number.'.$student['image']
//         ]);
//         exit();
$add_status = $id ? 'Readmission' : 'New Admission';

// Collect POST data
$fields = [
	'scl_roll_no',
	'scl_reg_no',
	'scl_name',
	'scl_father_name',
	'scl_father_quli',
	'scl_father_occu',
	'scl_father_inc',
	'scl_mother_name',
	'scl_mother_quli',
	'scl_mother_occu',
	'scl_mother_inc',
	'scl_dob',
	'scl_date',
	'scl_address',
	'scl_phone_no',
	'scl_religion',
	'scl_gender',
	'scl_class',
	'scl_session',
	'scl_section',
	'scl_car',
	'bank_name',
	'branch_name',
	'bank_ac_no',
	'bank_ifsc_code',
	'scl_nationality',
	'scl_category',
	'scl_pre_name',
	'scl_pre_class',
	'scl_tcn',
	'scl_tc_date',
	'scl_blood',
	'scl_dist',
	'scl_po',
	'scl_aadhar',
	'scl_location',
	'scl_block',
	'scl_email',
	'scl_bpl',
	'scl_disa',
	'scl_language',
	'scl_ide',
	'scl_pos',
	'scl_pol',
	'scl_dest',
	'alt_phone',
	'scl_mu',
	'scl_state',
	'scl_pin',
	'monthly_fee',
	'nationality',
	'religion'
];

$data = [];
foreach ($fields as $f)
	$data[$f] = getPost($f, $myDB);

// Capitalize name
$data['scl_name'] = strtoupper($data['scl_name']);

// Upload files
$uploads = [
	'image' => '../../student_reg_image',
	'dob_image' => '../../student_dob',
	'aadhar_image' => '../../student_aadhar',
	'father_aadhar_image' => '../../father_aadhar_image',
	'father_voter_image' => '../../father_voter_image',
	'mother_voter_image' => '../../mother_voter_image',
	'mother_aadhar_image' => '../../mother_aadhar_image',
	'passbook_image' => '../../passbook_image'
];

foreach ($uploads as $key => $dir) {
	$data[$key] = uploadFile($key, $dir) ?: ($student[$key] ?? '');
}

// Get next ID
$resultLast = mysqli_query($myDB, "SELECT id FROM rrsv_student_registration ORDER BY id DESC LIMIT 1");
$nextId = 1;
if ($resultLast) {
	$rowLast = mysqli_fetch_assoc($resultLast);
	$nextId = ($rowLast) ? $rowLast['id'] + 1 : 1;
}
//$id ? 'Readmission' : 'New Admission';
$scl_name = $data['scl_name'];


// Generate next roll number based on class, session, section
$scl_class = $data['scl_class'];
$scl_session = $data['scl_session'];
$scl_section = $data['scl_section'];

$query = "
    SELECT MAX(scl_roll_no + 0) AS max_roll
    FROM rrsv_student_registration
    WHERE scl_class = '$scl_class'
      AND scl_session = '$scl_session'
      AND scl_section = '$scl_section'
";

$res = mysqli_query($myDB, $query);
$row = mysqli_fetch_assoc($res);

$nextRoll = ($row['max_roll'] !== null) ? $row['max_roll'] + 1 : 1;


$data['scl_roll_no'] = $nextRoll;

$data['scl_reg_no'] = $id ?$student['scl_reg_no']:($scl_session . '-' . $scl_section . '-' . $nextId);

// Fetch fees safely
$scl_class = $data['scl_class'];
$res1 = mysqli_query($myDB, "SELECT monthly_fee, monthly_admission_fee, yearly_car_fee, yearly_fee FROM scl_fee WHERE scl_class='$scl_class'");
if ($res1 && mysqli_num_rows($res1) > 0) {
	$row = mysqli_fetch_assoc($res1);
	$data['monthly_fee'] = $row['monthly_fee'] ?? 0;
	$monthly_admission_fee = $row['monthly_admission_fee'] ?? 0;
} else {
	$data['monthly_fee'] = 0;
	$monthly_admission_fee = 0;
}

// Year-end expiry date
$currentMonth = date('m');
if ($currentMonth == 11 || $currentMonth == 12) {
	$data['exp_date'] = date('Y-12-31', strtotime('+1 year'));
} else {
	$data['exp_date'] = date('Y-12-31');
}

$data['add_status'] = $add_status;

// Insert student
$cols = implode(',', array_keys($data));
$vals = "'" . implode("','", array_map([$myDB, 'real_escape_string'], array_values($data))) . "'";
// $sql = "INSERT INTO rrsv_student_registration ($cols) VALUES ($vals)";
// $res = $myDB->query($sql);

// Insert into rrsv_admission
$sqlAd = "INSERT INTO rrsv_admission ($cols) VALUES ($vals)";
$myDB->query($sqlAd);

// Update rrsv_inquery

$myDB->query("UPDATE rrsv_inquery SET status='Admission', is_valid='No' WHERE class_name='$scl_class' AND scl_session='$scl_session' AND name='$scl_name'");

$response['status'] = 'success';
$response['message'] = 'Student admitted successfully.';

echo json_encode($response);
?>