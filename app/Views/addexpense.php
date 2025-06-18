<?php include "common/header.php";?>
<div class="form-control mb-3 right_container">
     <div class="alert d-none text-center position-fixed" role="alert"></div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-6">
                <h3 class="mb-0"><?= isset($isEdit) && $isEdit ? 'Edit Expense' : 'Add New Expense' ?></h3>
            </div>    
        </div>
        <hr>
        <div class="card-body">
            <form id="expense-form">
                <div class ="row"> 
                    <div class="form-group col-md-6">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="<?= isset($expense['date']) ? $expense['date'] : '' ?>" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Particular <span class="text-danger">*</span></label>
                        <input type="text" name="particular" class="form-control" value="<?= isset($expense['particular']) ? $expense['particular'] : '' ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" step="0.01" class="form-control" value="<?= isset($expense['amount']) ? $expense['amount'] : '' ?>" required>
                    </div>
                    <br><br>
                    <div class="form-group col-md-6">
                        <label>Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-control" required>
                            <option value="">Select</option>
                            <option value="cash" <?= isset($expense['payment_mode']) && strtolower($expense['payment_mode']) == 'cash' ? 'selected' : '' ?>>Cash</option>
                            <option value="bank transfer" <?= isset($expense['payment_mode']) && strtolower($expense['payment_mode']) == 'bank transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                            <option value="wamd" <?= isset($expense['payment_mode']) && strtolower($expense['payment_mode']) == 'wamd' ? 'selected' : '' ?>>WAMD</option>
                        </select>

                    </div>
                </div>
                <div class="col-md-12 text-end">
                <a href="<?= base_url('expense') ?>" class="btn btn-secondary">Discard</a>
            
                
                    <input type="hidden" name="id" value="<?= isset($expense['id']) ? $expense['id'] : '' ?>">
                    <button type="button" class="btn btn-primary" id="saveExpenseBtn" disabled>Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>
<script>
$(document).ready(function () {
    let originalData = $('#expense-form').serialize(); 

    $('#expense-form input, #expense-form select, #expense-form textarea').on('input change', function () {
        const currentData = $('#expense-form').serialize();
        $('#saveExpenseBtn').prop('disabled', currentData === originalData);
    });
    $('#saveExpenseBtn').on('click', function () {
        const alertBox = $('.alert');
        const form = $('#expense-form')[0];

        if (!form.checkValidity()) {
            alertBox
                .removeClass('d-none alert-success alert-warning')
                .addClass('alert-danger')
                .text('Please Fill All Mandatory Fields.');
            setTimeout(() => alertBox.addClass('d-none').text(''), 2000);
            return;
        }

        const formData = new FormData(form);

        // Disable save button to prevent multiple submissions
        $('#saveExpenseBtn').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('expense/store') ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    alertBox
                        .removeClass('d-none alert-danger alert-warning')
                        .addClass('alert-success')
                        .text(res.message);
                    setTimeout(() => {
                        window.location.href = "<?= base_url('expense') ?>";
                    }, 1500);

                } else if (res.status === 'nochange') {
                    alertBox
                        .removeClass('d-none alert-success alert-danger')
                        .addClass('alert-warning')
                        .text(res.message);
                    setTimeout(() => {
                        window.location.href = "<?= base_url('expense') ?>";
                    }, 1500);
                } else {
                    alertBox
                        .removeClass('d-none alert-success alert-warning')
                        .addClass('alert-danger')
                        .text(res.message || 'Failed to Save Expense.');
                    $('#saveExpenseBtn').prop('disabled', false);
                }
            },
            error: function () {
                alertBox
                    .removeClass('d-none alert-success alert-warning')
                    .addClass('alert-danger')
                    .text('Error Occurred While Saving Expense.');
                $('#saveExpenseBtn').prop('disabled', false);
            }
        });
    });
});

</script>

