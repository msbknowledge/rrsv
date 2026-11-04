<?php
include('include/header.php');
    include('include/dbcon.php');
if(isset($_GET['id'])) {
  $id=$myDB->escape_string($_GET['id']);
}

if(isset($_POST['scl_session']))
{
  $scl_session=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
}
if(isset($_POST['class_name']))
{
  $class_name=$myDB->escape_string(trim(addslashes($_POST['class_name'])));
}
if($retcode==3) {
  $msg="subject has been deleted successfully";
}
  if($id > 0) {
  
  $sql="Delete from rrsv_subject where id='".$id."'";
  $res=mysqli_query($myDB,$sql);
  if($res)
  {
  echo '<script language="javascript" type="text/javascript">';
      echo 'window.location.href="manage_subject.php?retcode=3";';
      echo '</script>';
  }
  }
?>
<script language="javascript" type="text/javascript">


  
  function confirmdel(id)
  {
    if(confirm("Are you sure to delecte this Information?")){
      window.location.href="manage_subject.php?id="+id;
      return true;
    }
  }
  </script>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

        <!-- jQuery Library -->
        <script src="libray/js/jquery-3.3.1.min.js"></script>
        
        
        
<div class="main-panel">
        <div class="content-wrapper">

            <div class="card">
                <div class="card-body">
                  <h4 class="card-title"> <a href='add_subject.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add Subject</button></a></h4>
                   </div>
            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
             <form name="frmsearch" method="post" action="manage_subject.php" >
            <table id='empTable' class='display dataTable'>
                <thead>
<tr>
                        <td class="text" align="center" colspan="12" valign="top">
<!--							<select name="scl_session" class="form-control" value="<?=$rows['scl_session'];?>" id="scl_session"style="-->
<!--    width: 240px;-->
<!--    margin-left: -703px;-->
<!--">-->
                              <!--<option value="">-Select a Session -</option>-->
                                <?php
								// 	for($i = date("Y")-3; $i <=date("Y")+10; $i++)
								// {
								// 	echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
								// }

								?>
							<!--</select>-->
                            <select name="class_name" class="form-control" id="class_name" style="
    margin-top: -86px;
    width: 269px;
    margin-left: -168px;
">
                                <option value="">-Select a Class-</option>
                                    <?php
									$id=0;
									$sql="select * from rrsv_class order by id";
									$res=mysqli_query($myDB,$sql);
									while($obj=mysqli_fetch_array($res,MYSQLI_ASSOC))
									{
									?>
								<option value="<?php echo $obj['class_name'];?>"<?php if(trim($rows['class_name']==$obj['class_name']))echo "selected";?>>
									<?php echo $obj['class_name'];?>
          						</option>
									<?php
									}
									?>
                            </select>
                            <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                            <!--  <option value="">-Select a Section-</option>-->

                            <!--</select>-->

<div class="butt" style="
    margin-top: -46px;
    margin-left: 796px;
">
                        <button type="submit" value="" class="btn btn-primary button" valign="center">Search</button>
                        <button type="submit" value="" name="Reset" class="btn btn-primary buttonr" valign="center">Refresh</button>
                        </div>
                </tr>
                </form>
                <tr>
 <th>SL.No</th>
                    <th>Class Name</th>
                   
                    <th>Subject Name</th>
                   
                    <th>Action</th>
                </tr>
                </thead>
 <?php
                          if(isset($_GET['page']))
                           {
                           $page=$_GET['page'];
                           }
                           else
                           {
                           $page=1;
                           }
                           $perpage=100;
                           $lowerlimit=($page-1)*$perpage;
                         
                         $sql="select * from rrsv_subject where 1";
		                 
					     if($class_name!="")
                         {
                         $sql.=" and class_name='".$class_name."'";
                        
                         }
                             $result=mysqli_query($myDB,$sql);
                             $totalrecord=mysqli_num_rows($result);
                             $totalpage=ceil($totalrecord/$perpage);
                             $sql .="  order by id desc limit $lowerlimit,$perpage";
                             $result1=mysqli_query($myDB,$sql);
                             $l=mysqli_num_rows($result1);
                             $result=mysqli_query($myDB,$sql);
                             if($l>0)
                             {
                             while ($rows=mysqli_fetch_array($result,MYSQLI_ASSOC))
                             {
                              if($bgcolor=='#dff8e3')
                            {$bgcolor='#EEEEEE';} 
                          else
                            {$bgcolor='#dff8e3';
                              
                            } 
                            $c++;
                          ?>
                      
                        <tr>
                        <td  class="text" valign="top" style="padding-left:15px;"><?php echo $c;?></td>
                        <td  class="text" valign="top" style="padding-left:15px;"><?=$rows['class_name'];?></td>
                      
					
					    <td  class="text" valign="top"><?=$rows['sub_name'];?></td>
					
						
                        <td  class="text" valign="top">
                         <a href="add_subject.php?id=<?=$rows['id'];?>" title="Click to edit this course" class="btn btn-primary btn-sm"><b>Edit</b></a>
                         <a href="#" onclick="confirmdel(<?=$rows['id'];?>);" title="Click to delete this Admission"  class="btn btn-danger btn-sm">Delete</a>  
                        </td>
                        </tr>
                        
                         <tr>
                         
                         </tr>
                         <?php
                           }
                          }
                          else {
                            echo "<tr>";
                            echo "<td class='errtext' align='center' colspan=10>Records Not Found</td>";
                            echo "</tr>";
                           }  
                       ?>
                         </table>
                    
            </table>
            <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
        <?php

                               if($page>1)
                               {
                              ?>
      <li class="page-item"><a class="page-link" href="manage_subject.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_subject.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_subject.php?page=<?=($page+1);?>">Next</a>
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
