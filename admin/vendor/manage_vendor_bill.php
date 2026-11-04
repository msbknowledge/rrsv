<?php 
include('../../include/header.php'); 
include('../../include/dbcon.php'); 
?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
<div class="content-wrapper">

<a href="add_vendor_bill.php" class="btn btn-primary">+ Add Bill</a>
<br><br>

<table id="billTable" class="display table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Vendor</th>
            <th>Bill No</th>
            <th>Date</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $q = mysqli_query($myDB, "
            SELECT b.*, v.name AS vendor 
            FROM rrsv_vendor_bills b
            LEFT JOIN rrsv_vendor v ON v.id = b.vendor_id
            ORDER BY b.id DESC
        ");

        while ($row = mysqli_fetch_assoc($q)) {
            echo "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['vendor']}</td>
                <td>{$row['bill_no']}</td>
                <td>{$row['bill_date']}</td>
                <td>₹{$row['total_amount']}</td>
                <td>₹{$row['paid_amount']}</td>
                <td>₹{$row['balance_amount']}</td>
                <td>
                <a href='print_bill.php?id={$row['id']}' class='btn btn-info btn-sm' target='_blank'>Print</a>
                <a href='return_bill.php?id={$row['id']}' class='btn btn-warning btn-sm'>Return</a>
                <button class='btn btn-success btn-sm' onclick='openPaymentModal({$row['id']})'>Pay</button>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>
<!--<a href='edit_vendor_bill.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>-->
</div>
</div>

<?php include('../../include/footer.php'); ?>

<!-- PAYMENT MODAL -->
<div class="modal fade" id="paymentModal">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="save_payment.php">

    <div class="modal-header">
        <h4>Add Payment</h4>
    </div>

    <div class="modal-body">
<div class="col-md-12 form-group">
        <input type="hidden" name="bill_id" id="bill_id">

        <label>Payment Date</label>
        <input type="date" name="pay_date" id="pay_date" class="form-control" required>
  </div>
  <div class="col-md-12 form-group">
        <label>Paid Amount</label>
        <input type="number" step="0.01" name="paid_amount" class="form-control" required>
  </div>
  <div class="col-md-12 form-group">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control"></textarea>
  </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" onclick="$('#paymentModal').modal('hide')">
            Close
        </button>
    </div>

</form>

</div></div>
</div>

<!-- Datatable JS -->
<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>

<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
<script src="<?php echo BASE_URL; ?>libray/DataTables/dataTables.buttons.min.js"></script>
<script src="<?php echo BASE_URL; ?>libray/DataTables/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
    $("#billTable").DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "ordering": true,
        "searching": true,
        "order": [[3, "desc"]]
    });
    
    // $('#billTable').DataTable({
    //     dom: 'Bfrtip',
    //     buttons: [ 'print' ],
    //     paging: true,
    //     ordering: true
    // });

});
function openPaymentModal(id) {
    $('#bill_id').val(id);
    $('#paymentModal').modal('show');
}

</script>
<script>
// Set today's date and disable backdates
document.addEventListener('DOMContentLoaded', function () {
    let today = new Date().toISOString().split('T')[0];
    let payDate = document.getElementById('pay_date');

    payDate.value = today;  // auto-select today
    payDate.setAttribute('min', today); // disable backdate
});
</script>
