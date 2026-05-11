<?php
session_start();
include "connection.php"; // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if ($email === "" || $password === "" || $role === "") {
        echo "<script>alert('Please fill all fields!'); window.location='login.php';</script>";
        exit();
    }

    // Fetch user by email
    $stmt = $con->prepare("SELECT user_id, email, password, role FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // --- Role check ---
        if ($user['role'] !== $role) {
            echo "<script>alert('Role does not match!'); window.location='login.php';</script>";
            exit();
        }

        // --- Password check ---
        // Plain text password comparison
        if (trim($password) === trim($user['password'])) {
            // Login success
            $_SESSION['loggedin'] = true;
            $_SESSION['userid']   = $user['id'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location='login.php';</script>";
            exit();
        }

    } else {
        echo "<script>alert('User not found!'); window.location='login.php';</script>";
        exit();
    }
}
?>
