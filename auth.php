<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

/**
 * Normalize role: lowercase, trim, remove spaces
 * "Inventory Staff" -> "inventory_staff"
 */
function normalize_role($role) {
    return str_replace(' ', '_', strtolower(trim($role)));
}

function require_roles(array $allowed_roles) {
    $userRole = normalize_role($_SESSION['role']);
    $allowed  = array_map('normalize_role', $allowed_roles);

    if (!in_array($userRole, $allowed)) {
        echo "<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied!</h2>";
        exit;
    }
}
?>
