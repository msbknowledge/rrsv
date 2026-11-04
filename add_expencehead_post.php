<?php
session_start();


 $token = $_SESSION['_token'];
include('include/dbcon.php');
	$id=0;
	$id								=	$myDB->escape_string(trim(addslashes($_POST['id'])));
	$expence 		=$myDB->escape_string(trim(addslashes($_POST['expence'])));
	$tokenpost 		=$myDB->escape_string(trim(addslashes($_POST['token'])));
$expence=strtoupper($expence);
	if(isset($tokenpost)==$token){
	  
	

if($id > 0) {
	
	
	$sqlUp="update  rrsv_expence_head  set
				expence	='".$expence."'
				

	where id							='".$id."'";
		
		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
		

 
		if($result) {
			echo 1;
		
		}
		
	} else {		// Add Mode

        
	
            
		
		
		$sqlIn="insert into  rrsv_expence_head 
						set
				expence	='".$expence."'";
			

		
		$result=mysqli_query($myDB,$sqlIn) ;
		

	if($result) {
	
		 echo 0;
   
		
	}
	}
	}
?>


