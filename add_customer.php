<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'sales man']);
include_once "connection.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Add Customer</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Header -->
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

  <!-- Sidebar -->
  <?php include_once "sidemenu.php"; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Add New Customer</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Add Customer</li>
      </ol>
    </section>

    <!-- Main Content -->
    <section class="content">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Customer Information</h3>
            </div>

            <!-- Customer Form -->
            <form action="save_customer.php" method="POST" role="form">
              <div class="box-body">
                
                <!-- Customer Name -->
                <div class="form-group">
                  <label for="name">Customer Name</label>
                  <input type="text" 
                         name="name" 
                         class="form-control" 
                         id="name" 
                         placeholder="Enter Customer Name" 
                         required 
                         pattern="[A-Za-z\s]+" 
                         title="Name should contain only letters and spaces">
                </div>

                <!-- Email -->
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" 
                         name="email" 
                         class="form-control" 
                         id="email" 
                         placeholder="Enter Email" 
                         required>
                </div>

                <!-- Phone -->
               <!-- Contact -->
<div class="form-group position-relative">
  <label for="contact">Contact Number:</label>
  <input type="tel" id="contact" name="contact" required>
  <input type="hidden" name="full_contact" id="full_contact">

  <script>
    const contactInputField = document.querySelector("#contact");
    const fullContactInput = document.querySelector("#full_contact");

    const iti = window.intlTelInput(contactInputField, {
      initialCountry: "pk",
      preferredCountries: ["pk", "us", "in", "gb"],
      separateDialCode: true,
      nationalMode: false,
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
    });

    // when user types or changes
    contactInputField.addEventListener('input', () => {
      fullContactInput.value = iti.getNumber(); // e.g. +923001234567
    });
  </script>
</div>


<script>
const phoneInput = document.getElementById("phone");
const phoneType = document.getElementById("phoneType");

phoneInput.addEventListener("input", function() {
  const value = this.value.trim();

  // Validation pattern for both national & international formats
  const validPattern = /^(?:\+?\d{1,3})?[ -]?\d{10,14}$/;
  
  if (!value) {
    phoneType.textContent = "—";
    phoneType.className = "badge bg-secondary";
    this.setCustomValidity("");
    return;
  }

  // Detect type based on starting digits
  if (/^(\+92|92|0)3\d{9}$/.test(value)) {
    phoneType.textContent = "Pakistan 🇵🇰";
    phoneType.className = "badge bg-success";
  } else if (validPattern.test(value)) {
    phoneType.textContent = "International 🌍";
    phoneType.className = "badge bg-primary";
  } else {
    phoneType.textContent = "Invalid ❌";
    phoneType.className = "badge bg-danger";
  }

  // Real-time form validation
  if (!validPattern.test(value)) {
    this.setCustomValidity("Please enter a valid phone number (e.g. 03001234567 or +923001234567)");
  } else {
    this.setCustomValidity("");
  }
});
</script>


                <!-- Address -->
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea name="address" 
                            class="form-control" 
                            id="address" 
                            placeholder="Enter Customer Address" 
                            rows="3" 
                            required></textarea>
                </div>

              </div>

              <!-- Submit -->
              <div class="box-footer text-center">
                <button type="submit" class="btn btn-primary">Save Customer</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs"><b>Version</b> 2.3.8</div>
    <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong>
    All rights reserved.
  </footer>

</div>

<!-- Scripts -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="plugins/fastclick/fastclick.js"></script>
<script src="dist/js/app.min.js"></script>
<script src="dist/js/demo.js"></script>

</body>
</html>
