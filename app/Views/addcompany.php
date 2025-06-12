<?php include "common/header.php";?>
<div id="companyAlert" class="alert alert-danger text-center alert-fixed" role="alert"></div>
<div class="form-control right_container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><?= isset($company['company_id']) ? 'Edit Company' : 'Add Company' ?></h3>
            <a href="<?= base_url('companylist') ?>" class="btn btn-secondary">Back to List</a>
        </div>
        <hr/>
        <div class="card-body">
            <form id="company-form" enctype="multipart/form-data" method="post">
                <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="company_name" class="form-label">Name</label>
                    <input type="text" name="company_name" id="company_name" class="form-control" 
                    style="height: 40px; width: 80%; font-size: 14px;"
                        value="<?= isset($company['company_name']) ? esc($company['company_name']) : '' ?>" />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">Company Address</label>
                    <textarea name="address" id="address" class="form-control" 
                    style="height: 30px; width: 80%; font-size: 14px;"
                    rows="3"><?= isset($company['address']) ? esc($company['address']) : '' ?></textarea>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tax_number" class="form-label">Tax Number</label>
                    <input type="text" name="tax_number" id="tax_number" class="form-control"
                    style="height: 40px; width: 80%; font-size: 14px;"
                        value="<?= isset($company['tax_number']) ? esc($company['tax_number']) : '' ?>" />
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Logo</label>
                    <?php if (!isset($company['company_id'])): ?>
                        <input type="file" name="company_logo" id="company_logo" class="form-control" 
                        style="height: 40px; width: 80%; font-size: 14px;"accept="image/*" />
                    <?php else: ?>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="btn-browse-file">Choose File</button>
                            <input type="text" id="fake-file-name" class="form-control" readonly
                                value="<?= esc($company['company_logo']) ?>" />
                            <input type="file" name="company_logo" id="company_logo" class="d-none" accept="image/*" />
                        </div>
                        <div class="mt-2">
                            <strong>Current Logo Preview:</strong><br>
                            <img id="logo-preview" src="<?= base_url('public/uploads/' . $company['company_logo']) ?>" width="100" class="border p-1" />
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="original_logo" id="original_logo"
                        value="<?= isset($company['company_logo']) ? esc($company['company_logo']) : '' ?>" />
                </div>
                </div>

                <input type="hidden" name="uid" id="uid"
                    value="<?= isset($company['company_id']) ? esc($company['company_id']) : '' ?>">

                <div class="mt-4 save-comp" style="text-align: right;">
                    <button type="button" class="btn btn-primary enter-btn" 
                        <?= isset($company['company_id']) ? 'disabled style="opacity: 0.6;"' : '' ?>>Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
                    </div>
<?php include "common/footer.php"; ?>
<script>
$(document).ready(function () {
    function containsLetters(str) {
        return /[a-zA-Z]/.test(str);
    }

    function containsAlphaNumeric(str) {
        return /[a-zA-Z0-9]/.test(str);
    }

    const originalData = {
        name: $('#company_name').val(),
        address: $('#address').val(),
        tax: $('#tax_number').val(),
        logo: $('#original_logo').val()
    };

    function checkChanges() {
		const currentData = {
			name: $('#company_name').val(),
			address: $('#address').val(),
			tax: $('#tax_number').val(),
			logoChanged: $('#company_logo')[0].files.length > 0
		};

		const hasChanged =
			currentData.name !== originalData.name ||
			currentData.address !== originalData.address ||
			currentData.tax !== originalData.tax ||
			currentData.logoChanged;

		if (hasChanged) {
			$('.enter-btn').prop('disabled', false).css('opacity', 1);
		} else {
			$('.enter-btn').prop('disabled', true).css('opacity', 0.6);
		}
	}


    $('#company_name, #address, #tax_number').on('input', checkChanges);
    $('#company_logo').on('change', checkChanges);

    <?php if (isset($company['company_id'])): ?>
        $('#btn-browse-file').on('click', function () {
            $('#company_logo').click();
        });

        $('#company_logo').on('change', function () {
            const file = this.files[0];
            if (file) {
                const validTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
                if (!validTypes.includes(file.type)) {
                    alert('Only JPG, PNG, and GIF image files are allowed.');
                    $(this).val('');
                    $('#fake-file-name').val("<?= esc($company['company_logo']) ?>");
                    $('#logo-preview').attr('src', "<?= base_url('public/uploads/' . $company['company_logo']) ?>");
                    return;
                }
                $('#fake-file-name').val(file.name);
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#logo-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                $('#fake-file-name').val("<?= esc($company['company_logo']) ?>");
                $('#logo-preview').attr('src', "<?= base_url('public/uploads/' . $company['company_logo']) ?>");
            }
        });
    <?php endif; ?>

    $('.enter-btn').on('click', function (e) {
        e.preventDefault();

        let name = $('#company_name').val().trim();
        let address = $('#address').val().trim();
        let tax = $('#tax_number').val().trim();
        let uid = $('#uid').val().trim();
        let fileInput = $('#company_logo')[0];
        let file = fileInput ? fileInput.files[0] : null;

        if (!name || !address || !tax) {
            showMessage('Please fill in all required fields.', 'danger');
            return;
        }

        if (!containsLetters(name)) {
            showMessage('Company Name must contain at least one letter.', 'danger');
            return;
        }

        if (!containsLetters(address)) {
            showMessage('Company Address must contain at least one letter.', 'danger');
            return;
        }

        if (!containsAlphaNumeric(tax)) {
            showMessage('Tax Number must contain at least one letter or number.', 'danger');
            return;
        }

        if (!file && !uid) {
            showMessage('Please upload a company logo (image file).', 'danger');
            return;
        }

        if (file) {
            let fileType = file.type;
            let validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
            if ($.inArray(fileType, validImageTypes) < 0) {
                showMessage('Only image files (JPG, PNG, GIF) are allowed for the company logo.', 'danger');
                return;
            }
        }

        let form = $('#company-form')[0];
        let formData = new FormData(form);

        $.ajax({
            url: '<?= base_url('managecompany/save') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $('.alert').removeClass('alert-danger alert-success alert-warning');

                if (response.status === 'error') {
                    showMessage(response.message, 'danger');
                } else {
                    showMessage(response.message, 'success');
                    setTimeout(() => {
                        $('#companyAlert').fadeOut();
                        window.location.href = "<?= base_url('companylist') ?>";
                    }, 3000);
                }
            },
            error: function () {
                showMessage('Something went wrong. Please try again.', 'danger');
            }
        });
    });

    function showMessage(msg, type) {
        $('.alert')
            .removeClass('alert-danger alert-success alert-warning')
            .addClass('alert-' + type)
            .html(msg)
            .fadeIn();

        setTimeout(function () {
            $('#companyAlert').fadeOut();
        }, 3000);
    }
});
</script>
