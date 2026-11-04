<?php
// print_r($_POST);
// die;
session_start();


$token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;

$class_id = 0;
if (isset($_POST['stock_type']) && trim(addslashes($_POST['stock_type'])) != "OTHERS") {
    $class_id = $myDB->escape_string(trim(addslashes($_POST['class_id'])));
}

$session = $myDB->escape_string(trim(addslashes($_POST['session'])));
$stock_type = $myDB->escape_string(trim(addslashes($_POST['stock_type'])));
$stock_master_id = $myDB->escape_string(trim(addslashes($_POST['stock_master_id'])));
$publishers_id = $myDB->escape_string(trim(addslashes($_POST['publishers_id'])));

$date = $myDB->escape_string(trim(addslashes($_POST['date'])));
$qty = $myDB->escape_string(trim(addslashes($_POST['qty'])));
$mrp = $myDB->escape_string(trim(addslashes($_POST['mrp'])));
$total = $myDB->escape_string(trim(addslashes($_POST['total'])));

$comission_prectantage = $myDB->escape_string(trim(addslashes($_POST['comission_prectantage'])));
$comission_amount = $myDB->escape_string(trim(addslashes($_POST['comission_amount'])));
$purchase_value = $myDB->escape_string(trim(addslashes($_POST['purchase_value'])));
$purchase_value_per_unit = $myDB->escape_string(trim(addslashes($_POST['purchaseValuePerUnit'])));
//$name = strtoupper($name);
//if (isset($tokenpost) == $token) {



if ($id > 0) {


	$sqlUp = "update stock_master set
				name	='" . $name . "'
				

	where id							='" . $id . "'";

	$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());



	if ($result) {
		echo 1;

	}

} else {		// Add Mode






	$sqlIn = "insert into  stock
						set
				date	='" . $date . "',
				class_id	='" . $class_id . "',
				session	='" . $session . "',
				stock_type	='" . $stock_type . "',
				stock_master_id	='" . $stock_master_id . "',
				publishers_id	='" . $publishers_id . "',

				qty	='" . $qty . "',
				mrp	='" . $mrp . "',
				total	='" . $total . "',

				comission_prectantage	='" . $comission_prectantage . "',
				comission_amount	='" . $comission_amount . "',
				purchase_value	='" . $purchase_value . "',
				purchase_value_per_unit ='" . $purchase_value_per_unit . "'";



	$result = mysqli_query($myDB, $sqlIn);


	if ($result) {

		echo 0;


	}
}
//}
?>