

<style>
    #testSelect1_multiSelect {
         width: 200px;
    }
    
    .red {
        color: red;
    }

</style>

<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
}
$sql="select * from rrsv_student_registration where id=$id";
$res=mysqli_query($myDB,$sql);
$obj=mysqli_fetch_array($res,MYSQLI_ASSOC);
$scl_class=$obj['scl_class'];
if(isset($_POST['scl_session']))
{
$scl_session=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
}
if(isset($_POST['scl_class']))
{
$scl_class=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
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
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Due Information</h4>
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
                            <!--    <input type="text" class="form-control" id="exampleInputMobile" value="<?php echo $car_due;?>" readonly>-->
                            <!--    <input type="hidden" name="car_due1" id="yearly_car_due" value="<?php echo $car_due;?>" readonly/>-->
                            <!--  </div>-->
                            <!--</div>-->
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
                                        <th>Book Name</th>
                                        <th>Publisher Name</th>
                                        <th>Rate</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
              $c=0;
             $sql="select book_name,book_pub,rate from rrsv_book  where scl_class='$scl_class'";
           $res=mysqli_query($myDB,$sql);
           while($rows=mysqli_fetch_array($res)){
               $c++;
              
           
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
                    $sql="select * from rrsv_fee  where scl_class='$scl_class'";
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
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Admission Fee:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="admission_fee_cost"  name="admission_fee_cost" value="<?=$rows['monthly_admission_fee']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Admission Form:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="admission_cost" name="admission_cost" value="<?=$rows['ad_form_charge']?>" readonly>
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

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Bill Genarate</h4>
                        <form class="form-sample" action="add_bill_post.php" method="post">
                            <input type="hidden" name="pay_id" value="<?=$obj['id'];?>">
                            <input type="hidden" name="id" value="<?=$obj['id'];?>">
                            <input type="hidden" name="recep_no" value="">
                            <p class="card-description">
                                Students Cost <p><span class ="text"><b>Payment Mode:&nbsp;</b></span>

                            <span class="txtAdd"> Admission</span> <input type="checkbox" name="admission" ><span class="txtMon">Monthly Fee</span>  <input type="checkbox" name="admission" >
                        </p><br>
                            <div class="row">
                                <br>
                                
                                        <label class="col-sm-6 col-form-label">&nbsp; Date:</label>
                                        <div class="col-sm-6">
                                          <!--<input type="date" name="scl_data"  style="width: 334;margin-left: -328px;">-->
                                           <input type="date" name="scl_data" class="form-control"  style="width: 334;" placeholder="dd-mm-yyyy" value=""min="1997-01-01" max="2030-12-31"><br>
                                          
                                        </div>
                                    
                                <div class="col-md-4 dis_month" style="margin-top: 46px;margin-left: -1015px;">
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
                                  <br>
                                <div class="col-md-4 dis_admission" >
                                   
                                    <div class="form-group row">
                                        
                                        <label class="col-sm-6 col-form-label"><input type="checkbox" class="cost_data" data-id="admission_fee" onchange="myFunction()" style="margin-left: -361px;margin-top: 109px;">&nbsp; Admission Fee:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control key_up_down" id="admission_fee_val" name="admission_fee_val" onchange="myFunction()" style="margin-left: -347px;margin-top: 113px;width: 336px;"readonly >
                                        </div>
                                    </div>
                                </div>
                                  <div class="col-md-4 dis_admission">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label"><input type="checkbox" class="cost_data" data-id="monthly_fee" onchange="myFunction()" style="margin-left: 10px;margin-top: 45px;" >&nbsp; Monthly Fee:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="monthly_fee_val" name="monthly_fee_val" onchange="myFunction()"  style="width: 336px;margin-left: 21px; ;margin-top: 39px;" readonly >
                                        </div>
                                    </div>
                                </div>
                                <br>
                               
                                
                            </div>
                            <div class="row">
                                  <div class="col-md-4 dis_admission">
                                  
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label"><input type="checkbox" class="cost_data" data-id="admission" onchange="myFunction()" style="margin-top: -19px;" >&nbsp; Admission Form:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="admission_val" name="admission_val" onchange="myFunction()" style="margin-top: 4px;margin-left: 20px;width: 199px;" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 dis_admission">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label" style="margin-left: 53px;"><input type="checkbox" class="cost_data" data-id="uniform" onchange="myFunction()" >&nbsp; Student Uniform:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="uniform_val" name="uniform_val" onchange="myFunction()" style="margin-left: 206px;margin-top: -47px;width: 199px;" readonly>
                                        </div>
                                    </div>
                                </div>  
                                </div>
                            <div class="row">
                                <div class="col-md-4 dis_admission">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label"><input type="checkbox" class="cost_data" data-id="icard" onchange="myFunction()">&nbsp; Icard:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="icard_val" name="icard_val" onchange="myFunction()" style="width: 200px;margin-left: 20px;" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 dis_admission">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label" style="margin-left: 53px;"><input type="checkbox" class="cost_data" data-id="sweater_slax_cap" onchange="myFunction()" >&nbsp; Sweater,Slax,Cap:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="sweater_slax_cap_val" name="sweater_slax_cap_val" onchange="myFunction()" style="margin-left: 207px;width: 200px;margin-top: -43px;" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-4 dis_admission">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label"><input type="checkbox" class="cost_data" data-id="bag" onchange="myFunction()">&nbsp; Bag:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="bag_val" name="bag_val" onchange="myFunction()" style="width: 200px;margin-left: 20px;" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 dis_admission">
                                    <div class="form-group row">
                                        <label class="col-sm-6 col-form-label"style="margin-left: 53px;"><input type="checkbox" class="cost_data" data-id="shoes_sockes" onchange="myFunction()">&nbsp; Shoes & Sockes:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="shoes_sockes_val" name="shoes_sockes_val" onchange="myFunction()" style="margin-left: 207px;width: 200px;margin-top: -43px;" readonly>
                                        </div>
                                    </div>
                                </div>
                              
                                
                            </div>

                            <p class="card-description">
                                Book Cost
                            </p>

                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Check Box</th>
                                            <th>Book Name</th>
                                            <th>Publisher Name</th>
                                            <th>Rate</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
              $c=0;
             $sql="select * from rrsv_book  where scl_class='$scl_class'";
           $res=mysqli_query($myDB,$sql);
           while($rows=mysqli_fetch_array($res)){
               $c++;
              ?>
                                        <tr>
                                            <td><input type="checkbox" id="<?=$rows['book_name'];?>" data-id="<?=$rows['rate'];?>" name="account[book_id][]" class="book_name" ></td>
                                            <td> <input type="text"  name="account[book_name][]" value="<?=$rows['book_name'];?>"  ></td>
                                            <td><?=$rows['book_pub'];?></td>
                                            <td>
                                                <input type="hidden" id="<?=$rows['rate'];?>_Text" value="<?=$rows['rate'];?>" name="account[rate][]" onchange="myFunction()">
                                                <input type="text" id="<?=$rows['rate'];?>_Rate" name="<?=$rows['rate'];?>_Rate" onchange="myFunction()" readonly>
                                            </td>
                                        </tr>
                                        <?php
           }
           ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 grid-margin stretch-card" style=";">
                                <div class="card" style="margin-left: -15px;">
                                    <div class="card-body">
                                        <h4 class="card-title">Bill Genarate</h4>
                                        <p><span class ="text"><b>Net Balance:&nbsp;</b></span>
                                            <input type="number" name="scl_net" id="scl_net" class="input14" value="0" size="63" maxlength="100" readonly style="margin-left: 46px;"></p>
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

                                            <input type="number" name="net" id="net" class="input14" value="<?php echo $actual_cost;?>" size="63" maxlength="100" readonly></p>
                                        <p><span class ="text"><b>Remaining/Discount:&nbsp;</b></span>

                                            <input type="number" name="ramining_balance" id="ramining_balance" class="input14" value="<?php echo $actual_cost;?>" size="63" maxlength="100"   readonly style="margin-left: 15px;"></p>
                                     
                                     <p class="red"><b>*Note:&nbsp;After puting reciveing amount you can t cheng privious value/amount or check uncheck.</b></span></p>
                                            <p><b>Payment Recive:&nbsp;</b></span>
                                                <input type="number" name="payment_recive" id="payment_recive" class="input14" value="" size="63" maxlength="100" style="margin-left: 15px;">
                                            </p>
                                            
                                            <p><b>Advance/Due:&nbsp;</b></span>
                                                <input type="number" name="advance_due" id="advance_due" class="input14" size="63" maxlength="100" style="margin-left: 15px;" readonly>
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
                        </br><b>Date Of Payment</b>:<?=date('d-m-Y',strtotime($rows['scl_data']));?>
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
    

</script>

<script>

    $(document).ready(function(){

        // $('#admission').click(function() {

        //     $('#monthly_fee_val').attr('disabled',! this.checked)

        // });

        $('#monthly').click(function() {

            $('#monthly_fee_val').attr('disabled',! this.checked)

            // $('#testSelect1').attr('disabled',! this.checked)

        });

       
    });

</script>

<script>


    $(document).ready(function() {
        
        // ("#payment_recive").keyup(function(){
        
        // var id = $(this).val();
        // alert(id);
        
        // });
        
        $("#payment_recive").on("input paste", function() {
            var payment_val = $(this).val();
            var net_val = $("#scl_net").val();
            var Advance_Due = Number(payment_val) - Number(net_val);
            $("#advance_due").val(Advance_Due);
            
        });


        $(".book_name").click(function(){
            var payment_val = $("#payment_recive").val();
            if(payment_val != 0) {
                alert("you cant chenge.You allready recived payment amount.If you need to do it at frist make payment recive input box put value 0.");
                return false;
            }
            var id = $(this).data("id");
            var text = $('#'+id+'_Text').val();
            if ($(this).is(':checked')) {
                $('#'+id+'_Rate').val(text);
                $('#'+id+'_Rate').removeAttr("readonly");
                var net_val = Number($("#scl_net").val()) + Number(text);
                $("#scl_net").val(net_val);
                var remain = Number($("#net").val()) - Number(net_val);
                $("#ramining_balance").val(remain);
            } else {
                $('#'+id+'_Rate').val(null);
                $('#'+id+'_Rate').attr("readonly", "readonly");
                var net_val = Number($("#scl_net").val()) - Number(text);
                $("#scl_net").val(net_val);
                var remain = Number($("#net").val()) - Number(net_val);
                $("#ramining_balance").val(remain);
            }
        });

        $(".cost_data").click(function(){
            var payment_val = $("#payment_recive").val();
            if(payment_val != 0) {
                alert("you cant chenge.You allready recived payment amount.If you need to do it at frist make payment recive input box put value 0.");
                return false;
            }
            var id = $(this).data("id");
            var text = $('#'+id+'_cost').val();
            if ($(this).is(':checked')) {
                $('#'+id+'_val').val(text);
                $('#'+id+'_val').removeAttr("readonly");
                var net_val = Number($("#scl_net").val()) + Number(text);
                $("#scl_net").val(net_val);
                var remain = Number($("#net").val()) - Number(net_val);
              
              $("#ramining_balance").val(remain);
            } else {
                $('#'+id+'_val').val(null);
                $('#'+id+'_val').attr("readonly", "readonly");
                  var net_val = Number($("#scl_net").val()) - Number(text);
                // alert(net_val);
                $("#scl_net").val(net_val);
                 var remain = Number($("#net").val()) - Number(net_val);
              
              $("#ramining_balance").val(remain);
            }
            
            
        });
        
        $(".key_up_down").keyup(function(){
           
           myFunction();
            // var id = $(this).val();
            //  //alert(id);
            //  var net_val = Number($("#scl_net").val()) + Number(id);
            // $("#scl_net").val(net_val);
            // var remain = Number($("#net").val()) - Number(net_val);
            // //var text = $('#'+id+'_cost').val();
            
            
        });
        

        // $(".open_form").click(function(){
        //     var id = $(this).data("id");
        //     if (id == "admission") {
        //         if ($(this).is(':checked')) {
        //             $(".dis_monthely").css("display","none");
        //             $(".dis_admission").css("display","block");
        //         } else {
        //             $(".dis_monthely").css("display","block");
        //             $(".dis_admission").css("display","block");
        //         }
        //     }
        //     if (id == "monthly") {

        //         if ($(this).is(':checked')) {
        //             $(".dis_admission").css("display","none");
        //             $(".dis_monthely").css("display","block");
        //         } else {
        //             $(".dis_admission").css("display","block");
        //             $(".dis_monthely").css("display","block");
        //         }


        //         //   $(".dis_admission").css("display","none");
        //         //   $('this').prop('checked');
        //     }

            // return false;


            // var text = $('#'+id+'_cost').val();
            // if ($(this).is(':checked')) {
            //     $('#'+id+'_val').val(text);
            //     $('#'+id+'_val').removeAttr("readonly");
            // } else {
            //     $('#'+id+'_val').val(null);
            //     $('#'+id+'_val').attr("readonly", "readonly");
            // }
        // });

    });


    function myFunction() {
        var monthly_fee_val=$('#monthly_fee_val').val();
        var admission_val=$('#admission_val').val();
        var uniform_val=$('#uniform_val').val();
        var icard_val=$('#icard_val').val();
        var sweater_slax_cap_val=$('#sweater_slax_cap_val').val();
        var bag_val=$('#bag_val').val();
        var shoes_sockes_val=$('#shoes_sockes_val').val();
        var admission_fee_val=$('#admission_fee_val').val();
        // var paid_val = Number(val) + Number(admission_val) + Number(uniform_val) + Number(icard_val) + Number(sweater_slax_cap_val) + Number(bag_val) + Number(shoes_sockes_val) + Number(shoes_sockes_val);
        // $("#scl_net").val(paid_val);
        $("#scl_net").val(Number($("#monthly_fee_val").val()) + Number($("#admission_val").val())+ Number($("#uniform_val").val())+ Number($("#icard_val").val())+ Number($("#sweater_slax_cap_val").val())+ Number($("#bag_val").val())+ Number($("#shoes_sockes_val").val())+ Number($("#admission_fee_val").val()));
        var remain = Number($("#net").val()) - Number($("#scl_net").val());
        $("#ramining_balance").val(remain);
    }
    

    document.multiselect('#testSelect1')

        .setCheckBoxClick("checkboxAll", function(target, args) {

            //	console.log("Checkbox 'Select All' was clicked and got value ", args.checked);

        })

    //	.setCheckBoxClick("1", function(target, args) {

    //		console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);

    //	});

</script>
       
