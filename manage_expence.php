<?php
error_reporting(1);
include('include/header.php');
include('include/dbcon.php');

$ret1code = "";
$id = 0;
$txtsearch = "";
$status = "";
$sid = 0;
$did = 0;
if (isset($_GET['page'])) {
  $current_page = $_GET['page'];
} else {
  $current_page = "";
}
if (isset($_GET['id'])) {
  $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
}

if (isset($_POST['txtsearch'])) {
  $txtsearch = $myDB->escape_string(trim(addslashes($_POST['txtsearch'])));
}
if (isset($_POST['scl_session'])) {
  $scl_session = $myDB->escape_string(trim(addslashes($_POST['scl_session'])));
}
if (isset($_POST['scl_class'])) {
  $scl_class = $myDB->escape_string(trim(addslashes($_POST['scl_class'])));
}

if (isset($_POST['scl_section'])) {
  $scl_section = $myDB->escape_string(trim(addslashes($_POST['scl_section'])));
}

if (isset($_GET['retcode'])) {
  $retcode = $myDB->escape_string(trim(addslashes($_GET['retcode'])));
}

if ($retcode == 1) {
  $msg = "course has been editted successfully";
}


if ($retcode == 3) {
  $msg = "Organization has been deleted successfully";
}
if ($retcode == 4) {
  $msg = "Organization has been Inactive successfully";
}


if (isset($_GET['mode'])) {
  $mode = $myDB->escape_string($_GET['mode']);
}

if (isset($_GET['status'])) {
  $status = $myDB->escape_string($_GET['status']);
}
if ($id > 0) {
  if ($mode == 'sts') {
    if (trim($status) == 'Panding')
      $status = 'Admission';
    else
      $status = 'Panding';
    $current = date('Y-m-d');
    $sqlsts = "update rrsv_expence set status='" . $status . "' where id='" . $id . "'";
    $resSts = mysqli_query($myDB, $sqlsts) or die("Error into change Student  status:" . mysql_error());

    if (mysqli_affected_rows($myDB) >= 1) {
      echo '<script language="javascript" type="text/javascript">';
      echo 'window.location.href="manage_expence.php?retcode=4;';
      echo '</script>';
    }
  }
}

?>
<script language="javascript" type="text/javascript">

  function dosearch() {
    document.frmsearch.method = 'post';
    document.frmsearch.action = 'manage_expence.php';
    document.frmsearch.submit();
    return true;
  }

  /*function confirmdel(id){
    if(confirm("Are you sure to delete this Information?")) {
      window.location.href="manage_expence.php?mode=del&id="+id;
      return true;
    }
  }*/




  function confirmsearch(id, status) {
    if (confirm("Are you sure to change this Student status?")) {
      //	window.location.href="manage_expence.php?mode=sts&id="+id+"$status="+status+;
      window.location.href = "manage_expence.php?mode=sts&id=" + id + "&status=" + status;
      //	window.location.href="manage_expence.php?mode=sts&id="+id+"&status="+status+'&page=<?= $current_page ?>';
      return true;
    }
  }
</script>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="libray/js/jquery-3.3.1.min.js"></script>



<div class="main-panel">
  <div class="content-wrapper">

    <div class="card">
      <div class="card-body">
        <h4 class="card-title"> <a href='add_expence.php'><button type="button"
              class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i>Add
              Expense</button></a></h4>
      </div>
    </div>


    <div class="col-12 grid-margin">
      <div class="card">
        <div>
          <!-- Table -->
          <form name="frmsearch" method="post" action="manage_expence.php">
            <table id='empTable' class='display dataTable'>
              <thead>
                <tr>
                  <td class="text" align="center" colspan="12" valign="top">

                    <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                    <!--  <option value="">-Select a Section-</option>-->

                    <!--</select>-->
                    <input type="text" name="txtsearch" class="form-control" value="" size="30" maxlength="100"
                      placeholder="Search By Expence Name" style="
    width: 231px;
    margin-left: 355px;
    margin-top: 4px;
">
                    <div class="butt" style="
    margin-top: -46px;
    margin-left: 796px;
