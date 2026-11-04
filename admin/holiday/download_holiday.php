<?php
include('../../include/dbcon.php');
require_once('../../libray/tcpdf/tcpdf.php');


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Syllabus ID is required.');
}

$id = intval($_GET['id']);

// Fetch syllabus info
$sql = "SELECT s.*, c.class_name, sub.sub_name
        FROM rrsv_syllabus s
        LEFT JOIN rrsv_class c ON s.class_id=c.id
        LEFT JOIN rrsv_subject sub ON s.subject_id=sub.id
        WHERE s.id=$id";
$res = mysqli_query($myDB, $sql);
if (!$row = mysqli_fetch_assoc($res)) {
    die('Syllabus not found.');
}

// Fetch chapters
$chapRes = mysqli_query($myDB, "SELECT * FROM rrsv_syllabus_details WHERE syllabus_id=$id");

// Create new PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('School Management System');
$pdf->SetTitle('Syllabus - ' . $row['sub_name']);
$pdf->SetHeaderData('', 0, 'Syllabus', '');
$pdf->setHeaderFont(array('helvetica', '', 12));
$pdf->setFooterFont(array('helvetica', '', 10));
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

// School Info
$html = '<h2 style="text-align:center;">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h2>';
$html .= '<p style="text-align:center;">Reg. No.: SO196094, U-DISE Code: 19251610302, ESTD: 2012</p>';
$html .= '<p style="text-align:center;">Baidyadanga, Rasulpur, Memari, Bardhaman, Pin - 713151</p>';
$html .= '<hr>';

// Syllabus Info
$html .= '<h3>Syllabus Details</h3>';
$html .= '<p><strong>Session:</strong> ' . $row['session'] . '</p>';
$html .= '<p><strong>Class:</strong> ' . $row['class_name'] . '</p>';
$html .= '<p><strong>Subject:</strong> ' . $row['sub_name'] . '</p>';

// Chapters
if (mysqli_num_rows($chapRes) > 0) {
    $html .= '<h4>Chapters</h4>
    <table border="1" cellpadding="5">
        <tr>
            <th>SL</th>
            <th>Chapter</th>
            <th>Description</th>
            <th>Page No.</th>
        </tr>';
    $sl = 1;
    while ($ch = mysqli_fetch_assoc($chapRes)) {
        $html .= '<tr>
            <td>' . $sl . '</td>
            <td>' . $ch['chapter'] . '</td>
            <td>' . $ch['description'] . '</td>
            <td>' . $ch['page_no'] . '</td>
        </tr>';
        $sl++;
    }
    $html .= '</table>';
}

// Output PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Syllabus_' . $row['class_name'] . '_' . $row['sub_name'] . '.pdf', 'D'); // 'D' forces download
