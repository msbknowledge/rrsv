<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('../dbcon.php');
$id=$con->escape_string(trim(addslashes($_GET['student_id'])));

$scl_name = '';
$scl_class = '';
$scl_roll_no = '';
$scl_reg_no = '';
$scl_session = '';
  
if (isset($_GET['student_id']) && $_GET['student_id'] !== '') {

    $id = (int) $_GET['student_id'];

    // Student information
    $sql = "
        SELECT 
            scl_name,
            scl_class,
            scl_roll_no,
            scl_reg_no,
            scl_session,
            image
        FROM rrsv_student_registration
        WHERE id = $id
        LIMIT 1
    ";

    $res = mysqli_query($con, $sql);

    if ($res === false) {
        die(
            '<strong>Student Query Error:</strong> ' .
            htmlspecialchars(mysqli_error($con)) .
            '<br><br><strong>SQL:</strong><br>' .
            htmlspecialchars($sql)
        );
    }

    $rows = mysqli_fetch_assoc($res);

    if (!$rows) {
        die('Student not found. Student ID: ' . $id);
    }

    $student_image = $rows['image'];

    $scl_name    = $rows['scl_name'];
    $scl_class   = $rows['scl_class'];
    $scl_roll_no = $rows['scl_roll_no'];
    $scl_reg_no  = $rows['scl_reg_no'];
    $scl_session = $rows['scl_session'];

    /*
     * Marksheet
     *
     * Drawing বাদ দিয়ে অন্যান্য subjects
     */
    $sql1 = "
        SELECT *
        FROM rrsv_test_marksheet
        WHERE student_id = $id
          AND session = '" . mysqli_real_escape_string($con, $scl_session) . "'
          AND class = '" . mysqli_real_escape_string($con, $scl_class) . "'
          AND LOWER(TRIM(subject)) <> 'drawing'
    ";

    $qryresult = mysqli_query($con, $sql1);

    // IMPORTANT: Query failed কিনা আগে check
    if ($qryresult === false) {
        die(
            '<strong>Marksheet Query Error:</strong> ' .
            htmlspecialchars(mysqli_error($con)) .
            '<br><br><strong>SQL:</strong><br>' .
            nl2br(htmlspecialchars($sql1))
        );
    }

    $totalrecord = mysqli_num_rows($qryresult);

    if ($totalrecord === 0) {
        echo '
        <script>
            alert("Please enter minimum 1 unit to print marksheet!");
            window.history.back();
        </script>
        ';
        exit;
    }
}
function check_qry($id,$unit,$activity) {
    include('../dbcon.php');
    $cur=date('Y');
   
    switch ($unit) {
        case 1:
            $start_date = $cur.'-01-01';
            $end_date = $cur.'-04-30';
        break;
        case 2:
            $start_date = $cur.'-05-01';
            $end_date = $cur.'-09-30';
        break;
        case 3:
            $start_date = $cur.'-10-01';
            $end_date = $cur.'-12-31';
        break;
        default:
            $start_date = $cur.'-01-01';
            $end_date = $cur.'-12-31';
    }

        $sql="SELECT COUNT(activties) as activity FROM `rrsv_student_behaviour` WHERE `student_id` = $id AND `activties` = '$activity' AND `date` BETWEEN '$start_date' AND '$end_date'";
        $res=mysqli_query($con,$sql);
        $rows=mysqli_fetch_array($res);
        $rrr = $rows['activity'];
    
        return $rrr;
}

function get_qry($id,$unit,$activity) {
    $result = (int)check_qry($id,$unit,$activity);
    $data = 'A';
    if($result > 4){
        $data = 'B';
    }
     if($result > 8){
        $data = 'C';
    }
     if($result > 12){
        $data = 'D';
    }
    
    
    return $data;
}


function absent_qry($id,$unit,$scl_class,$scl_session) {
        include('../dbcon.php');

        $cur=date('Y');

    switch ($unit) {
        case 1:
            $start_date = $cur.'-01-01';
            $end_date = $cur.'-04-30';
        break;
        case 2:
            $start_date = $cur.'-05-01';
            $end_date = $cur.'-09-30';
        break;
        case 3:
            $start_date = $cur.'-10-01';
            $end_date = $cur.'-12-31';
        break;
        default:
            $start_date = $cur.'-01-01';
            $end_date = $cur.'-12-31';
    }
    
      $sql="SELECT COUNT(status) as status FROM `rrsv_student_attendence` WHERE `student_id` = $id AND `date` BETWEEN '$start_date' AND '$end_date' AND `status` = 'Absent' AND `class_name` = '$scl_class' AND `scl_session` = $scl_session";
        $res=mysqli_query($con,$sql);
        $rows=mysqli_fetch_array($res);
        $rrr = $rows['status'];
    
        return $rrr;
    
}


