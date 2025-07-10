
<!DOCTYPE html>
<html lang="en">
<style>
.btn-primary:not(.btn-light) {
    background: #686868 !important;
    border: none;
    color: white;
}
</style>
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
	<!-- End plugin css for this page -->
	<!-- inject:css -->
	<link rel="stylesheet" href="<?php echo ASSET_PATH; ?>assets/css/style.css">
	<!-- endinject -->
	<link rel="shortcut icon" href="<?php echo ASSET_PATH; ?>assets/images/adminlogo.jpg" />
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
				<div class="brand-logo">
					<img src="<?php echo ASSET_PATH; ?>assets/images/adminlogo.jpg" alt="logo" style="width: 100px; height: 100;">
				</div>
				<h4>Hello! let's get started</h4>
				<h6 class="font-weight-light">Sign in to continue.</h6>
				<form class="pt-3" id="login-form">
				 <div class="alert alert-danger" role="alert" id="loginalert" style="display: none;">	
				</div>
					<div class="form-group">
						<input type="text" name="email" class="form-control form-control-lg"  placeholder="example@gmail.com">
					</div>
					<div class="form-group position-relative">
            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Password">
            <i class="mdi mdi-eye-off position-absolute" id="togglePassword" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></i>
          </div>
					<div class="mt-3 d-grid gap-2">
						<button type="button" class="enter-btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
					</div>
					<!--<div class="my-2 d-flex justify-content-between align-items-center">
						<a href="#" class="auth-link text-black">Forgot password?</a>
					</div>-->
                </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="<?php echo ASSET_PATH; ?>assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="<?php echo ASSET_PATH; ?>assets/js/off-canvas.js"></script>
  <script src="<?php echo ASSET_PATH; ?>assets/js/hoverable-collapse.js"></script>
  <script src="<?php echo ASSET_PATH; ?>assets/js/template.js"></script>
  <script src="<?php echo ASSET_PATH; ?>assets/js/settings.js"></script>
  <script src="<?php echo ASSET_PATH; ?>assets/js/todolist.js"></script>
  <!-- endinject -->
</body>
<script>
  $(document).ready(function () {

    $('#togglePassword').click(function () {
      const passwordField = $('#password');
      const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
      passwordField.attr('type', type);
      $(this).toggleClass('mdi-eye-off mdi-eye');
    });

    $('.enter-btn').click(function () {
      debugger;
      const body = $('#login-form').serialize();
      var url = '<?= base_url("login/authenticate") ?>';

      $.post(url, body, function (response) {
        if (response.status === 1) {
          
          $('#loginalert').removeClass('alert-danger').addClass('alert-success');
          $('#loginalert').html('Login Successful');
          $('#loginalert').fadeIn();

          setTimeout(function () {
            $('#loginalert').fadeOut();
            window.location.href = "<?= base_url('dashboard') ?>";
            // alert(response.user_Id);
          }, 2000); 
        } else {
          $('#loginalert').removeClass('alert-success').addClass('alert-danger');
          $('#loginalert').html('Invalid Credentials');
          $('#loginalert').fadeIn();
          setTimeout(function () {
            $('#loginalert').fadeOut();
          }, 2000);
        }
      }, 'json');
    });
  });
</script>

</html>