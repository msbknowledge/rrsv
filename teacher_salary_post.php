<?php
//     echo "<pre>";

// print_r($_POST);
//     die();
 include('include/dbcon.php');
  $mobile = $_POST['mobile'];

 $year = $_POST['salary_month'];
 $year =  explode(" ",$year);
 $year = $year[1];
 
$teacher_id = $_POST['teacher_id'];
 $date = $_POST['monthly_date']; // date('m/t/Y'); 
  $first_date = $_POST['first_date'];
   $last_date = $_POST['last_date'];
 
 $f_yere = date('Y', strtotime($date));
 
 $f_date = $f_yere."-01-01";
 
 $sql3 = "SELECT sum(leave_adjust_days) AS leave_adjust_count FROM `rrsv_teacher_salary_statement` where date between '$f_date' and '$date' and teacher_id=$teacher_id";
// echo $sql3;
// die();
$qr3 = mysqli_query($myDB, $sql3);
$count3 = mysqli_fetch_array($qr3, MYSQLI_ASSOC);
//where date between '08/31/2022' and '08/31/2022' and teacher_id=3
$cl_count = $count3['leave_adjust_count'];
// echo $cl_count;
// die();
// if(9 <=10) {
//     2;
// }
   
$sql4 = "SELECT sum(p_c_amount) AS p_c_amount_val FROM `rrsv_teacher_attendence` where date between '$first_date' and '$last_date' and teacher_id=$teacher_id";
$qr4 = mysqli_query($myDB, $sql4);
$count4 = mysqli_fetch_array($qr4, MYSQLI_ASSOC);
//print_r($count4);
$p_c_amount_val = $count4['p_c_amount_val'];
$total_provision_class_amount = $p_c_amount_val;
//die();
    $sql="select * from  rrsv_teacher where id='".$teacher_id."'";
    $result=mysqli_query($myDB,$sql) or die("Error into :".mysqli_connect_error());
   if(mysqli_num_rows($result) > 0) {
    		    $rowsval=mysqli_fetch_array($result,MYSQLI_ASSOC);
    }   
    
 $total_days_of_current_month =  date('t');
 
$full_name = $rowsval['full_name'];
$d_o_b = $rowsval['d_o_b'];
$qualification = $rowsval['qualification'];
$aadhar_no = $rowsval['aadhar_no'];
$pan_no = $rowsval['pan_no'];
$bank_account_no = $rowsval['bank_account_no'];


$teacher_name = $_POST['teacher_name'];
$monthly_gross_salary = $_POST['monthly_salary'];
$total_working_days = $_POST['working_days']; //date('t'); //
$single_day_salary = round($monthly_gross_salary / 30); //$total_days_of_current_month);  // round($monthly_gross_salary / $total_working_days); //

$total_leave = $_POST['total_absent'];
$total_late = $_POST['total_late'];
$working_days = $_POST['working_days'];

$presents_days = $_POST['presents_days']; //(int)$working_days - (int)$total_leave;

$provision_class = $_POST['provision_class'];
//$provision_class = 2;

// if($provision_class > 0) {
//   //// $single_provision_class_amount =  round($single_day_salary/6);
//   // $total_provision_class_amount = $single_provision_class_amount*$provision_class;
// } else {
//     $total_provision_class_amount = 0;
// }
// echo $total_provision_class_amount;
// die();
if($total_late > 2) {
    $var = $total_late / 3;
   if(gettype($var) === "double") {
       $var = explode('.',strval($var),2);
       $var = $var[0];
   }
    $total_leave = $total_leave + $var;
}

$leave_adjust = 0;

// echo $cl_count;
// die();
if($cl_count < 10 && $total_leave > 0) {
    $leave_adjust = 1;
}
// if($total_leave > 0) {
//      $leave_adjust = 1;
//      }
// echo $leave_adjust;
// die();
// if($total_leave > 0) {
    
// }

$leave_d = 1;
if($leave_adjust > 0) {
    $leave_d = 0;
}


