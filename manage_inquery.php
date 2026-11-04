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
    $sqlsts = "update rrsv_inquery set status='" . $status . "' where id='" . $id . "'";
    $resSts = mysqli_query($myDB, $sqlsts) or die("Error into change Student  status:" . mysql_error());

    if (mysqli_affected_rows($myDB) >= 1) {
      echo '<script language="javascript" type="text/javascript">';
      echo 'window.location.href="manage_inquery.php?retcode=4;';
      echo '</script>';
    }
  }
}

if (isset($_GET['dId']) && $_GET['dId'] != "") {
  $dId = $myDB->escape_string(trim(addslashes($_GET['dId'])));
  $sql10 = "Delete from rrsv_inquery where from_no='" . $dId . "'";
  $res10 = mysqli_query($myDB, $sql10);
  //die;
  if ($res10) {
    $sql11 = "Delete from  rrsv_scl_pl where bill='" . $dId . "'";
    $res11 = mysqli_query($myDB, $sql11);
    if ($res11) {
      echo '<script language="javascript" type="text/javascript">';

      echo 'window.location.href="manage_inquery.php?";';
      echo '</script>';
    } else {
      echo '<script language="javascript" type="text/javascript">';

      echo 'window.location.href="manage_inquery.php?";';
      echo '</script>';
    }

  } else {
    echo '<script language="javascript" type="text/javascript">';

    echo 'window.location.href="manage_inquery.php?";';
    echo '</script>';
  }

}


?>
<script language="javascript" type="text/javascript">

  function dosearch() {
    document.frmsearch.method = 'post';
    document.frmsearch.action = 'manage_inquery.php';
    document.frmsearch.submit();
    return true;
  }

  /*function confirmdel(id){
    if(confirm("Are you sure to delete this Information?")) {
      window.location.href="manage_inquery.php?mode=del&id="+id;
      return true;
    }
  }*/




  function confirmsearch(id, status) {
    if (status == "Panding") {
      if (confirm("Are you sure to change this Student status?")) {
        //	window.location.href="manage_inquery.php?mode=sts&id="+id+"$status="+status+;
        window.location.href = "manage_inquery.php?mode=sts&id=" + id + "&status=" + status;
        //	window.location.href="manage_inquery.php?mode=sts&id="+id+"&status="+status+'&page=<?= $current_page ?>';
        return true;
      }
    } else {
      alert("This student is already admission.");
    }
  }
</script>
<link href='libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="libray/js/jquery-3.3.1.min.js"></script>
<script language="javascript" type="text/javascript">



  function confirmdel(dId) {
    if (confirm("Are you sure to delete this Information?")) {
      //alert(dId);
      window.location.href = "password_check.php?dId=" + dId;
      return true;
    }
  }
</script>


