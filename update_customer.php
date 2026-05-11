<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'sales_man']);
include_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = htmlentities($_POST['customer_id']);
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $phone = htmlentities($_POST['phone']);
    $address = htmlentities($_POST['address']);

    $sql = "UPDATE customer 
            SET name='$name', email='$email', phone='$phone', address='$address'
            WHERE customer_id = $id";

    if (mysqli_query($con, $sql)) {
        header("Location: customers.php"); // Redirect to customer list
        exit();
    } else {
        echo "<script>alert('Error updating record: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}
?>
