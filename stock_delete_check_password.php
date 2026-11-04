<?php
//print_r($_POST['recep_no']);
if (isset($_POST['password']) && $_POST['password'] != "") {
  //echo $_POST['password'];
  if ($_POST['password'] != "@#$5A&*Cr!E") {
    echo "password did not match.";

    ?>
    <script>
      window.alert("password did not match.");
      window.history.back();
    </script>

    <?php exit();
  }
  include('include/dbcon.php');
  $dId = (int) $_POST['dId']; // 1464;
  if ($dId > 0) {
    $dSql = "delete from stock where id=" . $dId;
    $dSqlQry = mysqli_query($myDB, $dSql);
    if ($dSqlQry) { ?>
      <script>
        window.alert("bill delete successfully");
        //window.history.go(-2);
        window.location.href = "stock.php";
      </script>;
    <?php } else {
      echo "error in delete query.";
      exit();
    }
  } else {
    echo "delete id is incorrect.";
    exit();
  }
} ?>

<button onclick="window.history.back()">back</button>
<form id="myForm" method="post" action="">
  <input type="hidden" name="dId" value="<?= $_GET['dId']; ?>">
  Enter password :<input type="password" name="password">
  <input type="submit" />
</form>
<button onclick="document.getElementById('myForm').reset()">reset</button>