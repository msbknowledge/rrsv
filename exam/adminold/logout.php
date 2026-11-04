<?php
if (isset($_SESSION['username'])) {

    session_destroy();

}

$ref = @$_GET['q'];

header("location:../index.php");

?>