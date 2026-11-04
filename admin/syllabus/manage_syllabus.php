<?php
include('../../include/header.php');
//include('../../include/fuction.php');
include('../../include/dbcon.php');
if (isset($_GET['dId']) && !empty($_GET['dId'])) {
    $sql = "Delete from rrsv_syllabus where id='" . (int) $_GET['dId'] . "'";
    $res = mysqli_query($myDB, $sql);
    if ($res) { ?>

        <script>;
            alert('Class Information Delete successfully!');
            window.location.replace('manage_syllabus.php?');
        </script>;
    <?php } ?>



<?php } ?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>



<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> <a href='add_syllabus.php'><button type="button"
                            class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add
                            Syllabus</button></a></h4>
            </div>
        </div>


        <div class="col-12 grid-margin">
            <div class="card">
                <div>
                    <!-- Table -->
                    <table id='syllabusTable' class='display dataTable'>
                        <thead>
                            <form name="frmsearch" method="post" action="manage_bill.php"></form>
                            <tr>
                                <td class="text" colspan="12" valign="top">
                                    <!-- Class -->
                                    <select name="scl_class" class="form-control d-inline-block" id="scl_class"
                                        style="width:240px;">
                                        <?php
                                        $sql = "SELECT * FROM rrsv_class ORDER BY id";
                                        $res = mysqli_query($myDB, $sql);
                                        while ($obj = mysqli_fetch_assoc($res)) {
                                            $selected = (isset($rows['class_name']) && trim($rows['class_name']) == $obj['class_name']) ? "selected" : "";
                                            echo "<option value='{$obj['class_name']}' $selected>{$obj['class_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <!-- Session -->
                                    <select name="scl_session" class="form-control d-inline-block" id="scl_session"
                                        style="width:240px;">
                                        <?php
                                        for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                            $selected = (isset($rows['scl_session']) && $rows['scl_session'] == $i) ? "selected" : "";
                                            echo "<option value='$i' $selected>$i</option>";
                                        }
                                        ?>
                                    </select>
                                    <!-- Unit -->
                                    <select name="unit" class="form-control d-inline-block" id="unit" style="width:240px;">
                                        <?php
                                        $selectedUnit = $_POST['unit'] ?? 1; // default to 1st Unit
                                        for ($u = 1; $u <= 3; $u++) {
                                            $unitLabel = ($u == 1) ? '1st Unit' :
                                                (($u == 2) ? '2nd Unit' :
                                                    (($u == 3) ? '3rd Unit' : $u . 'th Unit'));
                                            $sel = ($selectedUnit == $u) ? 'selected' : '';
                                            echo "<option value='$u' $sel>$unitLabel</option>";
                                        }
                                        ?>
                                    </select>



                                    <div class="butt" style="margin-top: -46px;margin-left: 796px;">
                                        <button type="submit" value="" class="btn btn-primary button"
                                            valign="center">Search</button>
                                        <button type="submit" value="" name="Reset" class="btn btn-primary buttonr"
                                            valign="center">Refresh</button>
                                    </div>
                                </td>
                            </tr>
                            </form>
                            <tr>
                                <th>SL.No</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Unit</th>
                                <th>Session</th>
                                <!-- <th>File</th> -->
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>


    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Syllabus Details</h5>
                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>




    <?php
    include('../../include/footer.php');
    ?>
    <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
    <script>


        $(document).ready(function () {
            var table = $('#syllabusTable').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '<?php echo BASE_URL; ?>admin/syllabus/syllabus.php',
                    'data': function (d) {
                        d.scl_class = $('#scl_class').val();
                        d.scl_session = $('#scl_session').val();
                        d.unit = $('#unit').val();
                    }
                },
                'columns': [
                    { data: 'sl_no' },
                    { data: 'class_name' },
                    { data: 'subject_name' },
                    { data: 'unit' },
                    { data: 'session' },
                    // { data: 'syllabus_file' },
                    { data: 'action' }
                ]
            });

            // Trigger search on filter change
            $('#scl_class, #scl_session,#unit').change(function () {
                table.draw();
            });

            // Refresh button reset
            $('.buttonr').click(function (e) {
                e.preventDefault();
                $('#scl_class').val('');
                $('#scl_session').val('');
                 $('#unit').val('');
                table.draw();
            });
        });



        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/syllabus/view_syllabus.php',
                type: 'GET',
                data: { action: 'view', id: id }, // include action parameter
                dataType: 'json', // expect JSON
                success: function (res) {
                    if (res.status === 1) {
                        $('#viewModal .modal-body').html(res.html);
                        $('#viewModal').modal('show');
                    } else {
                        alert(res.msg);
                    }
                },
                error: function () {
                    alert('Error fetching syllabus details.');
                }
            });
        });



    </script>