<?php
include_once "connection.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm_password'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    $agree    = $_POST['agree'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (empty($agree)) {
        $error = "You must agree to the terms.";
    } else {
        // Check if username already exists
        $check = mysqli_query($con, "SELECT user_id FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // Check if email already exists
            $checkEmail = mysqli_query($con, "SELECT user_id FROM users WHERE email='$email'");
            if (mysqli_num_rows($checkEmail) > 0) {
                $error = "Email already registered. Please use another email.";
            } else {
                // Hash password and save
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO users (username, password, email, phone, role, reset_token, token_expiry)
                        VALUES ('$username', '$hashed', '$email', '', '$role', NULL, NULL)";
                if (mysqli_query($con, $sql)) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Vendor Store System | Register</title>
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
    .register-box { width: 400px; }

    .register-box-body {
      border-top: 4px solid #3c8dbc;
      border-radius: 0 0 6px 6px;
    }

    .register-logo a {
      font-size: 26px;
      font-weight: 700;
      color: #333;
    }

    .register-logo a b { color: #3c8dbc; }

    .form-control {
      border-radius: 4px;
      height: 40px;
    }

    .btn-register {
      background: linear-gradient(135deg, #3c8dbc, #1a6a9a);
      color: #fff;
      border: none;
      border-radius: 4px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }

    .btn-register:hover {
      background: linear-gradient(135deg, #1a6a9a, #3c8dbc);
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(60,141,188,0.4);
    }

    .alert { border-radius: 4px; font-size: 13px; padding: 10px 14px; }

    select.form-control { height: 40px; }

    .login-link {
      display: block;
      text-align: center;
      margin-top: 14px;
      font-size: 13px;
      color: #555;
    }

    .login-link a { color: #3c8dbc; font-weight: 600; }
    .login-link a:hover { text-decoration: underline; }
  </style>
</head>
<body class="hold-transition register-page">

<div class="register-box">
  <div class="register-logo">
    <a href="index.php"><b>Vendor Store </b>System</a>
  </div>

  <div class="register-box-body">
    <p class="login-box-msg">Register a New Member</p>

    <!-- Error Message -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if (!empty($success)): ?>
      <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST">

      <!-- Username -->
      <div class="form-group has-feedback">
        <input type="text"
               name="username"
               class="form-control"
               placeholder="Username"
               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
               required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>

      <!-- Email -->
      <div class="form-group has-feedback">
        <input type="email"
               name="email"
               class="form-control"
               placeholder="Email Address"
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
               required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>

      <!-- Password -->
      <div class="form-group has-feedback">
        <input type="password"
               name="password"
               class="form-control"
               placeholder="Password"
               required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <!-- Confirm Password -->
      <div class="form-group has-feedback">
        <input type="password"
               name="confirm_password"
               class="form-control"
               placeholder="Confirm Password"
               required>
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>

      <!-- Role -->
      <div class="form-group has-feedback">
        <select name="role" class="form-control" required>
          <option value="" disabled selected>-- Select Role --</option>
          <option value="admin"           <?php echo (($_POST['role'] ?? '') == 'admin')           ? 'selected' : ''; ?>>Admin</option>
          <option value="manager"         <?php echo (($_POST['role'] ?? '') == 'manager')         ? 'selected' : ''; ?>>Manager</option>
          <option value="inventory_staff" <?php echo (($_POST['role'] ?? '') == 'inventory_staff') ? 'selected' : ''; ?>>Inventory Staff</option>
          <option value="sales man"       <?php echo (($_POST['role'] ?? '') == 'sales man')       ? 'selected' : ''; ?>>Sales Man</option>
          <option value="accountant"      <?php echo (($_POST['role'] ?? '') == 'accountant')      ? 'selected' : ''; ?>>Accountant</option>
        </select>
        <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
      </div>

      <!-- Terms Checkbox -->
      <div class="row" style="margin-top: 10px;">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="agree" value="1"
                <?php echo isset($_POST['agree']) ? 'checked' : ''; ?>>
              I agree to the <a href="#">terms</a>
            </label>
          </div>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-register btn-block btn-flat">
            <i class="fa fa-user-plus"></i> Register
          </button>
        </div>
      </div>

    </form>

    <div class="login-link">
      Already have an account? <a href="login.php">Sign in here</a>
    </div>

  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<!-- Bootstrap -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%'
    });
  });
</script>

</body>
</html>