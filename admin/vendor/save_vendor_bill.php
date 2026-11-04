<?php
session_start();
include('../../include/dbcon.php');

// --------------------
// SAFE POST VALUES
// --------------------
$vendor_id    = $myDB->real_escape_string($_POST['vendor_id'] ?? '');
$bill_no      = $myDB->real_escape_string($_POST['bill_no'] ?? '');
$bill_date    = $myDB->real_escape_string($_POST['bill_date'] ?? date('Y-m-d'));
$total_amount = $myDB->real_escape_string($_POST['total_amount'] ?? '0');
$session      = $myDB->real_escape_string($_POST['session'] ?? '');
$remarks      = $myDB->real_escape_string($_POST['remarks'] ?? '');
$item_name = is_array($_POST['item_name']) ? $_POST['item_name'][0] : '';

// --------------------
// FILE UPLOAD
// --------------------
$uploadDir = "../../expence_image";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$finalImageName = "";
if (!empty($_FILES['vouture_file']['name']) && is_uploaded_file($_FILES['vouture_file']['tmp_name'])) {
    $imageName = basename($_FILES['vouture_file']['name']);
    $finalImageName = time() . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $imageName);
    $filePath = $uploadDir . "/" . $finalImageName;
    if (move_uploaded_file($_FILES['vouture_file']['tmp_name'], $filePath)) {
        chmod($filePath, 0644);
        // optional: create thumbnail here (omitted for brevity)
    } else {
        // upload failed — clear name so DB won't have a bad filename
        $finalImageName = "";
    }
}

// --------------------
// START TRANSACTION
// --------------------
$myDB->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
try {
    // INSERT VENDOR BILL
    $insertBill = "
        INSERT INTO rrsv_vendor_bills
        SET 
            vendor_id = '$vendor_id',
            bill_no = '$bill_no',
            bill_date = '$bill_date',
            session = '$session',
            total_amount = '$total_amount',
            balance_amount = '$total_amount',
            vouture_file = '" . $myDB->real_escape_string($finalImageName) . "',
            item_name = '$item_name',
            remarks = '$remarks'
    ";
    if (!mysqli_query($myDB, $insertBill)) {
        throw new Exception("Bill Insert Error: " . mysqli_error($myDB));
    }
    $bill_id = $myDB->insert_id;

    // INSERT ITEMS (and create an expense + PL record per item)
    if (!empty($_POST['item_name']) && is_array($_POST['item_name'])) {
        
        foreach ($_POST['item_name'] as $i => $item) {
            $qty = $_POST['qty'][$i];
            $rate = $_POST['rate'][$i];
            $amount = $_POST['amount'][$i];
            mysqli_query($myDB, "INSERT INTO rrsv_vendor_bill_items(bill_id,item_name,qty,rate,amount)
            VALUES('$bill_id', '$item', '$qty', '$rate', '$amount')");

            

            // insert expense (one expense per item)
            $expSQL = "
                INSERT INTO rrsv_expence
                SET
                    expence_name = '$item',
                    amount       ='0',
                    d_o_e        = '$bill_date',
                    image        = '" . $myDB->real_escape_string($finalImageName) . "',
                    exp_desc     = '$item'
                   
            ";
            if (!mysqli_query($myDB, $expSQL)) {
                throw new Exception("Expense Insert Error: " . mysqli_error($myDB));
            }
            $expense_id = $myDB->insert_id;

            // update from_no on expense (if you want it)
            $upd = "UPDATE rrsv_expence SET from_no = '$expense_id' WHERE id = $expense_id";
            if (!mysqli_query($myDB, $upd)) {
                throw new Exception("Expense Update from_no Error: " . mysqli_error($myDB));
            }

            // insert PL entry
            // $plSQL = "
            //     INSERT INTO rrsv_scl_pl
            //     SET
            //         pl_date    = '$bill_date',
            //         exp_amount = '0',
            //         bill       = '$expense_id',
            //         purpose    = '$item',
            //         session    = '$session'
            // ";
            // if (!mysqli_query($myDB, $plSQL)) {
            //     throw new Exception("PL Insert Error: " . mysqli_error($myDB));
            // }
        }
    }

    // COMMIT
    $myDB->commit();

    echo "<script>alert('Vendor Bill Saved Successfully!'); window.location='manage_vendor_bill.php';</script>";
    exit;

} catch (Exception $e) {
    $myDB->rollback();
    // Log or show the error — for now show a message and stop
    die("Error: " . $e->getMessage());
}
?>
