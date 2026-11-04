<?php
// search_expense.php
header('Content-Type: application/json; charset=utf-8');

include('../../include/dbcon.php'); // adjust path if necessary

$term = isset($_GET['term']) ? $myDB->real_escape_string(trim($_GET['term'])) : '';

if ($term === '') {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, item_name,rate FROM rrsv_items WHERE item_name LIKE '%{$term}%' ORDER BY item_name LIMIT 20";
$res = $myDB->query($sql);

$out = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = [
            'label'   => $row['item_name'].'-(Rs '.$row['rate'].')',
            'value'   => $row['item_name'],
            'rate' => $row['rate']
        ];
    }
}

echo json_encode($out);
exit;
?>