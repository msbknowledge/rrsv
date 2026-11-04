<?php
session_start();
include('../../include/header.php');
include('../../include/dbcon.php');


/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    mysqli_query($myDB, "DELETE FROM rrsv_menu_package_items WHERE package_id='$id'");
    mysqli_query($myDB, "DELETE FROM rrsv_menu_package WHERE id='$id'");

    $msg = "Menu package deleted successfully";
}
?>


<div class="main-panel">
        <div class="content-wrapper">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                         <a href="menu-package-add.php" class="btn btn-success mb-3">
                    ➕ Add Menu Package
                </a>
                    </h4>
                </div>
            </div>

            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body table-responsive">
    
    
                        <h2 class="page-title">Menu Packages</h2>
            
                     
                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-success"><?= $msg ?></div>
                        <?php } ?>
            
                        <table id="packageTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Package Name</th>
                                    <th>Meal Type</th>
                                    <th>Items</th>
                                    <th>Price</th>
                                    <!--<th>Status</th>-->
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
            
                            <tbody>
                                <?php
                                $q = mysqli_query($myDB, "
                                    SELECT 
                                        p.id,
                                        p.package_name,
                                        p.meal_type,
                                        p.price,
                                        p.active,
                                        COUNT(pi.food_id) AS total_items
                                    FROM rrsv_menu_package p
                                    LEFT JOIN rrsv_menu_package_items pi ON pi.package_id = p.id
                                    GROUP BY p.id
                                    ORDER BY p.id DESC
                                ");
            
                                $i = 1;
                                while ($r = mysqli_fetch_assoc($q)) {
                                    ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $r['package_name'] ?></td>
                                        <td><?= $r['meal_type'] ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= $r['total_items'] ?> items
                                            </span>
                                        </td>
                                        <td>₹<?= number_format($r['price'], 2) ?></td>
                                        <!--<td>-->
                                        <!--    <span class="badge <?= $r['active'] == 'Yes' ? 'badge-success' : 'badge-danger' ?>">-->
                                        <!--        <?= $r['active'] ?>-->
                                        <!--    </span>-->
                                        <!--</td>-->
                                        <td>
                                            <!--<a href="menu-package-view.php?id=<?= $r['id'] ?>" class="btn btn-info btn-sm">View</a>-->
                                            <!--<a href="menu-package-edit.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">Edit</a>-->
            
                                            <!--<a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this package?')"-->
                                            <!--    class="btn btn-danger btn-sm">Delete</a>-->
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
<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
    <?php include('../../include/footer.php'); ?>

    
    <script>
$(document).ready(function () {
    $('#packageTable').DataTable({
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

    
