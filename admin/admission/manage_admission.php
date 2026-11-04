<?php
error_reporting(1);
include('../../include/header.php');
include('../../include/dbcon.php');

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

if (isset($_GET['id_status'])) {
    $status = $myDB->escape_string($_GET['id_status']);
}
if ($id > 0) {
    if ($mode == 'sts') {
        if (trim($status) == 'Panding')
            $status = 'Approved';
        // else
        //     $status = 'Panding';

        $current = date('Y-m-d');
        $sqlsts = "update rrsv_admission set id_status='" . $status . "',s_a_d='" . $current . "' where id='" . $id . "'";

        $resSts = mysqli_query($myDB, $sqlsts) or die("Error into change Student  status:" . mysql_error());
        // If status approved → insert into rrsv_student_registration
        $sqlSelect = "SELECT * FROM rrsv_admission WHERE id = '$id'";
        $res = mysqli_query($myDB, $sqlSelect);
        $row = mysqli_fetch_assoc($res);

        if ($row && $row['id_status'] == 'Approved' && $resSts) {

            $sqlInsert = "
                INSERT INTO rrsv_student_registration (
                    scl_roll_no, scl_reg_no, scl_name, scl_aadhar, scl_father_name,
                    scl_father_quli, scl_father_occu, scl_father_inc, scl_mother_name,
                    scl_mother_quli, scl_mother_occu, scl_mother_inc, scl_dob, scl_date,
                    scl_address, scl_nationality, scl_phone_no, scl_religion, scl_gender,
                    scl_class, scl_session, scl_section, scl_car, scl_category, guardian_signature,
                    bank_name, branch_name, bank_ac_no, bank_ifsc_code, dob_image, father_voter_image,
                    father_aadhar_image, mother_voter_image, mother_aadhar_image, passbook_image,
                    image, id_status, print_issue, scl_validate, scl_date_issue, scl_pre_name,
                    scl_pre_class, scl_tcn, scl_tc_date, status, exp_date, scl_blood, scl_dist,
                    scl_po, admission_due, due_month_Ins, due_month_Ins_car, due_admission_ins,
                    paid_month_count, paid_car_rent_count, paid_admission_count, monthly_fee,
                    yearly_due, car_due, pre_due, father_phone_no, aadhar_image, add_status,
                    totalmonthly_fee_val, dueamount, total_cost, scl_location, scl_block,
                    scl_email, scl_bpl, scl_disa, scl_language, scl_ide, scl_pos, scl_pol,
                    scl_dest, alt_phone, scl_mu, scl_state, scl_pin, s_a_d, diaryval, bagval,
                    shoessockesval, icardval, uniformval, bookcost, copycost, paymentrecive,
                    whole_year, admissionval, add_total_cost, re_total_cost, admission_fee_val,
                    re_admission_fee_val, shoes_sockes_val, carfee, sweaterslaxcap_val, car_cost,
                    total_monthly_fee, admission_cost1, admission_cost, total_book, total_copy,
                    amount1, advance_due, val_ad_du, nationality, religion
                )
                SELECT
                    scl_roll_no, scl_reg_no, scl_name, scl_aadhar, scl_father_name,
                    scl_father_quli, scl_father_occu, scl_father_inc, scl_mother_name,
                    scl_mother_quli, scl_mother_occu, scl_mother_inc, scl_dob, scl_date,
                    scl_address, scl_nationality, scl_phone_no, scl_religion, scl_gender,
                    scl_class, scl_session, scl_section, scl_car, scl_category, guardian_signature,
                    bank_name, branch_name, bank_ac_no, bank_ifsc_code, dob_image, father_voter_image,
                    father_aadhar_image, mother_voter_image, mother_aadhar_image, passbook_image,
                    image, 'Panding' AS id_status, print_issue, scl_validate, scl_date_issue,
                    scl_pre_name, scl_pre_class, scl_tcn, scl_tc_date, status, exp_date,
                    scl_blood, scl_dist, scl_po, admission_due, due_month_Ins, due_month_Ins_car,
                    due_admission_ins, paid_month_count, paid_car_rent_count, paid_admission_count,
                    monthly_fee, yearly_due, car_due, pre_due, father_phone_no, aadhar_image,
                    add_status, totalmonthly_fee_val, dueamount, total_cost, scl_location,
                    scl_block, scl_email, scl_bpl, scl_disa, scl_language, scl_ide, scl_pos,
                    scl_pol, scl_dest, alt_phone, scl_mu, scl_state, scl_pin, s_a_d, diaryval,
                    bagval, shoessockesval, icardval, uniformval, bookcost, copycost, paymentrecive,
                    whole_year, admissionval, add_total_cost, re_total_cost, admission_fee_val,
                    re_admission_fee_val, shoes_sockes_val, carfee, sweaterslaxcap_val, car_cost,
                    total_monthly_fee, admission_cost1, admission_cost, total_book, total_copy,
                    amount1, advance_due, val_ad_du, nationality, religion
                FROM rrsv_admission
                WHERE id = '$id'
            ";

            mysqli_query($myDB, $sqlInsert) or die("Error copying record: " . mysqli_error($myDB));
        }


        if (mysqli_affected_rows($myDB) >= 1) {
            echo '<script language="javascript" type="text/javascript">';
            echo 'window.location.href="manage_admission.php?retcode=4;';
            echo '</script>';
        }
    }
}

