<?php
include('../../include/dbcon.php');

$date = $_GET['date'];

$sql = "
SELECT 
    i.item_name,
    l.qty,
    l.rate,
    (l.qty * l.rate) AS total
FROM rrsv_food_stock_ledger l
JOIN rrsv_food_items i ON i.id = l.item_id
WHERE l.direction = 'IN'
AND DATE(l.created_at) = '$date'
";

$result = mysqli_query($myDB, $sql);
?>

<table class="table table-sm table-bordered mt-2">
    <thead class="bg-light">
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($r = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td>*</td>
            <td><?= $r['item_name'] ?></td>
            <td><?= number_format($r['qty'],2) ?></td>
            <td>₹ <?= number_format($r['rate'],2) ?></td>
            <td>₹ <?= number_format($r['total'],2) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
