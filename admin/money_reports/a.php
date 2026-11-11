<?php
session_start();
include '../dbcon.php';

// ---------------------- GET FILTER VALUES ---------------------- //
$fromDate    = $_POST['fromDate'] ?? '';
$endDate     = $_POST['endDate'] ?? '';
$scl_class   = $_POST['scl_class'] ?? '';
$scl_session = $_POST['scl_session'] ?? '';

$_SESSION['fromDate']    = $fromDate;
$_SESSION['endDate']     = $endDate;
$_SESSION['scl_class']   = $scl_class;
$_SESSION['scl_session'] = $scl_session;

include('../header.php');
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <?php include '../top_navbar.php'; ?>
    </header>
    <?php include '../left_navbar.php'; ?>

    <div class="content-wrapper">
        <section class="content">

            <!-- Filter Form -->
            <div class="box box-danger mb-3 p-3">
                <h4>Filter Report</h4>
                <form method="post" action="" id="dateRangeForm">
                    <label for="fromDate">From:</label>
                    <input type="date" name="fromDate" id="fromDate" value="<?= htmlspecialchars($fromDate) ?>" required>

                    <label for="endDate">To:</label>
                    <input type="date" name="endDate" id="endDate" value="<?= htmlspecialchars($endDate) ?>" required>

                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <button type="button" onclick="window.location=''" class="btn btn-secondary btn-sm">Reset</button>
                </form>
            </div>

            <!-- Column Selector -->
            <div class="box box-secondary mb-3 p-3">
                <strong>Select Columns to Display:</strong><br>
                <?php
                $allColumns = [
                    "Sl.No","Reg. No","Roll No","Name","Admission","ReAdmission","Uniform","Shoes","Winter",
                    "Admission Form","Bag","I.card","Diary","Books","Copy","Tution Fee","Total Car Cost (P.A)",
                    "Car Cost Paid (P.M)","Total Due for Car","Total Gross (with Car)","Total Gross (without Car)",
                    "Total Bill","Total Payable","After Billing Advance","After Billing Due","Total Due (with Car)",
                    "Total Due (without Car)","Statement Amount","Statement Due"
                ];
                foreach($allColumns as $index => $col){
                    echo '<label style="margin-right:10px;"><input type="checkbox" class="col-toggle" data-col="'.$index.'" checked> '.$col.'</label>';
                }
                ?>
            </div>

            <!-- Money Report Table -->
            <div class="box box-primary p-3">
                <div class="text-center mb-3">
                    <h4>RASULPUR RAMKRISHNA SARADA VIDYAPITH</h4>
                    <p><strong>Reg. No.: SO196094 | U-DISE Code: 19251610302 | ESTD: 2012 | Baidyadanga, Rasulpur, Memari, Purba Bardhaman, Pin -713151</strong></p>
                    <p><strong>Money Report for: <?= date('d-m-Y', strtotime($fromDate)); ?> to <?= date('d-m-Y', strtotime($endDate)); ?></strong></p>
                </div>

                <table id="moneyReport" class="table table-bordered table-striped text-sm" style="width:100%">
                    <thead>
                        <tr>
                            <?php foreach($allColumns as $col): ?>
                                <th><?= $col ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT a.*, b.scl_date, b.last_advance_due, b.payment_recive
                                FROM rrsv_student_registration AS a
                                INNER JOIN rrsv_scl_studen_fee AS b ON a.id = b.pay_id
                                WHERE b.scl_date BETWEEN '$fromDate' AND '$endDate'
                                GROUP BY a.id";

                        $result = mysqli_query($con, $sql);
                        $sl = 1;

                        while($row = mysqli_fetch_assoc($result)):
                            $totalCarCost = $row['car_cost']*12;
                            $gross_with_car = $row['total_monthly_fee'] + $row['add_total_cost'] + $row['copycost'] + $totalCarCost;
                            $gross_without_car = $row['total_monthly_fee'] + $row['add_total_cost'] + $row['copycost'];
                        ?>
                        <tr>
                            <td><?= $sl++; ?></td>
                            <td><?= $row['scl_reg_no']; ?></td>
                            <td><?= $row['scl_roll_no']; ?></td>
                            <td><?= htmlspecialchars($row['scl_name']); ?></td>
                            <td><?= $row['admission_fee_val']; ?></td>
                            <td><?= $row['re_admission_fee_val']; ?></td>
                            <td><?= $row['uniformval']; ?></td>
                            <td><?= $row['shoessockesval']; ?></td>
                            <td><?= $row['sweaterslaxcap_val']; ?></td>
                            <td><?= $row['admissionval']; ?></td>
                            <td><?= $row['bagval']; ?></td>
                            <td><?= $row['icardval']; ?></td>
                            <td><?= $row['diaryval']; ?></td>
                            <td><?= $row['bookcost']; ?></td>
                            <td><?= $row['copycost']; ?></td>
                            <td><?= $row['total_monthly_fee']; ?></td>
                            <td><?= $totalCarCost; ?></td>
                            <td><?= $row['carfee']; ?></td>
                            <td><?= $totalCarCost - $row['carfee']; ?></td>
                            <td><?= $gross_with_car; ?></td>
                            <td><?= $gross_without_car; ?></td>
                            <td><?= $row['total_monthly_fee'] + $row['add_total_cost']; ?></td>
                            <td><?= $row['payment_recive']; ?></td>
                            <td><?= ($row['val_ad_du']=='Advance')?$row['advance_due']:0; ?></td>
                            <td><?= ($row['val_ad_du']=='Due')?$row['advance_due']:0; ?></td>
                            <td><?= $gross_with_car - $row['payment_recive']; ?></td>
                            <td><?= $gross_without_car - $row['payment_recive']; ?></td>
                            <td><?= $row['amount1']; ?></td>
                            <td><?= $row['amount1'] - $row['payment_recive']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <?php include '../footer.php'; ?>
</div>

<!-- jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<!-- DataTables & Buttons CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- DataTables Initialization -->
<script>
$(document).ready(function(){
    var table = $('#moneyReport').DataTable({
        scrollX: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        buttons: [
            { extend: 'copy', exportOptions: { columns: ':visible' } },
            { extend: 'csv', exportOptions: { columns: ':visible' } },
            { extend: 'excel', exportOptions: { columns: ':visible' } },
            { extend: 'pdf', exportOptions: { columns: ':visible' } },
            { extend: 'print', exportOptions: { columns: ':visible' } },
            'colvis'
        ]
    });

    table.buttons().container().appendTo('#moneyReport_wrapper .col-md-6:eq(0)');

    // Column toggle checkboxes
    $('.col-toggle').on('change', function(){
        var colIndex = $(this).data('col');
        var column = table.column(colIndex);
        column.visible(!column.visible());
    });
});
</script>

</body>
</html>
