<?php
session_start();
include_once "connection.php"; // optional but safe

// If a selected customer is stored in session, remove it
if (isset($_SESSION['selected_customer'])) {
    unset($_SESSION['selected_customer']);
}

// If a walk-in customer session exists, remove it too
if (isset($_SESSION['walk_in_customer'])) {
    unset($_SESSION['walk_in_customer']);
}

// (Optional) If you use a temporary sales table, you can clear it
// Example: mysqli_query($con, "DELETE FROM temp_sales WHERE customer_id = 0");

// After clearing, redirect back to your Add Sale page
header("Location: add_sales.php");
exit;
?>
