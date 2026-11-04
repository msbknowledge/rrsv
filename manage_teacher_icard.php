<?php
error_reporting(1);
include('include/header.php');
    include('include/dbcon.php');


$id=0;
$txtsearch="";
$status="";

	if(isset($_GET['page'])){
		$current_page=$_GET['page'];
	}else{
		$current_page="";
	}
if(isset($_GET['id'])) {
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
}

if(isset($_POST['txtsearch']))
{
  $txtsearch=$myDB->escape_string(trim(addslashes($_POST['txtsearch'])));
}


if(isset($_GET['status'])) {
	$status=$myDB->escape_string($_GET['status']);
}
// echo $txtsearch;
// die();
?>
<script language="javascript" type="text/javascript">

function dosearch()
  { 
    document.frmsearch.method='post';
    document.frmsearch.action='manage_icard.php';
    document.frmsearch.submit();
    return true;
  }
  
  /*function confirmdel(id){
		if(confirm("Are you sure to delete this Information?")) {
			window.location.href="manage_icard.php?mode=del&id="+id;
			return true;
		}
	}*/
	

	
	
	function confirmsearch(id,status)
	{
				if(confirm("Are you sure to change this Student status?")) {
				//	window.location.href="manage_icard.php?mode=sts&id="+id+"$status="+status+;
			window.location.href="manage_icard.php?mode=sts&id="+id+"&status="+status;
					//	window.location.href="manage_icard.php?mode=sts&id="+id+"&status="+status+'&page=<?=$current_page?>';
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

            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
             <form name="frmsearch" method="post" action="" >
            <table id='empTable' class='display dataTable'>
                <thead>
                                          <tr>
                        <td class="text" align="center" colspan="12" valign="top">
					
                            <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                            <!--  <option value="">-Select a Section-</option>-->

                            <!--</select>-->
                            <input type="text" name="txtsearch" class="form-control" value="" size="30" maxlength="100"  placeholder="Search By Name, Reg No" style="
    width: 231px;
    margin-left: 355px;
    margin-top: 4px;
">
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
                    <th>Photo</th>
                    <th>Teacher Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                </thead>
 <?php
 $bgcolor="";
                          $c=0;
                        

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
                         
                         $sql="select * from rrsv_teacher where 1 ";
                       //$sql="select a.*,b.class_name from rrsv_student_registration as a,class as b where a.scl_class=b.id ";

                         if($txtsearch!="")
                         {
                        $sql.=" and (full_name LIKE '%$txtsearch%' || email LIKE '%$txtsearch%' )";
                        
                         }
		                  if($scl_session!="")
                         {
                         $sql.=" and (scl_session LIKE '%$scl_session%')";
                        
                         }
					     if($scl_class!="")
                         {
                         $sql.=" and (scl_class LIKE '$scl_class')";
                        
                         }
				     if($scl_section!="")
                         {
                         $sql.=" and (scl_section LIKE '%$scl_section%')";
                        
                         }
                         /*if($luid!='1')
                         {
                         $sql.=" and (created_user='$luid' or updated_user='$luid')";
                         }*/

                       
                        
                         $result=mysqli_query($myDB,$sql);
                             $totalrecord=mysqli_num_rows($result);
                             $totalpage=ceil($totalrecord/$perpage);
                             $sql .="  order by id desc limit $lowerlimit,$perpage";
                             $result1=mysqli_query($myDB,$sql);
                             $l=mysqli_num_rows($result1);
                             $result=mysqli_query($myDB,$sql);

                         if($l>0)

                         {

                         while ($rows=mysqli_fetch_array($result1,MYSQLI_ASSOC))

                         {
                      
                        
                           $c++;
						   $id=$rows['id'];
						  $status= $rows['status'];
                          ?>
                      
                        <tr>
                         <td class="text" style="padding-left:10px;" valign="center">#<?php echo $c;?></td>
                        <td class="text"valign="center" >
					   <?php
					   
					            $img = $rows['image']; // == "" ? '' : '<img src="http://rrsv.in/teacher_image/'.$row['image'].'" hight="50" width="50" style="border-radius: 50%;" alt="image">',

							if($img !="") { ?>
							    <img src="http://rrsv.in/teacher_image/<?=$rows['image']?>" hight="50" width="50" alt="image">
							<?php } else { ?>
								 <img src="https://thumbs.dreamstime.com/b/avatar-icon-avatar-flat-symbol-isolated-white-avatar-icon-avatar-flat-symbol-isolated-white-background-avatar-simple-icon-124920496.jpg" hight="50" width="50" alt="image">
								<?php }	?>
						</td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['full_name'];?></td>
                        <td  class="text" valign="center" style="padding-left:10px;"><?=$rows['email'];?></td>

                        
					  <td  class="text" valign="center" style="padding-left: -50px;">
							  
						<a class="btn btn-primary" href="teacher_id_card1.php?id=<?=$rows['id'];?>" title="Click to edit this Student"  class="btn btn-primary">I-Catd
						</a>
                                           
                         </td>
                        </tr>
                        
                         <tr>

                         </tr>
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

            <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item">
        <?php

                               if($page>1)
                               {
                              ?>
      <a class="page-link" href="manage_teacher_icard.php?page=<?=($page-1);?>" tabindex="-1">Previous</a>
    </li>
    <?php
                              }
                              ?>
                              <?php
                              for($i=1;$i<=$totalpage;$i++)
                              {
                              ?>
    <li class="page-item"><a class="page-link" href="manage_teacher_icard.php?page=<?=$i;?>"><?php echo $i ?></a></li>
                              <?php
                              }
                              ?>

                              <?php
                              if($totalpage>$page)
                              {
                              ?>
    <li class="page-item">
      <a class="page-link" href="manage_teacher_icard.php?page=<?=($page+1);?>">Next</a>
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
