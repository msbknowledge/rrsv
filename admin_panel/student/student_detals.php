<?php
session_start();
include '../dbcon.php'; 
        $scl_reg_no=$con->escape_string(trim(addslashes($_GET['scl_reg_no'])));

$scl_name = '';
$scl_class = '';
$scl_roll_no = '';
$scl_reg_no = '';
$scl_session = '';
  
// if(isset($_GET['scl_reg_no'])) {
    $scl_reg_no = mysqli_real_escape_string($con,$_GET['scl_reg_no']);

    $sql="SELECT id,scl_name,scl_class,scl_roll_no,scl_reg_no,scl_session,image
          FROM rrsv_student_registration
          WHERE scl_reg_no='$scl_reg_no'";

    $res=mysqli_query($con,$sql);

    ?>

   

<!DOCTYPE html>
<html>
<?php include('../header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <!-- Header -->
    <header class="main-header">
        <a href="#" class="logo">
            <span class="logo-mini"><b>MSB</b></span>
             <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
        </a>
        <?php include '../top_navbar.php'; ?>
    </header>

    <!-- Sidebar -->
    <?php include '../left_navbar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Homework / Exam List</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Available Exams</h3>
                        </div>

                        <div class="box-body table-responsive">
            <table id="example1" class="table table-bordered table-striped" border="1" cellpadding="5">
                <thead>
<tr>
    <!--<th>Registration</th>-->
    <th>Class</th>
    <th>Roll</th>
    <th>Session</th>
    <th>Class Test</th>
    <th>MarkSheet</th>
</tr>
</thead>
                                <tbody>
<?php
while($row=mysqli_fetch_assoc($res)){
?>
<tr>
    <!--<td><?php echo $row['scl_reg_no']; ?></td>-->
    <td><?php echo $row['scl_class']; ?></td>
    <td><?php echo $row['scl_roll_no']; ?></td>
    <td><?php echo $row['scl_session']; ?></td>
    <td>
        
        <a href="marksheet2.php?student_id=<?=$row['id']?>&scl_class=<?php echo $row['scl_class']; ?>&scl_session=<?php echo $row['scl_session']; ?>">
            View
        </a>
    </td>
     <td>
          <a href="semester_test_report.php?student_id=<?=$row['id']?>&scl_class=<?php echo $row['scl_class']; ?>&scl_session=<?php echo $row['scl_session']; ?>">
            View
        </a>
        <!-- <a href="semester_report.php?student_id=<?=$row['id']?>&scl_class=<?php echo $row['scl_class']; ?>&scl_session=<?php echo $row['scl_session']; ?>">-->
        <!--    View-->
        <!--</a>-->
        <!--<div class="btn-group">-->
        <!--  <button type="button" class="btn btn-success dropdown-toggle"-->
        <!--          data-toggle="dropdown"-->
        <!--          style="background-color:#e76bbe;border-color:#a204a4;">-->
        <!--    View1-->
        <!--  </button>-->
        
        <!--  <ul class="dropdown-menu">-->
        
        <!--    <li>-->
        <!--      <a href="marksheet.php?student_id=<?=$row['id'];?>&scl_class=<?=$row['scl_class'];?>&scl_session=<?=$row['scl_session'];?>&unit=1">-->
        <!--        1st Unit-->
        <!--      </a>-->
        <!--    </li>-->
        
        <!--    <li>-->
        <!--      <a href="marksheet.php?student_id=<?=$row['id'];?>&scl_class=<?=$row['scl_class'];?>&scl_session=<?=$row['scl_session'];?>&unit=2">-->
        <!--        2nd Unit-->
        <!--      </a>-->
        <!--    </li>-->
        
        <!--    <li>-->
        <!--      <a href="marksheet.php?student_id=<?=$row['id'];?>&scl_class=<?=$row['scl_class'];?>&scl_session=<?=$row['scl_session'];?>&unit=3">-->
        <!--        3rd Unit-->
        <!--      </a>-->
        <!--    </li>-->
        
        <!--  </ul>-->
        <!--</div>-->
        
    </td>
</tr>
<?php } ?>
</tbody>
</table>

   </div> <!-- /.box-body -->
                    </div> <!-- /.box -->
                </div>
            </div>
        </section>
    </div>

    <?php include '../footer.php'; ?>
    <div class="control-sidebar-bg"></div>
</div>

<!-- Scripts -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script src="../assets/js/app.min.js"></script>
<script>
$(function () {
  $("#example1").DataTable();
});
</script>

</body>
</html>
