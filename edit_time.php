<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
$in_time=	$myDB->escape_string(trim(addslashes($_GET['in_time'])));
$out_time=	$myDB->escape_string(trim(addslashes($_GET['out_time'])));
}
?>
      <!-- partial -->
      <div class="main-panel">  
       <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_time.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Edit Time</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='manage_time.php' method='post'>
                      <input type="hidden" name="id" id="id" value="<?=$id;?>">
                      <div class="form-group">
                      <label for="exampleInputUsername1">Start Time</label>
                      <input type="time" class="form-control" id="in_time" placeholder="Book Publisher Name" name="in_time" value='<?=$in_time;?>' >
                     <span id="error3" style="color:red;"></span>
                    </div>
                                        <div class="form-group">
                      <label for="exampleInputUsername1">End Time</label>
                      <input type="time" class="form-control" id="out_time" placeholder="Book Rate" name="out_time" value='<?=$out_time;?>' >
                     <span id="error4" style="color:red;"></span>
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_time.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
                </div>
              </div>
            </div>
           </div>
        </div>
         <?php
include('include/footer.php');
?>
<script>
//     $('form').submit(function(e){
//         e.preventDefault();
//          var in_time = $('#in_time').val();
//          var out_time = $('#out_time').val();
         
//              if(in_time==''){
//               	$('#error').html('Please Enter Start Time.');
// 		        return false;
//                  }
//                  if(out_time==''){
//               	$('#error1').html('Please Enter End Time.');
// 		        return false;
//                  }
                  
//  $('form').submit();
//     });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "add_book_post.php",
                data: $('form').serialize(),
                success: function(val) {
                
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Book Add Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#class_name').val(""); 
                        $('#section_name').val(""); 
                }
                if(val == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Book Edited Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                if(val == 2) {
                     
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>The Book Name User has given is already Saved!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       