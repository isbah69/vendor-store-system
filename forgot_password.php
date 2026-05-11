<?php
include_once "connection.php";
$message = "";
$msgType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_email = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check_email) > 0) {
        $token  = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $update = mysqli_query($con, "UPDATE users SET reset_token='$token', token_expiry='$expiry' WHERE email='$email'");

        if ($update) {
            $reset_link = "http://localhost/fyp/reset_password.php?token=$token";
            $message  = "Reset link generated! Click below to reset your password:<br><br>
                         <a href='$reset_link' class='btn btn-success btn-sm btn-flat'>
                         <i class='fa fa-key'></i> Click Here to Reset Password</a>";
            $msgType  = "success";
        } else {
            $message = "Error saving token: " . mysqli_error($con);
            $msgType = "danger";
        }
    } else {
        $message = "No account found with that email address.";
        $msgType = "danger";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Vendor Store System | Forgot Password</title>
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
    body { background: #ecf0f5; }

    .login-box {
      width: 400px;
      margin: 7% auto;
    }

    .login-logo a {
      font-size: 26px;
      font-weight: 700;
      color: #333;
    }

    .login-logo a b { color: #3c8dbc; }

    .login-box-body {
      border-top: 4px solid #3c8dbc;
      border-radius: 0 0 6px 6px;
      padding: 30px;
    }

    .login-box-msg {
      font-size: 15px;
      color: #666;
      margin-bottom: 20px;
      text-align: center;
    }

    .form-control {
      border-radius: 4px;
      height: 42px;
      font-size: 14px;
    }

    .btn-reset {
      background: linear-gradient(135deg, #3c8dbc, #1a6a9a);
      color: #fff;
      border: none;
      border-radius: 4px;
      font-weight: 600;
      height: 42px;
      font-size: 14px;
      letter-spacing: 0.4px;
      transition: all 0.3s ease;
    }

    .btn-reset:hover {
      background: linear-gradient(135deg, #1a6a9a, #3c8dbc);
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(60,141,188,0.45);
    }

    .alert {
      border-radius: 4px;
      font-size: 13px;
      padding: 12px 16px;
      margin-bottom: 20px;
    }

    .divider {
      border: none;
      border-top: 1px solid #eee;
      margin: 20px 0;
    }

    .bottom-links {
      text-align: center;
      font-size: 13px;
      color: #777;
    }

    .bottom-links a {
      color: #3c8dbc;
      font-weight: 600;
    }

    .bottom-links a:hover { text-decoration: underline; }

    .icon-wrap {
      text-align: center;
      margin-bottom: 18px;
    }

    .icon-wrap .fa {
      font-size: 48px;
      color: #3c8dbc;
      opacity: 0.85;
    }
  </style>
</head>
<body class="hold-transition login-page">

<div class="login-box">

  <!-- Logo -->
  <div class="login-logo">
    <a href="index.php"><b>Vendor Store </b>System</a>
  </div>

  <div class="login-box-body">

    <!-- Icon -->
    <div class="icon-wrap">
      <i class="fa fa-lock"></i>
    </div>

    <p class="login-box-msg">Forgot your password? Enter your registered email address and we will generate a reset link for you.</p>

    <!-- Alert Message -->
    <?php if (!empty($message)): ?>
      <div class="alert alert-<?php echo $msgType; ?>">
        <i class="fa fa-<?php echo $msgType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <!-- Form -->
    <?php if (empty($message) || $msgType === 'danger'): ?>
    <form method="POST" action="">
      <div class="form-group has-feedback">
        <input type="email"
               name="email"
               class="form-control"
               placeholder="Enter your registered email"
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
               required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>

      <div class="form-group" style="margin-top: 20px;">
        <button type="submit" class="btn btn-reset btn-block btn-flat">
          <i class="fa fa-paper-plane"></i> &nbsp;Send Reset Link
        </button>
      </div>
    </form>
    <?php endif; ?>

    <hr class="divider">

    <div class="bottom-links">
      <i class="fa fa-arrow-left"></i>
      <a href="login.php"> Back to Login</a>
      &nbsp;&nbsp;|&nbsp;&nbsp;
      <i class="fa fa-user-plus"></i>
      <a href="register.php"> Register</a>
    </div>

  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<!-- Bootstrap -->
<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>