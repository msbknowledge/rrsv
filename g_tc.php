<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
                            $currentyear=date("Y");
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
//  $sql="select * from  rrsv_student_registration where id='$id' and scl_session='$currentyear'";
 $sql="select * from  rrsv_student_registration where id='$id' ";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
?>
      <!-- partial -->
      <div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Genarate Transfer Certificate</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='#' method='post'>
                      <input type="hidden" name="s_id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                      <div class="" id="change">

                                          
</div>
                    <div class="form-group">
                      <label for="exampleInputUsername1"> Student Name</label>
                      <input type="text" class="form-control" id="name" placeholder="Student Name" name="name" value='<?=$rows['scl_name'];?>'readonly >
                     <!--<span id="error2" style="color:red;"></span>-->
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Class Name</label>
                       <input type="text" class="form-control" id="name" placeholder="Class Name" name="a_class" value='<?=$rows['scl_class'];?>' readonly >
                     <!--<span id="error1" style="color:red;"></span>-->
                    </div>
                       <div class="form-group">
                      <!--<label for="exampleInputUsername1">Father's Name</label>-->
                       <input type="hidden" class="form-control" id="name" placeholder="Class Name" name="fname" value='<?=$rows['scl_father_name'];?>' readonly >
                     <!--<span id="error1" style="color:red;"></span>-->
                    </div>
                    <!--                       <div class="form-group">-->
                    <!--  <label for="exampleInputUsername1">Cast</label>-->
                    <!--   <input type="text" class="form-control" id="name" placeholder="Class Name" name="strem" value='<?=$rows['scl_religion'];?>' readonly >-->
                     <!--<span id="error1" style="color:red;"></span>-->
                    <!--</div>-->
                    
                      <div class="form-group">
                      <!--<label for="exampleInputUsername1">Address</label>-->
                      <input type="hidden" class="form-control" id="address" placeholder="Address" name="address" value='<?=$rows['scl_address'];?>'readonly >
                     <!--<span id="error3" style="color:red;"></span>-->
                    </div>
                                          <div class="form-group">
                      <!--<label for="exampleInputUsername1">District</label>-->
                      <input type="hidden" class="form-control" id="address" placeholder="Address" name="scl_dist" value='<?=$rows['scl_dist'];?>' readonly >
                     <!--<span id="error3" style="color:red;"></span>-->
                    </div>
                     <div class="form-group">
                      <!--<label for="exampleInputUsername1">Admission Date</label>-->
                      <input type="hidden" class="form-control" id="d_o_a" placeholder="Address" name="d_o_a" value='<?=$rows['scl_date'];?>' readonly >
                     <!--<span id="error3" style="color:red;"></span>-->
                    </div>
                                         <div class="form-group">
                      <!--<label for="exampleInputUsername1">Date of Birth</label>-->
                      <input type="hidden" class="form-control" id="d_o_a" placeholder="Address" name="scl_dob" value='<?=$rows['scl_dob'];?>' readonly >
                     <!--<span id="error3" style="color:red;"></span>-->
                    </div>

                    <div class="form-group">
                      <label for="exampleInputUsername1">Date of School Leaving</label>
                      <input type="date" class="form-control" id="d_o_t" placeholder="Date of left School" name="d_o_t" value='<?=$rows['d_o_t'];?>' >
                     <span id="error1" style="color:red;"></span>
                    </div>

                       <div class="form-group">
                      <label for="exampleInputUsername1">Promotion to Class Name</label>
                      <select name="p_class" class="form-control" id="p_class">
                                <option value="">Select Class</option>
                                  <option value="V">V</option>
                            <?php
                           // $id=0;
                             $sql="select * from rrsv_class order by id";
                              $res=mysqli_query($myDB,$sql);
                              
                              while($obj=mysqli_fetch_array($res,MYSQLI_ASSOC))
                              {
                                ?>
                              <option value="<?php echo $obj['class_name'];?>"<?php if(trim($rows['class_name']==$obj['class_name']))echo "selected";?>>
                              <?php echo $obj['class_name'];?>
                              
                              </option>
                                  <?php
                              }
                         ?>
                          </select>
                     <span id="error2" style="color:red;"></span>
                    </div>

                    <div class="form-group">
                      <label for="exampleInputUsername1">Character</label>
                      <input type="text" class="form-control" id="s_char" placeholder="Character" name="s_char" value='<?=$rows['name'];?>' >
                     <span id="error3" style="color:red;"></span>
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_tc.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
                </div>
              </div>
            </div>
           </div>
        </div>
         <?php
include('include/footer.php');
?>


<script>
    $('form').submit(function(e){
        e.preventDefault();
         var d_o_t = $('#d_o_t').val();
         var p_class = $('#p_class').val();
          var s_char = $('#s_char').val();
          
          
          if(d_o_t==''){
              	$('#error1').html('Please Enter Date of Left.');
		        return false;
                 }
                 if(p_class==''){
              	$('#error2').html('Please Enter Class Name.');
		        return false;
                 }
                    if(s_char==''){
              	$('#error3').html('Please Enter  Character.');
		        return false;
                 }
             
             
                 
            ajax_fun();

    });
    
    
   function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "g_tc_post.php",
                data: $('form').serialize(),
                success: function(val) {
                
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Generator Transfer Certificate Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                        $('#d_o_t').val(""); 
                        $('#p_class').val(""); 
                        $('#s_char').val("");  
                  
                }
              

             }
        });
        
    }    
</script>
        
       