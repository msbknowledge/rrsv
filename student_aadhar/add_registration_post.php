<?php 
error_reporting(1);
	include('include/dbcon.php');

	
	$id 	 							=$myDB->escape_string(trim(addslashes($_POST['id'])));
	$scl_roll_no 	 					=$myDB->escape_string(trim(addslashes($_POST['scl_roll_no'])));
	$scl_reg_no 	 					=$myDB->escape_string(trim(addslashes($_POST['scl_reg_no'])));
	$scl_name 							=$myDB->escape_string(trim(addslashes($_POST['scl_name'])));
	$scl_father_name 					=$myDB->escape_string(trim(addslashes($_POST['scl_father_name'])));
	$scl_father_quli 					=$myDB->escape_string(trim(addslashes($_POST['scl_father_quli'])));
	$scl_father_occu 					=$myDB->escape_string(trim(addslashes($_POST['scl_father_occu'])));
	$scl_father_inc 					=$myDB->escape_string(trim(addslashes($_POST['scl_father_inc'])));
	$scl_mother_name 					=$myDB->escape_string(trim(addslashes($_POST['scl_mother_name'])));
	$scl_mother_quli  					=$myDB->escape_string(trim(addslashes($_POST['scl_mother_quli'])));
	$scl_mother_occu 					=$myDB->escape_string(trim(addslashes($_POST['scl_mother_occu'])));
	$scl_mother_inc 					=$myDB->escape_string(trim(addslashes($_POST['scl_mother_inc'])));
	$scl_dob							=$myDB->escape_string(trim(addslashes($_POST['scl_dob'])));
	$scl_date							=$myDB->escape_string(trim(addslashes($_POST['scl_date'])));
	$scl_address 						=$myDB->escape_string(trim(addslashes($_POST['scl_address'])));
	$scl_distic 						=$myDB->escape_string(trim(addslashes($_POST['scl_distic'])));
	$scl_pin 							=$myDB->escape_string(trim(addslashes($_POST['scl_pin'])));
	$scl_phone_no 						=$myDB->escape_string(trim(addslashes($_POST['scl_phone_no'])));
	$scl_religion 						=$myDB->escape_string(trim(addslashes($_POST['scl_religion'])));
	$scl_gender							=$myDB->escape_string(trim(addslashes($_POST['scl_gender'])));
	$scl_ps								=$myDB->escape_string(trim(addslashes($_POST['scl_ps'])));
	$scl_po								=$myDB->escape_string(trim(addslashes($_POST['scl_po'])));
	$scl_car							=$myDB->escape_string(trim(addslashes($_POST['scl_car'])));
	$bank_name							=$myDB->escape_string(trim(addslashes($_POST['bank_name'])));
	$branch_name						=$myDB->escape_string(trim(addslashes($_POST['branch_name'])));
	$bank_ac_no							=$myDB->escape_string(trim(addslashes($_POST['bank_ac_no'])));
	$bank_ifsc_code						=$myDB->escape_string(trim(addslashes($_POST['bank_ifsc_code'])));
	$scl_class							=$myDB->escape_string(trim(addslashes($_POST['scl_class'])));
	$scl_session						=$myDB->escape_string(trim(addslashes($_POST['scl_session'])));
	$scl_section						=$myDB->escape_string(trim(addslashes($_POST['scl_section'])));
	$scl_nationality					=$myDB->escape_string(trim(addslashes($_POST['scl_nationality'])));
	$scl_category						=$myDB->escape_string(trim(addslashes($_POST['scl_category'])));
	$scl_pre_name						=$myDB->escape_string(trim(addslashes($_POST['scl_pre_name'])));
	$scl_pre_class						=$myDB->escape_string(trim(addslashes($_POST['scl_pre_class'])));
	$scl_tcn							=$myDB->escape_string(trim(addslashes($_POST['scl_tcn'])));
	$scl_tc_date						=$myDB->escape_string(trim(addslashes($_POST['scl_tc_date'])));
	$scl_blood					     	=$myDB->escape_string(trim(addslashes($_POST['scl_blood'])));
	$scl_dist					    	=$myDB->escape_string(trim(addslashes($_POST['scl_dist'])));
	$scl_po						        =$myDB->escape_string(trim(addslashes($_POST['scl_po'])));
	$scl_aadhar						    =$myDB->escape_string(trim(addslashes($_POST['scl_aadhar'])));
	$scl_location   			    	=$myDB->escape_string(trim(addslashes($_POST['scl_location'])));
	$scl_block      			    	=$myDB->escape_string(trim(addslashes($_POST['scl_block'])));
	$scl_email      			    	=$myDB->escape_string(trim(addslashes($_POST['scl_email'])));
	$scl_bpl        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_bpl'])));	
	$scl_disa       			    	=$myDB->escape_string(trim(addslashes($_POST['scl_disa'])));
	$scl_language   			    	=$myDB->escape_string(trim(addslashes($_POST['scl_language'])));	
	$scl_ide        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_ide'])));	
	$scl_pos        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_pos'])));	
	$scl_pol        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_pol'])));	
	$scl_dest        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_dest'])));	
	$alt_phone        			    	=$myDB->escape_string(trim(addslashes($_POST['alt_phone'])));	
	$scl_mu        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_mu'])));	
	$scl_state        			    	=$myDB->escape_string(trim(addslashes($_POST['scl_state'])));
	$add_status        			    	=$myDB->escape_string(trim(addslashes($_POST['add_status'])));
	$amount1        			    	=$myDB->escape_string(trim(addslashes($_POST['amount1'])));
	$reg1=0;
	$reg=0;
    
    $scl_name=strtoupper($scl_name);

