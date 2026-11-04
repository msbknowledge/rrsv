<?php
include_once '../dbConnection.php';

session_start();
//feedback
$username = $_SESSION['username'];
$scl_reg_no = $_SESSION['scl_reg_no'];
$user_id= $_SESSION['user_id'];
if (isset($_SESSION['key'])) {
    if (@$_GET['fdid'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $id = @$_GET['fdid'];
        $result = mysqli_query($con, "DELETE FROM feedback WHERE id='$id' ") or die('Error1');
        header("location:dash.php?q=3");
        exit;
    }
}
// disable
if (isset($_SESSION['key'])) {
    if (@$_GET['deidquiz'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $eid = @$_GET['deidquiz'];
        $r1 = mysqli_query($con, "UPDATE quiz SET status='disabled' WHERE eid='$eid' ") or die('Error');
        $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND status='ongoing' AND score_updated='false'");
        while($row = mysqli_fetch_array($q)){
            $user = $row['username'];
            $s = $row['score'];
            $r1 = mysqli_query($con, "UPDATE history SET status='finished',score_updated='true' WHERE eid='$eid' AND username='$user' ") or die('Error2');
            $q1 = mysqli_query($con, "SELECT * FROM rank WHERE username='$user'") or die('Error3');
            $rowcount = mysqli_num_rows($q1);
            if ($rowcount == 0) {
                $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,'','','$user','$s',NOW())") or die('Error4');
            } else {
                while ($row = mysqli_fetch_array($q1)) {
                    $sun = $row['score'];
                }
                        
                $sun = $s + $sun;
                $q3 = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$scl_reg_no'") or die('Error5');
            }
        }
        header("location:dash.php?q=10");
        exit;
    }
}
//enable
if (isset($_SESSION['key'])) {
    if (@$_GET['eeidquiz'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $eid = @$_GET['eeidquiz'];
        $r1 = mysqli_query($con, "UPDATE quiz SET status='enabled' WHERE eid='$eid' ") or die('Error6');
        header("location:dash.php?q=10");
    }
}
// delete user
if (isset($_SESSION['key'])) {
    if (@$_GET['dusername'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $dusername = @$_GET['dusername'];
        $r1 = mysqli_query($con, "DELETE FROM rank WHERE username='$dusername' ") or die('Error7');
        $r2 = mysqli_query($con, "DELETE FROM history WHERE username='$dusername' ") or die('Error8');
        $result = mysqli_query($con, "DELETE FROM user WHERE username='$dusername' ") or die('Error9');
        header("location:dash.php?q=1");
        exit;
    }
}
//remove quiz
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'rmquiz' && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $eid = @$_GET['eid'];
        $result = mysqli_query($con, "SELECT * FROM questions WHERE eid='$eid' ") or die('Error10');
        while ($row = mysqli_fetch_array($result)) {
            $qid = $row['qid'];
            $r1 = mysqli_query($con, "DELETE FROM options WHERE qid='$qid'") or die('Error11');
            $r2 = mysqli_query($con, "DELETE FROM answer WHERE qid='$qid' ") or die('Error12');
        }
        
        $r3 = mysqli_query($con, "DELETE FROM questions WHERE eid='$eid' ") or die('Error13');
        $r4 = mysqli_query($con, "DELETE FROM quiz WHERE eid='$eid' ") or die('Error14');
        $r4 = mysqli_query($con, "DELETE FROM history WHERE eid='$eid' ") or die('Error15');
        header("location:dash.php?q=5");
        exit;
    }
}
//add quize
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'addquiz' && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $name    = $_POST['name'];
        $name    = ucwords(strtolower($name));
        $total   = $_POST['total'];
        $correct = $_POST['right'];
        $wrong   = $_POST['wrong'];
        $time    = $_POST['time'];
        $status  = "disabled";
        $trade   = $_POST['trade'];
        $id      = uniqid();
        $q3      = mysqli_query($con, "INSERT INTO quiz VALUES(NULL,'$id','$name','$correct','$wrong','$total','$time', 'NOW()','$status','')");
        header("location:dash.php?q=4&step=2&eid=$id&n=$total");
        exit;
    }
}
//edit quiz
if (isset($_SESSION['key'])) {
    if (@$_GET['editq'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $eid = @$_GET['eid'];
         $title    = $_POST['name'];
        $title   = ucwords(strtolower($name));
        $total   = $_POST['total'];
        $correct = $_POST['right'];
        $wrong   = $_POST['wrong'];
        $time    = $_POST['time'];
         $trade    = $_POST['trade'];
        $d1 = mysqli_query($con, "UPDATE quiz SET title='$title',correct='$correct',wrong='$wrong',total='$total',time='$time',status='disabled', class='$trade',WHERE eid='$eid' ") or die('Error16');
        header("location:dash.php?q=1");
        exit;
    }
}

//delete history & rank & user ans
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'rmrah' && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $scl_reg_no = @$_GET['username'];
    
        $r3 = mysqli_query($con, "DELETE FROM rank WHERE username='$scl_reg_no' ") or die('Error17');
        $r4 = mysqli_query($con, "DELETE FROM user_answer WHERE username='$scl_reg_no' ") or die('Error18');
        $r4 = mysqli_query($con, "DELETE FROM history WHERE username='$scl_reg_no' ") or die('Error19');
        header("location:dash.php?q=2");
        exit;
    }
}


