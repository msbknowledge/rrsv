<?php
// echo $_SERVER['HTTP_REFERER'];
// die();
include('include/dbcon.php');
include('include/header.php');
?>

<!DOCTYPE html>
<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script>


            function dateDiff(startingDate, endingDate) {
                var startDate = new Date(new Date(startingDate).toISOString().substr(0, 10));
                if (!endingDate) {
                    endingDate = new Date().toISOString().substr(0, 10);    // need date in YYYY-MM-DD format
                }
                var endDate = new Date(endingDate);
                if (startDate > endDate) {
                    var swap = startDate;
                    startDate = endDate;
                    endDate = swap;
                }
                var startYear = startDate.getFullYear();
                var february = (startYear % 4 === 0 && startYear % 100 !== 0) || startYear % 400 === 0 ? 29 : 28;
                var daysInMonth = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

                var yearDiff = endDate.getFullYear() - startYear;
                var monthDiff = endDate.getMonth() - startDate.getMonth();
                if (monthDiff < 0) {
                    yearDiff--;
                    monthDiff += 12;
                }
                var dayDiff = endDate.getDate() - startDate.getDate();
                if (dayDiff < 0) {
                    if (monthDiff > 0) {
                        monthDiff--;
                    } else {
                        yearDiff--;
                        monthDiff = 11;
                    }
                    dayDiff += daysInMonth[startDate.getMonth()];
                }

                return yearDiff + 'Year ' + monthDiff + 'Month ' + dayDiff + 'Day';
            }


            var end = new Date().toISOString().slice(0, 10);

        </script>
        <?php
       $query = mysqli_query($myDB,"
SELECT r.*
FROM rrsv_student_registration r
INNER JOIN (
    SELECT scl_reg_no, MAX(id) AS latest_id
    FROM rrsv_student_registration
    GROUP BY scl_reg_no
) t ON r.id = t.latest_id
WHERE (r.dise_code IS NULL OR r.dise_code='')
AND DATE_ADD(r.scl_dob, INTERVAL 5 YEAR) <= CURDATE()
ORDER BY r.id DESC
LIMIT 10
");
        while($row = mysqli_fetch_assoc($query)){
        ?>

        <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between align-items-center"
            role="alert">
            <div>Reminder!
                <?= $row['scl_name']; ?> (
                <?= $row['scl_reg_no']; ?>) YEAR <?= $row['scl_session']; ?> and <span class="badge badge-pill badge-info "> DOB:
                    <?= $row['scl_dob']; ?>
                </span> needs DISE Code update.
                <span class="agecal" data-dob="<?= $row['scl_dob']; ?>"
                    style="background-color:#f9082e;padding: 3px 15px 3px 15px;color: white;font-weight: bold; border-radius: 5px;"></span>

            </div>
            <div>

                <button class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#updateModal"
                    data-id="<?= $row['id']; ?>" data-name="<?= $row['scl_name']; ?>">
                    Update
                </button>
                <button type="button" class="close badge badge-danger " data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>

        <?php } ?>
<?php
$q = mysqli_query($myDB, "
    SELECT title, description, plan_date
    FROM rrsv_calender_plan
    WHERE reminder_date <= CURDATE()
      AND plan_date >= CURDATE()
      AND status = 1
");

while ($row = mysqli_fetch_assoc($q)) {
?>

        <div class="alert alert-info alert-dismissible fade show">
            <strong>
                <?= $row['title']; ?>
            </strong><br>
            <?= $row['description']; ?><br>
            Plan Date :
            <?= $row['plan_date']; ?>

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>

        <?php } ?>
        <div class="row">

            <div class="col-12 col-xl-6 grid-margin stretch-card">
                <div class="row w-100 flex-grow">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Student Audience Metrics</p>
                                <p class="text-muted">25% more traffic than previous week</p>
                                <div class="row mb-3">
                                    <div class="col-md-7">
                                        <div class="d-flex justify-content-between traffic-status">
                                            <div class="item">
                                                <p class="mb-">Total Student</p>
                                                <h5 class="font-weight-bold mb-0">93,956</h5>
                                                <div class="color-border"></div>
                                            </div </div>
                                        </div>

                                    </div>
                                    <canvas id="audience-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6 grid-margin stretch-card">
                <div class="row w-100 flex-grow">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Student payment collection</p>
                                <div class="row mb-3">
                                    <div class="col-md-7">
                                        <div class="d-flex justify-content-between traffic-status">
                                            <div class="item">
                                                <p class="mb-">Total Income</p>
                                                <h5 class="font-weight-bold mb-0">100</h5>
                                                <div class="color-border"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <canvas id="balance-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="row">
            <div class="col-12 col-xl-12 grid-margin stretch-card">
                <div class="row w-100 flex-grow">
                    <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Student Dise</p>
                       
                        <?php
                    $q = mysqli_query($myDB,"
SELECT r.scl_name,
       r.scl_reg_no,
       r.dise_code,
       r.dise_date
FROM rrsv_student_registration r
INNER JOIN (
    SELECT scl_reg_no, MAX(id) AS latest_id
    FROM rrsv_student_registration
    GROUP BY scl_reg_no
) t ON r.id = t.latest_id
WHERE r.dise_code IS NOT NULL
AND r.dise_code != ''
ORDER BY r.dise_date DESC
");
                    ?>
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Reg No</th>
                                    <th>DISE Code</th>
                                    <th>DISE Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    $i=1;
                    while($rows=mysqli_fetch_assoc($q)){
                    ?>
                                <tr>
                                    <td>
                                        <?= $i++; ?>
                                    </td>
                                    <td>
                                        <?= $rows['scl_name']; ?>
                                    </td>
                                    <td>
                                        <?= $rows['scl_reg_no']; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <?= $rows['dise_code']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= !empty($rows['dise_date']) ? date('d-m-Y',strtotime($rows['dise_date'])) : ''; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>

        <!-- row end -->

        <!-- row end -->

    </div>
    <!-- content-wrapper ends -->
    <!-- partial:./partials/_footer.html -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <div class="modal fade" id="updateModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Update DISE Code</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form method="post" action="update_dise.php">

                    <div class="modal-body">

                        <input type="hidden" name="student_id" id="student_id">

                        <div class="form-group">
                            <label>Student Name</label>
                            <input type="text" id="student_name" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>DISE Code</label>
                            <input type="text" name="dise_code" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:./partials/_footer.html -->
    <script>
        $('#updateModal').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget);

            var id = button.data('id');
            var name = button.data('name');

            var modal = $(this);

            modal.find('#student_id').val(id);
            modal.find('#student_name').val(name);

        });

        document.querySelectorAll('.agecal').forEach(function (el) {
            var dob = el.getAttribute('data-dob');
            el.innerText = dateDiff(dob, end);
        });
      
$(document).ready(function() {
    $('#example1').DataTable({
        pagingType: "simple_numbers",
        "pageLength": 10,
        "lengthMenu": [10,25,50,100]
    });
});
    </script>

<?php
include('include/footer.php');
?>