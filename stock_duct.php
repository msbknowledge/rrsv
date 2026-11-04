<?php

//////////////////////////////////////////////////////////////
//////////// stock deduction ////////////////////////////////
function stock_duct($scl_class, $session, $stock_name, $qty, $stock_type)
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
    // print_r($oldValue);
    // die;
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

    }

}


//////////////////////////////////////////////////////////////
//////////// stock deduction ////////////////////////////////


?>