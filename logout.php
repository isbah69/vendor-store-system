<?php
session_start();      // Start the session

// Optionally check if user is logged in
if (isset($_SESSION["NAME"])) {
    unset($_SESSION["NAME"]); // Remove specific session variable
}

// Or clear all session variables
session_unset();      // Clear all session variables
session_destroy();    // Destroy the session

// Redirect to login page
header("Location: login.php");
exit;
?>
