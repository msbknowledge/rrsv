<?php
include_once 'dbConnection.php';

session_start();
$username = $_SESSION['username'];
//disabled result
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
/* Update Exam Status */

if (isset($_GET['exam'])) {
    $status = ($_POST['status'] == 'active') ? 'active' : 'inactive';
    mysqli_query($con, "UPDATE quiz_assignments SET status='$status' WHERE id='$id' ") or die(mysqli_error($con));
    echo "success";
    exit;
}

/* Update Result Status */
if (isset($_GET['result'])) {
    $r_status = ($_POST['status'] == 'Active') ? 'Active' : 'Inactive';
    mysqli_query($con, "UPDATE quiz_assignments SET r_status='$r_status' WHERE id='$id'") or die(mysqli_error($con));
    echo "success";
    exit;
}

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
if (isset($_GET['q']) && $_GET['q'] == 'addquizm') {

    $name    = ucwords(strtolower($_POST['name']));
    $total   = $_POST['total'];
    $correct = $_POST['right'];
    $wrong   = $_POST['wrong'];
    $time    = $_POST['time'];
    $trade   = $_POST['trade'];
    $unit    = $_POST['unit'];
    $status  = "disabled";
    $id      = uniqid();

    $query = "INSERT INTO quiz 
        (eid,title,correct,wrong,total,time,date,status,class,unit) 
        VALUES 
        ('$id','$name','$correct','$wrong','$total','$time',NOW(),'$status','$trade','$unit')";

    if (!mysqli_query($con, $query)) {
        die("SQL Error: " . mysqli_error($con));
    }

    header("location:quiz.php?q=10");
    exit();
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




//delete history & rank update score & user ans delete
if (isset($_SESSION['key'])) {
    if (@$_GET['q'] == 'rmrah' && $_SESSION['key'] == '66552277T') {

        $username = mysqli_real_escape_string($con, $_GET['username']);
        $eid      = mysqli_real_escape_string($con, $_GET['eid']);

        // Get score of deleted exam (optional)
        $history = mysqli_query($con, "SELECT score,user_id FROM history WHERE username='$username' AND eid='$eid' LIMIT 1");
        $row = mysqli_fetch_assoc($history);

        if ($row) {

            $user_id = $row['user_id'];

            // Delete only this exam history
            mysqli_query($con, "DELETE FROM history WHERE username='$username' AND eid='$eid'");

            // Delete only this exam answers
            mysqli_query($con, "DELETE FROM user_answer WHERE username='$username' AND eid='$eid'");

            // Calculate remaining total score
            $result = mysqli_query($con, "SELECT IFNULL(SUM(score),0) AS total_score FROM history WHERE username='$username'");
            $scoreRow = mysqli_fetch_assoc($result);
            $totalScore = $scoreRow['total_score'];

            if ($totalScore > 0) {
                mysqli_query($con, "UPDATE rank SET score='$totalScore' WHERE username='$username'");
            } else {
                // No history left, remove rank
                mysqli_query($con, "DELETE FROM rank WHERE username='$username'");
            }
        }

        header("Location: exam_status.php?q=8");
        exit;
    }
}
if (isset($_SESSION['key111'])) {
    if (@$_GET['q'] == 'rmrah' && $_SESSION['key'] == '66552277T') {
        // $username = @$_GET['username'];
        // $eid = @$_GET['eid'];
    
        // $r3 = mysqli_query($con, "DELETE FROM rank WHERE username='$username' ") or die('Error');
        // $r4 = mysqli_query($con, "DELETE FROM user_answer WHERE username='$username' ") or die('Error');
        // $r4 = mysqli_query($con, "DELETE FROM history WHERE eid='$eid' ") or die('Error');
        // header("location:exam_status.php?q=8");
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


if (isset($_SESSION['key'])) {
if (@$_GET['q'] == 'addqnsm' && $_SESSION['key'] == '66552277T') {

    $no  = @$_GET['n'];
    $eid = @$_GET['eid'];
    $type = $_POST['question_type']; // NEW

    $qid = uniqid();
    $qns = addslashes($_POST['qns1']);

    // 🔥 add question_type column in DB first
    mysqli_query($con,"INSERT INTO questions (id,eid,qid,qns,choice,sn,question_type)
    VALUES (NULL,'$eid','$qid','$qns','4','$no','$type')") or die(mysqli_error($con));

    // ================= MCQ =================
    if($type=='mcq'){
        $oaid = uniqid();
        $obid = uniqid();
        $ocid = uniqid();
        $odid = uniqid();

        $a = addslashes($_POST['11']);
        $b = addslashes($_POST['12']);
        $c = addslashes($_POST['13']);
        $d = addslashes($_POST['14']);

        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$a','$oaid')");
        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$b','$obid')");
        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$c','$ocid')");
        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$d','$odid')");

        $ans = $_POST['ans1'];

        $ansid = ($ans=='a')?$oaid:(($ans=='b')?$obid:(($ans=='c')?$ocid:$odid));

        mysqli_query($con,"INSERT INTO answer VALUES ('','$qid','$ansid')");
    }

    // ================= MULTI SELECT =================
    elseif($type=='multi_select'){
        $correct = $_POST['multi_ans'] ?? [];

        for($i=1;$i<=4;$i++){
            $opt = addslashes($_POST['m'.$i]);
            $optid = uniqid();

            mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$opt','$optid')");

            if(in_array($i,$correct)){
                mysqli_query($con,"INSERT INTO answer VALUES ('','$qid','$optid')");
            }
        }
    }

    // ================= TRUE FALSE =================
    elseif($type=='true_false'){
        $t = uniqid(); 
        $f = uniqid();

        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','True','$t')");
        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','False','$f')");

        $ansid = ($_POST['true_false']=='True') ? $t : $f;

        mysqli_query($con,"INSERT INTO answer VALUES ('','$qid','$ansid')");
    }

    // ================= SHORT / FILL / DESC =================
    elseif(in_array($type,['short_answer','fill_blank','description'])){
        $ans = addslashes($_POST[$type]);
        $optid = uniqid();

        mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$ans','$optid')");
        mysqli_query($con,"INSERT INTO answer VALUES ('','$qid','$optid')");
    }

    // ================= IMAGE =================
    elseif($type=='image'){

   
    $ids = [];
$uploadDir = "../uploads/exam/";   
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
    for($i=1;$i<=4;$i++){

        $file = "";

        if(!empty($_FILES["img$i"]["name"])){

            $ext = pathinfo($_FILES["img$i"]["name"], PATHINFO_EXTENSION);

            $file = uniqid().".".$ext;

            move_uploaded_file(
                $_FILES["img$i"]["tmp_name"],
                $uploadDir.$file
            );
        }

        $optid = uniqid();
        $ids[$i] = $optid;

        mysqli_query($con,"
            INSERT INTO options
            VALUES(NULL,'$qid','$file','$optid')
        ");
    }

    switch($_POST['image_answer']){
        case 'a': $ansid=$ids[1]; break;
        case 'b': $ansid=$ids[2]; break;
        case 'c': $ansid=$ids[3]; break;
        case 'd': $ansid=$ids[4]; break;
    }

    mysqli_query($con,"
        INSERT INTO answer
        VALUES(NULL,'$qid','$ansid')
    ");
}
    // elseif($type=='image'){
    //     $img = $_FILES['image']['name'];
    //     move_uploaded_file($_FILES['image']['tmp_name'], "uploads/exam/".$img);

    //     mysqli_query($con,"UPDATE questions SET qns=CONCAT(qns,'<br><img src=uploads/$img width=150>') WHERE qid='$qid'");
    // }

    // ================= ORDERING =================
    elseif($type=='ordering'){
        for($i=1;$i<=4;$i++){
            $opt = addslashes($_POST['order'.$i]);
            $optid = uniqid();

            mysqli_query($con,"INSERT INTO options VALUES (NULL,'$qid','$opt','$optid')");
        }
    }

    header("location:add_questions.php?q=7&eid=$eid");
}
}


//================ UPDATE QUESTION ===================
if($_GET['q']=="edit_question"){

    // Update Questions
    foreach($_POST['qid'] as $qid){

        $question=mysqli_real_escape_string(
            $con,
            $_POST['question'][$qid]
        );

        mysqli_query($con,"
            UPDATE questions
            SET qns='$question'
            WHERE qid='$qid'
        ");
    }

    // Update Text Options
    if(isset($_POST['option'])){

        foreach($_POST['option'] as $optionid=>$value){

            $value=mysqli_real_escape_string($con,$value);

            mysqli_query($con,"
                UPDATE options
                SET option='$value'
                WHERE optionid='$optionid'
            ");
        }

    }

    // Update Images
    $uploadDir="../uploads/exam/";

    if(!is_dir($uploadDir)){
        mkdir($uploadDir,0777,true);
    }

    foreach($_FILES as $key=>$file){

        if(strpos($key,"image_")===0 && $file['error']==0){

            $optionid=str_replace("image_","",$key);

            $ext=pathinfo($file['name'],PATHINFO_EXTENSION);

            $new=uniqid().".".$ext;

            move_uploaded_file(
                $file['tmp_name'],
                $uploadDir.$new
            );

            mysqli_query($con,"
                UPDATE options
                SET option='$new'
                WHERE optionid='$optionid'
            ");
        }

    }
//print_r(    $answerid);die();
    // Update Correct Answer
    // foreach($_POST['answer'] as $qid=>$answerid){

    //     mysqli_query($con,"
    //         UPDATE answer
    //         SET ansid='$answerid'
    //         WHERE qid='$qid'
    //     ");
    // }
//================ UPDATE ANSWER =================

foreach($_POST['answer'] as $qid => $ansid){

    $qid = mysqli_real_escape_string($con,$qid);
    $ansid = mysqli_real_escape_string($con,$ansid);

    // check existing answer
    $check = mysqli_query($con,
        "SELECT * FROM answer WHERE qid='$qid'"
    );

    if(mysqli_num_rows($check)>0){

        // update existing answer
        mysqli_query($con,"
            UPDATE answer 
            SET ansid='$ansid'
            WHERE qid='$qid'
        ") or die(mysqli_error($con));

    }else{

        // insert if not exists
        mysqli_query($con,"
            INSERT INTO answer(qid,ansid)
            VALUES('$qid','$ansid')
        ") or die(mysqli_error($con));

    }
}
     header("Location: edit_question.php?q=6&eid=".$_POST['eid']."&msg=updated");
    exit();
}
if (@$_GET['q'] == 'edit_question222') {

    $qid = $_POST['eid'];

    // Question
    $question = mysqli_real_escape_string($con, $_POST['question']);
//print_r(    $qid );die();
    mysqli_query($con,"
        UPDATE questions
        SET qns='$question'
        WHERE qid='$qid'
    ") or die(mysqli_error($con));

    // Get Question Type
    $q = mysqli_query($con,"SELECT question_type FROM questions WHERE qid='$qid'");
    $question = mysqli_fetch_assoc($q);
    $type = $question['question_type'];

    //================ IMAGE QUESTION =================
    if($type=='image'){

        $uploadDir="../uploads/exam/";

        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0777,true);
        }

        $optQuery=mysqli_query($con,"SELECT * FROM options WHERE qid='$qid'");

        $i=1;
        $correctOptionId="";

        while($opt=mysqli_fetch_assoc($optQuery)){

            $optionid=$opt['optionid'];
            $fileName=$opt['option'];

            // upload new image
            if(!empty($_FILES["img$i"]["name"])){

                if($fileName!="" && file_exists($uploadDir.$fileName)){
                    unlink($uploadDir.$fileName);
                }

                $ext=pathinfo($_FILES["img$i"]["name"],PATHINFO_EXTENSION);

                $newFile=uniqid().".".$ext;

                move_uploaded_file(
                    $_FILES["img$i"]["tmp_name"],
                    $uploadDir.$newFile
                );

                mysqli_query($con,"
                    UPDATE options
                    SET option='$newFile'
                    WHERE optionid='$optionid'
                ");

                $fileName=$newFile;
            }

            if($_POST['correct']==$i){
                $correctOptionId=$optionid;
            }

            $i++;
        }

    }
    //================ NORMAL QUESTION =================
    else{

        $optQuery=mysqli_query($con,"SELECT * FROM options WHERE qid='$qid'");

        $i=1;
        $correctOptionId="";

        while($opt=mysqli_fetch_assoc($optQuery)){

            $optionid=$opt['optionid'];

            $value=mysqli_real_escape_string($con,$_POST["option$i"]);

            mysqli_query($con,"
                UPDATE options
                SET option='$value'
                WHERE optionid='$optionid'
            ");

            if($_POST['correct']==$i){
                $correctOptionId=$optionid;
            }

            $i++;
        }

    }

    //================ UPDATE ANSWER =================
    mysqli_query($con,"
        UPDATE answer
        SET ansid='$correctOptionId'
        WHERE qid='$qid'
    ") or die(mysqli_error($con));

    header("Location: edit_question.php?q=6&eid=".$_POST['eid']."&msg=updated");
    exit();
}
?>
