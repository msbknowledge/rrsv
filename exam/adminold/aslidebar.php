<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">Dashboard</li>
        <!--<li ><a href=""><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>-->
        <li class="treeview active">
          <a href="#">
            <i class="fa fa-hand-o-right"></i>
            <span>Admin</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          

 <li <?php
if (@$_GET['q'] == 10)
    echo 'class="active"';
?>><a href="dashboard.php?q=10"><i class="fa fa-circle-o"></i>Home<span class="sr-only"></span></a></li>

        <li <?php
if (@$_GET['q'] == 1)
    echo 'class="active"';
?>><a href="dashboard.php?q=1"><i class="fa fa-circle-o"></i>Users</a></li>
    <li <?php
if (@$_GET['q'] == 2)
    echo 'class="active"';
?>><a href="dashboard.php?q=2"><i class="fa fa-circle-o"></i>Leaderboard</a></li>
 
<li <?php
if (@$_GET['q'] == 3)
    echo 'class="active"';
?>><a href="dashboard.php?q=3" class="active"><i class="fa fa-circle-o"></i>Feedback</a></li>
  
<li <?php
if (@$_GET['q'] == 4)
    echo 'class="active"';
?>><a href="dashboard.php?q=4" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  
<li <?php
if (@$_GET['q'] == 5)
    echo 'class="active"';
?>><a href="dashboard.php?q=5" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  
  
<li <?php
if (@$_GET['q'] == 6)
    echo 'class="active"';
?>><a href="dashboard.php?q=6" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  
<li <?php
if (@$_GET['q'] == 7)
    echo 'class="active"';
?>><a href="dashboard.php?q=7" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  
<li <?php
if (@$_GET['q'] == 8)
    echo 'class="active"';
?>><a href="dashboard.php?q=8" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  
<li <?php
if (@$_GET['q'] == 9)
    echo 'class="active"';
?>><a href="dashboard.php?q=9" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>