  <div class="modal fade" id="addExamModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <form action="save_homework_exam.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Homework / Exam</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Book</label>
                                <input type="text" name="book_name" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Chapter</label>
                                <input type="text" name="chapter_name" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Unit</label>
                                <select name="unit" class="form-control" id="unit">
                                    <?php
                                    $selectedUnit = $_POST['unit'] ?? 1; // default to 1st Unit
                                    for ($u = 1; $u <= 3; $u++) {
                                        $unitLabel = ($u == 1) ? '1st Unit' :
                                            (($u == 2) ? '2nd Unit' :
                                                (($u == 3) ? '3rd Unit' : $u . 'th Unit'));
                                        $sel = ($selectedUnit == $u) ? 'selected' : '';
                                        echo "<option value='$u' $sel>$unitLabel</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="homework">Homework</option>
                                    <option value="exam">Exam</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Session</label>
                                <select name="session" id="session" class="form-control">
                                    <?php
                                    $currentYear = date('Y');
                                    echo "<option value='$currentYear' selected>$currentYear</option>";
                                    for ($y = $currentYear - 1; $y >= ($currentYear - 5); $y--) {
                                        echo "<option value='$y'>$y</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Class</label>

                                <select name="class_id" class="form-control" id="class_id">
                                    <option value="">-Select a Class-</option>
                                    <?php
                                    $id = 0;
                                    $sql = "select * from rrsv_class order by id";
                                    $res = mysqli_query($con, $sql);
                                    while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $obj['id']; ?>">
                                            <?php echo $obj['class_name']; ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Subject <span class="text-red"> * </span></label>
                                <select class="form-control" name="subject_id" id="subject_id" required>
                                    <option value="">Select Subject</option>
                                    <?php
                                    $id = 0;
                                    $sql2 = "select * from rrsv_subject order by id";
                                    $res2 = mysqli_query($con, $sql2);
                                    while ($obj2 = mysqli_fetch_array($res2, MYSQLI_ASSOC)) {
                                        ?>
                                        <option value="<?php echo $obj2['id']; ?>">
                                            <?php echo $obj2['sub_name']; ?>
                                        </option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                            <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?? ''; ?>"> <!-- Example -->
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>