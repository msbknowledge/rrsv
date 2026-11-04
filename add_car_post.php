<?php
session_start();


 $token = $_SESSION['_token'];
include('include/dbcon.php');
	$id=0;
	$id								=	$myDB->escape_string(trim(addslashes($_POST['id'])));
	$kilo 		=$myDB->escape_string(trim(addslashes($_POST['kilo'])));
    $fair 		=$myDB->escape_string(trim(addslashes($_POST['fair'])));
    $scl_session 	            	=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
	$tokenpost 		=$myDB->escape_string(trim(addslashes($_POST['token'])));

	if(isset($tokenpost)==$token){
	  
	

if($id > 0) {
	
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
	
	$sqlUp="update rrsv_car  set
				kilo	='".$kilo."',
				scl_session='".$scl_session."',
				fair	='".$fair."'

	where id							='".$id."'";
		
		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
		
		if($result) {
			echo 1;
		
		}
		
	} else {		// Add Mode
	
		$strsql="select * from rrsv_car where scl_session='".$scl_session."' and kilo='".$kilo."'";
		$result=mysqli_query($myDB,$strsql) or die("Error into selecting course info by Class name post:".mysqli_connect_error());
		if(mysqli_num_rows($result) > 0) {
			
		$errcode=1;		//Duplicate check
// 			echo '<script language="javascript" type="text/javascript">';
// 			echo 'alert("name you have entired is duplicate.");';
// 			echo 'window.history.go(-1);';
// 			echo '</script>';	
// 			exit();
            echo 2;
		    exit();
		}
		
		
		$sqlIn="insert into rrsv_car 
						set
						kilo  ='".$kilo."',
						scl_session='".$scl_session."',	
				fair	='".$fair."'";
			

		
		$result=mysqli_query($myDB,$sqlIn) ;
		
		if($result) {
		
		  echo 0;
    }
   
		
	}
	}
?>


