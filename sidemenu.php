<?php
$role     = isset($_SESSION['role'])     ? strtolower(trim($_SESSION['role']))     : '';
$username = isset($_SESSION['username']) ? $_SESSION['username']                   : 'User';

// Detect current page for active highlighting
$current_page = basename($_SERVER['PHP_SELF']);
function isActive($page) {
    global $current_page;
    return $current_page == $page ? 'active' : '';
}
?>

<aside class="main-sidebar">
  <section class="sidebar">

    <!-- ── User Panel ── -->
    <div class="user-panel">
      <div class="pull-left image">
        <?php if (!empty($_SESSION['user_image'])): ?>
          <img src="<?php echo htmlspecialchars($_SESSION['user_image']); ?>"
               class="img-circle" alt="User Image"
               style="width:45px; height:45px; object-fit:cover;">
        <?php else: ?>
          <div class="img-circle text-center bg-primary"
               style="width:45px; height:45px; line-height:45px;
                      color:#fff; font-size:20px; font-weight:bold;">
            <?php echo strtoupper(substr($username, 0, 1)); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="pull-left info">
        <p><?php echo htmlspecialchars($username); ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- ── Search ── -->
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
          <button type="submit" class="btn btn-flat">
            <i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </form>

    <!-- ── Navigation Menu ── -->
    <ul class="sidebar-menu" data-widget="tree">

      <!-- Dashboard — visible to ALL roles -->
      <li class="header">NAVIGATION</li>
      <li class="<?php echo isActive('index.php'); ?>">
        <a href="index.php">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>

      <!-- ══ ADMIN ══ -->
      <?php if ($role == "admin"): ?>

        <li class="header">INVENTORY</li>
        <li class="<?php echo isActive('brands.php'); ?>">
          <a href="brands.php"><i class="fa fa-tags"></i> <span>Brands</span></a>
        </li>
        <li class="<?php echo isActive('categories.php'); ?>">
          <a href="categories.php"><i class="fa fa-list"></i> <span>Categories</span></a>
        </li>
        <li class="<?php echo isActive('companies.php'); ?>">
          <a href="companies.php"><i class="fa fa-building"></i> <span>Companies</span></a>
        </li>
        <li class="<?php echo isActive('Products.php'); ?>">
          <a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a>
        </li>
        <li class="<?php echo isActive('Returns.php'); ?>">
          <a href="Returns.php"><i class="fa fa-undo"></i> <span>Returns</span></a>
        </li>

        <li class="header">SALES</li>
        <li class="<?php echo isActive('customers.php'); ?>">
          <a href="customers.php"><i class="fa fa-users"></i> <span>Customers</span></a>
        </li>
        <li class="<?php echo isActive('Sales.php'); ?>">
          <a href="Sales.php"><i class="fa fa-shopping-cart"></i> <span>Sales</span></a>
        </li>
        <li class="<?php echo isActive('sales_item.php'); ?>">
          <a href="sales_item.php"><i class="fa fa-file-text"></i> <span>Sales Items</span></a>
        </li>

        <li class="header">FINANCE</li>
        <li class="<?php echo isActive('expenses.php'); ?>">
          <a href="expenses.php"><i class="fa fa-credit-card"></i> <span>Expenses</span></a>
        </li>
        <li class="<?php echo isActive('expense_categories.php'); ?>">
          <a href="expense_categories.php"><i class="fa fa-folder-open"></i> <span>Expense Categories</span></a>
        </li>

        <li class="header">SYSTEM</li>
        <li class="<?php echo isActive('user.php'); ?>">
          <a href="user.php"><i class="fa fa-user-secret"></i> <span>Users</span></a>
        </li>

      <?php endif; ?>

      <!-- ══ MANAGER / INVENTORY / INVENTORY STAFF ══ -->
      <?php if ($role == "manager" || $role == "inventory" || $role == "inventory_staff"): ?>

        <li class="header">INVENTORY</li>
        <li class="<?php echo isActive('brands.php'); ?>">
          <a href="brands.php"><i class="fa fa-tags"></i> <span>Brands</span></a>
        </li>
        <li class="<?php echo isActive('categories.php'); ?>">
          <a href="categories.php"><i class="fa fa-list"></i> <span>Categories</span></a>
        </li>
        <li class="<?php echo isActive('companies.php'); ?>">
          <a href="companies.php"><i class="fa fa-building"></i> <span>Companies</span></a>
        </li>
        <li class="<?php echo isActive('Products.php'); ?>">
          <a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a>
        </li>
        <li class="<?php echo isActive('Returns.php'); ?>">
          <a href="Returns.php"><i class="fa fa-undo"></i> <span>Returns</span></a>
        </li>

      <?php endif; ?>

      <!-- ══ SALES MAN ══ -->
      <?php if ($role == "sales man" || $role == "salesman"): ?>

        <li class="header">SALES</li>
        <li class="<?php echo isActive('customers.php'); ?>">
          <a href="customers.php"><i class="fa fa-users"></i> <span>Customers</span></a>
        </li>
        <li class="<?php echo isActive('Sales.php'); ?>">
          <a href="Sales.php"><i class="fa fa-shopping-cart"></i> <span>Sales</span></a>
        </li>
        <li class="<?php echo isActive('sales_item.php'); ?>">
          <a href="sales_item.php"><i class="fa fa-file-text"></i> <span>Sales Items</span></a>
        </li>
        <li class="<?php echo isActive('Products.php'); ?>">
          <a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a>
        </li>

      <?php endif; ?>

      <!-- ══ ACCOUNTANT ══ -->
      <?php if ($role == "accountant"): ?>

        <li class="header">FINANCE</li>
        <li class="<?php echo isActive('expenses.php'); ?>">
          <a href="expenses.php"><i class="fa fa-credit-card"></i> <span>Expenses</span></a>
        </li>
        <li class="<?php echo isActive('expense_categories.php'); ?>">
          <a href="expense_categories.php"><i class="fa fa-folder-open"></i> <span>Expense Categories</span></a>
        </li>
        <li class="<?php echo isActive('Returns.php'); ?>">
          <a href="Returns.php"><i class="fa fa-undo"></i> <span>Returns</span></a>
        </li>

      <?php endif; ?>

      <!-- ══ USER ══ -->
      <?php if ($role == "user"): ?>

        <li class="header">MENU</li>
        <li class="<?php echo isActive('Products.php'); ?>">
          <a href="Products.php"><i class="fa fa-cube"></i> <span>Products</span></a>
        </li>
        <li class="<?php echo isActive('customers.php'); ?>">
          <a href="customers.php"><i class="fa fa-users"></i> <span>Customers</span></a>
        </li>
        <li class="<?php echo isActive('Sales.php'); ?>">
          <a href="Sales.php"><i class="fa fa-shopping-cart"></i> <span>Sales</span></a>
        </li>

      <?php endif; ?>

    </ul>
  </section>
