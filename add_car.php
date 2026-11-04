<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_car where id=$id";
$res=mysqli_query($myDB,$sql);
$rows=mysqli_fetch_array($res,MYSQLI_ASSOC);
}
?>
      <!-- partial -->
      <div class="main-panel">  
      <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> <a href='manage_car.php'><button type="button" class="btn btn-dark btn-rounded btn-icon"><i class="mdi mdi-keyboard-backspace"></i></button></a></h4>
                </div>
            </div>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card" style="margin-left: 188px;">
                <div class="card-body">
                  <h4 class="card-title">Add Car Rate</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_car_post.php' method='post'>
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
                     <span id="error0" style="color:red;"></span>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputUsername1">Kilo Metre</label>
                      <input type="text" class="form-control" id="kilo" placeholder="Kilo Metre" name="kilo" value='<?=$rows['kilo'];?>' >
                     <span id="error" style="color:red;"></span>
                    </div>
                       <div class="form-group">
                      <label for="exampleInputUsername1">Rate</label>
                      <input type="text" class="form-control" id="fair" placeholder="Rate" name="fair" value='<?=$rows['fair'];?>' >
                     <span id="error1" style="color:red;"></span>
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_car.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
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
          var kilo = $('#kilo').val();
           var fair = $('#fair').val();
            var scl_session = $('#scl_session').val();
            if(scl_session==''){
              	$('#error0').html('Please Enter Session.');
		        return false;
                 }
          if(kilo==''){
              	$('#error').html('Please Enter kilometre.');
		        return false;
                 }
                  if(fair==''){
              	$('#error1').html('Please Enter Fair Charges.');
		        return false;
                 }
            ajax_fun();

    });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "add_car_post.php",
                data: $('form').serialize(),
                success: function(val) {
                    
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Kilo Metre  Add Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#kilo').val(""); 
                      $('#fair').val(""); 
                }
                if(val == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Kilo Metre Edit Successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
                 if(val == 2) {
                     
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>The Kilo Metre Name User has given is already Saved!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       