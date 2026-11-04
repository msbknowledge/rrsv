<?php
session_start();
include('../../include/dbcon.php');
include('../../include/config.php');

// Get POST or GET variables
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? 0;
$id = (int) $id;

if ($action === 'view' && $id > 0) {
    $res = mysqli_query($myDB, "SELECT s.*, c.class_name, sub.sub_name
        FROM rrsv_syllabus s
        LEFT JOIN rrsv_class c ON s.class_id=c.id
        LEFT JOIN rrsv_subject sub ON s.subject_id=sub.id
        WHERE s.id=$id");

    if ($row = mysqli_fetch_assoc($res)) {
        $html = "<p><strong>Session:</strong> {$row['session']}</p>";
        $html .= "<p><strong>Class:</strong> {$row['class_name']}</p>";
        $html .= "<p><strong>Subject:</strong> {$row['sub_name']}</p>";

        // if (!empty($row['syllabus_file'])) {
        //     $html .= "<p><strong>File:</strong> <a href='../../{$row['syllabus_file']}' target='_blank'>Download</a></p>";
        // }

        // Fetch chapters
        $chapRes = mysqli_query($myDB, "SELECT * FROM rrsv_syllabus_details WHERE syllabus_id=$id");
        if (mysqli_num_rows($chapRes) > 0) {
            $html .= "<h5>Chapters</h5>
<table class='table table-bordered'>
    <tr>
        <th>Chapter</th>
        <th>Description</th>
        <th>Page No.</th>
    </tr>";

            while ($ch = mysqli_fetch_assoc($chapRes)) {
                $html .= "<tr>
        <td>{$ch['chapter']}</td>
        <td>{$ch['description']}</td>
        <td>{$ch['page_no']}</td>
    </tr>";
            }

            $html .= "</table>";
        }

        // Return as JSON
        echo json_encode(['status' => 1, 'html' => $html]);
    } else {
        echo json_encode(['status' => 0, 'msg' => 'Syllabus not found']);
    }
    exit;
}
