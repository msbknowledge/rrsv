<style>
  /*Required*/
  @media (max-width: 576px) {
    .modal-dialog.modal-dialog-slideout {
      width: 80%
    }
  }

  .modal-dialog-slideout {
    min-height: 100%;
    margin: 0 auto 0 0;
    background: #fff;
  }

  .modal.fade .modal-dialog.modal-dialog-slideout {
    -webkit-transform: translate(-100%, 0);
    transform: translate(-100%, 0);
  }

  .modal.fade.show .modal-dialog.modal-dialog-slideout {
    -webkit-transform: translate(0, 0);
    transform: translate(0, 0);
    flex-flow: column;
  }

  .modal-dialog-slideout .modal-content {
    border: 0;
  }

  .modal-header h4 {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 1px;
  }

  .view_details_ul {
    list-style: none;
  }

  .view_details_ul li {
    display: block;
    color: #6a2414;
    font-size: 20px;
  }

  .view_details_ul li span {
    padding: 0px 50px;
  }
</style>
<?php
include('include/header.php');
include('include/dbcon.php');
$retcode = 0;
$msg = "";
if (isset($_GET['retcode'])) {
  $retcode = $myDB->escape_string(trim(addslashes($_GET['retcode'])));
}

if ($retcode == 1) {
  $msg = "Stock has been editted successfully";
}

if ($retcode == 2) {
  $msg = "Stock has been add successfully";
}

if ($retcode == 3) {
  $msg = "Stock has been deleted successfully";
}
if (isset($_GET['dId']) && !empty($_GET['dId'])) {

  $sql = "Delete from stock_master where id='" . (int) $_GET['dId'] . "'";
  $res = mysqli_query($myDB, $sql);
  if ($res) { ?>

    <script>;
      alert('Stock Delete successfully!');
      window.location.replace('manage_stock.php?');
    </script>;
  <?php } ?>



<?php } ?>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="libray/js/jquery-3.3.1.min.js"></script>



<div class="main-panel">
  <div class="content-wrapper">

    <div class="card">
      <div class="card-body">
        <h4 class="card-title"> <a href='add_publishers.php'><button type="button"
              class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Add
              Publishers</button></a></h4>

      </div>
      <h4><?php echo $msg; ?></h4>
    </div>


    <div class="col-12 grid-margin">
      <div class="card">
        <div>
          <!-- Table -->
          <table id='empTable' class='display dataTable'>
            <thead>
              <tr>
                <th>SL.No</th>
                <th>Stock Head </th>
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
    (function ($) {
      'use strict';
      $(function () {
        $('.file-upload-browse').on('click', function () {
          var file = $(this).parent().parent().parent().find('.file-upload-default');
          file.trigger('click');
        });
        $('.file-upload-default').on('change', function () {
          $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        });
      });
    })(jQuery);


    $(document).ready(function () {
      $('#empTable').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': 'ajaxpublishers.php'
        },
        'columns': [
          { data: 'sl_no' },
          { data: 'name' },
          { data: 'action' },

        ]
        //  success: function(data) {
        //   alert(data)  
        //  };
      });
    });



    $('form').submit(function (e) {
      e.preventDefault();
      var full_name = $('#full_name').val();
      if (full_name == '') {
        $('#full_name_error').html('Please Enter Full Name.');
        return false;
      }

      $.ajax({
        type: "POST",
        url: "add_publishers_post.php",
        data: $('form').serialize(),
        success: function (data) {

          if (data = "Insert") {

            $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Add Class success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

          }
          if (data = "Update") {

            $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Edit Class success!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

          }
        }

      });

    });


  </script>