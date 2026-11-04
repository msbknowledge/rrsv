<!DOCTYPE html>
<html>

<?php
session_start(); 
include_once '../dbConnection.php';
if (!isset($_SESSION['username'])) {
  # code...
  header('Location:../logout.php');
}


?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Online Exam</title>
    <link rel="icon" type="image/x-icon" href="/../msb1.ico">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="../css/font-awesome/css/font-awesome.min.css"> -->
    <!-- Ionicons -->
    <link rel="stylesheet" href="../css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../css/dataTables.bootstrap.css">
    <link rel="stylesheet" href="../css/dataTables.bootstrap.min.js">
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="../css/jquery.dataTables_themeroller.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../css/AdminLTE.min.css">

    <link rel="stylesheet" href="../css/_all-skins.min.css">
 <script src="../js/jquery-2.2.3.min.js"></script>
    <script language="javascript">
        function del_Users(uid) {
            if (confirm("Are you sure want to delete this Contact?")) {
                document.location.href = 'groupcontacts?act=del&uid=' + uid;
            }
        }
    </script>
</head>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">
        <!-- Header Start -->
        <header class="main-header">
            <!-- Logo -->
            <a href="#" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>MSB</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="../css/download.png" class="img-circle"
                        style="height:10%;width:44px"></span>


            </a>
            <!-- Header Navbar: style can be found in header.less -->


            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <!--  <span style="font-family:Calibri, Arial; font-size:10px; color:#FFFFFF;  padding-right:30px; float:right; margin-top:10px;">Support - Call Us :09246555511 <br><div align="right">040- 67978811 </div> </span> -->

                        <!-- Messages: style can be found in dropdown.less-->

                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?php
if (@$_GET['w']) {
    echo '<script>alert("' . @$_GET['w'] . '");</script>';
}

?>

                                <?php
include_once '../dbConnection.php';


if (!(isset($_SESSION['username']))) {
    header("location:../index.php");
} else {
    $name     = $_SESSION['name'];
    $username = $_SESSION['username'];
    $scl_reg_no = $_SESSION['scl_reg_no'];
    $user_id = $_SESSION['user_id'];
    
    include_once '../dbConnection.php';
    echo '<img src="../css/download.png" class="user-image" alt="User Image">
    
			  <span class="hidden-xs">'.$username . '</span>';
}
?>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="../css/download.png" class="img-circle" alt="User Image">

                                    <p>
                                        <?php                 echo 'Welcome  <small>' .$username.'('.$scl_reg_no .')</small>'; ?>
                                    </p>
                                </li>
                                <!-- Menu Body -->

                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="logout.php?q=account.php" class="btn btn-default btn-flat">Logout</a>
                                    </div>
                                </li>
                            </ul>


                        </li>
                        <!-- Control Sidebar Toggle Button -->

                    </ul>
                </div>
            </nav>


        </header><!-- Header End -->
        <!-- Left side column. contains the logo and sidebar -->

           <?php
              include_once 'uslidebar.php';
            ?>

        <!-- sidebar End -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    student


                    <small></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Exam Management</a></li>
                    <li><a href="#">Student</a></li>
                    <li class="active">Exam</li>
                </ol>
            </section>
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">HOME</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <?php
if (@$_GET['q'] == 4) {
?>

<!-- =========================================================
     DASHBOARD
========================================================= -->

<div class="col-md-12">

    <div class="box box-danger">

        <!-- =====================================================
             EXISTING ADMIN DASHBOARD CARDS
        ====================================================== -->

        <div class="box-header with-border">

            <div class="row">

                <!-- Total Students -->
                <div class="col-sm-3">

                    <div class="alert bg-green">

                        <h4>
                            Total Students
                            <i class="pull-right fa fa-users"></i>
                        </h4>

                        <span class="d-block">

                            <?php
                            $scl_session=date('Y');

                            $query = "
                                SELECT id
                                FROM rrsv_student_registration WHERE status ='Active' and scl_session='$scl_session'
                                ORDER BY id
                            ";

                            $query_run = mysqli_query($con, $query);

                            $row = mysqli_num_rows($query_run);

                            echo '<h4>' . $row . '</h4>';

                            ?>

                        </span>

                        <a href="user.php?q=1"
                           class="small-box-footer">

                            More info
                            <i class="fa fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>


                <!-- Pending Exam / Teachers -->
                <div class="col-sm-3">

                    <div class="alert bg-blue">

                        <h4>
                            Pending Exam
                            <i class="pull-right fa fa-graduation-cap"></i>
                        </h4>

                        <span class="d-block">

                            <?php

$user_id = (int) $user_id;

$query = "
    SELECT COUNT(DISTINCT qa.quiz_id) AS total
    FROM quiz_assignments qa
    INNER JOIN quiz q
        ON q.id = qa.quiz_id
    WHERE qa.student_id = $user_id
      AND qa.status = 'active'
      AND qa.r_status = 'Active'
      AND q.status = 'enabled'
      AND NOT EXISTS (
          SELECT 1
          FROM history h
          WHERE h.eid = q.eid
            AND h.user_id = $user_id
      )
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Pending Exam Query Error: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);

$pending_exams = (int) ($row['total'] ?? 0);

echo '<h4>' . $pending_exams . '</h4>';

?>
                        </span>

                        <a href=""
                           class="small-box-footer">

                            More info
                            <i class="fa fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>


                <!-- Total Exams -->
                <div class="col-sm-3">

                    <div class="alert bg-yellow">

                        <h4>
                            Exam
                            <i class="pull-right fa fa-key"></i>
                        </h4>

                        <span class="d-block">

                            <?php

                            $query = "
                                SELECT id
                                FROM quiz
                                ORDER BY id
                            ";

                            $query_run = mysqli_query($con, $query);

                            $row = mysqli_num_rows($query_run);

                            echo '<h4>' . $row . '</h4>';

                            ?>

                        </span>

                        <a href="quiz.php?q=10"
                           class="small-box-footer">

                            More info
                            <i class="fa fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>


                <!-- Results -->
                <div class="col-sm-3">

                    <div class="alert bg-red">

                        <h4>
                            Results
                            <i class="pull-right fa fa-building-o"></i>
                        </h4>

                        <span class="d-block">

                            <?php

                            $query = "
                                SELECT id
                                FROM rank
                                ORDER BY id
                            ";

                            $query_run = mysqli_query($con, $query);

                            $row = mysqli_num_rows($query_run);

                            echo '<h4>' . $row . '</h4>';

                            ?>

                        </span>

                        <a href=""
                           class="small-box-footer">

                            More info
                            <i class="fa fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>
        <!-- /.box-header -->


        <?php

$user_id = (int) $user_id;


/* =========================================================
   1. ASSIGNED EXAMS
========================================================= */

