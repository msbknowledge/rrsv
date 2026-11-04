<?php
session_start();

// print_r($_POST);
// die;
// $token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;
$stock_coloumn_name = "";
$stock_table_name = "";
$stock_master_id = 0;
$others_income_name = "";

$class_id = 0;
$class_name = "";
$session = "";
$stock_type = "";
$stock_master_id = 0;
$income_name = "";
$income_val = "";

$income_type = $myDB->escape_string(trim(addslashes($_POST['income_type'])));
$amount = $myDB->escape_string(trim(addslashes($_POST['amount'])));
$d_o_i = $myDB->escape_string(trim(addslashes($_POST['d_o_i'])));
$income_desc = $myDB->escape_string(trim(addslashes($_POST['income_desc'])));
$session = $myDB->escape_string(trim(addslashes($_POST['session'])));

if ($income_type === "with_stock") {
	$class_id = $myDB->escape_string(trim(addslashes($_POST['class_id'])));
	
	$stock_type = $myDB->escape_string(trim(addslashes($_POST['stock_type'])));
	$stock_master_id = $myDB->escape_string(trim(addslashes($_POST['stock_master_id'])));

	if ($stock_type === "BOOK") {
		$stock_coloumn_name = "book_name";
		$stock_table_name = "rrsv_book";
	} else if ($stock_type === "COPY") {
		$stock_coloumn_name = "copy_name";
		$stock_table_name = "rrsv_copy";
	} else if ($stock_type === "OTHERS") {
		$stock_coloumn_name = "name,field_name";
		$stock_table_name = "stock_master";
	}
	$sql = "select $stock_coloumn_name from $stock_table_name where id = $stock_master_id";
	$result = mysqli_query($myDB, $sql);
	$rows = mysqli_fetch_array($result);
	if ($stock_type === "OTHERS") {
		$others_income_name = $rows["name"];
		$income_name = $rows["field_name"];
	} else {
		$income_name = $rows[$stock_coloumn_name];
	}

	if ($stock_type === "BOOK" || $stock_type === "COPY") {
		$sql = "select class_name from rrsv_class where id = $class_id";
		$result2 = mysqli_query($myDB, $sql);
		$rows = mysqli_fetch_assoc($result2);
		$class_name = $rows["class_name"];
	}
	//echo "Processing key: $income_name with value: $stock_type with -- $class_name with -- $session\n";


} else {
	$income_name = $myDB->escape_string(trim(addslashes($_POST['income_name'])));

}
//echo "income_name: $income_name with amount: $amount with d_o_i-- $d_o_i with income_desc-- $income_desc\n";


//	if(isset($tokenpost)==$token){

if ($income_type === "with_stock" && $stock_type === "OTHERS") {
	$income_val = $others_income_name;
} else {
	$income_val = $income_name;
}

$currenr = date('Y');

$sqlIn = "insert into rrsv_income 
						set
				income_name	='" . $income_val . "',
				amount ='" . $amount . "',
				d_o_i	='" . $d_o_i . "',
			
				income_desc  ='" . $income_desc . "'";



$result = mysqli_query($myDB, $sqlIn);
$lstid = $myDB->insert_id;
//	$regid=$currenr.'-'.$lstid;
$curmonth = date('m');
$curyear = date('Y');

$regid = "RRSV" . '-' . $curmonth . '-' . $curyear . '-' . $lstid;

$Inpl = "insert into rrsv_scl_pl set	pl_date='" . $d_o_i . "',income_amount='" . $amount . "',bill='" . $regid . "',purpose='" . $income_name . "',session='" . $session . "'";
$res = mysqli_query($myDB, $Inpl);


$Upsql = "update rrsv_income set from_no='" . $regid . "' where id=$lstid";
$Upres = mysqli_query($myDB, $Upsql);

if ($income_type === "with_stock") {
	include('stock_duct.php');
	stock_duct($class_name, $session, $income_name, 1, $stock_type);
}

if ($result) {

	echo 0;
}



//	}
?>