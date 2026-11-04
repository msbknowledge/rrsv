 <!-- jQuery Library -->
        <script src="libray/js/jquery-3.3.1.min.js"></script>
<?php
include('include/header.php');
if(isset($_GET['dId']) && !empty($_GET['dId'])) {
    include('include/dbcon.php');
    $sql="Delete from rrsv_teacher where id='".(int)$_GET['dId']."'";
    $res=mysqli_query($myDB,$sql);
    if($res) { ?>
       
        <script>;
            alert('TeacherInformation Delete successfully!');
           // window.location.replace('http://rasulpuranathsamitykgschool.com/template/manage_teacher.php?');
           window.history.back();
        </script>;
    <?php  } ?>



<?php } ?>
    
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<div class="main-panel">
        <div class="content-wrapper">

            <div class="card">
                <div class="card-body">
                  <h4 class="card-title"> <a href='add_teachers.php'><button type="button" class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Teachers</button></a></h4>
                   </div>
            </div>
               
          
          <div class="col-12 grid-margin">
              <div class="card">
                    <div >
                        <div id="alert2"></div>   
            <!-- Table -->
            <table id='empTable1' class='display dataTable'>
                <thead>
                <tr>
                    <th>Employee Image</th>
                    <th>Employee name</th>
                    <th>Password</th>
                    <th>Mobile No</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Salary</th>
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

function dlte(id) {
   // alert(id);
    if (confirm('Are  you sure want delete this record?')) {
        window.location.href='manage_teacher.php?dId='+id;
    }
}

  
  
        $(document).ready(function(){
            $('#empTable1').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':'ajaxfile.php'
                },
                'columns': [
                    { data: 'emp_image' },
                    { data: 'emp_name' },
                    { data: 'password' },
                    { data: 'phone_no' },
                    { data: 'email' },
                    { data: 'gender' },
                    { data: 'salary' },
                    { data: 'action' },
                ]
            });
        });
      
      
        
  
    
</script>
        