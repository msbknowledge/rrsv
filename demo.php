<style>
    #testSelect1_multiSelect {
         width: 200px;
    }
    
    .red {
        color: red;
    }
    
    .hight_width {
        font-size: 25px;
    }

</style>

<?php
include('include/header.php');
include('include/dbcon.php');
	
if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
}
 $sql="select * from rrsv_student_registration  where id=$id  ";
$res=mysqli_query($myDB,$sql);
$obj=mysqli_fetch_array($res,MYSQLI_ASSOC);
$scl_class=$obj['scl_class'];
$scl_session=$obj['scl_session'];
$monthlyfee=$obj['monthly_fee'];
$monthlyadmissionfee=$obj['monthly_admission_fee'];
$ad=$obj['ad_form_charge'];
$book=$obj['book_charge'];
$uniform=$obj['uniform'];
$diary=$obj['diary'];
$icard=$obj['icard'];
$bag=$obj['bag'];
$sweater=$obj['sweater'];
$shoes=$obj['shoes'];
echo $totalbookcost=$obj['bookrate'];
 $total_cost=$monthlyfee+$monthlyadmissionfee+$ad+$book+$uniform+$diary+$icard+$bag+$sweater+$shoes;
$id=$obj['id'];
if(isset($_POST['scl_session']))
{
$scl_session=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
}
if(isset($_POST['scl_class']))
{
$scl_class=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
}


function fill_cat($myDB){
if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
}
		$output='';
		  $sql="select a.*,b.id  from rrsv_book as a, rrsv_student_registration as b where b.id=$id and a.scl_class=b.scl_class ";
		$res=mysqli_query($myDB,$sql);
		while($obj1=mysqli_fetch_array($res,MYSQLI_ASSOC)) {
			$output .='<option value="'.$obj1["book_name"].'">'.$obj1["book_name"].'</option>';
	    }	
	   return $output;
	} 
	function fill_copy($myDB){
if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
}
		$output='';
		  $sql="select a.*,b.id  from rrsv_copy as a, rrsv_student_registration as b where b.id=$id and a.scl_class=b.scl_class ";
		$res=mysqli_query($myDB,$sql);
		while($obj1=mysqli_fetch_array($res,MYSQLI_ASSOC)) {
			$output .='<option value="'.$obj1["copy_name"].'">'.$obj1["copy_name"].'</option>';
	    }	
	   return $output;
	} 

?>
<!--<link href="styles/multiselect.css" rel="stylesheet"/>--->

