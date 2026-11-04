<?php
session_start();
include __DIR__ . '/../dbcon.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? ($_GET['action'] ?? 'save');

if ($action === 'save') {
    $report_date = mysqli_real_escape_string($con, trim($_POST['report_date'] ?? date('Y-m-d')));
    $scl_session = mysqli_real_escape_string($con, trim($_POST['scl_session'] ?? date('Y')));
    $total_students = intval($_POST['total_students'] ?? 0);
    $new_adm = floatval($_POST['new_admission_collection'] ?? 0);
    $readm = floatval($_POST['readmission_collection'] ?? 0);
    $total = floatval($_POST['total_collection'] ?? 0);
    $notes = mysqli_real_escape_string($con, trim($_POST['notes'] ?? 'Daily Money Report Snapshot'));
    $user = $_SESSION['username'] ?? 'Admin';

    // Insert or update on duplicate
    $sql = "INSERT INTO rrsv_daily_money_report_snapshot 
            (report_date, scl_session, total_students, new_admission_collection, readmission_collection, total_collection, notes, saved_by)
            VALUES 
            ('$report_date', '$scl_session', $total_students, $new_adm, $readm, $total, '$notes', '$user')
            ON DUPLICATE KEY UPDATE 
            total_students = VALUES(total_students),
            new_admission_collection = VALUES(new_admission_collection),
            readmission_collection = VALUES(readmission_collection),
            total_collection = VALUES(total_collection),
            notes = VALUES(notes),
            saved_by = VALUES(saved_by),
            created_at = CURRENT_TIMESTAMP";

    if (mysqli_query($con, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Daily snapshot for ' . date('d-m-Y', strtotime($report_date)) . ' saved successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => mysqli_error($con)
        ]);
    }
    exit;
} elseif ($action === 'list') {
    $res = mysqli_query($con, "SELECT * FROM rrsv_daily_money_report_snapshot ORDER BY report_date DESC LIMIT 50");
    $list = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $list[] = $r;
    }
    echo json_encode(['success' => true, 'data' => $list]);
    exit;
}
