<?php
// echo "<pre>";
// print_r($_POST);
// die();

error_reporting(1);
	include('include/dbcon.php');
	
   $scl_roll_no             =$myDB->escape_string(trim(addslashes($_POST['scl_roll_no'])));
   $scl_name                =$myDB->escape_string(trim(addslashes($_POST['scl_name'])));
   $scl_class               =$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
   $scl_section             =$myDB->escape_string(trim(addslashes($_POST['scl_section'])));
   $grtotal1                =$myDB->escape_string(trim(addslashes($_POST['grtotal1'])));
   $grtotal2                =$myDB->escape_string(trim(addslashes($_POST['grtotal2'])));
   $grtotal3                =$myDB->escape_string(trim(addslashes($_POST['grtotal3'])));
   $grtotal4                =$myDB->escape_string(trim(addslashes($_POST['grtotal4'])));
   $per1                    =$myDB->escape_string(trim(addslashes($_POST['per1'])));
   $per2                    =$myDB->escape_string(trim(addslashes($_POST['per2'])));
   $remark                  =$myDB->escape_string(trim(addslashes($_POST['remark'])));
   $position                =$myDB->escape_string(trim(addslashes($_POST['position'])));
  
   $semi                    =$myDB->escape_string(trim(addslashes($_POST['scl_semi'])));
   $reg_id                  =$myDB->escape_string(trim(addslashes($_POST['reg_id'])));
   $behaviour               =$myDB->escape_string(trim(addslashes($_POST['behaviour'])));
   $neatness                =$myDB->escape_string(trim(addslashes($_POST['neatness'])));
   $dscipline               =$myDB->escape_string(trim(addslashes($_POST['dscipline'])));
   $self                    =$myDB->escape_string(trim(addslashes($_POST['self'])));
   $responsibility          =$myDB->escape_string(trim(addslashes($_POST['responsibility'])));
   $initiative              =$myDB->escape_string(trim(addslashes($_POST['initiative'])));
   $concentration           =$myDB->escape_string(trim(addslashes($_POST['concentration'])));
   $punctuality             =$myDB->escape_string(trim(addslashes($_POST['punctuality'])));
   $regularity              =$myDB->escape_string(trim(addslashes($_POST['regularity'])));
   $weight                  =$myDB->escape_string(trim(addslashes($_POST['weight'])));
   $project                 =$myDB->escape_string(trim(addslashes($_POST['project'])));
   $working                 =$myDB->escape_string(trim(addslashes($_POST['working'])));
   $absent                  =$myDB->escape_string(trim(addslashes($_POST['absent'])));
   $grades                  =$myDB->escape_string(trim(addslashes($_POST['grades'])));
  $arrcount= count($_POST['marksheet'][sub_id]); 
 
	$sql= "insert into marksheet (scl_roll_no,scl_name,scl_section,scl_class,grtotal1,grtotal2,grtotal3,grtotal4,per1,per2,remark,position)
values('$scl_roll_no','$scl_name','$scl_section','$scl_class','$grtotal1','$grtotal2','$grtotal3','$grtotal4','$per1','$per2','$remark','$position') ";
	   $result=mysqli_query($myDB,$sql);
	  $lastid= $myDB -> insert_id;
	 
	 
	   for($i=0; $i<$arrcount; $i++)
	   {
		  
	  $msql= "insert into marksheet_details(sub_id,ful,writen,oral,total,grade,scl_semi,reg_id,m_id,behaviour,neatness,dscipline,self,responsibility,initiative,concentration,punctuality,regularity,weight,project,working,absent,grades) values('".$_POST['marksheet'][sub_id][$i]."','".$_POST['marksheet'][ful][$i]."','".$_POST['marksheet'][writen][$i]."','".$_POST['marksheet'][oral][$i]."','".$_POST['marksheet'][total][$i]."','".$_POST['marksheet'][grade][$i]."','$semi','$reg_id','$lastid','$behaviour','$neatness','$dscipline','$self','$responsibility','$initiative','$concentration','$punctuality','$regularity','$weight','$project','$working','$absent','$grades') ";
	   $res=mysqli_query($myDB,$msql);
	   
	   }
	   if($result) {
		
		echo '<script language="javascript" type="text/javascript">';
		echo 'window.location.href="manage_marksheet.php?retcode=2";';
		echo '</script>';
	
	   
	  }
   

?>