//add question
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'addqns' && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $n   = @$_GET['n'];
        $eid = @$_GET['eid'];
        $ch  = @$_GET['ch'];
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
            
            $qans = mysqli_query($con, "INSERT INTO answer VALUES  (1,'$qid','$ansid')");
        }
        
        header("location:dash.php?q=10");
        exit;
    }
}
//select exam
if (@$_GET['q'] == 'quiz' && @$_GET['step'] == 2 && isset($_SESSION['6e447159425d2d']) && $_SESSION['6e447159425d2d'] == "6e447159425d2d" && isset($_POST['ans']) && (!isset($_GET['delanswer']))) {
        //print_r($_POST);die();
    $eid   = @$_GET['eid'];
    $sn    = @$_GET['n'];
    $total = @$_GET['t'];
    $ans   = $_POST['ans'];
    $qid   = @$_GET['qid'];
    $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error20');
    if (mysqli_num_rows($q) > 0) {
        $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error288');
        while ($row = mysqli_fetch_array($q)) {
            $time   = $row['timestamp'];
            $status = $row['status'];
        }
        
        $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$_GET[eid]' ") or die('Error197');
        while ($row = mysqli_fetch_array($q)) {
            $ttime   = $row['time'];
            $qstatus = $row['status'];
        }
        
        $remaining = (($ttime * 60) - ((time() - $time)));
        if ($status == "ongoing" && $remaining > 0 && $qstatus == "enabled") {
            $q = mysqli_query($con, "SELECT * FROM user_answer WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND qid='$qid' AND user_id = '$user_id'") or die('Error115');
            while ($row = mysqli_fetch_array($q)) {
                $prevans = $row['ans'];
            }
            $q = mysqli_query($con, "SELECT * FROM answer WHERE qid='$qid' ");
            while ($row = mysqli_fetch_array($q)) {
                $ansid = $row['ansid'];
            }
            $q = mysqli_query($con, "SELECT * FROM user_answer WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND qid='$qid' AND user_id = '$user_id'") or die('Error1977');
            if (mysqli_num_rows($q) != 0) {
                $q = mysqli_query($con, "UPDATE user_answer SET ans='$ans' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND qid='$qid' AND user_id = '$user_id'") or die('Error197');
            } else {
                $q = mysqli_query($con, "INSERT INTO user_answer VALUES(NULL,'$qid','$ans','$ansid','$_GET[eid]','$scl_reg_no',$user_id)");
            }
            
            $q = mysqli_query($con, "SELECT * FROM options WHERE qid='$qid' AND optionid='$ans'");
            while ($row = mysqli_fetch_array($q)) {
                $option = $row['option'];
            }
            
            
            if ($ans == $ansid) {
                $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ");
                while ($row = mysqli_fetch_array($q)) {
                    $correct = $row['correct'];
                    $wrong   = $row['wrong'];
                }
                
                $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error115');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $r = $row['correct'];
                    $w = $row['wrong'];
                }
                
                if (isset($prevans) && $prevans == $ansid) {
                } else if (isset($prevans) && $prevans != $ansid) {
                    $r++;
                    $w--;
                    $s = $s + $correct + $wrong;
                    $q = mysqli_query($con, "UPDATE `history` SET `score`=$s,`level`=$sn,`correct`=$r,`wrong`=$w, date= NOW()  WHERE  username = '$scl_reg_no' AND eid = '$eid' AND user_id = '$user_id'") or die('Error13');
                } else {
                    $r++;
                    $s = $s + $correct;
                    $q = mysqli_query($con, "UPDATE `history` SET `score`=$s,`level`=$sn,`correct`=$r, date= NOW()  WHERE  username = '$scl_reg_no' AND eid = '$eid' AND user_id = '$user_id'") or die('Error14');
                }
            } else {
                $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error129');
                while ($row = mysqli_fetch_array($q)) {
                    $wrong   = $row['wrong'];
                    $correct = $row['correct'];
                }
                
                $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error139');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $w = $row['wrong'];
                    $r = $row['correct'];
                }
                if (isset($prevans) && $prevans != $ansid) {
                } else if (isset($prevans) && $prevans == $ansid) {
                    $r--;
                    $w++;
                    $s = $s - $correct - $wrong;
                    $q = mysqli_query($con, "UPDATE `history` SET `score`=$s,`level`=$sn,`wrong`=$w,`correct`=$r, date= NOW()  WHERE  username = '$scl_reg_no' AND eid = '$eid' AND user_id = '$user_id'") or die('Error11');
                } else {
                    $w++;
                    $s = $s - $wrong;
                    $q = mysqli_query($con, "UPDATE `history` SET `score`=$s,`level`=$sn,`wrong`=$w,date= NOW()  WHERE  username = '$scl_reg_no' AND eid = '$eid' AND user_id = '$user_id'") or die('Error12');
                }
            }
            header("location:account.php?q=quiz&step=2&eid=$eid&n=$sn&t=$total") or die('Error152');
            exit;
            
        } else {
            unset($_SESSION['6e447159425d2d']);
            $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id' ") or die('Error197');
            $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error156');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $scorestatus = $row['score_updated'];
                }
                if($scorestatus=="false"){
                    $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' ") or die('Error197');
                    $q = mysqli_query($con, "SELECT * FROM rank WHERE username='$scl_reg_no'") or die('Error161');
                    $rowcount = mysqli_num_rows($q);
                    if ($rowcount == 0) {
                        $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,$user_id,'','$scl_reg_no','$s',NOW())") or die('Error165');
                    } else {
                        while ($row = mysqli_fetch_array($q)) {
                            $sun = $row['score'];
                        }
                        
                        $sun = $s + $sun;
                        $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$scl_reg_no' AND user_id = '$user_id'") or die('Error174');
                    }
                }
            header('location:account.php?q=result&eid=' . $_GET[eid]);
            exit;
        }
    } else {
        unset($_SESSION['6e447159425d2d']);
        $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id' ") or die('Error197');
        $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error156');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $scorestatus = $row['score_updated'];
                }
                if($scorestatus=="false"){
                    $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error197');
                    $q = mysqli_query($con, "SELECT * FROM rank WHERE username='$scl_reg_no' AND user_id = '$user_id'") or die('Error161');
                    $rowcount = mysqli_num_rows($q);
                    if ($rowcount == 0) {
                        $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,$user_id,'','$scl_reg_no','$s',NOW())") or die('Error165');
                    } else {
                        while ($row = mysqli_fetch_array($q)) {
                            $sun = $row['score'];
                        }
                        
                        $sun = $s + $sun;
                        $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$scl_reg_no'") or die('Error174');
                    }
                }
            header('location:account.php?q=result&eid=' . $_GET[eid]);
            exit;
    }
}
//delete selected option
if (@$_GET['q'] == 'quiz1' && @$_GET['step'] == 2 && isset($_SESSION['6e447159425d2d']) && $_SESSION['6e447159425d2d'] == "6e447159425d2d" && (!isset($_POST['ans'])) && (isset($_GET['delanswer'])) && $_GET['delanswer'] == "delanswer") {
    //print_r(1);die();
    $eid   = @$_GET['eid'];
    $sn    = @$_GET['n'];
    $total = @$_GET['t'];
    $qid   = @$_GET['qid'];
    $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error197');
    if (mysqli_num_rows($q) > 0) {
        $q = mysqli_query($con, "SELECT * FROM history WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error197');
        while ($row = mysqli_fetch_array($q)) {
            $time   = $row['timestamp'];
            $status = $row['status'];
        }
        
        $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$_GET[eid]' ") or die('Error197');
        while ($row = mysqli_fetch_array($q)) {
            $ttime   = $row['time'];
            $qstatus = $row['status'];
        }
        
        $remaining = (($ttime * 60) - ((time() - $time)));
        if ($status == "ongoing" && $remaining > 0 && $qstatus == "enabled") {
            $q = mysqli_query($con, "SELECT * FROM answer WHERE qid='$qid' ");
            while ($row = mysqli_fetch_array($q)) {
                $ansid = $row['ansid'];
            }
            $q = mysqli_query($con, "SELECT * FROM user_answer WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND qid='$qid' AND user_id = '$user_id'") or die('Error115');
            $row = mysqli_fetch_array($q);
            $ans = $row['ans'];
            $q = mysqli_query($con, "DELETE FROM user_answer WHERE qid='$qid' AND username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die("Error2222");
            if ($ans == $ansid) {
                $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error129');
                while ($row = mysqli_fetch_array($q)) {
                    $wrong   = $row['wrong'];
                    $correct = $row['correct'];
                }
                
                $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error139');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $w = $row['wrong'];
                    $r = $row['correct'];
                }
                $r--;
                $s = $s - $correct;
                $q = mysqli_query($con, "UPDATE `history` SET `score`=$s,`level`=$sn,`correct`=$r, date= NOW()  WHERE  username = '$scl_reg_no' AND eid = '$eid' AND user_id = '$user_id'") or die('Error11');
            } else {
                $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error129');
                while ($row = mysqli_fetch_array($q)) {
                    $wrong   = $row['wrong'];
                    $correct = $row['correct'];
                }
                
                $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error139');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $w = $row['wrong'];
                    $r = $row['correct'];
                }
                $w--;
                $s = $s + $wrong;
                $q = mysqli_query($con, "UPDATE `history` SET `score`=$s,`level`=$sn,`wrong`=$w, date= NOW()  WHERE  username = '$scl_reg_no' AND eid = '$eid' AND user_id = '$user_id'") or die('Error11');
            }
            header('location:account.php?q=quiz&step=2&eid=' . $_GET[eid] . '&n=' . $_GET[n] . '&t=' . $total);
            exit;
            
        } else {
            unset($_SESSION['6e447159425d2d']);
            $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error197');
             $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error156');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $scorestatus = $row['score_updated'];
                }
                if($scorestatus=="false"){
                    $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error197');
                    $q = mysqli_query($con, "SELECT * FROM rank WHERE username='$scl_reg_no'") or die('Error161');
                    $rowcount = mysqli_num_rows($q);
                    if ($rowcount == 0) {
                        $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,$user_id,'','$scl_reg_no','$s',NOW())") or die('Error165');
                    } else {
                        while ($row = mysqli_fetch_array($q)) {
                            $sun = $row['score'];
                        }
                        
                        $sun = $s + $sun;
                        $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$scl_reg_no' AND user_id = '$user_id'") or die('Error174');
                    }
                }
            header('location:account.php?q=result&eid=' . $_GET[eid]);
            exit;
        }
    } else {
        unset($_SESSION['6e447159425d2d']);
        $q = mysqli_query($con, "UPDATE history SET status='finished' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' ") or die('Error197');
        $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$_GET[eid]' AND username='$scl_reg_no' AND user_id = '$user_id'") or die('Error156');
                while ($row = mysqli_fetch_array($q)) {
                    $s = $row['score'];
                    $scorestatus = $row['score_updated'];
                }
                if($scorestatus=="false"){
                    $q = mysqli_query($con, "UPDATE history SET score_updated='true' WHERE username='$scl_reg_no' AND eid='$_GET[eid]' AND user_id = '$user_id'") or die('Error197');
                    $q = mysqli_query($con, "SELECT * FROM rank WHERE username='$scl_reg_no' AND user_id = '$user_id'") or die('Error161');
                    $rowcount = mysqli_num_rows($q);
                    if ($rowcount == 0) {
                        $q2 = mysqli_query($con, "INSERT INTO rank VALUES(NULL,$user_id,'','$scl_reg_no','$s',NOW())") or die('Error165');
                    } else {
                        while ($row = mysqli_fetch_array($q)) {
                            $sun = $row['score'];
                        }
                        
                        $sun = $s + $sun;
                        $q = mysqli_query($con, "UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$scl_reg_no' AND user_id = '$user_id'") or die('Error174');
                    }
                }
            header('location:account.php?q=result&eid=' . $_GET[eid]);
            exit;
    }
}
//student exam enable and disable for due
if (isset($_SESSION['key'])) {
    if (@$_GET['edue'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $eid = @$_GET['edue'];
        $d1 = mysqli_query($con, "UPDATE user SET due='0' WHERE username='$eid' ") or die('Error');
        header("location:dash.php?q=1");
        exit;
    }
}
if (isset($_SESSION['key'])) {
    if (@$_GET['ddue'] && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
        $eid = @$_GET['ddue'];
        $d1 = mysqli_query($con, "UPDATE user SET due='1' WHERE username='$eid' ") or die('Error');
        header("location:dash.php?q=1");
        exit;
    }
}


//student Add
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'std' && $_SESSION['key'] == '54585c506829293a2d4c3b68543b316e2e7a2d277858545a36362e5f39') {
$name     = $_POST['name'];
$name     = ucwords(strtolower($name));
//$gender   = $_POST['gender'];
$username = $_POST['username'];
//$phno     = $_POST['phno'];
$password = $_POST['password'];
$branch   = $_POST['branch'];
$rollno   = $_POST['rollno'];
/*$name     = stripslashes($name);
$name     = addslashes($name);
$name     = ucwords(strtolower($name));
$gender   = stripslashes($gender);
$gender   = addslashes($gender);
$username = stripslashes($username);
$username = addslashes($username);
$phno     = stripslashes($phno);
$phno     = addslashes($phno);
$password = stripslashes($password);
$password = addslashes($password);
$password = md5($password);*/

$q3 = mysqli_query($con, "INSERT INTO user (id, name, rollno, branch, username, password) VALUES  (NULL,'$name', '$rollno','$branch' ,'$username' ,'$password')");
 header("location:dash.php?q=1");
 exit;
    }
}
?>
