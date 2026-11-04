<?php
// print_r($_GET);
//     die();
 include('include/dbcon.php');
 if(isset($_GET['teacher_id'])) {
    $teacher_id = (int)$_GET['teacher_id'];
    $date = $_GET['date'];
    
    $sql = "DELETE FROM rrsv_teacher_attendence WHERE teacher_id='".$teacher_id."' and date='".$date."'";
    $result=mysqli_query($myDB,$sql);
    $sql2 = "DELETE FROM rrsv_p_c_teacher_class WHERE teacher_id='".$teacher_id."' and date='".$date."'";
    $result2=mysqli_query($myDB,$sql2);
    if ($result === TRUE && $result2 === TRUE) {
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
// echo "<pre>";
// print_r($_POST);
// die();
    session_start();
    $token = $_SESSION['_token'];
   
	$id=0;
	$id								=$myDB->escape_string(trim(addslashes((int)$_POST['id'])));
	$teacher_id  	            	=$myDB->escape_string(trim(addslashes($_POST['teacher_id'])));
	$date 	                 	=$myDB->escape_string(trim(addslashes($_POST['date'])));
	$in_time 	      	               =$myDB->escape_string(trim(addslashes($_POST['in_time'])));
	$out_time  	            	=$myDB->escape_string(trim(addslashes($_POST['out_time'])));
//	$scl_session 	            	=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
	$tokenpost 		                =$myDB->escape_string(trim(addslashes($_POST['token'])));
//	$provision_class 		                =$myDB->escape_string(trim(addslashes($_POST['provision_class'])));

$strsql="select * from rrsv_teacher_attendence where teacher_id='".$teacher_id."' and date='".$date."' and in_time ='".$in_time."'";
    		$result=mysqli_query($myDB,$strsql) or die("Error into :".mysqli_connect_error());
    		
    		if(mysqli_num_rows($result) > 0) {
        		$errcode=1;		//Duplicate check
        			echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("you have entired is duplicate55.");';
        			echo 'window.history.go(-1);';
        			echo '</script>';	
    		} 
    		
	
	/////////////////////////////////////
	
	$p_c_teacher_class_arrcount               =count($_POST['p_c_teacher_class']['p_c_teacher_id']); 
	$p_c_class = 0;
	$p_c_amount = 0;

	if($p_c_teacher_class_arrcount > 0) {
	    for($i=0; $i<$p_c_teacher_class_arrcount; $i++) {
	        $p_c_teacher_id = $_POST['p_c_teacher_class']['p_c_teacher_id'][$i];
	        $cls_count = $_POST['p_c_teacher_class']['cls_count'][$i];

	        $sql5 = "SELECT monthly_salary FROM rrsv_teacher WHERE id='".$p_c_teacher_id."'";
            $result5 = mysqli_query($myDB,$sql5);
            $s = mysqli_fetch_array($result5);
            $monthly_salary = $s['monthly_salary'];
            
            $single_day_salary = $monthly_salary / 30;
            
           $one_class_salary = $single_day_salary / 6;
           
            $amount = round($one_class_salary * $cls_count);
            //die();
		    $sql= "insert into  rrsv_p_c_teacher_class(date,teacher_id,p_c_teacher_id,cls_count,amount)values('$date','$teacher_id','$p_c_teacher_id','$cls_count','$amount') ";
            $res=mysqli_query($myDB,$sql);
        }
        
        $sql6 = "SELECT sum(amount) AS amount_val,sum(cls_count) AS count_val FROM `rrsv_p_c_teacher_class` where date='$date' and teacher_id=$teacher_id";
        $result6 = mysqli_query($myDB,$sql6);
        $s = mysqli_fetch_array($result6);
        $p_c_amount = $s['amount_val'];
        $p_c_class = $s['count_val'];
        
	}

	/////////////////////////////////////
	
	 if(isset($_POST['is_extra_day'])) {
        $is_extra_day = 1;
     } else {
         $is_extra_day = 0;
     }

	if($out_time == "" || $out_time == null) {
	    $time_in_second = null;
	} else {
	    $time_in_second =   strtotime($out_time) - strtotime($in_time);
	}
	
	/////////////////////////////
	$sql6 = "SELECT in_time FROM rrsv_time WHERE id=1";
            $result6 = mysqli_query($myDB,$sql6);
            $res6 = mysqli_fetch_array($result6);
            $master_in_time = $res6['in_time'];
            //die();
    /////////////////////////////////
            
    if(strtotime($in_time) > strtotime($master_in_time)) {
        $is_late = 2;
    } else {
         $is_late = 1;
    }


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
            	    $sqlUp="update rrsv_teacher_attendence  set
            					teacher_id           	='".$teacher_id."',
                				date         	        ='".$date."',
                				in_time             	='".$in_time."',
                				out_time             	='".$out_time."',
                				time_in_second	='".$time_in_second."'
                                is_late	='".$is_late."'
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
    		$strsql="select * from rrsv_teacher_attendence where teacher_id='".$teacher_id."' and date='".$date."' and in_time ='".$in_time."'";
    		$result=mysqli_query($myDB,$strsql) or die("Error into :".mysqli_connect_error());
    		if(mysqli_num_rows($result) > 0) {
        		$errcode=1;		//Duplicate check
        			echo '<script language="javascript" type="text/javascript">';
        			echo 'alert("you have entired is duplicate.");';
        			echo 'window.history.go(-1);';
        			echo '</script>';	
    		} else {
		        $sqlIn="insert into rrsv_teacher_attendence  
						set
				teacher_id           	='".$teacher_id."',
				date         	        ='".$date."',
				in_time             	='".$in_time."',
				out_time             	='".$out_time."',
				time_in_second	='".$time_in_second."',
				is_late                 = '".$is_late."',
				p_c_class        = '".$p_c_class."',
				p_c_amount        = '".$p_c_amount."',
				is_extra_day           = '".$is_extra_day."'";
				//die();
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
//	}





?>