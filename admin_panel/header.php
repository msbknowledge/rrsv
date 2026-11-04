<?php include(__DIR__ . '/../include/config.php');?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="description" content="RRSV" />
    <meta name="keywords" content="RRSV," />
    <meta name="author" content="RRSV" />
   <title>RRSV</title ><!-- Favicon and Touch Icons -->
    <link href="<?php echo BASE_URL; ?>libray/images/logo.jpeg" rel="shortcut icon" type="image/png">
    <link href="<?php echo BASE_URL; ?>libray/images/logo.jpeg" rel="apple-touch-icon">
    <link href="<?php echo BASE_URL; ?>libray/images/logo.jpeg" rel="apple-touch-icon" sizes="72x72">
    <link href="<?php echo BASE_URL; ?>libray/images/logo.jpeg" rel="apple-touch-icon"
        sizes="114x114">
    <link href="<?php echo BASE_URL; ?>libray/images/logo.jpeg" rel="apple-touch-icon"
        sizes="144x144">
   
   <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>libray/images/logo.jpeg">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/bootstrap.min.css">
 <!--<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>-->
  
   <!-- Font Awesome -->
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/font-awesome.min.css">
  <!-- <link rel="stylesheet" href="../assets/css/font-awesome/assets/css/font-awesome.min.css"> -->
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/dataTables.bootstrap.min.js">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/jquery.dataTables.min.css">
  
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/jquery.dataTables_themeroller.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the assets/css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin_panel/assets/css/_all-skins.min.css">
  
<script language="javascript">
function del_Users(uid)
{
	if(confirm("Are you sure want to delete this Contact?"))
	{
		document.location.href = 'groupcontacts?act=del&uid='+uid;
	}
}
</script>
<script>
 
  
  
  
$(function () {
    $(document).on( 'scroll', function(){
        console.log('scroll top : ' + $(window).scrollTop());
        if($(window).scrollTop()>=$(".logo").height())
        {
             $(".navbar").addClass("navbar-fixed-top");
        }

        if($(window).scrollTop()<$(".logo").height())
        {
             $(".navbar").removeClass("navbar-fixed-top");
        }
    });
});

  
// form display 
$(document).ready(function(){
	
	$('#add_button').click(function(){
		$('#modal_title').text('Add Student');
		$('#button_action').val('Add');
		$('#action').val('Add');
		$('#formModal').modal('show');
		clear_field();
	});

	
});
</script>
  <style>
  @media only screen and (max-width: 620px) {
  /* For mobile phones: */
  .menu,.sp,.main, .right {
    width: 100%;
  }
}

.sp {
   padding: 20px 30px 30px 20px;
    background: #09bffd;
    border-radius: 8px;
	text-transform:uppercase;
	font-size:15px;
	text-align:left; 
	padding-top:3px;
	padding-left:55px; 
	color:blue;
    
}
.footer{
  background-color:#3AA56A;
  color: #fff;
  text-align:center;
  font-size:20px;
  padding:10px;
  margin-top:37px;
  position: stiky;
  bottom: 0;
  width: 100%;
   }

.head {
    width: 390px;
    padding: 20px 30px 30px 30px;
    background: #09bffd;
    border-radius: 8px;
    display: table;
	margin: 50px;
    top:20%;
}
.br {
	
    font-size: 20px;
    line-height: 24px;
    font-weight: 100;
    text-align: center;
    color: #fff;
	border-radius: 8px;
    
}
 em {
    display: block;
    font-size: 25px;
    line-height: 33px;
    font-weight: 900px;
    text-transform: uppercase;
    letter-spacing: -1px;
}
.pure-form{
    background: #ddeef8;
    width: 100%;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0 0;
}

body { 
     font-family: 'Droid Sans', sans-serif;
     background:#E4D2B8;
    
    }

.heading {
	width:100%;
	height:96px;
	background:url(./image/banner_bg.gif);
	position:relative;
	border-radius: 8px;
	}
    .circle {
    border-radius: 50%!important;
    border-top-left-radius: 50%;
    border-top-right-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
    border-radius: 8px;
}

.btn-d {
 	background:#fd0963;
	
	height:32px;
	border:1px solid #fd0963;
	color:#FFFFFF;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
	padding:0 20px;
	border-radius: 8px;  
}
.btn-d:hover {
	border:1px solid #fd0963;
	padding:0 20px;
	color:#FFFFFF;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.4);
	background:#fd0663;
	border-radius: 8px; 
	cursor:pointer;
}
.button a:link, .button a:visited, .button a:active { color:#lime; text-decoration:none; }
a {
	font-size:16px;
	color:#lime;
	font-weight:bold;
	text-decoration:none;
	}	

  #pageloader
{
background: url('') 30% 30% no-repeat rgba( 255, 255, 255, 0.8 );
  display: none;
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 2000;
  background-position:center;
 
}

#pageloader img
{
   
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
  text-align: center;
  width : 400px
}

#pageloader:after{
   content: "Please Wait- Don't Press Any Button While Uploading";
   font-weight: bold;
  display: block;
  position: absolute;
  top:60%;
  left: 50%;
  transform: translate(-50%, -50%)
}
  </style>


</head>
