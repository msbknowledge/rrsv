<?php
//print_r($_POST['recep_no']);
if ($_POST['password'] === "@#$5A&*Cr!F") {

	//echo $_POST['password'];

	include('include/dbcon.php');
	$d_id = (int) $_POST['recep_no']; // 1464;
// echo $d_id;
// die();
// select the row in the table rrsv_scl_studen_fee using d_id 

	// select the row from the table rrsv_student_registration using student id

	// then create a data ----- rrsv_student_registration data - rrsv_scl_studen_fee data

	// then update the data in rrsv_student_registration table

	// delete   rrsv_book_cost where d_id
// delete   rrsv_copy_cost where d_id

	// then delete the row from rrsv_scl_studen_fee using d_id

	// delete   rrsv_scl_pl where d_id

	// recep_no

	$sql_1 = "SELECT * FROM rrsv_scl_studen_fee where recep_no=" . $d_id . " limit 1";
	$sqlCheckQry_1 = mysqli_query($myDB, $sql_1);
	$sqlCheckQry_1 = mysqli_fetch_array($sqlCheckQry_1);
	print_r($sqlCheckQry_1);
	$pay_id = $sqlCheckQry_1['pay_id'];

	$sql_2 = "SELECT * FROM rrsv_student_registration where id=" . $pay_id . " limit 1";
	$sqlCheckQry_2 = mysqli_query($myDB, $sql_2);
	$sqlCheckQry_2 = mysqli_fetch_array($sqlCheckQry_2);


	echo "?????????????????????????????";
	echo "////////////////////////////";
	print_r($sqlCheckQry_2);

	echo $admission_fee_val1 = $sqlCheckQry_2['admission_fee_val'] - $sqlCheckQry_1['admission_fee_val'];
	echo "//////////";
	echo $re_admission_fee_val1 = $sqlCheckQry_2['re_admission_fee_val'] - $sqlCheckQry_1['re_admission_fee_val'];
	echo "//////////";
	echo $admissionval = $sqlCheckQry_2['admissionval'];
	echo "//////////";
	echo $shoessockesval = $sqlCheckQry_2['shoessockesval'] - $sqlCheckQry_1['shoes_sockes_val'];
	echo "//////////";
	echo $bookcost = $sqlCheckQry_2['bookcost'] - $sqlCheckQry_1['book_cost'];
	echo "//////////";
	echo $copycost = $sqlCheckQry_2['copycost'] - $sqlCheckQry_1['copy_cost'];
	echo "//////////";
	echo $paymentrecive = $sqlCheckQry_2['paymentrecive'] - $sqlCheckQry_1['payment_recive'];
	echo "//////////";
	echo $diaryval = $sqlCheckQry_2['diaryval'] - $sqlCheckQry_1['diary_val'];
	echo "//////////";
	echo $bagval = $sqlCheckQry_2['bagval'] - $sqlCheckQry_1['bag_val'];
	echo "//////////";
	echo $sweaterslaxcap_val = $sqlCheckQry_2['sweaterslaxcap_val'] - $sqlCheckQry_1['sweater_slax_cap_val'];
	echo "//////////";
	echo $icardval = $sqlCheckQry_2['icardval'] - $sqlCheckQry_1['icard_val'];
	echo "//////////";
	echo $uniformval = $sqlCheckQry_2['uniformval'] - $sqlCheckQry_1['uniform_val'];
	echo "//////////";
	echo $totalmonthly_fee_val = $sqlCheckQry_2['totalmonthly_fee_val'] - $sqlCheckQry_1['monthly_fee_val'];
	echo "//////////";
	echo $carfee = $sqlCheckQry_2['carfee'] - $sqlCheckQry_1['car_fee'];
	echo "//////////";
	echo $advance_due = $sqlCheckQry_2['advance_due'] - $sqlCheckQry_1['$advance_due'];

	$sql_3 = "update rrsv_student_registration set admission_fee_val='" . $admission_fee_val1 . "',re_admission_fee_val='" . $re_admission_fee_val1 . "',add_total_cost='" . $add_total_cost . "',re_total_cost='" . $re_total_cost . "',admissionval='" . $admissionval . "', diaryval='" . $diaryval . "', bagval='" . $bagval . "',sweaterslaxcap_val='" . $sweaterslaxcap_val . "',icardval='" . $icardval . "', uniformval='" . $uniformval . "',totalmonthly_fee_val='" . $totalmonthly_fee_val . "',bookcost='" . $bookcost . "' ,copycost='" . $copycost . "',paymentrecive='" . $paymentrecive . "',shoessockesval='" . $shoessockesval . "',carfee='" . $carfee . "',car_cost='" . $car_cost . "',total_monthly_fee='" . $total_monthly_fee . "',admission_cost1='" . $admission_cost1 . "',admission_cost='" . $admission_cost . "',total_book='" . $total_book . "',total_copy='" . $total_copy . "',monthly_fee='" . $monthly_fee . "', advance_due='" . $advance_due . "',val_ad_du='" . $val_ad_du . "'   where id='" . $pay_id . "'";
	$sqlCheckQry_3 = mysqli_query($myDB, $sql_3);

	if ($sqlCheckQry_3) {
		echo $sql_4 = "delete from rrsv_scl_pl where bill=" . $d_id;
		$sqlCheckQry_4 = mysqli_query($myDB, $sql_4);

		if ($sqlCheckQry_4) {
			$sql_5 = "delete from rrsv_book_cost where cost_id=" . $d_id;
			$sqlCheckQry_5 = mysqli_query($myDB, $sql_5);

			if ($sqlCheckQry_5) {
				$sql_6 = "delete from rrsv_copy_cost where cost_id=" . $d_id;
				$sqlCheckQry_6 = mysqli_query($myDB, $sql_6);

				if ($sqlCheckQry_6) {
					$sql_7 = "delete from rrsv_scl_studen_fee where id=" . $d_id;
					$sqlCheckQry_7 = mysqli_query($myDB, $sql_7);

					if ($sqlCheckQry_7) { ?>
						<script>
							window.alert("bill delete successfully");
							window.history.go(-2);
						</script>;
					<?php } else {
						echo "error in sqlCheckQry_7";
						die();
					}
				} else {
					echo "error in sqlCheckQry_6";
					die();
				}
			} else {
				echo "error in sqlCheckQry_5";
				die();
			}
		} else {
			echo "error in sqlCheckQry_4";
			die();
		}
		////////////--delete from stock--/////////////
		$sql_stock_maping = "SELECT * FROM rrsv_bill_maping_stock where bill_id=$d_id";
		$sqlCheckQry_stock_maping = mysqli_query($myDB, $sql_stock_maping);
		while ($res = mysqli_fetch_array($sqlCheckQry_stock_maping, MYSQLI_ASSOC)) {

			$stock_id = $res["stock_id"];
			$sqlBill = "select * from stock where id=$stock_id";
			$sqlCheckQryBill = mysqli_query($myDB, $sqlBill);
			$oldValue = mysqli_fetch_array($sqlCheckQryBill);
			// echo "*****old values****";
			// print_r($oldValue);
			// echo "%%%%" . "<br>";quantity
			// echo "*****old values****" . "<br>";
			// echo "quantity=> " . $res["quantity"] . "sale=> " . $oldValue["sale"] . " ----- stock_after_sale=> " . $oldValue["qty"] . " ----- price=> " . $oldValue["price"];
			// echo "*****old values****" . "<br>";
			$updated_sale = $oldValue["sale"] - (int) $res["quantity"];
			$updated_stock_before_sale = $oldValue["stock_after_sale"] + (int) $res["quantity"];
			$updated_price = $oldValue["price"] - ($oldValue["purchase_value_per_unit"] * (int) $res["quantity"]);
			$activity_status = $oldValue["activity_status"];
			if ($activity_status == '0') {
				$activity_status = '1';
			}
			// echo "*****updated values****" . "<br>";
			// echo $activity_status . "quantity=> " . $res["quantity"] . "sale=> " . $updated_sale . " ----- stock_after_sale=> " . $updated_stock_before_sale . " ----- price=> " . $updated_price;
			// echo "*****updated values****" . "<br>";

			$sqlUp = "update stock set
					sale	='" . $updated_sale . "',
					stock_after_sale	='" . $updated_stock_before_sale . "',
					activity_status	='" . $activity_status . "',
					price	='" . $updated_price . "'
					where id							='" . $oldValue["id"] . "'";

			$result = mysqli_query($myDB, $sqlUp) or die("Error into delete stock tabie:" . mysqli_connect_error());


		}
		////////////--delete from stock--/////////////
	} else {
		echo "error in update rrsv_student_registration";
		die();
	}

} else {
	echo "password did not match.";
	?>
	<script>
		window.alert("password did not match.");
		window.history.back();
	</script>
<?php }
?>