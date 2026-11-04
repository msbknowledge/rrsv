<?php
session_start();
$token = $_SESSION['_token'] ?? '';
include('../../include/dbcon.php');
// response: 0 = insert, 1 = update, 2 = duplicate
$response = -1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id = intval($_POST['id'] ?? 0);
	$session = mysqli_real_escape_string($myDB, $_POST['scl_session'] ?? '');
	$class_id = intval($_POST['class_id'] ?? 0);
	$subject_id = intval($_POST['subject_id'] ?? 0);
	$unit = intval($_POST['unit'] ?? 0);

	// check duplicate syllabus (session+class+subject) if not updating same id
	$dupCheckSql = "SELECT id FROM rrsv_syllabus WHERE session='$session' AND class_id=$class_id AND unit=$unit AND subject_id=$subject_id";
	if ($id > 0) {
		$dupCheckSql .= " AND id!=$id";
	}
	$dupRes = mysqli_query($myDB, $dupCheckSql);
	if (mysqli_num_rows($dupRes) > 0) {
		echo 2;
		exit; // duplicate
	}

	// file upload (optional)
	$syllabus_file = '';
	if (!empty($_FILES['syllabus_file']['name'])) {
		$uploadDir = "../../uploads/syllabus/";
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0777, true);
		}

		$filename = time() . "_" . basename($_FILES['syllabus_file']['name']);
		$target = $uploadDir . $filename;

		if (move_uploaded_file($_FILES['syllabus_file']['tmp_name'], $target)) {
			$syllabus_file = "uploads/syllabus/" . $filename;
		}
	}

	// INSERT or UPDATE syllabus
	if ($id > 0) {
		// update
		if ($syllabus_file != '') {
			$sql = "UPDATE rrsv_syllabus SET session='$session', class_id=$class_id, unit=$unit,subject_id=$subject_id, syllabus_file='$syllabus_file' WHERE id=$id";
		} else {
			$sql = "UPDATE rrsv_syllabus SET session='$session', class_id=$class_id,unit=$unit, subject_id=$subject_id WHERE id=$id";
		}
		mysqli_query($myDB, $sql);

		// delete old chapter details
		mysqli_query($myDB, "DELETE FROM rrsv_syllabus_details WHERE syllabus_id=$id");

		$syllabusId = $id;
		$response = 1; // updated
	} else {
		// insert
		$sql = "INSERT INTO rrsv_syllabus (session, class_id, subject_id, syllabus_file,unit) 
                VALUES ('$session', $class_id, $subject_id, '$syllabus_file','$unit')";
		mysqli_query($myDB, $sql);
		$syllabusId = mysqli_insert_id($myDB);
		$response = 0; // inserted
	}

	// insert chapter details
	if (!empty($_POST['chapter'])) {
		foreach ($_POST['chapter'] as $k => $chapter) {
			$chapter = mysqli_real_escape_string($myDB, $chapter);
			$description = mysqli_real_escape_string($myDB, $_POST['description'][$k] ?? '');
			$page_no = mysqli_real_escape_string($myDB, $_POST['page_no'][$k] ?? '');

			if ($chapter != '') {
				$sql = "INSERT INTO rrsv_syllabus_details (syllabus_id, chapter, description, page_no) 
                        VALUES ($syllabusId, '$chapter', '$description', '$page_no')";
				mysqli_query($myDB, $sql);
			}
		}
	}
}

echo $response;