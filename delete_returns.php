<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

$id = $_GET['id'];

// Disable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// Delete the record
mysqli_query($con, "DELETE FROM returns WHERE return_id = $id");

// Reorder IDs to start from 1
mysqli_query($con, "
    SET @count = 0;
");

mysqli_query($con, "
    UPDATE returns SET return_id = (@count := @count + 1) ORDER BY return_id;
");

// Reset AUTO_INCREMENT to next number (start again from 1 if empty)
$result = mysqli_query($con, "SELECT COUNT(*) AS total FROM returns");
$row = mysqli_fetch_assoc($result);
$total = $row['total'];

if ($total > 0) {
    mysqli_query($con, "ALTER TABLE returns AUTO_INCREMENT = " . ($total + 1));
} else {
    mysqli_query($con, "ALTER TABLE returns AUTO_INCREMENT = 1");
}

// Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect
header("location:returns.php");
exit();
?>
