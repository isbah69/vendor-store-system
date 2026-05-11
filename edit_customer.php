<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'sales_man']);
include_once "connection.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Edit Customer</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="index.php" class="logo">
      <span class="logo-mini"><b>V</b>SS</span>
      <span class="logo-lg"><b>Vendor Store </b>System</span>
    </a>
    <nav class="navbar navbar-static-top">
      
<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">

        <span class="sr-only">Toggle navigation</span>
      </a>
    </nav>
     <style>
    .main-header .navbar {
    background-color: #0073b7 !important; /* blue */
}

.main-header .logo {
    background-color: #0073b7 !important; /* same blue for logo area */
    color: #ffffff !important; /* white text */
}

.main-header .navbar .nav > li > a {
    color: #ffffff !important; /* white icons/text */
}

.main-header .logo:hover {
    background-color: #0073b7 !important;
}
</style>
  </header>

  <?php include_once "sidemenu.php"; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Edit Customer</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Edit Customer</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Customer Details</h3>
            </div>

            <?php
            include_once "connection.php";
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $sql = "SELECT * FROM customer WHERE customer_id = $id";
                $result = mysqli_query($con, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                } else {
                    echo "<div class='alert alert-danger'>Customer not found!</div>";
                    exit;
                }
            } else {
                echo "<div class='alert alert-danger'>Invalid request.</div>";
                exit;
            }
            ?>

            <form action="update_customer.php" method="POST" role="form">
              <div class="box-body">
                <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">

                <div class="form-group">
                  <label for="name">Customer Name</label>
                  <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="form-control" id="name" placeholder="Enter Name" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="form-control" id="email" placeholder="Enter Email" required>
                </div>

                <!-- Phone -->
<div class="form-group position-relative">
<label for="phone">Phone Number:</label>
<input type="tel" id="phone" name="phone" required>
<input type="hidden" name="full_phone" id="full_phone">
<script>
const phoneInputField = document.querySelector("#phone");
const fullPhoneInput = document.querySelector("#full_phone");

const iti = window.intlTelInput(phoneInputField, {
  initialCountry: "pk", // Default to Pakistan
  preferredCountries: ["pk", "us", "in", "gb"],
  separateDialCode: true, // show country code separately
  nationalMode: false,
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
});

// When the user changes or types a number
phoneInputField.addEventListener('input', () => {
  fullPhoneInput.value = iti.getNumber(); // full international format (e.g. +923001234567)
});
</script>

                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea name="address" class="form-control" id="address" placeholder="Enter Address" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                </div>

                <div class="box-footer text-center">
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                  <a href="customer.php" class="btn btn-default">Cancel</a>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer text-center">
    <strong>&copy; 2025 Your Company.</strong> All rights reserved.
  </footer>
</div>

<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="dist/js/app.min.js"></script>
</body>
</html>
