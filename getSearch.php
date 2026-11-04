<?php
// print_r($_POST);
// die();
include('include/dbcon.php');

$type = null;
if(isset($_POST['scl_session'])){
    $scl_session = mysqli_real_escape_string($myDB,$_POST['scl_session']);
    $class = mysqli_real_escape_string($myDB,$_POST['class']);

    $sql = "SELECT id,scl_name FROM rrsv_student_registration where scl_session = '".$scl_session."' and scl_class = '".$class."'";
//echo $sql;
// die();
    $result = mysqli_query($myDB,$sql);
    
    $search_arr = array();
    
if(mysqli_num_rows($result) > 0) {
    while($fetch = mysqli_fetch_assoc($result)){
        $id = $fetch['id'];
        $name = $fetch['scl_name'];

        $search_arr[] = array("id" => $id, "name" => $name);
    }
} 
echo json_encode($search_arr);
} else {
        $search_arr[] = array("id" => 0, "name" => "no record found1");
        echo json_encode($search_arr);
}

?>