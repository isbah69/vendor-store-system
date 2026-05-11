<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

$id = $_GET['id'];

// Disable foreign key checks temporarily
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// 1. Delete products linked to this brand
mysqli_query($con, "DELETE FROM products WHERE brand_id = $id");

// 2. Delete the brand
mysqli_query($con, "DELETE FROM brands WHERE brand_id = $id");

// 3. Reset brand IDs to be consecutive
mysqli_query($con, "SET @count = 0");
mysqli_query($con, "UPDATE brands SET brand_id = @count:= @count + 1");
mysqli_query($con, "ALTER TABLE brands AUTO_INCREMENT = 1");

// Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect to brands page regardless
header("location:brands.php");
exit(); // make sure no further code runs
?>
