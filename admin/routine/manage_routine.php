<?php
error_reporting(1);
include('../../include/header.php');
include('../../include/dbcon.php');
if (isset($_GET['dId']) && !empty($_GET['dId'])) {
    $dId = (int) $_GET['dId'];
    $sql = "DELETE FROM rrsv_routine WHERE id='$dId'";
    $res = mysqli_query($myDB, $sql);
    if ($res) { ?>
        <script>
            alert('Routine deleted successfully!');
            window.location.replace('manage_routine.php');
        </script>
    <?php }
}
?>
<link href='<?= BASE_URL ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<script src="<?= BASE_URL ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href='add_routine.php'>
                        <button type="button" class="btn btn-primary btn-rounded btn-fw">
                            <i class="mdi mdi-comment-plus-outline"></i> Add Routine
                        </button>
                    </a>
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div>
                    <table id='routineTable' class='display dataTable'>
                        <thead>
                            <form name="frmsearch" method="post" action="manage_bill.php"></form>
                            <tr>
                                <td class="text" align="center" colspan="12" valign="top">
                                    <!-- Class -->
                                    <select name="scl_class" class="form-control d-inline-block" id="scl_class"
                                        style="width:269px; margin-right:10px;">
                                        <option value="">-Select a Class-</option>
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
                                        style="width:240px; margin-right:10px;">
                                        <option value="">-Select a Session-</option>
                                        <?php
                                        for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                            $selected = (isset($rows['scl_session']) && $rows['scl_session'] == $i) ? "selected" : "";
                                            echo "<option value='$i' $selected>$i</option>";
                                        }
                                        ?>
                                    </select>




                                    <div class="butt" style="margin-top: -46px;margin-left: 796px;">
                                        <button type="submit" value="" class="btn btn-primary button"
                                            valign="center">Search</button>
                                        <button type="submit" value="" name="Reset" class="btn btn-primary buttonr"
                                            valign="center">Refresh</button>
                                    </div>
                            </tr>
                            </form>
                            <tr>
                                <th>SL.No</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th>Day</th>
                                <th>Teacher</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../include/footer.php'); ?>

<script src="<?= BASE_URL ?>libray/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#routineTable').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': 'routineAjax.php',
                'data': function (d) {
                    d.scl_class = $('#scl_class').val();
                    d.scl_session = $('#scl_session').val();
                }
            },
            'columns': [
                { data: 'sl_no' },
                { data: 'class_name' },
                { data: 'section_name' },
                { data: 'subject_name' },
                { data: 'day_of_week' },
                { data: 'teacher_name' },
                { data: 'start_time' },
                { data: 'end_time' },
                { data: 'action' }
            ]
        });

        $('#scl_class, #scl_session').change(function () {
            table.draw();
        });

        $('.buttonr').click(function (e) {
            e.preventDefault();
            $('#scl_class').val('');
            $('#scl_session').val('');
            table.draw();
        });
    });
</script>