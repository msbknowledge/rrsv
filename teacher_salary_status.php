<?php
if(isset($_GET)) {
    include('include/dbcon.php');
    
    $teacher_id = $_GET['teacher_id'];
    $month = $_GET['month']." ".$_GET['year'];
    
    $sql="select * from  rrsv_teacher where id='".$teacher_id."'";
    $result=mysqli_query($myDB,$sql) or die("Error into :".mysqli_connect_error());
   if(mysqli_num_rows($result) > 0) {
    		    $rowsval=mysqli_fetch_array($result,MYSQLI_ASSOC);
    }   
    		     
$full_name = $rowsval['full_name'];
$d_o_b = $rowsval['d_o_b'];
$qualification = $rowsval['qualification'];
$aadhar_no = $rowsval['aadhar_no'];
$pan_no = $rowsval['pan_no'];
$bank_account_no = $rowsval['bank_account_no'];

    if($teacher_id!="" && $month!="") {
        $strsql="select * from  rrsv_teacher_salary_statement where teacher_id='".$teacher_id."' and salary_month='".$month."'";
        
    		$result=mysqli_query($myDB,$strsql) or die("Error into :".mysqli_connect_error());
    		if(mysqli_num_rows($result) > 0) {
    		     $rows=mysqli_fetch_array($result,MYSQLI_ASSOC);
                  
    		
    		    $teacher_id = $rows['teacher_id'];
    		    $monthly_grose_salary = $rows['monthly_grose_salary'];
    		    $salary_month = $rows['salary_month'];
    		    $working_days = $rows['working_days'];
    		    $presents_days = $rows['presents_days'];
    		    $absent_days = $rows['absent_days'];
    		    $leave_adjust_days = $rows['leave_adjust_days'];
    		    $leave_deposite_days = $rows['leave_deposite_days'];
    		    $no_of_pc_class = $rows['no_of_pc_class'];
    		    $pc_amount = $rows['pc_amount'];
    		    $deduct_amount = $rows['deduct_amount'];
    		    $total = $rows['total'];
    		    $mobile = $rows['mobile'];
    		 
    		    
    		    
    		} else { ?>
    		    <script language="javascript" type="text/javascript">
                alert("No record found");
                // if (confirm("Are you sure want generate this record?")) {
                //     var url = "teacher_salary_generate.php?teacher_id=1";
                //      location.assign(url);

                //   } else {
                    window.history.go(-1);
  //                }
  
  
            	
            	</script>
    	<?php	}
    } else {
        
    }
}
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
                    <th class="col">Teacher Name :<span><?=strtoupper($full_name)?></span></th>
                    <th class="col">Date Of Birth :<span><?=strtoupper($d_o_b)?></span></th>
                </tr>
               <tr>
                    <th class="col">Qualification :<span><?=strtoupper($qualification)?></span></th>
                    <th class="col">Aadhar No :<span><?=strtoupper($aadhar_no)?></span></th>
                </tr>
                <tr>
                    <th class="col">Pan No :<span><?=strtoupper($pan_no)?></span></th>
                    <th class="col">Bank Account No :<span><?=strtoupper($bank_account_no)?></span></th>
                </tr>
            </table>
        </div>
        <div class="marks">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-md-2">Sl No.</th>
                        <th class="col-md-5">About</th>
                        <th class="col-md-1">Description</th>

                    </tr>
                </thead>
                <tbody>
                    
                    <tr>
                        <td>2</td>
                        <td>Salary Month</td>
                        <td><?=$salary_month?></td>

                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Monthly Grose Salary</td>
                        <td><?=$monthly_grose_salary?></td>
                    </tr>
                   <tr>
                        <td>3</td>
                        <td>Working Days</td>
                        <td><?=$working_days?></td>

                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Absent Days</td>
                        <td><?=$absent_days?></td>

                    </tr>
                   <tr>
                        <td>5</td>
                        <td>Leave Adjust Days</td>
                        <td><?=$leave_adjust_days?></td>

                    </tr>
                   <tr>
                        <td>6</td>
                        <td>Leave Deposite Days</td>
                        <td><?=$leave_deposite_days?></td>

                    </tr>
                    
                     <tr>
                        <td>7</td>
                        <td>No Of PC Class</td>
                        <td><?=$no_of_pc_class?></td>

                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Pc Amount</td>
                        <td><?=$pc_amount?></td>

                    </tr>
                   <tr>
                        <td>9</td>
                        <td>Deduct Amount</td>
                        <td><?=$deduct_amount?></td>

                    </tr>
                   <tr>
                        <td>10</td>
                        <td>Total</td>
                        <td><b><?=$total?></b></td>

                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Mobile</td>
                        <td><?=$mobile?></td>

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

</body>

</html>