<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

$id = $_GET['id'];

// Disable foreign key checks temporarily (in case other tables reference expenses)
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// 1. Delete the selected expense record
mysqli_query($con, "DELETE FROM expenses WHERE expense_id = $id");

// 2. Check if there are any remaining expenses
$result = mysqli_query($con, "SELECT COUNT(*) AS total FROM expenses");
$row = mysqli_fetch_assoc($result);

if ($row['total'] > 0) {
    // 3. Reset expense IDs to be consecutive starting from 1
    mysqli_query($con, "SET @count = 0");
    mysqli_query($con, "UPDATE expenses SET expense_id = @count := @count + 1 ORDER BY expense_id ASC");

    // 4. Reset AUTO_INCREMENT to next available ID
    mysqli_query($con, "ALTER TABLE expenses AUTO_INCREMENT = " . ($row['total'] + 1));
} else {
    // 5. If no rows left, reset AUTO_INCREMENT to 1
    mysqli_query($con, "ALTER TABLE expenses AUTO_INCREMENT = 1");
}

// Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect back to expenses page
header("location:expenses.php");
exit();
?>
