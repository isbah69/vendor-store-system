<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure it's a number

    // Disable foreign key checks temporarily (important if linked with other tables)
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

    // 1. Delete the selected user
    mysqli_query($con, "DELETE FROM users WHERE user_id = $id");

    // 2. Create a temporary table to rebuild IDs
    mysqli_query($con, "
        CREATE TABLE temp_users AS
        SELECT * FROM users ORDER BY user_id ASC
    ");

    // 3. Truncate the original table to clear it and reset AUTO_INCREMENT
    mysqli_query($con, "TRUNCATE TABLE users");

    // 4. Insert data back (update field names below according to your table)
    mysqli_query($con, "
        INSERT INTO users (username, email, password, role)
        SELECT username, email, password, role FROM temp_users
    ");

    // 5. Drop the temporary table
    mysqli_query($con, "DROP TABLE temp_users");

    // Re-enable foreign key checks
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

    // Redirect back to user list
    header("Location: user.php?deleted=1");
    exit();

} else {
    echo "Invalid request.";
}
?>
