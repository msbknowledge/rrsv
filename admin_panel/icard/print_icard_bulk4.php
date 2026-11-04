<?php
include('../dbcon.php');

$student_ids = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];
if (empty($student_ids)) {
    die("<h3 style='text-align:center;color:red;'>No students selected.</h3>");
}

$ids = implode(',', array_map('intval', $student_ids));
$sql = "SELECT * FROM rrsv_student_registration WHERE id IN ($ids)";
$result = mysqli_query($con, $sql);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ID Cards - Multiple (4 per Row)</title>
<link rel="stylesheet" type="text/css" href="../../css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../libray/css/idcard.css">
<style>
.button {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 10px 24px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  margin: 10px 2px;
  cursor: pointer;
}
@page {
  size: A4;
  margin: 5mm;
}
body {
  background: #fff;
  margin: 10mm;
}
.cards-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.card-wrapper {
  width: 23%; /* 4 per row (100 / 4 - small gap) */
  margin-bottom: 15px;
  position: relative;
  page-break-inside: avoid;
  display: inline-block;
}
.admit-card {
  position: relative;
  width: 100%;
  aspect-ratio: 300 / 480; /* keep same proportion */
  overflow: hidden;
}
.admit-card img.bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.po10 img {
  border: 2px solid #fff;
  border-radius: 5px;
}
@media print {
  #printPageButton, #backPageButton {
    display: none;
  }
  body {
    margin: 0;
  }
}
</style>
</head>

<body>
  <div style="text-align:center;">
    <button type="button" id="backPageButton" class="button">
      <a style="color: #fff;text-decoration:none;" href="manage_icard.php">Back</a>
    </button>
    <button type="button" onclick="window.print()" class="button" id="printPageButton">Print</button>
  </div>

  <section class="cards-container">
    <?php while ($rows = mysqli_fetch_assoc($result)) { ?>
      <div class="card-wrapper">
        <div class="admit-card">
          <img src="../../libray/images/icardfinal.jpg" alt="Background" class="bg">
          <p class="po10"><img src="../../student_reg_image/<?= $rows['image']; ?>" alt="" width="80px"></p>
          <p class="po1"><?= htmlspecialchars($rows['scl_name']); ?></p>
          <p class="po2"><?= htmlspecialchars($rows['scl_father_name']); ?></p>
          <p class="po3"><?= htmlspecialchars($rows['scl_class']); ?></p>
          <p class="po5"><?= date('d-m-Y', strtotime($rows['scl_dob'])); ?></p>
          <p class="po6"><?= htmlspecialchars($rows['scl_blood']); ?></p>
          <p class="po8"><?= htmlspecialchars($rows['scl_address']); ?></p>
          <p class="po11" style="position:absolute;top:77%;left:176px;font-weight:bold;color:#151515;font-size:11px;">
            <?= htmlspecialchars($rows['scl_pos']); ?>
          </p>
          <p class="po9"><?= htmlspecialchars($rows['scl_phone_no']); ?></p>
          <p class="po4"><?= htmlspecialchars($rows['scl_pin']); ?></p>
          <p class="po7"><?= htmlspecialchars($rows['scl_dist']); ?></p>
        </div>
      </div>
    <?php } ?>
  </section>
</body>
</html>
