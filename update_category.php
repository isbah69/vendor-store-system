<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'inventory_staff']);
include_once "connection.php";

$name = htmlentities($_POST["name"]);          
$description = htmlentities($_POST["description"]); 
$id = htmlentities($_POST["category_id"]);

// ✅ Validation: Category name should only contain letters and spaces
if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    echo "❌ Invalid category name: Only letters and spaces are allowed.";
    exit;
}

$sql = "UPDATE categories SET name = '$name', description = '$description' WHERE category_id = '$id'";
$result = mysqli_query($con, $sql);

if ($result) {
    header("Location: categories.php");
    exit;
} else {
    echo "❌ Error updating record: " . mysqli_error($con);
}
?>
