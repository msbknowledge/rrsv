<?php 
session_start();
if (!isset($_SESSION['username']) && !isset($_SESSION['key']) && $_SESSION['key'] == '66552277A')) {
    header('location:index.php');
}
if(isset($_SESSION['username']) && isset($_SESSION['key']) && $_SESSION['key'] == '66552277A'){
        $username=$_SESSION['username'];
        $_SESSION["username"] = $username;
         header('location:dashboard.php?q=4');
      }
      ?>