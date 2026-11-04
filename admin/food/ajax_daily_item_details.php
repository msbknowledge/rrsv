<?php
include('../../include/dbcon.php');

$date = $_GET['date'] ?? '';

$data = [
    'purchase' => [],
    'expense'  => []
];

/* PURCHASE DETAILS */
$pq = mysqli_query($myDB,"
    SELECT i.item_name,
           l.qty,
           l.rate,
           (l.qty*l.rate) AS total
    FROM rrsv_food_stock_ledger l
    JOIN rrsv_food_items i ON i.id = l.item_id
    WHERE l.direction='IN'
      AND DATE(l.created_at)='$date'
");

while($r = mysqli_fetch_assoc($pq)){
    $data['purchase'][] = $r;
}

/* EXPENSE DETAILS */
$eq = mysqli_query($myDB,"
    SELECT i.item_name,
           l.qty,
           l.rate,
           (l.qty*l.rate) AS total
    FROM rrsv_food_stock_ledger l
    JOIN rrsv_food_items i ON i.id = l.item_id
    WHERE l.direction='OUT'
      AND DATE(l.created_at)='$date'
");

while($r = mysqli_fetch_assoc($eq)){
    $data['expense'][] = $r;
}

echo json_encode($data);
