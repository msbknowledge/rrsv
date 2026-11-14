<?php
session_start();
error_reporting(0);

include('../../include/header.php');
include('../../include/dbcon.php');
date_default_timezone_set('Asia/Kolkata');

if (empty($_SESSION['mid']) && empty($_SESSION['alogin'])) {
	header('location:index.php');
	exit;
}
$page_title = "Teacher Order Summary";

// Summary statistics
$stat_sql = "
    SELECT 
        COUNT(DISTINCT t.id) AS total_teachers,
        COUNT(DISTINCT o.id) AS total_orders,
        COALESCE(SUM(CASE WHEN i.status = 'Confirmed' THEN (i.price * i.quantity) ELSE 0 END), 0) AS total_sales,
        COALESCE(SUM(w.balance), 0) AS total_wallet_balance
    FROM rrsv_teacher t
    LEFT JOIN rrsv_food_orders o 
        ON (o.student_id = CONCAT('T-', t.id) OR o.student_id = CAST(t.id AS CHAR))
    LEFT JOIN rrsv_food_order_items i 
        ON i.order_id = o.id AND i.status = 'Confirmed'
    LEFT JOIN rrsv_food_wallet w 
        ON (w.student_id = CONCAT('T-', t.id) OR w.student_id = CAST(t.id AS CHAR))
    WHERE t.status = 'active' OR t.status IS NULL OR t.status = ''
";
$stat_res = mysqli_query($myDB, $stat_sql);
$stat = mysqli_fetch_assoc($stat_res);
?>

