<?php
// search_expense.php
header('Content-Type: application/json; charset=utf-8');

include('../../include/dbcon.php'); // adjust path if necessary

$term = isset($_GET['term']) ? $myDB->real_escape_string(trim($_GET['term'])) : '';

if ($term === '') {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, expence FROM rrsv_expence_head WHERE expence LIKE '%{$term}%' ORDER BY expence LIMIT 20";
$res = $myDB->query($sql);

$out = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = [
            'label'   => $row['expence'],
            'value'   => $row['expence'],
            'item_id' => $row['expence']
        ];
    }
}

echo json_encode($out);
exit;
?>