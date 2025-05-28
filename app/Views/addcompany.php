<?php include "common/header.php";?>
<div class="form-control"> 
	<div class="row">
		<div class="col-md-6">
			<h3>Add Company</h3>
		</div>
		<div class="back col-md-6 text-right">
			<a href="<?= base_url('companylist') ?>"><button class="btn btn-secondary">Back to list</button></a>
		</div>
		<div class="col-md-12"><hr/></div>
	</div>
	<div class="col-md-12 no-gutters">
	
		<form id="company-form" enctype="multipart/form-data" method="post">
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Name</label>
					<input type="text" name="company_name" id="company_name" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Company Address</label>
					<input type="text" name="address" id="address" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Tax Number</label>
					<input type="text" name="tax_number" id="tax_number" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<label>Company Logo</label>
					<input type="file" name="company_logo" id="company_logo" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 no-gutters">
					<button type="button"  class="enter-btn btn btn-primary">Save</button>
					<input type="hidden" name="uid" id="uid" value="">
					<div class="alert" role="alert"></div>
				</div>
			</div>	
		</form>
	</div>
</div>
</div>
<?php include "common/footer.php";?>
<script>
$(document).ready(function () {
    $('.enter-btn').on('click', function (e) {
        e.preventDefault();

        // Create FormData object
        var form = $('#company-form')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '<?= base_url('managecompany/save') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $('.alert')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text(response.message)
                    .show();
                $('#company-form')[0].reset(); 
            },
            error: function (xhr, status, error) {
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
