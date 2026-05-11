<?php
session_start();
include_once "connection.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = strtolower(trim($_SESSION['role']));

// ── Dashboard Stats ──
$total_products  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM products"))['t']  ?? 0;
$total_companies = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM companies"))['t'] ?? 0;
$total_sales     = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM sales"))['t']     ?? 0;
$total_users     = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM users"))['t']     ?? 0;
$total_customers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM customer"))['t']  ?? 0;
$total_brands    = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS t FROM brands"))['t']    ?? 0;

$revenue_row     = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(total_amount) AS t FROM sales"));
$total_revenue   = number_format($revenue_row['t'] ?? 0, 2);

$expense_row     = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(amount) AS t FROM expenses"));
$total_expenses  = number_format($expense_row['t'] ?? 0, 2);

// ── Monthly Sales Chart Data ──
$start = date('Y-m-01');
$end   = date('Y-m-t');
$sql_line    = "SELECT DATE(sale_date) as day, SUM(total_amount) as total FROM sales WHERE sale_date BETWEEN '$start' AND '$end' GROUP BY DATE(sale_date)";
$res_line    = mysqli_query($con, $sql_line);
$line_labels = [];
$line_data   = [];
while ($row = mysqli_fetch_assoc($res_line)) {
    $line_labels[] = date('d M', strtotime($row['day']));
    $line_data[]   = (float)$row['total'];
}

// ── Company Sales Doughnut ──
$sql_dnt       = "SELECT c.name AS company_name, SUM(s.total_amount) AS total FROM sales s JOIN companies c ON s.company_id = c.company_id GROUP BY s.company_id ORDER BY total DESC LIMIT 8";
$res_dnt       = mysqli_query($con, $sql_dnt);
$company_labels = [];
$company_data   = [];
while ($row = mysqli_fetch_assoc($res_dnt)) {
    $company_labels[] = $row['company_name'];
    $company_data[]   = (float)$row['total'];
}

// ── Recent Sales ──
$recent_sales = mysqli_query($con, "SELECT s.sale_id, c.name AS company, s.sale_date, s.total_amount FROM sales s LEFT JOIN companies c ON s.company_id = c.company_id ORDER BY s.sale_id DESC LIMIT 8");

