<?php
include('include/header.php');
    include('include/dbcon.php');


if(isset($_POST['id']))
{
    $id=$myDB->escape_string(trim(addslashes($_POST['id'])));
  $in_time=$myDB->escape_string(trim(addslashes($_POST['in_time'])));
  $out_time=$myDB->escape_string(trim(addslashes($_POST['out_time'])));
  $date = date('Y-m-d H:i:s');
$sqlUp="update rrsv_time  set
				in_time           	='".$in_time."',
				out_time           	='".$out_time."',
				update_at           = '".$date."'
            	where id			='".$id."'";
		
		$result=mysqli_query($myDB,$sqlUp) or die("Error into update post:".mysqli_connect_error());
		
		if($result) {
			echo '<script language="javascript" type="text/javascript">';
      echo 'alert("Time status update sucessfully");';
      echo '</script>';
		
		}
  
  
}

// if($id > 0) {
  
//   $sql="Delete from rrsv_book where id='".$id."'";
//   $res=mysqli_query($myDB,$sql);
//   if($res)
//   {
//   echo '<script language="javascript" type="text/javascript">';
//       echo 'window.location.href="manage_book.php?retcode=3";';
//       echo '</script>';
//   }
//   }
?>
<script language="javascript" type="text/javascript">


  
  function confirmdel(id)
  {
    if(confirm("Are you sure to delecte this Information?")){
      window.location.href="manage_book.php?id="+id;
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
                  <h4 class="card-title">manage time expenditure</h4>
                   </div>
            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
             <form name="frmsearch" method="post" action="manage_book.php" display="none">
            <table id='empTable' class='display dataTable'>
                <thead>

                    <th>Update Date</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th>Action</th>
                </tr>
                </thead>
 <?php
                          
                         
                         $sql="SELECT * FROM `rrsv_time` LIMIT 1";
		                 
                             $result=mysqli_query($myDB,$sql);
                             
                            
                             while ($rows=mysqli_fetch_array($result,MYSQLI_ASSOC))
                             {
                              
                          ?>
                      
                        <tr>
                        <td  class="text" valign="top" style="padding-left:15px;"><?=$rows['update_at'];?></td>
                        <td  class="text" valign="top"><?=$rows['in_time'];?></td>
					
					    <td  class="text" valign="top"><?=$rows['out_time'];?></td>
						
                        <td  class="text" valign="top">
                         <a href="edit_time.php?id=<?=$rows['id'];?>&in_time=<?=$rows['in_time'];?>&out_time=<?=$rows['out_time'];?>" title="Click to edit this course" class="btn btn-primary btn-sm"><b>Edit</b></a>
                        </td>
                        </tr>
                        
                         <tr>
                         
                         </tr>
                         <?php
                          
                          }
                        //   else {
                        //     echo "<tr>";
                        //     echo "<td class='errtext' align='center' colspan=10>Records Not Found</td>";
                        //     echo "</tr>";
                        //   }  
                       ?>
                         </table>
                    
            </table>
           
        </div>
        </div>
            </div>
     </div>

          
          
      
          
        <?php
include('include/footer.php');
?>
