<?php
include('../../include/header.php');
include('../../include/dbcon.php');
$id = 0;
$status = "";
if (isset($_GET['id'])) {
    $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
}
if (isset($_GET['mode'])) {
    $mode = $myDB->escape_string($_GET['mode']);
}

if (isset($_GET['status'])) {
    $status = $myDB->escape_string($_GET['status']);
}
if ($id > 0) {
    if ($mode == 'sts') {
        if (trim($status) == 'Active')
            $status = 'Inactive';
        else
            $status = 'Active';
        $current = date('Y-m-d');
        $sqlsts = "update rrsv_notice set status='" . $status . "' where id='" . $id . "'";
        $resSts =mysqli_query($myDB, $sqlsts) or die(mysqli_error($myDB));

        if (mysqli_affected_rows($myDB) >= 1) {
            echo '<script language="javascript" type="text/javascript">';
            echo 'window.location.href="manage_notice.php?retcode=4"';
            echo '</script>';
        }
    }
}


if (isset($_GET['dId']) && !empty($_GET['dId'])) {

    $id = (int) $_GET['dId'];
    $sql = "DELETE FROM rrsv_notice WHERE id='$id'";
    $res = mysqli_query($myDB, $sql);
    if ($res) {
        echo "<script>
            alert('Notice deleted successfully!');
            window.location.replace('manage_notice.php');
        </script>";
    }
}
?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href='add_notice.php'>
                        <button type="button" class="btn btn-primary btn-rounded btn-fw">
                            <i class="mdi mdi-comment-plus-outline"></i> Add Notice
                        </button>
                    </a>
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div>
                    <table id='noticeTable' class='display dataTable'>
                        <thead>
                            <tr>
                                <th>SL.No</th>
                                <th>Title</th>
                                <th>Description</th>
                                <!-- <th>File</th> -->
                                <th>Date</th>
                                <th>Status</th>
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
<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#noticeTable').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': 'noticeAjax.php'
            },
            'columns': [
                { data: 'sl_no' },
                { data: 'title' },
                { data: 'description' },
                // { data: 'notice_file' },
                { data: 'notice_date' },
                { data: 'status' },
                { data: 'action' }
            ]
        });
    });
    function confirmsearch(id, status) {
            if (confirm("Are you sure to change this Student status?")) {
                window.location.href = "manage_notice.php?mode=sts&id=" + id + "&status=" + status;
                return true;
            }
        }
</script>