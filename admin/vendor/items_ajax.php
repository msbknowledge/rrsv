<?php
// items_ajax.php
include('../../include/dbcon.php');

$term = mysqli_real_escape_string($myDB, $_GET['term'] ?? '');

$q = mysqli_query($myDB, "
    SELECT item_name, rate, qty 
    FROM rrsv_vendor_bill_items
    WHERE item_name LIKE '%$term%'
    LIMIT 20
");

$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    $data[] = [
        'label' => $row['item_name'],
        'value' => $row['item_name'],
        'rate' => (float)$row['rate'],
        'gst' => (float)$row['qty'],
    ];
}

echo json_encode($data);
?>
