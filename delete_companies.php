<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

// Sanitize input
$id = intval($_GET['id']); 

// Optional: disable foreign key checks temporarily if you have dependent tables
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// Delete the company
$query = "DELETE FROM companies WHERE company_id = $id";
$result = mysqli_query($con, $query);

if ($result) {
    // Success
    $_SESSION['message'] = "Company deleted successfully.";
} else {
    // Error
    $_SESSION['error'] = "Error deleting company: " . mysqli_error($con);
}

// Re-enable foreign key checks
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// Redirect back
header("Location: companies.php");
exit();
?>
