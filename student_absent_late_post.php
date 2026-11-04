<?php
// echo "<pre>";
// print_r($_GET);
// die();
include('include/dbcon.php');

if(isset($_GET['dlt_id'])) {
    $id = (int)$_GET['dlt_id'];

    $sql = "DELETE FROM rrsv_student_attendence WHERE id='".$id."'";
    $result=mysqli_query($myDB,$sql);
   
    if ($result === TRUE) {
        echo '<script language="javascript" type="text/javascript">';
        echo 'alert("Record deleted successfully.");';
        echo 'window.location.href="manage_student_absent_late.php?"';
        echo '</script>';
        } else {
            echo '<script language="javascript" type="text/javascript">';
            echo 'alert("Error deleting record:");';
        	echo 'window.history.go(-1);';
        	echo '</script>';
        }
    exit();
 }
 
//  Array
// (
//     [token] => Hk4A3ehHsjhoaT6BlJ4E7MnYx8GQOXd2
//     [scl_session] => 2022
//     [unit] => STANDERD 2
//     [student_id] => 12
//     [status] => Absent
//     [date] => 2022-10-14
// )


	$scl_session								=	$myDB->escape_string(trim(addslashes($_GET['scl_session'])));
	$class_name 		                    =$myDB->escape_string(trim(addslashes($_GET['unit'])));
	$date 		                            =$myDB->escape_string(trim(addslashes($_GET['date'])));
	$student_id 		                            =$myDB->escape_string(trim(addslashes($_GET['student_id'])));
    $status 		                            =$myDB->escape_string(trim(addslashes($_GET['status'])));

		
		$sqlIn="insert into rrsv_student_attendence 
						set
				date	='".$date."',
				student_id ='".$student_id."',
				status  ='".$status."',
				class_name  ='".$class_name."',
				scl_session  ='".$scl_session."'";
				

		
		$result=mysqli_query($myDB,$sqlIn) ;
		
  		if($result > 0) {
		    $res = "Student Attendence status Add Sucussfully";    
        } else {
    		$res = "Error!in insert.Try again.";    
        } 
        
?>
        
        
            <script language="javascript" type="text/javascript">
			alert("<?=$res?>");
			window.location.href='manage_student_absent_late.php?';
			</script>
