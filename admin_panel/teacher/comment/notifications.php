<?php
include '../../dbcon.php';
session_start();

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role']; // teacher/student

$notifications = mysqli_query($con, "
  SELECT * FROM rrsv_notifications 
  WHERE receiver_id='$user_id' AND receiver_role='$user_role' 
  ORDER BY created_at DESC
");
?>

<h3>Notifications</h3>
<ul class="list-group">
<?php while ($n = mysqli_fetch_assoc($notifications)): ?>
  <li class="list-group-item <?= $n['is_read'] ? '' : 'fw-bold' ?>">
    <?= htmlspecialchars($n['message']) ?>
    <br><small><?= $n['created_at'] ?></small>
  </li>
<?php endwhile; ?>
</ul>
