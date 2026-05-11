<?php
include_once "auth.php";
require_roles(['admin', 'manager', 'accountant']);
include_once "connection.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Add New</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
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
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>V</b>SS</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Vendor Store </b>System</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <!-- Sidebar toggle button-->
      
<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">

        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

        <li>
   <a href="logout.php" class="logout-btn">
  <i class="fa fa-sign-out"></i> Logout
</a>
<style>
  .logout-btn {
    background: linear-gradient(135deg, #ff4b2b, #ff416c);
    color: #fff !important;
    border-radius: 25px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(255, 65, 108, 0.35);
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(255, 65, 108, 0.5);
}

  </style>          
          </li>
          
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <div style="display: flex; align-items: center; gap: 8px;">
  <?php if (!empty($_SESSION['user_image'])): ?>
    <img src="<?php echo htmlspecialchars($_SESSION['user_image']); ?>" 
         class="img-circle" alt="User Image"
         style="width:30px; height:30px; object-fit:cover;">
  <?php else: ?>
    <div class="img-circle text-center bg-primary" 
         style="width:20px; height:20px; line-height:20px; 
                color:#fff; font-size:14px; font-weight:bold;">
      <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
          
    </div>
  <?php endif; ?>

  <span style="font-weight:600;">
    <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
  </span>
</div>

            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              
                 
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
 
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include_once "sidemenu.php"; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Edit Expense</h1>
       <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border"></div>

            <?php
            include_once "connection.php";
            $id = intval($_GET['id']);

            // Fetch expense with company name
            $sql = "SELECT e.*, c.name AS company_name 
                    FROM expenses e
                    LEFT JOIN companies c ON e.company_id = c.company_id
                    WHERE e.expense_id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
            } else {
                echo "<div class='alert alert-danger'>Expense not found.</div>";
                exit;
            }
            ?>

            <form action="update_expense.php" method="POST" role="form">
              <div class="box-body">
                <input type="hidden" name="expense_id" value="<?php echo $row['expense_id']; ?>">

                <div class="form-group">
                  <label for="company_id">Company</label>
                  <select name="company_id" id="company_id" class="form-control" required>
                    <?php
                    $companies = mysqli_query($con, "SELECT company_id, name FROM companies");
                    while($company = mysqli_fetch_assoc($companies)) {
                        $selected = ($company['company_id'] == $row['company_id']) ? "selected" : "";
                        echo "<option value='{$company['company_id']}' $selected>{$company['name']}</option>";
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="amount">Amount</label>
                  <input type="number" name="amount" value="<?php echo $row['amount']; ?>" class="form-control" id="amount" placeholder="Enter amount" required>
                </div>

                <div class="form-group">
                  <label for="expense_date">Expense Date</label>
                  <input type="date" name="expense_date" value="<?php echo $row['expense_date']; ?>" class="form-control" id="expense_date" required>
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea name="description" class="form-control" id="description" placeholder="Enter description"><?php echo $row['description']; ?></textarea>
                </div>
              </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update Expense</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs"><b>Version</b> 2.3.8</div>
    <strong>&copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights reserved.
  </footer>

</div>

<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="dist/js/app.min.js"></script>
</body>
</html>
