<?php 
session_start();
include('include/dbcon.php');
$status=$_SESSION['status'];
$add_status=$_SESSION['add_status'];
$scl_session=$_SESSION['scl_session'];
$scl_class=$_SESSION['scl_class'];


  
  
				       $sql="select * from rrsv_student_registration where 1";
				       //////////////////////////////// using singl search ///////////////////////////////////////////
				        if($scl_session!="" && $status == "" && $add_status == "" && $scl_class == "") {
                                $sql.=" and (scl_session='".$scl_session."')";
                                $status_report .= "and session for ".$scl_session;
                        }
                        if($scl_class!="" && $status == "" && $add_status == "" && $scl_session == "") {
                                $sql.=" and (scl_class='".$scl_class."')";
                                $status_report .= "and Class for ".$scl_class;
                        }
                        if($add_status!="" && $status == "" && $scl_session == "" && $scl_class == "") {
                                $sql.=" and (add_status='".$add_status."')";
                                $status_report .= "and Student Admination status for ".$add_status;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session == "" && $scl_class == "") {
                                $sql.=" and (status='".$status."')";
                                $status_report .= "and Student status for ".$status;
                        }
                        //////////////////////////////// using singl search ///////////////////////////////////////////

                        //////////////////////////////// using session ///////////////////////////////////////////
                        if($status=="" &&  $add_status== "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and session for ".$scl_session." and Class for ".$scl_class;
                        }
                        if($status=="" &&  $add_status!= "" && $scl_session != "" && $scl_class == "") {
                                $sql.=" and (add_status='".$add_status."' and  scl_session='".$scl_session."')";
                                $status_report .= " and Admination status for ".$status." and session for ".$scl_class;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session != "" && $scl_class == "") {
                                $sql.=" and (status='".$status."' and  scl_session='".$scl_session."')";
                                $status_report .= " and Student Status for ".$status." and session for ".$scl_session;
                        }
                        if($status=="" &&  $add_status!= "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (add_status='".$add_status."' and  scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Admination Status for ".$add_status." and session for ".$scl_session." and class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (status='".$status."' and  scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Student Status for ".$status." and session for ".$scl_session." and class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status!= "" && $scl_session != "" && $scl_class == "") {
                                $sql.=" and (status='".$status."' and  scl_session='".$scl_session."' and  add_status='".$add_status."')";
                                $status_report .= "and Student Status for ".$status." and session for ".$scl_session." and Admination status for ".$add_status;

                        }
                        //////////////////////////////// using session //////////////////////////////////////////
                        
                        //////////////////////////////// using class ///////////////////////////////////////////
                        if($status=="" &&  $add_status!= "" && $scl_session == "" && $scl_class != "") {
                                $sql.=" and (add_status='".$add_status."' and  scl_class='".$scl_class."')";
                                $status_report .= " and Admination Status for ".$add_status." and Class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status== "" && $scl_session == "" && $scl_class != "") {
                                $sql.=" and (status='".$status."' and  scl_class='".$scl_class."')";
                                $status_report .= " and Student Status for ".$status." and Class for ".$scl_class;
                        }
                        if($status!="" &&  $add_status!= "" && $scl_session == "" && $scl_class != "") {
                                $sql.=" and (status='".$status."' and  add_status='".$add_status."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Student Status for ".$status." and Admination status for ".$add_status." and class for ".$scl_class;
                        }
                        //////////////////////////////// using class //////////////////////////////////////////
                        
                        //////////////////////////////// using admination status ///////////////////////////////////////////
                        if($status!="" &&  $add_status!= "" && $scl_session == "" && $scl_class == "") {
                                $sql.=" and (add_status='".$add_status."' and  status='".$status."')";
                                $status_report .= " and Admination Status for ".$add_status." and Student Status for ".$status;
                        }
                        //////////////////////////////// using admination status //////////////////////////////////////////
                        
                        //////////////////////////////// using all filds ///////////////////////////////////////////
                        if($status!="" &&  $add_status!= "" && $scl_session != "" && $scl_class != "") {
                                $sql.=" and (add_status='".$add_status."' and  status='".$status."' and  scl_session='".$scl_session."' and  scl_class='".$scl_class."')";
                                $status_report .= "and Admination Status for ".$add_status." and Student status for ".$status." and Session for ".$scl_session." and Class for ".$scl_class;

                        }
                        //////////////////////////////// using all filds //////////////////////////////////////////
                        
                            // echo $sql;
                            // die();
				     $res=mysqli_query($myDB,$sql);
                   
				     
     
	if(mysqli_num_rows($res) > 0)
 {
  $output .= '
   <table class="table" bordered="1">  
    <tr>  
  <th >SL. NO</td>
                <th >REG. NO </th>
                <th >DATE OF ADMISSION </th>
               
                <th >STUDENT NAME</th>
                 <th >CLASS</th>
                 <th >SECTION</th>
                 <th >ROLL NO</th>
                <th >DATE OF BIRTH</th>
				<th >GENDER</th>
                <th >CAST</th>
               	<th >FATHER NAME</th>
				<th >MOTHER NAME</th>
				<th >PHONE NO</th>
				<th >WhatsApp NO</th>
               <th >ADDRESS</th>
              <th > POST OFFICE</th>
               <th > POLICE STATION</th>
                <th >BLOCK</th>
             <th >	GRAM PANCHAYAT/ MUNICIPALITY</th>
              <th >LOCALITY</th>
               <th >DISTRICT</th>
                <th >STATE</th>
             <th >PIN</th>    
               <th >ADMISSION TYPE</th>
               <th >STATUS</th>
                 <th >STATUS DATE</th>
     </tr>
  ';
while($row = mysqli_fetch_array($res))
  {
      $c++;
   $output .= '
    <tr> 
    <td>'.$c.'</td>  
   <td>'.$row['scl_reg_no'].'</td>
    <td>'.$row["scl_date"].'</td>  
   <td>'.$row["scl_name"].'</td> 
   <td>'.$row["scl_class"].'</td>  
     <td>'.$row["scl_section"].'</td> 
       <td>'.$row["scl_roll_no"].'</td> 
      <td>'.$row["scl_dob"].'</td> 
      <td>'.$row["scl_gender"].'</td> 
      <td>'.$row["scl_religion"].'</td> 
      <td>'.$row["scl_father_name"].'</td> 
      <td>'.$row["scl_mother_name"].'</td> 
      <td>'.$row["scl_phone_no"].'</td> 
      <td>'.$row["alt_phone"].'</td> 
       <td>'.$row["scl_address"].'</td> 

       <td>'.$row["scl_pos"].'</td> 

       <td>'.$row["scl_pol"].'</td> 

       <td>'.$row["scl_block"].'</td> 

       <td>'.$row["scl_mu"].'</td> 

       <td>'.$row["scl_location"].'</td> 

       <td>'.$row["scl_dist"].'</td> 

       <td>'.$row["scl_state"].'</td> 

       <td>'.$row["scl_pin"].'</td> 
        <td>'.$row["add_status"].'</td> 
  <td>'.$row["status"].'</td> 
    <td>'.$row["s_a_d"].'</td> 
   </tr>
   ';
  }
  $output .= '</table>';
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=statusreport.xls');
  echo $output;
 }
 else{
    echo "<script language='JavaScript'>";
		echo "window.location.href='status_report.php?status=<?php echo$status;?>';";
		echo "</script>"; 
 }
 
	?>