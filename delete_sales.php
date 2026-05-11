<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

// Sanitize input
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Disable foreign key checks temporarily
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// 1. Delete the selected sale
mysqli_query($con, "DELETE FROM sales WHERE sale_id = $id");

// 2. Drop temp table if it exists
mysqli_query($con, "DROP TABLE IF EXISTS temp_sales");

// 3. Create temp table like sales
mysqli_query($con, "CREATE TABLE temp_sales LIKE sales");

// 4. Copy remaining rows to temp table
mysqli_query($con, "INSERT INTO temp_sales SELECT * FROM sales ORDER BY sale_id ASC");

// 5. Truncate original sales table (resets auto_increment)
mysqli_query($con, "TRUNCATE TABLE sales");

// 6. Copy data back from temp table (no column names, prevents errors)
mysqli_query($con, "INSERT INTO sales SELECT * FROM temp_sales");

// 7. Drop the temporary table
mysqli_query($con, "DROP TABLE temp_sales");

// 8. Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect
header("Location: sales.php");
exit();
?>
