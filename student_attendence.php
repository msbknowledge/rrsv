<?php 
if(isset($_POST['token'])) {
    // print_r($_POST);
    // die();
   } 
?>
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




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
<!-- partial -->
<?php
session_start();
include('include/header.php');
include('include/dbcon.php');

if(isset($_POST['token'])) {
        $token =  $_POST['token'];
    $start_date =  $_POST['start_date'];
    $end_date =  $_POST['end_date'];
    $student_id_val =  $_POST['student_id_val'];

} else {
    $token =  "";
    $start_date =  "";
    $end_date =  "";
    $student_id_val =  "";
}

?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <!--Add Attendence-->
                        <h4 class="card-title">Add Absent</h4>
                        <form action="student_attendence_post.php" id="add_attendence" method="POST">
                            <input type="hidden" name="token" id="token" value="<?=$_SESSION['_token']?>">
                            <input type="hidden" name="id" id="id" value="0">
                         <div class="row">
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Student Name</label>
                                            <div class="col-sm-12">
                                                <select name="student_id" id="student_id" class="form-control minput">
                                                    <option value="">--Select Student Name--</option>
                                                    <?php
                                                        $sql="select id,scl_name from rrsv_student_registration where 1";
                                                        $res=mysqli_query($myDB,$sql);
                                                        while($rows=mysqli_fetch_array($res)) {
                                                    ?>
                                                    <option value="<?=$rows['id'];?>"><?=$rows['scl_name'];?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="date" name="date" onchange="myFunction()" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Unit</label>
                                            <div class="col-sm-12">
                                                <select name="unit" id="unit" class="form-control minput">
                                                    <option value="">--Select Unit--</option>
                                                    <option value="1st Unit">1st Unit</option>
                                                    <option value="2nd Unit">2nd Unit</option>
                                                    <option value="3rd Unit">3st Unit</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 dis_month"> 
                                        <div class="form-group row">
                                           <label class="col-form-label"><span></span>Session</label>
                                            <div class="col-sm-12">
                                                <select name="scl_session" id="scl_session" class="form-control minput">
                                                        <option value="">-Select a Session -</option>
                                            			<option value="2019">2019</option>
            											<option value="2020">2020</option>
            											<option value="2021">2021</option>
            											<option value="2022">2022</option>
            											<option value="2023">2023</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                                           
                                <div class="row">
                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                </div>
                                 </form>
                                 </div>
                                  <!--Add Attendence-->
                                  
                               <!--Search Attendence-->
                            <h4 class="card-title">Search Absent  <span>&nbsp; <a href="student_attendence.php"><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-refresh"></i></button></a></span></h4>
                            
                            <form action="" id="search_attendence" method="POST">
                                 <div class="row">
                                    
                                    <input type="hidden" name="token" id="token" value="<?=$_SESSION['_token']?>">
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Student Name</label>
                                            <div class="col-sm-12">
                                                <select name="student_id" id="student_id_val" class="form-control minput">
                                                    <option value="">--Select Student Name--</option>
                                                    <?php
                                                        $sql="select id,scl_name from rrsv_student_registration where 1";
                                                        $res=mysqli_query($myDB,$sql);
                                                        while($rows=mysqli_fetch_array($res)) {
                                                    ?>
                                                    <option value="<?=$rows['id'];?>"><?=$rows['scl_name'];?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>From Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="start_date" name="start_date"  value="<?=$start_date?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>To Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?=$end_date?>">
                                            </div>
                                        </div>
                                    </div>
                                   
                                <div class="row">
                                    <button type="submit" class="btn btn-primary me-2">Search</button>
                                </div>
                                 </div>
                                </form>
                    </div>
                    
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Student Atbsent</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th  class="card-title">
                                                sl
                                            </th>
                                            <th  class="card-title">
                                                Student Name
                                            </th>
                                            <th  class="card-title">
                                               Date
                                            </th>
                                            <th  class="card-title">
                                                In Time
                                            </th>
                                            <th  class="card-title">
                                                Out Time
                                            </th>
                                            <th  class="card-title">
                                                Total time
                                            </th>
                                            <th class="card-title">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php
                                       

                                            if(isset($_GET['page']))
                                              {
                                              $page=$_GET['page'];
                                              }
                                              else
                                              {
                                              $page=1;
                                              }
                                               $perpage=10;
                                                $lowerlimit=($page-1)*$perpage;

                                                 $sql="SELECT rrsv_student_attendence.*, rrsv_student_registration.scl_name FROM rrsv_student_attendence INNER JOIN rrsv_student_registration ON rrsv_student_attendence.student_id = rrsv_student_registration.id";

                                                    if(isset($_POST['token'])) {
                                                        $perpage=100;
                                                            if($_POST['student_id_val'] != "" && $_POST['start_date'] != "" && $_POST['end_date'] != "") {
                                                                $sql .=" WHERE student_id = ".$_POST['student_id_val']." AND  (date BETWEEN '".$_POST['start_date']."' AND '".$_POST['end_date']."')";
                                                            }
                                                            else if($_POST['student_id_val'] != "" && $_POST['start_date'] == "" || $_POST['end_date'] == "") {
                                                                $sql .=" WHERE student_id = ".$_POST['student_id_val'];
                                                            }
                                                            else if($_POST['student_id_val'] == "" && $_POST['start_date'] != "" && $_POST['end_date'] != "") {
                                                                $sql .=" WHERE (date BETWEEN '".$_POST['start_date']."' AND '".$_POST['end_date']."')";
                                                            }
                                                            else {
                                                                $sql .=" ";
                                                            }
                                                     }
                                                 $result=mysqli_query($myDB,$sql);
                                                 $totalrecord=mysqli_num_rows($result);
                                                  $totalpage=ceil($totalrecord/$perpage);
                                                 $sql .=" order by id desc limit $lowerlimit,$perpage";
                                                
                                                 $result1=mysqli_query($myDB,$sql);
                                                 $l=mysqli_num_rows($result1);
                                             
                                                  if($l>0)

                         {

                         while ($rows=mysqli_fetch_array($result1,MYSQLI_ASSOC))

                         {
                      
                        //echo 1234;
                           $c++;
						   $id=$rows['id'];
						  $status= $rows['status'];
                          ?>
                          
                                                 
                          
                                                 
                                        <tr>
                                            <td>
                                                <?=$rows['id'];?>
                                            </td>
                                            <td>
                                                <?=$rows['scl_name'];?>
                                            </td>
                                            <td>
                                                <?=date('d/m/Y', strtotime($rows['date']));?>
                                            </td>
                                            <td>
                                                <?=$rows['in_time'];?>
                                            </td>
                                            <td>
                                                <?=$rows['out_time'];?>
                                            </td>
                                            <td>
                                                <?=gmdate("H:i", (int)$rows['time_in_second']);?>
                                            </td>
                                            <td>
                                                <a href="edit_student_attendence.php?id=<?=$rows['id'];?>"><i class="mdi mdi-table-edit"></i></a><button onclick="teacher_dlt(<?=$rows['id'];?>)"><i class="mdi mdi-delete"></i></button>
                                            </td>
                                        </tr>
                                        <?php } } else { echo "no record found";}
     
                                                   ?>
                                    </tbody>
                                </table>
                                 <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
        <?php

                               if($page>1)
                               {
                              ?>
      <li class="page-item"> <a class="page-link" href="student_attendence.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="student_attendence.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="student_attendence.php?page=<?=($page+1);?>">Next</a>
    </li>
      <?php
                              }
                              
                              ?>
  </ul>
</nav>
                            </div>
                        </div>
                    </div>
                </div>
       
                    
               

<?php
include('include/footer.php');
?>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
<script src="multiselect-dropdown.js" ></script>
 <script>
    $(document).ready(function () {
        if('<?php echo $token;?>' != "") {
            $("#student_id_val").focus();
        }
    });
    
    $('#add_attendence').on('submit', function() {
        var studentid = $('#student_id').val();
        if(studentid == "" || studentid == null) {
            alert("Please Select Student name");
            return false; 
        }
        var date = $('#date').val();
        if(date == "" || date == null) {
            alert("Please Select date");
            return false; 
        }
        var unit = $('#unit').val();
        var scl_session = $('#scl_session').val();
        
        if(unit == ''){
            alert('Please select a unit');
            return false;
        }
        if(scl_session == ''){
            alert('Please select a Session');
            return false;
        }
        
         
     });
     
     $('#search_attendence').on('submit', function() {
        var studentid = $('#student_id_val').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(studentid == "" && start_date == "" && end_date == "") {
            alert("Please Select atleast one fild for search");
            return false; 
        }
        
        if(start_date != "" && end_date == "") {
            alert("Please Select end date");
            return false; 
        }
        
        if(start_date == "" && end_date != "") {
            alert("Please Select start date");
            return false; 
        }
        
        const x = new Date(start_date);
        const y = new Date(end_date);

        if (x.getTime() > y.getTime()) {
            alert("To Date Not Less Than Form Date");
            return false; 
        }
         
     });
     
     function teacher_dlt(did) {
        if (confirm('Are you sure want to delete this record?')) {
            //alert(did);
            window.location.href = "student_attendence_post.php?dId="+did;
        }
     }
     
     </script>

 