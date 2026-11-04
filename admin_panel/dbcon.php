<?php
// Set PHP timezone
date_default_timezone_set('Asia/Kolkata');

$DB_NAME = "rrsv";
$DB_USER = "root";
$DB_PASS = "";
$DB_HOST = "localhost";

if ($con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) {
    // Set MySQL session timezone
    mysqli_query($con, "SET time_zone = '+05:30'");
    mysqli_set_charset($con, "utf8mb4");
    $myDB = $con;
} else {
    echo "db not connect";
}
