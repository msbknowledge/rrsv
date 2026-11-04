<?php
include_once 'dbConnection.php';

session_start();
$username = $_SESSION['username'];

//disabled quiz
if (isset($_SESSION['key'])) {
    if (@$_GET['deidquiz'] && $_SESSION['key'] == '66552277T') {
        $eid = @$_GET['deidquiz'];
        $r1 = mysqli_query($con, "UPDATE quiz SET status='disabled' WHERE eid='$eid' ") or die('Error');
        header("location:quiz.php?q=10");
    }
}
//enableed quiz 
if (isset($_SESSION['key'])) {
    if (@$_GET['eeidquiz'] && $_SESSION['key'] == '66552277T') {
        $eid = @$_GET['eeidquiz'];
        $r1 = mysqli_query($con, "UPDATE quiz SET status='enabled' WHERE eid='$eid' ") or die('Error');
        header("location:quiz.php?q=10");
    }
}

//student exam enable  for due
if (isset($_SESSION['key'])) {
    if (@$_GET['edue'] && $_SESSION['key'] == '66552277T') {
        $eid = @$_GET['edue'];
        $d1 = mysqli_query($con, "UPDATE user SET due='0' WHERE username='$eid' ") or die('Error');
        header("location:user.php?q=1");
    }
}
//student exam  disable for due
if (isset($_SESSION['key'])) {
    if (@$_GET['ddue'] && $_SESSION['key'] == '66552277T') {
        $eid = @$_GET['ddue'];
        $d1 = mysqli_query($con, "UPDATE user SET due='1' WHERE username='$eid' ") or die('Error');
        header("location:user.php?q=1");
    }
}


//delete quiz & question & options
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'rmquiz' && $_SESSION['key'] == '66552277T') {
        $eid = @$_GET['eid'];
        $result = mysqli_query($con, "SELECT * FROM questions WHERE eid='$eid' ") or die('Error');
        while ($row = mysqli_fetch_array($result)) {
            $qid = $row['qid'];
            $r1 = mysqli_query($con, "DELETE FROM options WHERE qid='$qid'") or die('Error');
            $r2 = mysqli_query($con, "DELETE FROM answer WHERE qid='$qid' ") or die('Error');
        }
        
        $r3 = mysqli_query($con, "DELETE FROM questions WHERE eid='$eid' ") or die('Error');
        $r4 = mysqli_query($con, "DELETE FROM quiz WHERE eid='$eid' ") or die('Error');
        $r4 = mysqli_query($con, "DELETE FROM history WHERE eid='$eid' ") or die('Error');
        header("location:quiz.php?q=10");
    }
}
//add quiz
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'addquiz' && $_SESSION['key'] == '66552277T') {
        $name    = $_POST['name'];
        $name    = ucwords(strtolower($name));
        $total   = $_POST['total'];
        $correct = $_POST['right'];
        $wrong   = $_POST['wrong'];
        $time    = $_POST['time'];
        $trade    = $_POST['trade'];
        $status  = "disabled";
        $id      = uniqid();
        $q3      = mysqli_query($con, "INSERT INTO quiz VALUES(NULL,'$id','$name','$correct','$wrong','$total','$time', NOW(),'$status','$trade')");
        header("location:quiz.php?q=10");
        //header("location:dash.php?q=4&step=2&eid=$id&n=$total");
    }
}
//edit quiz
if (isset($_SESSION['key'])) {
    if (@$_GET['editq'] && $_SESSION['key'] == '66552277T') {
        $eid = @$_GET['eid'];
         $title    = $_POST['name'];
        $title   = ucwords(strtolower($name));
        $total   = $_POST['total'];
        $correct = $_POST['right'];
        $wrong   = $_POST['wrong'];
        $time    = $_POST['time'];
         $trade    = $_POST['trade'];
        $d1 = mysqli_query($con, "UPDATE quiz SET title='$title',correct='$correct',wrong='$wrong',total='$total',time='$time',status='disabled', trade='$trade',WHERE eid='$eid' ") or die('Error');
        header("location:dash.php?q=1");
    }
}
//add question
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'adqns' && $_SESSION['key'] == '66552277T') {
       $eid = @$_GET['eid'];
        $qid = @$_GET['qid'];
         $qns    = $_POST['qns'];
        $r1 = mysqli_query($con, "UPDATE questions SET qns='$qns' WHERE qid='$qid' AND  eid='$eid'") or die('Error');
       
        header("location:dash.php?q=5");

    }
    }