">
                      <button type="submit" value="" class="btn btn-primary button" valign="center">Search</button>
                      <button type="submit" value="" name="Reset" class="btn btn-primary buttonr"
                        valign="center">Refresh</button>
                    </div>
          </form>
          <tr>
            <th>SL. No</th>

            <th>Expence Head</th>
            <th>Date of Expence</th>
            <th>Amount</th>
            <th>Details</th>
            <th>Photo</th>

          </tr>
          </thead>
          <?php
          $bgcolor = "";
          $c = 0;


          if (isset($_GET['page'])) {
            $page = $_GET['page'];
          } else {
            $page = 1;
          }
          $perpage = 50;
          $lowerlimit = ($page - 1) * $perpage;

          $sql = "select * from rrsv_expence where 1 ";
          //$sql="select a.*,b.class_name from rrsv_student_registration as a,class as b where a.scl_class=b.id ";
          
          if ($txtsearch != "") {
            $sql .= " and (expence_name LIKE '%$txtsearch%')";

          }
          //     if($txtsearch!="")
          //      {
          //  echo   $sql.=" and (scl_reg_no LIKE '%$txtsearch%' )";
          
          //      }
          


          $result = mysqli_query($myDB, $sql);
          $l = mysqli_num_rows($result);

          if ($txtsearch == "") {
            $totalrecord = mysqli_num_rows($result);
            $totalpage = ceil($totalrecord / $perpage);
            $sql .= "  order by id desc limit $lowerlimit,$perpage";
            // echo $sql;
            $result = mysqli_query($myDB, $sql);
            $l = mysqli_num_rows($result);
            $result = mysqli_query($myDB, $sql);
          }
          //  print_r($result);
          // echo $i; //die;
          if ($l > 0) {

            while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {


              $c++;
              $id = $rows['id'];
              $status = $rows['status'];
              ?>

              <tr>
                <td class="text" style="padding-left:10px;" valign="center">#<?= $rows['id']; ?></td>


                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['expence_name']; ?></td>


                <td class="text" valign="center" style="padding-left:10px;"><?= date('d-m-Y', strtotime($rows['d_o_e'])); ?>
                </td>
                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['amount']; ?></td>
                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['exp_desc']; ?></td>


                <td class="text" valign="center" style="padding-left: -50px;">


                  <a href="add_expence.php?id=<?= $rows['id']; ?>" title="Click to Edit" class="btn btn-warning "
                    style="background-color:rgb(81, 78, 207);border-color:rgb(126, 163, 132);">Edit
                  </a>
                  <a href="print_exp.php?id=<?= $rows['id']; ?>" title="Click to Print" class="btn btn-warning "
                    target="_blank" style="background-color:rgb(51, 201, 51);border-color: #1f4626;">Print
                  </a>
                  <?php if ($rows['image'] && $rows['image'] != "") { ?>
                    <a href="expence_image/<?= $rows['image']; ?>" class="text" target="_blank"><b>Vouture </b></a>
                  <?php } ?>
                </td>
              </tr>

              <tr>

              </tr>
              <?php

            }
          } else {
            echo "<tr>";
            echo "<td class='errtext' align='center' colspan=10>Records Not Found</td>";
            echo "</tr>";
          }

          ?>


          </table>

          <?php if ($txtsearch == "") { ?>

            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                  <?php

                  if ($page > 1) {
                    ?>
                  <li class="page-item"> <a class="page-link" href="manage_expence.php?page=<?= ($page - 1); ?>"
                      tabindex="-1">Previous</a>
                  </li>
                  <?php
                  }
                  ?>
                <?php
                for ($i = 1; $i <= $totalpage; $i++) {
                  ?>
                  <li class="page-item"><a class="page-link" href="manage_expence.php?page=<?= $i; ?>"><?php echo $i ?></a>
                  </li>
                  <?php
                }
                ?>

                <?php
                if ($totalpage > $page) {
                  ?>
                  <li class="page-item">
                    <a class="page-link" href="manage_expence.php?page=<?= ($page + 1); ?>">Next</a>
                  </li>
                  <?php
                }

                ?>
              </ul>
            </nav>
          <?PHP } ?>
        </div>
      </div>
    </div>






    <?php
    include('include/footer.php');
    ?>