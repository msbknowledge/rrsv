<?php
include('include/header.php');
include('include/dbcon.php');



$id=0;
$status="";
$rows=0;
$res=0;
if(isset($_GET['id']))
{
$id=$_GET['id'];
$sql="select * from   rrsv_teacher_attendence where id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
print_r($rows);
$teacher_id = $rows['teacher_id'];
    $originalDate = $rows['date'];
     $in_time = $rows['in_time'];
     $out_time = $rows['out_time'];
echo $teacher_id;
?>

<div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <!--Add Attendence-->
                        <h4 class="card-title">
                            
                            <button type="button" class="btn btn-dark btn-rounded btn-icon" onclick="history.go(-1)"><i class="mdi mdi-keyboard-backspace"></i></button>
                            
                            &nbsp;Edit Teacher Attendence</h4>
                        <form action="teacher_attendence_post.php" method="POST">
                            <input type="hidden" name="token" id="token" value="<?=$_SESSION['_token']?>">
                            <input type="hidden" name="id" id="id" value="<?=$rows['id'];?>">
                         <div class="row">
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Teacher Name</label>
                                            <div class="col-sm-12">
                                                <select name="teacher_id" id="teacher_id" class="form-control minput">
                                                    <option value="">--Select Teacher Name--</option>
                                                    <?php
                                                        $sql="select id,full_name from rrsv_teacher where 1";
                                                        $res=mysqli_query($myDB,$sql);
                                                        while($rows=mysqli_fetch_array($res)) {
                                                    ?>
                                                    <option value="<?=$rows['id'];?>" <?php echo ((int)$teacher_id == (int)$rows['id']) ? "selected" : "" ?>><?=$rows['full_name'];?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Date</label>
                                            <div class="col-sm-12">
                                                <input type="date" class="form-control" id="date" value="<?php echo $originalDate;?>" name="date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>In Time</label>
                                            <div class="col-sm-12">
                                                <input type="time" id="in_time" name="in_time" value="<?php echo $in_time;?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 dis_month"> 
                                        <div class="form-group row">
                                            <label class="col-form-label"><span></span>Out Time</label>
                                            <div class="col-sm-12">
                                                <input type="time" id="out_time" name="out_time" value="<?php echo $out_time;?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                                           
                                <div class="row">
                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                </div>
                                 </form>
                                 </div>
                                  <!--Add Attendence-->
                                  
                                  
                                  </div>
                                  </div>
                                  </div>

<?php
include('include/footer.php');
?>
</div>
