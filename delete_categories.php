<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

$id = $_GET['id'];

// Disable foreign key checks temporarily (in case other tables reference categories)
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// 1. Delete the category
mysqli_query($con, "DELETE FROM categories WHERE category_id = $id");

// 2. Reset category IDs to be consecutive
mysqli_query($con, "SET @count = 0");
mysqli_query($con, "UPDATE categories SET category_id = @count:= @count + 1");
mysqli_query($con, "ALTER TABLE categories AUTO_INCREMENT = 1");

// Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect to categories page
header("location:categories.php");
exit();
?>
