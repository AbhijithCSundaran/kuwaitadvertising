<?php include "common/header.php";?>
<style>
input[type=number].no-spinner::-webkit-inner-spin-button,
input[type=number].no-spinner::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number].no-spinner {
    -moz-appearance: textfield;
}
</style>
<div class="form-control mb-3 right_container">
     <div class="alert d-none text-center position-fixed" role="alert"></div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-6">
                <h3 class="mb-0"><?= isset($isEdit) && $isEdit ? 'Edit Expense' : 'Add New Expense' ?></h3>
            </div>    
        </div>
        <hr class="d-none d-md-block">
        <div class="card-body p-3 px-md-4">
            <form id="expense-form">
                <div class ="row"> 
                    <?php
                        if (!empty($expense['date'])) {
                            $defaultDate = date('d-m-Y', strtotime($expense['date']));
                        } else {
                            $defaultDate = date('d-m-Y'); 
                        }
                    ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

                    <style>
                        .calendar-input-wrapper {
                            position: relative;
                        }

                        .calendar-icon {
                            position: absolute;
                            right: 10px;
                            top: 50%;
                            transform: translateY(-50%);
                            cursor: pointer;
                            color: #555;
                        }

                        .calendar-input-wrapper input {
                            padding-right: 35px; 
                        }
                    </style>

                    <div class="form-group col-md-6">
                        <label>Date <span class="text-danger">*</span></label>
                        <div class="calendar-input-wrapper">
                            <input type="text" name="date" id="date" class="form-control" value="<?= $defaultDate ?>" required>
                            <span class="calendar-icon" id="calendar-icon"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>

                </div>
                <div class ="row"> 
                    <div class="form-group col-md-6">
                        <label>Particular <span class="text-danger">*</span></label>
                        <textarea name="particular" class="form-control capitalize" rows="3" required><?= isset($expense['particular']) ? $expense['particular'] : '' ?></textarea>
                    </div>
                    <div class="form-group col-md-6">
                            <label>Reference</label>
                            <input type="text" name="reference" class="form-control" value="<?= isset($expense['reference']) ? esc($expense['reference']) : '' ?>">
                        </div>
                
                    <div class="form-group col-md-6">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="text" name="amount" id="amount" class="form-control no-spinner" inputmode="decimal" value="<?= isset($expense['amount']) ? $expense['amount'] : '' ?>" required>
                        <small class="text-danger d-none" id="amountError">Please enter a valid amount </small>
                    </div>

                    <br><br>
                    <div class="form-group col-md-6">
                        <label>Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-control" required>
                            <option value="">Select</option>
                            <option value="cash" <?= isset($expense['payment_mode']) && strtolower($expense['payment_mode']) == 'cash' ? 'selected' : '' ?>>Cash</option>
                            <option value="bank transfer" <?= isset($expense['payment_mode']) && strtolower($expense['payment_mode']) == 'bank transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                        <option value="bank transfer" <?= isset($expense['payment_mode']) && strtolower($expense['payment_mode']) == 'bank transfer' ? 'selected' : '' ?>>Bank Link</option>
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
const dateInput = document.getElementById("date");
const calendar = flatpickr(dateInput, {
    dateFormat: "d-m-Y",
    defaultDate: "<?= $defaultDate ?>"
});

document.getElementById("calendar-icon").addEventListener("click", function () {
    calendar.open();
});

$(document).ready(function () {
    let originalData = $('#expense-form').serialize(); 

    $('#expense-form input, #expense-form select, #expense-form textarea').on('input change', function () {
        const currentData = $('#expense-form').serialize();
        $('#saveExpenseBtn').prop('disabled', currentData === originalData);
    });

    // Restrict invalid characters in Amount field
    $('#amount').on('keypress', function (e) {
        const key = e.key;
        const val = $(this).val();

        // Allow: digits and one dot (.)
        if (!/[0-9.]/.test(key) || (key === '.' && val.includes('.'))) {
            e.preventDefault();
        }
    });

    $('#saveExpenseBtn').on('click', function () {
        const alertBox = $('.alert');
        const form = $('#expense-form')[0];
        const amountInput = $('#amount');
        const amountError = $('#amountError');

        let amountVal = amountInput.val().trim();
        if (amountVal === '.' || amountVal === '') {
            amountVal = '0.';
            amountInput.val(amountVal);
        }
        const validAmount = /^\d+(\.\d{0,2})?$/.test(amountVal);
        if (!form.checkValidity()) {
            alertBox.removeClass('d-none alert-success alert-warning')
                    .addClass('alert-danger')
                    .text('Please Fill All Mandatory Fields.');
            setTimeout(() => alertBox.addClass('d-none').text(''), 2000);
            return;
        }

        if (!validAmount) {
            amountError.removeClass('d-none');
            amountInput.addClass('is-invalid');
            return;
        } else {
            amountError.addClass('d-none');
            amountInput.removeClass('is-invalid');
        }

        const formData = new FormData(form);
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
                    alertBox.removeClass('d-none alert-danger alert-warning')
                            .addClass('alert-success')
                            .text(res.message);
                    setTimeout(() => {
                        if (res.redirect_to_list) {
                            window.location.href = "<?= base_url('expense') ?>";
                        } else {
                            window.location.href = "<?= base_url('addexpense') ?>"; 
                        }
                    }, 1500);
                } else if (res.status === 'nochange') {
                    alertBox.removeClass('d-none alert-success alert-danger')
                            .addClass('alert-warning')
                            .text(res.message);
                    setTimeout(() => {
                        window.location.href = "<?= base_url('expense') ?>";
                    }, 1500);
                } else {
                    alertBox.removeClass('d-none alert-success alert-warning')
                            .addClass('alert-danger')
                            .text(res.message || 'Failed to Save Expense.');
                    $('#saveExpenseBtn').prop('disabled', false);
                }
            },
            error: function () {
                alertBox.removeClass('d-none alert-success alert-warning')
                        .addClass('alert-danger')
                        .text('Error Occurred While Saving Expense.');
                $('#saveExpenseBtn').prop('disabled', false);
            }
        });
    });
});
</script>


