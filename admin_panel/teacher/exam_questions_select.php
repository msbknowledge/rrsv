<?php
session_start();
include '../dbcon.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: ../../index.php");
    exit;
}

$exam_id = $_GET['exam_id'];

$questions = mysqli_query($con,"
SELECT
    q.*,
    e.title AS exam_title
FROM rrsv_homework_exam_questions q
LEFT JOIN rrsv_homework_exam e
ON e.id=q.exam_id
ORDER BY q.id DESC
");
?>

<?php include('../header.php'); ?>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

<?php include '../top_navbar.php'; ?>
<?php include '../left_navbar.php'; ?>

<div class="content-wrapper">

<section class="content">

<div class="box box-primary">

<div class="box-header with-border">

<h3 class="box-title">

Select Existing Questions

</h3>

<div class="pull-right">
<button class="btn btn-success" data-toggle="modal" data-target="#examModal">
    <i class="fa fa-plus"></i> Add Selected Questions
</button>

<div class="modal fade" id="examModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="copyForm">

                <div class="modal-header">
                    <h4 class="modal-title">Select Exam</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Choose Exam</label>

                        <select name="exam_id" class="form-control" required>

                            <option value="">Select Exam</option>

                            <?php
                            $exam=mysqli_query($con,"
                                SELECT id,book_name
                                FROM rrsv_homework_exam
                                ORDER BY id DESC
                            ");

                            while($e=mysqli_fetch_assoc($exam)){
                            ?>

                            <option value="<?= $e['id'] ?>">
                                <?= $e['book_name'] ?>
                            </option>

                            <?php } ?>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="modal">

                        Cancel

                    </button>

                    <button
                    type="submit"
                    class="btn btn-success">

                        Add Questions

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
<!--<button-->
<!--class="btn btn-success"-->
<!--id="saveSelected">-->

<!--<i class="fa fa-plus"></i>-->

<!--Add Selected Questions-->

<!--</button>-->

</div>

</div>

<div class="box-body">

<form id="questionForm">

<input
type="hidden"
name="exam_id"
value="<?= $exam_id ?>">

<table
id="example1"
class="table table-bordered table-striped">

<thead>

<tr>

<th width="40">

<input
type="checkbox"
id="select_all">

</th>

<th width="60">#</th>

<th>Question</th>

<th width="120">

Type

</th>

<th width="200">

From Exam

</th>

</tr>

</thead>

<tbody>

<?php

$i=1;

while($q=mysqli_fetch_assoc($questions))
{

?>

<tr>

<td>

<input
type="checkbox"
class="row_checkbox"
name="question_ids[]"
value="<?= $q['id']?>">

</td>

<td>

<?= $i++ ?>

</td>

<td>

<b><?= htmlspecialchars($q['question']) ?></b>

</td>

<td>

<?= $q['question_type'] ?>

</td>

<td>

<?= htmlspecialchars($q['exam_title']) ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</form>

</div>

</div>

</section>

</div>

<?php include '../footer.php'; ?>

</div>

<script src="../assets/js/jquery-2.2.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTables.bootstrap.min.js"></script>
<script>
$(function () {

    $('#example1').DataTable({
        pageLength: 25,
        ordering: true,
        responsive: true
    });

    // Select All
    $('#select_all').click(function () {
        $('.row_checkbox').prop('checked', this.checked);
    });

    // Individual Checkbox
    $(document).on('change', '.row_checkbox', function () {
        $('#select_all').prop(
            'checked',
            $('.row_checkbox:checked').length === $('.row_checkbox').length
        );
    });

    // Save Selected Questions
    $('#saveSelected').click(function () {

        if ($('.row_checkbox:checked').length == 0) {
            alert('Please select at least one question.');
            return;
        }

        $.ajax({
            url: 'copy_exam_questions.php',
            type: 'POST',
            data: $('#questionForm').serialize(),
            beforeSend: function () {
                $('#saveSelected')
                    .html('<i class="fa fa-spinner fa-spin"></i> Saving...')
                    .prop('disabled', true);
            },
            success: function (response) {

                alert(response);

                $('#saveSelected')
                    .html('<i class="fa fa-plus"></i> Add Selected Questions')
                    .prop('disabled', false);

                window.location.href = "exam_question_list.php?exam_id=<?= $exam_id ?>";

            },
            error: function () {

                alert('Something went wrong.');

                $('#saveSelected')
                    .html('<i class="fa fa-plus"></i> Add Selected Questions')
                    .prop('disabled', false);
            }
        });

    });

});

$('#copyForm').submit(function(e){

    e.preventDefault();

    if($('.row_checkbox:checked').length==0){

        alert('Please select questions.');

        return;
    }

    var data=$(this).serialize();

    $('.row_checkbox:checked').each(function(){

        data+='&question_ids[]='+$(this).val();

    });

    $.ajax({

        url:'copy_exam_questions.php',

        type:'POST',

        data:data,

        success:function(res){

            alert(res);

            location.reload();

        }

    });

});
</script>

</body>
</html>