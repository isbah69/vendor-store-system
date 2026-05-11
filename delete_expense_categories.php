<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

$id = $_GET['id'];

// Disable foreign key checks temporarily
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// 1. Delete the selected category
mysqli_query($con, "DELETE FROM expense_categories WHERE category_id = $id");

// 2. Reset category IDs to start from 1 and be consecutive
mysqli_query($con, "SET @count = 0");
mysqli_query($con, "
    UPDATE expense_categories 
    SET category_id = (@count := @count + 1)
    ORDER BY category_id ASC
");

// 3. Reset AUTO_INCREMENT to (max_id + 1) or 1 if empty
$result = mysqli_query($con, "SELECT MAX(category_id) AS max_id FROM expense_categories");
$row = mysqli_fetch_assoc($result);
$next_id = ($row['max_id'] > 0) ? $row['max_id'] + 1 : 1;
mysqli_query($con, "ALTER TABLE expense_categories AUTO_INCREMENT = $next_id");

// Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect to categories page
header("location:expense_categories.php");
exit();
?>
