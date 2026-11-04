<?php
error_reporting(1);
include('include/dbcon.php');
/* Variable Declaration */
$stock_coloumn_name = "";
$stock_table_name = "";
echo "<pre>";
// print_r($_POST);
// die;


//die();
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$pay_id = $myDB->escape_string(trim(addslashes($_POST['pay_id'])));
$recep_no = $myDB->escape_string(trim(addslashes($_POST['recep_no'])));
$scl_roll_no = $myDB->escape_string(trim(addslashes($_POST['scl_roll_no'])));
$scl_name = $myDB->escape_string(trim(addslashes($_POST['scl_name'])));
$scl_reg_no = $myDB->escape_string(trim(addslashes($_POST['scl_reg_no'])));
$scl_session = $myDB->escape_string(trim(addslashes($_POST['scl_session'])));
$scl_section = $myDB->escape_string(trim(addslashes($_POST['scl_section'])));
$scl_class = $myDB->escape_string(trim(addslashes($_POST['scl_class'])));
$scl_date = $myDB->escape_string(trim(addslashes($_POST['scl_date'])));
$scl_month = $myDB->escape_string(trim(addslashes($_POST['scl_month'])));
$car_month = $myDB->escape_string(trim(addslashes($_POST['car_month'])));
$scl_admission = $myDB->escape_string(trim(addslashes($_POST['scl_admission'])));
$scl_instalment = $myDB->escape_string(trim(addslashes($_POST['scl_instalment'])));
$full_payment = $myDB->escape_string(trim(addslashes($_POST['full_payment'])));
$scl_net = $myDB->escape_string(trim(addslashes($_POST['scl_net'])));
$admission_due = $myDB->escape_string(trim(addslashes($_POST['admission_due'])));
$yearly_due1 = $myDB->escape_string(trim(addslashes($_POST['yearly_due1'])));
$car_due1 = $myDB->escape_string(trim(addslashes($_POST['car_due1'])));
$due_monthly_ins = $myDB->escape_string(trim(addslashes($_POST['due_monthly_ins'])));
$duemonth_car_ins = $myDB->escape_string(trim(addslashes($_POST['duemonth_car_ins'])));
$paid_month_count = $myDB->escape_string(trim(addslashes($_POST['paid_month_count'])));
$monthly_fee_val = $myDB->escape_string(trim(addslashes($_POST['monthly_fee_val'])));
$admission_val = $myDB->escape_string(trim(addslashes($_POST['admission_val'])));
$uniform_val = $myDB->escape_string(trim(addslashes($_POST['uniform_val'])));
$icard_val = $myDB->escape_string(trim(addslashes($_POST['icard_val'])));
$sweater_slax_cap_val = $myDB->escape_string(trim(addslashes($_POST['sweater_slax_cap_val'])));
$bag_val = $myDB->escape_string(trim(addslashes($_POST['bag_val'])));
$shoes_sockes_val = $myDB->escape_string(trim(addslashes($_POST['shoes_sockes_val'])));
$admission_fee_val = $myDB->escape_string(trim(addslashes($_POST['admission_fee_val'])));
$net = $myDB->escape_string(trim(addslashes($_POST['net'])));
$remining_balance = $myDB->escape_string(trim(addslashes($_POST['remining_balance'])));
$admission = $myDB->escape_string(trim(addslashes($_POST['admission'])));
$scl_month = $myDB->escape_string(trim(addslashes($_POST['scl_month'])));
$car_cost = $myDB->escape_string(trim(addslashes($_POST['car_cost'])));
$item_cost = $myDB->escape_string(trim(addslashes($_POST['item_cost'])));
$book_cost = $myDB->escape_string(trim(addslashes($_POST['book_cost'])));
$copy_cost = $myDB->escape_string(trim(addslashes($_POST['copy_cost'])));
$total_cost = $myDB->escape_string(trim(addslashes($_POST['total_cost'])));
$remining_balance = $myDB->escape_string(trim(addslashes($_POST['remining_balance'])));
$payment_recive = $myDB->escape_string(trim(addslashes($_POST['payment_recive'])));
$val_ad_du = $myDB->escape_string(trim(addslashes($_POST['val_ad_du'])));

