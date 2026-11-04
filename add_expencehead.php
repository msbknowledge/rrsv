<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_expence_head where id=$id";
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
                  <h4 class="card-title">Add Expense Head</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                   <form class="forms-sample" action ='add_class_post.php' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                      
                    <div class="form-group">
                      <label for="exampleInputUsername1">Expense Head</label>
                      <input type="text" class="form-control" id="expance" placeholder="Expense Head" name="expence" value='<?=$rows['expence'];?>' >
                     <span id="error" style="color:red;"></span>
                    </div>
                     
                    <button type="submit"  class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_expencehead.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
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
        //   var expence = $('#expence').val();
        //   if(expence==''){
        //       	$('#error').html('Please Enter expence Head.');
		      //  return false;
        //          }
           ajax_fun();

    });
    
    
   function ajax_fun() {

               
        $.ajax({
                type: "POST",
                url: "add_expencehead_post.php",
                data: $('form').serialize(),
                success: function(val) {
                    
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Expense Head Add Successfully!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    //	$('#error').html('');
                      $('#expence').val(""); 
                }
                if(val == 1) {
                    
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Expense Head Edit Successfully!!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                
             }
        });
        
   }
</script>
        
       