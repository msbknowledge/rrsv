<?php
include('../../include/dbcon.php');

// Get filters from POST
$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
$session = isset($_POST['scl_session']) ? mysqli_real_escape_string($myDB, $_POST['scl_session']) : '';
$unit = isset($_POST['unit']) ? mysqli_real_escape_string($myDB, $_POST['unit']) : '';

// Fetch syllabus
$sql = "SELECT s.*, sub.sub_name, c.class_name 
        FROM rrsv_syllabus s
        LEFT JOIN rrsv_class c ON s.class_id=c.id
        LEFT JOIN rrsv_subject sub ON s.subject_id=sub.id
        WHERE 1";
if ($class_id > 0)
  $sql .= " AND s.class_id=$class_id";
if ($unit > 0)
  $sql .= " AND s.unit=$unit";
if ($session != '')
  $sql .= " AND s.session='$session'";
$sql .= " ORDER BY sub.sub_name ASC";

$result = mysqli_query($myDB, $sql);

// Check if any data
if (mysqli_num_rows($result) == 0) {
  echo '<div class="alert alert-warning">No syllabus found for selected filters.</div>';
  exit;
}

// Build HTML table
$html = '<table class="table table-bordered table-striped">';
$html .= '<thead class="bg-theme-colored1 text-white">
<tr>
    <th style="width:15%;">Subject</th>
    <th style="width:15%;">Unit</th>
    <th style="width:50%;">Details</th>
    <th style="width:15%;">Page No</th>
</tr>
</thead>
<tbody>';
while ($row = mysqli_fetch_assoc($result)) {
    $syllabus_id = $row['id'];
    $detailsRes = mysqli_query($myDB, "SELECT * FROM rrsv_syllabus_details WHERE syllabus_id=$syllabus_id ORDER BY id ASC");

    $detailsHtml = '';
    $pageNumbersHtml = ''; // store formatted page numbers

    while ($d = mysqli_fetch_assoc($detailsRes)) {
        $chapter = htmlspecialchars($d['chapter']);
        $description = htmlspecialchars($d['description']);
        $page_no = htmlspecialchars($d['page_no']);

        $detailsHtml .= "<b>$chapter</b>: $description<br>";

        if (!empty($page_no)) {
            $pageNumbersHtml .= $page_no . "<br>";
        }
    }

    $subjectName = htmlspecialchars($row['sub_name']);
    $unitNumber = (int)$row['unit'];

    // Convert unit number to text
    if ($unitNumber == 1) {
        $unitText = '1st Unit';
    } elseif ($unitNumber == 2) {
        $unitText = '2nd Unit';
    } elseif ($unitNumber == 3) {
        $unitText = '3rd Unit';
    } else {
        $unitText = $unitNumber . 'th Unit';
    }

    $html .= '<tr>
        <td style="vertical-align:top;">' . $subjectName . '</td>
        <td>' . $unitText . '</td>
        <td>' . $detailsHtml . '</td>
        <td>' . $pageNumbersHtml . '</td>
    </tr>';
}


$html .= '</tbody></table>';

// Output
echo $html;
?>