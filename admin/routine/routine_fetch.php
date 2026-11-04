<?php
include('../../include/dbcon.php');

$where = [];
if (!empty($_POST['class_id']))
  $where[] = "r.class_id=" . (int) $_POST['class_id'];
if (!empty($_POST['scl_session']))
  $where[] = "r.session='" . mysqli_real_escape_string($myDB, $_POST['scl_session']) . "'";
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Fetch routine
$sql = "
SELECT r.*, c.class_name, s.sub_name, sec.section_name,t.full_name
FROM rrsv_routine r
JOIN rrsv_class c ON r.class_id = c.id
JOIN rrsv_subject s ON r.subject_id = s.id
JOIN rrsv_section sec ON r.section_id = sec.id
JOIN rrsv_teacher t ON r.teacher_id = t.id

$whereSQL
ORDER BY FIELD(r.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), r.start_time
";
$res = mysqli_query($myDB, $sql);
if (!$res) {
  die("<div class='alert alert-danger'>Query Error: " . mysqli_error($myDB) . "</div>");
}

// Build timetable
$class_name='';
$timetable = [];
$timeSlots = [];
$days = [];
while ($row = mysqli_fetch_assoc($res)) {
  $class_name = $row['class_name'];
  $day = $row['day_of_week'];
  $slot = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));
  $days[$day] = $day;
  $timeSlots[$slot] = $slot;
  $timetable[$day][$slot] = '<b>' . htmlspecialchars($row['sub_name']) . '</b><br><small>(' . htmlspecialchars($row['full_name']) . ')</small>';
}

if (empty($timetable)) {
  echo '<div class="alert alert-warning text-center">No routine found for selected session and class.</div>';
  exit;
}

// Sort
$daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$days = array_intersect($daysOrder, $days);
sort($timeSlots);
print_r($class_name);
?>

<table class="table table-bordered table-striped text-center">
  <thead class="bg-theme-colored2 text-white">
    <tr>
      <th>Day</th>
      <?php foreach ($timeSlots as $slot): ?>
        <th><?= $slot; ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($days as $day): ?>
      <tr>
        <td><strong><?= $day; ?></strong></td>
        <?php foreach ($timeSlots as $slot): ?>
          <td><?= $timetable[$day][$slot] ?? '—'; ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>