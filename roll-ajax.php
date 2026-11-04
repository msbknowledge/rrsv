<?php
    include('include/dbcon.php');

//////// code comment on 3-2-2026 //////////////
// $result="";
//  $scl_class=$_POST['scl_class'];
//  $scl_session=$_POST['scl_session'];
//  $scl_section=$_POST['scl_section'];
//   $sql="select scl_roll_no  from rrsv_student_registration where scl_class='$scl_class' and scl_session='$scl_session' and scl_section='$scl_section' order by id desc limit 0,1";
//  $res=mysqli_query($myDB,$sql);
//  $c=mysqli_num_rows($res);
//  if($c>0)
//  {
// 	$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
// 		 $roll=$rows['scl_roll_no'];
// 	 $roll=$roll+1;
	
 	 /*	$result .="<input type='text' class='form-control' name='scl_roll_no' id='scl_roll_no' value='<?php echo $roll;?>'>";*/
//  }
//  else
//  {
// 	 $roll=1;
//  }
// echo $roll;

//////// code comment on 3-2-2026 //////////////

// Generate next roll number based on class, session, section
$scl_class = $_POST['scl_class'];
$scl_session = $_POST['scl_session'];
$scl_section = $_POST['scl_section'];

$query = "
    SELECT MAX(scl_roll_no + 0) AS max_roll
    FROM rrsv_student_registration
    WHERE scl_class = '$scl_class'
      AND scl_session = '$scl_session'
      AND scl_section = '$scl_section'
";

$res = mysqli_query($myDB, $query);
$row = mysqli_fetch_assoc($res);

$nextRoll = ($row['max_roll'] !== null) ? $row['max_roll'] + 1 : 1;

echo $nextRoll;
?>
