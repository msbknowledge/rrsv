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
            $defaultImage = '../../landing/images/default.jpg';
            
            if (isset($_SESSION['role']) && $_SESSION['role'] == 'teacher') {
                $name  = $teacher['full_name'] ?? '';
                $id  = $teacher['id'] ?? '';
                $user_role=$_SESSION['role'] ;
                $url_profile=BASE_URL.'admin_panel/teacher/profile.php';
                $image = '../../teacher_image/' . htmlspecialchars($teacher['image'] ?? $defaultImage);
                 //$countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications WHERE receiver_type='$user_role'" ;
                 $countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications WHERE (receiver_type='$user_role' and receiver_id='$id')  ORDER BY created_at DESC LIMIT 5";
                  // 📨 Last 5 notifications
                  $notifQuery = "SELECT * FROM rrsv_notifications WHERE (receiver_type='$user_role' and receiver_id='$id')  ORDER BY created_at DESC LIMIT 5";
                  $notifResult = mysqli_query($con, $notifQuery);
                //   OR (behaviour_id IS NULL OR behaviour_id = 0)
            } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'student') {
                $name  = $teacher['scl_name'] ?? '';
                $user_role=$_SESSION['role'] ;
                $student_ids = $_SESSION['student_ids'];
                //print_r($student_ids);die();
                $url_profile=BASE_URL.'admin_panel/student/profile.php';
                if (!empty($student_ids)) {
                    $ids = implode(',', array_map('intval', $student_ids));
                } else {
                    $ids = 0; // fallback to prevent SQL error
                }
                $image = '../../student_reg_image/' . htmlspecialchars($teacher['image'] ?? $defaultImage);
                // $countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications 
                //      WHERE receiver_id IN ($ids)
                //      AND receiver_type='$user_role' 
                //      AND seen=0";
                 $countQuery = "SELECT COUNT(*) AS unread_count FROM rrsv_notifications 
                   WHERE (receiver_id IN ($ids)
                   AND receiver_type='$user_role' 
                   AND seen=0)  ORDER BY created_at DESC LIMIT 5";
                      
      $notifQuery = "SELECT * FROM rrsv_notifications 
                     WHERE (receiver_id IN ($ids)
                     AND receiver_type='$user_role') AND seen=0
                     ORDER BY created_at DESC LIMIT 5";
                    //  OR (behaviour_id IS NULL OR behaviour_id = 0)
      $notifResult = mysqli_query($con, $notifQuery);
            } else {
                $name = 'Admin';
                $image = '';
            }
            
            // ✅ Server-side file existence check
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/rrsv/' . str_replace('../../', '', $image);
            
            if (!file_exists($fullPath) || empty($teacher['image'])) {
                $image = $defaultImage;
            }
            // 🔔 Unread count
      
      $countResult = mysqli_query($con, $countQuery);
      $countData = mysqli_fetch_assoc($countResult);
      $unreadCount = $countData['unread_count'] ?? 0;

     
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
              <?php if (mysqli_num_rows($notifResult) > 0): ?>
                <?php while ($n = mysqli_fetch_assoc($notifResult)): ?>
                  <li>
                    <a href="<?=BASE_URL?>admin_panel/notifications/notifications.php?notif_id=<?php echo $n['id']; ?>&from_nav=1" 
                       class="<?= $n['is_read'] ? '' : 'font-weight-bold' ?>">
                      <i class="fa fa-comment text-aqua"></i>
                      <?php echo htmlspecialchars(substr($n['message'], 0, 50)); ?>...
                      <br>
                      <small><i class="fa fa-clock-o"></i> <?php echo date('d M Y H:i', strtotime($n['created_at'])); ?></small>
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
             <img src="<?php echo $image;?>" class="img-circle user-image" alt="User Image">
			  <span class="pull-right top title1" ></span> <span class="log log1" style="color:lightyellow"><?php echo htmlspecialchars($name ?? ''); ?></span>  
            </a>
			<ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo $image;?>" class="img-circle user-image" alt="User Image">
                <p>
                Welcome  <small>
                  
                
                <?php echo htmlspecialchars($name?? ''); ?></small>                </p>
              </li>
              <!-- Menu Body -->
             
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?=$url_profile?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?=BASE_URL?>logout.php" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
			
            
          </li>
         
        </ul>
      </div>
    </nav>
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
