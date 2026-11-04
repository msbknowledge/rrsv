<!DOCTYPE html>
<html>
<?php
session_start();
include '../dbcon.php';
// 🔒 Access Control: Only logged-in teachers can access
if (!isset($_SESSION['user_data']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit;
}

$teacher = $_SESSION['user_data'];
include '../../include/config.php';
include('../header.php');

$exam_id = $_GET['exam_id'] ?? 0;

// Fetch exam details
$exam = $con->query("SELECT * FROM rrsv_homework_exam WHERE id='$exam_id'")->fetch_assoc();

// Fetch all questions for this exam
$questions = $con->query("SELECT q.*, a.answer AS teacher_answer 
                           FROM rrsv_homework_exam_questions q
                           LEFT JOIN rrsv_teacher_answers a ON q.id = a.question_id
                           WHERE q.exam_id='$exam_id'");
?>

<style>
   
    h2, h3 { margin: 0; padding: 0; }
    .options { margin-left: 20px; margin-top: 10px; }
   
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <!-- Header Start -->
        <header class="main-header">
            <!-- Logo -->
            <a href="#" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>MSB</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg"
                        style="height:100%;width:44px;border-radius:50%;"></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <?php include '../top_navbar.php' ?>
        </header>
        <!-- Sidebar Start -->
        <?php include '../left_navbar.php' ?>
        <!-- sidebar End -->
        <div class="content-wrapper">


            <section class="content-header">
                <h1>
                    <?= htmlspecialchars($exam['book_name']); ?><?= htmlspecialchars($exam['chapter_name']); ?>
                    <small>(<?= ucfirst($exam['type']); ?>)</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="homework_exam.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Exam</a></li>
                    <li class="active">Questions</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Questions List</h3>
                                <div style="text-align:right;">

                                    <!--<a href="#" class="btn-sm btn btn-primary" data-toggle="modal"-->
                                    <!--    data-target="#addQuestionModal" style="margin-bottom:4px;">+ Add Question</a>-->
                                         <a href="add_homework_question.php?exam_id=<?=$exam_id?>" class="btn-sm btn btn-primary" style="margin-bottom:4px;">+ Add Question</a>
                                </div>
                            </div>

                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Question</th>
                                            <th>Question Type</th>
                                            <th>Video</th>
                                            <th>Options</th>
                                            <th>Correct Answer</th>
                                            <th>Teacher Answer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sn = 1;
                                        while ($q = $questions->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?= $sn++; ?></td>
                                                 <td class="ckeditor-content"><?= $q['question']; ?></td>
                                                <td><?= ucfirst(htmlspecialchars($q['question_type'])); ?></td>

                                        <td>
                                            <?php if (!empty($q['video_path'])): ?>
                                            <?php
                                                $video = htmlspecialchars($q['video_path']);
                                                // Simple check if it's a YouTube URL
                                                $is_youtube = (strpos($video, 'youtube.com') !== false || strpos($video, 'youtu.be') !== false);
                                            ?>
                                            <?php if ($is_youtube): ?>
                                                 <button onclick="playYouTubeModal('<?= $video; ?>')" class="btn btn-danger btn-sm">
                                                    ▶️ Play YouTube
                                                  </button>
                                            <?php else: ?>
                                                <button onclick="playVideoModal('../../uploads/videos/<?= $video; ?>')" class="btn btn-info">
                                                    🎬 Play Uploaded Video
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                          <!--  <?php if (!empty($q['video_path'])): ?>
                                            <button  onclick="playVideoModal('../../uploads/videos/<?= htmlspecialchars($q['video_path']); ?>')" class="btn btn-info">
                                          🎬Play Video
                                        </button>
                                        
                                            <?php else: ?>
                                                <em>No Video</em>
                                            <?php endif; ?>-->
                                            </td>

                                               <td>
                                                    <?php if ($q['question_type'] == 'mcq'): ?>
                                                        <b>A:</b> <?= htmlspecialchars($q['option_a']); ?><br>
                                                        <b>B:</b> <?= htmlspecialchars($q['option_b']); ?><br>
                                                        <b>C:</b> <?= htmlspecialchars($q['option_c']); ?><br>
                                                        <b>D:</b> <?= htmlspecialchars($q['option_d']); ?><br>
                                                
                                                    <?php elseif ($q['question_type'] == 'image'): ?>
                                                    <div class="options">
                                                        <?php if ($q['option_a']): ?><div>A: <img src="../../uploads/questions/<?= $q['option_a']; ?>" class="image-option" style="max-width:90px;"></div><?php endif; ?>
                                                        <?php if ($q['option_b']): ?><div>B: <img src="../../uploads/questions/<?= $q['option_b']; ?>" class="image-option" style="max-width:90px;"></div><?php endif; ?>
                                                        <?php if ($q['option_c']): ?><div>C: <img src="../../uploads/questions/<?= $q['option_c']; ?>" class="image-option" style="max-width:90px;"></div><?php endif; ?>
                                                        <?php if ($q['option_d']): ?><div>D: <img src="../../uploads/questions/<?= $q['option_d']; ?>" class="image-option" style="max-width:90px;"></div><?php endif; ?>
                                                </div>
                                                    <?php else: ?>
                                                        <p>Descriptive — no options</p>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($q['correct_answer']); ?></td>
                                                <td><?= nl2br(htmlspecialchars($q['teacher_answer'])); ?></td>
                                                <td>
                                                    <a href="edit_homework_exam_question.php?id=<?= $q['id']; ?>&exam_id=<?= $exam_id; ?>"
                                                        class="btn btn-info btn-xs"><i class="fa fa-edit"></i></a>
                                                    <a href="delete_homework_exam_question.php?id=<?= $q['id']; ?>&exam_id=<?= $exam_id; ?>"
                                                        class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Delete this question?')"><i
                                                            class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Add Question Modal -->
          <!-- ========================== -->
<!-- Add Question Modal Window -->
<!-- ========================== -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <form action="save_homework_exam_question.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">

        <!-- Header -->
        <div class="modal-header">
          <h4 class="modal-title">Add Question</h4>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <input type="hidden" name="exam_id" value="<?= $exam_id; ?>">

          <!-- Question -->
          <div class="form-group">
            <label>Question</label>
            <textarea name="question" id="editor1" rows="4" class="form-control" required></textarea>
          </div>
          <!-- Question -->
          <div class="form-group">
            <label>Question Head</label>
            <select id="question_head" name="question_head" class="form-control">
                <?php
                $heads = mysqli_query($con, "
                    SELECT * FROM rrsv_question_type 
                    WHERE status='active' 
                    ORDER BY id ASC 
                    LIMIT 999 OFFSET 4
                ");
        
                while ($row = mysqli_fetch_assoc($heads)) {
                    echo "<option value='{$row['type_name']}'>{$row['type_name']}</option>";
                }
                ?>
            </select>
        </div>


          <!-- Question Type -->
          <div class="form-group">
            <label>Type of Question</label>
            <select id="qtype" name="question_type" class="form-control" onchange="toggleOptions()" required>
                <?php
                $types = mysqli_query($con, "
                    SELECT * FROM rrsv_question_type 
                    WHERE status='active' 
                    ORDER BY id ASC 
                    LIMIT 4
                ");
        
                $default_type = 'mcq';
        
                while ($row = mysqli_fetch_assoc($types)) {
                    $selected = ($row['type_code'] == $default_type) ? 'selected' : '';
                    echo "<option value='{$row['type_code']}' $selected>{$row['type_name']}</option>";
                }
                ?>
            </select>
        </div>


          <!-- MCQ Options -->
          <div id="optionsDiv">
            <div class="form-group"><label>Option A</label><input type="text" name="option_a" class="form-control"></div>
            <div class="form-group"><label>Option B</label><input type="text" name="option_b" class="form-control"></div>
            <div class="form-group"><label>Option C</label><input type="text" name="option_c" class="form-control"></div>
            <div class="form-group"><label>Option D</label><input type="text" name="option_d" class="form-control"></div>

            <div class="form-group">
              <label>Correct Answer</label>
              <select id="correct_answer_text" name="correct_answer" class="form-control">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
              </select>
            </div>
          </div>

          <!-- Descriptive Type -->
          <div class="form-group" id="tDiv">
            <label>Descriptive Answer</label>
            <textarea name="teacher_answer" rows="5" class="form-control"></textarea>
          </div>

          <!-- Image Type -->
          <div class="form-group" id="imageDiv">
            <div class="form-group">
              <label>Option Image A</label>
              <input type="file" name="option_a" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_a')">
              <img id="preview_a" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Option Image B</label>
              <input type="file" name="option_b" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_b')">
              <img id="preview_b" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Option Image C</label>
              <input type="file" name="option_c" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_c')">
              <img id="preview_c" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Option Image D</label>
              <input type="file" name="option_d" class="form-control" accept="image/*"
                onchange="previewImage(this, 'preview_d')">
              <img id="preview_d" style="display:none; margin-top:5px; max-width:100px;">
            </div>

            <div class="form-group">
              <label>Correct Answer</label>
              <select id="correct_answer_image" name="correct_answer" class="form-control">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
              </select>
            </div>
          </div>

          <!-- Video Type -->
          <div class="form-group" id="vDiv">
            <label>Upload Video</label>
            <input type="url" 
         name="youtube_link" 
         id="youtubeLink" 
         class="form-control" 
         placeholder="Enter YouTube video link (e.g. https://www.youtube.com/watch?v=XXXX)"
         oninput="previewYouTubeLink(this.value)">
  
          <div id="youtubePreview" style="display:none; margin-top:10px;">
            <iframe width="300" height="180" 
                    frameborder="0" 
                    allowfullscreen></iframe>
          </div>
            <!--<input type="file" name="video_file" class="form-control" accept="video/*" onchange="previewVideo(this)">-->
            <!--<video id="videoPreview" controls style="display:none; margin-top:10px; max-width:200px;"></video>-->
            
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Question</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
  </div>
</div>
<!-- Video Preview Modal -->
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


        </div>

        <?php include '../footer.php' ?>
        <div class="control-sidebar-bg"></div>
    </div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
         function previewImage(input, previewId) {
      const preview = document.getElementById(previewId);
      const file = input.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }
    }
    function previewVideo(input) {
  const videoPreview = document.getElementById('videoPreview');
  const file = input.files[0];
  if (file) {
    const url = URL.createObjectURL(file);
    videoPreview.src = url;
    videoPreview.style.display = 'block';
  } else {
    videoPreview.src = '';
    videoPreview.style.display = 'none';
  }
}
        function toggleOptions() {
                var type = document.getElementById('qtype').value;
            
                // Hide all sections
                document.getElementById('optionsDiv').style.display = 'none';
                document.getElementById('tDiv').style.display = 'none';
                document.getElementById('imageDiv').style.display = 'none';
                document.getElementById('vDiv').style.display = 'none';
            
                // Disable all correct answer selects
                document.getElementById('correct_answer_text').disabled = true;
                document.getElementById('correct_answer_image').disabled = true;
            
                // Show & enable based on type
                if (type === 'mcq') {
                    document.getElementById('optionsDiv').style.display = 'block';
                    document.getElementById('correct_answer_text').disabled = false;
                }
                else if (type === 'image') {
                    document.getElementById('imageDiv').style.display = 'block';
                    document.getElementById('correct_answer_image').disabled = false;
                }
                else if (type === 'video') {
                    document.getElementById('vDiv').style.display = 'block';
                }
                else {
                    document.getElementById('tDiv').style.display = 'block';
                }
            }

        // Run once on modal load
        document.addEventListener('DOMContentLoaded', toggleOptions);

        // Also re-run when modal opens (Bootstrap event)
        $('#addQuestionModal').on('shown.bs.modal', function () {
            toggleOptions();
        });
        function openAddModal() {

            $('#modalTitle').text('Add Type');
        }
    </script>


<script>
  var player = videojs('videoPlayer');

  function playVideoModal(videoPath) {
    console.log("Playing video:", videoPath);

    // Set the new video source
    player.src({ type: 'video/mp4', src: videoPath });

    // Show modal
    $('#videoModal').modal('show');

    // When modal fully shown → play video
    $('#videoModal').on('shown.bs.modal', function () {
      player.play().catch(err => console.error("Autoplay blocked:", err));
    });

    // When modal closed → stop and reset
    $('#videoModal').on('hidden.bs.modal', function () {
      player.pause();
      player.currentTime(0);
      player.src({ src: '', type: 'video/mp4' });
    });
  }
  
  function previewYouTubeLink(link) {
  const youtubePreview = document.getElementById('youtubePreview');
  const iframe = youtubePreview.querySelector('iframe');
  const videoPreview = document.getElementById('videoPreview');

  // Hide local video preview if YouTube link is entered
  videoPreview.style.display = 'none';

  const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = link.match(regExp);

  if (match && match[2].length === 11) {
    const videoId = match[2];
    iframe.src = `https://www.youtube.com/embed/${videoId}`;
    youtubePreview.style.display = 'block';
  } else {
    iframe.src = '';
    youtubePreview.style.display = 'none';
  }
}
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

// function playYouTubeModal(link) {
//   const modal = $('#youtubeModal');
//   const iframe = $('#youtubeFrame');

//   // Extract YouTube video ID
//   const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
//   const match = link.match(regExp);

//   if (match && match[2].length === 11) {
//     const videoId = match[2];
//     const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
//     iframe.attr('src', embedUrl);
//     modal.modal('show');
//   }
// }

// Stop video when modal closes
$('#youtubeModal').on('hidden.bs.modal', function () {
  $('#youtubeFrame').attr('src', '');
});
</script>
<script>
CKEDITOR.replace('editor1', {
  customConfig: '<?php echo BASE_URL; ?>libray/ckeditor/ckeditor_config.js'
});
</script>

</body>

</html>