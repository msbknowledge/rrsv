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
        $sqlsts = "update rrsv_gallery set status='" . $status . "' where id='" . $id . "'";
        $resSts =mysqli_query($myDB, $sqlsts) or die(mysqli_error($myDB));

        if (mysqli_affected_rows($myDB) >= 1) {
            echo '<script language="javascript" type="text/javascript">';
            echo 'window.location.href="manage_gallery.php?retcode=4"';
            echo '</script>';
        }
    }
}

if (isset($_GET['dId']) && !empty($_GET['dId'])) {
   
    $sql = "DELETE FROM rrsv_gallery WHERE id='" . (int) $_GET['dId'] . "'";
    $res = mysqli_query($myDB, $sql);
    if ($res) { ?>
        <script>
            alert('Gallery item deleted successfully!');
            window.location.replace('manage_gallery.php');
        </script>
    <?php }
} ?>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href='add_gallery.php'>
                        <button type="button" class="btn btn-primary btn-rounded btn-fw">
                            <i class="mdi mdi-image-plus"></i> Add Gallery
                        </button>
                    </a>
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div>
                    <!-- Table -->
                    <table id='galleryTable' class='display dataTable'>
                        <thead>
                            <tr>
                                <th>SL.No</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Uploaded On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <?php include('../../include/footer.php'); ?>

    <!-- Datatable JS -->
    <script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#galleryTable').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': 'galleryAjax.php'
                },
                'columns': [
                    { data: 'sl_no' },
                    { data: 'title' },
                    { data: 'category' },
                    { data: 'image' },
                    { data: 'status' },
                    { data: 'uploaded_on' },
                    { data: 'action' }
                ]
            });


        });
        function confirmsearch(id, status) {
            if (confirm("Are you sure to change this Student status?")) {
                window.location.href = "manage_gallery.php?mode=sts&id=" + id + "&status=" + status;
                return true;
            }
        }

    </script>