$leave_diposite = 1;
$total_extra_date = $_POST['total_extra_days'];

$counting = 0;

if($total_leave > 0) {
    $counting = $total_leave - $leave_diposite;
}

$duduct_value = 0;

if($counting > 0) {
    $duduct_value = $single_day_salary * $counting;
}

$extra_salary = 0;
if($total_extra_date > 0) {
   
    $extra_salary = $single_day_salary * $total_extra_date;

}


$getSalary = $monthly_gross_salary - $duduct_value;

$getSalary = $getSalary + $extra_salary + $total_provision_class_amount;
$getSalary = $getSalary + (int)$mobile;

$p_m_value = $extra_salary + $total_provision_class_amount;

// $p_m_value = $extra_salary - $duduct_value;
// $p_m_value = $p_m_value + $total_provision_class_amount;

if($p_m_value > 0) {
    $val = "Add ";
    $a_d_val = $p_m_value;
} else {
    $val = "Deduct ";
     $a_d_val = $duduct_value;
}
$salary_month =  $_POST['salary_month']; //("F"). " ".date("Y"); // date('M Y');


// echo "<b>Teacher Name -- </b>".$teacher_name;
// echo "<br>-----------<br>";
// echo "<b>Monthly Grose Salary -- </b>".$monthly_gross_salary;
// echo "<br>-----------<br>";
// echo "<b>Salary Monthly -- </b>".$salary_month;
// echo "<br>-----------<br>";

// echo "working Days -- ".$working_days;
// echo "<br>-----------<br>";
// echo "Presents Days -- ".$presents_days;
// echo "<br>-----------<br>";
// echo "Absent Days -- ".$total_leave;
// echo "<br>-----------<br>";
// echo "late Days -- ".$total_late;
// echo "<br>-----------<br>";
// echo "leave Adjust Days -- ".$leave_adjust;
// echo "<br>-----------<br>";
// echo "Leave Deposite Days -- ".$leave_d;
// echo "<br>-----------<br>";
// echo "Extra Days -- ".$total_extra_date;
// echo "<br>-----------<br>";

// echo "No of P.C Class -- $provision_class";
// echo "<br>-----------<br>";
// echo "P.C Amount -- $total_provision_class_amount";
// echo "<br>-----------<br>";

// // echo "(+)/(-) Days -- ".$counting;
// // echo "<br>-----------<br>";
// echo "$val Amount // ".$p_m_value;
// echo "<br>-----------<br>";
// echo "<b>Total -- </b>".$getSalary;
// echo "<br>-----------<br>";
// echo "Mobile 240";
// echo "<br>";
////////////////////////////////////////////
////////////////////////////////////////////
 $teacher_id = (int)$_POST['teacher_id'];
//  $strsql="select * from  rrsv_teacher_salary_statement where teacher_id='".$teacher_id."' and date='".$date."'";
//     		$result=mysqli_query($myDB,$strsql) or die("Error into :".mysqli_connect_error());
//     		if(mysqli_num_rows($result) > 0) {
//     		    //update
//     		    $sqlIn="update rrsv_teacher_salary_statement  set
//             						monthly_grose_salary         	        ='".$monthly_gross_salary."',
//                     			    salary_month             	='".$salary_month."',
//                     			    year             	='".$year."',
//                     				working_days             	='".$working_days."',
//                     				presents_days	='".$presents_days."',
                    				
//                     				absent_days           	='".$total_leave."',
//                     				late_days         	        ='".$total_late."',
//                     				leave_adjust_days             	='".$leave_adjust."',
//                     				leave_deposite_days             	='".$leave_d."',
//                     				extra_days	='".$total_extra_date."',
                    				
