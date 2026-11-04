<?php
include('../../include/dbcon.php');

$date = $_GET['date'];

$sql = "
SELECT 
    mp.package_name,
    mp.meal_type,
    foi.quantity,
    foi.price,
    (foi.quantity * foi.price) AS total
FROM rrsv_food_order_items foi
JOIN rrsv_menu_package mp ON mp.id = foi.food_id
WHERE foi.status = 'Confirmed'
AND foi.order_date = '$date'
";

$result = mysqli_query($myDB, $sql);
?>

<table class="table table-sm table-bordered mt-2">
    <thead class="bg-light">
        <tr>
            <th>Package</th>
            <th>Meal Type</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($r = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $r['package_name'] ?></td>
            <td><?= $r['meal_type'] ?></td>
            <td><?= number_format($r['quantity'],2) ?></td>
            <td>₹ <?= number_format($r['price'],2) ?></td>
            <td>₹ <?= number_format($r['total'],2) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
