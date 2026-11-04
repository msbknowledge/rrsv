<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_book where id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
?>
      <!-- partial -->
      <div class="main-panel">  
       <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_book.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Add Book Rate</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_class_post.php' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                   <div class="form-group">
                      <label for="exampleInputUsername1">Session</label>
	                    <select name="scl_session" class="form-control" value="<?=$rows['scl_session'];?>"   id="scl_session">
                              <option value="">-Select a Session -</option>
                                <?php
									for($i = date("Y")-3; $i <=date("Y")+10; $i++)
								{
									?>
									<!-- '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;-->
								<option value="<?php echo $i;?>"<?php if(trim($rows['scl_session']==$i))echo "selected";?>>
								<?php echo $i;?>
          						</option>
									<?php
								}

								?>
							</select>
                     <span id="error" style="color:red;"></span>
                    </div>
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
								<option value="<?php echo $obj['class_name'];?>"<?php if(trim($rows['scl_class']==$obj['class_name']))echo "selected";?>>
									<?php echo $obj['class_name'];?>
          						</option>
									<?php
									}
									?>
                            </select>
                     <span id="error1" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Book Name</label>
                      <input type="text" class="form-control" id="book_name" placeholder="Book Name" name="book_name" value='<?=$rows['book_name'];?>' >
                     <span id="error2" style="color:red;"></span>
                    </div>
                      <div class="form-group">
                      <label for="exampleInputUsername1">Book Publisher Name</label>
                      <input type="text" class="form-control" id="book_pub" placeholder="Book Publisher Name" name="book_pub" value='<?=$rows['book_pub'];?>' >
                     <span id="error3" style="color:red;"></span>
                    </div>
                                        <div class="form-group">
                      <label for="exampleInputUsername1">Book Rate</label>
                      <input type="text" class="form-control" id="rate" placeholder="Book Rate" name="rate" value='<?=$rows['rate'];?>' >
                     <span id="error4" style="color:red;"></span>
                    </div>

                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_book.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
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
         var scl_session = $('#scl_session').val();
         var class_name = $('#class_name').val();
          var book_name = $('#book_name').val();
          
        
           var book_pub = $('#book_pub').val(); 
            var rate = $('#rate').val(); 
         
             if(scl_session==''){
              	$('#error').html('Please Enter Session Name.');
		        return false;
                 }
                 if(class_name==''){
              	$('#error1').html('Please Enter Class name.');
		        return false;
                 }
                  if(book_name==''){
              	$('#error2').html('Please Enter Book name.');
		        return false;
                 }
          if(book_pub==''){
              	$('#error3').html('Please Enter Book Publisher Name.');
		        return false;
                 }
                 if(rate==''){
              	$('#error4').html('Please Enter Book Rate.');
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
        
       