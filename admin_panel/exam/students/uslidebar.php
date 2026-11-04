<!-- Sidebar Start -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MSB USER</li>
        <!--<li ><a href="https://login.bulksmsgateway.in/userindex"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>-->
        <li class="treeview active">
          <a href="#">
            <i class="fa fa-hand-o-right"></i>
            <span>Exam Details</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li <?php
if (@$_GET['q'] == 4)
    echo 'class="active"';
?>><a href="account.php?q=4"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>Home<span class="sr-only">(current)</span></a></li>

            <li <?php
if (@$_GET['q'] == 1)
    echo 'class="active"';
?> ><a href="account.php?q=1"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>Exam<span class="sr-only">(current)</span></a></li>
        <li <?php
if (@$_GET['q'] == 2)
    echo 'class="active"';
?>><a href="account.php?q=2"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>My History</a></li>
    <li <?php
if (@$_GET['q'] == 3)
    echo 'class="active"';
?>><a href="account.php?q=3"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span>&nbsp;Leaderboard</a></li>
<li><a href="logout.php?q=account.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Logout</a></li>
          </ul>
        </li>
		
      <!--   <li class="treeview active">
          <a href="#">
            <i class="fa fa-envelope-o"></i>
            <span>SMS Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
		  
			<li ><a href=""><i class="fa fa-circle-o"></i> Templates</a></li>
			<li ><a href=""><i class="fa fa-circle-o"></i> Groups</a></li>
			
			<li class='active'><a href="phonebook"><i class="fa fa-circle-o"></i> Phone Book</a></li>
			
		  </ul>
        </li>
       
         <li class="treeview ">
          <a href="#">
            <i class="fa fa-file-code-o"></i>
            <span>Developer API</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href=""><i class="fa fa-circle-o"></i> API URL</a></li>
            
            <li ><a href=""><i class="fa fa-circle-o"></i> Whatsapp SMS API URL</a></li>
            
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
            <li ><a href=""><i class="fa fa-key"></i> Change Password</a></li>
             <li ><a href=""><i class="fa fa-at"></i> Update Email</a></li>
            
          </ul>
        </li>-->
        
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
<!-- sidebar End -->