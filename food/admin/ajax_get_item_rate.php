<?php
include('includes/config.php');

$item_id = (int)$_GET['item_id'];

/* Get latest rate from stock or purchase */
$q = mysqli_query($con,"
    SELECT avg_price ,unit
    FROM rrsv_food_stock
    WHERE item_id='$item_id'
");

if(mysqli_num_rows($q)){
    $row = mysqli_fetch_assoc($q);
    echo json_encode([
        'rate' => (float)$row['avg_price'],
        'unit' => $row['unit']
    ]);
} else {
    echo json_encode([
        'rate' => 0,
        'unit' => ''
    ]);
}