<!--<script src="multiselect.min.js"></script>-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
<!-- partial -->
<form name="hh" action="add_student_fee_post.php" method="post">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Student Details</h4>
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Student Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputUsername2" value="<?=$obj['scl_name']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Class Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputEmail2" value="<?=$obj['scl_class']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Section Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputMobile" value="<?=$obj['scl_section']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Roll No:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputPassword2" value="<?=$obj['scl_roll_no'];?>" readonly>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Admission Status:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputPassword2" value="<?=$obj['add_status'];?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Payment Status</h4>
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Admission Due:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="admission_due" id="admission_due" value="<?php echo $admission_due?>" readonly>
                                    <input type="hidden" name="admission_due_cal" id="admission_due_cal" value="<?php echo $admission_due?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Monthly Installment Due :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputEmail2" value='<?php echo $monthly_due;?>' readonly>
                                </div>
                            </div>
                            <!--<div class="form-group row">-->
                            <!--  <label for="exampleInputMobile" class="col-sm-3 col-form-label">Car Due:</label>-->
                            <!--  <div class="col-sm-9">-->
                            <!--    <input type="text" class="form-control" id="exampleInputMobile" value="<?php echo $car_due;?>" >-->
                            <!--    <input type="hidden" name="car_due1" id="yearly_car_due" value="<?php echo $car_due;?>" />-->
                            <!--  </div>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
                 <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Total Cost Information</h4>
                            <?php
                                echo $cost;
                            ?>
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Admission and Other Total Cost:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="admission_cost" id="admission_cost" value="Loading...." readonly>
                                    <input type="hidden" name="admission_due_cal" id="admission_due_cal" value="<?php echo $admission_due?>" readonly>
                                </div>
                            </div>
                             <?php

                                     $sql="select book_name,book_pub,rate  from rrsv_book  where scl_class='$scl_class'";
                                     $res=mysqli_query($myDB,$sql);
                                     while($rows=mysqli_fetch_array($res)){
                                       $c++;
                                    $cost=$cost+$rows['rate'];
                                     }
                                        ?>
                           
                                                        <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">monthly whole year Cost :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="monthly_whole_year_Cost" value='Loading....' readonly>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Total Book Cost :</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="total_book_cost" value='<?php echo $cost;?>' readonly>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Whole Year Total Amount :</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="whole_year_total_amount" value='Loading....' readonly>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Monthly Due Information</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <address>
                                        <p class="font-weight-bold">January :</p>
                                        <p>February :</p>
                                        <p>March :</p>
                                        <p>April :</p>
                                        <p>May :</p>
                                        <p>June :</p>
                                        <p>July :</p>
                                        <p>August :</p>
                                        <p>September :</p>
                                        <p>October :</p>
                                        <p>November :</p>
                                        <p>December :</p>

                                    </address>
                                </div>
                                <div class="col-md-6">
                                    <address class="text-primary">
                                        <?php
                          $monthapp='';
  $sql="select a.*,b.scl_name,b.scl_roll_no from rrsv_scl_studen_fee as a,rrsv_student_registration as b where a.pay_id=b.id and b.id=$id";
                            $res=mysqli_query($myDB,$sql);
                           	while($rows=mysqli_fetch_array($res,MYSQLI_ASSOC)) {
                               $manthvar=$rows['scl_month'];
                               $monthapp=$monthapp.','.$manthvar;
                            }
                           $monthtotal=str_replace(',,',',',$monthapp);
                           $montharr=explode(',',$monthtotal);
						?>
                                        <p><?php if(in_array('January', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('February', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('March', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('April', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('May', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('June', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('July', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('August', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('September', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('October', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('November', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                        <p><?php if(in_array('December', $montharr)){ echo 'Paid';}else{ echo 'Unpaid';}?></p>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Book Status</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <address>
                                        <?php
                                         $sql="select * from rrsv_book  where scl_class='$scl_class'";
           $res=mysqli_query($myDB,$sql);
           while($rows=mysqli_fetch_array($res)){
                                        ?>
                                        <p class="font-weight-bold"><?=$rows['book_name'];?> :</p>
                                        <?php
           }
           ?>
                                    </address>
                                </div>
                                <div class="col-md-6">
                                    <address class="text-primary">
                                        <?php
                          $monthapp='';
                         $sql="select a.*,b.book_name from rrsv_scl_studen_fee as a,rrsv_book_cost as b where a.id=b.cost_id and a.pay_id=$id ";
                            $res=mysqli_query($myDB,$sql);
                           	while($rows=mysqli_fetch_array($res,MYSQLI_ASSOC)) {

                            
                          if($rows['book_name']!=''){
                              ?>
                             
                               
                                  <p>COMPLETED</p>
                                  <?php
                              }

 
                           	}
                           	?>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12 grid-margin stretch-card" style="">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Book Cost</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.No</th>
                                        <th>Class Name</th>
                                        <th>Book Name</th>
                                        <th>Rate</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                      $c=0;
                                     $sql="select book_name,book_pub,rate  from rrsv_book  where scl_class='$scl_class'";
                                     $res=mysqli_query($myDB,$sql);
                                     while($rows=mysqli_fetch_array($res)){
                                       $c++;
                                    $cost=$cost+$rows['rate'];
                                      ?>
                                    <tr>
                                        <td><?php echo $c;?></td>
                                        <td><?=$rows['book_name'];?></td>
                                        <td><?=$rows['book_pub'];?></td>
                                        <td><?=$rows['rate'];?></td>
                                    </tr>
                                    <?php
                                     }

                                     ?>
                                     <tr>
                                           <td>Total Book Cost: <p id="b_cost">Loading....</p></td>
                                     </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 grid-margin stretch-card" style="">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Copy Cost</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.No</th>
                                        <th>Copy Name</th>
                                        <th>Rate</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                      $c=0;
                                     $sql="select book_name,book_pub,rate from rrsv_copy  where scl_class='$scl_class'";
                                     $res=mysqli_query($myDB,$sql);
                                     while($rows=mysqli_fetch_array($res)){
                                       $c++;
                                         $cost1=$cost1+$rows['rate'];
                                      ?>
                                    <tr>
                                        <td><?php echo $c;?></td>
                                        <td><?=$rows['copy_name'];?></td>
                                        <td><?=$rows['rate'];?></td>
                                    </tr>
                                    <?php
                                     }
                                     ?>
                                      <tr>
                                           <td>Total Book Cost:<?php echo $cost1;?></td>
                                     </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-12 grid-margin stretch-card" style="">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Car Cost</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>SL.No</th>
                                        <th>Session</th>
                                        <th>Kilo Meter</th>
                                        <th>Rate</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                      $c=0;
                                     $sql="select *  from rrsv_car  where 1";
                                     $res=mysqli_query($myDB,$sql);
                                     while($rows=mysqli_fetch_array($res)){
                                       $c++;
                                    $cost=$cost+$rows['rate'];
                                      ?>
                                    <tr>
                                        <td><?php echo $c;?></td>
                                        <td><?=$rows['scl_session'];?></td>
                                        <td><?=$rows['kilo'];?></td>
                                        <td><?=$rows['fair'];?></td>
                                    </tr>
                                    <?php
                                     }

                                     ?>
                                     
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Other Cost Information</h4>
                            <form class="form-sample">
                                <?php
                  $sql="select a.*,b.add_status from rrsv_fee as a,rrsv_student_registration as b  where a.scl_class='$scl_class' and  a.scl_class=b.scl_class and b.id='$id' ";
                    $res=mysqli_query($myDB,$sql);
                    $rows=mysqli_fetch_array($res);
                    $monthly_fee=$rows['monthly_fee'];
                    $monthly_admission_fee=$rows['monthly_admission_fee'];
                    $ad_form_charge=$rows['ad_form_charge'];
                       $uniform= $rows['uniform'];
                       $icard= $rows['icard'];
                       $sweater= $rows['sweater'];
                       $bag= $rows['bag'];
                       $shoes= $rows['shoes'];
                      $addstatus=$rows['add_status'];
                        $total_other_cost=$monthly_fee+$monthly_admission_fee+$ad_form_charge+$uniform+$icard+$sweater+$bag+$shoes;
                    ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Monthly Fee:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="monthly_fee_cost"  name="monthly_fee_cost" value="<?=$rows['monthly_fee']?>" readonly> 
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if($addstatus=='New Admission'){
                                    ?>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">New Admission Fee:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="admission_fee_cost"  name="admission_fee_cost" value="<?=(int)$rows['monthly_admission_fee']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    else 
                                    {
                                        
                                    ?>
                                     <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Re Admission Fee:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="admission_fee_cost"  name="admission_fee_cost" value="<?=(int)$rows['monthly_admission_fee']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Admission Form:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="admissions_cost" name="admissions_cost" value="<?=(int)$rows['ad_form_charge']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Student Uniform:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="uniform_cost" name="uniform_cost" value="<?=$rows['uniform']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Icard:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="icard_cost" name="icard_cost" value="<?=$rows['icard']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Sweater,Slax,Cap:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="sweater_slax_cap_cost" name="sweater_slax_cap_cost" value="<?=$rows['sweater']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Bag:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="bag_cost" name="bag_cost" value="<?=$rows['bag']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Shoes & Sockes:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="shoes_sockes_cost" name="shoes_sockes_cost" value="<?=$rows['shoes']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
                
            <!---->
            
          
                
                <!---->
                
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Bill Genarate for Students Cost</h4>
                        <form class="form-sample" action="add_bill_post.php" method="post">
                            <input type="hidden" name="pay_id" value="<?=$obj['id'];?>">
                            <input type="hidden" name="id" value="<?=$obj['id'];?>">
                            <input type="hidden" name="recep_no" value="">
                            
                                
                            <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label">Date:</label>
                                            <div class="col-sm-6">
                                                <input type="date" class="form-control" id="admission_cost" name="admission_cost" value="1">
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if($addstatus=='New Admission'){
                                    ?>
                                    <div class="col-md-4 dis_admission new_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>New Admission Fee:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="admission_fee_val" name="admission_fee_val" onchange="myFunction()">
                                            </div>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <div class="col-md-4 dis_admission re_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Re Admission Fee:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="re_admission_fee_val" name="re_admission_fee_val" value="<?=$rows['monthly_admission_fee']?>" onchange="myFunction()">
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Car Cost:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="car_cost_val" name="car_cost_val" onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Monthly Fee:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="monthly_fee_val" name="monthly_fee_val" onchange="myFunction()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Admission Form:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="admission_val" name="admission_val"  onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Student Uniform:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="uniform_val" name="uniform_val" onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                            
                                            <label class="col-sm-6 col-form-label"><span></span> Icard:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="icard_val" name="icard_val"  onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                           
                                            <label class="col-sm-6 col-form-label"><span></span>Sweater,Slax,Cap:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="sweater_slax_cap_val" name="sweater_slax_cap_val"  onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Bag:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="bag_val" name="bag_val" onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 dis_admission">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label"><span></span>Shoes & Sockes:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="shoes_sockes_val" name="shoes_sockes_val" onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month">
                                        <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">&nbsp; Month of Payment:</label>
                                    <div class="bb1" style="margin-left: 178px;margin-top: -35px;">
                                       
                                        <select name="scl_month[]" class="form-control minput" id="testSelect1"   multiple  >
                                            <option value="January">January</option>
                                            <option value="February">February</option>
                                            <option value="March">March</option>
                                            <option value="April">April</option>
                                            <option value="May">May</option>
                                            <option value="June">June</option>
                                            <option value="July">July</option>
                                            <option value="August">August</option>
                                            <option value="September">September</option>
                                            <option value="October">October</option>
                                            <option value="November">November</option>
                                            <option value="December">December</option>
                                        </select>
                                    </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            
                                        </div>
                                    </div>
                                </div>
                            
                            

                            <p class="card-description">
                                Book Cost
                            </p>
                            <button type="button" name="add" id="add" class="btn btn-primary">Add</button>
                            <div class="row">
                                <div class="table-responsive" >
                                    <table class="table table-striped" id="crud_table">
                                        <thead>
                                        <tr>
                                        

                                            <th>Book Name</th>
                                            <th>Rate</th>
                                        </tr>
                                        </thead>
                                       <tr>
                                       <td>
									<select name="account[cat_id][]" class="form-control cat" id="cat_id1" accesskey="1" >
									<option value="">-Select a Book  -</option>
									  <?php echo fill_cat($myDB);?>
								</select>
                                </td>
 	          						 <td>
								<input type="number" accesskey="1" class="form-control qtycal" id="qty1" name="account[qty][]" placeholder="rate" onchange="myFunction()"  />
							 </td>
                                </tr>
                                    </table>
                                </div>
                            </div>
                                <p class="card-description">
                                Copy Cost
                            </p>
                            <button type="button" name="add" id="add1" class="btn btn-primary">Add</button>
                            <div class="row">
                                <div class="table-responsive" >
                                    <table class="table table-striped" id="crud_table1">
                                        <thead>
                                        <tr>
                                        

                                            <th>Copy Name</th>
                                          <th>Quenty</th>
                                            <th>Rate</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                       <tr>
                                       <td>
									<select name="account[cat_id][]" class="form-control cat" id="cat_id1" accesskey="1" >
									<option value="">-Select a Copy  -</option>
									  <?php echo fill_copy($myDB);?>
								</select>
                                </td>
 
							 <td>
								<input type="number" accesskey="1" class="form-control qtycopy" id="copyqty1" name="account[qty][]" placeholder="Quenty" onchange="myFunction()"  />
							 </td>
							 <td>
								<input type="number" accesskey="1" class="form-control ratecopy" id="rate1" name="account[qty][]" placeholder="Rate" onchange="myFunction()"  />
							 </td>
							 <td>
								<input type="number" accesskey="1" class="form-control tcopy" id="totalcopy1" name="account[qty][]" placeholder="Total Copy " onchange="myFunction()"  />
							 </td>
                                </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 grid-margin stretch-card" style=";">
                                <div class="card" style="margin-left: -15px;">
                                    <div class="card-body">
                                        <h4 class="card-title">Bill Genarate</h4>
                                        <p><span class ="text"><b>Item Cost:&nbsp;</b></span>
                                            <input type="number" name="item_cost" id="itemcost" class="input14" value="0" size="63" maxlength="100"  style="margin-left: 46px;" onchange="myFunction()" ></p>
                                              <p><span class ="text"><b>Book cost:&nbsp;</b></span>
                                            <input type="number" name="book_cost" id="cost" class="input14 grdtot" value="0" size="63" maxlength="100"  style="margin-left: 46px;"></p>
                                             <p><span class ="text"><b>Copy cost:&nbsp;</b></span>
                                            <input type="number" name="book_cost" id="copy" class="input14 copyt" value="0" size="63" maxlength="100"  style="margin-left: 46px;"></p>
                                            <p><span class ="text"><b>Total Cost:&nbsp;</b></span>
                                            <input type="number" name="scl_net" id="scl_net" class="input14" value="0" size="63" maxlength="100"  style="margin-left: 46px;" onchange="myFunction()" ></p>
                                        <?php
                  $sql="select sum(rate) as book_rate from rrsv_book  where scl_class='$scl_class'";
           $res=mysqli_query($myDB,$sql);
           while($rows=mysqli_fetch_array($res)){
             $book_cost=$rows['book_rate'];
           }
           $actual_cost=$total_other_cost+$book_cost;
         $due_cost=$total_other_cost-$book_cost;
                 ?>
                 
                                        <p><span class ="text"><b>Actual Net Balance:&nbsp;</b></span>

                                            <input type="number" name="net" id="net" class="input14" value="Loading...." size="63" maxlength="100" ></p>
                                        <p><span class ="text"><b>Remaining/Discount Amount:&nbsp;</b></span>

                                            <input type="number" name="ramining_balance" id="ramining_balance" class="input14" value="Loading...." size="63" maxlength="100"    style="margin-left: 15px;"></p>
                                     
                                     <p class="red"><b>*Note:&nbsp;After puting reciveing amount you can t cheng privious value/amount or check uncheck.</b></span></p>
                                            <p><b>Payment Received Amount:&nbsp;</b></span>
                                                <input type="number" name="payment_recive" id="payment_recive" class="input14" value="" size="63" maxlength="100" style="margin-left: 15px;" onchange="myFunction()" >
                                            </p>
                                            
                                            <p><b>Advance/Due Amount:&nbsp;</b></span>
                                                <input type="number" name="advance_due" id="advance_due" class="input14" size="63" maxlength="100" style="margin-left: 15px;" >
                                            </p>
                                             <p><b>Using:&nbsp;</b></span>
                                                <input type="text" name="using" id="using" class="input14" size="63" maxlength="100" style="margin-left: 15px;" >
                                            </p>
                                     
                                        <div class="btn1">
                                            <input type="submit" name="submit" value="Save" class="btn icon-btn btn-primary ccc">
                                        </div>

                        </form>
                    </div>
                </div>
</form>
</div>
</div>
<div class="fixed-table-container">
    <table cellspacing="0">
    </table>
    <div class="header-background">
        <div class="fixed-table-container-inner">
            <table>
                <tr height="30" style="background:#0a5079;border-color: #d29c29;">

                    <td width="10%" class="text" style="color:#fff;padding-left:5px;"><strong>SL.No</strong></td>

                    <td width="15%" class="texttd" style="padding-left:10px;"><strong>Student Info</strong></td>
                    <td width="15%" class="texttd"><strong>Payment Info</strong></td>
                  
                    <td width="10%" class="texttd"><strong>Action</strong></td>

                <tr>

                    <td colspan="11" height="1" bgcolor="#809C69"></td> </tr>

                <?php

				                         $bgcolor="";

                                 $c=0;

                                 $scroll_page = 30; 

                              $sql="select a.*,b.id,b.scl_name,b.scl_roll_no from rrsv_scl_studen_fee as a,rrsv_student_registration as b where a.pay_id=$id and a.pay_id=b.id";

                              //  $sql="select a.*,b.scl_name,b.scl_roll_no from scl_studen_fee as a,student_registration as b where a.pay_id=$id";

                                $sql .=" order by a.id desc ";

                                 $result1=mysqli_query($myDB,$sql);

                                 $l=mysqli_num_rows($result1);

                                 if($l>0)

                {

                while ($rows=mysqli_fetch_array($result1,MYSQLI_ASSOC))

                {

                if($bgcolor=='#dff8e3')

                {

                $bgcolor='#EEEEEE';

                }

                else

                {

                $bgcolor='#dff8e3';

                }

                $c++;

                ?>
                <tr  bgcolor="<?php echo $bgcolor;?>" height="20">

                    <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c;?></td>

                    <td  class="text" valign="center" style="padding-left:10px;"><b>Student Name:</b><?=$rows['scl_name'];?>
                        </br><b>Roll No:</b><?=$rows['scl_roll_no'];?>
                    </td>
                    <td  class="text" valign="center" style="padding-left:10px;"><b>Received No:</b><?=$rows['recep_no'];?>
                        </br><b>Date Of Payment</b>:<?=$rows['scl_data'];?>
                        </br><b>Admission:</b><?=$rows['admission_fee_val'];?>
                        </br><b>Monthly payment:</b><?=$rows['monthly_fee_val'];?>
                        </br><b>Car Fee:</b><?=$rows['car_fee'];?>
                    </td>
                   
                    <td>	<a href="payment.php?recep_no=<?=$rows['recep_no'];?>" title="Click to Print Bill" class="btn btn-success p" target="_blank" style="background-color: #1f4626;border-color: #1f4626;"><i class="fa fa-print"></i></a>

                    </td>

                </tr>

                <tr>

                    <td colspan="11" height="1" bgcolor="#809C69"></td>

                </tr>

                <?php

						               }

                          }

						           else {

                            echo "<tr>";

                echo "<td class='errtext' colspan=11>Records Not Found</td>";

                echo "</tr>";

                }

                ?>

            </table>
        </div>
    </div>
</div>
</div>
</div>
<?php
include('include/footer.php');
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
<script src="multiselect-dropdown.js" ></script>
 <script>
$(document).ready(function(){
	var i=$('table tr').length;
    $("#add").on('click',function(){
        html = '<tr id="row'+i+'">';
        html += '<th width=20%><select name="account[cat_id][]" id="cat_id'+i+'" accesskey="'+i+'" class="form-control cat"><option value="">Select Category</option><?php
         echo fill_cat($myDB); ?></select></th>';
		html += '<th><input type="number" class="form-control qtycal" name="account[qty][]"  id="qty'+i+'"  accesskey="'+i+'" onchange="myFunction()"></td>';

        $('#crud_table').append(html);
        i++;
    });
    var i=$('table tr').length;
    $("#add1").on('click',function(){
        html = '<tr id="row'+i+'">';
        html += '<th width=20%><select name="account[cat_id][]" id="cat_id'+i+'" accesskey="'+i+'" class="form-control cat"><option value="">Select Category</option><?php
        echo fill_copy($myDB); ?></select></th>';
   		html += '<th><input type="number" class="form-control qtycopy" name="account[qty][]"  id="copyqty'+i+'"  accesskey="'+i+'" onchange="myFunction()"></td>';
		html += '<th><input type="number" class="form-control ratecopy" name="account[qty][]"  id="rate'+i+'"  accesskey="'+i+'" onchange="myFunction()"></td>';
		html += '<th><input type="number" class="form-control tcopy" name="account[qty][]"  id="totalcopy'+i+'"  accesskey="'+i+'" onchange="myFunction()"></td>';

        $('#crud_table1').append(html);
        i++;
    })
    $('#crud_table1').on('keyup', '.ratecopy', function() {
		var subtotal=0;
	    var c= $(this).attr("accesskey");
	    
    	var qty=$('#copyqty'+c).val();
    	
		var price=$('#rate'+c).val();
		
		var totalprice=	(price*qty);
		
		$('#totalcopy'+c).val(totalprice);
	 var copyTotal = 0;
 $(".tcopy").each(function () {
	 var stval = parseFloat($(this).val());
	 copyTotal += isNaN(stval) ? 0 : stval;
	
 });
 
 $('.copyt').val(copyTotal.toFixed(2));
});	
   $('#crud_table').on('change', '.cat', function() {
	    var c= $(this).attr("accesskey");
    	var cat_id=$('#cat_id'+c).val();
    	var scl_class='<?php echo $scl_class;?>';
    		$.ajax({
    		url:'class.php',
    		type:'POST',
                data:{
                       
                        cat_id:cat_id,
                        scl_class:scl_class
                       
                        
                    },
    		success:function(result){
                $('#para_id'+c).html(result);
    		}
    	});
	});	

$('#crud_table').on('keyup', '.qtycal', function() {
		var subtotal=0;
	    var c= $(this).attr("accesskey");
    	var qty=$('#qty'+c).val();
		var price=$('#unitprice'+c).val();
		var totalprice=	(price*qty);
		$('#total'+c).val(totalprice);
	 var grandTotal = 0;
 $(".qtycal").each(function () {
	 var stval = parseFloat($(this).val());
	 grandTotal += isNaN(stval) ? 0 : stval;
	
 });
 
 $('.grdtot').val(grandTotal.toFixed(2));
 
});	

});     
   function myFunction() {
      var cost=$('#cost').val();
      
            var cosy=$('#cosy').val();
      var payment_recive=$('#payment_recive').val();
       var scl_net=$('#scl_net').val();
      
      var re_admission_fee_val=$('#re_admission_fee_val').val();
        var monthly_fee_val=$('#monthly_fee_val').val();
        
        var admission_val=$('#admission_val').val();
                
        var uniform_val=$('#uniform_val').val();
        var icard_val=$('#icard_val').val();
        var sweater_slax_cap_val=$('#sweater_slax_cap_val').val();
        var bag_val=$('#bag_val').val();
        var shoes_sockes_val=$('#shoes_sockes_val').val();
        var admission_fee_val=$('#admission_fee_val').val();
         var car_cost_val=$('#car_cost_val').val();
        // var paid_val = Number(val) + Number(admission_val) + Number(uniform_val) + Number(icard_val) + Number(sweater_slax_cap_val) + Number(bag_val) + Number(shoes_sockes_val) + Number(shoes_sockes_val);
        // $("#scl_net").val(paid_val);
        // $("#item_cost").val(Number($("#monthly_fee_val").val()));
      $("#itemcost").val(Number($("#monthly_fee_val").val()) + Number($("#admission_val").val())+ Number($("#uniform_val").val())+ Number($("#icard_val").val())+ Number($("#sweater_slax_cap_val").val())+ Number($("#bag_val").val())+ Number($("#shoes_sockes_val").val())+ Number($("#admission_fee_val").val())+ Number($("#car_cost_val").val()));
        var total_cost = Number($("#itemcost").val()) + Number($("#cost").val())+Number($("#copy").val());
        
        var remain = Number($("#net").val()) - Number($("#scl_net").val());
                var advence = Number($("#scl_net").val()) - Number($("#payment_recive").val());
            
         $("#scl_net").val(total_cost);
        $("#ramining_balance").val(remain);
                $("#advance_due").val(advence);
                
              
                
                if( Math.sign(advence) != -1) {
                    alert(advence); // due
                }

    }
    
    
    
 </script>   
 <script>
     $(document).ready(function(){ //   b_cost    
         
        //  var cost=$("#scl_net").val();
  
        $("#admission_cost").val(Number($("#admission_fee_cost").val()) + Number($("#admissions_cost").val()) + Number($("#uniform_cost").val())+ Number($("#icard_cost").val())+ Number($("#sweater_slax_cap_cost").val())+ Number($("#bag_cost").val())+ Number($("#shoes_sockes_cost").val()));
        //alert($("#admission_cost").val());
        //return false;

          //var a_cost = Number($("#net").val());
         // $("#admission_cost").val(a_cost);
          
          var m_cost = Number($("#monthly_fee_cost").val()) * 12;
          $("#monthly_whole_year_Cost").val(m_cost);
          
          var w_cost = Number($("#admission_cost").val()) + m_cost + Number($("#total_book_cost").val());
          $("#whole_year_total_amount").val(w_cost);
          
          var book_cost = Number($("#total_book_cost").val());  
          $("#b_cost").text(book_cost);
          
           $("#net").val(Number(w_cost));
          // $("#ramining_balance").val(Number(w_cost));
     
     });
     
     
 </script>

