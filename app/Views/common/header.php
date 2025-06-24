<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Majestic Admin Pro</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/css/custom.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?php echo ASSET_PATH; ?>assets/images/adminlogo.jpeg" />
  <!-- Add in your header or before closing body tag -->
   <!-- Add this to your common/header.php if not already present -->
	<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">-->
  <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> -->
 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" > -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" ></script> -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


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
      <!-- <div id="globalAlertContainer" style="position: fixed; top: 70px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 400px;"></div> -->

  <div class="navbar-brand-wrapper d-flex justify-content-center">
    <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
      <a class="navbar-brand brand-logo" href="#">
    <img src="<?= ASSET_PATH; ?>assets/images/adminlogo.jpeg" alt="Logo" style="height: 40px;">
</a>

      <a class="navbar-brand brand-logo-white" href="index.html"><img src="<?= ASSET_PATH; ?>assets/images/adminlogo.jpeg"
          alt="logo" /></a>
      <a class="navbar-brand brand-logo-mini" href="index.html"><img src="<?= ASSET_PATH; ?>assets/images/adminlogo.jpeg"
          alt="logo" /></a>
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
       
      </button>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

  </div>
</nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper px-0">      
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <?php $uri = service('uri'); ?>
    <li class="nav-item">
      <a class="nav-link <?= $uri->getSegment(1) == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
        <i class="mdi mdi-home menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>    
    <li class="nav-item">
      <a class="nav-link <?= $uri->getSegment(1) == 'adduserlist' ? 'active' : '' ?>" href="<?= base_url('adduserlist') ?>">
        <i class="mdi mdi-circle-outline menu-icon"></i>
        <span class="menu-title">Manage User</span>
        
      </a>
    </li>    
    <li class="nav-item">
      <a class="nav-link <?= $uri->getSegment(1) == 'companylist' ? 'active' : '' ?>" href="<?= base_url('companylist') ?>">
        <i class="mdi mdi-view-headline menu-icon"></i>
        <span class="menu-title">Company Management</span>
        
      </a>
      <div class="collapse" id="form-elements">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="pages/forms/basic_elements.html">Basic Elements</a></li>          
        </ul>
      </div>
    </li>    
    <li class="nav-item">
	  <a class="nav-link <?= $uri->getSegment(1) == 'rolemanagement' && $uri->getSegment(2) == 'rolelist' ? 'active' : '' ?>" href="<?= base_url('rolemanagement/rolelist') ?>">
		<i class="mdi mdi-chart-pie menu-icon"></i>
		<span class="menu-title">Role Management</span>
	  </a>
	</li>

    <li class="nav-item">
      <a class="nav-link <?= $uri->getSegment(1) == 'estimatelist' ? 'active' : '' ?>" href="<?= base_url('estimatelist') ?>">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Estimate Generation</span>
        
      </a>
      <div class="collapse" id="tables">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/tables/basic-table.html">Basic table</a></li>
          </li>
        </ul>
      </div>
    </li>    
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
        <i class="mdi mdi-emoticon menu-icon"></i>
        <span class="menu-title">Invoice Generation</span>
        <!-- <i class="menu-arrow"></i> -->
      </a>
      <div class="collapse" id="icons">
        <ul class="nav flex-column sub-menu">          
          <li class="nav-item"> <a class="nav-link" href="pages/icons/font-awesome.html">Font Awesome</a></li>                              
        </ul>
      </div>
    </li>  
    <?php
    $isExpenseActive = $uri->getSegment(1) == 'expense' && $uri->getSegment(2) == '';
    ?>
    <li class="nav-item">
      <a class="nav-link <?= $isExpenseActive ? 'active' : '' ?>" href="<?= base_url('expense') ?>">
        <i class="mdi mdi-square-outline menu-icon"></i>
        <span class="menu-title">Expenses</span>
      </a>
    </li>  
    <?php
    $segment1 = $uri->getSegment(1);
    $segment2 = $uri->getSegment(2);
    $isReportActive = in_array("$segment1/$segment2", [
        'expense/report',
    'ledger/company',
    'report/sales',
    'report/total-expense'
]) || $segment1 === 'companyledger';
    ?>

    <li class="nav-item">
        <a class="nav-link <?= $isReportActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#auth" aria-expanded="<?= $isReportActive ? 'true' : 'false' ?>" aria-controls="auth">
            <i class="mdi mdi-clipboard menu-icon"></i>
            <span class="menu-title">Reports</span>
        </a>
        <div class="collapse <?= $isReportActive ? 'show' : '' ?>" id="auth">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link <?= "$segment1/$segment2" == 'expense/report' ? 'active' : '' ?>" href="<?= base_url('expense/report') ?>">Expense Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($segment1 === 'companyledger') ? 'active' : '' ?>" href="<?= base_url('companyledger') ?>">
                        Company Ledger
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= "$segment1/$segment2" == 'report/sales' ? 'active' : '' ?>" href="<?= base_url('report/sales') ?>">Total Sales Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= "$segment1/$segment2" == 'report/total-expense' ? 'active' : '' ?>" href="<?= base_url('report/total-expense') ?>">Total Expense Report</a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
      <a href="#" id="logoutLink" class="nav-link">
  <i class="mdi mdi-logout menu-icon"></i>
  <span class="menu-title">Logout</span>
</a>
    </li>

  </ul>
</nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModel" tabindex="-1" role="dialog" aria-labelledby="logoutModelLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModelLabel">Confirmation</h5>
        <button type="button" class="close" id="closeModalBtn" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        Are you sure you want to logout?
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelLogoutBtn">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmlogout">Logout</button>
      </div>
      
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
