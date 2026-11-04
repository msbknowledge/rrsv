<?php
session_start();
include('includes/config.php');
if (isset($_POST['login'])) {

    $scl_reg_no = mysqli_real_escape_string($con, $_POST['username']);
    $password   = $_POST['password'];

    $sql = "
        SELECT 
            u.id,
            u.scl_reg_no,
            u.scl_name,
            u.password,
            COALESCE(w.balance, 0) AS balance
        FROM rrsv_student_registration u
        LEFT JOIN rrsv_food_wallet w 
            ON u.id = w.student_id
        WHERE u.scl_reg_no = '$scl_reg_no'
        LIMIT 1
    ";

    $query = mysqli_query($con, $sql);

    if ($query && mysqli_num_rows($query) === 1) {

        $row = mysqli_fetch_assoc($query);

        // ⚠️ If password is plain text (NOT recommended)
        if ($row['password'] === $password) {
            $_SESSION['student_id'] = $row['id'];
            $_SESSION['scl_reg_no'] = $row['scl_reg_no'];
            $_SESSION['name']       = $row['scl_name'];
            $_SESSION['balance']    = $row['balance'];
            $_SESSION['alogin'] = $row['scl_name']; 
            header("Location: dashboard.php");
            exit;
        }
    }

    echo "<div class='alert alert-danger'>Invalid Registration No or Password</div>";
}
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">


	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">

</head>

<body>
	<div class="login-page bk-img">
		<div class="form-content">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<h1 class="text-center text-bold mt-4x">Login</h1>
						<div class="well row pt-2x pb-3x bk-light">
							<div class="col-md-8 col-md-offset-2">
								<form method="post">

									<label for="" class="text-uppercase text-sm">Your ID</label>
									<input type="text" placeholder="Your ID" name="username" class="form-control mb"
										required>

									<label for="" class="text-uppercase text-sm">Password</label>
									<input type="password" placeholder="Password" name="password"
										class="form-control mb" required>
									<button class="btn btn-primary btn-block" name="login" type="submit">LOGIN</button>
								</form>
								<br>
								<p>Don't Have an Account? <a href="register.php">Signup</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>

</html>