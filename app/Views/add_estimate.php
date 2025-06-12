<?php include "common/header.php";?>
<!DOCTYPE html>
<html>
<head>
    <title>Estimate</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .estimate-box {
            border: 1px solid #000;
            padding: 20px;
        }
        .estimate-title {
            text-align: right;
            font-weight: bold;
            font-size: 24px;
        }
        .estimate-details {
            text-align: right;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000 !important;
        }
        .totals td {
            text-align: right;
        }
        .remove-item-btn {
            cursor: pointer;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5 estimate-box right_container">
        <div class="alert alert-fixed alert-dismissible fade show" role="alert"></div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3><?= isset($estimate['estimate_id']) ? 'Edit Estimate' : 'Estimate Generation' ?></h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="<?= base_url('estimatelist') ?>" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        <form id="estimate-form">
            <div class="row">
                <div class="col-md-6">
                    <label><strong>Customer Name</strong></label>
                    <input type="text" name="customer_name" value="<?= isset($estimate['customer_name']) ? $estimate['customer_name'] : '' ?>" class="form-control" required>

                    <label class="mt-3"><strong>Customer Address</strong></label>
                    <textarea name="customer_address" class="form-control" rows="3" required><?= isset($estimate['customer_address']) ? $estimate['customer_address'] : '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="estimate-title">ESTIMATE</div>
                    <div class="estimate-details">
                        <p id="estimate-id-display">Estimate No : <?= isset($estimate['estimate_id']) ? $estimate['estimate_id'] : '' ?></p>
                        <p>Date : <?= date('d-m-Y') ?></p>
                    </div>
                </div>
            </div>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Description Of Goods</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="item-container">
        <?php if (isset($items) && count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <tr class="item-row">
                    <td><input type="text" name="description[]" class="form-control" value="<?= $item['description'] ?>"></td>
                    <td><input type="number" name="price[]" class="form-control price" value="<?= $item['price'] ?>"></td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" value="<?= $item['quantity'] ?>"></td>
                    <td><input type="number" name="total[]" class="form-control total" value="<?= $item['total'] ?>" readonly></td>
                    <td class="text-center">
                        <span class="remove-item-btn" title="Remove">
                            <i class="fas fa-trash text-danger"></i>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr class="item-row">
                <td><input type="text" name="description[]" class="form-control" placeholder="Description"></td>
                <td><input type="number" name="price[]" class="form-control price"></td>
                <td><input type="number" name="quantity[]" class="form-control quantity"></td>
                <td><input type="number" name="total[]" class="form-control total" readonly></td>
                <td class="text-center">
                    <span class="remove-item-btn" title="Remove">
                        <i class="fas fa-trash text-danger"></i>
                    </span>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>

            </table>

            <button type="button" class="btn btn-outline-secondary mb-34" id="add-item">Add More Item</button>

            <table class="table totals">
                <tr>
                    <td><strong>Sub Total:</strong></td>
                    <td><span id="sub_total_display">0.00</span> KWD</td>
                </tr>
                <tr>
                    <td><strong>Discount:</strong></td>
                    <td>
                        <input type="number" name="discount" id="discount" class="form-control w-25 d-inline" value="<?= isset($estimate['discount']) ? $estimate['discount'] : '0' ?>" min="0">
                        KWD
                    </td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong><span id="total_display">0.00</span> KWD</strong></td>
                </tr>
            </table>
            <input type="hidden" name="estimate_id" value="<?= isset($estimate['estimate_id']) ? $estimate['estimate_id'] : '' ?>">
            <div class="text-right">
                <button type="submit" id="generate-btn" class="btn btn-primary">Generate Estimate</button>
            </div>
        </form>
    </div>
</div>
<?php include "common/footer.php"; ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
function calculateTotals() {
    let subtotal = 0;
    $('.item-row').each(function () {
        let qty = parseFloat($(this).find('.quantity').val()) || 0;
        let price = parseFloat($(this).find('.price').val()) || 0;
        let total = qty * price;
        $(this).find('.total').val(total.toFixed(2));
        subtotal += total;
    });

    $('#sub_total_display').text(subtotal.toFixed(2));

    // Treat discount as a percentage
    let discountPercent = parseFloat($('#discount').val()) || 0;
    let discountAmount = (subtotal * discountPercent) / 100;
    let grandTotal = subtotal - discountAmount;

    $('#total_display').text(grandTotal.toFixed(2));
}


function enableGenerateButton() {
    $('#generate-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
}

function disableGenerateButton() {
    $('#generate-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
}

$(document).ready(function () {
    const isEditMode = <?= isset($estimate['estimate_id']) ? 'true' : 'false' ?>;

    if (isEditMode) {
        disableGenerateButton(); // disable on load for edit
    }

    function showAlert(message, type = 'success') {
        $('.alert')
            .removeClass('alert-success alert-danger')
            .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
            .text(message)
            .fadeIn();

        setTimeout(() => {
            $('.alert').fadeOut();
        }, 3000);
    }

    function calculateTotals() {
        let subtotal = 0;
        $('.item-row').each(function () {
            let qty = parseFloat($(this).find('.quantity').val()) || 0;
            let price = parseFloat($(this).find('.price').val()) || 0;
            let total = qty * price;
            $(this).find('.total').val(total.toFixed(2));
            subtotal += total;
        });

        $('#sub_total_display').text(subtotal.toFixed(2));
        let discount = parseFloat($('#discount').val()) || 0;
        let grandTotal = subtotal - discount;
        $('#total_display').text(grandTotal.toFixed(2));
        $('#total_amount').val(grandTotal.toFixed(2));
    }

    function enableGenerateButton() {
        $('#generate-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    }

    function disableGenerateButton() {
        $('#generate-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
    }

    // Handle dynamic changes
    $(document).on('input change', '.quantity, .price, #discount, input[name="customer_name"], textarea[name="customer_address"], input[name="description[]"]', function () {
        calculateTotals();
        if (isEditMode) enableGenerateButton();
    });

    $('#add-item').on('click', function () {
        const newRow = `
            <tr class="item-row">
                <td><input type="text" name="description[]" class="form-control" placeholder="Description"></td>
                <td><input type="number" name="price[]" class="form-control price"></td>
                <td><input type="number" name="quantity[]" class="form-control quantity"></td>
                <td><input type="number" name="total[]" class="form-control total" readonly></td>
                <td class="text-center">
                    <span class="remove-item-btn" title="Remove">
                        <i class="fas fa-trash text-danger"></i>
                    </span>
                </td>
            </tr>`;
        $('#item-container').append(newRow);
        if (isEditMode) enableGenerateButton();
    });

    $(document).on('click', '.remove-item-btn', function () {
        $(this).closest('tr').remove();
        calculateTotals();
        if (isEditMode) enableGenerateButton();
    });

    $('#estimate-form').on('submit', function (e) {
        e.preventDefault();
        $('.alert').hide();

        const customerName = $('input[name="customer_name"]').val().trim();
        const customerAddress = $('textarea[name="customer_address"]').val().trim();

        if (!customerName || !customerAddress) {
            showAlert('Please fill in Customer Name and Address.', 'danger');
            return;
        }

        let validItems = 0;
        let invalidItemExists = false;

        $('.item-row').each(function () {
            const desc = $(this).find('input[name="description[]"]').val().trim();
            const price = parseFloat($(this).find('input[name="price[]"]').val());
            const qty = parseFloat($(this).find('input[name="quantity[]"]').val());

            if (desc && price > 0 && qty > 0) {
                validItems++;
            } else if (desc || price || qty) {
                invalidItemExists = true;
            } else {
                $(this).remove(); // clean up blank row before submission
            }
        });

        if (validItems === 0 || invalidItemExists) {
            showAlert('Enter at least one valid item with Description, Price > 0, and Quantity > 0.', 'danger');
            return;
        }

        $.ajax({
            url: "<?= site_url('estimate/save') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    showAlert(response.message, 'success');
                    setTimeout(() => {
                        window.location.href = "<?= base_url('estimatelist') ?>";
                    }, 1000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function () {
                showAlert('Something went wrong. Please try again.', 'danger');
            }
        });
    });

    if (!isEditMode) {
        $('#add-item').click(); // Only auto add row in create mode
    }

    calculateTotals();
});

$('.alert')
  .removeClass('alert-danger alert-success')
  .addClass('alert-success')
  .text(response.message)
  .fadeIn();

setTimeout(() => {
  $('.alert').fadeOut();
}, 3000);

</script>
</body>
</html>
