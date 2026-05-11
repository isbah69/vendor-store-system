<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include_once "connection.php";

$message = "";
$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

if(isset(htmlentities($_POST['update'])){
    $first_name = mysqli_real_escape_string($con, htmlentities($_POST['first_name']);
    $last_name  = mysqli_real_escape_string($con, htmlentities($_POST['last_name']);
    $phone      = mysqli_real_escape_string($con, htmlentities($_POST['phone']);

    $update = mysqli_query($con, "UPDATE users SET first_name='$first_name', last_name='$last_name', phone='$phone' WHERE user_id='$user_id'");
    if($update){
        $message = "Profile updated successfully!";
        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;
        $user['phone'] = $phone;
    } else {
        $message = "Failed to update profile!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Profile | Vendor Store</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php include('dashboard_sidebar.php'); // optional sidebar include ?>

  <div class="content-wrapper">
    <section class="content-header"><h1>Profile</h1></section>
    <section class="content">
      <?php if($message) echo "<div class='alert alert-info'>$message</div>"; ?>
      <form method="post">
        <div class="form-group">
          <label>Username</label>
          <input type="text" class="form-control" value="<?php echo $user['username']; ?>" readonly>
        </div>
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="first_name" class="form-control" value="<?php echo $user['first_name']; ?>" required>
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="last_name" class="form-control" value="<?php echo $user['last_name']; ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>" required>
        </div>
        <div class="form-group">
          <label>Role</label>
          <input type="text" class="form-control" value="<?php echo $user['role']; ?>" readonly>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
      </form>
    </section>
  </div>
</div>
</body>
</html>