</aside>

<style>
/* ── Force ALL sidebar elements to same dark color ── */
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
  background-image: none !important;  /* removes any gradient */
}

/* ── Section Headers ── */
.sidebar-menu > li.header {
  color: rgba(255,255,255,0.4) !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  letter-spacing: 1.2px !important;
  padding: 14px 16px 6px !important;
  text-transform: uppercase !important;
  background: #1a1a2e !important;
  background-color: #1a1a2e !important;
}

/* ── Menu Items ── */
.sidebar-menu > li > a {
      border-radius: 0;
      transition: all 0.2s;
    }
    .sidebar-menu > li.active > a,
    .sidebar-menu > li > a:hover {
      background: rgba(255,255,255,0.1) !important;
      border-left: 3px solid #3c8dbc;
 }


/* ── Hover ── */
.sidebar-menu > li > a:hover {
  background: rgba(255,255,255,0.07) !important;
  background-color: rgba(255,255,255,0.07) !important;
  color: #fff !important;
  border-left-color: #3c8dbc !important;
}
.sidebar-menu > li > a:hover .fa {
  color: #3c8dbc !important;
}

/* ── Active ── */
.sidebar-menu > li.active > a,
.sidebar-menu > li.active > a:hover {
  background: rgba(60,141,188,0.18) !important;
  background-color: rgba(60,141,188,0.18) !important;
  color: #fff !important;
  border-left-color: #3c8dbc !important;
  font-weight: 700 !important;
}
.sidebar-menu > li.active > a .fa {
  color: #3c8dbc !important;
}

/* ── User Panel ── */
.user-panel {
  padding: 14px 16px !important;
  border-bottom: 1px solid rgba(255,255,255,0.08) !important;
  background: #1a1a2e !important;
  background-color: #1a1a2e !important;
}
.user-panel > .info > p {
  color: #fff !important;
  font-weight: 600 !important;
  font-size: 13px !important;
  margin: 0 0 3px !important;
}
.user-panel > .info > a {
  color: rgba(255,255,255,0.55) !important;
  font-size: 12px !important;
}
.user-panel > .info > a .fa {
  color: #00e676 !important;
}

/* ── Search Form ── */
.sidebar-form {
  border-top: 1px solid rgba(255,255,255,0.08) !important;
  border-bottom: 1px solid rgba(255,255,255,0.08) !important;
  padding: 8px 10px !important;
  background: #1a1a2e !important;
  background-color: #1a1a2e !important;
}
.sidebar-form input {
  background: rgba(255,255,255,0.08) !important;
  border: 1px solid rgba(255,255,255,0.12) !important;
  color: #fff !important;
  border-radius: 6px 0 0 6px !important;
  font-size: 12px !important;
}
.sidebar-form input::placeholder { color: rgba(255,255,255,0.4) !important; }
.sidebar-form input:focus {
  background: rgba(255,255,255,0.12) !important;
  outline: none !important;
  box-shadow: none !important;
}
.sidebar-form .btn {
  background: rgba(255,255,255,0.1) !important;
  border: 1px solid rgba(255,255,255,0.12) !important;
  color: rgba(255,255,255,0.7) !important;
  border-radius: 0 6px 6px 0 !important;
}
.sidebar-form .btn:hover {
  background: rgba(255,255,255,0.2) !important;
  color: #fff !important;
}

/* ── Logo Area ── */
.main-header .logo,
.skin-blue .main-header .logo,
.skin-blue-dark .main-header .logo {
  background: #1a1a2e !important;
  background-color: #1a1a2e !important;
  background-image: none !important;
  border-bottom: 1px solid rgba(255,255,255,0.08) !important;
}
.main-header .logo:hover {
  background: #16213e !important;
  background-color: #16213e !important;
}
</style>