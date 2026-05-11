<?php
include_once "auth.php";
require_roles(['admin']);
include_once "connection.php";

// Get sale_item_id from URL
$id = $_GET['id'];

// Fetch the record from the sales_item table
$sql = "SELECT * FROM sales_item WHERE sale_item_id = $id";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    echo "Record not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Edit Sales Item</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="index.php" class="logo">
      <span class="logo-mini"><b>V</b>SS</span>
      <span class="logo-lg"><b>Admin</b>LTE</span>
    </a>
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      
<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">

        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">    </nav>
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
      <h1>Edit Sales Item</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Edit Sales Item</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Details</h3>
            </div>

            <form action="update_sales_item.php" method="POST" role="form">
              <div class="box-body">
                <input type="hidden" name="sale_item_id" value="<?php echo $row['sale_item_id']; ?>">

                <div class="form-group">
                  <label for="sale_id">Sale ID</label>
                  <input type="number" name="sale_id" class="form-control" id="sale_id"
                         value="<?php echo $row['sale_id']; ?>" required>
                </div>

                <div class="form-group">
                  <label for="product_id">Product ID</label>
                  <input type="number" name="product_id" class="form-control" id="product_id"
                         value="<?php echo $row['product_id']; ?>" required>
                </div>

                <div class="form-group">
                  <label for="quantity">Quantity</label>
                  <input type="number" name="quantity" class="form-control" id="quantity"
                         value="<?php echo $row['quantity']; ?>" required>
                </div>

                <div class="form-group">
                  <label for="unit_price">Unit Price</label>
                  <input type="text" name="unit_price" class="form-control" id="unit_price"
                         value="<?php echo $row['unit_price']; ?>" required>
                </div>

                <div class="form-group">
                  <label for="total_price">Total Price</label>
                  <input type="text" name="total_price" class="form-control" id="total_price"
                         value="<?php echo $row['total_price']; ?>" required>
                </div>
                <div class="form-group">
                  <label for="discount">Discount</label>
                  <input type="float" name="discount" class="form-control" id="discount"
                         value="<?php echo $row['discount']; ?>" required>
                </div>
                 <div class="form-group">
                  <label for="tax">Tax</label>
                  <input type="float" name="tax" class="form-control" id="tax"
                         value="<?php echo $row['tax']; ?>" required>
                </div>
              </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="sales_item.php" class="btn btn-default">Cancel</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.8
    </div>
    <strong>Copyright &copy; 2014-2016
      <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.
    </strong> All rights reserved.
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
  <div class="control-sidebar-bg"></div>
</div>

<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="plugins/fastclick/fastclick.js"></script>
<script src="dist/js/app.min.js"></script>
<script src="dist/js/demo.js"></script>
</body>
</html>
