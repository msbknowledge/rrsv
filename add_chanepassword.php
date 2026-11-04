<?php
include('include/header.php');
include('include/dbcon.php');

// if(isset($_GET['id']))
// {
// $id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
//  $sql="select * from rrsv_class where id=$id";
// $res=mysqli_query($myDB,$sql);
// $rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
// }
?>
      <!-- partial -->
      <div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Change Password</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                      
                    <div class="form-group">
                      <label for="exampleInputUsername1">Old Password</label>
                      <input type="text" class="form-control" id="oldpassword" placeholder="Old Password" name="oldpassword" value='<?=$rows['oldpassword'];?>' >
                     <span id="error" style="color:red;"></span>
                    </div>
                     <div class="form-group">
                      <label for="exampleInputUsername1">New Password</label>
                      <input type="text" class="form-control" id="newpassword" placeholder="New Password" name="newpassword" value='<?=$rows['newpassword'];?>' >
                     <span id="error1" style="color:red;"></span>
                    </div>
                    <button type="submit" class="btn btn-primary me-2" >Submit</button>
                  
                  </form>
                    <!--<a href="manage_class.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>-->
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
          var oldpassword = $('#oldpassword').val();
          
              var newpassword = $('#newpassword').val();
          if(oldpassword==''){
              	$('#error').html('Please Enter Old Password.');
		        return false;
                 }
                    if(newpassword==''){
              	$('#error1').html('Please Enter New Password.');
		        return false;
                 }
            ajax_fun();

    });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "change_password_post.php",
                data: $('form').serialize(),
                success: function(val) {
                    
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Password Change Successfully!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#oldpassword').val(""); 
                        $('#newpassword').val(""); 
                }
                if(val == 1) {
                    alert(val);
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Class Edit Successfully!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                if(val == 2) {
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>The Class Name User has given is already Saved!!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       