// ── Top Products ──
$top_products = mysqli_query($con, "SELECT p.p_name, SUM(si.quantity) AS total_sold FROM sales_item si JOIN products p ON si.product_id = p.product_id GROUP BY si.product_id ORDER BY total_sold DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Vendor Store System | Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    /* ══ STAT CARDS ══ */
    .stat-card {
      border-radius: 12px;
      padding: 20px 22px;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 18px;
      margin-bottom: 20px;
      box-shadow: 0 4px 18px rgba(0,0,0,0.13);
      transition: transform 0.2s, box-shadow 0.2s;
      position: relative;
      overflow: hidden;
    }
    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 28px rgba(0,0,0,0.18);
    }
    .stat-card::after {
      content: '';
      position: absolute;
      right: -18px; top: -18px;
      width: 90px; height: 90px;
      border-radius: 50%;
      background: rgba(255,255,255,0.1);
    }
    .stat-card-icon {
      width: 56px; height: 56px;
      border-radius: 12px;
      background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center;
      font-size: 24px;
      flex-shrink: 0;
    }
    .stat-card-text .label-text {
      font-size: 12px;
      font-weight: 600;
      opacity: 0.85;
      text-transform: uppercase;
      letter-spacing: 0.7px;
    }
    .stat-card-text .value-text {
      font-size: 28px;
      font-weight: 800;
      line-height: 1.1;
    }
    .stat-card-text .sub-text {
      font-size: 11px;
      opacity: 0.75;
      margin-top: 2px;
    }

    .card-blue    { background: linear-gradient(135deg, #1a6abd, #0f3460); }
    .card-green   { background: linear-gradient(135deg, #1e7e34, #28a745); }
    .card-orange  { background: linear-gradient(135deg, #d35400, #e67e22); }
    .card-purple  { background: linear-gradient(135deg, #4a2c91, #6f42c1); }
    .card-red     { background: linear-gradient(135deg, #a93226, #e74c3c); }
    .card-teal    { background: linear-gradient(135deg, #117a65, #1abc9c); }
    .card-navy    { background: linear-gradient(135deg, #1a1a2e, #0f3460); }
    .card-pink    { background: linear-gradient(135deg, #c0392b, #ff6b6b); }

    /* ══ SECTION BOXES ══ */
    .dash-box {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 16px rgba(0,0,0,0.07);
      margin-bottom: 24px;
      overflow: hidden;
    }
    .dash-box-header {
      padding: 14px 20px;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .dash-box-title {
      font-size: 14px;
      font-weight: 700;
      color: #222;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .dash-box-title .fa { color: #0f3460; }
    .dash-box-body { padding: 18px 20px; }

    /* ══ WELCOME BANNER ══ */
    .welcome-banner {
      background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 60%, #1a6abd 100%);
      border-radius: 14px;
      padding: 26px 30px;
      color: #fff;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 6px 24px rgba(15,52,96,0.25);
      position: relative;
      overflow: hidden;
    }
    .welcome-banner::before {
      content: '';
      position: absolute;
      right: -40px; top: -40px;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(255,255,255,0.05);
    }
    .welcome-banner::after {
      content: '';
      position: absolute;
      right: 60px; bottom: -60px;
      width: 150px; height: 150px;
      border-radius: 50%;
      background: rgba(255,255,255,0.04);
    }
    .welcome-text h2 {
      font-size: 22px;
      font-weight: 800;
      margin: 0 0 4px;
    }
    .welcome-text h2 span { color: #a78bfa; }
    .welcome-text p {
      font-size: 13px;
      opacity: 0.75;
      margin: 0;
    }
    .welcome-meta {
      text-align: right;
      font-size: 12px;
      opacity: 0.7;
      z-index: 1;
    }
    .welcome-meta .date-big {
      font-size: 15px;
      font-weight: 700;
      opacity: 0.9;
    }
    .role-badge {
      display: inline-block;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.25);
      border-radius: 20px;
      padding: 3px 14px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-top: 8px;
    }

    /* ══ RECENT SALES TABLE ══ */
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table thead tr {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: #fff;
    }
    .dash-table thead th {
      padding: 10px 14px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }
    .dash-table tbody tr:nth-child(even) { background: #f8f9fc; }
    .dash-table tbody tr:hover { background: #eef3fb; }
    .dash-table tbody td {
      padding: 10px 14px;
      font-size: 13px;
      color: #444;
      border-bottom: 1px solid #f0f0f0;
    }
    .badge-status {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
    }
    .badge-completed { background: #e6f9ee; color: #1e7e34; }

    /* ══ TOP PRODUCTS BARS ══ */
    .product-bar-item { margin-bottom: 14px; }
    .product-bar-label {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      font-weight: 600;
      color: #333;
      margin-bottom: 5px;
    }
    .product-bar-track {
      background: #f0f2f5;
      border-radius: 20px;
      height: 8px;
      overflow: hidden;
    }
    .product-bar-fill {
      height: 100%;
      border-radius: 20px;
      background: linear-gradient(90deg, #0f3460, #1a6abd);
      transition: width 1s ease;
    }

    /* ══ SIDEBAR CUSTOM ══ */
    .sidebar-menu > li > a {
      border-radius: 0;
      transition: all 0.2s;
    }
    .sidebar-menu > li.active > a,
    .sidebar-menu > li > a:hover {
      background: rgba(255,255,255,0.1) !important;
      border-left: 3px solid #3c8dbc;
    }

    /* ══ LOGOUT BTN ══ */
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
      box-shadow: 0 4px 10px rgba(255,65,108,0.35);
    }
    .logout-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(255,65,108,0.5);
    }

    /* ══ TOGGLE BTN ══ */
    .glow-toggle {
      color: #fff !important;
      padding: 8px 12px;
      border-radius: 6px;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.12);
    }
    .glow-toggle i { font-size: 18px; }
    .glow-toggle:hover { background: rgba(255,255,255,0.25); }
    /* ══ FIXED SIDEBAR — SCROLLABLE CONTENT ONLY ══ */

/* Fix the entire wrapper to full height */
html, body, .wrapper {
  height: 100%;
  overflow: hidden !important;
}

/* Fix sidebar in place — never scrolls */
.main-sidebar,
.left-side {
  position: fixed !important;
  top: 0;
  left: 0;
  height: 100% !important;
  overflow-y: auto !important;   /* sidebar itself scrolls if menu is long */
  overflow-x: hidden !important;
  z-index: 810 !important;
  
  /* Hide scrollbar but keep scroll ability */
  scrollbar-width: none !important;        /* Firefox */
  -ms-overflow-style: none !important;     /* IE */
}
.main-sidebar::-webkit-scrollbar,
.left-side::-webkit-scrollbar {
  display: none !important;                /* Chrome/Safari */
}

/* Fix the top header in place */
.main-header {
  position: fixed !important;
  top: 0;
  left: 0;
  right: 0;
  z-index: 820 !important;
  width: 100% !important;
}

/* Content wrapper scrolls — not the whole page */
.content-wrapper,
.right-side {
  overflow-y: auto !important;
  overflow-x: hidden !important;
  height: 100vh !important;

  /* Push down below fixed header */
  margin-top: 50px !important;

  /* Push right to avoid sidebar overlap */
  margin-left: 230px !important;

  /* Full height minus header */
  min-height: calc(100vh - 50px) !important;
}

/* Sidebar mini (collapsed) — content takes full width */
.sidebar-mini.sidebar-collapse .content-wrapper,
.sidebar-mini.sidebar-collapse .right-side {
  margin-left: 50px !important;
}

/* Fix footer to stay below content */
.main-footer {
  position: relative !important;
  z-index: 1 !important;
  margin-left: 0 !important;
}
/* ══ SIDEBAR DARK COLOR — FIXED ══ */
.main-sidebar,
.left-side,
.sidebar,
.main-sidebar .sidebar,
aside.main-sidebar,
.skin-blue .main-sidebar,
.skin-blue .left-side,
.skin-blue-dark .main-sidebar,
.skin-blue-dark .left-side,
.sidebar-menu,
.main-sidebar > .sidebar,
section.sidebar {
  background: #1a1a2e !important;
  background-color: #1a1a2e !important;
  background-image: none !important;
}
/* ══ MATCH HEADER HEIGHT ONLY ══ */
.main-header .logo,
.skin-blue .main-header .logo,
.skin-blue-dark .main-header .logo {
  height: 50px !important;
  line-height: 50px !important;
}

.main-header .navbar,
.skin-blue .main-header .navbar,
.skin-blue-dark .main-header .navbar {
  height: 50px !important;
  min-height: 50px !important;
}

.main-header .navbar .nav > li > a {
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  height: 50px !important;
  line-height: 50px !important;
}

.main-header .sidebar-toggle {
  height: 50px !important;
  line-height: 50px !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}
  </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- ══ HEADER ══ -->
  <header class="main-header">
    <a href="index.php" class="logo">
      <span class="logo-mini"><b>V</b>SS</span>
      <span class="logo-lg"><b>Vendor Store</b> System</span>
    </a>

    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle glow-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-bars"></i>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- Logout -->
          <li>
            <a href="logout.php" class="logout-btn">
              <i class="fa fa-sign-out"></i> Logout
            </a>
          </li>

          <!-- User -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <div style="display:flex; align-items:center; gap:8px;">
                <?php if (!empty($_SESSION['user_image'])): ?>
                  <img src="<?php echo htmlspecialchars($_SESSION['user_image']); ?>"
                       class="img-circle" style="width:30px;height:30px;object-fit:cover;" alt="User">
                <?php else: ?>
                  <div class="img-circle bg-primary text-center"
                       style="width:28px;height:28px;line-height:28px;color:#fff;font-size:13px;font-weight:bold;">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                  </div>
                <?php endif; ?>
                <span style="font-weight:600;font-size:13px;">
                  <?php echo htmlspecialchars($username); ?>
                </span>
              </div>
            </a>
          </li>

        </ul>
      </div>
    </nav>
  </header>

  <!-- ══ SIDEBAR ══ -->
  <aside class="main-sidebar">
    <section class="sidebar">

      <!-- User Panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <?php if (!empty($_SESSION['user_image'])): ?>
            <img src="<?php echo htmlspecialchars($_SESSION['user_image']); ?>" class="img-circle" alt="User">
          <?php else: ?>
            <div class="img-circle bg-primary text-center"
                 style="width:45px;height:45px;line-height:45px;color:#fff;font-size:20px;font-weight:bold;">
              <?php echo strtoupper(substr($username, 0, 1)); ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- Search -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
            <button type="submit" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>

      <!-- Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION</li>

        <!-- Dashboard for all -->
        <li class="active">
          <a href="index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>

        <?php if ($role == "admin" || $role == "manager" || $role == "inventory" || $role == "inventory_staff"): ?>
          <li class="header">INVENTORY</li>
          <li><a href="brands.php"><i class="fa fa-tags"></i> <span>Brands</span></a></li>
          <li><a href="categories.php"><i class="fa fa-list"></i> <span>Categories</span></a></li>
          <li><a href="companies.php"><i class="fa fa-building"></i> <span>Companies</span></a></li>
          <li><a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a></li>
          <li><a href="Returns.php"><i class="fa fa-undo"></i> <span>Returns</span></a></li>
        <?php endif; ?>

        <?php if ($role == "sales man" || $role == "salesman"): ?>
          <li class="header">SALES</li>
          <li><a href="customers.php"><i class="fa fa-users"></i> <span>Customers</span></a></li>
          <li><a href="Sales.php"><i class="fa fa-shopping-cart"></i> <span>Sales</span></a></li>
          <li><a href="sales_item.php"><i class="fa fa-file-text"></i> <span>Sales Items</span></a></li>
          <li><a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a></li>
        <?php endif; ?>

        <?php if ($role == "admin"): ?>
          <li class="header">SALES</li>
          <li><a href="customers.php"><i class="fa fa-users"></i> <span>Customers</span></a></li>
          <li><a href="Sales.php"><i class="fa fa-shopping-cart"></i> <span>Sales</span></a></li>
          <li><a href="sales_item.php"><i class="fa fa-file-text"></i> <span>Sales Items</span></a></li>
          <li class="header">FINANCE</li>
          <li><a href="expenses.php"><i class="fa fa-credit-card"></i> <span>Expenses</span></a></li>
          <li><a href="expense_categories.php"><i class="fa fa-folder-open"></i> <span>Expense Categories</span></a></li>
          <li class="header">SYSTEM</li>
          <li><a href="user.php"><i class="fa fa-users"></i> <span>Users</span></a></li>
        <?php endif; ?>

        <?php if ($role == "accountant"): ?>
          <li class="header">FINANCE</li>
          <li><a href="expenses.php"><i class="fa fa-credit-card"></i> <span>Expenses</span></a></li>
          <li><a href="expense_categories.php"><i class="fa fa-folder-open"></i> <span>Expense Categories</span></a></li>
          <li><a href="Returns.php"><i class="fa fa-undo"></i> <span>Returns</span></a></li>
        <?php endif; ?>

        <?php if ($role == "user"): ?>
          <li class="header">MENU</li>
          <li><a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a></li>
          <li><a href="customers.php"><i class="fa fa-users"></i> <span>Customers</span></a></li>
          <li><a href="Sales.php"><i class="fa fa-shopping-cart"></i> <span>Sales</span></a></li>
        <?php endif; ?>

      </ul>
    </section>
  </aside>

  <!-- ══ CONTENT WRAPPER ══ -->
  <div class="content-wrapper" style="background:#f4f6fb;">
    <section class="content-header">
      <h1>Dashboard <small>Overview</small></h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <section class="content">

      <!-- ══ WELCOME BANNER ══ -->
      <div class="welcome-banner">
        <div class="welcome-text">
          <h2>Welcome back, <span><?php echo htmlspecialchars($username); ?></span> 👋</h2>
          <p>Here is what is happening in your store today.</p>
          <span class="role-badge"><?php echo htmlspecialchars($role); ?></span>
        </div>
        <div class="welcome-meta">
          <div class="date-big"><?php echo date('l'); ?></div>
          <div><?php echo date('d F Y'); ?></div>
          <div><?php echo date('h:i A'); ?></div>
        </div>
      </div>

      <!-- ══ STAT CARDS ROW 1 ══ -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-blue">
            <div class="stat-card-icon"><i class="fa fa-cube"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Products</div>
              <div class="value-text"><?php echo $total_products; ?></div>
              <div class="sub-text">Total in inventory</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-green">
            <div class="stat-card-icon"><i class="fa fa-shopping-cart"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Sales</div>
              <div class="value-text"><?php echo $total_sales; ?></div>
              <div class="sub-text">Total transactions</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-orange">
            <div class="stat-card-icon"><i class="fa fa-money"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Revenue</div>
              <div class="value-text" style="font-size:20px;">Rs. <?php echo $total_revenue; ?></div>
              <div class="sub-text">Total sales amount</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-red">
            <div class="stat-card-icon"><i class="fa fa-credit-card"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Expenses</div>
              <div class="value-text" style="font-size:20px;">Rs. <?php echo $total_expenses; ?></div>
              <div class="sub-text">Total expenses</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══ STAT CARDS ROW 2 ══ -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-purple">
            <div class="stat-card-icon"><i class="fa fa-building"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Companies</div>
              <div class="value-text"><?php echo $total_companies; ?></div>
              <div class="sub-text">Registered vendors</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-teal">
            <div class="stat-card-icon"><i class="fa fa-users"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Customers</div>
              <div class="value-text"><?php echo $total_customers; ?></div>
              <div class="sub-text">Registered customers</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-navy">
            <div class="stat-card-icon"><i class="fa fa-tags"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Brands</div>
              <div class="value-text"><?php echo $total_brands; ?></div>
              <div class="sub-text">Active brands</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="stat-card card-pink">
            <div class="stat-card-icon"><i class="fa fa-user-secret"></i></div>
            <div class="stat-card-text">
              <div class="label-text">Users</div>
              <div class="value-text"><?php echo $total_users; ?></div>
              <div class="sub-text">System accounts</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══ CHARTS ROW ══ -->
      <div class="row">

        <!-- Monthly Sales Line Chart -->
        <div class="col-md-8">
          <div class="dash-box">
            <div class="dash-box-header">
              <div class="dash-box-title">
                <i class="fa fa-line-chart"></i> Monthly Sales — <?php echo date('F Y'); ?>
              </div>
            </div>
            <div class="dash-box-body" style="height:300px;">
              <canvas id="monthlyChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Company Doughnut Chart -->
        <div class="col-md-4">
          <div class="dash-box">
            <div class="dash-box-header">
              <div class="dash-box-title">
                <i class="fa fa-pie-chart"></i> Sales by Company
              </div>
            </div>
            <div class="dash-box-body" style="height:300px; display:flex; align-items:center; justify-content:center;">
              <canvas id="companyChart"></canvas>
            </div>
          </div>
        </div>

      </div>

      <!-- ══ RECENT SALES + TOP PRODUCTS ══ -->
      <div class="row">

        <!-- Recent Sales Table -->
        <div class="col-md-8">
          <div class="dash-box">
            <div class="dash-box-header">
              <div class="dash-box-title">
                <i class="fa fa-clock-o"></i> Recent Sales
              </div>
              <a href="Sales.php" style="font-size:12px; color:#0f3460; font-weight:600;">
                View All <i class="fa fa-arrow-right"></i>
              </a>
            </div>
            <div class="dash-box-body" style="padding:0;">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (mysqli_num_rows($recent_sales) > 0):
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($recent_sales)): ?>
                    <tr>
                      <td><?php echo $row['sale_id']; ?></td>
                      <td><?php echo htmlspecialchars($row['company'] ?? 'N/A'); ?></td>
                      <td><?php echo date('d M Y', strtotime($row['sale_date'])); ?></td>
                      <td><strong>Rs. <?php echo number_format($row['total_amount'], 2); ?></strong></td>
                      <td><span class="badge-status badge-completed">Completed</span></td>
                    </tr>
                    <?php $i++; endwhile;
                  else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px; color:#999;">No sales found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Top Products -->
        <div class="col-md-4">
          <div class="dash-box">
            <div class="dash-box-header">
              <div class="dash-box-title">
                <i class="fa fa-trophy"></i> Top Selling Products
              </div>
            </div>
            <div class="dash-box-body">
              <?php
              $max_sold = 1;
              $products_arr = [];
              if ($top_products && mysqli_num_rows($top_products) > 0) {
                  while ($row = mysqli_fetch_assoc($top_products)) {
                      $products_arr[] = $row;
                      if ($row['total_sold'] > $max_sold) $max_sold = $row['total_sold'];
                  }
              }
              if (count($products_arr) > 0):
                foreach ($products_arr as $p):
                  $pct = round(($p['total_sold'] / $max_sold) * 100);
              ?>
              <div class="product-bar-item">
                <div class="product-bar-label">
                  <span><?php echo htmlspecialchars($p['p_name']); ?></span>
                  <span style="color:#0f3460;"><?php echo $p['total_sold']; ?> sold</span>
                </div>
                <div class="product-bar-track">
                  <div class="product-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
                </div>
              </div>
              <?php endforeach;
              else: ?>
                <p style="text-align:center; color:#999; padding:20px 0;">No product sales data yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
      <!-- /.row -->

    </section>
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs"><b>Version</b> 1.0</div>
    <strong>Copyright &copy; 2025 Vendor Store System.</strong> All rights reserved.
  </footer>

</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="dist/js/app.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // ── Sidebar Toggle ──
  $(document).ready(function () {
    $('.sidebar-toggle').on('click', function (e) {
      e.preventDefault();
      $('body').toggleClass('sidebar-collapse');
    });
  });

  // ── Monthly Line Chart ──
  var ctxLine = document.getElementById('monthlyChart').getContext('2d');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($line_labels); ?>,
      datasets: [{
        label: 'Sales Amount (Rs.)',
        data: <?php echo json_encode($line_data); ?>,
        backgroundColor: 'rgba(15,52,96,0.08)',
        borderColor: '#0f3460',
        borderWidth: 2.5,
        pointBackgroundColor: '#0f3460',
        pointRadius: 4,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a1a2e',
          titleColor: '#fff',
          bodyColor: '#ccc',
          padding: 10,
          callbacks: {
            label: function(ctx) { return ' Rs. ' + ctx.parsed.y.toLocaleString(); }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { size: 11 }, color: '#888' }
        },
        y: {
          beginAtZero: true,
          grid: { color: '#f0f0f0' },
          ticks: {
            font: { size: 11 }, color: '#888',
            callback: function(v) { return 'Rs. ' + v.toLocaleString(); }
          }
        }
      }
    }
  });

  // ── Company Doughnut Chart ──
  var ctxDnt = document.getElementById('companyChart').getContext('2d');
  new Chart(ctxDnt, {
    type: 'doughnut',
    data: {
      labels: <?php echo json_encode($company_labels); ?>,
      datasets: [{
        data: <?php echo json_encode($company_data); ?>,
        backgroundColor: ['#0f3460','#1a6abd','#28a745','#e67e22','#6f42c1','#e74c3c','#1abc9c','#f39c12'],
        borderColor: '#fff',
        borderWidth: 3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { size: 11 }, padding: 12, boxWidth: 12 }
        },
        tooltip: {
          backgroundColor: '#1a1a2e',
          callbacks: {
            label: function(ctx) {
              return ' ' + ctx.label + ': Rs. ' + ctx.parsed.toLocaleString();
            }
          }
        }
      },
      cutout: '65%'
    }
  });
</script>

</body>
</html>