$query = "
    SELECT COUNT(DISTINCT quiz_id) AS total
    FROM quiz_assignments
    WHERE student_id = $user_id
      AND status = 'active'
      AND r_status = 'Active'
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Assigned Exams Query Error: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);

$assigned_exams = (int) ($row['total'] ?? 0);


/* =========================================================
   2. ACTIVE EXAMS
========================================================= */

$query = "
    SELECT COUNT(DISTINCT qa.quiz_id) AS total
    FROM quiz_assignments qa
    INNER JOIN quiz q
        ON q.id = qa.quiz_id
    WHERE qa.student_id = $user_id
      AND qa.status = 'active'
      AND qa.r_status = 'Active'
      AND q.status = 'enabled'
      AND NOT EXISTS (
          SELECT 1
          FROM history h
          WHERE h.eid = q.eid
            AND h.user_id = $user_id
            AND h.status = 'finished'
      )
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Active Exams Query Error: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);

$active_exams = (int) ($row['total'] ?? 0);


/* =========================================================
   3. COMPLETED EXAMS
========================================================= */

$query = "
    SELECT COUNT(DISTINCT eid) AS total
    FROM history
    WHERE user_id = $user_id
      AND status = 'finished'
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Completed Exams Query Error: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);

$completed_exams = (int) ($row['total'] ?? 0);


/* =========================================================
   4. AVERAGE SCORE
========================================================= */

$query = "
    SELECT
        ROUND(
            AVG(
                (h.score / NULLIF(q.total, 0)) * 100
            ),
            2
        ) AS average_score
    FROM history h
    INNER JOIN quiz q
        ON q.eid = h.eid
    WHERE h.user_id = $user_id
      AND h.status = 'finished'
";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Average Score Query Error: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);

$average_score = (float) ($row['average_score'] ?? 0);

?>


<!-- =========================================================
     STUDENT ONLINE EXAM DASHBOARD
========================================================= -->

<div class="box-body">

    <div class="row">


        <!-- ================================================
             ASSIGNED EXAMS
        ================================================= -->

        <div class="col-sm-3">

            <div class="small-box bg-green">

                <div class="inner">

                    <h3>
                        <?= $assigned_exams; ?>
                    </h3>

                    <p>
                        Assigned Exams
                    </p>

                </div>

                <div class="icon">

                    <i class="fa fa-book"></i>

                </div>

                

            </div>

        </div>


        <!-- ================================================
             ACTIVE EXAMS
        ================================================= -->

        <div class="col-sm-3">

            <div class="small-box bg-blue">

                <div class="inner">

                    <h3>
                        <?= $active_exams; ?>
                    </h3>

                    <p>
                        Active Exams
                    </p>

                </div>

                <div class="icon">

                    <i class="fa fa-pencil"></i>

                </div>

               

            </div>

        </div>


        <!-- ================================================
             COMPLETED EXAMS
        ================================================= -->

        <div class="col-sm-3">

            <div class="small-box bg-orange">

                <div class="inner">

                    <h3>
                        <?= $completed_exams; ?>
                    </h3>

                    <p>
                        Completed Exams
                    </p>

                </div>

                <div class="icon">

                    <i class="fa fa-check-circle"></i>

                </div>

              

            </div>

        </div>


        <!-- ================================================
             AVERAGE SCORE
        ================================================= -->

        <div class="col-sm-3">

            <div class="small-box bg-red">

                <div class="inner">

                    <h3>
                        <?= number_format($average_score, 2); ?>%
                    </h3>

                    <p>
                        Average Score
                    </p>

                </div>

                <div class="icon">

                    <i class="fa fa-bar-chart"></i>

                </div>

               
            </div>

        </div>


    </div>

</div>
            <!-- /.row -->

        </div>
        <!-- /.box-body -->


    </div>
    <!-- /.box -->

</div>
<!-- /.col-md-12 -->


