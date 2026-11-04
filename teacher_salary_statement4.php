<?php
include('include/dbcon.php');


if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));



$sql = "SELECT * FROM `rrsv_teacher_salary_statement` where id=$id";
$qr = mysqli_query($myDB, $sql);
 
    $cccc =  mysqli_fetch_array($qr, MYSQLI_ASSOC);
    // print_r($cccc);
    // die();
}
$teacher_id = $cccc['teacher_id'];

$sql2="select * from  rrsv_teacher where id='".$teacher_id."'";
    $result2=mysqli_query($myDB,$sql2) or die("Error into :".mysqli_connect_error());
   if(mysqli_num_rows($result2) > 0) {
    		    $rowsval=mysqli_fetch_array($result2,MYSQLI_ASSOC);
    		  //  print_r($rowsval);
    		  //  die();
    		  $full_name = $rowsval['full_name'];
$d_o_b = $rowsval['d_o_b'];
$qualification = $rowsval['qualification'];
$aadhar_no = $rowsval['aadhar_no'];
$pan_no = $rowsval['pan_no'];
$bank_account_no = $rowsval['bank_account_no'];
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
    <a type="button" href="teacher_salary.php" class="btn btn-info" style="color: #fff">Back</a>
	<button type="button" onclick = "printDiv('print')" class="btn btn-info">Print</button>
	<div id="print">
        <div class="container">

    <div class="heading">
        

        <h4 style="text-decoration:underline">RASULPUR RAMKRISHNA SARADA VIDYAPITH</h4>
    </div>
    <div class="container">
        <div>
            <table class="table">
                <tr>
                    <th class="col" colspan="2">Salary slip for :&nbsp;<?=strtoupper($cccc['salary_month'])?></th>
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
                    <!--Array ( [id] => 6 [teacher_id] => 4 [year] => 2022 [date] => 2022-08-31 [presents_days] => 10 [late_days] => 3 [extra_days] => 3 [pc_amount] => 0 [deduct_amount] => 489 [total] => 3759 [mobile] => 240 )-->
                    <tr>
                        <td>1</td>
                        <td>Salary Month</td>
                        <td><?=$cccc['salary_month'];?></td>
                        <td>----</td>

                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Monthly Grose Salary</td>
                        <td>----</td>
                        <td><?=$cccc['monthly_grose_salary'];?></td>
                    </tr>
                   <tr>
                        <td>3</td>
                        <td>Working Days</td>
                        <td><?=$cccc['working_days'];?></td>
                        <td>----</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Absent Days</td>
                        <td><?=$cccc['absent_days'];?></td>
                        <td>(-)<?=$cccc['absent_deduct_amount'];?></td>

                    </tr>
                   <tr>
                        <td>5</td>
                        <td>Leave Adjust Days</td>
                        <td><?=$cccc['leave_adjust_days'];?></td>
                        <td>----</td>
                    </tr>
                   <tr>
                        <td>6</td>
                        <td>Leave Deposite Days</td>
                        <td><?=$cccc['leave_deposite_days'];?></td>
                        <td>----</td>
                    </tr>
                    
                     <tr>
                        <td>7</td>
                        <td>No Of PC Class</td>
                        <td><?=$cccc['no_of_pc_class'];?></td>
                        <td>(+)<?=$cccc['pc_amount'];?></td>

                    </tr>
                   
                   <tr>
                        <td>8</td>
                        <td>Extra Days amount</td>
                        <td><?=$cccc['extra_days'];?></td>
                        <td>(+)<?=$cccc['extra_days_amount'];?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Mobile</td>
                        <td>----</td>
                        <td>(+)<?=$cccc['mobile'];?></td>

                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Total</td>
                        <td>----</td>
                        <td><b><?=$cccc['total'];?></b></td>
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
</script>