<!DOCTYPE html>
<html>
<?php
session_start();
include '../../include/config.php';
include '../dbcon.php';
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit;
}

$teacher = $_SESSION['user_data'];
include('../header.php');

?>

<body class="hold-transition skin-blue sidebar-mini">


  <div class="wrapper">
    <!-- Header Start -->
    <header class="main-header">
      <!-- Logo -->
      <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>MSB</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="assets/css/download.png" style="height:100%;width:44px"></span>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <?php include '../top_navbar.php' ?>
    </header><!-- Header End -->
    <!-- Left side column. contains the logo and sidebar -->

    <!-- Sidebar Start -->
    <?php include '../left_navbar.php' ?>
    <!-- sidebar End -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <?php

      $exam_id = $_GET['exam_id'];
      $student_id = $_SESSION['user_data']['id'];

      // Fetch exam info
      $exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();

      // Fetch questions
      $questions = $con->query("SELECT * FROM rrsv_homework_exam_questions WHERE exam_id='$exam_id'");
      ?>

      <section class="content-header">
        <h1><?= htmlspecialchars($exam['title']); ?> <small>(<?= ucfirst($exam['type']); ?>)</small></h1>
      </section>

      <section class="content">
        <form action="submit_exam.php" method="POST">
          <input type="hidden" name="exam_id" value="<?= $exam_id; ?>">

         <?php
         $i=1;
while ($q = $questions->fetch_assoc()) { 
    $type = strtolower($q['question_type']);
?>
<div class="box box-info">
    <div class="box-header with-border">
          <p class="">Q<?= $i++; ?>.<?= $q['question_head']; ?> <?= $q['question']; ?></p>
    </div>

    <div class="box-body">

        <?php if ($type === 'mcq'): ?>

            <!-- Text MCQ Options -->
            <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="A"> 
                <?= htmlspecialchars($q['option_a']); ?></label><br>
            <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="B"> 
                <?= htmlspecialchars($q['option_b']); ?></label><br>
            <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="C"> 
                <?= htmlspecialchars($q['option_c']); ?></label><br>
            <label><input type="radio" name="answers[<?= $q['id']; ?>]" value="D"> 
                <?= htmlspecialchars($q['option_d']); ?></label><br>

        <?php elseif ($type === 'image'): ?>

            <!-- Image MCQ Options -->
            <label>
                <input type="radio" name="answers[<?= $q['id']; ?>]" value="A">
                <img src="../../uploads/questions/<?= $q['option_a']; ?>" style="width:120px">
            </label><br>

            <label>
                <input type="radio" name="answers[<?= $q['id']; ?>]" value="B">
                <img src="../../uploads/questions/<?= $q['option_b']; ?>" style="width:120px">
            </label><br>

            <label>
                <input type="radio" name="answers[<?= $q['id']; ?>]" value="C">
                <img src="../../uploads/questions/<?= $q['option_c']; ?>" style="width:120px">
            </label><br>

            <label>
                <input type="radio" name="answers[<?= $q['id']; ?>]" value="D">
                <img src="../../uploads/questions/<?= $q['option_d']; ?>" style="width:120px">
            </label><br>

        <?php elseif ($type === 'descriptive'): ?>

            <!-- Descriptive Type -->
            <textarea name="answers[<?= $q['id']; ?>]" class="form-control"
                placeholder="Write your answer here..."></textarea>

        <?php elseif ($type === 'video'): ?>
 <?php

//print_r( $q);die();
function getYouTubeId($url) {
    preg_match(
        '/(youtu\.be\/|youtube\.com\/(watch\?v=|embed\/|shorts\/))([^\?&\/]+)/',
        $url,
        $matches
    );
    return $matches[3] ?? null;
}

$video = $q['video_path'];
$is_youtube = (strpos($video, 'youtube') !== false || strpos($video, 'youtu.be') !== false);
?>

<?php if ($is_youtube): ?>

    <?php $videoId = getYouTubeId($video); ?>

    <iframe width="420" height="240"
        src="https://www.youtube.com/embed/<?= $videoId ?>"
        frameborder="0"
        allowfullscreen>
    </iframe>

<?php else: ?>

    <video width="350" controls>
        <source src="../../uploads/questions/<?= $video ?>" type="video/mp4">
    </video>

<?php endif; ?>
            <!-- Video Type -->
            <!--<video width="320" controls>-->
            <!--    <source src="../../uploads/questions/<?= $q['video_url']; ?>" type="video/mp4">-->
            <!--</video>-->

            <textarea name="answers[<?= $q['id']; ?>]" class="form-control"
                placeholder="Write your answer here..."></textarea>

        <?php endif; ?>

    </div>
</div>

<?php } ?>
          <button type="submit" class="btn btn-success">Submit Answers</button>
        </form>
      </section>


<!-- ===== Video Modal ===== -->
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Question Video</h4>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>

      <div class="modal-body text-center">
        <!-- Video.js CSS -->
        <link href="https://vjs.zencdn.net/8.23.4/video-js.css" rel="stylesheet" />

        <!-- Video Player -->
        <video
          id="videoPlayer"
          class="video-js vjs-big-play-centered"
          controls autoplay
          preload="auto"
          width="640"
          height="360"
          data-setup="{}"
        >
          <source src="" type="video/mp4" id="videoSource">
          Your browser does not support HTML5 video.
        </video>

        <!-- Video.js Script -->
        <script src="https://vjs.zencdn.net/8.23.4/video.min.js"></script>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="youtubeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">YouTube Video</h4>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body text-center">
        <iframe
          id="youtubeFrame"
          width="100%"
          height="400"
          frameborder="0"
          allowfullscreen
          style="display:block; margin:auto;"
        ></iframe>
      </div>
    </div>
  </div>
</div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Footer Start -->
    <?php include '../footer.php' ?>
    <div class="control-sidebar-bg"></div>
  </div>











  <!-- ./wrapper -->
  <!-- jQuery 2.2.3 -->
  <script src="../assets/js/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap 3.3.6 -->
  <script src="../assets/js/bootstrap.min.js"></script>
  <!-- DataTables -->
  <script src="../assets/js/jquery.dataTables.min.js"></script>
  <script src="../assets/js/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="../assets/js/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="../assets/js/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="../assets/js/app.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../assets/js/demo.js"></script>
  <!-- page script -->
  <script>
    $(function () {
      $("#example1").DataTable();
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false
      });
    });
  </script>

<script>
    function playYouTubeModal(link) {
  const modal = $('#youtubeModal');
  const iframe = $('#youtubeFrame');

  let videoId = null;

  // Match normal YouTube URLs
  const normalRegex =
    /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]{11}).*/;

  // Match YouTube Shorts
  const shortsRegex =
    /^.*youtube\.com\/shorts\/([^#&?]{11}).*/;

  let match = link.match(normalRegex);

  if (match && match[2]) {
    videoId = match[2];
  } else {
    match = link.match(shortsRegex);
    if (match && match[1]) {
      videoId = match[1];
    }
  }

  if (videoId) {
    const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    iframe.attr('src', embedUrl);
    modal.modal('show');
  } else {
    alert('Invalid YouTube link');
  }
}

$('#youtubeModal').on('hidden.bs.modal', function () {
  $('#youtubeFrame').attr('src', '');
});
</script>


</body>

</html>