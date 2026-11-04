<?php 
if(isset($_POST['token'])) {
    // print_r($_POST);
    // die();
   } 
    include('include/dbcon.php');

   
  	$teacher_id								=$myDB->escape_string(trim(addslashes((int)$_GET['id'])));
	$date 							=$myDB->escape_string(trim(addslashes($_GET['date'])));

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

function teacher_name($myDB,$id){
    $output='';
    $sql = "select full_name from rrsv_teacher where id='".$id."' limit 1";
    $res=mysqli_query($myDB,$sql);
    $obj1=mysqli_fetch_array($res,MYSQLI_ASSOC);
    if($obj1['full_name'] == "") {
        return 0;
    } else {
        return $obj1['full_name'];
    }
} 
?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

             <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body"><button type="button" onclick="history.back()" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button>
                        <h4 class="card-title">P .C cLASS</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th  class="card-title">
                                               Date
                                            </th>
                                            <th  class="card-title">
                                                TEACHER NAME
                                            </th>
                                            <th  class="card-title">
                                                 P.C TEACHER NAME
                                            </th>
                                            <th  class="card-title">
                                                CLASS COUNT
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
                                               $perpage=40;
                                                $lowerlimit=($page-1)*$perpage;

                                                 $sql="SELECT * FROM rrsv_p_c_teacher_class where teacher_id=$teacher_id and date='".$date."'";
                                                 $result=mysqli_query($myDB,$sql);
                                                 $totalrecord=mysqli_num_rows($result);
                                                  $totalpage=ceil($totalrecord/$perpage);
                                                 $sql .=" order by id desc limit $lowerlimit,$perpage";
                                                 $result1=mysqli_query($myDB,$sql);
                                                 $l=mysqli_num_rows($result1);
                                                 $result=mysqli_query($myDB,$sql);
                                                 // echo $sql;
                                                  if($l>0)

                         {

                         while ($rows=mysqli_fetch_array($result1,MYSQLI_ASSOC))

                         {
                      
                        
        //                   $c++;
						  // $id=$rows['id'];
						  //$status= $rows['status'];
                          ?>
                          
                                                 
                          
                                                 
                                        <tr>
                                            <td>
                                                <?=date('d/m/Y', strtotime($rows['date']));?>
                                            </td>
                                            <td>
                                                <?php echo teacher_name($myDB,$rows['teacher_id']);?>
                                            </td>
                                            <td>
                                                <?php echo teacher_name($myDB,$rows['p_c_teacher_id']);?>
                                            </td>
                                            <td>
                                                <?=$rows['cls_count'];?>
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
      <li class="page-item"> <a class="page-link" href="teacher_attendence.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="teacher_attendence.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="teacher_attendence.php?page=<?=($page+1);?>">Next</a>
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


/////////////////////
/////////////////







                
