<?php
session_start();


 $token = $_SESSION['_token'];
include('include/dbcon.php');
	$id=0;
	$id								=$myDB->escape_string(trim(addslashes($_POST['id'])));
	$ad_form_charge  	            	=$myDB->escape_string(trim(addslashes($_POST['ad_form_charge'])));
	$book_charge 	            	=$myDB->escape_string(trim(addslashes($_POST['book_charge'])));
	$uniform 	      	=$myDB->escape_string(trim(addslashes($_POST['uniform'])));
	$diary 	            	=$myDB->escape_string(trim(addslashes($_POST['diary'])));
    $icard 	            	=$myDB->escape_string(trim(addslashes($_POST['icard'])));
    $bag 	            	=$myDB->escape_string(trim(addslashes($_POST['bag'])));
    $sweater 	            	=$myDB->escape_string(trim(addslashes($_POST['sweater'])));
    $shoes 	            	=$myDB->escape_string(trim(addslashes($_POST['shoes'])));
	$tokenpost 		                =$myDB->escape_string(trim(addslashes($_POST['token'])));

	if(isset($tokenpost)==$token){
        if($id > 0) {
$sqlUp="update rrsv_other_fee  set
                ad_form_charge         	='".$ad_form_charge."',
				book_charge         	='".$book_charge."',
                uniform             	='".$uniform."',
				diary         	        ='".$diary."',
				icard	                ='".$icard."',
				bag                 	='".$bag."',
				sweater             	='".$sweater."',
                shoes	                ='".$shoes."'
		    	where id				='".$id."'";
		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
			if($result) {
			echo 1;
		
		}
		
	} else {		

	echo	$sqlIn="insert into rrsv_other_fee 
						set
                        ad_form_charge         	='".$ad_form_charge."',
                        book_charge         	='".$book_charge."',
                        uniform             	='".$uniform."',
                        diary         	        ='".$diary."',
                        icard	                ='".$icard."',
                        bag                 	='".$bag."',
                        sweater             	='".$sweater."',
                        shoes	                ='".$shoes."'";
	
		$result=mysqli_query($myDB,$sqlIn) ;
		
		if($result) {
		
		  echo 0;
    }
   
		
	}
	}
?>


