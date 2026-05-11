<?php
function allow($roles = []) {
    if (!in_array($_SESSION['role'], $roles)) {
        echo "<h3>Access Denied</h3>";
        exit();
    }
}
?>
