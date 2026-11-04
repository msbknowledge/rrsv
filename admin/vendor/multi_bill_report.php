<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Vendor Bill Report</title>

<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; color:#222; background:#f8f8f8; }
.container { max-width:900px; margin:20px auto; padding:20px; background:#fff; box-shadow:0 0 5px #ccc; }
.company { text-align:center; margin-bottom:20px; }
.company h1 { margin:0; font-size:24px; }
.company .meta { font-size:12px; color:#444; }

.flex { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.box { border:1px solid #ddd; padding:12px; }

table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border:1px solid #e5e5e5; padding:8px; font-size:13px; }
th { background:#f7f7f7; }
.text-right { text-align:right; }

.summary-table td { border:0; padding:6px; }
.bill-box { margin-bottom:50px; padding-bottom:20px; border-bottom:4px solid #666; }

.no-print { margin-top:20px; text-align:right; }
@media print { .no-print { display:none; } }
</style>
</head>
<body>

<?php

include('../../include/dbcon.php');

$vendor_id = intval($_GET['id'] ?? 0);

$q_bills = $myDB->query("
    SELECT 
        b.*,
        v.name AS vendor_name,
        v.description AS vendor_address,

        (SELECT IFNULL(SUM(p.paid_amount),0) 
         FROM rrsv_vendor_bill_payments p 
         WHERE p.bill_id = b.id) AS total_paid

    FROM rrsv_vendor_bills b
    INNER JOIN rrsv_vendor v ON v.id = b.vendor_id
    WHERE b.vendor_id = $vendor_id
    ORDER BY b.bill_date DESC
");

$date_print = date("d-m-Y h:i A");

while($bill = $q_bills->fetch_assoc()) {

?>

<div class="container bill-box">

  <div class="company">
    <h1>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h1>
    <div class="meta">
      Reg. No. : SO196094 | U-DISE : 19251610302 | ESTD : 2012<br>
      Baidyadanga, Rasulpur, Memari, Purba Bardhaman - 713151<br>
      Phone: 0000000000 | Email: example@mail.com
    </div>
  </div>

  <div class="flex">
    <div class="box" style="width:60%;">
      <strong>Vendor:</strong><br>
      <?php echo nl2br(htmlentities($bill['vendor_name'])); ?><br>
      <?php echo nl2br(htmlentities($bill['vendor_address'])); ?>
    </div>

    <div style="width:35%;">
      <table class="summary-table">
        <tr><td><strong>Invoice #</strong></td><td class="text-right"><?php echo $bill['bill_no']; ?></td></tr>
        <tr><td><strong>Date</strong></td><td class="text-right"><?php echo $bill['bill_date']; ?></td></tr>
        <tr><td><strong>Printed</strong></td><td class="text-right"><?php echo $date_print; ?></td></tr>
      </table>
    </div>
  </div>

  <?php
    $items_q = $myDB->query("SELECT * FROM rrsv_vendor_bill_items WHERE bill_id = {$bill['id']}");
  ?>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Item Description</th>
        <th>Qty</th>
        <th>Rate</th>
        <th>Total</th>
      </tr>
    </thead>

    <tbody>
    <?php
      $i=1;
      $subtotal=0;

      while($it = $items_q->fetch_assoc()) {
        $line_total = $it['qty'] * $it['rate'];
        $subtotal += $line_total;

        echo "
          <tr>
            <td>{$i}</td>
            <td>".htmlentities($it['item_name'])."</td>
            <td class='text-right'>".number_format($it['qty'],2)."</td>
            <td class='text-right'>".number_format($it['rate'],2)."</td>
            <td class='text-right'>".number_format($line_total,2)."</td>
          </tr>
        ";
        $i++;
      }

      $paid = $bill['total_paid'];
      $due = $subtotal - $paid;
    ?>
    </tbody>

    <tfoot>
      <tr>
        <th colspan="4" class="text-right">Subtotal</th>
        <th class="text-right">₹<?php echo number_format($subtotal,2); ?></th>
      </tr>

      <tr>
        <th colspan="4" class="text-right">Amount Paid</th>
        <th class="text-right">₹<?php echo number_format($paid,2); ?></th>
      </tr>

      <tr>
        <th colspan="4" class="text-right">Balance Due</th>
        <th class="text-right">₹<?php echo number_format($due,2); ?></th>
      </tr>
    </tfoot>
  </table>

  <br>
  <strong>Remarks:</strong><br>
  <?php echo nl2br(htmlentities($bill['remarks'])); ?>

</div>

<?php } ?>

<div class="no-print">
  <button onclick="window.print()" style="padding:8px 14px;background:#1a73e8;color:#fff;border:0;border-radius:4px;">
    Print All Bills
  </button>
</div>

</body>
</html>
