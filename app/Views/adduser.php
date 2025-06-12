<?php include "common/header.php";?>
<div class="form-control mb-3"> 
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0"><?= isset($isEdit) && $isEdit ? 'Edit User' : 'Create New User' ?></h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('adduserlist') ?>" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
	<div class="col-md-12"><hr/></div>
		<div class="col-md-12 no-gutters">
			<form id="user-login-form">
                <div class="alert" role="alert"></div>
				<div class="form-group">
                    <div class="row">
                        <div class="col-md-6 no-gutters">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" maxlength="20" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" value="<?= isset($userData['name']) ? $userData['name'] : '' ?>"/>
                        </div>
                            <div class="col-md-6 no-gutters">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" class="form-control" value="<?= isset($userData['email']) ? $userData['email'] : '' ?>" autocomplete="off"/>
                            </div>
                    </div>
				</div>
				<div class="form-group">
                    <div class="row">
                        <div class="col-md-6 no-gutters">
                                <label>Password 
                                    <?php if(!isset($isEdit) || !$isEdit): ?>
                                        <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" id="Password" class="form-control" minlength="6" maxlength="15" autocomplete="new-password"/>
                                    <span id="togglePassword" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; z-index: 10;">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        <div class="col-md-6 no-gutters">
                            <label>Phone Number</label>
                            <input type="text" name="phonenumber" id="phonenumber" class="form-control" value="<?= isset($userData['phonenumber']) ? $userData['phonenumber'] : '' ?>" maxlength="15" />
                        </div>
                    </div>
				</div>
				<div class="form-group">
					<div class="col-md-12  text-end">
						<button type="button"  class="enter-btn btn btn-primary" id="saveUserBtn" disabled>Save User</button>
						<input type="hidden" name="uid" id="uid" value="<?= isset($uid) ? $uid : '' ?>">
					
					</div>
				</div>	
			</form>
		</div>
	</div>
</div>
</div>
<?php include "common/footer.php"; ?>
<script>
$(document).ready(function () {
let initialFormData = $('#user-login-form').serialize();
$('#user-login-form input').on('input change', function () {
    const currentFormData = $('#user-login-form').serialize();

    if (currentFormData !== initialFormData) {
        $('#saveUserBtn').prop('disabled', false);
    } else {
        $('#saveUserBtn').prop('disabled', true); 
    }
});

    const uid = "<?= isset($uid) ? $uid : '' ?>";

   $('#togglePassword').on('click', function () {
    const passwordInput = $('#Password');
    const icon = $(this).find('i'); 
    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
    passwordInput.attr('type', type);
    icon.toggleClass('fa-eye fa-eye-slash');
});

$('#name').on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

    $('#phonenumber').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);
    });

  $('#saveUserBtn').on('click', function (e) {
    e.preventDefault();

    const uid = $('#uid').val().trim();
    const name = $('#name').val().trim();
    const email = $('#email').val().trim();
    const phone = $('#phonenumber').val().trim();
    const password = $('#Password').val().trim();

    const isNewUser = uid === '';

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

if (name === '' || email === '' || (isNewUser && password === '')) {
    $('.alert')
        .removeClass('alert-success alert-warning')
        .addClass('alert-danger')
        .html('Please fill all mandatory fields <span class="text-danger">*</span>.')
        .show();
    setTimeout(() => { $('.alert').fadeOut(); }, 3000);
    return;
}
if (!emailRegex.test(email)) {
    $('.alert')
        .removeClass('alert-success alert-warning')
        .addClass('alert-danger')
        .html('Please enter a valid email address.')
        .show();
    setTimeout(() => { $('.alert').fadeOut(); }, 3000);
    return;
}
if (isNewUser && (password.length < 6 || password.length > 15)) {
    $('.alert')
        .removeClass('alert-success alert-warning')
        .addClass('alert-danger')
        .html('Password must be between 6 and 15 characters.')
        .show();
    setTimeout(() => { $('.alert').fadeOut(); }, 3000);
    return;
}
    const formData = new FormData($('#user-login-form')[0]);

    $.ajax({
        url: '<?= base_url('manageuser/save') ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                $('.alert')
                    .removeClass('alert-danger alert-warning')
                    .addClass('alert-success')
                    .text(response.message)
                    .show();

                if (isNewUser) {
                    $('#user-login-form')[0].reset();
                } else {
                    $('#user-login-form input').prop('disabled', true); 
                }

                setTimeout(() => {
                    window.location.href = '<?= base_url('adduserlist') ?>';
                }, 3000);
            } else if (response.status === 'nochange') {
                $('.alert')
                    .removeClass('alert-success alert-danger')
                    .addClass('alert-warning')
                    .text('No changes were made.')
                    .show();
                setTimeout(() => { $('.alert').fadeOut(); }, 3000);
            } else {
                $('.alert')
                    .removeClass('alert-success alert-warning')
                    .addClass('alert-danger')
                    .html(response.message)
                    .show();
                setTimeout(() => { $('.alert').fadeOut(); }, 3000);
            }
        },
        error: function () {
            $('.alert')
                .removeClass('alert-success alert-warning')
                .addClass('alert-danger')
                .text('Something went wrong. Please try again.')
                .show();
            setTimeout(() => { $('.alert').fadeOut(); }, 3000);
        }
    });
});

});
</script>