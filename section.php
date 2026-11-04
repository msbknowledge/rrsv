<?php
    include('include/dbcon.php');

$result="";

$scl_class=$_POST['scl_class'];

echo $sql="select * from rrsv_section where class_name='$scl_class'";
 $res=mysqli_query($myDB,$sql);
 $result .="<option value=''>Select Section</option>";
while($rows=mysqli_fetch_array($res,MYSQLI_ASSOC))
{
	$result .="<option value='".$rows['section_name']."'>".$rows['section_name']."</option>";
	
}
echo $result;
?>
