<?php session_start();
include('include/dbcon.php');
	
	$fromDate		=$myDB->escape_string(trim(addslashes($_POST['fromDate'])));
	$endDate=$myDB->escape_string(trim(addslashes($_POST['endDate'])));
	$purpose		=$myDB->escape_string(trim($_POST['purpose']));
  
   
       $sql="select * from rrsv_scl_pl where 1";
         if(!empty($fromDate) && !empty($endDate)){
           $sql .= " and pl_date
           between '".$fromDate."' and '".$endDate."' ";
         }
            if($purpose!="")
                         {
                        $sql.="  (purpose='$purpose')";
                        
                         }
     $result=mysqli_query($myDB,$sql);
     
	if(mysqli_num_rows($result) > 0)
 {
  $output .= '
   <table class="table" bordered="1">  
    <tr>  
   <th>SL.No</th>
                    <th>Date</th>
                    <th>Purposr</th>
                     <th>Income</th>
                      <th>Expence</th>
     </tr>
  ';
while($row = mysqli_fetch_array($result))
  {
   $output .= '
    <tr> 
    <td>'.$row["id"].'</td>  
   <td>'.$row['pl_date'].'</td>
    <td>'.$row["purpose"].'</td>  
   <td>'.$row["income_amount"].'</td> 
   <td>'.$row["exp_amount"].'</td>  
   
   </tr>
   ';
  }
  $output .= '</table>';
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=Income&ExpenseReport.xls');
  echo $output;
 }
 else{
    echo "<script language='JavaScript'>";
		echo "window.location.href='manage_expenditure.php?retcode=1';";
		echo "</script>"; 
 }

	?>