<?php

session_start();
include_once 'dbConnection.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['key']) || $_SESSION['key'] != '66552277T') {
    die("Access Denied");
}

$eid = isset($_GET['eid'])
    ? mysqli_real_escape_string($con, $_GET['eid'])
    : '';

if ($eid == '') {
    die("Invalid Quiz ID");
}

$result = mysqli_query(
    $con,
    "SELECT * FROM quiz WHERE eid='$eid' LIMIT 1"
) or die(mysqli_error($con));

if (mysqli_num_rows($result) == 0) {
    die("Quiz not found");
}

$quiz = mysqli_fetch_assoc($result);

$title   = $quiz['title'];
$total   = $quiz['total'];
$correct = $quiz['correct'];
$wrong   = $quiz['wrong'];
$time    = $quiz['time'];
$class   = $quiz['class'];
$unit    = $quiz['unit'];

?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>Edit Quiz</title>

   <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../css/font-awesome.min.css">
  <!-- <link rel="stylesheet" href="../css/font-awesome/css/font-awesome.min.css"> -->
  <!-- Ionicons -->
  <link rel="stylesheet" href="../css/ionicons.min.css">
  
  <!-- DataTables -->
  <link rel="stylesheet" href="../css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../css/dataTables.bootstrap.min.js">
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
  
   <link rel="stylesheet" href="../css/jquery.dataTables_themeroller.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the ../css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  

</head>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include_once 'aslidebar.php'; ?>

<div class="content-wrapper">

<section class="content-header">

    <h1>
        Edit Quiz
        <small><?php echo htmlspecialchars($title); ?></small>
    </h1>

</section>

<section class="content">

<div class="row">

<div class="col-md-8 col-md-offset-2">

<div class="box box-primary">

<div class="box-header with-border">

    <h3 class="box-title">
        Edit Quiz Information
    </h3>

</div>

<form action="update.php?q=editq&eid=<?php echo urlencode($eid); ?>"
      method="post">

<div class="box-body">

    <!-- Quiz Name -->
    <div class="form-group">

        <label>Quiz Name</label>

        <input
            type="text"
            name="name"
            class="form-control"
            value="<?php echo htmlspecialchars($title); ?>"
            required
        >

    </div>


    <!-- Total Questions -->
    <div class="form-group">

        <label>Total Questions</label>

        <input
            type="number"
            name="total"
            class="form-control"
            value="<?php echo htmlspecialchars($total); ?>"
            required
        >

    </div>


    <!-- Correct -->
    <div class="form-group">

        <label>Correct Answer Marks</label>

        <input
            type="number"
            step="0.01"
            name="right"
            class="form-control"
            value="<?php echo htmlspecialchars($correct); ?>"
            required
        >

    </div>


    <!-- Wrong -->
    <div class="form-group">

        <label>Wrong Answer Marks</label>

        <input
            type="number"
            step="0.01"
            name="wrong"
            class="form-control"
            value="<?php echo htmlspecialchars($wrong); ?>"
            required
        >

    </div>


    <!-- Time -->
    <div class="form-group">

        <label>Total Time</label>

        <input
            type="number"
            name="time"
            class="form-control"
            value="<?php echo htmlspecialchars($time); ?>"
            required
        >

    </div>


    <!-- Class -->
    <div class="form-group">

        <label>Class</label>

        <select
            name="trade"
            class="form-control"
            required
        >

            <option value="">Select Class</option>

            <?php

            $classes = mysqli_query(
                $con,
                "SELECT * FROM rrsv_class ORDER BY id"
            );

            while ($row = mysqli_fetch_assoc($classes)) {

                $selected =
                    ($row['class_name'] == $class)
                    ? 'selected'
                    : '';

                echo '<option value="' .
                    htmlspecialchars($row['class_name']) .
                    '" ' .
                    $selected .
                    '>' .
                    htmlspecialchars($row['class_name']) .
                    '</option>';
            }

            ?>

        </select>

    </div>


    <!-- Unit -->
    <div class="form-group">

        <label>Unit</label>

        <select
            name="unit"
            class="form-control"
            required
        >

            <option value="">Select Unit</option>

            <option value="1"
                <?php echo ($unit == 1) ? 'selected' : ''; ?>>
                1st Unit
            </option>

            <option value="2"
                <?php echo ($unit == 2) ? 'selected' : ''; ?>>
                2nd Unit
            </option>

            <option value="3"
                <?php echo ($unit == 3) ? 'selected' : ''; ?>>
                3rd Unit
            </option>

        </select>

    </div>


    <!-- Quiz ID -->
    <input
        type="hidden"
        name="eid"
        value="<?php echo htmlspecialchars($eid); ?>"
    >

</div>

<div class="box-footer">

    <button
        type="submit"
        class="btn btn-success"
    >
        <i class="fa fa-save"></i>
        Update Quiz
    </button>

    <a
        href="quiz.php?q=10"
        class="btn btn-default"
    >
        Cancel
    </a>

</div>

</form>

</div>

</div>

</div>

</section>

</div>

</div>

</body>
</html>