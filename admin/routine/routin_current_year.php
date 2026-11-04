<?php
include('../../include/dbcon.php');

// ---- Get Filters ----
$class_id   = $_POST['class_id']  ?? '';
$session    = $_POST['session']   ?? date('Y');

// ---- Build WHERE clause ----
$where = [];
if (!empty($class_id)) $where[] = "r.class_id=" . (int)$class_id;
if (!empty($session))  $where[] = "r.session='" . mysqli_real_escape_string($myDB, $session) . "'";
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// ---- Query ----
$sql = "
SELECT r.*, c.class_name, s.sub_name, sec.section_name
FROM manage_routine r
JOIN rrsv_class c ON r.class_id = c.id
JOIN rrsv_subject s ON r.subject_id = s.id
JOIN rrsv_section sec ON r.section_id = sec.id
$whereSQL
ORDER BY FIELD(r.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
         r.start_time
";
$res = mysqli_query($myDB, $sql);

// ---- Prepare Data ----
$timetable = [];
$timeSlots = [];
$days = [];

while ($row = mysqli_fetch_assoc($res)) {
  $day = $row['day_of_week'];
  $slot = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));
  $days[$day] = $day;
  $timeSlots[$slot] = $slot;
  $timetable[$day][$slot] = [
    'subject' => $row['sub_name'],
    'teacher' => $row['teacher_name'],
    'is_break' => $row['is_break']
  ];
}

$daysOrder = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$days = array_intersect($daysOrder, $days);
sort($timeSlots);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Class Routine</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
  body { background-color:#f5f7fb; }
  table { font-size:14px; }
  th { background:#0a4a97 !important; color:white; }
  td.break { background:#ffe6e6; font-style:italic; }
  .routine-card {
    background:white; border-radius:10px; padding:20px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
  }
</style>
</head>
<body class="p-4">

<div class="routine-card container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary m-0">📘 Class Routine (<?= htmlspecialchars($session) ?>)</h4>
    <?php if ($class_id): ?>
      <h6 class="text-secondary">Class ID: <?= htmlspecialchars($class_id) ?></h6>
    <?php endif; ?>
  </div>

  <?php if (empty($timetable)): ?>
    <div class="alert alert-warning text-center">No routine found for this session and class.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Day</th>
            <?php foreach ($timeSlots as $slot): ?>
              <th><?= $slot ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($days as $day): ?>
            <tr>
              <td><strong><?= $day ?></strong></td>
              <?php foreach ($timeSlots as $slot): 
                $cell = $timetable[$day][$slot] ?? null;
              ?>
                <td class="<?= $cell && $cell['is_break'] ? 'break' : '' ?>">
                  <?php 
                    if ($cell) {
                      if ($cell['is_break']) {
                        echo "Break";
                      } else {
                        echo "<b>" . htmlspecialchars($cell['subject']) . "</b><br><small>(" . htmlspecialchars($cell['teacher']) . ")</small>";
                      }
                    } else {
                      echo "—";
                    }
                  ?>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
