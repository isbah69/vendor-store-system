<?php
include_once "connection.php";
$message = "";

// Check if token exists in URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Validate token and expiry
    $query = "SELECT * FROM users WHERE reset_token = '$token' AND token_expiry > NOW() LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Handle new password submission
        if (isset($_POST['new_password'])) {
            $new_password = $_POST['new_password'];

            // Update password and clear token fields
            $update = "UPDATE users 
                       SET password = '$new_password', reset_token = NULL, token_expiry = NULL 
                       WHERE user_id = {$user['user_id']}";

            if (mysqli_query($con, $update)) {
                $message = "<div class='alert alert-success text-center'>
                                ✅ Password reset successful. <a href='login.php' class='alert-link'>Login now</a>.
                            </div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>
                                ❌ Something went wrong. Please try again.
                            </div>";
            }
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>
                        ❌ Invalid or expired token.
                    </div>";
    }
} else {
    $message = "<div class='alert alert-warning text-center'>
                    ⚠️ No token provided!
                </div>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Vender Store System | Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Vendor Store</b> System</a>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">Reset your password</p>

    <?php echo $message; ?>

    <?php if (isset($user)) { ?>
    <form method="POST">
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="new_password" placeholder="Enter New Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Update Password</button>
        </div>
      </div>
    </form>
    <?php } ?>

    <br>
    <a href="login.php" class="text-center">Back to Login</a>

  </div>
</div>

<!-- jQuery -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
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
