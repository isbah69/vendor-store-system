<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

// ✅ Check if id is passed in URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for safety

    // ✅ Disable foreign key checks temporarily
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

    // ✅ 1. Delete the selected sale item
    mysqli_query($con, "DELETE FROM sales_item WHERE sale_item_id = $id");

    // ✅ 2. Create a temporary table to store data in correct order
    mysqli_query($con, "
        CREATE TABLE temp_sales_item AS
        SELECT * FROM sales_item ORDER BY sale_item_id ASC
    ");

    // ✅ 3. Truncate the original table to reset auto_increment to 1
    mysqli_query($con, "TRUNCATE TABLE sales_item");

    // ✅ 4. Insert data back (reassigns IDs from 1, 2, 3…)
    mysqli_query($con, "
        INSERT INTO sales_item (product_id, quantity, price, total, sale_id)
        SELECT product_id, quantity, price, total, sale_id FROM temp_sales_item
    ");

    // ✅ 5. Drop the temporary table
    mysqli_query($con, "DROP TABLE temp_sales_item");

    // ✅ Re-enable foreign key checks
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

    // ✅ Redirect back to the main page after delete
    header("Location: sales_item.php?deleted=1");
    exit();

} else {
    echo "Invalid request.";
}
?>
