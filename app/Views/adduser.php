<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container"> 
  <div class="alert d-none text-center position-fixed" role="alert"></div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div class="col-md-6">
        <h3 class="mb-0"><?= isset($isEdit) && $isEdit ? 'Edit User' : 'Create New User' ?></h3>
      </div>
    </div>
    <div class="col-md-12"><hr/></div>

    <div class="card-body">
      <form id="user-login-form">
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <label>Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control" maxlength="20"
                pattern="[A-Za-z\s]+" title="Only letters and spaces allowed"
                value="<?= $userData['name'] ?? '' ?>"/>
            </div>
            <div class="col-md-6">
              <label>Email <span class="text-danger">*</span></label>
              <input type="email" name="email" id="email" class="form-control"
                value="<?= $userData['email'] ?? '' ?>" autocomplete="off"/>
            </div>
          </div>
        </div>

        <div class="form-group mt-3">
          <div class="row">
            <div class="col-md-6">
              <label>Phone Number</label>
              <input type="text" name="phonenumber" id="phonenumber" class="form-control"
                value="<?= $userData['phonenumber'] ?? '' ?>" maxlength="15" />
            </div>
            <div class="col-md-6 no-gutters mt-3">
  <label>Role <span class="text-danger">*</span></label>
  <select name="role_id" id="role_id" class="form-control" required>
    <option value="">Select Role</option>
    <?php foreach ($roles as $role): ?>
      <option value="<?= $role['role_id'] ?>"
        <?= isset($userData['role_id']) && $userData['role_id'] == $role['role_id'] ? 'selected' : '' ?>>
        <?= esc($role['role_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>


            <?php if (!isset($isEdit) || !$isEdit): ?>
            <div class="col-md-6">
              <label>Password <span class="text-danger">*</span></label>
              <div class="input-group position-relative mb-2">
                <input type="password" name="password" id="password" class="form-control" minlength="6" maxlength="15" required />
                <span class="toggle-password" toggle="#password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
                  <i class="fa fa-eye-slash"></i>
                </span>
              </div>
            </div>
            <?php else: ?>
            <div class="col-md-12">
              <p class="text-muted"><b>Enter a new password and confirm it only if you wish to change your current password.</b></p>
            </div>
            <div class="col-md-6">
              <label>New Password</label>
              <div class="input-group position-relative mb-2">
                <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" maxlength="15" />
                <span class="toggle-password" toggle="#new_password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
                  <i class="fa fa-eye-slash"></i>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <label>Confirm Password</label>
              <div class="input-group position-relative mb-2">
                <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" minlength="6" maxlength="15" />
                <span class="toggle-password" toggle="#confirm_new_password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
                  <i class="fa fa-eye-slash"></i>
                </span>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group mt-3 text-end">
          <input type="hidden" name="uid" id="uid" value="<?= $uid ?? '' ?>">
          <a href="<?= base_url('adduserlist') ?>" class="btn btn-secondary">Discard</a>
          <button type="button" class="enter-btn btn btn-primary" id="saveUserBtn" disabled>Save User</button>
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
    $('#saveUserBtn').prop('disabled', $('#user-login-form').serialize() === initialFormData);
  });

  // Show/hide password toggler
  $(document).on('click', '.toggle-password', function () {
    const input = $($(this).attr('toggle'));
    const type = input.attr('type') === 'password' ? 'text' : 'password';
    input.attr('type', type);
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });

  // Restrict name and phone fields
  $('#name').on('input', function () {
    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
  });
  $('#phonenumber').on('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 15);
  });

  $('#saveUserBtn').on('click', function (e) {
    e.preventDefault();
    const btn = $(this).prop('disabled', true);
    const uid = $('#uid').val().trim();
    const isNew = uid === '';
    const name = $('#name').val().trim();
    const email = $('#email').val().trim();
    const phone = $('#phonenumber').val().trim();
    const pw = $('#password').val()?.trim() || '';
    const newPw = $('#new_password').val()?.trim() || '';
    const confPw = $('#confirm_new_password').val()?.trim() || '';
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!name || !email || (isNew && !pw)) {
      showAlert('Please fill all mandatory fields <span class="text-danger">*</span>.', 'danger');
      return btn.prop('disabled', false);
    }
    if (!emailRe.test(email)) {
      showAlert('Please enter a valid email address.', 'danger');
      return btn.prop('disabled', false);
    }
    if (isNew && (pw.length < 6 || pw.length > 15)) {
      showAlert('Password must be between 6 and 15 characters.', 'danger');
      return btn.prop('disabled', false);
    }
    if (!isNew && (newPw || confPw)) {
      if (newPw.length < 6 || newPw.length > 15) {
        showAlert('New Password must be between 6 and 15 characters.', 'danger');
        return btn.prop('disabled', false);
      }
      if (newPw !== confPw) {
        showAlert('New Password and Confirm Password must match.', 'danger');
        return btn.prop('disabled', false);
      }
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
          showAlert(response.message, 'success');
          if (isNew) {
            $('#user-login-form')[0].reset();
            initialFormData = $('#user-login-form').serialize();
          }
          setTimeout(() => window.location.href = '<?= base_url('adduserlist') ?>', 2000);
        } else {
          showAlert(response.message || 'Failed to save user.', 'danger');
          btn.prop('disabled', false);
        }
      },
      error: function () {
        showAlert('Something went wrong. Please try again.', 'danger');
        btn.prop('disabled', false);
      }
    });
  });

  function showAlert(msg, type = 'danger') {
    const a = $('.alert')
      .removeClass('d-none alert-success alert-danger alert-warning')
      .addClass(`alert-${type}`)
      .html(msg)
      .fadeIn();
    setTimeout(() => a.fadeOut(() => a.addClass('d-none')), 3000);
  }
});
</script>
