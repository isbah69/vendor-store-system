<?php
// login_check.php - FINAL SECURE VERSION

include_once "connection.php"; 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // FIX: Safely retrieve and trim POST data, defaulting to an empty string to prevent warnings
    // (This fixes the "Undefined array key" warning you were getting)
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // The role from the form is IGNORING for security.

    if (empty($email) || empty($password)) {
        // Redirect back to login if fields are empty
        header("Location: login.php?error=empty_fields");
        exit();
    }

    // 1. Prepare SQL statement to fetch user data and HASHED password
    $stmt = mysqli_prepare($con, "SELECT user_id, name, email, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        
        // 2. User exists: Verify the password hash
        if (password_verify($password, $row['password'])) {
            // ✅ SUCCESS: Password verified

            // Set session variables
            $_SESSION['is_loggedin'] = true; 
            $_SESSION['user_id'] = $row['user_id'];
            
            // Set the CORRECT role from the database (THIS IS THE RBAC KEY)
            $_SESSION['role'] = strtolower($row['role']); 
            
            // Set username and initial 
            $_SESSION['username'] = !empty($row['name']) ? $row['name'] : ucfirst(explode('@', $email)[0]);
            $_SESSION['user_initial'] = strtoupper(substr($_SESSION['username'], 0, 1));
            
            // Redirect to dashboard
            header("Location: index.php");
            exit();

        } else {
            // ❌ Failure: Password mismatch
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
    } else {
        // ❌ Failure: User not found
        header("Location: login.php?error=invalid_credentials");
        exit();
    }

    mysqli_stmt_close($stmt);
}

// Redirect non-POST requests
header("Location: login.php");
exit();
?>