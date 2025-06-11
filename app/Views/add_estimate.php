<?php include "common/header.php"; ?>
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
<div class="container mt-5 estimate-box">
<div class="alert mt-3" style="display:none;"></div>

    <div class="row mb-3">
        <div class="col-md-6">
            <h3>Estimate Generation</h3>
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
                <td class="text-center"><span class="remove-item-btn">&times;</span></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr class="item-row">
            <td><input type="text" name="description[]" class="form-control" placeholder="Description"></td>
            <td><input type="number" name="price[]" class="form-control price"></td>
            <td><input type="number" name="quantity[]" class="form-control quantity"></td>
            <td><input type="number" name="total[]" class="form-control total" readonly></td>
            <td class="text-center"><span class="remove-item-btn">&times;</span></td>
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
            <button type="submit" class="btn btn-primary">Generate Estimate</button>
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
    let discount = parseFloat($('#discount').val()) || 0;
    let grandTotal = subtotal - discount;
    $('#total_display').text(grandTotal.toFixed(2));
    $('#total_amount').val(grandTotal.toFixed(2));
}

$(document).ready(function () {
    $(document).on('input', '.quantity, .price, #discount', calculateTotals);

    $('#add-item').on('click', function () {
        const newRow = `
            <tr class="item-row">
                <td><input type="text" name="description[]" class="form-control" placeholder="Description"></td>
                <td><input type="number" name="price[]" class="form-control price"></td>
                <td><input type="number" name="quantity[]" class="form-control quantity"></td>
                <td><input type="number" name="total[]" class="form-control total" readonly></td>
                <td class="text-center"><span class="remove-item-btn">&times;</span></td>
            </tr>`;
        $('#item-container').append(newRow);
    });

    $(document).on('click', '.remove-item-btn', function () {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    $('#estimate-form').on('submit', function (e) {
        e.preventDefault();
        $('.alert').hide();

        const customerName = $('input[name="customer_name"]').val().trim();
        const customerAddress = $('textarea[name="customer_address"]').val().trim();

        if (!customerName || !customerAddress) {
            $('.alert').removeClass('alert-success').addClass('alert-danger')
                .text('Please fill in Customer Name and Address.').show();
            return;
        }

        let validItems = 0;
        let hasInvalidItem = false;

        $('.item-row').each(function () {
            const desc = $(this).find('input[name="description[]"]').val().trim();
            const price = parseFloat($(this).find('input[name="price[]"]').val());
            const qty = parseFloat($(this).find('input[name="quantity[]"]').val());

            if (desc && price > 0 && qty > 0) {
                validItems++;
            } else if (desc || price || qty) {
                hasInvalidItem = true;
            }
        });

        if (validItems === 0 || hasInvalidItem) {
            $('.alert').removeClass('alert-success').addClass('alert-danger')
                .text('Enter at least one valid item with Description, Price > 0, and Quantity > 0.').show();
            return;
        }

        $.ajax({
            url: "<?= site_url('estimate/save') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $('.alert').removeClass('alert-danger').addClass('alert-success')
                        .text(response.message).show();
                    $('#estimate-id-display').text('Estimate No : ' + response.estimate_id);
                    $('#estimate-form')[0].reset();
                    $('#item-container').html('');
                    $('#add-item').click();
                    calculateTotals();
                } else {
                    $('.alert').removeClass('alert-success').addClass('alert-danger')
                        .text(response.message).show();
                }
            },
            error: function () {
                $('.alert').removeClass('alert-success').addClass('alert-danger')
                    .text('Something went wrong. Please try again.').show();
            }
        });
    });

    $('#add-item').click();
});

</script>
</body>
</html>
