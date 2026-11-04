<?php
include('dbConnection.php');

$class = $_POST['class'];
$session = $_POST['session'];
$quiz_id = $_POST['quiz_id'];

// Get students
$res = mysqli_query($con,"
    SELECT id, scl_name, scl_reg_no, scl_class, scl_section, scl_session,status FROM rrsv_student_registration
    WHERE scl_class='$class' AND scl_session='$session' AND status='Active'
");

echo '
<table id="studentTable" class="table table-bordered table-striped">
<thead>
<tr>
    <th><input type="checkbox" id="select_all"></th>
    <th>Name</th>
    <th>Reg No</th>
    <th>Class</th>
    <th>Section</th>
    <th>Session</th>
</tr>
</thead>
<tbody>
';

while($row = mysqli_fetch_assoc($res)){

    $student_id = $row['id'];

    // Check if already assigned
    $check = mysqli_query($con,"
        SELECT * FROM quiz_assignments 
        WHERE quiz_id='$quiz_id' AND student_id='$student_id'
    ");

    $checked = (mysqli_num_rows($check) > 0) ? "checked" : "";

    echo '
    <tr>
        <td>
            <input type="checkbox" class="student_checkbox" value="'.$student_id.'" '.$checked.'>
        </td>
        <td>'.$row['scl_name'].'</td>
        <td>'.$row['scl_reg_no'].'</td>
        <td>'.$row['scl_class'].'</td>
        <td>'.$row['scl_section'].'</td>
        <td>'.$row['scl_session'].'</td>
    </tr>';
}

echo '</tbody></table>';
?>

<script>
$('#studentTable').DataTable({
    "pageLength": 10,
    "lengthMenu": [10, 25, 50, 100],
    "paging": true,
    "searching": true,
    "ordering": true,
    "destroy": true // IMPORTANT when reloading via AJAX
});

$('#select_all').click(function(){
    $('.student_checkbox').prop('checked', this.checked);
});
</script>