<?php
$student_cnt = 0;
$teacher_cnt = 0;
$exam_cnt = 0;
$class_cnt = 0;

if (isset($con) && $con) {
    $r = mysqli_query($con, "SELECT COUNT(*) as c FROM rrsv_student_registration");
    if ($r && $row = mysqli_fetch_assoc($r)) $student_cnt = $row['c'];

    $r = mysqli_query($con, "SELECT COUNT(*) as c FROM rrsv_teacher");
    if ($r && $row = mysqli_fetch_assoc($r)) $teacher_cnt = $row['c'];

    $r = mysqli_query($con, "SELECT COUNT(*) as c FROM rrsv_exam");
    if ($r && $row = mysqli_fetch_assoc($r)) $exam_cnt = $row['c'];

    $r = mysqli_query($con, "SELECT COUNT(*) as c FROM rrsv_class");
    if ($r && $row = mysqli_fetch_assoc($r)) $class_cnt = $row['c'];
}
?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Admin Dashboard
      <small>Control Panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?=BASE_URL?>admin_panel"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- Stat Cards -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?=number_format($student_cnt)?></h3>
            <p>Total Students</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
          <a href="<?=BASE_URL?>admin_panel/student_behaviour" class="small-box-footer">View Students <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?=number_format($teacher_cnt)?></h3>
            <p>Total Teachers</p>
          </div>
          <div class="icon">
            <i class="fa fa-graduation-cap"></i>
          </div>
          <a href="<?=BASE_URL?>admin_panel/teacher/index.php" class="small-box-footer">Teacher Portal <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?=number_format($exam_cnt)?></h3>
            <p>Total Exams</p>
          </div>
          <div class="icon">
            <i class="fa fa-pencil-square-o"></i>
          </div>
          <a href="<?=BASE_URL?>admin_panel/head" class="small-box-footer">Manage Exams <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?=number_format($class_cnt)?></h3>
            <p>Classes</p>
          </div>
          <div class="icon">
            <i class="fa fa-building-o"></i>
          </div>
          <a href="<?=BASE_URL?>home.php" class="small-box-footer">Main School ERP <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>

    <!-- Quick Navigation Box -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-th-large"></i> Quick Portal Navigation</h3>
          </div>
          <div class="box-body">
            <div class="row" style="margin-top: 15px;">
              <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 20px;">
                <a href="<?=BASE_URL?>home.php" class="btn btn-primary btn-block btn-lg" style="padding: 15px;">
                  <i class="fa fa-university fa-2x d-block"></i><br>Main School ERP
                </a>
              </div>
              <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 20px;">
                <a href="<?=BASE_URL?>admin_panel/teacher/index.php" class="btn btn-info btn-block btn-lg" style="padding: 15px;">
                  <i class="fa fa-chalkboard-teacher fa-user fa-2x d-block"></i><br>Teacher Portal
                </a>
              </div>
              <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 20px;">
                <a href="<?=BASE_URL?>admin_panel/icard/index.php" class="btn btn-warning btn-block btn-lg" style="padding: 15px; color: #fff;">
                  <i class="fa fa-id-card fa-2x d-block"></i><br>Student &amp; Teacher iCards
                </a>
              </div>
              <div class="col-md-3 col-sm-6 text-center" style="margin-bottom: 20px;">
                <a href="<?=BASE_URL?>admin_panel/head/index.php" class="btn btn-success btn-block btn-lg" style="padding: 15px;">
                  <i class="fa fa-cogs fa-2x d-block"></i><br>Question Types &amp; Setup
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
