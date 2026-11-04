<?php
include('../dbcon.php');

$student_ids = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];
if (empty($student_ids)) {
    die("<h3 style='text-align:center;color:red;'>No students selected.</h3>");
}

// --- SAFER SQL using prepared statement ---
$placeholders = implode(',', array_fill(0, count($student_ids), '?'));
$stmt = $con->prepare("SELECT * FROM rrsv_student_registration WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($student_ids)), ...$student_ids);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ID Cards - Multiple (3per Row)</title>
<link rel="stylesheet" href="../../css/bootstrap.css">
<link rel="stylesheet" href="../../../libray/css/idcard.css">
<style>

section{
	margin: 30px;
	margin-left:70px;
	width:1600px;
}

.txt-center {
    text-align: center;
}

.idcard{position:relative;}

.img img{position:absolute;left:84%;top:88%;}

/*.border- {
    border: 1px solid #000 !important;
}*/

@import url('https://fonts.googleapis.com/css2?family=Source+Serif+Pro:wght@700&display=swap');


.postin{position:relative;}
.po10{position: absolute;
top: 18%;
left: 25%;
font-weight: bold;
color: #2b65a7;
font-size: 20px;}
.po1{position: absolute;margin-top:232px;left: 34%;font-weight: bold;color: #151515;font-size: 11px;}
.po2{position: absolute;margin-top:250px;left: 34%;font-weight: bold;color: #131313;font-size: 11px;}
.po3{position: absolute;left: 34%;font-weight: bold;color: #000;font-size: 11px;margin-top: 267px;}
.po4{position: absolute;top: 71%;left: 34%;font-weight: bold;color: #151515;font-size: 11px;}
.po5{position: absolute;top: 65%;left: 34%;font-weight: bold;color: #0f0f0f;font-size: 11px;}
.po6{position:absolute;top:70%;left:34%;font-weight:bold;color:#2b65a7;font-size:24px;}
.po7{position: absolute;top: 84%;left: 34%;font-weight: bold;color: #101010;font-size: 11px;}
.po8{position: absolute;top: 72.7%;left: 34%;font-weight: bold;color: #1c1c1c;font-size: 11px;}
.po9{position: absolute;top: 68.5%;left: 34%;font-weight: bold;color: #151515;font-size: 11px;}
.po11{position: absolute;top: 76%;left: 34%;font-weight: bold;color: #151515;font-size: 11px;}

.img img{position:absolute;left:65%;top:87%;}
@media print{

section{
	margin: 0px;
}


.po10{position: absolute;
top: 18%;
left: 13%;
font-weight: bold;
color: #2b65a7;
font-size: 20px;}
.po1{position: absolute;margin-top:230px;left:165px;font-weight: bold;color: #151515;font-size: 11px;}
.po2{position: absolute;margin-top:250px;left: 165px;font-weight: bold;color: #131313;font-size: 11px;}
.po3{position: absolute;margin-top:270px;left: 165px;font-weight: bold;color: #000;font-size: 11px;}
.po4{position: absolute;margin-top:71%;left: 165px;font-weight: bold;color: #151515;font-size: 11px;}
.po5{position: absolute;top: 65%;left: 165px;font-weight: bold;color: #0f0f0f;font-size: 11px;}
.po6{position:absolute;top:70%;left:165px;font-weight:bold;color:#2b65a7;font-size:24px;}
.po7{position: absolute;top: 84%;left: 165px;font-weight: bold;color: #101010;font-size: 11px;}
.po8{position: absolute;top: 72.7%;left: 165px;font-weight: bold;color: #1c1c1c;font-size: 11px;}
.po9{position: absolute;top: 68.5%;left: 165px;font-weight: bold;color: #151515;font-size: 11px;}
.po11{position: absolute;top: 76.5%;left: 165px;font-weight: bold;color: #151515;font-size: 11px;}

.img img{position:absolute;left:25%;top:87%;}
}
/* ---------- Button Styles ---------- */
.button {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 10px 24px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  margin: 10px 5px;
  cursor: pointer;
  border-radius: 4px;
}
.button:hover {
  background-color: #45a049;
}

/* ---------- Page Settings ---------- */
@page {
  size: A3;
  margin: 0mm;
}
html {
      background-color: #FFFFFF;
      margin: 15px; /* this affects the margin on the HTML before sending to printer */
    }
body {
  background: #fff;
  margin: 10mm 15mm 10mm 15mm;
  font-family: Arial, sans-serif;
}

/* ---------- Layout ---------- */
.cards-container {
  display: flex;
  flex-wrap: wrap;
  /*justify-content: space-around;*/
  page-break-inside: avoid;
  
}
.card-wrapper {
  width: 23%; /* 23~4 per row */
  margin-bottom: 5px;
  position: relative;
  page-break-inside: avoid;
  break-inside: avoid;
  display: inline-block;
  margin: 5px;
}
.admit-card {
  position: relative;
  width: 100%;
  overflow: hidden;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
.admit-card img.bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ---------- Data Field Styles ---------- */
.data-field {
  position: absolute;
  font-weight: bold;
  color: #151515;
  font-size: 10px;
  white-space: nowrap;
  overflow: hidden;
  left: 70%;
  transform: translateX(-40%);
  width: 50%;
}

/* ---------- Custom Positions (Matches Your Layout) ---------- */
.po10 {
  position: absolute;
  top: 18%;
  left: 50%;
  transform: translateX(-50%);
  width: 40%;
  text-align: center;
}
.po10 img {
  border: 1px solid #ccc;
  border-radius: 2px;
  width: 75%;
  height: auto;
}

.po1  { top: 9.5% !important; }
.po2  { top: 10.5% !important; }
.po3  { top: 11% !important; }



.po4  { top: 32% !important; }

.po6  { display: none; } /* Hide blood group if unused */

/* ---------- Print Styles ---------- */
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
  <div class="text-center my-3">
    <a href="index.php" id="backPageButton" class="button">Back</a>
    <button type="button" onclick="window.print()" class="button" id="printPageButton">Print</button>
  </div>

  <section class="cards-container">
    <?php while ($rows = $result->fetch_assoc()) { ?>
      <div class="card-wrapper">
        <div class="admit-card">
          <img src="../../libray/images/student_icard.png" alt="Background" class="bg">
          
          <div class="po10">
            <img src="../../student_reg_image/<?= htmlspecialchars($rows['image'] ?: 'default.png'); ?>" alt="Student Photo">
          </div>

          <p class="po1 data-field"><?= htmlspecialchars($rows['scl_name']); ?></p>
          <p class="po2 data-field"><?= htmlspecialchars($rows['scl_father_name']); ?></p>
          <p class="po3 data-field"><?= date('d-m-Y', strtotime($rows['scl_dob'])); ?></p>
          <p class="po5 data-field"><?= htmlspecialchars($rows['scl_phone_no']); ?></p>
          <p class="po9 data-field"><?= htmlspecialchars($rows['scl_address']); ?></p>
          <p class="po8 data-field"><?= htmlspecialchars($rows['scl_pos']); ?></p>
          <p class="po11 data-field"><?= htmlspecialchars($rows['scl_pin']); ?></p>
          <p class="po4 data-field"><?= htmlspecialchars($rows['scl_dist']); ?></p>
          <p class="po7 data-field"><?= htmlspecialchars($rows['scl_blood']); ?></p>
          
        </div>
      </div>
    <?php } ?>
  </section>
</body>
</html>
