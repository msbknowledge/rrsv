<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');
date_default_timezone_set('Asia/Kolkata');

if (empty($_SESSION['alogin'])) {
	header('location:index.php');
}
$page_title = "Student Order Summary";
?>



<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   Student-wise Order Summary
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin ">
            <div class="card table-responsive">
                <div class="card-body">

				<h2 class="page-title"></h2>
				<table id="studentTable" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Student Name</th>
							<th>Email</th>
							<th>Balance</th>
							<th>Total Orders</th>
							<th>Total Amount (₹)</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$cnt = 1;

						$sql = "
                   SELECT 
    u.id,
    u.scl_reg_no,
    u.scl_name,
    w.balance,

    COUNT(DISTINCT o.id) AS total_orders,

    COALESCE(SUM(
        CASE 
            WHEN i.status = 'Confirmed' 
            THEN (i.price * i.quantity) 
            ELSE 0 
        END
    ), 0) AS total_amount

FROM rrsv_student_registration u

INNER JOIN (
    SELECT MAX(id) AS id
    FROM rrsv_student_registration
    GROUP BY scl_reg_no
) latest ON latest.id = u.id

LEFT JOIN rrsv_food_orders o 
    ON o.student_id = u.scl_reg_no

LEFT JOIN rrsv_food_order_items i 
    ON i.order_id = o.id
    AND i.status = 'Confirmed'

LEFT JOIN rrsv_food_wallet w 
    ON w.student_id = u.scl_reg_no

GROUP BY u.scl_name, u.scl_reg_no
ORDER BY total_orders ASC;

                ";

						$query = mysqli_query($myDB, $sql);

						while ($row = mysqli_fetch_assoc($query)) {
							?>
							<tr>
								<td><?= $cnt++; ?></td>
								<td><?= htmlspecialchars($row['scl_reg_no']); ?></td>
								<td><?= htmlspecialchars($row['scl_name']); ?></td>
									<td class="text-center"><?= $row['balance']; ?></td>
								<td class="text-center"><?= $row['total_orders']; ?></td>
								<td class="text-right">
									<?= number_format($row['total_amount'], 2); ?>
								</td>
								<td class="text-center">
									<a href="student_manage.php?student_id=<?= $row['scl_reg_no']; ?>" class="btn btn-xs btn-info">
										<i class="fa fa-eye"></i> View
									</a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				
				 </div>
            </div>
        </div>

    </div>
<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
    <?php include('../../include/footer.php'); ?>

    
    <script>
$(document).ready(function () {
    $('#studentTable').DataTable({
        paging: true,       // pagination
        searching: true,    // search box
        ordering: true,     // column sorting
        info: true,         // "Showing X of Y"
        lengthChange: true, // page length dropdown
        pageLength: 10,     // default rows per page
        order: [[0, 'desc']] // sort by ID desc
    });
});
</script>

    

