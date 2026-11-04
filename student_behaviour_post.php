<?php
// echo "<pre>";
// print_r($_POST);
// die();
include('include/dbcon.php');

if(isset($_GET['dlt_id'])) {
    $id = (int)$_GET['dlt_id'];

    $sql = "DELETE FROM rrsv_student_behaviour WHERE id='".$id."'";
    $result=mysqli_query($myDB,$sql);
   
    if ($result === TRUE) {
        echo '<script language="javascript" type="text/javascript">';
        echo 'alert("Record deleted successfully.");';
        echo 'window.location.href="manage_student_behaviour.php?"';
        echo '</script>';
        } else {
            echo '<script language="javascript" type="text/javascript">';
            echo 'alert("Error deleting record:");';
        	echo 'window.history.go(-1);';
        	echo '</script>';
        }
    exit();
 }
 
 


	$teacher_id								=	$myDB->escape_string(trim(addslashes($_POST['teacher_id'])));
	$student_id 		                    =$myDB->escape_string(trim(addslashes($_POST['student_id'])));
	$activties 		                    =$myDB->escape_string(trim(addslashes($_POST['activties'])));
	$date 		                            =$myDB->escape_string(trim(addslashes($_POST['date'])));
	$des 		                            =$myDB->escape_string(trim(addslashes($_POST['des'])));
    $unit 		                            =$myDB->escape_string(trim(addslashes($_POST['unit'])));
    $scl_session 		                            =$myDB->escape_string(trim(addslashes($_POST['scl_session'])));

		
		$sqlIn="insert into rrsv_student_behaviour 
						set
				teacher_id	='".$teacher_id."',
				student_id ='".$student_id."',
				activties ='".$activties."',
				date	='".$date."',
			    des  ='".$des."',
				unit  ='".$unit."',
				scl_session  ='".$scl_session."'";
				

		
		$result=mysqli_query($myDB,$sqlIn) ;
		
  		if($result > 0) {
		    $res = "Student activties Add Sucussfully";    
        } else {
    		$res = "Error!in insert.Try again.";    
        } 
        
?>
        
        
            <script language="javascript" type="text/javascript">
			alert("<?=$res?>");
			window.location.href='manage_student_behaviour.php?';
			</script>
