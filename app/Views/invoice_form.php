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

<div class="mt-1 estimate-box right_container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3><?= isset($invoice['invoice_id']) ? 'Edit Invoice' : 'Create Invoice' ?></h3>
        </div>
    </div>

    <form id="invoice-form">
        <div class="row">
            <div class="col-md-6">
                <label><strong>Person Name</strong><span class="text-danger">*</span></label>
                <div class="input-group mb-2 d-flex">
                    <select name="customer_id" id="customer_id" class="form-control select2">
                        <option value="" disabled <?= !isset($invoice['customer_id']) ? 'selected' : '' ?>>Select Customer</option>
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
             <div class="col-md-6 text-end">
                <div class="estimate-title">INVOICE</div>
                <div class="estimate-details">
                    <p class="mb-1">Invoice No: <?= isset($invoice['invoice_id']) ? $invoice['invoice_id'] : 'Auto' ?></p>
                    <p>Date: <?= date('d-m-Y') ?></p>
                </div>
            </div>
            <div class="row">
                <!-- Billing Address -->
                <div class="col-md-6">
                    <label class="mt-3"><strong>Billing Address</strong><span class="text-danger">*</span></label>
                    <textarea name="customer_address" id="customer_address" class="form-control" rows="3"><?= isset($invoice['customer_address']) ? trim($invoice['customer_address']) : '' ?></textarea>
                </div>

                <!-- Shipping Address -->
                <div class="col-md-6">
                    <label class="mt-3"><strong>Shipping Address</strong><span class="text-danger">*</span></label>
                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3"><?= isset($invoice['shipping_address']) ? trim($invoice['shipping_address']) : '' ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="mt-3"><strong>LPO No</strong><span class="text-danger">*</span></label>
                    <input type="text" name="lpo_no" id="lpo_no" class="form-control" value="<?= isset($invoice['lpo_no']) ? esc($invoice['lpo_no']) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="mt-3"><strong>Phone Number</strong><span class="text-danger">*</span></label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?= isset($invoice['phone_number']) ? esc($invoice['phone_number']) : '' ?>" minlength="7" maxlength="15"  pattern="^\+?[0-9]{7,15}$" title="Phone number must be 7 to 15 digits and can start with +" />
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
                        <td><input type="text" name="description[]" class="form-control"  value="<?= esc($item['item_name']) ?>" ></td>
                        <td><input type="number" class="form-control price" name="price[]" value="<?= $item['price'] ?>"></td>
                        <td><input type="number" class="form-control quantity" name="quantity[]" value="<?= $item['quantity'] ?>"></td>
                        <td><input type="number" class="form-control total" name="total[]" step="0.01" value="<?= $item['total'] ?>" readonly></td>
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
                    <input type="number" name="discount" id="discount" class="form-control w-50 d-inline" value="<?= isset($invoice['discount']) ? $invoice['discount'] : '0' ?>" min="0"> %
                </td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td><strong><span id="total_display">0.00</span> KWD</strong></td>
            </tr>
        </table>

        <input type="hidden" name="invoice_id" value="<?= isset($invoice['invoice_id']) ? $invoice['invoice_id'] : '' ?>">

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
    
$(document).ready(function () {
    $('#customer_id').select2({
        placeholder: "Select Customer",
        width: 'resolve'
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
                <td><input type="number" name="price[]" class="form-control price" step="0.01"></td>
                <td><input type="number" name="quantity[]" class="form-control quantity" step="0.01"></td>
                <td><input type="number" name="total[]" class="form-control total" step="0.01" readonly></td>
                <td class="text-center"><span class="remove-item-btn" title="Remove"><i class="fas fa-trash text-danger"></i></span></td>
            </tr>`;
        $('#item-container').append(row);
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
                    $('#customerError').removeClass('d-none').text(res.message || 'Error adding customer.');
                }
            },
            error: function () {
                $('#customerError').removeClass('d-none').text('Server error.');
            }
        });
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
 
        const formData = new FormData(this);
        formData.append('customer_name', $('#customer_id option:selected').text().trim());
 
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
        setTimeout(() => window.location.href = res.redirect, 1000); // Redirect to print page
    } else {
        showAlert(res.message || 'Failed to save invoice.', 'danger');
    }
},
 
            error: function () {
                showAlert('Server error while saving.', 'danger');
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
