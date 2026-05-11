<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'inventory_staff']);
include_once "connection.php";

$id = htmlentities($_POST["brand_id"]);
$name = htmlentities($_POST["b_name"]);          
$description = htmlentities($_POST["description"]); 

// ✅ Validation: Brand name should only contain letters and spaces
if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    echo "❌ Invalid brand name: Only letters and spaces are allowed.";
    exit;
}

$sql = "UPDATE brands SET b_name = '$name', description = '$description' WHERE brand_id = '$id'";
$result = mysqli_query($con, $sql);

if ($result) {
    header("Location: brands.php");
    exit;
} else {
    echo "❌ Error updating record: " . mysqli_error($con);
}
?>