//                     				no_of_pc_class           	='".$provision_class."',
//                     				pc_amount         	        ='".$total_provision_class_amount."',
//                     				deduct_amount             	='".$a_d_val."',
//                     				amount_status             	='".$val."',
//                     				total             	='".$getSalary."',
//                     				mobile                 = '".$mobile."',
//             		    	where teacher_id							='".$teacher_id."'
//             		    	and date							='".$date."'";
//     		} else {
//     		    // insert
//     		   $sqlIn="insert into rrsv_teacher_salary_statement  
// 						set
// 				teacher_id           	='".$teacher_id."',
// 				monthly_grose_salary         	        ='".$monthly_gross_salary."',
// 				salary_month             	='".$salary_month."',
// 				year             	='".$year."',
// 				date             	='".$date."',
// 				working_days             	='".$working_days."',
// 				presents_days	='".$presents_days."',
				
// 				absent_days           	='".$total_leave."',
// 				late_days         	        ='".$total_late."',
// 				leave_adjust_days             	='".$leave_adjust."',
// 				leave_deposite_days             	='".$leave_d."',
// 				extra_days	='".$total_extra_date."',
				
// 				no_of_pc_class           	='".$provision_class."',
//                 pc_amount         	        ='".$total_provision_class_amount."',
// 				deduct_amount             	='".$a_d_val."',
// 				amount_status             	='".$val."',
// 				total             	='".$getSalary."',
// 				mobile                 = '".$mobile."',
// 				absent_deduct_amount             	='".$duduct_value."',
// 				extra_days_amount             	='".$extra_salary."'";
//     		}
    		
//     		$result=mysqli_query($myDB,$sqlIn) or die("Error into update post:".mysqli_connect_error());
    		
    		
//     	$currenr=date('Y-m-d');	
//      $Inpl="insert into rrsv_scl_pl set	pl_date='".$currenr."',exp_amount='".$getSalary."',purpose='Salary Paid'";
//             	$res=mysqli_query($myDB,$Inpl) ;		
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .heading h4 {
            text-align: center;

        }

        .heading p {
            font-size: 25px;
            font-family: serif;
            text-align: center;
            margin-left: 15%;
            margin-right: 15%;
        }

        table,
        td,
        tr,
        th {
            border: 2px solid black;

        }

        .marks {
            margin-top: 50px;
        }

        .disclaim {

            font-style: italic;
            text-align: center;


        }

        h6 {
            padding: 120px;
            text-decoration: overline;
            text-align: end;
        }
    </style>
    <title>Teacher Salary Slip</title>
</head>

