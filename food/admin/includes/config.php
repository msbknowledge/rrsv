<?php
date_default_timezone_set('Asia/Kolkata');

// DB credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'u353140863_food');
define('DB_PASS', 'Rrsv_2026');
define('DB_NAME', 'u353140863_food');
// Establish database connection.
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$con) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// ================================
// MYSQL TIMEZONE (IMPORTANT)
// ================================
mysqli_query($con, "SET time_zone = '+05:30'");

// Optional: charset (recommended)
mysqli_set_charset($con, "utf8mb4");

?>