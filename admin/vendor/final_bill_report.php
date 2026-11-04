<?php
include('../../include/header.php');
include('../../include/dbcon.php');
?>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4; }
.container { max-width:900px; margin:20px auto; background:#fff; padding:20px; box-shadow:0 0 6px #ccc; }
.b {max-width:900px; margin:20px auto;  padding:20px; }
h1 { margin:0; padding:0; font-size:22px; text-align:center; }
.meta { text-align:center; font-size:12px; color:#555; margin-bottom:20px; }
.company { text-align:center; margin-bottom:20px; }
.company h1 { margin:0; font-size:24px; }
.company .meta { font-size:12px; color:#444; }

.flex { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.box { border:1px solid #ddd; padding:12px; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; font-size:13px; }
th { background:#f7f7f7; }
.text-right { text-align:right; }

</style>
<style>
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

<div class="main-panel">
  <div class="content-wrapper">

    <div class="card">
      <div class="card-body">
        <h4 class="card-title">
         
        </h4>
      </div>
    </div>
    <div class="col-12 grid-margin">
        <div class="card">
          
            <div class="container">
    
    <?php
    include('../../include/dbcon.php');
    
    $vendor_id = intval($_GET['id'] ?? 0);
    
    $vendor_q = $myDB->query("SELECT * FROM rrsv_vendor WHERE id=$vendor_id");
    $vendor = $vendor_q->fetch_assoc();
    ?>
    
    <div class="company">
    <h1>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h1>
    <div class="flex mt-4">
        <div style="width:20%;">
            <a class="navbar-brand brand-logo" href="#">
                <img src="/rrsv/libray/images/logo.jpeg" class="img rounded-md" height="80" width="80" alt="image">
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
    <div class="flex box">
    <div style="width:60%;">
    <h3>Name: <?php echo $vendor['name']; ?></h3>
    <p><?php echo nl2br($vendor['mobile']); ?></p>
    <p><?php echo nl2br($vendor['address']); ?></p>
     </div>
     </div>
    <?php
    $bill_q = $myDB->query("
        SELECT 
            b.id AS bill_id,
            b.bill_no,
            b.bill_date,
            (SELECT IFNULL(SUM(p.paid_amount),0) FROM rrsv_vendor_bill_payments p WHERE p.bill_id=b.id) AS paid
        FROM rrsv_vendor_bills b
        WHERE b.vendor_id = $vendor_id
        ORDER BY b.bill_date ASC
    ");
    
    $grand_total = 0;
    $grand_paid = 0;
    ?>
    
    <table>
    <thead>
    <tr>
        <th>#</th>
        <th>Bill No</th>
        <th>Bill Date</th>
        <th>Item</th>
        <th>Qty</th>
        <!--<th>Rate</th>-->
        <th>Item Total</th>
        <th>Bill Total</th>
        <th>Bill Paid</th>
        <th>Bill Due</th>
    </tr>
    </thead>
    
    <tbody>
    
    <?php
    $sr = 1;
    
    while($bill = $bill_q->fetch_assoc())
    {
        // Fetch items
        $items_q = $myDB->query("
            SELECT * FROM rrsv_vendor_bill_items 
            WHERE bill_id = {$bill['bill_id']}
        ");
    
        $items = [];
        $bill_total = 0;
    
        while($it = $items_q->fetch_assoc()){
            $it['line_total'] =$it['amount']; //$it['qty'] * $it['rate'];
            $bill_total += $it['line_total'];
            $items[] = $it;
        }
    
        $paid = $bill['paid'];
        $due  = $bill_total - $paid;
    
        $rowspan = count($items);
    
        // Grand totals
        $grand_total += $bill_total;
        $grand_paid  += $paid;
    
        $first = true;
    
        foreach($items as $it){
    ?>
    
    <tr>
        <td><?php echo $sr++; ?></td>
    
        <?php if ($first) { ?>
            <td rowspan="<?php echo $rowspan; ?>"><?php echo $bill['bill_no']; ?></td>
            <td rowspan="<?php echo $rowspan; ?>"><?php echo $bill['bill_date']; ?></td>
        <?php } ?>
    
        <td><?php echo htmlentities($it['item_name']); ?></td>
        <td class="text-right"><?php echo number_format($it['qty'],2); ?></td>
        <!--<td class="text-right"><?php echo number_format($it['rate'],2); ?></td>-->
        <td class="text-right"><?php echo number_format($it['line_total'],2); ?></td>
    
        <?php if ($first) { ?>
            <td rowspan="<?php echo $rowspan; ?>" class="text-right">₹<?php echo number_format($bill_total,2); ?></td>
            <td rowspan="<?php echo $rowspan; ?>" class="text-right">₹<?php echo number_format($paid,2); ?></td>
            <td rowspan="<?php echo $rowspan; ?>" class="text-right">₹<?php echo number_format($due,2); ?></td>
        <?php } ?>
    </tr>
    
    <?php
            $first = false;
        }
    }
    ?>
    
    </tbody>
    
    <tfoot>
    <?php 
    $grand_due = $grand_total - $grand_paid; 
    ?>
    
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <th class="text-right">₹<?php echo number_format($grand_total,2); ?></th>
        <th class="text-right">₹<?php echo number_format($grand_paid,2); ?></th>
        <th class="text-right">₹<?php echo number_format($grand_due,2); ?></th>
    </tr>
    
    </tfoot>
    </table>
    
    </div>
    
    
            <div class="no-print b">
                <button onclick="window.print()" style="padding:10px 15px;background:#007bff;color:#fff;border:0;border-radius:4px; margin:20px auto;">Print</button>
            </div>
        </div>
        </div>
    </div>
</div>
<?php include('../../include/footer.php'); ?>
