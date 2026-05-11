<?php
session_start();

include_once "connection.php";

$name = htmlentities($_POST['cname']);
$des = htmlentities($_POST['description']);

// ✅ Validation: Name should not contain digits, only letters and spaces allowed
if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    echo "❌ Invalid name: Only letters and spaces are allowed.";
    exit;
}

$sql = "INSERT INTO categories (name, description) VALUES ('$name', '$des')";
$result = mysqli_query($con, $sql);

if ($result) {
    header("Location: categories.php");
    exit;
} else {
    echo "❌ Database Error: " . mysqli_error($con);
}
?>
