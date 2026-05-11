<?php
session_start();
include_once 'connection.php'; // make sure this defines $conn (mysqli connection)

// ---------------- SAFE INPUT HANDLING ---------------- //
$id            = isset($_POST['company_id'])    ? trim(htmlentities((string)$_POST['company_id']))    : '';
$name          = isset($_POST['name'])          ? trim(htmlentities((string)$_POST['name']))          : '';
$address       = isset($_POST['address'])       ? trim(htmlentities((string)$_POST['address']))       : '';
$city          = isset($_POST['city'])          ? trim(htmlentities((string)$_POST['city']))          : '';
$state         = isset($_POST['state'])         ? trim(htmlentities((string)$_POST['state']))         : '';
$country       = isset($_POST['country'])       ? trim(htmlentities((string)$_POST['country']))       : '';
$postal_code   = isset($_POST['postal_code'])   ? trim(htmlentities((string)$_POST['postal_code']))   : '';
$contact_email = isset($_POST['contact_email']) ? trim(htmlentities((string)$_POST['contact_email'])) : '';
$contact = isset($_POST['full_contact']) && $_POST['full_contact'] !== '' 
    ? trim($_POST['full_contact']) 
    : (isset($_POST['contact']) ? trim($_POST['contact']) : '');


// ---------------- VALIDATION ---------------- //

// Clean and validate phone (supports +92, 03, etc.)
 // ✅ Server-side phone validation
    if (!preg_match("/^\+?[0-9]{10,15}$/", $_POST['contact'])) {
    echo "Invalid phone number";
}



// Validate Email format
if ($contact_email !== '' && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format. Please enter a valid email.'); window.history.back();</script>";
    exit();
}


// ---------------- DATABASE INSERT ---------------- //
$sql = "INSERT INTO companies 
        (name, address, city, state, country, postal_code, contact_email, contact) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param(
    "ssssssss",
    $name,
    $address,
    $city,
    $state,
    $country,
    $postal_code,
    $contact_email,
    $contact
);

if ($stmt->execute()) {
    header("Location: companies.php");
    exit();
} else {
    echo "<script>alert('Database Error: " . addslashes($stmt->error) . "');</script>";
}

$stmt->close();
$conn->close();
?>
