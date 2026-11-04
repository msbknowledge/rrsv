<?php
include('include/header.php');
include('include/dbcon.php');

if(isset($_GET['id']))
{
$id=	$myDB->escape_string(trim(addslashes($_GET['id'])));
 $sql="select * from rrsv_other_fee where id=$id";
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
                  <h4 class="card-title">Add Fee</h4>
                  <p class="card-description">
                    <div id="alert"></div>
                  </p>
                  <form class="forms-sample" action ='add_otherfee.php' method='post'>
                      <input type="hidden" name="id" value="<?=$rows['id'];?>"/>
                      <input type="hidden" name="token" value="<?php echo $token;?>"/>
                   

                    <div class="form-group">
                      <label for="exampleInputUsername1">Admission Form Fee</label>
                      <input type="text" class="form-control" id="ad_form_charge" placeholder="Section Name" name="ad_form_charge" value='<?=$rows['ad_form_charge'];?>' >
                     <span id="error" style="color:red;"></span>
                    </div>
                      <div class="form-group">
                      <label for="exampleInputUsername1">Book & Copys</label>
                      <input type="text" class="form-control" id="book_charge" placeholder="Section Name" name="book_charge" value='<?=$rows['book_charge'];?>' >
                     <span id="error1" style="color:red;"></span>
                    </div>
                        <div class="form-group">
                      <label for="exampleInputUsername1">Student Uniform</label>
                      <input type="text" class="form-control" id="uniform" placeholder="Section Name" name="uniform" value='<?=$rows['uniform'];?>' >
                     <span id="error2" style="color:red;"></span>
                    </div>
                          <div class="form-group">
                      <label for="exampleInputUsername1">Diary</label>
                      <input type="text" class="form-control" id="diary" placeholder="Section Name" name="diary" value='<?=$rows['diary'];?>' >
                     <span id="error3" style="color:red;"></span>
                    </div>
                                  <div class="form-group">
                      <label for="exampleInputUsername1">Icard</label>
                      <input type="text" class="form-control" id="icard" placeholder="Section Name" name="icard" value='<?=$rows['icard'];?>' >
                     <span id="error4" style="color:red;"></span>
                    </div>
        
                              <div class="form-group">
                      <label for="exampleInputUsername1">Bag</label>
                      <input type="text" class="form-control" id="bag" placeholder="Section Name" name="bag" value='<?=$rows['bag'];?>' >
                     <span id="error5" style="color:red;"></span>
                    </div>
                                  <div class="form-group">
                      <label for="exampleInputUsername1">Sweater,Slax,Cap</label>
                      <input type="text" class="form-control" id="sweater" placeholder="Section Name" name="sweater" value='<?=$rows['sweater'];?>' >
                     <span id="error6" style="color:red;"></span>
                    </div>
                                  <div class="form-group">
                      <label for="exampleInputUsername1">Shoes</label>
                      <input type="text" class="form-control" id="shoes" placeholder="Section Name" name="shoes" value='<?=$rows['shoes'];?>' >
                     <span id="error7" style="color:red;"></span>
                    </div>        
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                  
                  </form>
                    <a href="manage_otherfee.php"><button class="btn btn-light" style="margin-left: 97px;margin-top: -68px;">Back</button></a>
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
          var ad_form_charge = $('#ad_form_charge').val();
          var book_charge = $('#book_charge').val();
          var uniform = $('#uniform').val();
          var diary = $('#diary').val();
          var icard = $('#icard').val();   
          var bag = $('#bag').val();
          var sweater = $('#sweater').val();
          var shoes = $('#shoes').val();          
          if(ad_form_charge==''){
              	$('#error').html('Please Enter The Text.');
		        return false;
                 }
                  if(book_charge==''){
              	$('#error1').html('Please Enter The Text.');
		        return false;
                 }
                 if(uniform==''){
              	$('#error2').html('Please Enter The Text.');
		        return false;
                 }
                  if(diary==''){
              	$('#error3').html('Please Enter The Text.');
		        return false;
                 }
                 if(icard==''){
              	$('#error4').html('Please Enter The Text.');
		        return false;
                 }
                 if(bag==''){
              	$('#error5').html('Please Enter The Text.');
		        return false;
                 }
                 if(sweater==''){
              	$('#error6').html('Please Enter The Text.');
		        return false;
                 }
                 if(shoes==''){
              	$('#error7').html('Please Enter The Text.');
		        return false;
                 }

            ajax_fun();

    });
    
    
    function ajax_fun() {

        $.ajax({
                type: "POST",
                url: "ajaxotherfee.php",
                data: $('form').serialize(),
                success: function(val) {
                if(val == 0) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Add Class success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    	$('#error').html('');
                      $('#class_name').val(""); 
                      $('#section_name').val(""); 
                      $('#uniform').val(""); 
                      $('#diary').val(""); 
                      $('#icard').val(""); 
                      $('#bag').val(""); 
                      $('#sweater').val(""); 
                      $('#shoes').val("");                       
                      
                }
                if(val == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Edit Class success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
             }
        });
        
    }    
</script>
        
       