$sql1="select monthly_fee,monthly_admission_fee,yearly_car_fee,yearly_fee from scl_fee where scl_class='$scl_class'";
	$res1=mysqli_query($myDB,$sql1);
	$row=mysqli_fetch_array($res1);           
	$monthly_admission_fee = $row['monthly_admission_fee'];
	
	
/*/	$sql2="select scl_install from class where scl_class='$scl_class'";
	$res2=mysqli_query($myDB,$sql2);
	$row1=mysqli_fetch_array($res2); 
	$scl_install=$row1['scl_install'] */
	
	/*=================To pick up Updated Date======================*/
	$date = date('m');
	if($date==11 || $date==12)
	{
		$nextday=strftime("%Y-12-31", strtotime("y +1 year"));
	}
	else
	{
		$nextday=date("%Y-12-31", strtotime(y));
	}


$image=$_FILES['image']['name'];
    $uploadflg=1;
	$file_base = "student_reg_image";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['image']['name'];
		$image_type=$_FILES['image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="student_reg_image/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="image/pjpeg"){

                $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag
$image=$store_file_name;	//n
$dob_image=$_FILES['dob_image']['name'];
    $uploadflg=1;
	$file_base = "student_dob";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['dob_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['dob_image']['name'];
		$image_type=$_FILES['dob_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['dob_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$dob_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="student_dob/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="dob_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="dob_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="dob_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="dob_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="dob_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="dob_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="dob_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="dob_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag

$dob_image=$store_file_name;

$aadhar_image=$_FILES['aadhar_image']['name'];
    $uploadflg=1;
	$file_base = "student_aadhar";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['aadhar_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['aadhar_image']['name'];
		$image_type=$_FILES['aadhar_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['aadhar_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$aadhar_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="student_aadhar/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="aadhar_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="aadhar_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="aadhar_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="aadhar_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="aadhar_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="aadhar_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="aadhar_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="aadhar_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag

$aadhar_image=$store_file_name;


$father_aadhar_image=$_FILES['father_aadhar_image']['name'];
    $uploadflg=1;
	$file_base = "father_aadhar_image";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['father_aadhar_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['father_aadhar_image']['name'];
		$image_type=$_FILES['father_aadhar_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['father_aadhar_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$father_aadhar_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="father_aadhar_image/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="father_aadhar_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="father_aadhar_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="father_aadhar_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="father_aadhar_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="father_aadhar_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="father_aadhar_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="father_aadhar_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="father_aadhar_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag

$father_aadhar_image=$store_file_name;
$father_voter_image=$_FILES['father_voter_image']['name'];
    $uploadflg=1;
	$file_base = "father_voter_image";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['father_voter_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['father_voter_image']['name'];
		$image_type=$_FILES['father_voter_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['father_voter_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$father_voter_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="father_voter_image/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="father_voter_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="father_voter_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="father_voter_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="father_voter_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="father_voter_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="father_voter_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="father_voter_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="father_voter_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag

$father_voter_image=$store_file_name;
$mother_voter_image=$_FILES['mother_voter_image']['name'];
    $uploadflg=1;
	$file_base = "mother_voter_image";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['mother_voter_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['mother_voter_image']['name'];
		$image_type=$_FILES['mother_voter_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['mother_voter_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$dob_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="mother_voter_image/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="mother_voter_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="mother_voter_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="mother_voter_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="mother_voter_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="mother_voter_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="mother_voter_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="mother_voter_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="mother_voter_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag

$mother_voter_image=$store_file_name;
$mother_aadhar_image=$_FILES['mother_aadhar_image']['name'];
    $uploadflg=1;
	$file_base = "mother_aadhar_image";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['mother_aadhar_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['mother_aadhar_image']['name'];
		$image_type=$_FILES['mother_aadhar_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['mother_aadhar_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$mother_aadhar_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="mother_aadhar_image/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="mother_aadhar_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="mother_aadhar_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="mother_aadhar_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="mother_aadhar_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="mother_aadhar_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="mother_aadhar_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="mother_aadhar_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="mother_aadhar_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag

$mother_aadhar_image=$store_file_name;
$passbook_image=$_FILES['passbook_image']['name'];
    $uploadflg=1;
	$file_base = "passbook_image";
    $sub_file =  $file_base;
if(is_dir($sub_file) == false ){	// Make Directory
		mkdir($sub_file, 0777);
	}
	$store_file_name = $_FILES['passbook_image']['name'];
	
	 $filename = $sub_file . "/" . $store_file_name;	
	
	$file_flag = 0;
	if ($store_file_name==""){
		//$store_file_name=$prvfilenm;
		$uploadflg=0;
	}
	 $prt_image=$_FILES['passbook_image']['name'];
		$image_type=$_FILES['passbook_image']['type'];
		if ($uploadflg==1){
			if($file_flag == 0){
				if( move_uploaded_file($_FILES['passbook_image']['tmp_name'], $filename)){		
		if(file_exists($filename)){		 
				
						chmod("$filename",0777);
							$n_width=200;          // Fix the width of the thumb nail images
				$n_height=250;         // Fix the height of the thumb nail images

				$thumbfile_name="thumb_".$passbook_image;

				//echo "File Type=" . $userfile_type."<br>";
					   
					$tsrc="passbook_image/$thumbfile_name";  // Path where thumb nail image will be stored
					if($image_type =="passbook_image/jpeg"){
					
				$im=ImageCreateFromJpeg($filename); 
				}
				if($image_type =="passbook_image/gif"){
					
				$im=ImageCreateFromGif($filename); 
				}
				if($image_type =="passbook_image/png"){
					
				$im=ImageCreateFromPNG($filename); 
				}
			    if($image_type =="passbook_image/pjpeg"){

               $im=imagecreatefromjpeg($filename);
                }
				$old_x=ImageSx($im);              // Original picture width is stored
				$old_y=ImageSy($im);             // Original picture height is stored
				//echo $old_x;

				//$old_x=imageSX($src_img);
				//$old_y=imageSY($src_img);

				if ($old_x > $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$old_y*($n_height/$old_x);
				}
				if ($old_x < $old_y) {
					$thumb_w=$old_x*($n_width/$old_y);
					$thumb_h=$n_height;
				}
				if ($old_x == $old_y) {
					$thumb_w=$n_width;
					$thumb_h=$n_height;
				}

				//end here

				$newimage=imagecreatetruecolor($thumb_w,$thumb_h);                 
				imageCopyResampled($newimage,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
				if($image_type =="passbook_image/jpeg"){
					
					ImageJpeg($newimage,$tsrc);
				}
				if($image_type =="passbook_image/gif"){
					
					ImageGIF($newimage,$tsrc);
				}
				if($image_type =="passbook_image/png"){
					
					ImagePNG($newimage,$tsrc);
				}
				if($image_type =="passbook_image/pjpeg"){

               ImageJpeg($newimage,$tsrc);
               }
				chmod("$tsrc",0777);
				
			}
					else{

						$Show_msg = '<script  language=JavaScript>';
						$Show_msg .= 'alert("'  .   " File "  .  $store_file_name .  " Does not exist.. " . '")';
						$Show_msg .= 'window.history.go(-1);';
						$Show_msg .= '</script>';
					}

											
				}//move uploaded file end here 

			}	// File Flag Ends here
	}	// Upload Flag
$passbook_image=$store_file_name;
	if($id > 0) {
		/*	$strsql="select * from rrsv_student_registration where  id='".$id."'";
		//$strsql="select * from rrsv_student_registration where scl_name='".$scl_name."' and id<>'".$id."' and scl_class='".$scl_class."' and scl_session='".$scl_session."' ";
		$result=mysqli_query($myDB,$strsql) or die(" Error into selecting manage_admission info by name and id post:".mysqli_connect_error());
		if(mysqli_num_rows($result) > 0) {
		
			$errcode=2;		//Duplicate check
			echo '<script language="javascript" type="text/javascript">';
			echo 'alert("student name you have entered is duplicate!.");';
			echo 'window.history.go(-1);';
			echo '</script>';
			exit();
		}*/
		$strsql="select * from rrsv_student_registration where  scl_class='".$scl_class."' and scl_session='".$scl_session."' and id='".$id."' ";
		$result=mysqli_query($myDB,$strsql) or die(" Error into selecting manage_admission info by name and id post:".mysqli_connect_error());
	//	$rows=mysqli_fetch_array($result);
		
		if(mysqli_num_rows($result) > 0)
//	if($rows['scl_class']=='".$scl_class."' and $rows['scl_session']=='".$scl_session."')
		{
			$strsql="Update rrsv_student_registration set";
			$strsql.=" scl_name					='".$scl_name."',";
			$strsql.=" scl_roll_no				='".$scl_roll_no."',";
			$strsql.=" scl_father_name	 	    ='".$scl_father_name."',";
			$strsql.=" scl_father_quli	 	    ='".$scl_father_quli."',";
			$strsql.=" scl_father_occu	 	    ='".$scl_father_occu."',";
			$strsql.=" scl_father_inc	 	    ='".$scl_father_inc."',";
			$strsql.=" scl_mother_name	 	    ='".$scl_mother_name."',";
			$strsql.=" scl_mother_quli	 	    ='".$scl_mother_quli."',";
			$strsql.=" scl_mother_occu	 	    ='".$scl_mother_occu."',";
			$strsql.=" scl_mother_inc	 	    ='".$scl_mother_inc."',";
			$strsql.=" scl_date                 ='".$scl_date."',";
			$strsql.=" scl_dob 					='".$scl_dob."',";
			$strsql.=" scl_address	 			='".$scl_address."',";
			$strsql.=" scl_phone_no	 		    ='".$scl_phone_no."',";
			$strsql.=" scl_car					='".$scl_car."',";
			$strsql.=" scl_religion	 			='".$scl_religion."',";
			$strsql.=" scl_gender 				='".$scl_gender."',";
			$strsql.=" scl_class 				='".$scl_class."',";
			$strsql.=" scl_session 				='".$scl_session."',";
			$strsql.=" scl_section 				='".$scl_section."',";
			$strsql.=" bank_name 			    ='".$bank_name."',";
			$strsql.=" branch_name 			    ='".$branch_name."',";
			$strsql.=" bank_ac_no 		     	='".$bank_ac_no."',";
			$strsql.=" bank_ifsc_code 		    ='".$bank_ifsc_code."',";
			$strsql.=" scl_nationality 		    ='".$scl_nationality."',";
			$strsql.=" scl_pre_name 			='".$scl_pre_name."',";
			$strsql.=" scl_pre_class 			='".$scl_pre_class."',";
			$strsql.=" scl_tcn 		     	    ='".$scl_tcn."',";
        	$strsql.=" scl_blood 		        ='".$scl_blood."',";		
			$strsql.=" scl_tc_date 		     	='".$scl_tc_date."',";
			$strsql.=" scl_dist 		     	='".$scl_dist."',";
			$strsql.=" scl_pos 		         	='".$scl_pos."',";
			$strsql.=" admission_due 		   	='".$monthly_admission_fee."',";
			$strsql.=" scl_pin 		        	='".$scl_pin."',";
			$strsql.=" scl_aadhar 		     	='".$scl_aadhar."',";
			$strsql.=" monthly_fee				='".$monthly_fee."',";
			$strsql.=" scl_block				='".$scl_block."',";
			$strsql.=" scl_email				='".$scl_email."',";
			$strsql.=" scl_bpl			    	='".$scl_bpl."',";
			$strsql.=" scl_disa			    	='".$scl_disa."',";
			$strsql.=" scl_language				='".$scl_language."',";
			$strsql.=" scl_ide			    	='".$scl_ide."',";			
			$strsql.=" father_phone_no			='".$father_phone_no."',";
        	$strsql.=" scl_pos			        ='".$scl_pos."',";
	    	$strsql.=" scl_pol		        	='".$scl_pol."',";
			$strsql.=" scl_dest		        	='".$scl_dest."',";
        	$strsql.=" alt_phone		    	='".$alt_phone."',";
	    	$strsql.=" scl_mu		        	='".$scl_mu."',";
			$strsql.=" scl_state		    	='".$scl_state."',";
			
// 			$strsql.=" add_status		    	='".$add_status."',";
            $strsql.=" scl_location		    	='".$scl_location."',";      			
			
			$strsql.=" scl_category 		    ='".$scl_category."'";
		
 		if ($image<>""){
		   $sql="select image from rrsv_student_registration where id='".$id."'";
		   $result=mysqli_query($myDB,$sql);
		   $arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
		   $p_image=$arr['image'];
		   if($p_image!=$image){
           if($p_image<>""){
		   $file1="student_reg_image/".$arr['image'];
		    if($file1<>""){
	   if(file_exists($file1)){
		   unlink($file1);
	   }
	   }
	   	   $file2="student_reg_image/thumb_".$arr['image'];
	    if($file2<>""){
	   	if(file_exists($file2)){
		   unlink($file2);
	   }
	   }  
	   		   }
		   	 $strsql.=",image='".$image."'";
		}
		}
		if ($dob_image<>""){
			$sql="select dob_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['dob_image'];
			if($p_image!=$dob_image){
			if($p_image<>""){
			$file1="student_dob/".$arr['dob_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="student_dob/thumb_".$arr['dob_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",dob_image='".$dob_image."'";
		 }
		 }
		 	if ($aadhar_image<>""){
			$sql="select aadhar_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['aadhar_image'];
			if($p_image!=$aadhar_image){
			if($p_image<>""){
			$file1="student_aadhar/".$arr['aadhar_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="student_aadhar/thumb_".$arr['aadhar_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",aadhar_image='".$aadhar_image."'";
		 }
		 }
				if ($father_aadhar_image<>""){
			$sql="select father_aadhar_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['father_aadhar_image'];
			if($p_image!=$father_aadhar_image){
			if($p_image<>""){
			$file1="father_aadhar_image/".$arr['father_aadhar_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="father_aadhar_image/thumb_".$arr['father_aadhar_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",father_aadhar_image='".$father_aadhar_image."'";
		 }
		 }
						if ($father_voter_image<>""){
			$sql="select father_voter_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['father_voter_image'];
			if($p_image!=$father_voter_image){
			if($p_image<>""){
			$file1="father_voter_image/".$arr['father_voter_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="father_voter_image/thumb_".$arr['father_voter_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",father_voter_image='".$father_voter_image."'";
		 }
		 }
		
						if ($mother_aadhar_image<>""){
			$sql="select mother_aadhar_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['mother_aadhar_image'];
			if($p_image!=$mother_aadhar_image){
			if($p_image<>""){
			$file1="mother_aadhar_image/".$arr['mother_aadhar_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="mother_aadhar_image/thumb_".$arr['mother_aadhar_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",mother_aadhar_image='".$mother_aadhar_image."'";
		 }
		 }
		
								if ($mother_voter_image<>""){
			$sql="select mother_voter_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['mother_voter_image'];
			if($p_image!=$mother_voter_image){
			if($p_image<>""){
			$file1="mother_voter_image/".$arr['mother_voter_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="mother_voter_image/thumb_".$arr['mother_voter_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",mother_voter_image='".$mother_voter_image."'";
		 }
		 }
		
								if ($passbook_image<>""){
			$sql="select passbook_image from rrsv_student_registration where id='".$id."'";
			$result=mysqli_query($myDB,$sql);
			$arr=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$p_image=$arr['passbook_image'];
			if($p_image!=$passbook_image){
			if($p_image<>""){
			$file1="passbook_image/".$arr['passbook_image'];
			 if($file1<>""){
		if(file_exists($file1)){
			unlink($file1);
		}
		}
			   $file2="passbook_image/thumb_".$arr['passbook_image'];
		 if($file2<>""){
			if(file_exists($file2)){
			unlink($file2);
		}
		}  
		
			}
				 $strsql.=",passbook_image='".$passbook_image."'";
		 }
		 }
		


		 $strsql.="where id=".$id; 
		$result=mysqli_query($myDB,$strsql) or die("Error into updateing:".mysqli_connect_error());
		
		if($result){
			echo "<script language='JavaScript'>";
			echo "window.location.href='manage_registration.php?retcode=1';";
			echo "</script>";
		}
		
}
	
	else
	{
	    
	    $sql="select * from rrsv_student_registration where id=$id";
	    $res=mysqli_query($myDB,$sql);
	    $rows=mysqli_fetch_array($res);
	    $reg_id=$rows['scl_reg_no'];
	    $admission_due=$rows['admission_due'];
	    $yearly_due=$rows['yearly_due'];
	    $car_due=$rows['car_due'];
	     $pre=$admission_due+$yearly_due+$car_due;
	    	$errcode = 0; 
	     		$strsql = "select * from rrsv_student_registration where scl_session='".$scl_session."'   and id='$id'";
		 $objrs = mysqli_query($myDB,$strsql) or die(mysqli_connect_error());
		 if(mysqli_num_rows($objrs) > 0){
			 $errcode = 1;	
			 echo'<script language="javascript" type="text/javascript">';
			 echo'alert("student name you have entered is duplicate!");';
			 echo'window.history.go(-1);';
			 echo'</script>';
			 exit();
		 }
		
	     $strsql="";
		 $strsql.="insert into rrsv_student_registration(scl_reg_no,scl_roll_no,scl_name,scl_father_name,scl_father_quli,scl_father_occu,scl_father_inc,scl_mother_name,scl_mother_quli,scl_mother_occu,scl_mother_inc,scl_dob,scl_date,scl_address,scl_phone_no,scl_religion,scl_gender,scl_class,scl_session,scl_section,scl_car,bank_name,branch_name,bank_ac_no,bank_ifsc_code,scl_nationality,scl_category,dob_image,father_voter_image,father_aadhar_image,mother_voter_image,mother_aadhar_image,passbook_image,image,scl_pre_name,scl_pre_class,scl_tcn,exp_date,scl_blood,scl_dist,scl_po,scl_aadhar,monthly_fee,admission_due,pre_due,father_phone_no,aadhar_image,add_status,scl_location,scl_block,scl_email,scl_bpl,scl_disa,scl_language,scl_ide,scl_pos,scl_pol,scl_dest,alt_phone,scl_mu,scl_state,scl_pin,s_a_d)";
		 $strsql.="values('".$reg_id."','".$scl_roll_no."','".$scl_name."','".$scl_father_name."','".$scl_father_quli."','".$scl_father_occu."','".$scl_father_inc."','".$scl_mother_name."','".$scl_mother_quli."','".$scl_mother_occu."','".$scl_mother_inc."','".$scl_dob."','".$scl_date."','".$scl_address."','".$scl_phone_no."','".$scl_religion."','".$scl_gender."','".$scl_class."','".$scl_session."','".$scl_section."','".$scl_car."','".$bank_name."','".$branch_name."','".$bank_ac_no."','".$bank_ifsc_code."','".$scl_nationality."','".$scl_category."','".$dob_image."','".$father_aadhar_image."','".$father_voter_image."','".$mother_aadhar_image."','".$mother_voter_image."','".$passbook_image."','".$image."','".$scl_pre_name."','".$scl_pre_class."','".$scl_tcn."','".$nextday."','".$scl_blood."','".$scl_dist."','".$scl_po."','".$scl_aadhar."','".$monthly_fee."','".$monthly_admission_fee."','".$pre."','".$father_phone_no."','".$aadhar_image."','Readmission','".$scl_location."','".$scl_block."','".$scl_email."','".$scl_bpl."','".$scl_disa."','".$scl_language."','".$scl_ide."','".$scl_pos."','".$scl_pol."','".$scl_dest."','".$alt_phone."','".$scl_mu."','".$scl_state."','".$scl_pin."','".$scl_date."')";
	 
	 $qresult=mysqli_query($myDB,$strsql) or die("Error into inserting:".mysqli_connect_error());

	 if($qresult){
		 echo "<script language='JavaScript'>";
		 echo "window.location.href='manage_registration.php?retcode=2';";
		 echo "</script>";
	 }	
	}
}
else{
	$errcode = 0;
// 		$strsql = "select * from rrsv_student_registration where scl_name='".$scl_name."'";
// 		 $objrs = mysqli_query($myDB,$strsql) or die(mysqli_connect_error());
// 		 if(mysqli_num_rows($objrs) > 0){
// 			 $errcode = 1;	
// 			 echo'<script language="javascript" type="text/javascript">';
// 			 echo'alert("student name you have entered is duplicate!");';
// 			 echo'window.history.go(-1);';
// 			 echo'</script>';
// 			 exit();
// 		 }
 
	 $strsql="";
	$strsql.="insert into rrsv_student_registration(scl_roll_no,scl_name,scl_father_name,scl_father_quli,scl_father_occu,scl_father_inc,scl_mother_name,scl_mother_quli,scl_mother_occu,scl_mother_inc,scl_dob,scl_date,scl_address,scl_phone_no,scl_religion,scl_gender,scl_class,scl_session,scl_section,scl_car,bank_name,branch_name,bank_ac_no,bank_ifsc_code,scl_nationality,scl_category,dob_image,father_voter_image,father_aadhar_image,mother_voter_image,mother_aadhar_image,passbook_image,image,scl_pre_name,scl_pre_class,scl_tcn,exp_date,scl_blood,scl_dist,scl_po,scl_aadhar,monthly_fee,admission_due,father_phone_no,aadhar_image,add_status,scl_location,scl_block,scl_email,scl_bpl,scl_disa,scl_language,scl_ide,scl_pos,scl_pol,scl_dest,alt_phone,scl_mu,scl_state,scl_pin,s_a_d)";
 	$strsql.="values('".$scl_roll_no."','".$scl_name."','".$scl_father_name."','".$scl_father_quli."','".$scl_father_occu."','".$scl_father_inc."','".$scl_mother_name."','".$scl_mother_quli."','".$scl_mother_occu."','".$scl_mother_inc."','".$scl_dob."','".$scl_date."','".$scl_address."','".$scl_phone_no."','".$scl_religion."','".$scl_gender."','".$scl_class."','".$scl_session."','".$scl_section."','".$scl_car."','".$bank_name."','".$branch_name."','".$bank_ac_no."','".$bank_ifsc_code."','".$scl_nationality."','".$scl_category."','".$dob_image."','".$father_aadhar_image."','".$father_voter_image."','".$mother_aadhar_image."','".$mother_voter_image."','".$passbook_image."','".$image."','".$scl_pre_name."','".$scl_pre_class."','".$scl_tcn."','".$nextday."','".$scl_blood."','".$scl_dist."','".$scl_po."','".$scl_aadhar."','".$monthly_fee."','".$monthly_admission_fee."','".$father_phone_no."','".$aadhar_image."','New Admission','".$scl_location."','".$scl_block."','".$scl_email."','".$scl_bpl."','".$scl_disa."','".$scl_language."','".$scl_ide."','".$scl_pos."','".$scl_pol."','".$scl_dest."','".$alt_phone."','".$scl_mu."','".$scl_state."','".$scl_pin."','".$scl_date."')";

	 $qresult=mysqli_query($myDB,$strsql) or die("Error into inserting:".mysqli_connect_error());
 	$curr=date("Y");
	 $lstid=$myDB ->insert_id;
// 	 $regid=$scl_session.'-'.$lstid;
 $regid=$scl_session.'-'.$scl_section.'-'.$lstid;
 
 $Upsql="update rrsv_student_registration set scl_reg_no='".$regid."' where id=$lstid";
	 $Upres=mysqli_query($myDB,$Upsql);
	 
	 if($qresult){
		 echo "<script language='JavaScript'>";
		 echo "window.location.href='manage_registration.php?retcode=2';";
		 echo "</script>";
	 }
 }
 
 ?>
 