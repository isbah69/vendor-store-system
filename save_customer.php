<?php
session_start();

include_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $phone = htmlentities($_POST['phone']);
    $address = htmlentities($_POST['address']);

    $sql = "INSERT INTO customer (name, email, phone, address)
            VALUES ('$name', '$email', '$phone', '$address')";

    if (mysqli_query($con, $sql)) {
        // Redirect to customer page after saving
        header("Location: customers.php");
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}
?>
