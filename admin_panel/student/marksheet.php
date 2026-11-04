
<?php
session_start();
include('../dbcon.php');
// GET parameters
$student_id = $_GET['student_id'];
$scl_class = $_GET['scl_class'];
$scl_session = $_GET['scl_session'];
$unit = $_GET['unit']; // 1, 2, or 3

$current_unit_status = 0; // Default: no unit submitted

// Query the current unit data
$unitsql = "SELECT * FROM rrsv_marksheet_unit WHERE student_id = $student_id AND session = $scl_session AND class = '$scl_class'";
$unitres = mysqli_query($con, $unitsql);
$totalunitrecord = mysqli_num_rows($unitres);

if ($totalunitrecord > 0) {
    $unitrows = mysqli_fetch_assoc($unitres);
    $unit_val = $unitrows['unit'];

    if ($unit_val === '2nd_unit') {
        $current_unit_status = 1; // 1st submitted
    } elseif ($unit_val === '3rd_unit') {
        $current_unit_status = 2; // 1st + 2nd submitted
    } elseif ($unit_val == '1') {
        $current_unit_status = 3; // All 3 units done
    }
}
//  echo "current_unit_status ".$current_unit_status;
//  echo "unit ".$unit;
 //die;




$suffix = '';
if ($unit == 1) {
    $suffix = 'st';
} elseif ($unit == 2) {
    $suffix = 'nd';
} elseif ($unit == 3) {
    $suffix = 'rd';
} else {
    $suffix = 'th'; // fallback
}

$unit_col_prefix = "{$unit}{$suffix}_unit";
// Restriction logic
$msg = "";

if ($current_unit_status == 0) {
   echo  $msg = "No units have been submitted yet. You cannot edit any unit.";
} elseif ($current_unit_status == 1 && !in_array($unit, [1])) {
    echo  $msg = "Only 1st unit has been submitted. You can only edit 1st unit.";
} elseif ($current_unit_status == 2 && !in_array($unit, [1, 2])) {
    echo  $msg = "Only 1st and 2nd units have been submitted. You can only edit 1st or 2nd unit.";
}



// Get student info
$sql_student = "SELECT * FROM rrsv_student_registration WHERE id = '$student_id'";
$res_student = mysqli_query($con, $sql_student);
$row_student = mysqli_fetch_assoc($res_student);

// Get unit info
$sql_unit = "SELECT * FROM rrsv_marksheet_unit WHERE student_id = '$student_id' AND session = '$scl_session' AND class = '$scl_class'";
$res_unit = mysqli_query($con, $sql_unit);
$row_unit = mysqli_fetch_assoc($res_unit);

// Get subjects and marks
$sql_marksheet = "SELECT * FROM rrsv_marksheet WHERE student_id = '$student_id' AND session = '$scl_session' AND class = '$scl_class'";
$res_marksheet = mysqli_query($con, $sql_marksheet);



// Subject dropdown
function fill_sub($con, $student_id) {
    $get_class = mysqli_fetch_assoc(mysqli_query($con, "SELECT scl_class FROM rrsv_student_registration WHERE id = '$student_id'"));
    $class_name = $get_class['scl_class'];
    $output = '';
    $sql = "SELECT sub_name FROM rrsv_subject WHERE class_name = '$class_name'";
    $res = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $output .= '<option value="'.$row['sub_name'].'">'.$row['sub_name'].'</option>';
    }
    return $output;
}

