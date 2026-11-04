<div class="brand clearfix">
	<h4 class="pull-left text-white text-uppercase" style="margin:20px 0px 0px 20px"><i class="fa fa-user"></i>&nbsp;
		<?php echo htmlentities($_SESSION['alogin']); ?></h4>
	<span class="menu-btn"><i class="fa fa-bars"></i></span>1
	<ul class="ts-profile-nav">
		<li class="ts-account">
			
		</li>
		<li class="ts-account">
			<a href="#"><img src="img/ts-avatar.jpg" class="ts-avatar hidden-side" alt=""> <?= $_SESSION['name'] ?> <i
					class="fa fa-angle-down hidden-side"></i></a>
			<ul>
				<li><a href="change-password.php">Change Password</a></li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
		</li>
	</ul>
</div>
<script>
function refreshBalance() {
    $.ajax({
        url: 'get_balance.php',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            console.log(res.balance);
            $('#walletBalance').text(parseFloat(res.balance).toFixed(2));
        }
    });
}

// 🔁 every 5 seconds
setInterval(refreshBalance, 5000);
</script>
