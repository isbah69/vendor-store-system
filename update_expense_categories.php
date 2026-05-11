<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'accountant']);
include_once "connection.php";

$id          = trim(htmlentities($_POST["category_id"]));
$name        = trim(htmlentities($_POST["name"]));
$description = trim(htmlentities($_POST["description"]));

// ✅ Validation: Name must only contain letters & spaces
if (!preg_match("/^[A-Za-z\s]+$/", $name)) {
    echo "<script>alert('Invalid category name. Only letters and spaces are allowed.'); window.history.back();</script>";
    exit;
}

// ✅ Validation: Description must not be empty
if (empty($description)) {
    echo "<script>alert('Description cannot be empty.'); window.history.back();</script>";
    exit;
}

// ✅ Update query (no need to update category_id itself)
$sql = "UPDATE expense_categories 
        SET name = '$name', description = '$description' 
        WHERE category_id = '$id'";

$result = mysqli_query($con, $sql);

if ($result) {
    header("Location: expense_categories.php");
    exit;
} else {
    echo "Error updating record: " . mysqli_error($con);
}
?>
