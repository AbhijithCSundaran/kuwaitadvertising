<?php include "common/header.php";?>
<div class="alert d-none text-center position-fixed" role="alert"></div>
<!-- <div id="companyAlert" class="alert alert-danger w-50 mx-auto text-center fixed top mt-3 alert-fixed" role="alert"></div> -->
    <div class="form-control right_container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?= isset($company['company_id']) ? 'Edit Company' : 'Add Company' ?></h3>
            </div>
            <hr/>
            <div class="card-body">
                <form id="company-form" enctype="multipart/form-data" method="post">
                    <div class="d-flex flex-wrap">
                        
                        <div class="col-6 mb-3 px-2">
                            <label for="company_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" class="form-control" maxlength="50"
                            value="<?= isset($company['company_name']) ? esc($company['company_name']) : '' ?>" />
                        </div>

                        <div class="col-6 mb-3 px-2">
                            <label for="address" class="form-label">Company Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control" maxlength="150"
                                style="resize: vertical;"
                                rows="3"><?= isset($company['address']) ? esc($company['address']) : '' ?></textarea>
                        </div>

                        <div class="col-6 mb-3 px-2">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                            value="<?= isset($company['email']) ? esc($company['email']) : '' ?>" />
                        </div>
                        <div class="col-6 mb-3 px-2">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" maxlength="10" pattern="\d{10}"
                            value="<?= isset($company['phone']) ? esc($company['phone']) : '' ?>" />
                        </div>
                        
                        <div class="col-6 mb-3 px-2">
                            <label for="tax_number" class="form-label">Tax Number</label>
                            <input type="text" name="tax_number" id="tax_number" class="form-control"
                            value="<?= isset($company['tax_number']) ? esc($company['tax_number']) : '' ?>" />
                        </div>
                        <div class="col-6 mb-3 px-2">
                            <label class="form-label">Company Logo <span class="text-danger">*</span></label>
                           <?php if (!isset($company['company_id'])): ?>
                                <input type="file" name="company_logo" id="company_logo" class="form-control" accept="image/*" />
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
                                <div>
                                    <input type="hidden" name="original_logo" id="original_logo"
                                    value="<?= isset($company['company_logo']) ? esc($company['company_logo']) : '' ?>" />
                                </div>
                        </div>

                        <input type="hidden" name="uid" id="uid"
                        value="<?= isset($company['company_id']) ? esc($company['company_id']) : '' ?>" />
                        <div class="col-12 p-3 d-flex justify-content-end gap-2" >
                            <a href="<?= base_url('companylist') ?>" class="btn btn-secondary">Discard</a>
                               <button type="button" class="btn btn-primary enter-btn" 
                             <?= isset($company['company_id']) ? 'disabled style="opacity: 0.6;"' : '' ?>>Save</button>
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
    const isEditMode = $('#uid').val().trim() !== '';
    const $saveBtn = $('.enter-btn');
    const $form = $('#company-form');

    const originalData = {
        name: $('#company_name').val(),
        address: $('#address').val(),
        tax: $('#tax_number').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
        logo: $('#original_logo').val()
    };

    if (isEditMode) {
        $saveBtn.prop('disabled', true).css('opacity', 0.6);
    }

    function checkChanges() {
        const currentData = {
            name: $('#company_name').val(),
            address: $('#address').val(),
            tax: $('#tax_number').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            logoChanged: $('#company_logo')[0].files.length > 0
        };

        const hasChanged =
            currentData.name !== originalData.name ||
            currentData.address !== originalData.address ||
            currentData.tax !== originalData.tax ||
            currentData.email !== originalData.email ||
            currentData.phone !== originalData.phone ||
            currentData.logoChanged;

        if (isEditMode) {
            if (hasChanged) {
                $saveBtn.prop('disabled', false).css('opacity', 1);
            } else {
                $saveBtn.prop('disabled', true).css('opacity', 0.6);
            }
        }
    }

    $('#company_name, #address, #tax_number, #email, #phone, #company_logo').on('input change', checkChanges);

    $saveBtn.on('click', function (e) {
        e.preventDefault();

        if ($(this).prop('disabled')) return;

        let name = $('#company_name').val().trim();
        let address = $('#address').val().trim();
        let tax = $('#tax_number').val().trim();
        let email = $('#email').val().trim();
        let phone = $('#phone').val().trim();
        let uid = $('#uid').val().trim();
        let fileInput = $('#company_logo')[0];
        let file = fileInput ? fileInput.files[0] : null;

        if (!name || !address) {
            showMessage('Please fill in all required fields.', 'danger');
            return;
        }

        if (!/[a-zA-Z]/.test(name)) {
            showMessage('Company Name must contain at least one letter.', 'danger');
            return;
        }

        if (!/[a-zA-Z]/.test(address)) {
            showMessage('Company Address must contain at least one letter.', 'danger');
            return;
        }

        if (!email || !phone) {
            showMessage('Email and phone number are required.', 'danger');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showMessage('Please enter a valid email address.', 'danger');
            return;
        }

        if (!/^[0-9+\-\s]{7,20}$/.test(phone)) {
            showMessage('Please enter a valid phone number.', 'danger');
            return;
        }

        if (!file && !uid) {
            showMessage('Please upload a company logo (image file).', 'danger');
            return;
        }

        if (file) {
            const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
            if (!validImageTypes.includes(file.type)) {
                showMessage('Only image files (JPG, PNG, GIF) are allowed.', 'danger');
                return;
            }
        }

        const formData = new FormData($form[0]);
        $saveBtn.prop('disabled', true).css('opacity', 0.6); // Prevent double click

        $.ajax({
            url: '<?= base_url('managecompany/save') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $('.alert').removeClass('d-none alert-danger alert-success alert-warning');

                if (response.status === 'error') {
                    showMessage(response.message, 'danger');
                    $saveBtn.prop('disabled', false).css('opacity', 1);
                } else {
                    showMessage(response.message, 'success');
                    setTimeout(() => {
                        $('.alert').fadeOut();
                        window.location.href = "<?= base_url('companylist') ?>";
                    }, 3000);
                }
            },
            error: function () {
                showMessage('Something went wrong. Please try again.', 'danger');
                $saveBtn.prop('disabled', false).css('opacity', 1);
            }
        });
    });

    function showMessage(msg, type) {
        $('.alert')
            .removeClass('d-none alert-danger alert-success alert-warning')
            .addClass('alert-' + type)
            .html(msg)
            .fadeIn();

        setTimeout(() => {
            $('.alert').fadeOut();
        }, 3000);
    }
});
</script>