//add question
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'addqns' && $_SESSION['key'] == '66552277T') {
        $no   = @$_GET['n'];
        $n =1;
        $eid = @$_GET['eid'];
        $ch  = @$_GET['ch'];
        for ($i = 1; $i <= $n; $i++) {
            $qid  = uniqid();
            $qns  = addslashes($_POST['qns' . $i]);
            $q3   = mysqli_query($con, "INSERT INTO questions VALUES  (NULL,'$eid','$qid','$qns' , '$ch' , '$no')") or die();
            $oaid = uniqid();
            $obid = uniqid();
            $ocid = uniqid();
            $odid = uniqid();
            $a    = addslashes($_POST[$i . '1']);
            $b    = addslashes($_POST[$i . '2']);
            $c    = addslashes($_POST[$i . '3']);
            $d    = addslashes($_POST[$i . '4']);
            $qa = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$a','$oaid')") or die('Error61');
            $qb = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$b','$obid')") or die('Error62');
            $qb = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$c','$ocid')") or die('Error63'.mysqli_error($con));
            $qd = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$d','$odid')") or die('Error64');
            $e = $_POST['ans' . $i];
            switch ($e) {
                case 'a':
                    $ansid = $oaid;
                    break;
                
                case 'b':
                    $ansid = $obid;
                    break;
                
                case 'c':
                    $ansid = $ocid;
                    break;
                
                case 'd':
                    $ansid = $odid;
                    break;
                
                default:
                    $ansid = $oaid;
            }
            
            $qans = mysqli_query($con, "INSERT INTO answer VALUES  ('','$qid','$ansid')");
        }
        
        header("location:add_questions.php?q=7&eid=$eid");
    }
}
//add_questions.php?q=7&eid=6414489f8aff8
//viewquiz.php?q=6&eid=6414489f8aff8

//add question
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'qns' && $_SESSION['key'] == '66552277T') {
       
        $eid = @$_GET['eid'];
       
        for ($i = 1; $i <= $n; $i++) {
            $qid  = uniqid();
            $qns  = addslashes($_POST['qns' . $i]);
            $q3   = mysqli_query($con, "INSERT INTO questions VALUES  (NULL,'$eid','$qid','$qns' , '$ch' , '$i')") or die();
            $oaid = uniqid();
            $obid = uniqid();
            $ocid = uniqid();
            $odid = uniqid();
            $a    = addslashes($_POST[$i . '1']);
            $b    = addslashes($_POST[$i . '2']);
            $c    = addslashes($_POST[$i . '3']);
            $d    = addslashes($_POST[$i . '4']);
            $qa = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$a','$oaid')") or die('Error61');
            $qb = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$b','$obid')") or die('Error62');
            $qc = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$c','$ocid')") or die('Error63'.mysqli_error($con));
            $qd = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$d','$odid')") or die('Error64');
            $e = $_POST['ans' . $i];
            switch ($e) {
                case 'a':
                    $ansid = $oaid;
                    break;
                
                case 'b':
                    $ansid = $obid;
                    break;
                
                case 'c':
                    $ansid = $ocid;
                    break;
                
                case 'd':
                    $ansid = $odid;
                    break;
                
                default:
                    $ansid = $oaid;
            }
            
            $qans = mysqli_query($con, "INSERT INTO answer VALUES  ('','$qid','$ansid')");
        }
        
        header("location:questions.php?q=1");
    }
}

