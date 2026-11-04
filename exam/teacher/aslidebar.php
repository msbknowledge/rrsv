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
?>><a href="dash.php?q=10"><i class="fa fa-circle-o"></i>Home<span class="sr-only">(current)</span></a></li>
<li <?php
if (@$_GET['q'] == 10)
    echo 'class="active"';
?>><a href="quiz.php?q=10"><i class="fa fa-circle-o"></i>Add Exam<span class="sr-only">(current)</span></a></li>
        <li <?php
if (@$_GET['q'] == 1)
    echo 'class="active"';
?>><a href="user.php?q=1"><i class="fa fa-circle-o"></i>Students</a></li>
    <li <?php
if (@$_GET['q'] == 2)
    echo 'class="active"';
?>><a href="dash.php?q=2"><i class="fa fa-circle-o"></i>Leaderboard</a></li>
 
<li <?php
if (@$_GET['q'] == 8)
    echo 'class="active"';
?>><a href="exam_status.php?q=8" class="active"><i class="fa fa-circle-o"></i>Exam Status</a></li>
  <!--
<li>><a href="add_questions.php?q=7"><i class="fa fa-circle-o"></i></a></li>
<li><a href="#" id="add_button" class="active">Add Student</a></li>
           
          </ul>
        </li>
 	
         <li class="treeview active">
          <a href="#">
            <i class="fa fa-envelope-o"></i>
            <span>Exam Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
		 <li <?php
if (@$_GET['q'] == 4)
    echo 'class="active"';
?>><a href="dashboard.php?q=10"><i class="fa fa-circle-o"></i>Quiz</a></li>
        <li <?php
if (@$_GET['q'] == 5)
    echo 'class="active"';
?>><a href="index1.php?q=5"><i class="fa fa-circle-o"></i>Remove Quiz</a></li> 
			
						
			
			
		  </ul>
        </li>
       
         <li class="treeview ">
          <a href="#">
            <i class="fa fa-file-code-o"></i>
            <span>Result</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="https://login.bulksmsgateway.in/apiurl"><i class="fa fa-circle-o"></i> API URL</a></li>
           
          </ul>
        </li>
       <li class="treeview ">
          <a href="#">
            <i class="fa fa-wrench"></i>
            <span>Personal Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="https://login.bulksmsgateway.in/cpass"><i class="fa fa-key"></i> Change Password</a></li>
			 <li ><a href="https://login.bulksmsgateway.in/emailupdate"><i class="fa fa-at"></i> Update Email</a></li>
            
          </ul>
        </li>
        
         -->
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>