<div class="main-panel">
  <div class="content-wrapper">

    <div class="card">
      <div class="card-body">
        <h4 class="card-title"> <a href='add_inquery.php'><button type="button"
              class="btn btn-primary btn-rounded btn-fw"><i class="mdi mdi-comment-plus-outline"></i> Admission Form
              Sell</button></a></h4>
      </div>
    </div>


    <div class="col-12 grid-margin">
      <div class="card">
        <div>
          <!-- Table -->
          <form name="frmsearch" method="post" action="manage_inquery.php">
            <table id='empTable' class='display dataTable'>
              <thead>
                <tr>
                  <td class="text" align="center" colspan="12" valign="top">


                    <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                    <!--  <option value="">-Select a Section-</option>-->

                    <!--</select>-->
                    <input type="text" name="txtsearch" class="form-control" value="" size="30" maxlength="100"
                      placeholder="Search Form No" style="
    width: 231px;
    margin-left: 305px;
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
                </tr>
          </form>
          <tr>
            <th>SL. No</th>
            <th>Form No</th>
            <th>Student Name</th>
            <th>Phone No</th>
            <th>Class Name</th>
            <th>Date of Sell</th>
            <th>Action</th>
            <th>Status</th>
            <th>Whatsapp</th>

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
          $perpage = 100;
          $lowerlimit = ($page - 1) * $perpage;

          $sql = "select * from rrsv_inquery where 1 ";
          //$sql="select a.*,b.class_name from rrsv_student_registration as a,class as b where a.scl_class=b.id ";
          
          if ($txtsearch != "") {
            $sql .= " and (from_no LIKE '%$txtsearch%')";
            // $sql.=" and (from_no='".$txtsearch."')";
          
          }
          //     if($txtsearch!="")
          //      {
          //  echo   $sql.=" and (scl_reg_no LIKE '%$txtsearch%' )";
          
          //      }
          


          $result = mysqli_query($myDB, $sql);
          $totalrecord = mysqli_num_rows($result);
          $totalpage = ceil($totalrecord / $perpage);
          $sql .= "  order by id desc  limit $lowerlimit,$perpage";
          $result1 = mysqli_query($myDB, $sql);
          $l = mysqli_num_rows($result1);
          $result = mysqli_query($myDB, $sql);

          if ($l > 0) {

            while ($rows = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {


              $c++;
              $id = $rows['id'];
              $status = $rows['status'];
              $phone = $rows['phone'];
              ?>

              <tr>
                <td class="text" style="padding-left:10px;" valign="center">#<?= $rows['id']; ?></td>


                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['from_no']; ?></td>
                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['name']; ?></td>
                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['phone']; ?></td>
                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['class_name']; ?></td>
                <td class="text" valign="center" style="padding-left:10px;"><?= date('d-m-Y', strtotime($rows['d_o_i'])); ?>
                </td>
                <!--<td  class="text" valign="center" style="padding-left:10px;"><?= $rows['add_status']; ?></td>-->
                <td class="text" valign="center">

                  <a class="btn btn-warning" href="add_inquery.php?id=<?= $rows['id']; ?>"
                    title="Click to Edit/Re Admission" class="btn btn-warning">Edit
                  </a>
                  <a onclick="confirmdel('<?= $rows['from_no']; ?>');" title="delete" class="btn btn-info">Delete</a>
                </td>
                <td class="text" valign="center" style="padding-left:10px;">
                  <a href="#" onclick="javascript:confirmsearch(<?php echo $id; ?>,'<?php echo $status; ?>');"
                    title="Click to change this register status" class="text">
                    <i data="<?php echo $id; ?>"
                      class="status_checks btn <?php if ($status == 'Panding') { ?>btn-danger<?php } else { ?>btn-success<?php } ?>"
                      style="
    
">     <?php echo $status; ?>
                    </i></a>
                </td>

                <td class="text" valign="center" style="padding-left: -50px;">

                  <a href="inquery.php?id=<?= $rows['id']; ?>" title="Click to Print this Admission from"
                    class="btn btn-warning " target="_blank" style="
    background-color: #1a9730;border-color: #1f4626;">Print </a>
                </td>
                <td class="text" valign="center" style="padding-left: -50px;">

                  <a href="https://wa.me/+91<?php echo $phone; ?>?text=Hi, Welcome to RASULPUR RAMKRISHNA SARADA VIDYAPITH.
 Dear student Your Form No is: <?= nl2br($rows['from_no']); ?>
 
                                                                                                                            Admission Form Amount: <?= $rows['price']; ?>"
                    target="_blank" class="btn btn-success"><i class="mdi mdi-whatsapp"></i></a>
                </td>
              </tr>



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

          <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
              <li class="page-item disabled">
                <?php

                if ($page > 1) {
                  ?>
                <li class="page-item"> <a class="page-link" href="manage_inquery.php?page=<?= ($page - 1); ?>"
                    tabindex="-1">Previous</a>
                </li>
                <?php
                }
                ?>
              <?php
              for ($i = 1; $i <= $totalpage; $i++) {
                ?>
                <li class="page-item"><a class="page-link" href="manage_inquery.php?page=<?= $i; ?>"><?php echo $i ?></a>
                </li>
                <?php
              }
              ?>

              <?php
              if ($totalpage > $page) {
                ?>
                <li class="page-item">
                  <a class="page-link" href="manage_inquery.php?page=<?= ($page + 1); ?>">Next</a>
                </li>
                <?php
              }

              ?>
            </ul>
          </nav>
        </div>
      </div>
    </div>






    <?php
    include('include/footer.php');
    ?>