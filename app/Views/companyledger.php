<?php include "common/header.php"; ?>
<div class="form-control right_container mb-3">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Company Ledger</h3>
    </div>

    <form id="ledgerForm">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="company_id" class="form-label">Select Company <span class="text-danger">*</span></label>
            <select class="form-control" name="company_id" id="company_id" required>
                <option value="">-- Select Company --</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= $company['company_id'] ?>"><?= esc($company['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save to Ledger</button>
</form>

</div>
                    </div>
<?php include "common/footer.php"; ?>

<script>
$(document).ready(function () {
    $('#ledgerForm').submit(function (e) {
    e.preventDefault();
    const companyId = $('#company_id').val();
    const alertBox = $('.alert');

    if (!companyId) {
        alertBox.removeClass().addClass('alert alert-danger text-center position-fixed')
            .text('Please Select A Company.').fadeIn();
        setTimeout(() => alertBox.fadeOut(), 2000);
        return;
    }

    $.ajax({
        url: "<?= base_url('companyledger/save') ?>",
        method: "POST",
        data: { company_id: companyId },
        dataType: "json",
        success: function (res) {
            if (res.status === 'success') {
                alertBox.removeClass().addClass('alert alert-success text-center position-fixed')
                    .text(res.message).fadeIn();
                $('#ledgerForm')[0].reset();
            } else {
                alertBox.removeClass().addClass('alert alert-danger text-center position-fixed')
                    .text(res.message).fadeIn();
            }
            setTimeout(() => alertBox.fadeOut(), 2000);
        }
    });
});

});
</script>
