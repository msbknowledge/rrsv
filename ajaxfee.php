<?php
session_start();


 $token = $_SESSION['_token'];
include('include/dbcon.php');
	$id=0;
	$id								=$myDB->escape_string(trim(addslashes($_POST['id'])));
	$scl_class  	            	=$myDB->escape_string(trim(addslashes($_POST['class_name'])));
	$scl_session 	            	=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
	$monthly_admission_fee 	      	=$myDB->escape_string(trim(addslashes($_POST['monthly_admission_fee'])));
	$monthly_fee 	            	=$myDB->escape_string(trim(addslashes($_POST['monthly_fee'])));
	$ad_form_charge  	            	=$myDB->escape_string(trim(addslashes($_POST['ad_form_charge'])));
	$book_charge 	            	=$myDB->escape_string(trim(addslashes($_POST['book_charge'])));
	$uniform 	      	=$myDB->escape_string(trim(addslashes($_POST['uniform'])));
	$diary 	            	=$myDB->escape_string(trim(addslashes($_POST['diary'])));
    $icard 	            	=$myDB->escape_string(trim(addslashes($_POST['icard'])));
    $bag 	            	=$myDB->escape_string(trim(addslashes($_POST['bag'])));
    $sweater 	            	=$myDB->escape_string(trim(addslashes($_POST['sweater'])));
    $shoes 	            	=$myDB->escape_string(trim(addslashes($_POST['shoes'])));
	$tokenpost 		                =$myDB->escape_string(trim(addslashes($_POST['token'])));
	$readmission_fee 		                =$myDB->escape_string(trim(addslashes($_POST['readmission_fee'])));

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
	
	$sqlUp="update rrsv_fee  set
				scl_class           	='".$scl_class."',
				scl_session         	='".$scl_session."',
				monthly_admission_fee	='".$monthly_admission_fee."',
				monthly_fee	='".$monthly_fee."',
				ad_form_charge         	='".$ad_form_charge."',
				book_charge         	='".$book_charge."',
                uniform             	='".$uniform."',
				diary         	        ='".$diary."',
				icard	                ='".$icard."',
				bag                 	='".$bag."',
				sweater             	='".$sweater."',
				readmission_fee         ='".$readmission_fee."',
                shoes	                ='".$shoes."'
		    	where id							='".$id."'";
		
		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
		
		if($result) {
			echo 1;
		
		}
		
	} else {		// Add Mode
	
		$strsql="select * from rrsv_fee where scl_class='".$scl_class."' and scl_session='".$scl_session."'";
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
		
		$sqlIn="insert into rrsv_fee 
						set
				scl_class           	='".$scl_class."',
				scl_session         	='".$scl_session."',
				monthly_admission_fee	='".$monthly_admission_fee."',
				monthly_fee	            ='".$monthly_fee."',
                ad_form_charge         	='".$ad_form_charge."',
                book_charge         	='".$book_charge."',
                uniform             	='".$uniform."',
                diary         	        ='".$diary."',
                icard	                ='".$icard."',
                bag                 	='".$bag."',
                sweater             	='".$sweater."',
                readmission_fee         ='".$readmission_fee."',
                shoes	                ='".$shoes."'";

		
		$result=mysqli_query($myDB,$sqlIn) ;
		
		if($result) {
		
		  echo 0;
    }
   
		
	}
	}
?>