function get_data($student_id,$session,$class,$parm) {
    include('../dbcon.php');

        $sql="SELECT $parm  FROM `rrsv_marksheet_unit` WHERE `student_id` = $student_id AND `session` = '$session' AND `class` = '$class'";
        $res=mysqli_query($con,$sql);
        $rows=mysqli_fetch_array($res);
        $rrr = $rows[$parm];
    
        return $rrr;
}


 function cal_percentage($num_amount, $num_total)
{
    $num_amount = (float)$num_amount;
    $num_total  = (float)$num_total;

    if ($num_total <= 0) {
        return '0';
    }

    return number_format(($num_amount / $num_total) * 100, 0);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .heading h4 {
            text-align: center;

        }

        .heading p {
            font-size: 25px;
            font-family: serif;
            text-align: center;
            margin-left: 15%;
            margin-right: 15%;
        }

        table,
        td,
        tr,
        th {
            font-size: 7px;
            border: 2px solid black;
            font-weight: bold;

        }

        .marks {
            margin-top: 50px;
        }

        .disclaim {

            font-style: italic;
            text-align: center;


        }

        h6 {
            padding: 120px;
            text-decoration: overline;
            text-align: end;
        }
        .container {
  font-size: 8px;
}

.fd {
      font-size: 13px;

}
table.borderless tr,table.borderless td,table.borderless th{
     border: none !important;
}
    </style>
    <title>Student Marksheet</title>
</head>

<body>
    <a type="button" href="manage_marksheet.php" class="btn btn-dark" style="color: #fff">< Back</a>
	<a type="button" href="print_marksheet.php?student_id=<?=$id?>&scl_class=<?=$scl_class?>&scl_session=<?=$scl_session?>" class="btn btn-danger" style="color: #fff">Print Marksheet</a>

	<a type="button" href="extra_curricular_activities.php?student_id=<?=$id?>&scl_class=<?=$scl_class?>&scl_session=<?=$scl_session?>" class="btn btn-warning" style="color: #fff">Print Extra Curricular Activities</a>
	
	<div id="print">
        <div class="container">

    <div class="heading">
        

        <h4 style="text-decoration:underline">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h4>
        
    </div>
    <div class="container">
        <div>
            <table class="table borderless text-center">
            
                <tr>
                    <th class="col-md-2"><img src="../../libray/images/logo.jpeg" class="img"  alt="image" style="
    width: 94px;
    height: 89px;
    margin-left: 0px;
" /></th>
                    <th class="col-md-8 fd"><p>Reg. No. : SO196094, U-DISE Code : 19251610302, ESTD : 2012<br>Baidyadanga, Rasulpur, Memari, Purba Bardhaman, Pin -713151</p></th>
                    <th class="col-md-2"><img src="../../student_reg_image/<?=$student_image;?>" class="img"  alt="image" style="width: 94px;height: 89px;margin-left: 0px;"/> /</th>
                </tr>
            </table>
            
            <table class="table">
                <tr>
                    <th class="col" colspan="4">Name :&nbsp;<?=$scl_name?></th>
                </tr>

                <tr>
                    <th class="col">Registration No :&nbsp;<span><?=$scl_reg_no;?></span></th>
                    <th class="col">Session :&nbsp;<span><?=$scl_session?></span></th>
                    <th class="col">Class :&nbsp;<span><?=$scl_class?></span></th>
                    <th class="col">Roll No :&nbsp;<span><?=$scl_roll_no?></span></th>
                </tr>
            </table>
        </div>
        <div class="marks">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th class="col-md-3">Subject</th>
                        <th class="col-md-1">1st Unit Marks(60)</th>
                        <th class="col-md-1">1st Unit H.M(60)</th>
                        <th class="col-md-1">2nd Unit Marks(80)</th>
                        <th class="col-md-1">2nd Unit H.M(80)</th>
                        <th class="col-md-1">3rd Unit Marks(60)</th>
                        <th class="col-md-1">3rd Unit H.M(60)</th>
                        <th class="col-md-1">Total(200)</th>
                        <th class="col-md-1">%</th>
                        <th class="col-md-1">H.M(200)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                         
                        $frist_marks = 0;
                        $frist_hm = 0;
                        $second_marks = 0;
                        $second_hm = 0;
                        $third_marks = 0;
                        $third_hm = 0;
                        $total_marks = 0;
                        $row_val = 0;
                        $total_exam_marks = 0;
                        while ($rows=mysqli_fetch_array($qryresult,MYSQLI_ASSOC))
                       
                            
                         {
                            $frist_marks = $frist_marks + (int)$rows['1st_unit_marks'];
                            $frist_hm = $frist_hm + (int)$rows['1st_unit_hm'];
                            $second_marks = $second_marks + (int)$rows['2nd_unit_marks'];
                            $second_hm = $second_hm + (int)$rows['2nd_unit_hm'];
                            $third_marks = $third_marks + (int)$rows['3rd_unit_marks'];
                            $third_hm = $third_hm + (int)$rows['3rd_unit_hm'];
                            //$total_marks = $frist_marks + $second_marks + $third_marks;
                            $total_marks = $total_marks + $rows['total'];
                            $row_val++;
                            $total_exam_marks = $row_val * 200;
                           // $qq = $val2;
                         ?>
                    <tr>
                        <td><?=$rows['subject'];?></td>
                        <td><?=$rows['1st_unit_marks'];?></td>
                        <td><?=$rows['1st_unit_hm'];?></td>
                        <td><?=$rows['2nd_unit_marks'];?></td>
                        <td><?=$rows['2nd_unit_hm'];?></td>
                        <td><?=$rows['3rd_unit_marks'];?></td>
                        <td><?=$rows['3rd_unit_hm'];?></td>
                        <td><?=$rows['total'];?></td>
                        <td><?=$rows['percent'];?></td>
                        <td>00</td>
                        <!--<td><?=$rows['total_hm'];?></td>-->
                    </tr>
                    
                    <?php  } ?>
                    
                    <tr>
                        <td>GRAND TOTAL</td>
                        <td><?=$frist_marks;?></td>
                        <td><?=$frist_hm;?></td>
                        <td><?=$second_marks;?></td>
                        <td><?=$second_hm;?></td>
                        <td><?=$third_marks;?></td>
                        <td><?=$third_hm;?></td>
                        <td><?=$total_marks;?></td>
                        <td><?=cal_percentage($total_marks, $total_exam_marks);?></td>
                        <td>---</td>
                    </tr>
                    <tr>
                        <td>DRAWING</td>
                        <?php

$sql = "SELECT *
        FROM rrsv_test_marksheet
        WHERE student_id = '$id'
          AND session = '$scl_session'
          AND class = '$scl_class'
          AND LOWER(subject) = 'drawing'
        LIMIT 1";

$res = mysqli_query($con, $sql);

if ($res === false) {
    die("Drawing SQL Error: " . mysqli_error($con));
}

$drawing = mysqli_fetch_assoc($res);

if ($drawing) {

    $drawing_total_marks =
        (int)$drawing['1st_unit_marks'] +
        (int)$drawing['2nd_unit_marks'] +
        (int)$drawing['3rd_unit_marks'];

} else {

    $drawing = [
        '1st_unit_marks' => 0,
        '1st_unit_hm'    => 0,
        '2nd_unit_marks' => 0,
        '2nd_unit_hm'    => 0,
        '3rd_unit_marks' => 0,
        '3rd_unit_hm'    => 0
    ];

    $drawing_total_marks = 0;
}
?>

                      <td><?=$drawing['1st_unit_marks'];?></td>
<td><?=$drawing['1st_unit_hm'];?></td>

<td><?=$drawing['2nd_unit_marks'];?></td>
<td><?=$drawing['2nd_unit_hm'];?></td>

<td><?=$drawing['3rd_unit_marks'];?></td>
<td><?=$drawing['3rd_unit_hm'];?></td>

<td><?=$drawing_total_marks;?></td>

<td><?=cal_percentage($drawing_total_marks, 200);?></td>

<td>--</td>
                    </tr>
                </tbody>
            </table>
                        <table class="table text-center">
                <thead class="text-center">
                    <tr>
                        <th class="col-md-2">EXAMINATION</th>
                        <th class="col-md-4">Guardian Signature with Date</th>
                        <th class="col-md-4">Principal Signature with Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1st unit (Jan to April)</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2nd unit (May to August)</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>3rd unit (September to December)</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Teacher Signature with date and comment</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

           
                    
        </div>

        <div class="disclaim">
            <p>Please report of any discrepancy within 7 days,
                Otherwise ,Institute will not be responsible for any errors in transcripts(if any)
            </p>
        </div>
       

    </div>
    </div>
</div>
</body>

</html>
<script>
function printDiv(id){
        var printContents = document.getElementById(id).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}
</script>