<?php
}
?>
                                <?php
                                        if (@$_GET['q'] == 1) {
                                        $result = mysqli_query($con,"
                                        SELECT 
                                            q.class,
                                            qa.unit,
                                            qa.r_status,
                                            qa.status as unit_status,
                                            q.title,
                                            q.total,
                                            q.correct,
                                            q.wrong,
                                            q.time,
                                            q.eid,
                                            q.class, qa.session, q.unit
                                        FROM quiz_assignments qa
                                        JOIN quiz q ON q.id = qa.quiz_id
                                        JOIN rrsv_student_registration u ON u.id = qa.student_id

                                        WHERE u.scl_reg_no = '$scl_reg_no'
                                        AND q.status = 'enabled' AND qa.status = 'active'

                                        ORDER BY q.date DESC
                                        ") or die(mysqli_error($con));

                                            echo '<table id="example1" class="table table-bordered table-striped" >
                                                        <thead>
                                                        <tr>
                                                        <th>S.NO</th>
                                                        <th>Name</th>
                                                        <th>Class</th>
                                                        <th>Session</th>
                                                        <th>Unit</th>
                                                        <th>Total question</th>
                                                        <th>Correct</th>
                                                        <th>Wrong</th>
                                                        <th>Total Marks</th>
                                                            
                                                        <th>Time limit</th>
                                                        <th>Actions</th>
                                                        
                                                        </tr>
                                                        </thead>
                                                        <tbody>';
                                        
                                            $c = 1;
                                            while ($row = mysqli_fetch_array($result)) {
                                                $title   = $row['title'];
                                                $session   = $row['session'];
                                                $unit = $row['unit'];
                                                $total   = $row['total'];
                                                $correct = $row['correct'];
                                                $trade = $row['class'];
                                                $wrong   = $row['wrong'];
                                                $time    = $row['time'];
                                                $eid     = $row['eid'];
                                                $r_status=$row['r_status'];
                                                $q12 = mysqli_query($con, "SELECT score FROM history WHERE eid='$eid' AND username='$scl_reg_no' AND  user_id='$user_id'") or die('Error2');
                                                $rowcount = mysqli_num_rows($q12);
                                                if ($rowcount == 0) {
                                                    echo '<tr><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '</td><td style="vertical-align:middle">' . $trade . '</td><td style="vertical-align:middle">' . $unit . '</td><td style="vertical-align:middle">' . $session . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">+' . $correct . '</td><td style="vertical-align:middle">-' . $wrong . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td>
                                        <td style="vertical-align:middle"><b><a href="account.php?q=quiz&step=2&eid=' . $eid . '&n=1&t=' . $total . '&start=start" class="btn" style="color:#FFFFFF;background:darkgreen;font-size:12px;padding:7px;padding-left:10px;padding-right:10px"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>&nbsp;<span><b>Start</b></span></a></b></td></tr>';
                                                } else {
                                                    $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$eid' AND  user_id='$user_id'") or die('Error3');
                                                    while ($row = mysqli_fetch_array($q)) {
                                                        $timec  = $row['timestamp'];
                                                        $status = $row['status'];
                                                    
                                                    }
                                                    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error4');
                                                    while ($row = mysqli_fetch_array($q)) {
                                                        $ttimec  = $row['time'];
                                                        $qstatus = $row['status'];
                                                    }
                                                    $remaining = (($ttimec * 60) - ((time() - $timec)));
                                                    
                                                    if ($remaining > 0 && $qstatus == "enabled" && $status == "ongoing") {
                                                    echo  $status;
                                                        echo '<tr style="color:darkgreen"><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '&nbsp;<span title="This quiz is already solve by you" class="glyphicon glyphicon-ok" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $trade . '</td><td style="vertical-align:middle">' . $unit . '</td><td style="vertical-align:middle">' . $session . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">+' . $correct . '</td><td style="vertical-align:middle">-' . $wrong . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td>
                                        <td style="vertical-align:middle"><b><a href="account.php?q=quiz&step=2&eid=' . $eid . '&n=1&t=' . $total . '&start=start" class="btn" style="margin:0px;background:darkorange;color:white">&nbsp;<span class="title1"><b>Continue</b></span></a></b></td></tr>';
                                                    } elseif ($qstatus == "enabled" && $status == "finished" && $r_status == "Active") { 
                                                        
                                                        echo '<tr style="color:darkgreen"><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '&nbsp;<span title="This quiz is already solve by you" class="glyphicon glyphicon-ok" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $trade . '</td><td style="vertical-align:middle">' . $unit . '</td><td style="vertical-align:middle">' . $session . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">+' . $correct . '</td><td style="vertical-align:middle">-' . $wrong . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td>
                                        <td style="vertical-align:middle"><b><a href="account.php?q=result&eid=' . $eid . '" class="btn" style="margin:0px;background:darkred;color:white">&nbsp;<span class="title1"><b>View Result</b></span></a></b></td></tr>';
                                                    }else{
                                                        echo '<tr style="color:darkgreen"><td style="vertical-align:middle">' . $c++ . '</td><td style="vertical-align:middle">' . $title . '&nbsp;<span title="This quiz is already solve by you" class="glyphicon glyphicon-ok" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $trade . '</td><td style="vertical-align:middle">' . $unit . '</td><td style="vertical-align:middle">' . $session . '</td><td style="vertical-align:middle">' . $total . '</td><td style="vertical-align:middle">+' . $correct . '</td><td style="vertical-align:middle">-' . $wrong . '</td><td style="vertical-align:middle">' . $correct * $total . '</td><td style="vertical-align:middle">' . $time . '&nbsp;min</td>
                                        <td style="vertical-align:middle"><b></b></td></tr>';
                                                    }
                                                }
                                            }
                                            $c = 0;
                                            echo '<tbody></table><div class="panel" style="padding-top:1px;padding-left:15%;padding-right:15%;word-wrap:break-word"><h3 align="center" style="font-family:calibri">:: General Instructions ::</h3><br /><ul type="circle"><font style="font-size:14px;font-family:calibri">';
                                            $file = fopen("instructions.txt", "r");
                                            while (!feof($file)) {
                                                echo '<li>';
                                                $string = fgets($file);
                                                $num    = strlen($string) - 1;
                                                $c      = str_split($string);
                                                for ($i = 0; $i < $num; $i++) {
                                                    $last = $c[$i - 1];
                                                    if ($c[$i] == ' ' && $last == ' ') {
                                                        echo '&nbsp;';
                                                    } else {
                                                        echo $c[$i];
                                                    }
                                                }
                                                echo "</li><br />";
                                            }
                                            
                                            fclose($file);
                                            echo '</font></ul></div>';
                                            
                                        }
                                        ?>

                                <?php

            //if (@$_GET['q'] == 'quiz' && @$_GET['step'] == 2 && isset($_SESSION['6e447159425d2d']) && $_SESSION['6e447159425d2d'] == "6e447159425d2d" && $_GET['endquiz'] == 'end') {
            if (
                isset($_GET['q'], $_GET['step'], $_GET['endquiz']) &&
                $_GET['q'] == 'quiz' &&
                $_GET['step'] == 2 &&
                $_GET['endquiz'] == 'end' &&
                isset($_SESSION['6e447159425d2d']) &&
                $_SESSION['6e447159425d2d'] == "6e447159425d2d"
            ){
                unset($_SESSION['6e447159425d2d']);
                date_default_timezone_set('Asia/Kolkata');
                $date=date("Y-m-d");
                $time=date("h:i:s");
            //print_r($time);die();
                $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND  user_id='$user_id' ") or die('Error5');
                    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND  user_id='$user_id'") or die('Error6');
                            while ($row = mysqli_fetch_array($q)) {
                                $s = $row['score'];
                                $scorestatus = $row['score_updated'];
                            }
                        $q13 = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no='$scl_reg_no' AND id='$user_id'") or die('Error7');
                    while ($row = mysqli_fetch_array($q13)) {
                            
                            $branch   = $row['scl_class'];
                            }
                            if($scorestatus=="false"){
                                $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND  user_id='$user_id' ") or die('Error8');
                                $q = mysqli_query($con, "SELECT * FROM `rank` WHERE username='$scl_reg_no' AND  user_id='$user_id'") or die('Error9');
                                $rowcount = mysqli_num_rows($q);
                                if ($rowcount == 0) {
                                
                                
                                    $q2 = mysqli_query($con, "INSERT INTO `rank` VALUES(NULL,$user_id,'$branch','$scl_reg_no','$s','$time','$date')") or die('Error10');
                                } else {
                                    while ($row = mysqli_fetch_array($q)) {
                                        $sun = $row['score'];
                                    }
                                    
                                    $sun = $s + $sun;
                                    $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time='$time',date='$date' WHERE username= '$scl_reg_no' AND  user_id='$user_id'") or die('Error11');
                                }
                            }
                        header('location:account.php?q=result&eid='. $_GET['eid']);
            }

            if (@$_GET['q'] == 'quiz' && @$_GET['step'] == 2 && isset($_GET['start']) && $_GET['start'] == "start" && (!isset($_SESSION['6e447159425d2d']))) {
                $q = mysqli_query($con, "SELECT * FROM `history` WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die('Error12');
                
                if (mysqli_num_rows($q) > 0) {
                    $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id' ") or die('Error13');
                    while ($row = mysqli_fetch_array($q)) {
                        
                        $timel  = $row['timestamp'];
                        $status = $row['status'];
                    }
                    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$_GET[eid]' ") or die('Error14');
                    while ($row = mysqli_fetch_array($q)) {
                        $ttimel  = $row['time'];
                        $qstatus = $row['status'];
                            
                    }
                    $remaining = (($ttimel * 60) - ((time() - $timel)));
            
                    if ($status == "ongoing" && $remaining > 0 && $qstatus == "enabled") {
                        $_SESSION['6e447159425d2d'] = "6e447159425d2d";
                        header('location:account.php?q=quiz&step=2&eid='.$_GET[eid].'&n='.$_GET[n].'&t=' . $_GET[t]);
                        
                    } else {
                         //print_r($row);die();
                            $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die('Error15');
                            $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id='$user_id'") or die('Error16');
                            while ($row = mysqli_fetch_array($q)) {
                                $s = $row['score'];
                                $scorestatus = $row['score_updated'];
                            }
                            $q13 = mysqli_query($con, "SELECT * FROM rrsv_student_registration WHERE scl_reg_no='$scl_reg_no' AND id='$user_id'") or die('Error17');
                    while ($row = mysqli_fetch_array($q13)) {
                            
                            $branch   = $row['scl_class'];
                            }
                            if($scorestatus=="false"){
                                $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id' ") or die('Error18');
                                $q = mysqli_query($con, "SELECT * FROM rank WHERE username='$scl_reg_no' AND user_id='$user_id'") or die('Error161');
                                $rowcount = mysqli_num_rows($q);
                                if ($rowcount == 0) {
                                    $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,$user_id,'$branch','$scl_reg_no','$s','$time','$date')") or die('Error19');
                                } else {
                                    while ($row = mysqli_fetch_array($q)) {
                                        $sun = $row['score'];
                                    }
                                    
                                    $sun = $s + $sun;
                                    $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time='$time',date='$date' WHERE username= '$scl_reg_no' AND user_id='$user_id'") or die('Error20');
                                }
                            }
                        header('location:account.php?q=result&eid=' . $_GET[eid]);
                    }
                    
                } else {
                   // print_r($user_id);die();
                date_default_timezone_set('Asia/Kolkata');

                $date = date("Y-m-d");
                $tim  = date("H:i:s");
                $time = time();
                
                $eid = isset($_GET['eid']) ? mysqli_real_escape_string($con, $_GET['eid']) : '';
                $sql = "INSERT INTO history (user_id, username, eid, score, level, correct, wrong, date, time, timestamp, status, score_updated) VALUES ('$user_id', '$scl_reg_no', '$eid', 0, 0, 0, 0, '$date', '$tim', '$time', 'ongoing', 'false')";
                
                $q = mysqli_query($con, $sql);



                    $_SESSION['6e447159425d2d'] = "6e447159425d2d";
                    header('location:account.php?q=quiz&step=2&eid=' . $_GET['eid'] . '&n=' . $_GET['n'] . '&t=' . $_GET['t']);
                }
            }
            //exam

            if (@$_GET['q'] == 'quiz' && @$_GET['step'] == 2 && isset($_SESSION['6e447159425d2d']) && $_SESSION['6e447159425d2d'] == "6e447159425d2d") {
                $eid = $_GET['eid']; // ✅ সহজভাবে আগে নিয়ে নাও
                $sn    = $_GET['n'];
                $total = $_GET['t'];

                    if (isset($_GET['endquiz']) && $_GET['endquiz'] == 'end') {
                    
                        unset($_SESSION['6e447159425d2d']);
                    
                        mysqli_query($con, "UPDATE history
                            SET status='finished'
                            WHERE username='$scl_reg_no'
                            AND eid='$eid'
                            AND user_id='$user_id'");
                    
                        // Rank update code এখানে থাকবে
                        date_default_timezone_set('Asia/Kolkata');
                        $date = date("Y-m-d");
                        $time = date("H:i:s");
                        
                        $q55 = mysqli_query($con, "SELECT score,score_updated
                        FROM history
                        WHERE username='$scl_reg_no'
                        AND eid='$eid'
                        AND user_id='$user_id'");
                        
                        $row55 = mysqli_fetch_assoc($q55);
                        
                        $s = $row55['score'];
                        $scorestatus = $row55['score_updated'];
                        
                        $q13 = mysqli_query($con, "SELECT scl_class
                        FROM rrsv_student_registration
                        WHERE scl_reg_no='$scl_reg_no'
                        AND id='$user_id'");
                        
                        $row13 = mysqli_fetch_assoc($q13);
                        $branch = $row13['scl_class'];
                        
                        if($scorestatus == "false"){
                        
                            mysqli_query($con,"UPDATE history
                            SET score_updated='true'
                            WHERE username='$scl_reg_no'
                            AND eid='$eid'
                            AND user_id='$user_id'");
                        
                            $rank = mysqli_query($con,"SELECT score
                            FROM rank
                            WHERE username='$scl_reg_no'
                            AND user_id='$user_id'");
                        
                            if(mysqli_num_rows($rank)==0){
                        
                                mysqli_query($con,"INSERT INTO rank
                                VALUES(NULL,'$user_id','$branch','$scl_reg_no','$s','$time','$date')");
                        
                            }else{
                        
                                $r = mysqli_fetch_assoc($rank);
                        
                                $newScore = $r['score'] + $s;
                        
                                mysqli_query($con,"UPDATE rank
                                SET score='$newScore',
                                    time='$time',
                                    date='$date'
                                WHERE username='$scl_reg_no'
                                AND user_id='$user_id'");
                            }
                        }
                    
                        header("Location: account.php?q=result&eid=".$eid);
                        exit;
                    }
                $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$eid' AND user_id='$user_id' ") or die('Error22');
                
                if (mysqli_num_rows($q) > 0) {
                    $q = mysqli_query($con, "SELECT * FROM `history` WHERE username='$scl_reg_no' AND eid='$eid' AND user_id='$user_id'") or die('Error23');
                    while ($row = mysqli_fetch_array($q)) {
                        $time   = $row['timestamp'];
                        $status = $row['status'];
                    }
                    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid'") or die('Error24');
                    while ($row = mysqli_fetch_array($q)) {
                        $ttime   = $row['time'];
                        $qstatus = $row['status'];
                    }
                    $remaining = (($ttime * 60) - ((time() - $time)));
                    if ($status == "ongoing" && $remaining > 0 && $qstatus == "enabled") {
                        $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$eid' AND user_id='$user_id'") or die('Error25');
                        while ($row = mysqli_fetch_array($q)) {
                            $time = $row['timestamp'];
                        }
                        $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid'") or die('Error26');
                        while ($row = mysqli_fetch_array($q)) {
                            $ttime = $row['time'];
                        }
                        $remaining = (($ttime * 60) - ((time() - $time)));
                        echo '<script>
                            var seconds = ' . $remaining . ';
                            function end(e) {
                                if (e) e.preventDefault();

                                var data = confirm("Are you sure to finish this Exam?");
                                if (data) {
                                    window.location = "account.php?q=result&eid=' . $_GET['eid'] . '";
                                }
                            }
                            function enable() {
                                document.getElementById("sbutton").removeAttribute("disabled");
                                 // Auto submit
                                document.getElementById("qform").submit();

                            }
                            function frmreset() {
                                document.getElementById("sbutton").setAttribute("disabled", "true");
                                document.getElementById("qform").reset();
                            }
                            function secondPassed() {
                                var minutes = Math.round((seconds - 30) / 60);
                                var remainingSeconds = seconds % 60;
                                if (remainingSeconds < 10) {
                                    remainingSeconds = "0" + remainingSeconds;
                                }
                                document.getElementById(\'countdown\').innerHTML = minutes + ":" +    remainingSeconds;
                            if (seconds <= 0) {
                                    clearInterval(countdownTimer);
                                    document.getElementById(\'countdown\').innerHTML = "Time Over";
                                    // Auto submit
                                    window.location.href = "account.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'&endquiz=end";
                                } else {
                                    seconds--;
                                }
                            }
                            var countdownTimer = setInterval(\'secondPassed()\', 1000);


                        </script>';
                        // echo '<font size="3" style="margin-left:100px;font-family:\'typo\' font-size:20px; font-weight:bold;color:darkred">Time Left : </font><span class="timer btn btn-default" style="margin-left:20px;"><font style="font-family:\'typo\';font-size:20px;font-weight:bold;color:darkblue" id="countdown"></font></span><span class="timer btn btn-primary" style="margin-left:50px" onclick="end(event)"><span class=" glyphicon glyphicon-off"></span>&nbsp;&nbsp;<font style="font-size:12px;font-weight:bold">Finish Exam</font></span>';
echo '<div class="row">
            <div class="col-md-10">
                <font size="3" style="margin-left:100px;font-family:\'typo\';font-size:20px;font-weight:bold;color:darkred">
                Time Left :
                </font>
    
                <span class="timer btn btn-default" style="margin-left:20px;">
                    <font style="font-family:\'typo\';font-size:20px;font-weight:bold;color:darkblue" id="countdown"></font>
                </span>
    
                <a href="account.php?q=quiz&step=2&eid='.$_GET['eid'].'&n='.$_GET['n'].'&t='.$_GET['t'].'&endquiz=end"
                class="timer btn btn-primary"
                style="margin-left:50px"
                onclick="return confirm(\'Are you sure to finish this Exam?\')">
    
                <span class="glyphicon glyphicon-off"></span>
                &nbsp;&nbsp;
                <font style="font-size:12px;font-weight:bold">Finish Exam</font>
    
                </a>';
    
                $eid   = @$_GET['eid'];
                $sn    = @$_GET['n'];
                $total = @$_GET['t'];
                $q     = mysqli_query($con, "SELECT * FROM questions WHERE eid='$eid' AND sn='$sn' ");
                
                echo '<div class="panel" style="margin-right:5%;margin-left:5%;margin-top:10px;border-radius:10px">';
                while ($row = mysqli_fetch_array($q)) {
                    $qns = stripslashes($row['qns']);
                    $qid = $row['qid'];
                     $question_type = $row['question_type'];
                    echo '<b><pre style="background-color:white"><div style="font-size:20px;font-weight:bold;font-family:calibri;margin:10px">' . $sn . ' : ' . $qns . '</div></pre></b>';
                }
                
                echo '<form id="qform" name="qform" action="update.php?q=quiz&step=2&eid=' . $eid . '&n=' . $sn . '&t=' . $total . '&qid=' . $qid . '" method="POST"  class="form-horizontal"><br />';
                $q = mysqli_query($con, "SELECT * FROM user_answer WHERE qid='$qid' AND username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die("Error27");
                if (mysqli_num_rows($q) > 0) {
                    $row = mysqli_fetch_array($q);
                    $ans = $row['ans'];
                    $q = mysqli_query($con, "SELECT * FROM options WHERE qid='$qid' AND optionid='$ans'") or die("Error28");
                    $row = mysqli_fetch_array($q);
                    $ans = $row['option'];
                } else {
                    $ans = "";
                }
                if (strlen($ans) > 0) {
                    echo "<font style=\"color:green;font-size:12px;font-weight:bold\">Selected answer: </font><font style=\"color:#565252;font-size:12px;\">" . $ans . "</font>&nbsp;&nbsp;<br /><br />";
                    
                    // <a href=update.php?q=quiz1&step=2&eid=$eid&n=$sn&t=$total&qid=$qid&delanswer=delanswer><span class=\"glyphicon glyphicon-remove\" style=\"font-size:12px;color:darkred\"></span></a>
                }
                echo '<div class="funkyradio">';
                $q = mysqli_query($con, "SELECT * FROM options WHERE qid='$qid' ");
                $q5 = mysqli_query($con, "SELECT * FROM user_answer WHERE qid='$qid' AND username='$scl_reg_no' AND eid='$eid' AND user_id='$user_id'");
                    
                          
                $selectedOption = "";
                
                if(mysqli_num_rows($q5)){
                    $row5 = mysqli_fetch_assoc($q5);
                    $selectedOption = trim($row5['ans']);
                }
                while ($row = mysqli_fetch_array($q)) {
                    $option   = stripslashes($row['option']);
                    $optionid = $row['optionid'];
                   $checked = ($selectedOption == $optionid) ? 'checked' : '';
                    if ($question_type == 'image') {

           
                    echo '<div class="funkyradio-success">
            
                            <input type="radio"
                                   id="'.$optionid.'"
                                   name="ans"
                                   value="'.$optionid.'" '.$checked.'
                                   onclick="enable()">
            
                            <label for="'.$optionid.'" style="width:220px">
            
                                <img src="../uploads/exam/'.$option.'"
                                     style="width:180px;
                                            height:180px;
                                            object-fit:contain;
                                            border:1px solid #ccc;
                                            padding:5px;
                                            background:#fff;">
            
                            </label>
            
                          </div>';
            
                            } else {
                            echo '<div class="funkyradio-success"><input type="radio" id="' . $optionid . '" name="ans" value="' . $optionid . '" '.$checked.' onclick="enable()"> <label for="' . $optionid . '" style="width:50%"><div style="color:black;font-size:12px;word-wrap:break-word">&nbsp;&nbsp;' . $option . '</div></label></div>';
                }
            }
            echo '</div>';
            if ($_GET['t'] > $_GET['n'] && $_GET['n'] != 1) {
                echo '<br /><a href="account.php?q=quiz&step=2&eid=' . $eid . '&n=' . ($sn - 1) . '&t=' . $total . '" class="btn btn-primary" style="height:30px"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"  style="font-size:12px"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-default" disabled="true" id="sbutton" style="height:30px"><span class="glyphicon glyphicon-lock" style="font-size:12px" aria-hidden="true"></span><font style="font-size:12px;font-weight:bold"> Save</font></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-default" onclick="frmreset()" style="height:30px"></span><font style="font-size:12px;font-weight:bold">Reset</font></button>&nbsp;&nbsp;&nbsp;&nbsp;<a href="account.php?q=quiz&step=2&eid=' . $eid . '&n=' . ($sn + 1) . '&t=' . $total . '" id="idsub" class="btn btn-primary" style="height:30px"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"  style="font-size:12px"></span></a></form><br><br>';
            } else if ($_GET['t'] == $_GET['n']) {
                echo '<br /><a href="account.php?q=quiz&step=2&eid=' . $eid . '&n=' . ($sn - 1) . '&t=' . $total . '" class="btn btn-primary" style="height:30px"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"  style="font-size:12px"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-default" disabled="true" id="sbutton" style="height:30px"><span class="glyphicon glyphicon-lock" style="font-size:12px" aria-hidden="true"></span><font style="font-size:12px;font-weight:bold">Save</font></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-default" onclick="frmreset()" style="height:30px"></span><font style="font-size:12px;font-weight:bold">Reset</font></button>&nbsp;&nbsp;&nbsp;&nbsp;</form><br><br>';
            } else if ($_GET['t'] > $_GET['n'] && $_GET['n'] == 1) {
                echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-default" disabled="true" id="sbutton" style="height:30px"><span class="glyphicon glyphicon-lock" style="font-size:12px" aria-hidden="true"></span><font style="font-size:12px;font-weight:bold"> Save<font></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-default" onclick="frmreset()" style="height:30px"></span><font style="font-size:12px;font-weight:bold">Reset</font></button>&nbsp;&nbsp;&nbsp;&nbsp;<a href="account.php?q=quiz&step=2&eid=' . $eid . '&n=' . ($sn + 1) . '&t=' . $total . '" id="idsub" class="btn btn-primary" style="height:30px"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"  style="font-size:12px"></span></a></form><br><br>';
            } else {
            }
            echo '</div>
            </div>';
          echo '<div class="col-md-2">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                          <h3 class="box-title">Instruction To User</h3>
            				<div class="box-tools pull-right">
            				</div>
                        </div>
                        <div class="box-body">
                            <div class="timeline-body">';
                                        $q = mysqli_query($con, "SELECT * FROM questions WHERE eid='$_GET[eid]'") or die("Error29");
                                        $i = 1;
                                        while ($row = mysqli_fetch_array($q)) {
                                            $ques[$row['qid']] = $i;
                                            $i++;
                                        }
                                        $q = mysqli_query($con, "SELECT * FROM user_answer WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id='$user_id'") or die("Error30");
                                        $i = 1;
                                        while ($row = mysqli_fetch_array($q)) {
                                            if (isset($ques[$row['qid']])) {
                                                $quesans[$ques[$row['qid']]] = true;
                                            }
                                        }
                                        for ($i = 1; $i <= $total; $i++) {
                                             if ($i == $sn) {
                                                $color = "darkblue";          // Current Question
                                                } elseif (isset($quesans[$i])) {
                                                    $color = "darkgreen";       // Answered
                                                } elseif ($i < $sn) {
                                                    $color = "orange";
                                                } else {
                                                    $color = "darkred";         // Unattempted
                                                }
                                            echo '<a href="account.php?q=quiz&step=2&eid='.$eid.'&n='.$i.'&t='.$total.'"
            style="
                float:left;
                margin:5px;
                padding:5px 10px;
                background-color:'.$color.';
                color:#fff;
                font-size:16px;
                font-family:calibri;
                border-radius:4px;
                text-decoration:none;">
            '.$i.'
          </a>';
                                        }
                    echo ' </div> </div> </div> </div></div></div>';
                                    } else {
                                        unset($_SESSION['6e447159425d2d']);
                                        $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die('Error31');
                                    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id='$user_id'") or die('Error32');
                                            while ($row = mysqli_fetch_array($q)) {
                                                $s = $row['score'];
                                                $scorestatus = $row['score_updated'];
                                            }
                                             if($scorestatus=="false"){
                                                $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die('Error33');
                                                $q = mysqli_query($con, "SELECT * FROM `rank` WHERE username='$scl_reg_no' AND user_id='$user_id'") or die('Error34');
                                                $rowcount = mysqli_num_rows($q);
                                                if ($rowcount == 0) {
                                                    $q2 = mysqli_query($con, "INSERT INTO `rank` VALUES(NULL,$user_id,'','$scl_reg_no','$s','$time','$date')") or die('Error35');
                                                } else {
                                                    while ($row = mysqli_fetch_array($q)) {
                                                        $sun = $row['score'];
                                                    }
                                                    
                                                    $sun = $s + $sun;
                                                    $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time='$time',date='$date' WHERE username= '$scl_reg_no' AND user_id='$user_id'") or die('Error36');
                                                }
                                            }
                                        header('location:account.php?q=result&eid=' . $_GET[eid]);
                                    }
                                } else {
                                    unset($_SESSION['6e447159425d2d']);
                                    $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die('Error37');
                                    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id='$user_id'") or die('Error38');
                                            while ($row = mysqli_fetch_array($q)) {
                                                $s = $row['score'];
                                                $scorestatus = $row['score_updated'];
                                            }
                                            if($scorestatus=="false"){
                                                $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id='$user_id'") or die('Error39');
                                                $q = mysqli_query($con, "SELECT * FROM rank WHERE username='$scl_reg_no' AND user_id='$user_id'") or die('Error40');
                                                $rowcount = mysqli_num_rows($q);
                                                if ($rowcount == 0) {
                                                    $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,$user_id,'','$scl_reg_no','$s','$time','$date')") or die('Error41');
                                                } else {
                                                    while ($row = mysqli_fetch_array($q)) {
                                                        $sun = $row['score'];
                                                    }
                                                    
                                                    $sun = $s + $sun;
                                                    $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time='$time',date='$date' WHERE username= '$scl_reg_no' AND user_id='$user_id'") or die('Error42');
                                                }
                                            }
                                        header('location:account.php?q=result&eid=' . $_GET['eid']);
                                }
                            }
                            
                            
    
// test result

if (@$_GET['q'] == 'result' && @$_GET['eid'] && @$_GET['quiz']!='review') {
    $eid = @$_GET['eid'];
    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid'") or die('Error43');
    $total = 0;
    while ($row = mysqli_fetch_array($q)) {
        $total = $row['total'];
    }
    $q = mysqli_query($con, "
        SELECT 
            h.score,
            h.wrong,
            h.correct,
            h.status,
            qa.r_status
        FROM history h
        INNER JOIN quiz q 
            ON q.eid = h.eid
        INNER JOIN quiz_assignments qa
            ON qa.quiz_id = q.id
            AND qa.student_id = h.user_id
        WHERE 
            h.eid='$eid'
            AND h.username='$scl_reg_no' ") or die('Error44');
     $s = $w = $r = 0;
    $status = '';
    $r_status = '';
    while ($row = mysqli_fetch_array($q)) {
        $s      = $row['score'];
        $w      = $row['wrong'];
        $r      = $row['correct'];
        $status = $row['status'];
        $r_status = $row['r_status'];
    }
    if ($status == "finished") {
        echo '<div class="panel">
                <center><h1 class="title" style="color:#660033">Result</h1><center>
                <br />
                <table class="table table-striped title1" style="font-size:20px;font-weight:1000;">';
                        echo '<tr style="color:darkblue"><td style="vertical-align:middle">Total Questions</td><td style="vertical-align:middle">' . $total . '</td></tr>
                      <tr style="color:darkgreen"><td style="vertical-align:middle">Correct Answer&nbsp;<span class="glyphicon glyphicon-ok-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">'. ($r_status == 'Active' ? $r : 'Process') .  '</td></tr> 
                    <tr style="color:red"><td style="vertical-align:middle">Wrong Answer&nbsp;<span class="glyphicon glyphicon-remove-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($r_status == 'Active' ? $w : 'Process') . '</td></tr>
                    
                    <tr style="color:orange"><td style="vertical-align:middle">Unattempted&nbsp;<span class="glyphicon glyphicon-ban-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($total - $r - $w) . '</td></tr>
                    <tr style="color:lime"><td style="vertical-align:middle">Attempted&nbsp;<span class="glyphicon glyphicon-ban-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($r + $w) . '</td></tr>
                    <tr style="color:darkblue"><td style="vertical-align:middle">Score&nbsp;<span class="glyphicon glyphicon-star" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($r_status == 'Active' ? $s : 'Process') . '</td></tr>';
                        $q = mysqli_query($con, "SELECT * FROM `rank` WHERE  username='$scl_reg_no'") or die('Error157');
                        while ($row = mysqli_fetch_array($q)) {
                            $s = $row['score'];
                            echo '<tr style="color:#990000"><td style="vertical-align:middle">Overall Score&nbsp;<span class="glyphicon glyphicon-stats" aria-hidden="true"></span></td><td style="vertical-align:middle">' .($r_status == 'Active' ?$overall_score:' Process') . '</td></tr></table></div>';
                        }
                    }
                }



//full result

if (@$_GET['q'] == 'review' && @$_GET['eid'] && @$_GET['quiz']=='review') {
    $eid = @$_GET['eid'];
    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error157');
    while ($row = mysqli_fetch_array($q)) {
        $total = $row['total'];
    }
    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$scl_reg_no' AND user_id='$user_id'") or die('Error157');
    
    while ($row = mysqli_fetch_array($q)) {
        $s      = $row['score'];
        $w      = $row['wrong'];
        $r      = $row['correct'];
        $status = $row['status'];
    }
    if ($status == "finished") {
        echo '<div class="panel">
<center><h1 class="title" style="color:#660033">Result</h1><center><br /><table class="table table-striped title1" style="font-size:20px;font-weight:1000;">';
        echo '<tr style="color:darkblue"><td style="vertical-align:middle">Total Questions</td><td style="vertical-align:middle">' . $total . '</td></tr>
      <tr style="color:darkgreen"><td style="vertical-align:middle">Correct Answer&nbsp;<span class="glyphicon glyphicon-ok-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $r . '</td></tr> 
    <tr style="color:red"><td style="vertical-align:middle">Wrong Answer&nbsp;<span class="glyphicon glyphicon-remove-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $w . '</td></tr>
    <tr style="color:orange"><td style="vertical-align:middle">Unattempted&nbsp;<span class="glyphicon glyphicon-ban-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($total - $r - $w) . '</td></tr>
    <tr style="color:darkblue"><td style="vertical-align:middle">Score&nbsp;<span class="glyphicon glyphicon-star" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $s . '</td></tr>';
        $q = mysqli_query($con, "SELECT * FROM rank WHERE  username='$scl_reg_no' ") or die('Error157');
        while ($row = mysqli_fetch_array($q)) {
            $s = $row['score'];
            echo '<tr style="color:#990000"><td style="vertical-align:middle">Overall Score&nbsp;<span class="glyphicon glyphicon-stats" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $s . '</td></tr>';
        }
        echo '<tr></tr></table></div><div class="panel"><br /><h3 align="center" style="font-family:calibri">:: Detailed Analysis ::</h3><br /><ol style="font-size:20px;font-weight:bold;font-family:calibri;margin-top:20px">';
        $q = mysqli_query($con, "SELECT * FROM questions WHERE eid='$_GET[eid]'") or die('Error197');
        while ($row = mysqli_fetch_array($q)) {
            $question = $row['qns'];
            $qid      = $row['qid'];
            $question_type = $row['question_type'];
            $q2 = mysqli_query($con, "SELECT * FROM user_answer WHERE eid='$_GET[eid]' AND qid='$qid' AND username='$scl_reg_no' AND user_id='$user_id'") or die('Error197');
            if (mysqli_num_rows($q2) > 0) {
                $row1         = mysqli_fetch_array($q2);
                $ansid        = $row1['ans'];
                $correctansid = $row1['correctans'];
                $q3 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$ansid'") or die('Error197');
                $q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$correctansid'") or die('Error197');
                $row2       = mysqli_fetch_array($q3);
                $row3       = mysqli_fetch_array($q4);
                $ans        = $row2['option'];
                $correctans = $row3['option'];
            } else {
                $q3 = mysqli_query($con, "SELECT * FROM answer WHERE qid='$qid'") or die('Error197');
                $row1         = mysqli_fetch_array($q3);
                $correctansid = $row1['ansid'];
                $q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$correctansid'") or die('Error197');
                $row2       = mysqli_fetch_array($q4);
                $correctans = $row2['option'];
                $ans        = "Unanswered";
            }
            if ($question_type == 'image') {

    if ($correctans == $ans && $ans != "Unanswered") {

        echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:lightgreen;padding:10px;word-wrap:break-word;border-radius:10px;">'.$question.' <span class="glyphicon glyphicon-ok" style="color:darkgreen"></span></div>';

        echo '<font style="font-size:14px;color:darkgreen"><b>Your Answer:</b></font><br>';
        echo '<img src="../uploads/exam/'.$ans.'" width="150" height="150" style="border:1px solid #ccc;padding:5px;"><br><br>';

        echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer:</b></font><br>';
        echo '<img src="../uploads/exam/'.$correctans.'" width="150" height="150" style="border:2px solid green;padding:5px;">';

    }
    else if ($ans == "Unanswered") {

        echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f7f576;padding:10px;word-wrap:break-word;border-radius:10px;">'.$question.'</div>';

        echo '<font style="font-size:14px;color:#ff0000"><b>Your Answer:</b></font> Unattempted<br><br>';

        echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer:</b></font><br>';
        echo '<img src="../uploads/exam/'.$correctans.'" width="150" height="150" style="border:2px solid green;padding:5px;">';

    }
    else {

        echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f99595;padding:10px;word-wrap:break-word;border-radius:10px;">'.$question.' <span class="glyphicon glyphicon-remove" style="color:red"></span></div>';

        echo '<font style="font-size:14px;color:#ff0000"><b>Your Answer:</b></font><br>';
        echo '<img src="../uploads/exam/'.$ans.'" width="150" height="150" style="border:1px solid #ccc;padding:5px;"><br><br>';

        echo '<font style="font-size:14px;color:#069706"><b>Correct Answer:</b></font><br>';
        echo '<img src="../uploads/exam/'.$correctans.'" width="150" height="150" style="border:2px solid green;padding:5px;">';

    }

} else {
            if ($correctans == $ans && $ans != "Unanswered") {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:lightgreen;padding:10px;word-wrap:break-word;border-radius:10px;">' . $question . ' <span class="glyphicon glyphicon-ok" style="color:darkgreen"></span></div>';
                echo '<font style="font-size:14px;color:darkgreen"><b>Your Answer: </b></font><font style="font-size:14px;">' . $ans . '</font><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font>';
            } 
            else if ($ans == "Unanswered") {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f7f576;padding:10px;word-wrap:break-word;border-radius:10px;">' . $question . ' </div>';
                 echo '<font style="font-size:14px;color:#ff0000"><b>Your Answer: </b></font><font style="font-size:14px;">Unattempted</font><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font>';
            } 
            else {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f99595;padding:10px;word-wrap:break-word;border-radius:10px;">' . $question . ' <span class="glyphicon glyphicon-remove" style="color:red"></span></div>';
                echo '<font style="font-size:14px;color:#ff0000"><b>Your Answer: </b></font><font style="font-size:14px;">' . $ans . '</font><br />';
                echo '<font style="font-size:14px;color:#069706"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font>';
                
            }
}
            echo "<br /></li>";
        }
        echo '</ol>';
        echo "</div>";
    } else {
        die("Thats a 404 Error bro. You are trying to access a wrong page");
    }
}


if (@$_GET['q'] == 2) {
 

$q = mysqli_query($con,"
SELECT h.*, q.title, q.total, qa.status, qa.r_status, u.scl_name, u.scl_class FROM history h INNER JOIN quiz q ON q.eid = h.eid LEFT JOIN quiz_assignments qa ON qa.quiz_id = q.id AND qa.student_id = h.user_id INNER JOIN rrsv_student_registration u ON u.id = h.user_id 
WHERE h.username='$scl_reg_no'
AND h.user_id='$user_id'
AND h.status='finished'
ORDER BY h.date DESC
") or die(mysqli_error($con));


echo '<table id="example1" class="table table-bordered table-striped">
<thead>
<tr>
<th>SL.NO</th>
<th>Quiz</th>
<th>Total Questions</th>
<th>Right</th>
<th>Wrong</th>
<th>Unattempted</th>
<th>Score</th>
<th>Action</th>
</tr>
</thead>
<tbody>';

$c = 0;

while ($row = mysqli_fetch_array($q)) {

    $eid   = $row['eid'];
    $title = $row['title'];
    $total = $row['total'];
    $s     = $row['score'];
    $w     = $row['wrong'];
    $r     = $row['correct'];

    $c++;
$r_status = $row['r_status'];
    echo '<tr>
    <td>'.$c.'</td>
    <td>'.$title.'</td>
    <td>'.$total.'</td>
    <td>'.($r_status == 'Active' ? $r:'---').'</td>
    <td>'.($r_status == 'Active' ? $w:'---').'</td>
    <td>'.($total - $r - $w).'</td>
    <td>'.($r_status == 'Active' ? $s : '---').'</td>
    <td>';
    if ($r_status == 'Active') {
        //q=review&eid='.$eid.'&quiz=review"
        echo '<a href="account.php?q=result&eid='.$eid.'"
             class="btn btn-success btn-sm">
            View Result
          </a>';
    } else {
    echo '<span class="label label-warning">Result Pending</span>';
        }
   echo '</td>
    </tr>';
}

echo '</tbody></table>';

 
}


if (@$_GET['q'] == 3) {
$limit = 10;
$page = isset($_GET['show']) ? (int)$_GET['show'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT h.username,q.unit,s.scl_name,s.scl_class,SUM(h.score) AS total_score,COUNT(DISTINCT h.eid) AS total_exam FROM history h INNER JOIN quiz q ON q.eid=h.eid INNER JOIN quiz_assignments qa ON qa.quiz_id=q.id AND qa.student_id=h.user_id LEFT JOIN rrsv_student_registration s ON s.scl_reg_no=h.username WHERE h.status='finished' AND qa.r_status='active' GROUP BY h.username,s.scl_name,s.scl_class ORDER BY total_score DESC LIMIT $offset,$limit;";

$result = mysqli_query($con, $sql);

$c = $offset;

echo '<table id="example1" class="table table-bordered table-striped" >';
echo '<thead>
<tr>
<th>SL No</th>

<th>Name</th>
<th>Reg No</th>
<th>Class</th>
<th>Total Exam</th>
<th>Score</th>
</tr>
</thead><tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    $c++;

   echo "<tr>
        <td>$c</td>
        <td>{$row['scl_name']}</td>
        <td>{$row['username']}</td>
        <td>{$row['scl_class']}</td>
        <td>{$row['total_exam']}</td>
        <td>{$row['total_score']}</td>
    </tr>";
}

echo '</tbody></table>';


  
}
//window.location = "account.php?q=quiz&step=2&eid='.$_GET['eid'].'&n='.$_GET['n'].'&t='.$_GET['t'].'&endquiz=end";
?>

            <!-- /.col -->
        </div>
        <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- Footer Start -->
        <footer class="main-footer" style="text-align:center;">



            <strong>Copyright &copy; 2013-2021 </strong> All rights

            reserved.

        </footer> <!-- Footer End -->
        </div>
         <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar --> 
        <!-- ./wrapper -->
        <!-- jQuery 2.2.3 -->
       
        <!-- Bootstrap 3.3.6 -->
        <script src="../js/bootstrap.min.js"></script>
        <!-- DataTables -->
        <script src="../js/jquery.dataTables.min.js"></script>
        <script src="../js/dataTables.bootstrap.min.js"></script>
        <!-- SlimScroll -->
        <script src="../js/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="../js/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="../js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../js/demo.js"></script>
        <!-- page script -->
        <script>
            $(function () {
                $("#example1").DataTable();
                $('#example2').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false
                });
            });


            // $(document).click(function () {
            //     $("#qform").submit();
            // });
        </script>
        
</body>
</html>