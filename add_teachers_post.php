<?php
session_start();


$token = $_SESSION['_token'];
include('include/dbcon.php');
// print_r($_POST);
// die();
$tokenpost = $myDB->escape_string(trim(addslashes($_POST['token'])));


$id = $myDB->escape_string(trim(addslashes($_POST['id'])));
$full_name = $myDB->escape_string(trim(addslashes($_POST['full_name'])));
$fathers_name = $myDB->escape_string(trim(addslashes($_POST['fathers_name'])));
$mothers_name = $myDB->escape_string(trim(addslashes($_POST['mothers_name'])));
$gender = $myDB->escape_string(trim(addslashes($_POST['gender'])));
$d_o_b = $myDB->escape_string(trim(addslashes($_POST['d_o_b'])));
$pin = $myDB->escape_string(trim(addslashes($_POST['pin'])));

$DOJ = $myDB->escape_string(trim(addslashes($_POST['DOJ'])));
$village = $myDB->escape_string(trim(addslashes($_POST['village'])));
$post = $myDB->escape_string(trim(addslashes($_POST['post'])));

$email = $myDB->escape_string(trim(addslashes($_POST['email'])));
$designation = $myDB->escape_string(trim(addslashes($_POST['designation'])));
$qualification = $myDB->escape_string(trim(addslashes($_POST['qualification'])));
$monthly_salary = $myDB->escape_string(trim(addslashes($_POST['monthly_salary'])));
$cl = $myDB->escape_string(trim(addslashes($_POST['cl'])));
$medical = $myDB->escape_string(trim(addslashes($_POST['medical'])));
$aadhar_no = $myDB->escape_string(trim(addslashes($_POST['aadhar_no'])));
$pan_no = $myDB->escape_string(trim(addslashes($_POST['pan_no'])));
$bank_account_no = $myDB->escape_string(trim(addslashes($_POST['bank_account_no'])));
$bank_brunch_name = $myDB->escape_string(trim(addslashes($_POST['bank_brunch_name'])));
$bank_ifsc = $myDB->escape_string(trim(addslashes($_POST['bank_ifsc'])));
$tech_phone_no = $myDB->escape_string(trim(addslashes($_POST['tech_phone_no'])));

$store_file_name = "";

if (isset($tokenpost) == $token) {

	$uploadflg = 1;
	$file_base = "teacher_image";
	$sub_file = $file_base;
	if (is_dir($sub_file) == false) {	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['image']['name'];
	// 	 print_r($store_file_name);
// 	die();
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
				// 				    print_r("dddd");
//  	die();
				if (file_exists($filename)) {

					chmod("$filename", 0777);
					$n_width = 200;          // Fix the width of the thumb nail images
					$n_height = 250;         // Fix the height of the thumb nail images

					$thumbfile_name = "thumb_" . $prt_image;

					//echo "File Type=" . $userfile_type."<br>";

					$tsrc = "admission_image/$thumbfile_name";  // Path where thumb nail image will be stored
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

	// echo 11111;
// echo $store_file_name;
// die();



	if ($id > 0) {


		if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
			$sqlUp = "update rrsv_teacher  set image = '" . $store_file_name . "' where id    ='" . $id . "'";
			mysqli_query($myDB, $sqlUp);
		}
		$sqlUp = "update rrsv_teacher  set
				full_name	='" . $full_name . "',
				fathers_name	='" . $fathers_name . "',
				mothers_name	='" . $mothers_name . "',
				gender	='" . $gender . "',
				d_o_b	='" . $d_o_b . "',
				pin	='" . $pin . "',

				DOJ	='" . $DOJ . "',
				village	='" . $village . "',
				post	='" . $post . "',

				email	='" . $email . "',
				designation	='" . $designation . "',
				qualification	='" . $qualification . "',
				monthly_salary	='" . $monthly_salary . "',
				cl	='" . $cl . "',
				medical	='" . $medical . "',
				aadhar_no	='" . $aadhar_no . "',
				pan_no	='" . $pan_no . "',
				bank_account_no	='" . $bank_account_no . "',
				bank_brunch_name	='" . $bank_brunch_name . "',
				bank_ifsc	='" . $bank_ifsc . "',
				tech_phone_no   ='" . $tech_phone_no . "'
	            where id    ='" . $id . "'";

		$result = mysqli_query($myDB, $sqlUp) or die("Error into update post:" . mysqli_connect_error());

		if ($result) {
			echo "<script language='JavaScript'>";
			//echo "window.alert(1)";
			echo "window.location.href = 'manage_teacher.php?retcode=1'";
			echo "</script>";
		}

	} else {		// Add Mode

		// 	echo "insert";
// 	die();

		$strsql = "select * from rrsv_teacher where d_o_b='" . $d_o_b . "' and email='" . $email . "' and aadhar_no='" . $aadhar_no . "' and bank_account_no='" . $bank_account_no . "'";
		$result = mysqli_query($myDB, $strsql) or die("Error into selecting course info by Class name post:" . mysqli_connect_error());
		if (mysqli_num_rows($result) > 0) {
			echo 3;
			exit();
		}

		$strsql = "";

		$strsql .= "insert into rrsv_teacher(full_name,fathers_name,mothers_name,gender,d_o_b,pin,DOJ,village,post,email,qualification,monthly_salary,cl,medical,aadhar_no,pan_no,bank_account_no,bank_brunch_name,bank_ifsc,image,status,tech_phone_no)";

		$strsql .= "values('" . $full_name . "','" . $fathers_name . "','" . $mothers_name . "', '" . $gender . "','" . $d_o_b . "','" . $pin . "',  
                        '" . $DOJ . "','" . $village . "','" . $post . "','" . $email . "','" . $qualification . "', '" . $monthly_salary . "','" . $cl . "','" . $medical . "',
                         '" . $aadhar_no . "','" . $pan_no . "','" . $bank_account_no . "', '" . $bank_brunch_name . "','" . $bank_ifsc . "',
                         '" . $store_file_name . "','active','" . $tech_phone_no . "'
                    )";

		$qresult = mysqli_query($myDB, $strsql) or die("Error into inserting:" . mysql_error());



		if ($qresult) {

			if ($qresult) {
				echo "<script language='JavaScript'>";
				echo "window.location.href = 'manage_teacher.php?'";
				echo "</script>";
			}
		}


	}
}
?>