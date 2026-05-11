<?php
session_start();
include_once "connection.php";

$error = "";

if (isset($_POST['login'])) {
    $login_input   = trim($_POST['email']);
    $password      = $_POST['password'];
    $selected_role = $_POST['role'];

    if (empty($login_input) || empty($password) || empty($selected_role)) {
        $error = "Please fill in all fields.";
    } else {
        $login_input_safe = mysqli_real_escape_string($con, strtolower($login_input));

        $query  = "SELECT * FROM users WHERE (LOWER(username)='$login_input_safe' OR LOWER(email)='$login_input_safe') LIMIT 1";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if ($password == $user['password']) {
                if (strtolower($user['role']) == strtolower($selected_role)) {
                    $_SESSION['user_id']  = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role']     = $user['role'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Incorrect role selected for this account.";
                }
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No account found with that username or email.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Vendor Store System | Login</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    /* ── Body — Light Blue & White ── */
    body {
      background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 50%, #e0f2fe 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Arial, sans-serif;
    }

    /* ── Login Box ── */
    .login-box {
      width: 420px;
      margin: 0 auto;
    }

    /* ── Logo ── */
    .login-logo {
      text-align: center;
      margin-bottom: 20px;
    }
    .login-logo a {
      font-size: 28px;
      font-weight: 800;
      color: #1a1a2e !important;
      text-decoration: none;
      letter-spacing: 0.5px;
    }
    .login-logo a b { color: #0f3460; }
    .login-logo .logo-subtitle {
      display: block;
      font-size: 12px;
      color: #666;
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-top: 4px;
      font-weight: 500;
    }

    /* ── Card ── */
    .login-box-body {
      background: #fff;
      border-radius: 16px;
      padding: 36px 32px 28px;
      box-shadow: 0 10px 40px rgba(15, 52, 96, 0.12);
      border-top: 4px solid #0f3460;
    }

    .login-box-msg {
      font-size: 14px;
      color: #777;
      text-align: center;
      margin-bottom: 24px;
      font-weight: 500;
    }

    /* ── Alert ── */
    .alert-danger {
      background: #fde8e8;
      color: #c0392b;
      border: 1px solid #f5c6cb;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ── Field Label ── */
    .field-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      margin-bottom: 7px;
    }

    /* ── Input Wrapper ── */
    .input-wrap {
      display: flex;
      align-items: center;
      border: 1.5px solid #dde2ec;
      border-radius: 8px;
      overflow: hidden;
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
      margin-bottom: 18px;
    }
    .input-wrap:focus-within {
      border-color: #0f3460;
      box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
    }
    .input-icon {
      padding: 0 13px;
      color: #aaa;
      font-size: 14px;
      background: #f0f6ff;
      border-right: 1.5px solid #dde2ec;
      height: 44px;
      display: flex;
      align-items: center;
    }
    .input-wrap input,
    .input-wrap select {
      border: none;
      outline: none;
      width: 100%;
      padding: 10px 13px;
      font-size: 14px;
      color: #333;
      background: #fff;
      height: 44px;
    }
    .input-wrap select { cursor: pointer; }
    .input-wrap input::placeholder { color: #bbb; }

    /* ── Password Toggle ── */
    .toggle-pw {
      padding: 0 13px;
      color: #aaa;
      font-size: 14px;
      cursor: pointer;
      background: none;
      border: none;
      outline: none;
      transition: color 0.2s;
    }
    .toggle-pw:hover { color: #0f3460; }

    /* ── Remember + Sign In ── */
    .login-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 6px;
      margin-bottom: 20px;
    }
    .remember-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: #666;
      cursor: pointer;
    }
    .remember-label input[type="checkbox"] {
      width: 15px;
      height: 15px;
      accent-color: #0f3460;
      cursor: pointer;
    }

    /* ── Sign In Button ── */
    .btn-signin {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px 28px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      letter-spacing: 0.4px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(15, 52, 96, 0.25);
    }
    .btn-signin:hover {
      background: linear-gradient(135deg, #0f3460, #1a1a2e);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(15, 52, 96, 0.35);
    }

    /* ── Divider ── */
    .login-divider {
      border: none;
      border-top: 1px solid #eee;
      margin: 18px 0;
    }

    /* ── Bottom Links ── */
    .login-links {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
    }
    .login-links a {
      color: #0f3460;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.2s;
    }
    .login-links a:hover {
      color: #6f42c1;
      text-decoration: underline;
    }

    /* ── Footer Note ── */
    .login-footer-note {
      text-align: center;
      margin-top: 18px;
      font-size: 12px;
      color: #888;
    }

    /* ── Floating Circles Decoration ── */
    .bg-circle {
      position: fixed;
      border-radius: 50%;
      opacity: 0.07;
      background: #0f3460;
      z-index: 0;
      pointer-events: none;
    }
    .bg-circle-1 { width: 400px; height: 400px; top: -100px; left: -100px; }
    .bg-circle-2 { width: 300px; height: 300px; bottom: -80px; right: -80px; }
    .bg-circle-3 { width: 200px; height: 200px; top: 40%; right: 10%; }

    .login-box { position: relative; z-index: 1; }
  </style>
</head>

<body>

  <!-- Background Decoration -->
  <div class="bg-circle bg-circle-1"></div>
  <div class="bg-circle bg-circle-2"></div>
  <div class="bg-circle bg-circle-3"></div>

  <div class="login-box">

    <!-- Logo -->
    <div class="login-logo">
      <a href="#">
        <b>Vendor Store</b> System
        <span class="logo-subtitle">Inventory &amp; Sales Management</span>
      </a>
    </div>

    <!-- Card -->
    <div class="login-box-body">

      <p class="login-box-msg">
        <i class="fa fa-lock" style="color:#0f3460; margin-right:6px;"></i>
        Sign in to your account
      </p>

      <!-- Error -->
      <?php if ($error != ""): ?>
        <div class="alert-danger">
          <i class="fa fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST">

        <!-- Username / Email -->
        <label class="field-label">
          <i class="fa fa-user"></i> &nbsp;Username or Email
        </label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa fa-envelope"></i></span>
          <input type="text"
                 name="email"
                 placeholder="Enter username or email"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                 required>
        </div>

        <!-- Role -->
        <label class="field-label">
          <i class="fa fa-id-badge"></i> &nbsp;Login As
        </label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa fa-briefcase"></i></span>
          <select name="role" required>
            <option value="" disabled selected>-- Select Your Role --</option>
            <option value="admin"           <?php echo (($_POST['role'] ?? '') == 'admin')           ? 'selected' : ''; ?>>Admin</option>
            <option value="manager"         <?php echo (($_POST['role'] ?? '') == 'manager')         ? 'selected' : ''; ?>>Manager</option>
            <option value="sales man"       <?php echo (($_POST['role'] ?? '') == 'sales man')       ? 'selected' : ''; ?>>Sales Man</option>
            <option value="inventory_staff" <?php echo (($_POST['role'] ?? '') == 'inventory_staff') ? 'selected' : ''; ?>>Inventory Staff</option>
            <option value="accountant"      <?php echo (($_POST['role'] ?? '') == 'accountant')      ? 'selected' : ''; ?>>Accountant</option>
            <option value="user"            <?php echo (($_POST['role'] ?? '') == 'user')            ? 'selected' : ''; ?>>User</option>
          </select>
        </div>

        <!-- Password -->
        <label class="field-label">
          <i class="fa fa-lock"></i> &nbsp;Password
        </label>
        <div class="input-wrap">
          <span class="input-icon"><i class="fa fa-lock"></i></span>
          <input type="password"
                 name="password"
                 id="passwordField"
                 placeholder="Enter your password"
                 required>
          <button type="button" class="toggle-pw" onclick="togglePassword()" title="Show / Hide Password">
            <i class="fa fa-eye" id="eyeIcon"></i>
          </button>
        </div>

        <!-- Remember + Submit -->
        <div class="login-actions">
          <label class="remember-label">
            <input type="checkbox" name="remember"> Remember me
          </label>
          <button type="submit" name="login" class="btn-signin">
            <i class="fa fa-sign-in"></i> &nbsp;Sign In
          </button>
        </div>

      </form>

      <hr class="login-divider">

      <!-- Bottom Links -->
      <div class="login-links">
        <a href="forgot_password.php">
          <i class="fa fa-key"></i> Forgot Password?
        </a>
        <a href="register.php">
          <i class="fa fa-user-plus"></i> Register
        </a>
      </div>

    </div>
    <!-- /.login-box-body -->

    <!-- Footer -->
    <div class="login-footer-note">
      &copy; 2025 Vendor Store System. All rights reserved.
    </div>

  </div>
  <!-- /.login-box -->

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>

<script>
  function togglePassword() {
    const field   = document.getElementById('passwordField');
    const eyeIcon = document.getElementById('eyeIcon');
    if (field.type === 'password') {
      field.type        = 'text';
      eyeIcon.className = 'fa fa-eye-slash';
    } else {
      field.type        = 'password';
      eyeIcon.className = 'fa fa-eye';
    }
  }
</script>
</body>
</html>