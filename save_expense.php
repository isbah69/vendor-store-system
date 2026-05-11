<?php
session_start();

include_once "connection.php";

// collect form fields
$company_name   = htmlentities($_POST["name"]);
$amount         = htmlentities($_POST["amount"]);
$date           = htmlentities($_POST["date"]);
$formatted_date = date('Y-m-d', strtotime($date));
$des            = htmlentities($_POST["description"]);

$sql = "INSERT INTO expenses (company_id, amount, expense_date, description) 
        VALUES ('$company_name', '$amount', '$formatted_date', '$des')";


$result = mysqli_query($con, $sql);

// redirect or show error
if($result) {
    header("Location: expenses.php");
    exit;
} else {
    echo "Error: " . mysqli_error($con);
}
?>