<div class="main-panel">
    <div class="content-wrapper">

        <!-- Top Header Card -->
        <div class="card mb-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 8px;">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h3 class="m-0 font-weight-bold text-white">
                            <i class="fa fa-cutlery"></i> Teacher-wise Food & Wallet Summary
                        </h3>
                        <p class="text-white-50 m-0 mt-1" style="font-size: 14px;">
                            Manage teacher meal bookings, wallet recharges, monthly food bills & salary deduction attachments
                        </p>
                    </div>
                    <div class="col-md-5 text-md-right mt-3 mt-md-0">
                        <a href="order_meal_type_token.php" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                            <i class="fa fa-ticket"></i> Daily Meal Tokens
                        </a>
                        <a href="student_list.php" class="btn btn-outline-light btn-sm ml-2">
                            <i class="fa fa-graduation-cap"></i> Student Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-0" style="border-left: 4px solid #4e73df !important; border-radius: 6px;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted font-weight-bold text-uppercase">Total Teachers</small>
                                <h3 class="font-weight-bold text-dark mb-0"><?= number_format($stat['total_teachers'] ?? 0); ?></h3>
                            </div>
                            <div class="bg-primary text-white p-3 rounded-circle">
                                <i class="fa fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-0" style="border-left: 4px solid #1cc88a !important; border-radius: 6px;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted font-weight-bold text-uppercase">Total Food Orders</small>
                                <h3 class="font-weight-bold text-success mb-0"><?= number_format($stat['total_orders'] ?? 0); ?></h3>
                            </div>
                            <div class="bg-success text-white p-3 rounded-circle">
                                <i class="fa fa-shopping-cart fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-0" style="border-left: 4px solid #36b9cc !important; border-radius: 6px;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted font-weight-bold text-uppercase">Total Food Billed</small>
                                <h3 class="font-weight-bold text-info mb-0">₹<?= number_format($stat['total_sales'] ?? 0, 2); ?></h3>
                            </div>
                            <div class="bg-info text-white p-3 rounded-circle">
                                <i class="fa fa-inr fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-0" style="border-left: 4px solid #f6c23e !important; border-radius: 6px;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted font-weight-bold text-uppercase">Teachers Wallet Balance</small>
                                <h3 class="font-weight-bold text-warning mb-0">₹<?= number_format($stat['total_wallet_balance'] ?? 0, 2); ?></h3>
                            </div>
                            <div class="bg-warning text-white p-3 rounded-circle">
                                <i class="fa fa-google-wallet fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers Table Card -->
        <div class="card shadow-sm border-0" style="border-radius: 8px;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fa fa-list"></i> Teacher List & Food Accounts
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="teacherTable" class="table table-striped table-bordered align-middle" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th style="width: 60px;">Photo</th>
                                <th>Teacher Name & Designation</th>
                                <th>Teacher ID</th>
                                <th>Contact No</th>
                                <th class="text-center">Current Balance (₹)</th>
                                <th class="text-center">Orders</th>
                                <th class="text-right">Total Food (₹)</th>
                                <th class="text-center" style="width: 130px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cnt = 1;
                            $sql = "
                                SELECT 
                                    t.id,
                                    t.full_name,
                                    t.designation,
                                    t.tech_phone_no,
                                    t.email,
                                    t.image,
                                    t.monthly_salary,
                                    w.balance,
                                    COUNT(DISTINCT o.id) AS total_orders,
                                    COALESCE(SUM(
                                        CASE 
                                            WHEN i.status = 'Confirmed' 
                                            THEN (i.price * i.quantity) 
                                            ELSE 0 
                                        END
                                    ), 0) AS total_amount
                                FROM rrsv_teacher t
                                LEFT JOIN rrsv_food_orders o 
                                    ON (o.student_id = CONCAT('T-', t.id) OR o.student_id = CAST(t.id AS CHAR))
                                LEFT JOIN rrsv_food_order_items i 
                                    ON i.order_id = o.id AND i.status = 'Confirmed'
                                LEFT JOIN rrsv_food_wallet w 
                                    ON (w.student_id = CONCAT('T-', t.id) OR w.student_id = CAST(t.id AS CHAR))
                                WHERE t.status = 'active' OR t.status IS NULL OR t.status = ''
                                GROUP BY t.id, t.full_name, t.designation, t.tech_phone_no, t.email, t.image, t.monthly_salary, w.balance
                                ORDER BY t.full_name ASC
                            ";

                            $query = mysqli_query($myDB, $sql);

                            while ($row = mysqli_fetch_assoc($query)) {
                                $img_src = !empty($row['image']) && file_exists('../../teacher_image/' . $row['image']) 
                                    ? '../../teacher_image/' . htmlspecialchars($row['image']) 
                                    : '../../libray/images/default-avatar.png';
                                $curr_balance = (float)($row['balance'] ?? 0);
                                $balance_badge_class = $curr_balance > 0 ? 'badge-success' : ($curr_balance < 0 ? 'badge-danger' : 'badge-secondary');
                            ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $cnt++; ?></td>
                                    <td class="text-center">
                                        <img src="<?= $img_src; ?>" alt="Teacher Photo" 
                                             style="width: 42px; height: 42px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd;"
                                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['full_name']); ?>&background=1e3c72&color=fff';">
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 15px;">
                                            <?= htmlspecialchars($row['full_name']); ?>
                                        </div>
                                        <small class="badge badge-info" style="font-size: 11px;">
                                            <?= htmlspecialchars($row['designation'] ?: 'Teacher'); ?>
                                        </small>
                                        <?php if (!empty($row['monthly_salary'])): ?>
                                            <small class="text-muted ml-1" style="font-size: 11px;">
                                                (Salary: ₹<?= number_format($row['monthly_salary']); ?>)
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-dark font-weight-bold">
                                            T-<?= $row['id']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fa fa-phone text-muted mr-1"></i>
                                        <?= htmlspecialchars($row['tech_phone_no'] ?: 'N/A'); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $balance_badge_class; ?> px-2 py-1" style="font-size: 13px;">
                                            ₹ <?= number_format($curr_balance, 2); ?>
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        <?= (int)$row['total_orders']; ?>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        ₹ <?= number_format((float)$row['total_amount'], 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="teacher_manage.php?teacher_id=<?= $row['id']; ?>" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 4px;">
                                            <i class="fa fa-cogs"></i> Manage Food
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
</div>

<link href='<?php echo BASE_URL; ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>
<?php include('../../include/footer.php'); ?>

<script src="<?php echo BASE_URL; ?>libray/DataTables/datatables.min.js"></script>
<script>
$(document).ready(function () {
    $('#teacherTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 15,
        order: [[2, 'asc']], // Order by teacher name
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search teacher, phone, designation..."
        }
    });
});
</script>
