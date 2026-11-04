<?php
session_start();


 $token = $_SESSION['_token'];
include('include/dbcon.php');
	$id=0;
	$s_id								=$myDB->escape_string(trim(addslashes($_POST['s_id'])));
	$a_class 		                    =$myDB->escape_string(trim(addslashes($_POST['a_class'])));
	$name 		                        =$myDB->escape_string(trim(addslashes($_POST['name'])));
	$fname 		                        =$myDB->escape_string(trim(addslashes($_POST['fname'])));
	$address 	                    	=$myDB->escape_string(trim(addslashes($_POST['address'])));
	$p_class	                    	=$myDB->escape_string(trim(addslashes($_POST['p_class'])));
	$s_char		                        =$myDB->escape_string(trim(addslashes($_POST['s_char'])));
	$scl_dist	                    	=$myDB->escape_string(trim(addslashes($_POST['scl_dist'])));	
	$d_o_a  	                    	=$myDB->escape_string(trim(addslashes($_POST['d_o_a'])));
	$d_o_t	                        	=$myDB->escape_string(trim(addslashes($_POST['d_o_t'])));
	$strem	                        	=$myDB->escape_string(trim(addslashes($_POST['strem'])));
	$scl_dob	                       	=$myDB->escape_string(trim(addslashes($_POST['scl_dob'])));	
	
	$tokenpost 	                    	=$myDB->escape_string(trim(addslashes($_POST['token'])));
    $name=strtoupper($name);
	if(isset($tokenpost)==$token){
	  
// 	if($id > 0) {
	
// 		/*$strsql="select * from class where class_name='".$class_name."' and id<>'".$id."'";
// 		$result=mysqli_query($myDB,$strsql) or die("Error into selecting category info by name and id post:".mysqli_connect_error());
// 		if(mysqli_num_rows($result) > 0) {
		
// 			$errcode=2;		//Duplicate check
// 			echo '<script language="javascript" type="text/javascript">';
// 			echo 'alert(" name you have entered is duplicate!.");';
// 			echo 'window.history.go(-1);';
// 			echo '</script>';
// 			exit();
// 		}*/
	
// 		$sqlUp="update rrsv_inquery 		set
// 		class_name	='".$class_name."',
// 				scl_session         ='".$scl_session."',
// 				name	            ='".$name."',
// 				phone               ='".$phone."',
// 				price               ='".$price."'
// 		    	where id			='".$id."'";
		
// 		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
		
// 		if($result) {
// 			echo 1;
		
// 		}
		
// 	} else {

		$currenr=date('Y-m-d');
		
		$sqlIn="insert into rrsv_tc 
						set
		    	s_id	        ='".$s_id."',				
				a_class	        ='".$a_class."',
				p_class         ='".$p_class."',
				name        	='".$name."',
				fname           ='".$fname."',
				d_o_t           ='".$d_o_t."',
				d_o_a           ='".$d_o_a."',
				address         ='".$address."',
				scl_dist        ='".$scl_dist."',
				strem           ='".$strem."',
				scl_dob         ='".$scl_dob."',
				s_char          ='".$s_char."'";
                
	       	$result=mysqli_query($myDB,$sqlIn) ;
			
            
          
		if($result) {
		  echo 0;
    }
	} 
//}
?>


