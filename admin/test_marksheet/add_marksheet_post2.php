<?php
include('../../include/dbcon.php');

function cal_percentage($num_amount,$num_total){
    if($num_total==0) return 0;
    return number_format(($num_amount/$num_total)*100,2);
}

$student_id = $_POST['student_id'];
$class      = $_POST['scl_class'];
$session    = $_POST['scl_session'];
$unit       = $_POST['unit'];
	

$arrcount = count($_POST['marksheet']['subject']);

for($i=0;$i<$arrcount;$i++){

$subject = mysqli_real_escape_string($myDB,$_POST['marksheet']['subject'][$i]);
$unitsql = "SELECT * FROM rrsv_test_marksheet 
WHERE student_id='$student_id' 
AND session='$session' 
AND class='$class' 
AND subject='$subject' 
LIMIT 1";
$unitres = mysqli_query($myDB,$unitsql);
$totalunitrecord = mysqli_num_rows($unitres);
$unitrows = mysqli_fetch_assoc($unitres);
$row_id = $unitrows['id'];
$marks   = (int)$_POST['marksheet']['unit_marks'][$i];
$hm      = (int)$_POST['marksheet']['unit_hm'][$i];


/* -------- 1st UNIT INSERT -------- */

if($unit=='1st_unit' && $totalunitrecord == 0){

$insert="INSERT INTO rrsv_test_marksheet
(student_id,class,session,subject,
1st_unit_marks,1st_unit_hm,
2nd_unit_marks,2nd_unit_hm,
3rd_unit_marks,3rd_unit_hm,
total,percent,total_hm)

VALUES
('$student_id','$class','$session','$subject',
'$marks','$hm','0','0','0','0','0','0','0')";

mysqli_query($myDB,$insert);

}else{


/* -------- 2nd UNIT UPDATE -------- */

if($unit=='2nd_unit' && $unitrows['2nd_unit_marks'] == 0){

$update="UPDATE rrsv_test_marksheet SET 2nd_unit_marks='$marks',2nd_unit_hm='$hm'
WHERE id='$row_id'
AND class='$class'
AND session='$session'
AND subject='$subject'";

mysqli_query($myDB,$update);

}


/* -------- 3rd UNIT UPDATE -------- */

if($unit=='3rd_unit' &&  $unitrows['3rd_unit_marks'] == 0){

$check="SELECT * FROM rrsv_test_marksheet
WHERE id='$row_id'
AND class='$class'
AND session='$session'
AND subject='$subject'";

$res=mysqli_query($myDB,$check);
$row=mysqli_fetch_assoc($res);

$first_marks=$row['1st_unit_marks'];
$second_marks=$row['2nd_unit_marks'];

$first_hm=$row['1st_unit_hm'];
$second_hm=$row['2nd_unit_hm'];

$total = $first_marks + $second_marks + $marks;
$total_hm = $first_hm + $second_hm + $hm;

$percent = cal_percentage($total,$total_hm);

$update="UPDATE rrsv_test_marksheet
SET
3rd_unit_marks='$marks',
3rd_unit_hm='$hm',
total='$total',
total_hm='$total_hm',
percent='$percent'
WHERE student_id='$student_id'
AND class='$class'
AND session='$session'
AND subject='$subject'";

mysqli_query($myDB,$update);

}
}
}

echo "<script>
window.location.href='manage_test_marksheet.php?retcode=1';
</script>";
?>