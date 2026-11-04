<?php

// if(isset($_POST)) {
//     echo "<pre>";
//     print_r($_POST);
// }
//die;
 include('include/dbcon.php');


	$teacher_id								=	$myDB->escape_string(trim(addslashes($_POST['teacher_id'])));
	$monthly_grose_salary					=	$myDB->escape_string(trim(addslashes($_POST['monthly_grose_salary'])));
	$salary_month							=	$myDB->escape_string(trim(addslashes($_POST['salary_month'])));
	$year							    	=	$myDB->escape_string(trim(addslashes($_POST['year'])));
	$date							    	=	$myDB->escape_string(trim(addslashes($_POST['date'])));
	$working_days							=	$myDB->escape_string(trim(addslashes($_POST['working_days'])));
	$presents_days							=	$myDB->escape_string(trim(addslashes($_POST['presents_days'])));
	
	$total_leave							=	$myDB->escape_string(trim(addslashes($_POST['absent_days'])));
	$total_late								=	$myDB->escape_string(trim(addslashes($_POST['late_days'])));
	
	$leave_adjust							=	$myDB->escape_string(trim(addslashes($_POST['leave_adjust_days'])));
	$leave_d								=	$myDB->escape_string(trim(addslashes($_POST['leave_deposite_days'])));
	$total_extra_date						=	$myDB->escape_string(trim(addslashes($_POST['extra_days'])));
	$provision_class						=	$myDB->escape_string(trim(addslashes($_POST['no_of_pc_class'])));
	$total_provision_class_amount			=	$myDB->escape_string(trim(addslashes($_POST['pc_amount'])));
	$a_d_val								=	$myDB->escape_string(trim(addslashes($_POST['deduct_amount'])));
	$val							    	=	$myDB->escape_string(trim(addslashes($_POST['amount_status'])));
	$getSalary								=	$myDB->escape_string(trim(addslashes($_POST['total'])));
	$mobile							    	=	$myDB->escape_string(trim(addslashes($_POST['mobile'])));
	$duduct_value							=	$myDB->escape_string(trim(addslashes($_POST['absent_deduct_amount'])));
	$extra_salary							=	$myDB->escape_string(trim(addslashes($_POST['extra_days_amount'])));


    		   $sqlIn="insert into rrsv_teacher_salary_statement  
						set
				teacher_id           	='".$teacher_id."',
				monthly_grose_salary         	        ='".$monthly_grose_salary."',
				salary_month             	='".$salary_month."',
				year             	='".$year."',
				date             	='".$date."',
				working_days             	='".$working_days."',
				presents_days	='".$presents_days."',
				
				absent_days           	='".$total_leave."',
				late_days         	        ='".$total_late."',
				leave_adjust_days             	='".$leave_adjust."',
				leave_deposite_days             	='".$leave_d."',
				extra_days	='".$total_extra_date."',
				
				no_of_pc_class           	='".$provision_class."',
                pc_amount         	        ='".$total_provision_class_amount."',
				deduct_amount             	='".$a_d_val."',
				amount_status             	='".$val."',
				total             	='".$getSalary."',
				mobile                 = '".$mobile."',
				absent_deduct_amount             	='".$duduct_value."',
				extra_days_amount             	='".$extra_salary."'";
    		
    		
    		$result=mysqli_query($myDB,$sqlIn) or die("Error into update post:".mysqli_connect_error());
    		
    		
    		 	$currenr=date('Y-m-d');	
     $Inpl="insert into rrsv_scl_pl set	pl_date='".$currenr."',exp_amount='".$getSalary."',session='".$year."',purpose='Salary Paid'";
            	$res=mysqli_query($myDB,$Inpl) ;
            	
            	
    		 if ($res === TRUE) {
        echo '<script language="javascript" type="text/javascript">';
        echo 'alert("Record Update successfully.");';
        echo 'window.location.href="teacher_salary.php?"';
        echo '</script>';
        }
        
   


?>