?>
<!DOCTYPE html>
<html>
<?php include('../header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <!-- Header -->
    <header class="main-header">
        <a href="#" class="logo">
            <span class="logo-mini"><b>MSB</b></span>
             <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg" style="height:100%;width:44px;border-radius:50%;"></span>
        </a>
        <?php include '../top_navbar.php'; ?>
    </header>

    <!-- Sidebar -->
    <?php include '../left_navbar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Homework / Exam List</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Available Exams</h3>
                        </div>


                <input type="hidden" name="student_id" value="<?=$student_id?>">
                <input type="hidden" name="scl_class" value="<?=$scl_class?>">
                <input type="hidden" name="scl_session" value="<?=$scl_session?>">
                <input type="hidden" name="unit" id="unit" value="<?=$unit?>">
                  
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Student Details</h4>
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Student Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_name" id="exampleInputUsername2" value="<?=$row_student['scl_name']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Class Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_class" id="exampleInputEmail2" value="<?=$scl_class?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Section Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_section" id="exampleInputMobile" value="<?=$row_student['scl_section']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Roll No:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_roll_no" id="exampleInputPassword2" value="<?=$row_student['scl_roll_no'];?>" readonly>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Admission Status:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputPassword2" value="<?=$row_student['add_status'];?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                           
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Unit:</label>
                                <div class="col-sm-9">
                                  <b><?=$unit;?><?=$suffix;?> Unit</b>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Body Weight:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="body_weight" class="form-control" value="<?= ($row_unit["{$unit_col_prefix}_body_weight"] === 'NA') ? '' : $row_unit["{$unit_col_prefix}_body_weight"] ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Home Project:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="home_project" class="form-control" value="<?= ($row_unit["{$unit_col_prefix}_home_project"] === 'NA') ? '' : $row_unit["{$unit_col_prefix}_home_project"] ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Grade:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="grade" class="form-control" value="<?= ($row_unit["{$unit_col_prefix}_grade"] === 'NA') ? '' : $row_unit["{$unit_col_prefix}_grade"] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
				
				
				<table width="100%">
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
		                </tr>
		            </tbody>
		        </table>
		
		
		        <table class="table table-bordered" id="crud_table">
                    <thead>
                <tr height="30" style="background:#0a5079;border-color: #d29c29;">
                    <td width="35%" class="text" style="color:#fff;"><strong>Subject Name</strong></td>
                            <td width="25%" class="text" style="color:#fff;"><strong>Marks</strong></td>
                            <td width="25%" class="text" style="color:#fff;"><strong>H.M</strong></td>
                            <td width="15%" class="text" style="color:#fff;"><strong>Action</strong></td>
                </tr>
            </thead>
                    <tbody>
                        
                        <?php
                $i = 1;
                while ($row = mysqli_fetch_assoc($res_marksheet)) {
                    $subject = $row['subject'];
                    $marks = $row["{$unit_col_prefix}_marks"];
                    $hm = $row["{$unit_col_prefix}_hm"];
                    ?>
                    <tr id="row<?=$i?>">

                        <td>
                            <select name="marksheet[subject][]" class="form-control cat" id="sub_id1" accesskey="1" readonly>
                                <option value="<?=$subject?>" selected><?=$subject?></option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="marksheet[unit_marks][]" class="form-control writencal" id="writen1" value="<?=$marks?>">
                        </td>
                        <td>
                            <input type="text" name="marksheet[unit_hm][]" class="form-control oralcal" id="oral1" value="<?=$hm?>">
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                <?php $i++; } ?>
   	                </tbody>
   	            </table>
   	                    
   	                    
   	            
			
			
	 </div> <!-- /.box -->
                </div>
            </div>
        </section>
    </div>

    <?php include '../footer.php'; ?>
    <div class="control-sidebar-bg"></div>
</div>

<!-- Scripts -->
<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script src="../assets/js/app.min.js"></script>
<script>
$(function () {
  $("#example1").DataTable();
});
</script>

</body>
</html>

<script>
let i = <?=$i?>;

$("#add").click(function() {
    let html = `<tr id="row${i}">
        <td>
            <select name="marksheet[subject][]" class="form-control">
                <option value="">Select Subject</option>
                <?= fill_sub($con, $student_id); ?>
            </select>
        </td>
        <td><input type="text" name="marksheet[unit_marks][]" class="form-control" placeholder="Marks"></td>
        <td><input type="text" name="marksheet[unit_hm][]" class="form-control" placeholder="H.M"></td>
        <td><button type="button" class="btn btn-danger btn_remove" id="${i}">Remove</button></td>
    </tr>`;
    $('#crud_table tbody').append(html);
    i++;
});

$(document).on('click', '.btn_remove', function() {
    let rowId = $(this).attr("id");
    $('#row' + rowId).remove();
});
</script>
 