$purpose = $myDB->escape_string(trim(addslashes($_POST['purpose'])));
$usingtype = $myDB->escape_string(trim(addslashes($_POST['usingtype'])));
$advance_due = $myDB->escape_string(trim(addslashes($_POST['advance_due'])));
$re_admission_fee_val = $myDB->escape_string(trim(addslashes($_POST['re_admission_fee_val'])));
$totalmonthly_fee_val = $myDB->escape_string(trim(addslashes($_POST['totalmonthly_fee_val'])));
$dueamount = $myDB->escape_string(trim(addslashes($_POST['dueamount'])));
$total_cost = $myDB->escape_string(trim(addslashes($_POST['total_cost'])));
$add_total_cost = $myDB->escape_string(trim(addslashes($_POST['add_total_cost'])));
$re_total_cost = $myDB->escape_string(trim(addslashes($_POST['re_total_cost'])));
$diary_val = $myDB->escape_string(trim(addslashes($_POST['diary_val'])));
$adjust_l_a_d = $myDB->escape_string(trim(addslashes($_POST['adjust_l_a_d'])));
$last_advance_due = $myDB->escape_string(trim(addslashes($_POST['last_advance_due'])));
$last_a_d_status = $myDB->escape_string(trim(addslashes($_POST['last_a_d_status'])));
$car_fee = $myDB->escape_string(trim(addslashes($_POST['car_fee'])));
$car_cost = $myDB->escape_string(trim(addslashes($_POST['car_cost'])));
$total_monthly_fee = $myDB->escape_string(trim(addslashes($_POST['total_monthly_fee'])));
$admission_cost1 = $myDB->escape_string(trim(addslashes($_POST['admission_cost1'])));
$admission_cost = $myDB->escape_string(trim(addslashes($_POST['admission_cost'])));
$total_book = $myDB->escape_string(trim(addslashes($_POST['total_book'])));
$total_copy = $myDB->escape_string(trim(addslashes($_POST['total_copy'])));
$monthly_fee = $myDB->escape_string(trim(addslashes($_POST['monthly_fee'])));
$amount1 = $myDB->escape_string(trim(addslashes($_POST['amount1'])));
$advance_due = $myDB->escape_string(trim(addslashes($_POST['advance_due'])));

// $arrcount               =count($_POST['account']['cat_id']); 
$arrcount = count($_POST['account']['copy_id']);

$book_arrcount = count($_POST['account']['cat_id']);

/* End of Variable Declaration */

/* check duplicate into tabel */
$sqlCheck = "SELECT COUNT(*) AS vall  FROM rrsv_scl_studen_fee where
            pay_id 		             ='" . $pay_id . "'
		AND 	scl_name 	             ='" . $scl_name . "'
		AND 	scl_reg_no 	             ='" . $scl_reg_no . "'
		AND 	scl_roll_no 	         ='" . $scl_roll_no . "'
		AND 	scl_month		         ='" . $scl_month . "'
     	AND 	scl_date		         ='" . $scl_date . "'
		AND 	car_fee 	             ='" . $car_fee . "'
		AND 	status_add				 ='admission'
		AND 	monthly_fee_val          ='" . $monthly_fee_val . "'
		AND 	admission_val            ='" . $admission_val . "'
		AND     uniform_val              ='" . $uniform_val . "'
		AND 	icard_val                ='" . $icard_val . "'
		AND 	sweater_slax_cap_val     ='" . $sweater_slax_cap_val . "'
		AND 	bag_val                  ='" . $bag_val . "'
	    AND 	shoes_sockes_val         ='" . $shoes_sockes_val . "'
        AND 	admission_fee_val        ='" . $admission_fee_val . "'
        AND 	net                      ='" . $net . "' 
        AND 	item_cost                ='" . $item_cost . "'
        AND 	book_cost                ='" . $book_cost . "'
        AND 	copy_cost                ='" . $copy_cost . "'
        AND 	total_cost               ='" . $total_cost . "'
        AND 	payment_recive           ='" . $payment_recive . "' 
        AND 	val_ad_du                ='" . $val_ad_du . "' 
        AND 	remining_balance         ='" . $remining_balance . "'
        AND 	advance_due              ='" . $advance_due . "'
        AND 	usingtype                ='" . $usingtype . "'
        AND 	re_admission_fee_val     ='" . $re_admission_fee_val . "'
        AND 	scl_session              ='" . $scl_session . "'
        AND 	scl_class                ='" . $scl_class . "'
        AND 	diary_val                ='" . $diary_val . "'
        AND 	adjust_l_a_d             ='" . $adjust_l_a_d . "'
        AND 	last_advance_due        ='" . $last_advance_due . "'
        AND 	last_a_d_status            ='" . $last_a_d_status . "'
        AND 	car_month                  ='" . $car_month . "' 
        AND 	purpose                  ='" . $purpose . "'";
