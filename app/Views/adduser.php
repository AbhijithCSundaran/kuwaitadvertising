<?php include "common/header.php";?>
<div class="form-control"> 
	<div class="row">
		<div class="col-md-6">
			<h3>Add Users</h3>
		</div>
		<div class="col-md-6 text-right">
			<a href="<?= base_url('adduserlist') ?>"><button class="btn btn-secondary">Back to list</button></a>
		</div>
		<div class="col-md-12"><hr/></div>
	</div>
	<div class="col-md-12 no-gutters">
		<form id="user-login-form">
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Name</label>
					<input type="text" name="name" id="name" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Email</label>
					<input type="text" name="email" id="email" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Password</label>
					<input type="password" name="password" id="Password" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>PhoneNumber</label>
					<input type="text" name="phonenumber" id="phonenumber" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<button type="button"  class="enter-btn btn btn-primary" id="saveUserBtn">Save User</button>
					<input type="hidden" name="uid" id="uid" value="<?= isset($uid) ? $uid : '' ?>">
				<div class="alert" role="alert"></div>
				</div>
			</div>	
		</form>
	</div>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
$(document).ready(function () {
debugger;
    //const uid = $('#uid').val();  // Get from hidden input
	// const uid = new URLSearchParams(window.location.search).get('uid');
const uid = "<?= isset($uid) ? $uid : '' ?>";


    if (uid) {
        $.ajax({
            url: '<?= base_url('manageuser/getUser') ?>/' + uid,
            method: 'GET',
            dataType: 'json',
            success: function (user) {
                if (user) {
                    $('#name').val(user.name);
                    $('#email').val(user.email);
                    $('#phonenumber').val(user.phonenumber);
                    $('#Password').val(''); 
                }
            },
			error: function (xhr, status, error) {
				console.error('AJAX Error:', status, error);
				console.error('Response:', xhr.responseText);
				alert('Failed to load user data. Please try again later.');
			}
			
        });
    }
	// else
		// alert('no uid');

    $('#saveUserBtn').on('click', function (e) {
        e.preventDefault();
        var form = $('#user-login-form')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '<?= base_url('manageuser/save') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $('.alert')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text('User saved successfully.')
                    .show();
                $('#user-login-form')[0].reset(); 
                setTimeout(() => {
                    // window.location.href = '<?= base_url('adduserlist') ?>';
                }, 1000);
            },
            error: function () {
                $('.alert')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text('Something went wrong. Please try again.')
                    .show();
            }
        });
    });
});
</script>

</script>