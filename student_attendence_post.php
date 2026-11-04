<?php
// echo "<pre>";
// print_r($_POST);
// die();
 include('include/dbcon.php');
 if(isset($_GET['dId'])) {
    $student_id = (int)$_GET['dId'];
    $sql = "DELETE FROM rrsv_student_attendence WHERE id='".$student_id."'";
    $result=mysqli_query($myDB,$sql) ;
    if ($result === TRUE) {
        echo '<script language="javascript" type="text/javascript">';
        echo 'alert("Record deleted successfully.");';
        echo 'window.location.href="teacher_attendence.php?"';
        echo '</script>';
        } else {
            echo '<script language="javascript" type="text/javascript">';
            echo 'alert("Error deleting record:");';
        	echo 'window.history.go(-1);';
        	echo '</script>';
        }

        exit();
 }


    session_start();
    $token = $_SESSION['_token'];
   
	$id=0;
	$id								=$myDB->escape_string(trim(addslashes((int)$_POST['id'])));
	$student_id  	            	=$myDB->escape_string(trim(addslashes($_POST['student_id'])));
	$date 	                 	=$myDB->escape_string(trim(addslashes($_POST['date'])));
	$unit 	      	               =$myDB->escape_string(trim(addslashes($_POST['unit'])));
	$scl_session  	            	=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
//	$tokenpost 		                =$myDB->escape_string(trim(addslashes($_POST['token'])));
	
	
// 	echo $time_in_second;
// 	die();

//	if(isset($tokenpost)==$token){
        if($id > 0) {
            // print_r($_POST);
            // die();
            		/*$strsql="select * from class where class_name='".$class_name."' and id<>'".$id."'";
            		$result=mysqli_query($myDB,$strsql) or die("Error into selecting category info by name and id post:".mysqli_connect_error());
            		if(mysqli_num_rows($result) > 0) {
            		
            			$errcode=2;		//Duplicate check
            			echo '<script language="javascript" type="text/javascript">';
            			echo 'alert(" name you have entered is duplicate!.");';
            			echo 'window.history.go(-1);';
            			echo '</script>';
            			exit();
            		}*/
            	    $sqlUp="update rrsv_student_attendence  set
            					student_id           	='".$student_id."',
                				date         	        ='".$date."',
                			    unit             	='".$unit."',
				                scl_session             	='".$scl_session."'
            
            		    	where id							='".$id."'";
            		
            		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
            		
            		if($result) {
                    echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("Attendence Edit successfully .");';
        			echo 'window.history.go(-2);';
        			echo '</script>';
                } else {
                	echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("error to insert.");';
        			echo 'window.history.go(-1);';
        			echo '</script>';
                }
                
	    } else {		// Add Mode
    		$strsql="select * from rrsv_student_attendence where student_id='".$student_id."' and date='".$date."'";
    		$result=mysqli_query($myDB,$strsql) or die("Error into :".mysqli_connect_error());
    		if(mysqli_num_rows($result) > 0) {
        		$errcode=1;		//Duplicate check
        			echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("you have entired is duplicate.");';
        			echo 'window.history.go(-1);';
        			echo '</script>';	
    		} else {
		        $sqlIn="insert into rrsv_student_attendence  
						set
				student_id           	='".$student_id."',
				date         	        ='".$date."',
				unit             	='".$unit."',
				scl_session             	='".$scl_session."'";
				
		        $result=mysqli_query($myDB,$sqlIn) ;
		        if($result) {
                    echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("Attendence add successfully .");';
        			echo 'window.history.go(-1);';
        			echo '</script>';
                } else {
                	echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("error to insert.");';
        			echo 'window.history.go(-1);';
        			echo '</script>';
                }
		    }
		
	    }
	//}





?>