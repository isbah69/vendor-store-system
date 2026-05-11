<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'inventory_staff']);
include_once "connection.php";

$id            = trim(htmlentities($_POST["company_id"]));
$name          = trim(htmlentities($_POST["name"]));      
$address       = trim(htmlentities($_POST["address"]));  
$city          = trim(htmlentities($_POST["city"]));  
$state         = trim(htmlentities($_POST["state"]));
$country       = trim(htmlentities($_POST["country"]));
$postal_code   = trim(htmlentities($_POST["postal_code"]));
$contact_email = trim(htmlentities($_POST["contact_email"]));
$contact = isset($_POST['full_contact']) && $_POST['full_contact'] !== '' 
    ? trim($_POST['full_contact']) 
    : (isset($_POST['contact']) ? trim($_POST['contact']) : '');



// ✅ Validation: Name (only letters + spaces)
if (!preg_match("/^\+?[0-9]{10,15}$/", $_POST['contact'])) {
    echo "Invalid phone number";
}



// ✅ Validation: Email
if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format. Please enter a valid email.'); window.history.back();</script>";
    exit;
}

// ✅ Update query (no need to update company_id itself)
$sql = "UPDATE companies 
        SET name = '$name', 
            address = '$address', 
            city = '$city', 
            state = '$state', 
            country = '$country', 
            postal_code = '$postal_code', 
            contact_email = '$contact_email', 
            contact = '$contact' 
        WHERE company_id = '$id'";

$result = mysqli_query($con, $sql);

if ($result) {
    header("Location: companies.php");
    exit;
} else {
    echo "Error updating record: " . mysqli_error($con);
}
?>
