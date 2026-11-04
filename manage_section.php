<?php
include('include/header.php');

if(isset($_GET['dId']) && !empty($_GET['dId'])) {
    include('include/dbcon.php');
    $sql="Delete from rrsv_section where id='".(int)$_GET['dId']."'";
    $res=mysqli_query($myDB,$sql);
    if($res) { ?>
       
        <script>;
            alert('SectionInformation Delete successfully!');
            window.location.replace('http://rrsv.in/manage_section.php?');
        </script>;
    <?php  } ?>



<?php } ?>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

        <!-- jQuery Library -->
        <script src="libray/js/jquery-3.3.1.min.js"></script>
        
        
        
<div class="main-panel">
        <div class="content-wrapper">

            <div class="card">
                <div class="card-body">
                  <h4 class="card-title"> <a href='add_section.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add Section</button></a></h4>
                   </div>
            </div>
                  
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
            <!-- Table -->
            <table id='empTable' class='display dataTable'>
                <thead>
                <tr>
                    <th>SL.No</th>
                    <th>Class Name</th>
                    <th>Section Name</th>
                    <th>Action</th>
                </tr>
                </thead>

            </table>
        </div>
              </div>
            </div>
            
            
         </div>

          
          
      
          
        <?php
include('include/footer.php');
?>
<!-- Datatable JS -->
        <script src="libray/DataTables/datatables.min.js"></script>
<script>
(function($) {
    'use strict';
    $(function() {
      $('.file-upload-browse').on('click', function() {
        var file = $(this).parent().parent().parent().find('.file-upload-default');
        file.trigger('click');
      });
      $('.file-upload-default').on('change', function() {
        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
      });
    });
  })(jQuery);
  
  
        $(document).ready(function(){
            $('#empTable').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'ajaxsection.php'
                },
                'columns': [
                    { data: 'sl_no' },
                    { data: 'class_name' },
                    { data: 'section_name' },
                    { data: 'action' },
                   
                ]
                //  success: function(data) {
                //   alert(data)  
                //  };
            });
        });
      
         $('#mm').on('click', function(){
            var uniqueId = $(this).attr("data-deleteIid");
            alert(uniqueId);
            return false;
            
        });  
        
   $('form').submit(function(e){
        e.preventDefault();
        var full_name = $('#full_name').val();
        if(full_name == ''){
          $('#full_name_error').html('Please Enter Full Name.');
		  return false;
        }
            
        $.ajax({
            type: "POST",
            url: "add_section_post.php",
            data: $('form').serialize(),
            success: function(data) {
             
              if(data="Insert") {
                
                $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Add Class success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                 
                }
                  if(data="Update") {
                
                $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Edit Class success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                  
                }
         }
            
        });

    });
    
    
</script>
        