?>
<script language="javascript" type="text/javascript">

    function dosearch() {
        document.frmsearch.method = 'post';
        document.frmsearch.action = 'manage_admission.php';
        document.frmsearch.submit();
        return true;
    }

    /*function confirmdel(id){
          if(confirm("Are you sure to delete this Information?")) {
              window.location.href="manage_admission.php?mode=del&id="+id;
              return true;
          }
      }*/




    function confirmsearch(id, id_status) {
        if (confirm("Are you sure to change this Student status?")) {
            window.location.href = "manage_admission.php?mode=sts&id=" + id + "&id_status=" + id_status;
            return true;
        }
    }
</script>
<link href='<?= BASE_URL ?>libray/DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

<!-- jQuery Library -->
<script src="<?= BASE_URL ?>libray/js/jquery-3.3.1.min.js"></script>



<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">

            </div>
        </div>


        <div class="col-12 grid-margin">
            <div class="card">
                <div>
                    <!-- Table -->
                    <form name="frmsearch" method="post" action="manage_admission.php">
                        <table id='empTable' class='display dataTable'>
                            <thead>
                                <tr>
                                    <td class="text" align="center" colspan="12" valign="top">
                                        <select name="scl_session" class="form-control"
                                            value="<?= $rows['scl_session']; ?>" id="scl_session" style="
    width: 240px;
    margin-left: -703px;
">
                                            <option value="">-Select a Session -</option>
                                            <?php
                                            for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                                echo '<option value="' . $i . '">' . $i . '</option>' . PHP_EOL;
                                            }

                                            ?>
                                        </select>
                                        <select name="scl_class" class="form-control" id="scl_class"
                                            style="margin-top: -44px;width: 269px;margin-left: -168px;">
                                            <option value="">-Select a Class-</option>
                                            <?php
                                            $id = 0;
                                            $sql = "select * from rrsv_class order by id";
                                            $res = mysqli_query($myDB, $sql);
                                            while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                                                ?>
                                                <option value="<?php echo $obj['class_name']; ?>" <?php if (trim($rows['class_name'] == $obj['class_name']))
                                                       echo "selected"; ?>>
                                                    <?php echo $obj['class_name']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <!--<select name="scl_section" class="secinput" value="" id="scl_section">-->
                                        <!--  <option value="">-Select a Section-</option>-->

                                        <!--</select>-->
                                        <input type="text" name="txtsearch" class="form-control" value="" size="30"
                                            maxlength="100" placeholder="Search By Name, Reg No" style="
    width: 231px;
    margin-left: 355px;
    margin-top: -44px;
">
                                        <div class="butt" style="
    margin-top: -46px;
    margin-left: 796px;
