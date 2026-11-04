<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');


if (empty($_SESSION['alogin'])) {
	header('location:index.php');
}
$page_title = "Student Order Summary";
?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

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
                <div>

				<h2 class="page-title"></h2>
			
				 </div>
            </div>
        </div>

    </div>

    <?php include('../../include/footer.php'); ?>

    <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
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

    

