<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_subject where id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
?>
      <!-- partial -->
      <div class="main-panel">  
       <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_subject.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Add Subjecte</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_class_post.php' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                  
                     <div class="form-group">
                      <label for="exampleInputUsername1">Class</label>
                            <select name="class_name" class="form-control" id="class_name">
                                <option value="">-Select a Class-</option>
                                    <?php
									$id=0;
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
                     <span id="error1" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Subject Name</label>
                      <input type="text" class="form-control" id="sub_name" placeholder="Subject Name" name="sub_name" value='<?=$rows['sub_name'];?>' >
                     <span id="error2" style="color:red;"></span>
                    </div>

                        

                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_subject.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
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
       
         var class_name = $('#class_name').val();
          var sub_name = $('#sub_name').val();
          
        
           var book_pub = $('#book_pub').val(); 
            var rate = $('#rate').val(); 
         
           
                 if(class_name==''){
              	$('#error1').html('Please Enter Class name.');
		        return false;
                 }
                  if(sub_name==''){
              	$('#error2').html('Please Enter Subject name.');
		        return false;
                 }
         
        //           if(monthly_fee==''){
        //       	$('#error1').html('Please Enter Monthly Fee.');
		      //  return false;
        //          }
            ajax_fun();

    });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "add_subject_post.php",
                data: $('form').serialize(),
                success: function(val) {
                
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Subject Add Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#class_name').val(""); 
                        $('#sub_name').val(""); 
                }
                if(val == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Subject Edited Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                if(val == 2) {
                     
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>The Book Name User has given is already Saved!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       