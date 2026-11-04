<style>
    #testSelect1_multiSelect {
         width: 200px;
    }
    
    .red {
        color: red;
    }
    
    .hight_width {
        font-size: 25px;
    }

</style>

<?php
include('include/dbcon.php');
$rows=0;
$reg=0;
$reg2=0;
$id=0;
$scl_install_due=0;
$scl_install=0;
$scl_admission=0;
$scl_panding=0;
$scl_net=0;
$scl_instalment=0;
$full_payment=0;
$scl_enroll_no=0;
$eid=0;
$id=0;
$row=0;
$reg2=0;
$txtsearch ="";
if(isset($_GET['id']))
{
  $id=$myDB->escape_string(trim(addslashes($_GET['id'])));
$sql="select * from rrsv_student_registration  where id=$id ";
 $res=mysqli_query($myDB,$sql);
  $rows=mysqli_fetch_array($res);

}
// echo "<pre>";
// print_r($rows);
// die();
// scl_session

	function fill_sub($myDB){
if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
}
		$output='';
		  $sql="select a.*,b.id  from rrsv_subject as a, rrsv_student_registration as b where b.id=$id and a.class_name=b.scl_class ";
		$res=mysqli_query($myDB,$sql);
		while($obj1=mysqli_fetch_array($res,MYSQLI_ASSOC)) {
			$output .='<option value="'.$obj1["sub_name"].'">'.$obj1["sub_name"].'</option>';
	    }	
	   return $output;
	} 
	
	$session = $rows['scl_session'];
	$class = $rows['scl_class'];
	
	 $unitsql="SELECT * FROM rrsv_marksheet_unit where student_id = $id and session = $session and class = '$class'";
	$unitres=mysqli_query($myDB,$unitsql);
	$totalunitrecord=mysqli_num_rows($unitres);
	$unitrows=mysqli_fetch_array($unitres,MYSQLI_ASSOC);
    $unit_val = $unitrows['unit'];
    if($unit_val === '1') { ?>

        <script>
        alert('marksheet complete!Please click to print button for print');
        window.history.go(-1);
        </script>
        
<?php exit(); }	

if($totalunitrecord === 0) {
    $unit_val = '1st_unit';
    $unit_val2 = '1st Unit';
   
} else {
    		$unit_val = $unitrows['unit'];
    		if($unit_val == '2nd_unit') {
    		    $unit_val2 = '2nd Unit';
    		}
    		if($unit_val == '3rd_unit') {
    		    $unit_val2 = '3rd Unit';
    		}
}
include('include/header.php');
	
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
<!-- partial -->
 <form name="frm" action="add_marksheet_post2.php" method="post">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <input type="hidden" name="student_id" value="<?=$rows['id'];?>">
                <input type="hidden" name="scl_session" value="<?=$rows['scl_session'];?>">
                <input type="hidden" name="unit" id="unit" value="<?=$unit_val;?>">
                  
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Student Details</h4>
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Student Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_name" id="exampleInputUsername2" value="<?=$rows['scl_name']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Class Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_class" id="exampleInputEmail2" value="<?=$rows['scl_class']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Section Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_section" id="exampleInputMobile" value="<?=$rows['scl_section']?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Roll No:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="scl_roll_no" id="exampleInputPassword2" value="<?=$rows['scl_roll_no'];?>" readonly>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Admission Status:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exampleInputPassword2" value="<?=$rows['add_status'];?>" readonly>
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
                                  <b><?=$unit_val2;?></b>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Body Weight:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="body_weight" id="exampleInputEmail2" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Home Project:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="home_project" id="exampleInputMobile" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Grade:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="grade" id="exampleInputMobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div align="right">
    	            <button type="button" name="add" id="add" class="btn btn-primary"><i class="mdi mdi-plus"></i></button>
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
		
		
		        <table id="crud_table">
                    <tbody>
                        <tr height="30" style="background:#0a5079;border-color: #d29c29;">
                            <td width="5%" class="text" style="color:#fff;"><strong>Subject Name</strong></td>
                            <td width="5%" class="text" style="color:#fff;"><strong>Marks</strong></td>
                            <td width="5%" class="text" style="color:#fff;"><strong>H.M</strong></td>
                            <td width="10%" class="text" style="color:#fff;"><strong>Action</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="marksheet[subject][]" class="cat" id="sub_id1" accesskey="1">
								    <option value="">-Select Subject -</option>
								    <?php
             echo fill_sub($myDB); ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="writencal" id="writen1" name="marksheet[unit_marks][]" placeholder="Marks" accesskey="1">
                            </td>
				    	    <td>
				    	        <input type="text" class="oralcal" id="oral1" name="marksheet[unit_hm][]" placeholder="H.M" accesskey="1">
				    	    </td>
				            <td>&nbsp;</td>
                        </tr>
   	                </tbody>
   	            </table>
   	                    
   	                    
   	                    
   	            <div align="center">
				    <button type="button" name="save" id="save" class="btn btn-info">Save</button>
				</div>
			</form>
		</div>
    </div>
