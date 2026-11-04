<?php
include('include/header.php');
include('include/dbcon.php');
if(isset($_POST['txtsearch']))
{
  $txtsearch=$myDB->escape_string(trim(addslashes($_POST['txtsearch'])));
}
if(isset($_GET['id'])) {
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}
if(isset($_GET['retcode'])) {
  $retcode=$myDB->escape_string(trim(addslashes($_GET['retcode'])));
}

if($retcode==3) {
  $msg="course has been deleted successfully";
}
if($id > 0) {
  
  $sql="Delete from rrsv_class where id='".$id."'";
  $res=mysqli_query($myDB,$sql);
  if($res)
  {
  echo '<script language="javascript" type="text/javascript">';
      echo 'window.location.href="manage_class.php?retcode=3";';
      echo '</script>';
  }
  }
?>
<script language="javascript" type="text/javascript">


  function confirmdel(id)
  {
    if(confirm("Are you sure to delecte this Information?")){
      window.location.href="manage_class.php?id="+id;
      return true;
    }
  }
  </script>
    <!-- partial -->
   
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
          
           
            <div class="col-lg-12 grid-margin stretch-card">

              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"> <a href='add_class.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Class</button></a></h4>
                  <?php $msg;?>
                   <form name="frmsearch" method="post" action="manage_class.php" >
                   <div class="form-group">
                    <div class=" ">
                      <input type="text" name="txtsearch" class="form-control searchform" placeholder="Recipient's username" aria-label="Recipient's username">
                      <div class="">
                       <button type="submit" class="btn btn-primary btn-icon-text searchButton">
                          <i class="mdi mdi-file-check btn-icon-prepend"></i>
                          Submit
                        </button>
                        </div>
                        <div class="">
                        <a href='manage_class.php'><button type="button" name="Reset" class="btn btn-warning btn-icon-text searchReset">
                          <i class="mdi mdi-reload btn-icon-prepend"></i>                                                    
                          Reset
                        </button>
                      </div>
                    </div>
                  </div>
                  </form>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>
                            SL.No
                          </th>
                          <th>
                            Class name
                          </th>
                         

                          <th>
                            Action
                          </th>
                        </tr>
                      </thead>
                      <?php
                            
							$c=0;
                      if(isset($_GET['page']))
                           {
                           $page=$_GET['page'];
                           }
                           else
                           {
                           $page=1;
                           }
                           $perpage=5;
                           $lowerlimit=($page-1)*$perpage;
                           
                           $sql="select * from  rrsv_class where 1";
                           if($txtsearch!="")
                           {
                           $sql.=" and (class_name LIKE '%$txtsearch%')";
                           }
                             $result=mysqli_query($myDB,$sql);
                             $totalrecord=mysqli_num_rows($result);
                             $totalpage=ceil($totalrecord/$perpage);
                             $sql .=" order by id  limit $lowerlimit,$perpage";
                             $result1=mysqli_query($myDB,$sql);
                             $l=mysqli_num_rows($result1);
                             $result=mysqli_query($myDB,$sql);
                             if($l>0)
                             {
                             while ($rows=mysqli_fetch_array($result,MYSQLI_ASSOC))
                             {
 if($bgcolor=='#dff8e3')
                            {$bgcolor='#FFFFFF';} 
                          else
                            {$bgcolor='#dff8e3';}
                          $c++;
                         
                      ?>
                      <tbody>
                        <tr bgcolor="<?php echo $bgcolor;?>" height="20">
                          <td class="py-1">
                            <?php echo $c;?>
                          </td>
                          
                          <td>
                            <?=$rows['class_name'];?>
                          </td>
                         <td>
                             <a  href="add_class.php?id=<?=$rows['id'];?>" title="Click to edit this Category"><button type="button" class="btn btn-primary btn-rounded btn-icon">
                        <i class="mdi mdi-table-edit"></i>
                      </button>
                        </a>
                                        
                          <a href="#" onclick="confirmdel(<?=$rows['id'];?>);" title="Click to delete this Admission"><button type="button" class="btn btn-danger btn-rounded btn-icon">
 <i class="mdi mdi-delete"></i>
                      </button></a>
                         </td>
                         
                        </tr>
                        
                      </tbody>
                      <?php
                           }
                          }
                          else {
                            echo "<tr>";
                            echo "<td class='errtext' align='center' colspan=6>Records Not Found</td>";
                            echo "</tr>";
                           }  
                       ?>
                    </table>
                    <div style="margin-top: 26px;margin-left: 5px;">
                    <nav aria-label="Page navigation example">
  <ul class="pagination">
      <?php
      if($page>1)
      {
      ?>
    <li class="page-item"><a class="page-link" href="manage_class.php?page=<?=($page-1);?>">Previous</a></li>
    <?php
      }
      for($i=1;$i<=$totalpage;$i++)
                              {
                             
      ?>
    <li class="page-item"><a class="page-link" href="manage_class.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                                  <?php
                              }
                              if($totalpage>$page)
                              {
                              ?>
   
    <li class="page-item"><a class="page-link" href="manage_class.php?page=<?=($page+1);?>">Next</a></li>
    <?php
                              }
                              ?>
  </ul>
</nav>
</div>
                     
                  </div>
                </div>
              </div>
            </div>
         
           
           
          </div>
        </div>
        <!-- content-wrapper ends -->
       <?php
include('include/footer.php');
?>
        