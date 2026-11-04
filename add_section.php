<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_section where id=$id";
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
                  <h4 class="card-title">Add Section</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_class_post.php' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                                          <div class="form-group">
                      <label for="exampleInputUsername1">Class Name</label>
                      <select name="class_name" class="form-control" id="class_name">
                                <option value="">Select Class</option>
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
                     <span id="error" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Section Name</label>
                      <input type="text" class="form-control" id="section_name" placeholder="Section Name" name="section_name" value='<?=$rows['section_name'];?>' >
                     <span id="error1" style="color:red;"></span>
                    </div>
                     
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_section.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
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
           var section_name = $('#section_name').val();
          if(class_name==''){
            	$('#error').html('Please Enter Class Name.');
		        return false;
                }
                 if(section_name==''){
              	$('#error1').html('Please Enter Section Name.');
		        return false;
                 }
            ajax_fun();

    });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "add_section_post.php",
                data: $('form').serialize(),
                success: function(val) {
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Section Saved Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#class_name').val(""); 
                        $('#section_name').val(""); 
                }
                if(val == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Section Edit Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                 if(val == 2) {
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Section Name you have entired is duplicate!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       