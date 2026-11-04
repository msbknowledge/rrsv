<?php
// print_r($_POST);
// die;
session_start();


// $token = $_SESSION['_token'];
include('include/dbcon.php');
$id = 0;
$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$expence_name = $myDB->escape_string(trim(addslashes($_POST['expence_name'])));
$amount = $myDB->escape_string(trim(addslashes($_POST['amount'])));
$d_o_e = $myDB->escape_string(trim(addslashes($_POST['d_o_e'])));
$session = $myDB->escape_string(trim(addslashes($_POST['session'])));
$exp_desc = $myDB->escape_string(trim(addslashes($_POST['exp_desc'])));

// echo $id;
// die;
$uploadflg = 1;
$file_base = "expence_image";
$sub_file = $file_base;
if (is_dir($sub_file) == false) {	// Make Directory
	mkdir($sub_file, 0777);
}
$store_file_name = $_FILES['image']['name'];

$filename = $sub_file . "/" . $store_file_name;

$file_flag = 0;
if ($store_file_name == "") {
	//$store_file_name=$prvfilenm;
	$uploadflg = 0;
}
$prt_image = $_FILES['image']['name'];
$image_type = $_FILES['image']['type'];
if ($uploadflg == 1) {
	if ($file_flag == 0) {
		if (move_uploaded_file($_FILES['image']['tmp_name'], $filename)) {
			if (file_exists($filename)) {

				chmod("$filename", 0777);
				$n_width = 200;          // Fix the width of the thumb nail images
				$n_height = 250;         // Fix the height of the thumb nail images

				$thumbfile_name = "thumb_" . $prt_image;

				//echo "File Type=" . $userfile_type."<br>";

				$tsrc = "expence_image/$thumbfile_name";  // Path where thumb nail image will be stored
				if ($image_type == "image/jpeg") {

					$im = ImageCreateFromJpeg($filename);
				}
				if ($image_type == "image/gif") {

					$im = ImageCreateFromGif($filename);
				}
				if ($image_type == "image/png") {

					$im = ImageCreateFromPNG($filename);
				}
				if ($image_type == "image/pjpeg") {

					$im = imagecreatefromjpeg($filename);
				}
				$old_x = ImageSx($im);              // Original picture width is stored
				$old_y = ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w = $n_width;
					$thumb_h = $old_y * ($n_height / $old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w = $old_x * ($n_width / $old_y);
					$thumb_h = $n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w = $n_width;
					$thumb_h = $n_height;
				}

				//end here

				$newimage = imagecreatetruecolor($thumb_w, $thumb_h);
				imageCopyResampled($newimage, $im, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
				if ($image_type == "image/jpeg") {

					ImageJpeg($newimage, $tsrc);
				}
				if ($image_type == "image/gif") {

					ImageGIF($newimage, $tsrc);
				}
				if ($image_type == "image/png") {

					ImagePNG($newimage, $tsrc);
				}
				if ($image_type == "image/pjpeg") {

					ImageJpeg($newimage, $tsrc);
				}
				chmod("$tsrc", 0777);

			} else {

				$Show_msg = '<script  language=JavaScript>';
				$Show_msg .= 'alert("' . " File " . $store_file_name . " Does not exist.. " . '")';
				$Show_msg .= 'window.history.go(-1);';
				$Show_msg .= '</script>';
			}


		}//move uploaded file end here 

	}	// File Flag Ends here
}	// Upload Flag



$image = $store_file_name;	//name of the file that has been uploaded
if (isset($_POST['id']) && $_POST['id'] > 0) {
	// echo "update";
	// echo $image;
	$Upsql = "update rrsv_expence set image='" . $image . "' where id=$id";
	$Upres = mysqli_query($myDB, $Upsql);
	//die;
	if ($Upres) {

		echo "<script language='JavaScript'>";
		echo "window.location.href='manage_expence.php?retcode=3'";
		echo "</script>";
	}
} else {
	//	$currenr=date('Y-m-d');

	$sqlIn = "insert into rrsv_expence 
						set
				expence_name	='" . $expence_name . "',
				amount ='" . $amount . "',
				d_o_e	='" . $d_o_e . "',
			    image  ='" . $image . "',
				exp_desc  ='" . $exp_desc . "'";



	$result = mysqli_query($myDB, $sqlIn);
	$lstid = $myDB->insert_id;
	// 	$regid=$scl_session.'-'.$class_name.'/'.$lstid;
	$regid = $lstid;
	$Inpl = "insert into rrsv_scl_pl set	pl_date='" . $d_o_e . "',exp_amount='" . $amount . "',bill='" . $regid . "',purpose='" . $expence_name . "', session='" . $session . "'";
	$res = mysqli_query($myDB, $Inpl);


	$Upsql = "update rrsv_expence set from_no='" . $regid . "' where id=$lstid";
	$Upres = mysqli_query($myDB, $Upsql);
	if ($result) {

		echo "<script language='JavaScript'>";
		echo "window.location.href='manage_expence.php?retcode=2';";
		echo "</script>";
	}

}

//	}
?>