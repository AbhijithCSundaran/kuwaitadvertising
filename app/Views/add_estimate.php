<?php include "common/header.php"; ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="alert d-none text-center position-fixed" role=alert></div>
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

        .table-bordered td,
        .table-bordered th {
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

        .select2-container--default .select2-selection--single {
            height: 38px;
            /* same as Bootstrap input */
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-selection__rendered {
            line-height: 24px;
        }

        .select2-selection__arrow {
            height: 36px;
        }
    </style>
</head>
<body>
    <div class="mt-1 estimate-box right_container">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3><?= isset($estimate['estimate_id']) ? 'Edit Estimate' : 'Estimate Generation' ?></h3>
            </div>
        </div>
        <form id="estimate-form">
            <div class="row">
                <div class="col-md-6">
                    <label><strong> Customer</strong><span class="text-danger">*</span></label>
                    <div class="input-group mb-2 d-flex">
                        <select name="customer_id" id="customer_id" class="form-control select2" required>
                            <option value="" disabled <?= !isset($estimate['customer_id']) ? 'selected' : '' ?>>Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['customer_id'] ?>"
                                    <?= (isset($estimate['customer_id']) && $estimate['customer_id'] == $customer['customer_id']) ? 'selected' : '' ?>>
                                    <?= esc($customer['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary" id="addCustomerBtn">+</button>
                        </div>
                    </div>
                    <label class="mt-3"><strong>Customer Address</strong><span class="text-danger">*</span></label>
                    <textarea name="customer_address" id="customer_address" class="form-control" rows="3"
                        required><?= isset($estimate['customer_address']) ? $estimate['customer_address'] : '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="estimate-title">ESTIMATE</div>
                    <div class="estimate-details">
                        <p <p class="mb-1" id="estimate-id-display">Estimate No :
                            <?= isset($estimate['estimate_id']) ? $estimate['estimate_id'] : '' ?></p>
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
                                <td><input type="text" name="description[]" class="form-control"
                                        value="<?= $item['description'] ?>"></td>
                                <td><input type="number" name="price[]" class="form-control price"
                                        value="<?= $item['price'] ?>"></td>
                                <td><input type="number" name="quantity[]" class="form-control quantity"
                                        value="<?= $item['quantity'] ?>"></td>
                                <td><input type="number" name="total[]" class="form-control total" value="<?= $item['total'] ?>"
                                        readonly></td>
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
                        <input type="number" name="discount" id="discount" class="form-control w-50 d-inline"
                            value="<?= isset($estimate['discount']) ? $estimate['discount'] : '0' ?>" min="0">
                        %
                    </td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong><span id="total_display">0.00</span> KWD</strong></td>
                </tr>
            </table>
            <input type="hidden" name="estimate_id"
                value="<?= isset($estimate['estimate_id']) ? $estimate['estimate_id'] : '' ?>">
            <div class="text-right">
                <a href="<?= base_url('estimatelist') ?>" class="btn btn-secondary">Discard</a>
                <button type="submit" id="generate-btn" class="btn btn-primary">Generate Estimate</button>
            </div>
        </form>
    </div>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="customerForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            id="closeCustomerModalBtn"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" id="popup_name" required>
                            <!-- <textarea class="form-control" id="popup_address" rows="3" required></textarea> -->

                        </div>
                        <div class="form-group">
                            <label>Customer Address</label>
                            <!-- <input type="text" name="description[]" class="form-control description" required> -->
                            <textarea class="form-control" id="popup_address" rows="3" required></textarea>
                        </div>
                        <div class="alert alert-danger d-none" id="customerError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="saveCustomerBtn">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="cancelCustomerBtn">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php include "common/footer.php"; ?>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script>
        $(document).ready(function () {
            $('#customer_id').select2({
                placeholder: "Select Customer",
                // allowClear: true,
                width: 'calc(100% - 40px)',
                minimumResultsForSearch: 0 // always show the search bar
            });

            // Open modal when '+' is clicked
            $('#addCustomerBtn').on('click', function () {
                $('#customerModal').modal('show');
            });
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
                let discountAmt = (subtotal * discount) / 100;
                let total = subtotal - discountAmt;
                $('#total_display').text(total.toFixed(2));
            }

            $('#add-item').click(function () {
                $('#item-container').append(`
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
                `);
            });

            $(document).on('click', '.remove-item-btn', function () {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input change', '.price, .quantity, #discount', calculateTotals);
            calculateTotals();

            $('#addCustomerBtn').click(function () {
                $('#customerModal').modal('show');
            });
            $('#cancelCustomerBtn, #closeCustomerModalBtn').on('click', function () {
                $('#customerModal').modal('hide');
            });

            $('#customer_id').on('change', function () {
                var customerId = $(this).val();
                if (customerId === '') {
                    $('#customer_address').val('');
                    return;
                }

                $.ajax({
                    url: '<?= site_url('customer/get-address') ?>',
                    type: 'POST',
                    data: { customer_id: customerId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#customer_address').val(response.address);
                        } else {
                            $('#customer_address').val('');
                        }
                    },
                    error: function () {
                        $('#customer_address').val('');
                    }
                });
            });

            $('#customerForm').submit(function (e) {
                e.preventDefault();
                const name = $('#popup_name').val().trim();
                const address = $('#popup_address').val().trim();

                if (!name || !address) {
                    $('#customerError').removeClass('d-none').text('Please Enter Valid Name And Address');
                    return;
                }

                $.ajax({
                    url: "<?= site_url('customer/create') ?>",
                    type: "POST",
                    data: { name, address },
                    dataType: "json",
                    success: function (res) {
                        if (res.status === 'success') {
                            const newOption = new Option(res.customer.name, res.customer.customer_id, true, true);
                            $('#customer_id').append(newOption).trigger('change');
                            $('#popup_name').val('');
                            $('#popup_address').val('');
                            $('#customerModal').modal('hide');
                            $('.alert')
                                .removeClass('d-none alert-danger')
                                .addClass('alert-success')
                                .text('Customer Created Successfully.')
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        } else {
                            $('.alert')
                                .removeClass('d-none alert-success')
                                .addClass('alert-danger')
                                .text(res.message || 'Failed To Create Customer.')
                                .fadeIn()
                                .delay(3000)
                                .fadeOut();
                        }
                    },
                    error: function () {
                        $('.alert')
                            .removeClass('d-none alert-success')
                            .addClass('alert-danger')
                            .text('Server Error Occurred While Creating Customer.')
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                    }
                });
            });

            $('#estimate-form').submit(function (e) {
                e.preventDefault();

                let validItemExists = false;

    
                $('.item-row').each(function () {
                    const desc = $(this).find('input[name="description[]"]').val().trim();
                    const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
                    const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;

                    if (desc && price > 0 && qty > 0) {
                        validItemExists = true;
                        return false; // exit loop early
                    }
                });

                if (!validItemExists) {
                    $('.alert')
                        .removeClass('d-none alert-success alert-warning')
                        .addClass('alert-danger')
                        .text('Please enter at least one valid item with Description, Price, and Quantity.')
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();
                    return;
                }


                $('.item-row').each(function () {
                    const desc = $(this).find('input[name="description[]"]').val().trim();
                    const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
                    const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;

                    if (!desc && price === 0 && qty === 0) {
                        $(this).remove();
                    }
                });

    
            $.ajax({
            url: "<?= site_url('estimate/save') ?>",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    $('.alert')
                        .removeClass('d-none alert-danger alert-warning')
                        .addClass('alert-success')
                        .text(res.message)
                        .fadeIn();
                    setTimeout(function () {
                        window.location.href = "<?= site_url('estimate/generateEstimate/') ?>" + res.estimate_id;
                    }, 1500);
                } else if (res.status === 'nochange') {
                    $('.alert')
                        .removeClass('d-none alert-success alert-danger')
                        .addClass('alert-warning')
                        .text(res.message)
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();
                } else {
                    $('.alert')
                        .removeClass('d-none alert-success alert-warning')
                        .addClass('alert-danger')
                        .text(res.message || 'Failed To Save Estimate.')
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();
                }
            },
            error: function () {
                $('.alert')
                    .removeClass('d-none alert-success alert-warning')
                    .addClass('alert-danger')
                    .text('Something Went Wrong While Saving The Estimate.')
                    .fadeIn()
                    .delay(3000)
                    .fadeOut();
            }
            
        });
    });
 });
$(window).on('keydown', function (e) {
    // Ctrl + Enter to generate
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        $('#generate-btn').trigger('click');
    }

    // Ctrl + F to add row
    if (e.ctrlKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        $('#add-item').trigger('click');
    }
});
</script>