</div>

<?php
include('include/footer.php');
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
<script src="multiselect-dropdown.js" ></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
 
<script>
$(document).ready(function(){
	var i=$('table tr').length;
    $("#add").on('click',function(){
        html = '<tr id="row'+i+'">';
        html += '<td><select name="marksheet[subject][]" id="sub_id'+i+'" accesskey="'+i+'" class="cat"><option value="">-Select Subject-</option><?php
             echo fill_sub($myDB); ?></select></td>';
        
        html += '<td><input type="text" class="writencal"  name="marksheet[unit_marks][]]"  id="writen'+i+'" accesskey="'+i+'" placeholder="Writen"></td>';
        html += '<td><input type="text" class="oralcal"  name="marksheet[unit_hm][]"  id="oral'+i+'" accesskey="'+i+'" placeholder="H.M"></td>';
        // html += '<td><input type="text" class="totalcal"  name="marksheet[total][]"  id="total'+i+'" accesskey="'+i+'" placeholder="Total(200)"></td>';
        // html += '<td><input type="text" class="totalcal"  name="marksheet[total][]"  id="total'+i+'" accesskey="'+i+'" placeholder="%"></td>';
        // html += '<td><input type="text" class="totalcal"  name="marksheet[total][]"  id="total'+i+'" accesskey="'+i+'" placeholder="H.M(200)"></td>';
        html += '<td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove" title="Remove" accesskey="'+i+'"><i class="mdi mdi-minus"></i></button></td></tr>';
        $('#crud_table').append(html);
        i++;
    });
	     $(document).on('click', '.btn_remove', function(){
     	 var delete_row = $(this).attr("accesskey");
      	$('#row'+delete_row).remove();
     });
     $('#crud_table').on('keyup', '.writencal,.oralcal', function() {
       var totalprice=0;
	    var c= $(this).attr("accesskey");
    	var writen=$('#writen'+c).val();
// var oral=$('#oral'+c).val();
// var totalprice=Number(writen) + Number(oral);
var totalprice=Number(writen);
	$('#total'+c).val(totalprice);
 
	var fulltotal=0;
  var writenTotal = 0;
  var oralTotal=0;
  var grandTotal = 0;
  $(".fullcal").each(function () {    
  var stval = parseFloat($(this).val());
  fulltotal += isNaN(stval) ? 0 : stval;
  });
  $('.futotal').val(fulltotal.toFixed(2));  
  $(".writencal").each(function () {    
  var stval = parseFloat($(this).val());
   writenTotal += isNaN(stval) ? 0 : stval;
 });
  $('.wtotal').val(writenTotal.toFixed(2));

  $(".oralcal").each(function () {    
  var stval = parseFloat($(this).val());
  oralTotal += isNaN(stval) ? 0 : stval;
  
 });
  $('.ototal').val(oralTotal.toFixed(2));  


$(".totalcal").each(function () {
  var stval = parseFloat($(this).val());
  grandTotal += isNaN(stval) ? 0 : stval;
 });
$('.ftotal').val(grandTotal.toFixed(2));

 $('#grade'+c).val('AA')


     });
     $(document).on('click', '#save', function(){
     	document.frm.method="post";
		document.frm.action="add_marksheet_post2.php";
		document.frm.submit();
     });
     
 });
  </script>
 