">
                                            <button type="submit" value="" class="btn btn-primary button"
                                                valign="center">Search</button>
                                            <button type="submit" value="" name="Reset" class="btn btn-primary buttonr"
                                                valign="center">Refresh</button>
                                        </div>
                                </tr>
                    </form>
                    <tr>
                        <th>SL. No</th>
                        <th>Photo</th>
                        <th>Reg. No</th>
                        <th>Session</th>
                        <th>Student Name</th>
                        <th>Class Name</th>
                        <th>Section</th>
                        <th>Admission Status</th>
                        <th>Status</th>
                        <th>Actions</th>
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

                    $sql = "select * from rrsv_admission where id_status='Panding'";
                    //$sql="select a.*,b.class_name from rrsv_admission as a,class as b where a.scl_class=b.id ";
                    
                    if ($txtsearch != "") {
                        $sql .= " and (scl_name LIKE '%$txtsearch%')";

                    }
                    if ($txtsearch != "") {
                        $sql .= " or (scl_reg_no ='" . $txtsearch . "' )";

                    }
                    if ($scl_session != "") {
                        $sql .= " and (scl_session='" . $scl_session . "')";

                    }
                    if ($scl_class != "") {
                        $sql .= " and (scl_class='" . $scl_class . "')";

                    }
                    //  if($scl_section!="")
                    //                  {
                    //                  $sql.=" or (scl_section LIKE '%$scl_section%')";
                    
                    //                  }
                    /*if($luid!='1')
                    {
                    $sql.=" and (created_user='$luid' or updated_user='$luid')";
                    }*/



                    $result = mysqli_query($myDB, $sql);
                    $totalrecord = mysqli_num_rows($result);
                    $totalpage = ceil($totalrecord / $perpage);
                    $sql .= "  order by id desc limit $lowerlimit,$perpage";
                    $result1 = mysqli_query($myDB, $sql);
                    $l = mysqli_num_rows($result1);
                    $result = mysqli_query($myDB, $sql);

                    if ($l > 0) {

                        while ($rows = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {


                            $c++;
                            $id = $rows['id'];
                            $id_status = $rows['id_status'];
                            $alt_phone = $rows['alt_phone'];
                            ?>

                            <tr>
                                <td class="text" style="padding-left:10px;" valign="center">#<?= $rows['id']; ?></td>
                                <td class="text" valign="center">
                                    <?php
                                    if ($image <> " ") {
                                        ?>
                                        <img src="../../student_reg_image/<?= $rows['image']; ?>" width="60" height="60" />
                                        <?php
                                    } else {
                                        echo "No Image";
                                    }
                                    ?>
                                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['scl_reg_no']; ?></td>
                                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['scl_session']; ?></td>
                                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['scl_name']; ?></td>
                                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['scl_class']; ?></td>
                                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['scl_section']; ?></td>
                                <td class="text" valign="center" style="padding-left:10px;"><?= $rows['add_status']; ?></td>
                                <td valign="center" style="padding-left:10px;">
                                    <?php if ($id_status == 'Panding'){?>
                                    <a href="#"
                                        onclick="javascript:confirmsearch(<?php echo $id; ?>,'<?php echo $id_status; ?>');"
                                        title="Click to change this register status" class="text">
                                        <i data="<?php echo $id; ?>"
                                            class="btn <?php if ($id_status == 'Panding') { ?>btn-info<?php } elseif ($id_status == 'Rejected') { ?>btn-danger<?php } else { ?>btn-success<?php } ?>"
                                            style="
    
">         <?php echo $id_status; ?>
                                        </i></a>
                                        <?php }else{?>
                                            <a href="#" class="btn btn-success"> <?php echo $id_status; ?></a>
                                         <?php }?>
                                </td>

                                <td class="text" valign="center" style="padding-left: -50px;">



                                    <!-- <a href="#" onclick="confirmdel(<?= $rows['id']; ?>);" title="Click to delete this Admission"  class="btn btn-danger"><i class="icon_close_alt2"></i></a>-->

                                    <a href="viewreg.php?id=<?= $rows['id']; ?>" title="Click to View this Admission Details"
                                        class="btn btn-success v" style="
    background-color: #bf4c15;
    border-color: #bf4c15;
">View/Edit</a>
                                   

                                </td>
                            </tr>

                            <tr>

                            </tr>
                            <?php

                        }
                    } else {
                        echo "<tr>";
                        echo "<td class='errtext' align='center' colspan=6>Records Not Found</td>";
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
                                <li class="page-item"> <a class="page-link"
                                        href="manage_admission.php?page=<?= ($page - 1); ?>" tabindex="-1">Previous</a>
                                </li>
                                <?php
                                }
                                ?>
                            <?php
                            for ($i = 1; $i <= $totalpage; $i++) {
                                ?>
                                <li class="page-item"><a class="page-link"
                                        href="manage_admission.php?page=<?= $i; ?>"><?php echo $i ?></a></li>
                                <?php
                            }
                            ?>

                            <?php
                            if ($totalpage > $page) {
                                ?>
                                <li class="page-item">
                                    <a class="page-link" href="manage_admission.php?page=<?= ($page + 1); ?>">Next</a>
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
        include('../../include/footer.php');
        ?>