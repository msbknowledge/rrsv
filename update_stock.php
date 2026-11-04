<?php
include('include/dbcon.php');
// print_r($_POST);
// die;
$stockId = $_POST['stockId'];
$returnQty = $_POST['returnQty'];
$returnAmount = $_POST['returnAmount'];
$damageQty = $_POST['damageQty'];
$damageAmount = $_POST['damageAmount'];
$damageRemarks = $_POST['damageRemarks'];
$activityStatus = $_POST['activityStatus'];

$query = "UPDATE stock SET
        activity_status = '$activityStatus',
          stock_return = '$returnQty',
          value_after_return = '$returnAmount',
          damage_qty = '$damageQty',
          damage_value = '$damageAmount',
          damage_remarks = '$damageRemarks'
          WHERE id = '$stockId'";

if (mysqli_query($myDB, $query)) {
    echo "Stock updated successfully";
} else {
    echo "Error updating stock: " . mysqli_error($myDB);
}
?>