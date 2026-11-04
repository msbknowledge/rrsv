<?php

include('include/dbcon.php');
$result="";
$scl_class=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
$cat_id=$myDB->escape_string(trim(addslashes($_POST['cat_id'])));
print_r($_POST);

//echo $sql="select a.*,b.pro_name from ak_category as a ,ak_product as b where a.id=$cat_id";
echo $sql="select * from rrsv_book  where scl_class='".$cat_id."'";
//$sql="select * price_list where dept_id=$dept_id";
$res=mysqli_query($myDB,$sql);
$result .="<option value=''>-Select a Parameter-</option>";
while($rows=mysqli_fetch_array($res,MYSQLI_ASSOC))

{
	
	$result .="<option value='".$rows['id']."'>".$rows['book_name']."</option>";
	//$result .="<input type='text' id='Fld_Name' value='".$rows['id']."'>".$rows['price']."";
//	$result .="<input type='text' class='form-control' name='Fld_Name' id='Fld_Name' value='" . $rows["price"]. "'>";
}
// $result .="<input type='text' value='".$rows['price']."'>";  
echo $result;

?>

