<?php
session_start();

include('../../include/dbcon.php');


if (empty($_SESSION['mid'])) {
    header('location:index.php');
    exit;
}
$msg = '';
$error = '';

if (isset($_POST['save'])) {

     $name   = trim($_POST['package_name'] ?? '');
    $meal   = trim($_POST['meal_type'] ?? '');
    $price  = trim($_POST['price'] ?? '');
    $active = trim($_POST['active'] ?? 'Yes');
    $foods  = $_POST['food_id'] ?? [];

    if ($meal == '') {
        echo json_encode(['status'=>'error','msg'=>'Select meal type']);
        exit;
    }
    if ($name == '') {
        echo json_encode(['status'=>'error','msg'=>'Package name required']);
        exit;
    }
    if ($price == '' || $price <= 0) {
        echo json_encode(['status'=>'error','msg'=>'Enter valid price']);
        exit;
    }
    if (count($foods) == 0) {
        echo json_encode(['status'=>'error','msg'=>'Select at least one food item']);
        exit;
    }

    // ✅ IF NO ERROR → INSERT
    if ($error == '') {

        mysqli_query($myDB, "
            INSERT INTO rrsv_menu_package(package_name, meal_type, price, active)
            VALUES ('$name','$meal','$price','$active')
        ");

        $pid = mysqli_insert_id($myDB);

        foreach ($foods as $fid) {
            mysqli_query($myDB, "
                INSERT INTO rrsv_menu_package_items(package_id, food_id)
                VALUES ('$pid','$fid')
            ");
        }

        echo json_encode([
            'status' => 'success',
            'msg' => 'Package added successfully'
        ]);
        exit;
    }

    // ❌ ERROR RESPONSE
    echo json_encode([
        'status' => 'error',
        'msg' => $error
    ]);
    exit;
}
include('../../include/header.php');
?>

		<style>
			.errorWrap {
				padding: 10px;
				margin: 0 0 20px 0;
				background: #dd3d36;
				color: #fff;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
				box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			}

			.succWrap {
				padding: 10px;
				margin: 0 0 20px 0;
				background: #5cb85c;
				color: #fff;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
				box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
			}
			.form-control {
    height: 44px;
    border-radius: 6px;
}

		</style>


<div class="main-panel">
    <div class="content-wrapper">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                   Add Menu Package
                </h4>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">

    <h3></h3>
    <?php if ($msg)
        echo "<div class='alert alert-success'>$msg</div>"; ?>

    <form method="post">
          <!-- ROW 1 -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <select class="form-control" name="meal_type" required>
                    <option value="">Select Meal</option>
                    <option>Breakfast</option>
                    <option>Lunch</option>
                    <option>Dinner</option>
                    <option>Tiffin</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <input type="number"
                       step="0.01"
                       class="form-control"
                       name="price" id="price"
                       placeholder="Price" required>
            </div>
        </div>
        <!--<div class="col-md-6">-->
        <!--    <div class="form-group">-->
        <!--        <select class="form-control" name="active" required>-->
        <!--            <option value="Yes">Active</option>-->
        <!--            <option value="No">Inactive</option>-->
        <!--        </select>-->
        <!--    </div>-->
        <!--</div>-->
    </div>

    <!-- ROW 2 -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <input type="text"
                       class="form-control"
                       name="package_name" id="package_name"
                       placeholder="Package Name"
                       readonly required>
            </div>
        </div>

    </div>

      
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Items</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <?php
                        $q = mysqli_query($myDB, "SELECT * FROM rrsv_food WHERE status='Active'");
                        while ($r = mysqli_fetch_assoc($q)) {
                            ?>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="food-check" name="food_id[]" value="<?= $r['id'] ?>"
                                            data-price="<?= $r['price'] ?>" data-name="<?= $r['name'] ?>" >
                                        <label for="customCheckbox1" class="custom-control-label"> <?= $r['name'] ?>
                                            </label>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>



                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <br>
        <div class="row">
                <div class="col-md-4 form-group">
                <button type="button" id="btnSave" class="btn btn-success">
    Save
</button>

        <a href="menu-package-list.php" class="btn btn-primary">Back</a>
               </div>
            </div>
       
    </form>

     </div>
       </div>
            </div>
        </div>

    </div>

    <script>
$(document).ready(function () {
    $('#foodTable').DataTable({
        paging: true,       // pagination
        searching: true,    // search box
        ordering: true,     // column sorting
        info: true,         // "Showing X of Y"
        lengthChange: true, // page length dropdown
        pageLength: 10,     // default rows per page
        order: [[0, 'desc']] // sort by ID desc
    });
});
</script>
 <script>
        $(document).ready(function () {

            $('.food-check').on('change', function () {

                let total = 0;
                let names = [];

                $('.food-check:checked').each(function () {
                    // total += parseFloat($(this).data('price'));
                    names.push($(this).data('name'));
                });

                // $('#price').val(total.toFixed(2));
                $('#package_name').val(names.join(', '));
            });

        });
    </script>

    <script>
       $('#btnSave').on('click', function (e) {
    e.preventDefault();

    let btn = $(this);
    btn.prop('disabled', true);

    $('.alert').remove();

    $.ajax({
        url: '',
        type: 'POST',
        dataType: 'json', // 🔴 REQUIRED
        data: $('form').serialize() + '&save=1',

        success: function (res) {

            if (res.status === 'success') {
                $('form').before(
                    '<div class="alert alert-success">' + res.msg + '</div>'
                );
                $('form')[0].reset();
                $('#package_name').val('');
            } else {
                $('form').before(
                    '<div class="alert alert-danger">' + res.msg + '</div>'
                );
            }
        },

        error: function () {
            $('form').before(
                '<div class="alert alert-danger">Server error</div>'
            );
        },

        complete: function () {
            btn.prop('disabled', false);
        }
    });
});

    </script>

