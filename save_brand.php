<?php

session_start();

include_once "connection.php";

// Get data from POST
$b_name = htmlentities($_POST['b_name']);
$description = htmlentities($_POST['description']);

// Correct SQL query
$sql = "INSERT INTO brands (b_name, description) VALUES ('$b_name', '$description')";

if(mysqli_query($con, $sql)){
    header("Location: brands.php");
} else {
    echo "Error: " . mysqli_error($con);
}
?>
