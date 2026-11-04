<?php
include '../../dbcon.php';
session_start();

$behaviour_id = $_GET['id'];
$user_id = $_SESSION['id'];
$user_role = $_SESSION['role']; // 'teacher' or 'student'

// Fetch behaviour record
$beh = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rrsv_student_behaviour WHERE id='$behaviour_id'"));
?>

<h3>Behaviour Details</h3>
<p><b>Activity:</b> <?= htmlspecialchars($beh['activties']) ?></p>
<p><b>Description:</b> <?= htmlspecialchars($beh['des']) ?></p>
<p><b>Date:</b> <?= htmlspecialchars($beh['date']) ?></p>
<hr>

<!-- Add new comment -->
<form id="commentForm" method="POST" action="save_comment.php">
  <input type="hidden" name="behaviour_id" value="<?= $behaviour_id ?>">
  <input type="hidden" name="parent_id" value="">
  <textarea name="comment_text" class="form-control" required placeholder="Write a comment..."></textarea>
  <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
</form>

<hr>
<h4>Comments</h4>

<?php
$comments = mysqli_query($con, "
  SELECT * FROM rrsv_behaviour_comments 
  WHERE behaviour_id='$behaviour_id' AND parent_id IS NULL 
  ORDER BY created_at ASC
");

while ($row = mysqli_fetch_assoc($comments)) {
  echo "<div class='comment-box border p-2 mb-2 rounded'>
          <b>{$row['user_role']}:</b> {$row['comment_text']}<br>
          <small>{$row['created_at']}</small>
          <form method='POST' action='save_comment.php' class='mt-2'>
            <input type='hidden' name='behaviour_id' value='{$behaviour_id}'>
            <input type='hidden' name='parent_id' value='{$row['id']}'>
            <textarea name='comment_text' class='form-control' rows='1' placeholder='Write reply...'></textarea>
            <button type='submit' class='btn btn-sm btn-secondary mt-1'>Reply</button>
          </form>";

  // Replies
  $replies = mysqli_query($con, "SELECT * FROM rrsv_behaviour_comments WHERE parent_id='{$row['id']}' ORDER BY created_at ASC");
  while ($reply = mysqli_fetch_assoc($replies)) {
    echo "<div class='reply-box border-start ps-3 mt-2'>
            <b>{$reply['user_role']}:</b> {$reply['comment_text']}<br>
            <small>{$reply['created_at']}</small>
          </div>";
  }

  echo "</div>";
}
?>
