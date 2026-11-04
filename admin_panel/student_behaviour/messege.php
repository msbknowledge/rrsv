<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messeges</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/card.css">
<style>
 :root {
  --body-bg: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  --msger-bg: #fff;
  --border: 2px solid #ddd;
  --left-msg-bg: #ececec;
  --right-msg-bg: #579ffb;
}

html {
  box-sizing: border-box;
}

*,
*:before,
*:after {
  margin: 0;
  padding: 0;
  box-sizing: inherit;
}

.msger {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 100%;
  max-width: 867px;
  height: 90vh;
  margin: 25px 10px;
  border: var(--border);
  border-radius: 5px;
  background: var(--msger-bg);
  box-shadow: 0 15px 15px -5px rgba(0, 0, 0, 0.2);
}

.msger-header {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: var(--border);
  background: #eee;
  color: #666;
}

.msger-chat {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
}
.msger-chat::-webkit-scrollbar { width: 6px; }
.msger-chat::-webkit-scrollbar-track { background: #ddd; }
.msger-chat::-webkit-scrollbar-thumb { background: #bdbdbd; }

.msg {
  display: flex;
  align-items: flex-end;
  margin-bottom: 10px;
}
.msg:last-of-type { margin: 0; }

.msg-bubble {
  max-width: 450px;
  padding: 15px;
  border-radius: 15px;
  background: var(--left-msg-bg);
}

.msg-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
.msg-info-name { font-weight: bold; }
.msg-info-time { font-size: 0.85em; }

.left-msg .msg-bubble { border-bottom-left-radius: 0; }
.right-msg { flex-direction: row-reverse; }
.right-msg .msg-bubble {
  background: var(--right-msg-bg);
  color: #fff;
  border-bottom-right-radius: 0;
}
.right-msg .msg-img { margin-left: 10px; }

.msger-inputarea {
  display: flex;
  padding: 10px;
  border-top: var(--border);
  background: #eee;
}
.msger-inputarea * {
  padding: 10px;
  border: none;
  border-radius: 3px;
  font-size: 1em;
}
.msger-input {
  flex: 1;
  background: #ddd;
}
.msger-send-btn {
  margin-left: 10px;
  background: rgb(0, 196, 65);
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.23s;
}
.msger-send-btn:hover { background: rgb(0, 180, 50); }

.chat-popup {
  display: none;
  position: fixed;
  bottom: 0;
  right: 15px;
  border: 3px solid #f1f1f1;
  z-index: 999;
  background: #fff;
}

.form-container {
  max-width: 400px;
  background-color: white;
  border-radius: 6px;
  overflow: hidden;
}
</style>
</head>

<?php
session_start();
include '../dbcon.php';

if (!isset($_SESSION['user_data']) || !in_array($_SESSION['role'], ['teacher', 'student'])) {
    header("Location: ../../index.php?error=Unauthorized Access");
    exit;
}

$teacher = $_SESSION['user_data'];
$user_id = $teacher['id'];
$current_session = date('Y');
$id_list = implode(',', array_map('intval', $_SESSION['student_ids']))??[];
if ($_SESSION['role'] == 'student') {
    $behaviours = mysqli_query($con, "
        SELECT b.*, 
               s.scl_name AS student_name, 
               s.scl_class AS student_class, 
               t.full_name AS teacher_name
        FROM rrsv_student_behaviour AS b
        LEFT JOIN rrsv_student_registration AS s ON b.student_id = s.id
        LEFT JOIN rrsv_teacher AS t ON b.teacher_id = t.id
        WHERE b.scl_session = '$current_session' 
        AND (b.student_id IN ($id_list) )
        ORDER BY b.id DESC
    ");
    
     $behaviour=mysqli_query($con,"SELECT  rrsv_student_behaviour.*,rrsv_teacher.full_name AS teacher_name,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_behaviour INNER JOIN rrsv_teacher ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id INNER JOIN rrsv_student_registration ON rrsv_student_behaviour.student_id = rrsv_student_registration.id WHERE rrsv_student_behaviour.scl_session = '$current_session' 
        AND (rrsv_student_behaviour.student_id IN ($id_list) )
        ORDER BY rrsv_student_behaviour.id DESC");

} else {
     $behaviour=mysqli_query($con,"SELECT  rrsv_student_behaviour.*,rrsv_teacher.full_name AS teacher_name,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_behaviour INNER JOIN rrsv_teacher ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id INNER JOIN rrsv_student_registration ON rrsv_student_behaviour.student_id = rrsv_student_registration.id WHERE rrsv_student_behaviour.scl_session = '$current_session' 
        AND rrsv_student_behaviour.teacher_id = $user_id
        ORDER BY rrsv_student_behaviour.id DESC");

    $behaviours = mysqli_query($con, "
        SELECT b.*, 
               s.scl_name AS student_name, 
               s.scl_class AS student_class, 
               t.full_name AS teacher_name
        FROM rrsv_student_behaviour AS b
        LEFT JOIN rrsv_student_registration AS s ON b.student_id = s.id
        LEFT JOIN rrsv_teacher AS t ON b.teacher_id = t.id
        WHERE b.scl_session = '$current_session' 
        AND b.teacher_id = $user_id
        ORDER BY b.id DESC
    ");
}

//   $behaviour=mysqli_query($con,"SELECT  rrsv_student_behaviour.*,rrsv_teacher.full_name AS teacher_name,rrsv_student_registration.scl_name AS student_name FROM  rrsv_student_behaviour INNER JOIN rrsv_teacher ON rrsv_student_behaviour.teacher_id = rrsv_teacher.id INNER JOIN rrsv_student_registration ON rrsv_student_behaviour.student_id = rrsv_student_registration.id ");
?>

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

  <div class="content-wrapper">
      <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        
                        <div class="box-header with-border">
                           
                            <h3 class="box-title">Messege</h3>
                            <?php if ($_SESSION['role'] == 'teacher') {?>
                            <div style="text-align: right;">
                                <a href="#" class="btn-sm btn btn-primary" data-toggle="modal"
                                    data-target="#addBehaviourModal" style="margin-bottom:4px;">Add Behaviour</a>
                            </div>
                           <?php 
                                
                            }
                            ?>
                        </div>
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Teacher</th>
                                        <th>Student</th>
                                        <th>Description</th>
                                        <th>Session</th>
                                        <th>Unit</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($b = mysqli_fetch_assoc($behaviour)) {
                                        
                                    ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            
                                            <td><?= htmlspecialchars($b['teacher_name']) ?> </td>
                                            <td><?= htmlspecialchars($b['student_name']) ?> </td>
                                            <td><?= htmlspecialchars($b['des']) ?></td>
                                            <td><?= htmlspecialchars($b['scl_session']) ?></td>
                                            <td><?= htmlspecialchars($b['unit']) ?></td>
                                            <td><?= htmlspecialchars($b['date']) ?> </td>
                                           
                                           <td> <button class="btn btn-xs btn-primary" onclick="openForm(<?= $b['id'] ?>)">Message</button> 
                                            <!-- Chat Popup -->
                                            <div class="chat-popup" id="form_<?= $b['id'] ?>">
                                             <form action="save_comment.php" method="POST" class="form-container">
                <section class="msger">
                  <header class="msger-header">
                    <div class="msger-header-title"><?= htmlspecialchars($b['des']) ?></div>
                    <button type="button" class="btn btn-danger btn-xs" onclick="closeForm(<?= $b['id'] ?>)">X</button>
                  </header>

                  <main class="msger-chat">
                    <?php
                   
                    $comments = mysqli_query($con, "
                        SELECT c.*, 
                               CASE 
                                    WHEN c.user_type = 'teacher' THEN t.full_name
                                    WHEN c.user_type = 'student' THEN s.scl_name
                               END AS user_name
                        FROM rrsv_behaviour_comments AS c
                        LEFT JOIN rrsv_teacher AS t ON c.user_type = 'teacher' AND c.user_id = t.id
                        LEFT JOIN rrsv_student_registration AS s ON c.user_type = 'student' AND c.user_id = s.id
                        WHERE c.behaviour_id = {$b['id']}
                        ORDER BY c.created_at asc
                    ");
                    while ($c = mysqli_fetch_assoc($comments)):
                      $align = ($c['user_type'] == 'teacher') ? 'left' : 'right';
                    ?>
                      <div class="msg <?= $align ?>-msg">
                        <div class="msg-bubble">
                          <div class="msg-info">
                            <div class="msg-info-name"><?= ucfirst($c['user_name']) ?><br/></div>
                            
                          </div>
                          <div class="msg-info-time"><?= date('H:i', strtotime($c['created_at'])) ?></div>
                          <div class="msg-text"><?= htmlspecialchars($c['comment']) ?></div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </main>

                  <div class="msger-inputarea">
                    <input type="hidden" name="behaviour_id" value="<?= $b['id'] ?>">
                    <input type="hidden" name="user_type" value="<?= $_SESSION['role'] ?>">
                    <input type="hidden" name="teacher_id" value="<?= $user_id ?>">
                    <input type="hidden" name="student_id" value="<?= $b['student_id'] ?>">
                    <textarea name="comment" class="msger-input" placeholder="Enter your message..." required></textarea>
                    <button type="submit" class="msger-send-btn">Send</button>
                  </div>
                </section>
              </form>
                                            </div>
                                           
                                           </td>
                                           
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div> <!-- /.box-body -->
                    </div> <!-- /.box -->
                    
                    
                    
                    
                    
        <div class="row">
          

          <div class="col-md-12">
            <!-- DIRECT CHAT DANGER -->
            <div class="card card-danger direct-chat direct-chat-danger">
              <div class="card-header">
                <h3 class="card-title">Direct Chat</h3>

                <div class="card-tools">
                  <span title="3 New Messages" class="badge">3</span>
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                    <i class="fas fa-comments"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages">
                  <!-- Message. Default to the left -->
                    <?php
                   if ($_SESSION['role'] == 'student') {
                    $comments = mysqli_query($con, "
                        SELECT c.*, 
                               CASE 
                                    WHEN c.user_type = 'teacher' THEN t.full_name
                                    WHEN c.user_type = 'student' THEN s.scl_name
                               END AS user_name
                        FROM rrsv_behaviour_comments AS c
                        LEFT JOIN rrsv_teacher AS t ON c.user_type = 'teacher' AND c.user_id = t.id
                        LEFT JOIN rrsv_student_registration AS s ON c.user_type = 'student' AND c.user_id = s.id where (c.student_id IN ($id_list) )
                        ORDER BY c.created_at asc limit 5
                    ");
                   }else{
                        $comments = mysqli_query($con, "
                        SELECT c.*, 
                               CASE 
                                    WHEN c.user_type = 'teacher' THEN t.full_name
                                    WHEN c.user_type = 'student' THEN s.scl_name
                               END AS user_name
                        FROM rrsv_behaviour_comments AS c
                        LEFT JOIN rrsv_teacher AS t ON c.user_type = 'teacher' AND c.user_id = t.id
                        LEFT JOIN rrsv_student_registration AS s ON c.user_type = 'student' AND c.user_id = s.id where user_id='$user_id'
                        ORDER BY c.created_at asc limit 5");
                   }
                    while ($c = mysqli_fetch_assoc($comments)):
                        if ($c['user_type'] == 'teacher'){ 
                    ?>
                    
                    
                  <div class="direct-chat-msg">
                      
                  

                      
                      
                     
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-left"><?= ucfirst($c['user_name']) ?></span>
                      <span class="direct-chat-timestamp float-right"><?= date('H:i', strtotime($c['created_at'])) ?></span>
                    </div>
                    <!-- /.direct-chat-infos -->
                   <button type="button" class="btn btn-default btn-sm" onclick="openForm(<?= $c['behaviour_id'] ?>)">
                    <i class="fas fa-share"></i>
                  </button>
                    <!--<img class="direct-chat-img" src="../dist/img/user1-128x128.jpg" alt="Message User Image">-->
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">
                      <?= htmlspecialchars($c['comment']) ?>
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->
 <?
                  }else{
                  ?>
                  <!-- Message to the right -->
                  <div class="direct-chat-msg right">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-right"><?= ucfirst($c['user_name']) ?></span>
                      <span class="direct-chat-timestamp float-left"><?= date('H:i', strtotime($c['created_at'])) ?></span>
                    </div>
                    <!-- /.direct-chat-infos -->
                     <button type="button" class="btn btn-default btn-sm" onclick="openForm(<?= $c['behaviour_id'] ?>)">
                    <i class="fas fa-share"></i>
                  </button>
                   
                    <!--<img class="direct-chat-img" src="../dist/img/user3-128x128.jpg" alt="Message User Image">-->
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">
                     <?= htmlspecialchars($c['comment']) ?>
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <?
                  }
                  ?>
                   <?php endwhile; ?>
                  <!-- /.direct-chat-msg -->
                </div>
                <!--/.direct-chat-messages-->

                <!-- Contacts are loaded here -->
                <div class="direct-chat-contacts">
                  <ul class="contacts-list">
                    <li>
                      <a href="#">
                        <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Avatar">

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            Count Dracula
                            <small class="contacts-list-date float-right">2/28/2015</small>
                          </span>
                          <span class="contacts-list-msg">How have you been? I was...</span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                  </ul>
                  <!-- /.contatcts-list -->
                </div>
                <!-- /.direct-chat-pane -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <form action="#" method="post">
                  <div class="input-group">
                      <div class="msger-inputarea">
                        <input type="hidden" name="behaviour_id" value="<?= $b['id'] ?>">
                        <input type="hidden" name="user_type" value="<?= $_SESSION['role'] ?>">
                        <input type="hidden" name="teacher_id" value="<?= $user_id ?>">
                        <input type="hidden" name="student_id" value="<?= $b['student_id'] ?>">
                        <textarea name="comment" class="msger-input" placeholder="Enter your message..." required></textarea>
                        <button type="submit" class="msger-send-btn">Send</button>
                      </div>
                    <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                    <span class="input-group-append">
                      <button type="submit" class="btn btn-danger">Send</button>
                    </span>
                  </div>
                </form>
              </div>
              <!-- /.card-footer-->
            </div>
            <!--/.direct-chat -->
          </div>
          <!-- /.col -->
        </div>
                    
                    
                    
                    
                    
                    
                </div>
            </div>
        </section>
        <section class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="compose.html" class="btn btn-primary btn-block mb-3">Compose</a>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Folders</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item active">
                  <a href="#" class="nav-link">
                    <i class="fas fa-inbox"></i> Inbox
                    <span class="badge bg-primary float-right">12</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-envelope"></i> Sent
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-file-alt"></i> Drafts
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="fas fa-filter"></i> Junk
                    <span class="badge bg-warning float-right">65</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-trash-alt"></i> Trash
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Labels</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-danger"></i>
                    Important
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-warning"></i> Promotions
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-primary"></i>
                    Social
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Inbox</h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Search Mail">
                  <div class="input-group-append">
                    <div class="btn btn-primary">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check1">
                        <label for="check1"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">5 mins ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check2">
                        <label for="check2"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">28 mins ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check3">
                        <label for="check3"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">11 hours ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check4">
                        <label for="check4"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">15 hours ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check5">
                        <label for="check5"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">Yesterday</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check6">
                        <label for="check6"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">2 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check7">
                        <label for="check7"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">2 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check8">
                        <label for="check8"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">2 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check9">
                        <label for="check9"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">2 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check10">
                        <label for="check10"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">2 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check11">
                        <label for="check11"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">4 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check12">
                        <label for="check12"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">12 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check13">
                        <label for="check13"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">12 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check14">
                        <label for="check14"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">14 days ago</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check15">
                        <label for="check15"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Alexander Pierce</a></td>
                    <td class="mailbox-subject"><b>AdminLTE 3.0 Issue</b> - Trying to find a solution to this problem...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">15 days ago</td>
                  </tr>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                  <i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
   
  </div>
<div class="modal fade" id="addBehaviourModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg"> <!-- wider modal -->
    <form action="student_behaviour_post.php" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Student Behaviour</h4>
        </div>
        <div class="modal-body">
          <div class="row">
              <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group">
                <label>Session</label>
                <select name="scl_session" id="scl_session" class="form-control">
                  <?php
                  $currentYear = date('Y');
                  echo "<option value='$currentYear' selected>$currentYear</option>";
                  for ($y = $currentYear - 1; $y >= ($currentYear - 5); $y--) {
                    echo "<option value='$y'>$y</option>";
                  }
                  ?>
                </select>
              </div>
              </div>
              <div class="col-md-4">
              <div class="form-group">
                <label>Class</label>
                <select name="class_id" class="form-control" id="class_id">
                  <option value="">-Select a Class-</option>
                  <?php
                  $sql = "select * from rrsv_class order by id";
                  $res = mysqli_query($con, $sql);
                  while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    ?>
                    <option value="<?php echo $obj['class_name']; ?>">
                      <?php echo $obj['class_name']; ?>
                    </option>
                    <?php
                  }
                  ?>
                </select>
              </div>
              </div>
              <div class="col-md-4">
             <div class="col-md-4 dis_month">
                <button type="button" id="find_student" class="btn btn-primary" style="margin-top:20px;">Find Student</button>
            </div>
            </div>
              
              </div>
            <!-- Left Column -->
             <div class="col-md-6">
                  <div class="form-group">
                    <label>Student</label>
                   <select name="student_id" id="student_id" class="form-control minput">
                        <option value="">--Select Student Name--</option>
                    </select>
                  </div>

              <div class="form-group">
                <label>Activties</label>
                <select name="activties" id="activties" class="form-control minput">
                                                    <option value="">--Select activties type--</option>
                                                    <option value="Behaviour">Behaviour</option>
                                                    <option value="Neatness">Neatness</option>
                                                    <option value="Discipline">Discipline</option>
                                                    <option value="Self Confidence">Self Confidence</option>
                                                    <option value="Responsibility">Responsibility</option>
                                                    <option value="Initiative">Initiative</option>
                                                    <option value="Concentration">Concentration</option>
                                                    <option value="Punctuality">Punctuality</option>
                                                    <option value="Regularity">Regularity</option>
                                                </select>
                                              </div>
                                            
                                            </div>
                                             <!-- Right Column -->
                                            <div class="col-md-6">
                                             
                                      <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control"></textarea>
                                      </div>
                                
                                      <div class="form-group">
                                      <label>Date <span class="text-danger">*</span></label>
                                      <input type="date" name="date" id="date" required class="form-control" autocomplete="off">
                                    </div>
                                </div>
                              </div>
                              <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?? ''; ?>">
                            </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>


  <?php include '../footer.php'; ?>
</div>

<!-- JS Section -->
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
<script>
   

$("#find_student").click(function(){
        var class_id = $('#class_id').val();
        var scl_session = $('#scl_session').val();
        if(scl_session == ''){
            alert('Please select a Session');
            return false;
        }
        if(class_id == '') {
            alert('Please select a Class');
            return false;
        }
        
       // alert(43535);
            $.ajax({
                url: 'getSearch.php',
                type: 'post',
                data: {scl_session:scl_session,class:class_id},
                dataType: 'json',
                success:function(response){
                    var len = response.length;
                    $("#student_id").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        $("#student_id").append("<option value='"+id+"'>"+name+"</option>");
                    }
                }
            });
});




        $('#addBehaviourModal').submit(function(e){
            //e.preventDefault();
            //alert(JSON.stringify($('form').serialize()));
            //return false;
            var teacher_id = $('#teacher_id').val();
            var student_id = $('#student_id').val();
            var date = $('#date').val();
            var des = $('textarea#exampleTextarea1').val();
            var class_id = $('#class_id').val();
            var scl_session = $('#scl_session').val();

            if(teacher_id == ''){
                alert('Please select teacher Name.');
                return false; 
            }
            if(date == ''){
                alert('Please select Date.');
                return false;
            }
            if(student_id == ''){
                alert('Please select student name');
                return false;
            }
            if(des == ''){
                alert('Please Enter description');
                return false;
            }
            if(class_id == ''){
                alert('Please select a unit');
                return false;
            }
            if(scl_session == ''){
                alert('Please select a Session');
                return false;
            }
           
                $('form').submit();

        });

function openForm(id) {
  closeAllForms();
  document.getElementById("form_" + id).style.display = "block";
}
function closeForm(id) {
  document.getElementById("form_" + id).style.display = "none";
}
function closeAllForms() {
  document.querySelectorAll('.chat-popup').forEach(el => el.style.display = 'none');
}
</script>
</body>
</html>