//student Add
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'std' && $_SESSION['key'] == '66552277T') {
$name     = $_POST['name'];
$name     = ucwords(strtolower($name));
$username = $_POST['username'];
$password = $_POST['password'];
$branch   = $_POST['branch'];
$rollno   = $_POST['rollno'];
$q3 = mysqli_query($con, "INSERT INTO user (id, name, rollno, branch, username, password) VALUES  (NULL,'$name', '$rollno','$branch' ,'$username' ,'$password')");
 header("location:user.php?q=1");
    }
}




//delete history & rank & user ans
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'rmrah' && $_SESSION['key'] == '66552277T') {
        $username = @$_GET['username'];
    
        $r3 = mysqli_query($con, "DELETE FROM rank WHERE username='$username' ") or die('Error');
        $r4 = mysqli_query($con, "DELETE FROM user_answer WHERE username='$username' ") or die('Error');
        $r4 = mysqli_query($con, "DELETE FROM history WHERE username='$username' ") or die('Error');
        header("location:dashboard.php?q=2");
    }
}




//feedback
$username = $_SESSION['username'];
if (isset($_SESSION['key'])) {
    if (@$_GET['fdid'] && $_SESSION['key'] == '66552277T') {
        $id = @$_GET['fdid'];
        $result = mysqli_query($con, "DELETE FROM feedback WHERE id='$id' ") or die('Error');
        header("location:dash.php?q=3");
    }
}

// delete user
if (isset($_SESSION['key'])) {
    if (@$_GET['dusername'] && $_SESSION['key'] == '66552277T') {
        $dusername = @$_GET['dusername'];
        $r1 = mysqli_query($con, "DELETE FROM rank WHERE username='$dusername' ") or die('Error');
        $r2 = mysqli_query($con, "DELETE FROM history WHERE username='$dusername' ") or die('Error');
        $result = mysqli_query($con, "DELETE FROM user WHERE username='$dusername' ") or die('Error');
        header("location:user.php?q=1");
    }
}


// //add question
// if (isset($_SESSION['key'])) {
//     if (@$_GET['q'] == 'addqns' && $_SESSION['key'] == '66552277T') {
//         $n   = @$_GET['n'];
//         $eid = @$_GET['eid'];
//         $ch  = @$_GET['ch'];
//         for ($i = 1; $i <= $n; $i++) {
//             $qid  = uniqid();
//             $qns  = addslashes($_POST['qns' . $i]);
//             $q3   = mysqli_query($con, "INSERT INTO questions VALUES  (NULL,'$eid','$qid','$qns' , '$ch' , '$i')") or die();
//             $oaid = uniqid();
//             $obid = uniqid();
//             $ocid = uniqid();
//             $odid = uniqid();
//             $a    = addslashes($_POST[$i . '1']);
//             $b    = addslashes($_POST[$i . '2']);
//             $c    = addslashes($_POST[$i . '3']);
//             $d    = addslashes($_POST[$i . '4']);
//             $qa = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$a','$oaid')") or die('Error61');
//             $qb = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$b','$obid')") or die('Error62');
//             $qb = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$c','$ocid')") or die('Error63'.mysqli_error($con));
//             $qd = mysqli_query($con, "INSERT INTO options VALUES  (NULL,'$qid','$d','$odid')") or die('Error64');
//             $e = $_POST['ans' . $i];
//             switch ($e) {
//                 case 'a':
//                     $ansid = $oaid;
//                     break;
                
//                 case 'b':
//                     $ansid = $obid;
//                     break;
                
//                 case 'c':
//                     $ansid = $ocid;
//                     break;
                
//                 case 'd':
//                     $ansid = $odid;
//                     break;
                
//                 default:
//                     $ansid = $oaid;
//             }
            
//             $qans = mysqli_query($con, "INSERT INTO answer VALUES  (1,'$qid','$ansid')");
//         }
        
//         header("location:dash.php?q=10");
//     }

// }

?>
