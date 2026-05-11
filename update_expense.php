<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'accountant']);
include_once "connection.php";

// update_expense.php
include_once "connection.php";

// basic validation and sanitization
$id          = isset($_POST['expense_id']) ? intval(htmlentities($_POST['expense_id'])) : 0;
$cid         = isset($_POST['company_id']) ? intval(htmlentities($_POST['company_id'])) : null;
$amount      = isset($_POST['amount']) ? htmlentities($_POST['amount']) : null;
$date        = isset($_POST['expense_date']) ? htmlentities($_POST['expense_date']) : null;
$description = isset($_POST['description']) ? trim(htmlentities($_POST['description'])) : null;

if ($id <= 0) {
    die("Invalid expense id.");
}

// validate amount as number
if (!is_numeric($amount)) {
    die("Invalid amount.");
}
$amount = floatval($amount);

// format date to YYYY-MM-DD (will produce false on invalid)
$formatted_date = date('Y-m-d', strtotime($date));
if (!$formatted_date) {
    die("Invalid date.");
}

// prepared statement for update
$stmt = mysqli_prepare($con, "UPDATE expenses SET company_id = ?, amount = ?, expense_date = ?, description = ? WHERE expense_id = ?");
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}

// types: i (company_id), d (amount), s (expense_date), s (description), i (expense_id)
mysqli_stmt_bind_param($stmt, "idssi", $cid, $amount, $formatted_date, $description, $id);

$executed = mysqli_stmt_execute($stmt);
if ($executed) {
    mysqli_stmt_close($stmt);
    // redirect back to the listing so changes are visible immediately
    header("Location: expenses.php");
    exit();
} else {
    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    echo "Error updating record: " . htmlspecialchars($err);
}
?>
