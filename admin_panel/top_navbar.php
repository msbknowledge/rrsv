 <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
       
            <?php
            $name = '';
            $image = '';
            $defaultImage = BASE_URL . 'landing/images/default.jpg';
            $notifResult = false;
            $unreadCount = 0;
            
            $user_role = $_SESSION['role'] ?? '';
            $teacher = $_SESSION['user_data'] ?? [];

            if ($user_role === 'teacher') {
                $name = $teacher['full_name'] ?? 'Teacher';
                $id = $teacher['id'] ?? '';
                $url_profile = BASE_URL . 'admin_panel/teacher/profile.php';
                $image = !empty($teacher['image']) ? BASE_URL . 'teacher_image/' . htmlspecialchars($teacher['image']) : $defaultImage;

                $countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications WHERE (receiver_type='teacher' AND receiver_id='$id') ORDER BY created_at DESC LIMIT 5";
                $countResult = mysqli_query($con, $countQuery);
                if ($countResult && $cData = mysqli_fetch_assoc($countResult)) {
                    $unreadCount = $cData['unread_count'] ?? 0;
                }

                $notifQuery = "SELECT * FROM rrsv_notifications WHERE (receiver_type='teacher' AND receiver_id='$id') ORDER BY created_at DESC LIMIT 5";
                $notifResult = mysqli_query($con, $notifQuery);
            } elseif ($user_role === 'student') {
                $name = $teacher['scl_name'] ?? 'Student';
                $student_ids = $_SESSION['student_ids'] ?? [];
                $url_profile = BASE_URL . 'admin_panel/student/profile.php';
                $ids = !empty($student_ids) ? implode(',', array_map('intval', (array)$student_ids)) : '0';
                $image = !empty($teacher['image']) ? BASE_URL . 'student_reg_image/' . htmlspecialchars($teacher['image']) : $defaultImage;

                $countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications WHERE (receiver_id IN ($ids) AND receiver_type='student' AND seen=0) ORDER BY created_at DESC LIMIT 5";
                $countResult = mysqli_query($con, $countQuery);
                if ($countResult && $cData = mysqli_fetch_assoc($countResult)) {
                    $unreadCount = $cData['unread_count'] ?? 0;
                }

                $notifQuery = "SELECT * FROM rrsv_notifications WHERE (receiver_id IN ($ids) AND receiver_type='student') AND seen=0 ORDER BY created_at DESC LIMIT 5";
                $notifResult = mysqli_query($con, $notifQuery);
            } elseif ($user_role === 'icard') {
                $name = 'iCard Admin';
                $url_profile = BASE_URL . 'admin_panel/icard';
                $image = BASE_URL . 'libray/images/logo.jpeg';
            } else {
                $name = $teacher['admin_user'] ?? 'Admin';
                $url_profile = BASE_URL . 'admin_panel';
                $image = BASE_URL . 'libray/images/logo.jpeg';

                $countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications WHERE (receiver_type='admin' AND seen=0)";
                $countResult = mysqli_query($con, $countQuery);
                if ($countResult && $cData = mysqli_fetch_assoc($countResult)) {
                    $unreadCount = $cData['unread_count'] ?? 0;
                }

                $notifQuery = "SELECT * FROM rrsv_notifications WHERE receiver_type='admin' ORDER BY created_at DESC LIMIT 5";
                $notifResult = mysqli_query($con, $notifQuery);
            }
            ?>   
       
             <!-- 🔔 Notification Menu -->
      <li class="dropdown notifications-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-bell-o"></i>
          <span class="label label-danger"><?php echo $unreadCount; ?></span>
        </a>
        <ul class="dropdown-menu">
          <li class="header">You have <?php echo $unreadCount; ?> notifications</li>
          <li>
            <ul class="menu" id="notifList">
              <?php if ($notifResult && mysqli_num_rows($notifResult) > 0): ?>
                <?php while ($n = mysqli_fetch_assoc($notifResult)): ?>
                  <li>
                    <a href="<?=BASE_URL?>admin_panel/notifications/notifications.php?notif_id=<?php echo $n['id']; ?>&from_nav=1" 
                       class="<?= !empty($n['is_read']) ? '' : 'font-weight-bold' ?>">
                      <i class="fa fa-comment text-aqua"></i>
                      <?php echo htmlspecialchars(substr($n['message'] ?? '', 0, 50)); ?>...
                      <br>
                      <small><i class="fa fa-clock-o"></i> <?php echo !empty($n['created_at']) ? date('d M Y H:i', strtotime($n['created_at'])) : ''; ?></small>
                    </a>
                  </li>
                <?php endwhile; ?>
              <?php else: ?>
                <li><a>No notifications found</a></li>
              <?php endif; ?>
            </ul>
          </li>
          <li class="footer"><a href="<?=BASE_URL?>admin_panel/notifications/notifications.php">View all</a></li>
        </ul>
      </li>
          <!-- User Account: style can be found in dropdown.less -->
           <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $image;?>" class="img-circle user-image" alt="User Image" style="object-fit: cover; width: 25px; height: 25px;">
			  <span class="pull-right top title1" ></span> <span class="log log1" style="color:lightyellow"><?php echo htmlspecialchars($name); ?></span>  
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo $image;?>" class="img-circle user-image" alt="User Image" style="object-fit: cover; width: 90px; height: 90px;">
                <p>
                Welcome <small><?php echo htmlspecialchars($name); ?></small>
                </p>
              </li>
              <!-- Menu Body -->
             
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?=$url_profile?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?=BASE_URL?>admin_panel/logout.php" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
          </li>
         
        </ul>
      </div>
    </nav>
