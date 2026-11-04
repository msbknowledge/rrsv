<?php
// print_bill.php
include('../../include/header.php');
include('../../include/dbcon.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  echo "Invalid bill id.";
  exit;
}

// Fetch bill + vendor
$bill_q = mysqli_query($myDB, "
  SELECT b.*, 
         v.name AS vendor_name,
         v.address AS vendor_address,
         v.mobile AS vendor_phone,
         v.id AS vendor_gst
  FROM rrsv_vendor_bills b
  LEFT JOIN rrsv_vendor v ON v.id = b.vendor_id
  WHERE b.id = '$id'
");
$bill = mysqli_fetch_assoc($bill_q);

if (!$bill) {
  echo "Bill not found.";
  exit;
}

// Fetch items  ✔ FIXED TABLE NAME
$items_q = mysqli_query($myDB, "
    SELECT * FROM rrsv_vendor_bill_items 
    WHERE bill_id = {$id}
");

$date_print = date('F j, Y');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice - Bill #<?php echo htmlentities($bill['bill_no']); ?></title>

<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; color:#222; }
.container { max-width:900px; margin:20px auto; padding:20px; }
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

@media print {
    body * {visibility: hidden !important;}
    .sidebar, .left-sidebar, .topbar, header, nav, .navbar, .page-header, .main-header { display: none !important; visibility: hidden !important; }
    .container,.container * {visibility: visible !important;}
    .container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        background: #fff !important;
    }


    
}
</style>
</head>

<body>
<div class="container">

  <div class="company">
    <h1>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h1>
    <div class="flex box mt-4">
        <div style="width:20%;">
            <a class="navbar-brand brand-logo" href="#">
                <img src="libray/images/logo.jpeg" class="img rounded-md" height="80" width="80" alt="image">
            </a>
        </div>
        <div style="width:60%;">
            <div class="meta text-left">
              Reg. No. : SO196094 | U-DISE : 19251610302 | ESTD : 2012<br>
              Baidyadanga, Rasulpur, Memari, Purba Bardhaman - 713151<br>
              Phone: 0000000000 | Email: example@mail.com
            </div>
        </div>
    </div>
  
  </div>
  <!-- BILL HEADER -->
  <div class="flex box">
    <div style="width:60%;">
      <strong>Bill To:</strong><br>
      <?php echo nl2br(htmlentities($bill['vendor_name'])); ?><br>
      <?php echo nl2br(htmlentities($bill['vendor_address'])); ?><br>
      <?php if ($bill['vendor_phone']) echo "Phone: ".htmlentities($bill['vendor_phone'])."<br>"; ?>
      <?php if ($bill['vendor_gst']) echo "GSTIN: ".htmlentities($bill['vendor_gst'])."<br>"; ?>
    </div>

    <div style="width:35%;">
      <table class="summary-table">
        <tr><td><strong>Invoice #</strong></td><td class="text-left"><?php echo $bill['bill_no']; ?></td></tr>
        <tr><td><strong>Date</strong></td><td class="text-left"><?php echo $bill['bill_date']; ?></td></tr>
        <tr><td><strong>Printed</strong></td><td class="text-left"><?php echo $date_print; ?></td></tr>
      </table>
    </div>
  </div>

  <!-- ITEMS -->
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Item Description</th>
        <th>Qty</th>
        <!--<th>Rate</th>-->
        <!--<th>GST %</th>-->
        <!--<th>Taxable</th>-->
        <!--<th>CGST</th>-->
        <!--<th>SGST</th>-->
        <th>Total</th>
      </tr>
    </thead>

    <tbody>
    <?php
      $i = 1;
      $subtotal = 0;
      $total_cgst = 0;
      $total_sgst = 0;

      while ($it = mysqli_fetch_assoc($items_q)) {

        $qty  = (float)$it['qty'];
        $rate = (float)$it['rate'];
        $gst_rate = (float)$it['gst_rate'];

        $taxable = (float)$it['amount'];//$qty * $rate;

        $cgst = ($gst_rate/2 / 100) * $taxable;
        $sgst = ($gst_rate/2 / 100) * $taxable;
        $line_total = $taxable + $cgst + $sgst;

        $subtotal += $taxable;
        $total_cgst += $cgst;
        $total_sgst += $sgst;

        echo "
          <tr>
            <td>{$i}</td>
            <td>".htmlentities($it['item_name'])."</td>
            <td class='text-right'>".number_format($qty,2)."</td>
            <td class='text-right'>".number_format($line_total,2)."</td>
          </tr>
        ";
        $i++;
      }

      $grand_total = $subtotal + $total_cgst + $total_sgst;
    ?>
    </tbody>
<!--  <td class='text-right'>".number_format($gst_rate,2)."</td>
<td class='text-right'>".number_format($cgst,2)."</td>
            <td class='text-right'>".number_format($sgst,2)."</td>-->
    <tfoot>
      <tr>
        <th colspan="3" class="text-right">Subtotal</th>
        <!--<th class="text-right">₹<?php echo number_format($subtotal,2); ?></th>-->
        <!--<th class="text-right">₹<?php echo number_format($total_cgst,2); ?></th>-->
        <!--<th class="text-right">₹<?php echo number_format($total_sgst,2); ?></th>-->
        <th class="text-right">₹<?php echo number_format($grand_total,2); ?></th>
      </tr>
      <tr>
        <th colspan="3" class="text-right">Amount Paid</th>
        <th class="text-right">₹<?php echo number_format($bill['paid_amount'],2); ?></th>
      </tr>
      <tr>
        <th colspan="3" class="text-right">Balance Due</th>
        <th class="text-right">₹<?php echo number_format($bill['balance_amount'],2); ?></th>
      </tr>
    </tfoot>
  </table>

  <br>
  <strong>Remarks:</strong><br>
  <?php echo nl2br(htmlentities($bill['remarks'])); ?>

  <div class="no-print b">
                <button onclick="window.print()" style="padding:10px 15px;background:#007bff;color:#fff;border:0;border-radius:4px; margin:20px auto;">Print</button>
            </div>

</div>
</body>
</html>
<script>function printSection() {
    var printContent = document.querySelector('.container').innerHTML;
    var win = window.open('', '', 'width=900,height=650');
    win.document.write('<html><head><title>Print</title>');
    win.document.write('</head><body>');
    win.document.write(printContent);
    win.document.write('</body></html>');
    win.print();
    win.close();
}</script>
