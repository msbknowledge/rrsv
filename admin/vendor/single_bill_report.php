<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Vendor Combined Bill Report</title>

<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4; }
.container { max-width:900px; margin:20px auto; background:#fff; padding:20px; box-shadow:0 0 6px #ccc; }
h1 { margin:0; padding:0; font-size:22px; text-align:center; }
.meta { text-align:center; font-size:12px; color:#555; margin-bottom:20px; }

table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; font-size:13px; }
th { background:#f7f7f7; }
.text-right { text-align:right; }

.no-print { margin-top:20px; text-align:right; }
@media print { .no-print { display:none; } }
</style>
</head>
<body>

<div class="container">

<?php
include('../../include/dbcon.php');

$vendor_id = intval($_GET['id'] ?? 0);

$vendor_q = $myDB->query("SELECT * FROM rrsv_vendor WHERE id=$vendor_id");
$vendor = $vendor_q->fetch_assoc();
?>

<h1>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h1>
<div class="meta">
  Reg. No. : SO196094 | U-DISE : 19251610302 | ESTD : 2012<br>
  Baidyadanga, Rasulpur, Memari, Purba Bardhaman - 713151
</div>

<h3>Vendor: <?php echo $vendor['name']; ?></h3>
<p><?php echo nl2br($vendor['description']); ?></p>

<?php
$q = $myDB->query("
    SELECT 
        i.*,
        b.bill_no,
        b.bill_date,
        (SELECT IFNULL(SUM(p.paid_amount),0) FROM rrsv_vendor_bill_payments p WHERE p.bill_id=b.id) AS paid
    FROM rrsv_vendor_bill_items i
    INNER JOIN rrsv_vendor_bills b ON b.id = i.bill_id
    WHERE b.vendor_id = $vendor_id
    ORDER BY b.bill_date ASC
");

$total = 0;
$total_paid = 0;
?>

<table>
<thead>
<tr>
    <th>#</th>
    <th>Bill No</th>
    <th>Bill Date</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Rate</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
<?php
$i=1;
while($row=$q->fetch_assoc()){
    $line = $row['qty'] * $row['rate'];
    $total += $line;
    $total_paid += $row['paid'];

    echo "
    <tr>
        <td>{$i}</td>
        <td>{$row['bill_no']}</td>
        <td>{$row['bill_date']}</td>
        <td>".htmlentities($row['item_name'])."</td>
        <td class='text-right'>".number_format($row['qty'],2)."</td>
        <td class='text-right'>".number_format($row['rate'],2)."</td>
        <td class='text-right'>".number_format($line,2)."</td>
    </tr>
    ";
    $i++;
}

$due = $total - $total_paid;
?>
</tbody>

<tfoot>
<tr>
    <th colspan="6" class="text-right">Total Amount</th>
    <th class="text-right">₹<?php echo number_format($total,2); ?></th>
</tr>

<tr>
    <th colspan="6" class="text-right">Total Paid</th>
    <th class="text-right">₹<?php echo number_format($total_paid,2); ?></th>
</tr>

<tr>
    <th colspan="6" class="text-right">Total Due</th>
    <th class="text-right">₹<?php echo number_format($due,2); ?></th>
</tr>
</tfoot>
</table>

</div>

<div class="no-print">
    <button onclick="window.print()" style="padding:10px 15px;background:#007bff;color:#fff;border:0;border-radius:4px;">Print</button>
</div>

</body>
</html>
