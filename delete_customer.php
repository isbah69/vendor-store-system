<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM customer WHERE customer_id = $id";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_affected_rows($con) > 0) {
        $check = mysqli_query($con, "SELECT COUNT(*) AS count FROM customer");
        $row = mysqli_fetch_assoc($check);
        if ($row['count'] == 0) {
            mysqli_query($con, "ALTER TABLE customer AUTO_INCREMENT = 1");
        }

        header("Location: customers.php");
        exit();
    } else {
        echo "Error deleting customer: " . mysqli_error($con);
    }
} else {
    echo "Invalid request.";
}
?>