<body>
        <button type="button" id="backPageButton" onclick="window.history.back()" class="btn btn-info" style="color: #fff">Back</button>
	<button type="button" onclick = "printDiv('print')" class="btn btn-info">Print</button>
	
		<button type="button" onclick = "submitSalary()" class="btn btn-danger">SUBMIT</button>

		 <form id="salarySubmit" action="teacher_salary_submit.php" method="post">
	        <input type="hidden" id="teacher_id" name="teacher_id" value="<?=$teacher_id;?>">
	        <input type="hidden" id="monthly_grose_salary" name="monthly_grose_salary" value="<?=$monthly_gross_salary;?>">
	        <input type="hidden" id="salary_month" name="salary_month" value="<?=$salary_month;?>">
	        <input type="hidden" id="year" name="year" value="<?=$year;?>">
	        <input type="hidden" id="date" name="date" value="<?=$date;?>">
	        <input type="hidden" id="working_days" name="working_days" value="<?=$working_days;?>">
	        <input type="hidden" id="presents_days" name="presents_days" value="<?=$presents_days;?>">
	        <input type="hidden" id="absent_days" name="absent_days" value="<?=$total_leave;?>">
	        <input type="hidden" id="late_days" name="late_days" value="<?=$total_late;?>">
	        <input type="hidden" id="leave_adjust_days" name="leave_adjust_days" value="<?=$leave_adjust;?>">
	        <input type="hidden" id="leave_deposite_days" name="leave_deposite_days" value="<?=$leave_d;?>">
	        <input type="hidden" id="extra_days" name="extra_days" value="<?=$total_extra_date;?>">
	        <input type="hidden" id="no_of_pc_class" name="no_of_pc_class" value="<?=$provision_class;?>">
	        <input type="hidden" id="pc_amount" name="pc_amount" value="<?=$total_provision_class_amount;?>">
	        <input type="hidden" id="deduct_amount" name="deduct_amount" value="<?=$a_d_val;?>">
	        <input type="hidden" id="amount_status" name="amount_status" value="<?=$val;?>">
	        <input type="hidden" id="total" name="total" value="<?=$getSalary;?>">
	        <input type="hidden" id="mobile" name="mobile" value="<?=$mobile;?>">
	        <input type="hidden" id="absent_deduct_amount" name="absent_deduct_amount" value="<?=$duduct_value;?>">
	        <input type="hidden" id="extra_days_amount" name="extra_days_amount" value="<?=$extra_salary;?>">
	    </form>
	    
	<div id="print">
	    
        <div class="container">

    <div class="heading">
        

        <h4 style="text-decoration:underline">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h4>
    </div>
    <div class="container">
        <div>
            <table class="table">
                <tr>
                    <th class="col" colspan="2">Salary slip for :<?=strtoupper($salary_month)?></th>
                </tr>

                <tr>
                    <th class="col">Teacher Name :&nbsp;<span><?=strtoupper($full_name)?></span></th>
                    <th class="col">Date Of Birth :&nbsp;<span><?=strtoupper($d_o_b)?></span></th>
                </tr>
               <tr>
                    <th class="col">Qualification :&nbsp;<span><?=strtoupper($qualification)?></span></th>
                    <th class="col">Aadhar No :&nbsp;<span><?=strtoupper($aadhar_no)?></span></th>
                </tr>
                <tr>
                    <th class="col">Pan No :&nbsp;<span><?=strtoupper($pan_no)?></span></th>
                    <th class="col">Bank Account No :&nbsp;<span><?=strtoupper($bank_account_no)?></span></th>
                </tr>
            </table>
        </div>
        <div class="marks">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-md-1">Sl No.</th>
                        <th class="col-md-4">About</th>
                        <th class="col-md-2">Description</th>
                        <th class="col-md-1">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Salary Month</td>
                        <td><?=$salary_month;?></td>
                        <td>----</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Monthly Grose Salary</td>
                        <td>----</td>
                        <td><?=$monthly_gross_salary?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Working Days</td>
                        <td><?=$working_days;?></td>
                        <td>----</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Absent Days</td>
                        <td><?=$total_leave?></td>
                        <td>(-)<?=$duduct_value;?></td>
                    </tr>
                   <tr>
                        <td>5</td>
                        <td>Leave Adjust Days</td>
                        <td><?=$leave_adjust?></td>
                        <td>----</td>
                    </tr>
                   <tr>
                        <td>6</td>
                        <td>Leave Deposite Days</td>
                        <td><?=$leave_d?></td>
                        <td>----</td>
                    </tr>
                    
                     <tr>
                        <td>7</td>
                        <td>No Of PC Class</td>
                        <td><?=$provision_class?></td>
                         <td>(+)<?=$total_provision_class_amount?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Extra Days amount</td>
                        <td><?=$total_extra_date;?></td>
                        <td>(+)<?=$extra_salary?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Mobile</td>
                        <td>----</td>
                        <td>(+)<?=$mobile?></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Total</td>
                        <td>----</td>
                        <td><b><?=$getSalary?></b></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="disclaim">
            <p>Please report of any discrepancy within 7 days,
                Otherwise ,Institute will not be responsible for any errors in transcripts(if any)
            </p>

        </div>
        <div>
            <h6>Controller of Administitor</h6>
        </div>

    </div>
    </div>
</div>
</body>

</html>
<script>
function printDiv(id){
        var printContents = document.getElementById(id).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}

function submitSalary() {
     if (confirm("Are you sure want to submit this statement?")) {
       document.getElementById("salarySubmit").submit();// Form submission Success!

  }
}
</script>
