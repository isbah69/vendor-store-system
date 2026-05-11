<?php
session_start();

include_once "connection.php";

$name = trim(htmlentities($_POST["ecname"]));
$des  = trim(htmlentities($_POST["description"]));

// ✅ Validation: Name must only contain letters & spaces
if (!preg_match("/^[A-Za-z\s]+$/", $name)) {
    echo "<script>alert('Invalid category name. Only letters and spaces are allowed.'); window.history.back();</script>";
    exit;
}

// ✅ Validation: Description must not be empty
if (empty($des)) {
    echo "<script>alert('Description cannot be empty.'); window.history.back();</script>";
    exit;
}

// ✅ Insert query
$sql = "INSERT INTO expense_categories (name, description) VALUES ('$name', '$des')";
$result = mysqli_query($con, $sql);

if ($result) {
    header("Location: expense_categories.php");
    exit;
} else {
    echo "Error: " . mysqli_error($con);
}
?>