// $sqlCheck="SELECT COUNT(*) AS vall FROM rrsv_scl_studen_fee where pay_id ='239' AND scl_name ='' AND scl_reg_no ='2021-A-17' AND scl_roll_no ='' AND scl_month ='0' AND scl_date ='2023-02-05' AND car_fee ='200' AND status_add ='admission' AND monthly_fee_val ='' AND admission_val ='150' AND uniform_val ='' AND icard_val ='' AND sweater_slax_cap_val ='' AND bag_val ='' AND shoes_sockes_val ='6000' AND admission_fee_val ='' AND net ='' AND item_cost ='6200' AND book_cost ='0' AND copy_cost ='0' AND total_cost ='6200' AND payment_recive ='3456' AND val_ad_du ='Due' AND remining_balance ='' AND advance_due ='2744' AND usingtype ='cash2' AND re_admission_fee_val ='' AND scl_session ='2023' AND scl_class ='STANDERD 4' AND diary_val ='' AND adjust_l_a_d ='' AND last_advance_due ='0' AND last_a_d_status ='Due' AND car_month ='May' AND purpose ='for bag'";

$sqlCheckQry = mysqli_query($myDB, $sqlCheck);
$sqlCheckQryCount = mysqli_fetch_array($sqlCheckQry);
$sqlCheckQryCount = $sqlCheckQryCount['vall'];
print_r($sqlCheckQryCount);
if ($sqlCheckQryCount == 0) {
	//   echo "no record found";

	// die();
	/* Data insert into tabel */
	$sqlIn = "insert into rrsv_scl_studen_fee	set
            pay_id 		             ='" . $pay_id . "',
			scl_name 	             ='" . $scl_name . "',
			scl_reg_no 	             ='" . $scl_reg_no . "',
			scl_roll_no 	         ='" . $scl_roll_no . "',
			scl_month		         ='" . $scl_month . "',
     		scl_date		         ='" . $scl_date . "',
			car_fee 	             ='" . $car_fee . "',
			status_add				 ='admission',
			monthly_fee_val          ='" . $monthly_fee_val . "',
			admission_val            ='" . $admission_val . "',
		    uniform_val              ='" . $uniform_val . "',
			icard_val                ='" . $icard_val . "',
			sweater_slax_cap_val     ='" . $sweater_slax_cap_val . "',
			bag_val                  ='" . $bag_val . "',
	    	shoes_sockes_val         ='" . $shoes_sockes_val . "',
        	admission_fee_val        ='" . $admission_fee_val . "',
        	net                      ='" . $net . "', 
        	item_cost                ='" . $item_cost . "',
        	book_cost                ='" . $book_cost . "',
        	copy_cost                ='" . $copy_cost . "',
        	total_cost               ='" . $total_cost . "',
        	payment_recive           ='" . $payment_recive . "', 
        	val_ad_du                ='" . $val_ad_du . "', 
        	remining_balance         ='" . $remining_balance . "',
        	advance_due              ='" . $advance_due . "',
        	usingtype                ='" . $usingtype . "',
        	re_admission_fee_val     ='" . $re_admission_fee_val . "',
        	scl_session              ='" . $scl_session . "',
        	scl_class                ='" . $scl_class . "',
        	diary_val                ='" . $diary_val . "',
        	adjust_l_a_d             ='" . $adjust_l_a_d . "',
        	last_advance_due        ='" . $last_advance_due . "',
        	last_a_d_status            ='" . $last_a_d_status . "',
        	car_month                  ='" . $car_month . "', 
        	purpose                  ='" . $purpose . "'";


	$result = mysqli_query($myDB, $sqlIn);
	$lstid = $myDB->insert_id;
	// $curmonth=date('m');
	// $curyear=date('Y');
	// $recepno="RRSV".'-'.$curmonth.'-'.$curyear.'-'.$lstid;

	for ($i = 0; $i < $book_arrcount; $i++) {
		$sql = "insert into rrsv_book_cost(cost_id,scl_reg_no,scl_session,scl_class,book_name,rate)values('$lstid','$scl_reg_no','$scl_session','$scl_class','" . $_POST['account'][cat_id][$i] . "','" . $_POST['account'][rate][$i] . "') ";
		$res = mysqli_query($myDB, $sql);
	}

	for ($i = 0; $i < $arrcount; $i++) {
		$sql = "insert into rrsv_copy_cost(cost_id,copy_name,qty,rate,total)values('$lstid','" . $_POST['account'][copy_id][$i] . "','" . $_POST['account'][qty][$i] . "','" . $_POST['account'][price][$i] . "','" . $_POST['account'][total][$i] . "') ";
		$res = mysqli_query($myDB, $sql);
	}

	$Upreg = "update rrsv_scl_studen_fee set recep_no='" . $lstid . "' where id='" . $lstid . "'";
	$Upres = mysqli_query($myDB, $Upreg);


	$sql = "select * from rrsv_scl_studen_fee where pay_id=$pay_id";
	$res = mysqli_query($myDB, $sql);
	while ($rows = mysqli_fetch_array($res)) {
		$admission_fee_val1 = $admission_fee_val1 + $rows['admission_fee_val'];
		$re_admission_fee_val1 = $re_admission_fee_val1 + $rows['re_admission_fee_val'];
		$admissionval = $rows['admission_val'];
		//  echo $add_total_cost        =$rows['add_total_cost'];
		//  $re_total_cost         =$rows['re_total_cost'];
		$shoessockesval = $shoessockesval + $rows['shoes_sockes_val'];
		$bookcost = $bookcost + $rows['book_cost'];
		$copycost = $copycost + $rows['copy_cost'];
		$paymentrecive = $paymentrecive + $rows['payment_recive'];
		$diaryval = $diaryval + $rows['diary_val'];
		$bagval = $bagval + $rows['bag_val'];
		$sweaterslaxcap_val = $sweaterslaxcap_val + $rows['sweater_slax_cap_val'];
		$icardval = $icardval + $rows['icard_val'];
		$uniformval = $uniformval + $rows['uniform_val'];
		$totalmonthly_fee_val = $totalmonthly_fee_val + $rows['monthly_fee_val'];
		$carfee = $carfee + $rows['car_fee'];
		// $advance_due           =$advance_due+$rows['advance_due'];
	}




	$sql = "update rrsv_student_registration set admission_fee_val='" . $admission_fee_val1 . "',re_admission_fee_val='" . $re_admission_fee_val1 . "',add_total_cost='" . $add_total_cost . "',re_total_cost='" . $re_total_cost . "',admissionval='" . $admissionval . "', diaryval='" . $diaryval . "', bagval='" . $bagval . "',sweaterslaxcap_val='" . $sweaterslaxcap_val . "',icardval='" . $icardval . "', uniformval='" . $uniformval . "',totalmonthly_fee_val='" . $totalmonthly_fee_val . "',bookcost='" . $bookcost . "' ,copycost='" . $copycost . "',paymentrecive='" . $paymentrecive . "',shoessockesval='" . $shoessockesval . "',carfee='" . $carfee . "',car_cost='" . $car_cost . "',total_monthly_fee='" . $total_monthly_fee . "',admission_cost1='" . $admission_cost1 . "',admission_cost='" . $admission_cost . "',total_book='" . $total_book . "',total_copy='" . $total_copy . "',monthly_fee='" . $monthly_fee . "',amount1='" . $amount1 . "',advance_due='" . $advance_due . "',val_ad_du='" . $val_ad_du . "'   where id='" . $pay_id . "'";
	$rr = mysqli_query($myDB, $sql);

	//	 $Inpl="insert into rrsv_scl_pl set	pl_date='".$scl_date."',income_amount='".$payment_recive."',purpose='".$purpose."'";
	$Inpl = "insert into rrsv_scl_pl set	pl_date='" . $scl_date . "',income_amount='" . $payment_recive . "',bill='" . $lstid . "',session='" . $scl_session . "',purpose='Student Bill Paid'";

	$res = mysqli_query($myDB, $Inpl);



	//////////////////////////////////////////////////////////////
	//////////// stock deduction ////////////////////////////////
	$stockCheckingArray = ["diary_val", "uniform_val", "icard_val", "sweater_slax_cap_val", "bag_val", "shoes_sockes_val"];
	function processKeyWithValue($billId, $scl_class, $session, $stock_name, $qty, $stock_type)
	{
		include('include/dbcon.php');
		//echo "Processing key: $stock_name with value: $stock_type with -- $scl_class with -- $session\n";
		$sql = "select a.* from stock as a left join";

		if ($stock_type === "BOOK") {
			$stock_coloumn_name = "book_name";
			$stock_table_name = "rrsv_book";
		} else if ($stock_type === "COPY") {
			$stock_coloumn_name = "copy_name";
			$stock_table_name = "rrsv_copy";
		} else if ($stock_type === "OTHERS") {
			$stock_coloumn_name = "field_name";
			$stock_table_name = "stock_master";
		}

		$sql .= " $stock_table_name b on a.stock_master_id=b.id where a.session = '" . $session . "' and b.$stock_coloumn_name = '" . $stock_name . "'";

		if ($stock_type === "BOOK" || $stock_type === "COPY") {

			$sql .= " and b.scl_class = '" . $scl_class . "'";
		}

		$sql .= " and a.activity_status = '1'";

		//echo "$sql\n";
		$t = mysqli_query($myDB, $sql);
		//print_r(mysqli_fetch_assoc($result));
		$oldValue = mysqli_fetch_assoc($t);

		if (mysqli_num_rows($t) != 0) {
			//print_r($oldValue['id']);
			// 	print_r(mysqli_fetch_array($t));
			//echo 77 . "\n";
			$updated_sale = $oldValue["sale"] + (int) $qty;
			$updated_stock_after_sale = $oldValue["qty"] - $updated_sale;
			$updated_price = $oldValue["price"] + $oldValue["purchase_value_per_unit"];
			//echo "updated_sale: $updated_sale with updated_stock_after_sale: $updated_stock_after_sale with updated_price -- $updated_price\n";
			$activity_status = '1';
			if ($updated_stock_after_sale == 0) {
				$activity_status = '0';
			}
			$sqlUp = "update stock set
					sale	='" . $updated_sale . "',
					stock_after_sale	='" . $updated_stock_after_sale . "',
					activity_status	='" . $activity_status . "',
					price	='" . $updated_price . "'
					where id							='" . $oldValue["id"] . "'";

			$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());

			$stock_id = $oldValue["id"];

			$sqlBillMapingStock = "insert into rrsv_bill_maping_stock	set
			bill_id 		             = $billId,
			stock_id 	             =  $stock_id,
			quantity 	             = $qty";
			$re = mysqli_query($myDB, $sqlBillMapingStock);

		}

	}

	foreach ($_POST as $key => $value) {
		if (in_array($key, $stockCheckingArray)) {
			if ($value != "") {
				processKeyWithValue($lstid, trim($_POST['scl_class']), trim($_POST['scl_session']), $key, 1, "OTHERS");
			}
		}
	}

	if (isset($_POST["account"]["cat_id"]) && is_array($_POST["account"]["cat_id"]) && count($_POST["account"]["cat_id"]) > 0) {
		$book_arr = $_POST["account"]["cat_id"];
		foreach ($book_arr as $value) {
			if ($value != "") {
				processKeyWithValue($lstid, trim($_POST['scl_class']), trim($_POST['scl_session']), trim($value), 1, "BOOK");
			}
		}
	}

	if (isset($_POST["account"]["copy_id"]) && is_array($_POST["account"]["copy_id"]) && count($_POST["account"]["copy_id"]) > 0) {
		$copy_arr_count = count($_POST["account"]["copy_id"]);
		for ($i = 0; $i < $copy_arr_count; $i++) {
			if ($_POST["account"]["copy_id"][$i] != "") {
				processKeyWithValue($lstid, trim($_POST['scl_class']), trim($_POST['scl_session']), trim($_POST["account"]["copy_id"][$i]), trim($_POST["account"]["qty"][$i]), "COPY");
			}
		}
	}
	//////////////////////////////////////////////////////////////
	//////////// stock deduction ////////////////////////////////

	if ($result) {
		echo '<script language="javascript" type="text/javascript">';
		echo 'window.location.href="add_bill.php?id=' . $id . '";';
		echo '</script>';
	}

} else {
	echo '<script language="javascript" type="text/javascript">';
	echo 'alert("Trying to Duplicate record entry")';
	echo '</script>';
}
// die();

?>