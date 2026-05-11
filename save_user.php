<?php
session_start();
include_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Clean and secure inputs
    $uname    = isset($_POST["user_name"]) ? trim(htmlentities($_POST["user_name"])) : '';
    $password = isset($_POST["password"]) ? trim(htmlentities($_POST["password"])) : '';

    $email    = isset($_POST["email"]) ? trim(htmlentities($_POST["email"])) : '';


    $role     = isset($_POST["role"]) ? trim(htmlentities($_POST["role"])) : 'User';
    $reset_token     = isset($_POST["reset_token"]) ? trim(htmlentities($_POST["reset_token"])) : 'reset_token'; // Default role
    $token_expiry     = isset($_POST["token_expiry"]) ? trim(htmlentities($_POST["token_expiry"])) : 'token_expiry'; // Default role



    // ✅ Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('❌ Invalid email format!'); window.history.back();</script>";
        exit;
    }

    // ✅ Prevent duplicate users (based on email)
    $check = mysqli_query($con, "SELECT * FROM users WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('⚠️ Email already registered! Please log in instead.'); window.location='login.php';</script>";
        exit;
    }

    // ✅ Hash password for security (recommended)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert new user
    $query = "INSERT INTO users (username, password, email, role, reset_token, token_expiry)
              VALUES ('$uname', '$hashedPassword', '$email', '$role', '$reset_token', '$token_expiry')";

    if (mysqli_query($con, $query)) {
        // ✅ Get inserted user ID
        $user_id = mysqli_insert_id($con);

        // ✅ Start login session for new user
        $_SESSION['user_id']   = $user_id;
        $_SESSION['user_name'] = $fname . ' ' . $lname;
        $_SESSION['user_role'] = $role;

        echo "<script>alert('🎉 Registration successful! Welcome, $fname.'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('⚠️ Error saving user: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}
?>
