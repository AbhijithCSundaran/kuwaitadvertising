<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Alrai Printing Press</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
  <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css"> -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/css/custom.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?php echo ASSET_PATH; ?>assets/images/adminlogo.jpg" />
  <!-- Add in your header or before closing body tag -->
  <!-- Add this to your common/header.php if not already present -->
  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">-->
  <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> -->

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Already included in your file -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" > -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" ></script> -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Bootstrap 5 -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- MDI Icons -->
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css" rel="stylesheet">


  <!-- DataTables with Bootstrap 5 -->
</head>

<body>
  <!-- <div class="container-scroller">
    <div class="row p-0 m-0 proBanner" id="proBanner">
      <div class="col-md-12 p-0 m-0">
        <div class="card-body card-body-padding px-3 d-flex align-items-center justify-content-between">
          <div class="ps-lg-1">
            <div class="d-flex align-items-center justify-content-between">
              <p class="mb-0 font-weight-medium me-3 buy-now-text">Free 24/7 customer support, updates, and more with this template!</p>
              <a href="https://www.bootstrapdash.com/product/majestic-admin-pro/?utm_source=navbar&utm_medium=productdemo&utm_campaign=getpro" target="_blank" class="btn me-2 buy-now-btn border-0">Buy Now</a>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <a href="https://www.bootstrapdash.com/product/majestic-admin-pro/"><i class="mdi mdi-home me-3 text-white"></i></a>
            <button id="bannerClose" class="btn border-0 p-0">
              <i class="mdi mdi-close text-white me-0"></i>
            </button>
          </div>
        </div>
      </div>
    </div> -->
  <!-- partial:partials/_navbar.html -->
  <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="navbar-brand-wrapper d-flex justify-content-center">
      <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
        <!-- <button id="menuToggle" class="btn btn-secondary d-lg-none">
              <i class="fas fa-bars"></i>
            </button> -->

        <a class="navbar-brand brand-logo" href="#">
          <img src="<?= ASSET_PATH; ?>assets/images/adminlogo.jpg" alt="Logo" style="height: 50px;">
        </a>
        <a class="navbar-brand brand-logo-white" href="index.html"><img
            src="<?= ASSET_PATH; ?>assets/images/adminlogo.jpg" alt="logo" /></a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img
            src="<?= ASSET_PATH; ?>assets/images/adminlogo.jpg" alt="logo" /></a>
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        </button>
      </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
      <button id="menuToggle" class="btn icon-align d-xl-none">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </nav>
  <!-- partial -->
  <div class="container-fluid page-body-wrapper px-0">
    <?php
    $session = session();
    $allowedMenus = $session->get('allowed_menus') ?? [];
    $uri = service('uri');
    ?>
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        <?php if (in_array('dashboard', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'dashboard' ? 'active' : '' ?>"
              href="<?= base_url('dashboard') ?>">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array('companylist', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'companylist' ? 'active' : '' ?>"
              href="<?= base_url('companylist') ?>">
              <i class="mdi mdi-view-headline menu-icon"></i>
              <span class="menu-title">Company Management</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array('adduserlist', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'adduserlist' ? 'active' : '' ?>"
              href="<?= base_url('adduserlist') ?>">
              <i class="mdi mdi-bi bi-person menu-icon"></i>
              <span class="menu-title">Manage User</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array('rolemanagement', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'rolemanagement' ? 'active' : '' ?>"
              href="<?= base_url('rolemanagement/rolelist') ?>">
              <i class="mdi mdi-chart-pie menu-icon"></i>
              <span class="menu-title">Role Management</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array('customer', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'customer' ? 'active' : '' ?>"
              href="<?= base_url('customer/list') ?>">
              <i class="mdi mdi-account-multiple menu-icon"></i>
              <span class="menu-title">Customer List</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array('estimatelist', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'estimatelist' ? 'active' : '' ?>"
              href="<?= base_url('estimatelist') ?>">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Estimate Generation</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array('expense', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'expense' ? 'active' : '' ?>" href="<?= base_url('expense') ?>">
              <i class="mdi mdi-square-outline menu-icon"></i>
              <span class="menu-title">Expenses</span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (in_array('invoices', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(1) == 'invoicelist' ? 'active' : '' ?>"
              href="<?= base_url('invoicelist') ?>">
              <i class="mdi mdi-receipt menu-icon"></i>
              <span class="menu-title">Invoice List</span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (in_array('reports', $allowedMenus)): ?>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
              <i class="mdi mdi-clipboard menu-icon"></i>
              <span class="menu-title">Reports</span>
            </a>
            <div class="collapse" id="auth">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('expense/report') ?>">Expense Report</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('sales/report') ?>">Sales Report</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('companyledger') ?>">Company Ledger</a></li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a href="#" id="logoutLink" class="nav-link">
            <i class="mdi mdi-logout menu-icon"></i>
            <span class="menu-title">Logout</span>
          </a>
        </li>
      </ul>
    </nav>

    <!-- <div class="offcanvas offcanvas-start  text-black" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body p-3">
        <ul class="nav flex-column">
          <?php if (in_array('dashboard', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                <i class="mdi mdi-home me-2"></i> Dashboard
              </a>
            </li>
          <?php endif; ?>
          <?php if (in_array('companylist', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'companylist' ? 'active' : '' ?>" href="<?= base_url('companylist') ?>">
                <i class="mdi mdi-view-headline menu-icon"></i>
                <span class="menu-title">Company Management</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('adduserlist', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'adduserlist' ? 'active' : '' ?>" href="<?= base_url('adduserlist') ?>">
                <i class="mdi mdi-bi bi-person menu-icon"></i>
                <span class="menu-title">Manage User</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('rolemanagement', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'rolemanagement' ? 'active' : '' ?>" href="<?= base_url('rolemanagement/rolelist') ?>">
                <i class="mdi mdi-chart-pie menu-icon"></i>
                <span class="menu-title">Role Management</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('customer', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'customer' ? 'active' : '' ?>" href="<?= base_url('customer/list') ?>">
                <i class="mdi mdi-account-multiple menu-icon"></i>
                <span class="menu-title">Customer List</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('estimatelist', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'estimatelist' ? 'active' : '' ?>" href="<?= base_url('estimatelist') ?>">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Estimate Generation</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('expense', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black <?= $uri->getSegment(1) == 'expense' ? 'active' : '' ?>" href="<?= base_url('expense') ?>">
                <i class="mdi mdi-square-outline menu-icon"></i>
                <span class="menu-title">Expenses</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('reports', $allowedMenus)): ?>
            <li class="nav-item">
              <a class="nav-link text-black " data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="mdi mdi-clipboard menu-icon"></i>
                <span class="menu-title">Reports</span>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('expense/report') ?>">Expense Report</a></li>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('sales/report') ?>">Sales Report</a></li>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('companyledger') ?>">Company Ledger</a></li>
                </ul>
              </div>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a href="#" id="logoutLink" class="nav-link text-black ">
              <i class="mdi mdi-logout menu-icon"></i>
              <span class="menu-title">Logout</span>
            </a>
          </li>
        </ul>
      </div>
    </div> -->


    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModel" tabindex="-1" role="dialog" aria-labelledby="logoutModelLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="logoutModelLabel">Confirmation</h5>
            <button type="button" class="close" id="closeModalBtn" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            Are You Sure You Want To Logout?
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="confirmlogout">Logout</button>
            <button type="button" class="btn btn-secondary" id="cancelLogoutBtn">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>