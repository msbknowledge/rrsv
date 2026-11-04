<?php
// echo "<pre>";
// print_r($_POST);
// die();
include('include/dbcon.php');

$marksheet = $myDB->escape_string(trim(addslashes($_POST['marksheet']['subject'][0])));
$unit             =$myDB->escape_string(trim(addslashes($_POST['unit'])));
$msg = "";
if($marksheet === "") {
    $msg = "Please Enter subject with marks.";
}
if($unit === "") {
    $msg = "Please select a Unit";
}
if($unit === "" || $marksheet === "") { ?>
    <script language="javascript" type="text/javascript">
    alert('<?=$msg;?>');
	window.location.href="manage_marksheet2.php?retcode=2";
	</script>;
<?php  } 

function cal_percentage($num_amount, $num_total) {
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = number_format($count2, 0);
  return $count;
}

//error_reporting(1);
	
   $student_id             =$myDB->escape_string(trim(addslashes($_POST['student_id'])));
   $session                =$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
   $class               =$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
   
   $body_weight            =$myDB->escape_string(trim(addslashes($_POST['body_weight'])));
   $home_project                =$myDB->escape_string(trim(addslashes($_POST['home_project'])));
   $grade               =$myDB->escape_string(trim(addslashes($_POST['grade'])));
   
  // UPDATE `rrsv_marksheet_unit` SET `unit` = 'err33' WHERE `rrsv_marksheet_unit`.`id` = 1;
    $arrcount= count($_POST['marksheet'][subject]); 
 if($unit === '1st_unit') {
$qry2 = "INSERT INTO rrsv_marksheet_unit (student_id, session, class, unit,1st_unit_body_weight,1st_unit_home_project,1st_unit_grade,2nd_unit_body_weight,2nd_unit_home_project,2nd_unit_grade,3rd_unit_body_weight,3rd_unit_home_project,3rd_unit_grade) VALUES ('$student_id', '$session', '$class', '2nd_unit', '$body_weight', '$home_project', '$grade','','','','','','')";
//die();
$res2=mysqli_query($myDB,$qry2);
}

if($unit === '2nd_unit') {
 $qry3 = "UPDATE rrsv_marksheet_unit SET unit = '3rd_unit', 2nd_unit_body_weight = '$body_weight', 2nd_unit_home_project = '$home_project', 2nd_unit_grade = '$grade' WHERE student_id = '$student_id' AND class = '$class' AND session = '$session'";
$res3=mysqli_query($myDB,$qry3);
}

if($unit === '3rd_unit') {
 $qry3 = "UPDATE rrsv_marksheet_unit SET unit = '1', 3rd_unit_body_weight = '$body_weight', 3rd_unit_home_project = '$home_project', 3rd_unit_grade = '$grade' WHERE student_id = '$student_id' AND class = '$class' AND session = '$session'";
$res3=mysqli_query($myDB,$qry3);
}
//die();

	   for($i=0; $i<$arrcount; $i++)
	   {
	       
	       $qry = " WHERE rrsv_marksheet.student_id = '$student_id' AND rrsv_marksheet.class = '$class' AND rrsv_marksheet.session = '$session' AND rrsv_marksheet.subject = '".$_POST['marksheet'][subject][$i]."'";
		   
		   if($unit === '1st_unit') {
		      $msql= "insert into rrsv_marksheet(student_id,class,session,subject,1st_unit_marks,1st_unit_hm,2nd_unit_marks,2nd_unit_hm,3rd_unit_marks,3rd_unit_hm,total,percent,total_hm) 
	                values('$student_id','$class','$session','".$_POST['marksheet'][subject][$i]."','".$_POST['marksheet'][unit_marks][$i]."','".$_POST['marksheet'][unit_hm][$i]."','','','','','','','') ";
	                $res=mysqli_query($myDB,$msql);
	                
	                
		   }
    	   if($unit === '2nd_unit') {
    	       $msql= "UPDATE rrsv_marksheet SET 2nd_unit_marks = '".$_POST['marksheet'][unit_marks][$i]."', 2nd_unit_hm = '".$_POST['marksheet'][unit_hm][$i]."'".$qry;
    	       $res=mysqli_query($myDB,$msql);
    	   }
    	   if($unit === '3rd_unit') {
    	        $msql= "SELECT * FROM rrsv_marksheet".$qry;
                $res=mysqli_query($myDB,$msql);
                $obj1=mysqli_fetch_array($res,MYSQLI_ASSOC);
                
                $final_marks = (int)$obj1['1st_unit_marks'] + (int)$obj1['2nd_unit_marks'];
                $final_marks = $final_marks + $_POST['marksheet'][unit_marks][$i];

                $percentage = cal_percentage($final_marks, 200);

                $final_hm = (int)$obj1['1st_unit_hm'] + (int)$obj1['2nd_unit_hm'];
                $final_hm = $final_hm + $_POST['marksheet'][unit_hm][$i];
                
                $msql= "UPDATE rrsv_marksheet SET 3rd_unit_marks = '".$_POST['marksheet'][unit_marks][$i]."', 3rd_unit_hm = '".$_POST['marksheet'][unit_hm][$i]."', total = '$final_marks', percent = '$percentage', total_hm = '$final_hm'".$qry;
                $res=mysqli_query($myDB,$msql);
            }

	   
	   }
	  // if($result) {
		
		echo '<script language="javascript" type="text/javascript">';
		echo 'window.location.href="manage_marksheet.php?retcode=2";';
		echo '</script>';
	
	   
	  //}
   

?>