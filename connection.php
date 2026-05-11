<?php
$host = "localhost";
$user = "root";
$pw = "";
$db = "store";

// Connect to MySQL
$con = mysqli_connect("127.0.0.1", "root", "", "store");

// Check connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional: set charset
mysqli_set_charset($con, "utf8");
?>
