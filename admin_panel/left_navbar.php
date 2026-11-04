<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">Dashboard</li>
      <li ><a href="<?=BASE_URL?>admin_panel"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li class="treeview active">
        <a href="#">
          <i class="fa fa-hand-o-right"></i>
          <span>Education</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php

          if (isset($_SESSION['role']) && $_SESSION['role'] == 'teacher') {

            ?>
            <li><a href="<?=BASE_URL?>admin_panel/head"><i class="fa fa-circle-o"></i>Head Type</a></li>
            <li><a href="<?=BASE_URL?>admin_panel/exam/teach/dash.php?q=10"><i class="fa fa-circle-o"></i> Exam</a></li>
            <li><a href="<?=BASE_URL?>admin_panel/teacher/homework_exam.php"><i class="fa fa-circle-o"></i> Class Work</a></li>
            <li class="active"><a href="<?=BASE_URL?>admin_panel/student_behaviour"><i class="fa fa-circle-o"></i>Student Behaviour</a></li>
            <!--<li><a href="<?=BASE_URL?>admin_panel/view_results.php"><i class="fa fa-circle-o"></i>Result</a></li>-->
            <?php
          } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'student') {
           $users = $_SESSION['user_data'];
            ?>
            <li><a href="<?=BASE_URL?>admin_panel/exam/students/account.php?q=4"><i class="fa fa-circle-o"></i> Exam</a></li>
            <li><a href="<?=BASE_URL?>admin_panel/student/homework_exam_list.php"><i class="fa fa-circle-o"></i> Home Work</a></li>
            <li class="active"><a href="<?=BASE_URL?>admin_panel/student/food/student_order_history.php"><i class="fa fa-circle-o"></i>Food</a></li>
            <li class="active"><a href="<?=BASE_URL?>admin_panel/student_behaviour"><i class="fa fa-circle-o"></i>Student Behaviour</a></li>
            <li class="active"><a href="<?=BASE_URL?>admin_panel/student/student_detals.php?scl_reg_no=<?=$users['scl_reg_no']?>"><i class="fa fa-circle-o"></i>Marksheet</a></li>
            <?php
          } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'icard') {

            ?>
            <li><a href="<?=BASE_URL?>admin_panel/icard/teacher_icard.php"><i class="fa fa-circle-o"></i>Teacher Icard</a></li>
            <li><a href="<?=BASE_URL?>admin_panel/icard"><i class="fa fa-circle-o"></i>Student Icard</a></li>
            
            <?php
          } else {
            ?>
            <li><a href="<?=BASE_URL?>admin_panel"><i class="fa fa-circle-o"></i> Dashboard</a></li>
            <?php
          }
          ?>
          <!--<li><a href="<?=BASE_URL?>admin_panel/comment"><i class="fa fa-circle-o"></i>Comment<span class="sr-only"></span></a>-->
          <!--</li>-->
          
          
          <!--<li><a href="#"><i class="fa fa-circle-o"></i>Leaderboard</a></li>-->
          <!--<li><a href="#"><i class="fa fa-circle-o"></i>feedback</a></li>-->
          <!--<li><a href="#" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>-->


          <!--<li><a href="#" id="add_button" class="active">Add Student</a></li>-->

        </ul>
      </li>

      <!--<li class="treeview active">-->
      <!--  <a href="#">-->
      <!--    <i class="fa fa-envelope-o"></i>-->
      <!--    <span>Exam Management</span>-->
      <!--    <span class="pull-right-container">-->
      <!--      <i class="fa fa-angle-left pull-right"></i>-->
      <!--    </span>-->
      <!--  </a>-->
      <!--  <ul class="treeview-menu">-->
      <!--    <li><a href="#"><i class="fa fa-circle-o"></i>#</a></li>-->
      <!--    <li><a href="#"><i class="fa fa-circle-o"></i>#</a></li>-->




      <!--  </ul>-->
      <!--</li>-->

      <!--<li class="treeview ">-->
      <!--  <a href="#">-->
      <!--    <i class="fa fa-file-code-o"></i>-->
      <!--    <span>Result</span>-->
      <!--    <span class="pull-right-container">-->
      <!--      <i class="fa fa-angle-left pull-right"></i>-->
      <!--    </span>-->
      <!--  </a>-->
      <!--  <ul class="treeview-menu">-->
      <!--    <li><a href="#"><i class="fa fa-circle-o"></i> Result</a></li>-->

      <!--  </ul>-->
      <!--</li>-->
      <!--<li class="treeview ">-->
      <!--  <a href="#">-->
      <!--    <i class="fa fa-wrench"></i>-->
      <!--    <span>Personal Settings</span>-->
      <!--    <span class="pull-right-container">-->
      <!--      <i class="fa fa-angle-left pull-right"></i>-->
      <!--    </span>-->
      <!--  </a>-->
      <!--  <ul class="treeview-menu">-->
      <!--    <li><a href="#"><i class="fa fa-key"></i> Change Password</a></li>-->
      <!--    <li><a href="#"><i class="fa fa-at"></i> Update Email</a></li>-->

      <!--  </ul>-->
      <!--</li>-->


    </ul>
  </section>
  <!-- /.sidebar -->
</aside>