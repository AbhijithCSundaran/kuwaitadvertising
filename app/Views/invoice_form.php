<?php include "common/header.php"; ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="alert d-none text-center position-fixed" role="alert"></div>


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
            position: absolute;
            right: 15px;
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

        textarea[readonly] {
            background-color: #fff !important;
            border-color: #ced4da;
            /* Optional: match normal border */
            box-shadow: none !important;
            /* Remove blue focus glow */
            color: #212529;
            /* Default text color */
        }
    </style>
</head>

<div class="mt-1 estimate-box right_container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3><?= isset($invoice['invoice_id']) ? 'Edit Invoice' : 'Create Invoice' ?></h3>
        </div>

        <div class="col-md-6 text-end">
            <div class="estimate-title">INVOICE</div>
            <div class="estimate-details">
                <p class="mb-1">Invoice No:
                    <?= isset($invoice['invoice_id']) ? $invoice['invoice_id'] : '' ?>
                </p>
                <p>Date: <?= date('d-m-Y') ?></p>
            </div>
        </div>
    </div>

    <form id="invoice-form">
        <div class="row">
            <div class="col-md-6">
                <label><strong>Customer Name</strong><span class="text-danger">*</span></label>
                <div class="input-group mb-2 d-flex">
                    <select name="customer_id" id="customer_id" class="form-control select2">
                        <option value="" disabled <?= !isset($invoice['customer_id']) ? 'selected' : '' ?>>Select
                            Customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['customer_id'] ?>" <?= (isset($invoice['customer_id']) && $invoice['customer_id'] == $customer['customer_id']) ? 'selected' : '' ?>>
                                <?= esc($customer['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" id="addCustomerBtn">+</button>
                    </div>
                </div>
            </div>
            <?php
            $billingVal = isset($invoice['customer_address']) ? trim($invoice['customer_address']) : '';
            $shippingVal = isset($invoice['shipping_address']) ? trim($invoice['shipping_address']) : '';
            $isSame = $billingVal !== '' && $billingVal === $shippingVal;
            ?>
            <div class="row">
                <!-- Billing Address -->
                <div class="col-md-6">
                    <label for="customer_address" class="form-label ">
                        <strong>Billing Address</strong> <span class="text-danger">*</span>

                    </label>
                    <textarea name="customer_address" id="customer_address" class="form-control capitalize"
                        maxlength="150" style="resize: vertical;" rows="3"><?= esc($billingVal) ?></textarea>
                </div>

                <!-- Shipping Address -->
                <div class="col-md-6">
                    <label for="shipping_address"
                        class="form-label d-flex flex-column-reverse flex-md-row justify-content-md-between align-items-md-center"><span><strong>Shipping
                                Address</strong> <span class="text-danger">*</span></span>
                        <div class="form-check d-flex align-items-center ps-3 ps-md-0 pb-2 pb-md-0 pe-2 m-0 sameas">
                            <input type="checkbox" class="form-check-input me-1" id="sameAddressCheck" <?= $isSame ? 'checked' : '' ?>>
                            <label class="form-check-label small m-0" for="sameAddressCheck">Same as Billing
                                Address</label>
                        </div>
                    </label>
                    <textarea name="shipping_address" id="shipping_address" class="form-control capitalize"
                        maxlength="150" style="resize: vertical;" rows="3"><?= esc($shippingVal) ?></textarea>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <label class="mt-3"><strong>LPO No</strong></label>
                    <input type="text" name="lpo_no" id="lpo_no" class="form-control"
                        value="<?= isset($invoice['lpo_no']) ? esc($invoice['lpo_no']) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="mt-3"><strong>Phone Number</strong><span class="text-danger">*</span></label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control"
                        value="<?= isset($invoice['phone_number']) ? esc($invoice['phone_number']) : '' ?>"
                        minlength="7" maxlength="15" pattern="^\+?[0-9]{7,15}$"
                        title="Phone number must be 7 to 15 digits and can start with +" />
                </div>
            </div>
        </div>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="item-container">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr class="item-row">
                            <td><input type="text" name="description[]" class="form-control"
                                    value="<?= esc($item['item_name']) ?>"></td>
                            <td><input type="number" class="form-control price" name="price[]" value="<?= $item['price'] ?>">
                            </td>
                            <td><input type="number" class="form-control quantity" name="quantity[]"
                                    value="<?= $item['quantity'] ?>"></td>
                            <td><input type="number" class="form-control total" name="total[]" step="0.01"
                                    value="<?= $item['total'] ?>" readonly></td>
                            <td class="text-center">
                                <span class="remove-item-btn" title="Remove"><i class="fas fa-trash text-danger"></i></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="item-row">
                        <td><input type="text" name="description[]" class="form-control" placeholder="Description"></td>
                        <td><input type="number" name="price[]" class="form-control price" step="0.01" min="0"></td>
                        <td><input type="number" name="quantity[]" class="form-control quantity"></td>
                        <td><input type="number" name="total[]" class="form-control total" readonly></td>
                        <td class="text-center">
                            <span class="remove-item-btn" title="Remove"><i class="fas fa-trash text-danger"></i></span>
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

        <button type="button" class="btn btn-outline-secondary mb-3" id="add-item">Add More Item</button>

        <table class="table totals">
            <tr>
                <td><strong>Sub Total:</strong></td>
                <td><span id="sub_total_display">0.00</span> KWD</td>
            </tr>
            <tr>
                <td><strong>Discount:</strong></td>
                <td>
                    <input type="number" name="discount" id="discount" class="form-control w-50 d-inline"
                        value="<?= isset($invoice['discount']) ? $invoice['discount'] : '0' ?>" min="0"> %
                </td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td><strong><span id="total_display">0.00</span> KWD</strong></td>
            </tr>
        </table>

        <input type="hidden" name="invoice_id"
            value="<?= isset($invoice['invoice_id']) ? $invoice['invoice_id'] : '' ?>">

        <div class="text-end">
            <a href="<?= base_url('invoicelist') ?>" class="btn btn-secondary">Discard</a>
            <button type="submit" id="save-invoice-btn" class="btn btn-primary">Generate Invoice</button>
        </div>
    </form>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="customerForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <label>Name</label>
                    <input type="text" id="popup_name" class="form-control mb-2" required>
                    <label>Address</label>
                    <textarea id="popup_address" class="form-control" rows="3" required></textarea>
                    <div class="alert alert-danger d-none mt-2" id="customerError"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let initialFormData;
    $(document).ready(function () {
        $('#customer_id').select2({
            placeholder: "Select Customer",
            width: 'resolve'
        });
        initialFormData = $('#invoice-form').serialize();
        $('#save-invoice-btn').prop('disabled', true);
        $('#invoice-form input, #invoice-form select, #invoice-form textarea').on('input change', function () {
            const currentFormData = $('#invoice-form').serialize();
            const hasChanged = currentFormData !== initialFormData;
            $('#save-invoice-btn').prop('disabled', !hasChanged);
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
            let finalTotal = subtotal - (subtotal * discount / 100);
            $('#total_display').text(finalTotal.toFixed(2));
        }

        $('#add-item').click(function () {
            const row = `
            <tr class="item-row">
                <td><input type="text" name="description[]" class="form-control" placeholder="Description"></td>
                <td><input type="text" name="price[]" class="form-control price" step="0.01"></td>
                <td><input type="number" name="quantity[]" class="form-control quantity" step="0.01"></td>
                <td><input type="number" name="total[]" class="form-control total" step="0.01" readonly></td>
                <td class="text-center"><span class="remove-item-btn" title="Remove"><i class="fas fa-trash text-danger"></i></span></td>
            </tr>`;
            $('#item-container').append(row);

            // ✅ Recalculate totals
            calculateTotals();

            // ✅ Force trigger change tracking by binding again
            const currentFormData = $('#invoice-form').serialize();
            const hasChanged = currentFormData !== initialFormData;
            $('#save-invoice-btn').prop('disabled', !hasChanged);
        });
       $('#popup_name').on('input', function () {
            let value = $(this).val();
            let capitalized = value.replace(/\b\w/g, function (char) {
                return char.toUpperCase();
            });
            $(this).val(capitalized);
        });

       $('#popup_address').on('input', function () {
            let value = $(this).val();
            let capitalized = value.replace(/\b\w/g, function (char) {
                return char.toUpperCase();
            });

            $(this).val(capitalized);
        });


        document.getElementById('phone_number').addEventListener('input', function () {
            let val = this.value;
            // Allow + only at the beginning and remove all other non-digit characters
            if (val.charAt(0) === '+') {
                this.value = '+' + val.slice(1).replace(/[^0-9]/g, '');
            } else {
                this.value = val.replace(/[^0-9]/g, '');
            }
        });
        $(document).on('click', '.remove-item-btn', function () {
            $(this).closest('tr').remove();
            calculateTotals();

            const currentFormData = $('#invoice-form').serialize();
            const hasChanged = currentFormData !== initialFormData;
            $('#save-invoice-btn').prop('disabled', !hasChanged);
        });
        $(document).on('input', '.price, .quantity, #discount', calculateTotals);
        calculateTotals();

        $('#addCustomerBtn').click(function () {
            $('#popup_name').val('');
            $('#popup_address').val('');
            $('#customerModal').modal('show');
        });

        $('#customerForm').submit(function (e) {
            e.preventDefault();
            const name = $('#popup_name').val().trim();
            const address = $('#popup_address').val().trim();

            if (!name || !address) {
                $('#customerError').removeClass('d-none').text('Name and address are required.');
                return;
            }

            $.ajax({
                url: "<?= base_url('customer/create') ?>",
                type: "POST",
                data: { name, address },
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        const newOption = new Option(res.customer.name, res.customer.customer_id, true, true);
                        $('#customer_id').append(newOption).trigger('change');
                        $('#customerModal').modal('hide');
                    } else {
                        $('#customerError').removeClass('d-none').text(res.message || 'Error Adding Customer.');
                    }
                },
                error: function () {
                    $('#customerError').removeClass('d-none').text('Server error.');
                }
            });
        });

        $('#sameAddressCheck').on('change', function () {
            if ($(this).is(':checked')) {
                $('#shipping_address').val($('#customer_address').val()).prop('readonly', true);
            } else {
                $('#shipping_address').val('').prop('readonly', false);
            }
        });

        // Keep shipping updated when billing changes and checkbox is checked
        $('#customer_address').on('input', function () {
            if ($('#sameAddressCheck').is(':checked')) {
                $('#shipping_address').val($(this).val());
            }
        });



        $('#customer_id').on('change', function () {
            const customerId = $(this).val();
            $.post("<?= site_url('customer/get-address') ?>", { customer_id: customerId }, function (res) {
                if (res.status === 'success') {
                    $('#customer_address').val(res.address);
                } else {
                    $('#customer_address').val('');
                }
            }, 'json');
        });

        $('#invoice-form').submit(function (e) {
            e.preventDefault();
            const currentFormData = $('#invoice-form').serialize();
            if (currentFormData === initialFormData) {
                showAlert('No Changes Made.', 'info');
                return;
            }
            const $submitBtn = $('#save-invoice-btn');
            $submitBtn.prop('disabled', true).text('Generating...');
            const customerId = $('#customer_id').val();
            const customerName = $('#customer_id option:selected').text().trim();
            const billingAddress = $('#customer_address').val()?.trim();
            const shippingAddress = $('#shipping_address').val()?.trim();
            const phoneNumber = $('#phone_number').val()?.trim();

            if (!customerId || !billingAddress || !shippingAddress || !phoneNumber) {
                showAlert('Please Fill All Mandatory Fields.', 'danger');
                $submitBtn.prop('disabled', false).text('Generate Invoice');
                return;
            }

            // Ensure at least one valid item
            let validItemExists = false;
            $('.item-row').each(function () {
                const desc = $(this).find('input[name="description[]"]').val().trim();
                const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
                const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;

                if (desc && price > 0 && qty > 0) {
                    validItemExists = true;
                    return false;
                }
            });

            if (!validItemExists) {
                showAlert('Please Enter At Least One Valid Item With Description, Price, and Quantity.', 'danger');
                $submitBtn.prop('disabled', false).text('Generate Invoice');
                return;
            }

            // Remove empty rows
            $('.item-row').each(function () {
                const desc = $(this).find('input[name="description[]"]').val().trim();
                const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
                const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;

                if (!desc && price === 0 && qty === 0) {
                    $(this).remove();
                }
            });

            const formData = new FormData(this);
            $.ajax({
                url: "<?= site_url('invoice/save') ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        showAlert(res.message, 'success');
                        setTimeout(() => window.location.href = res.redirect, 1000);
                    } else {
                        showAlert(res.message || 'Failed to save invoice.', 'danger');
                        $submitBtn.prop('disabled', false).text('Generate Invoice');
                    }
                },

                error: function () {
                    showAlert('Server error while saving.', 'danger');
                    $submitBtn.prop('disabled', false).text('Generate Invoice');
                }
            });
        });

        function showAlert(message, type) {
            $('.alert')
                .removeClass('d-none alert-success alert-danger')
                .addClass('alert-' + type)
                .text(message)
                .fadeIn().delay(3000).fadeOut();
        }
    });
</script>