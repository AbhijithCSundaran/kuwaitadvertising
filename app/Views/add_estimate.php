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
                        <select name="customer_id" id="customer_id" class="form-control select2">
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
                    <textarea name="customer_address" id="customer_address" class="form-control" rows="3"><?= isset($estimate['customer_address']) ? trim($estimate['customer_address']) : '' ?></textarea>
                    <div class="phone pt-3">
                        <label class="mt-md-0 mt-3"><strong>Contact Number</strong><span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control"
                        value="<?= isset($estimate['phone_number']) ? esc($estimate['phone_number']) : '' ?>"
                        minlength="7" maxlength="15" pattern="^\+?[0-9]{7,15}$"
                        title="Phone number must be 7 to 15 digits and can start with +" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="estimate-title">ESTIMATE</div>
                    <div class="estimate-details">
                        <p class="mb-1" id="estimate-id-display">Estimate No :
                            <?= isset($estimate['estimate_id']) ? $estimate['estimate_id'] : '' ?></p>
                        <p>Date : <?= date('d-m-Y') ?></p>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
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
                                    <td><input type="number" class="form-control price" name="price[]" value="<?= $item['price'] ?>">
                            </td>
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
            </div>
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
            <input type="hidden" id="estimate_id" value="<?= $estimate['estimate_id'] ?? '' ?>">

            <div class="text-right">
                <a href="<?= base_url('estimatelist') ?>" class="btn btn-secondary">Discard</a>
                <button type="submit" id="generate-btn" class="btn btn-primary">Generate Estimate</button>
                <button type="button" id="convert-invoice-btn" class="btn btn-primary">Convert Invoice</button>
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
            width: 'calc(100% - 40px)',
            minimumResultsForSearch: 0
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

        $('#convert-invoice-btn').on('click', function () {
            const estimateId = $('#estimate_id').val().trim(); // Ensure this hidden field is present
            const alertBox = $('.alert');

            if (estimateId) {
                window.open(`<?= base_url('invoice/print/') ?>${estimateId}`, '_blank');
            } else {
                alertBox
                    .removeClass('d-none alert-success')
                    .addClass('alert-danger')
                    .text('Please generate the estimate first before converting to invoice.');

                // Auto-hide after 3 seconds
                setTimeout(() => {
                    alertBox.addClass('d-none').removeClass('alert-danger').text('');
                }, 3000);
            }
        });

        $(document).on('input', 'input[name="description[]"]', function () {
            let value = $(this).val();
            let capitalized = value.replace(/\b\w/g, function (char) {
                return char.toUpperCase();
            });
            $(this).val(capitalized);
        });

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
            const newRow = $(` 
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
            $('#item-container').append(newRow);
            newRow.find('input[name="description[]"]').focus();
        });


        $(document).on('click', '.remove-item-btn', function () {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        $(document).on('input', '.price', function () {
            let input = this;
            let val = input.value;
            if (val === '' || val === '.') return;
            let match = val.match(/^(\d{0,8})(\.(\d{0,2})?)?/);
            if (match) {
                let newVal = (match[1] || '') + (match[2] || '');
                if (newVal !== val) {
                    input.value = newVal;
                    input.setSelectionRange(newVal.length, newVal.length);
                }
            } else {
                val = val.slice(0, -1);
                input.value = val;
                input.setSelectionRange(val.length, val.length);
            }
        });

        $(document).on('input', '.price, .quantity, #discount', calculateTotals);
        calculateTotals();
        

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
            let name = $('#popup_name').val().trim();
            let address = $('#popup_address').val().trim();
            name = name.replace(/\b\w/g, char => char.toUpperCase());
            address = address.replace(/(^\s*\w|[.!?]\s*\w)/g, char => char.toUpperCase());


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
       
let initialEstimateData = $('#estimate-form').serialize();


$('#generate-btn').prop('disabled', true);


$('#estimate-form').on('input change', 'input, select, textarea', function () {
    const currentData = $('#estimate-form').serialize();
    const hasChanged = currentData !== initialEstimateData;
    $('#generate-btn').prop('disabled', !hasChanged); 
});


function updateInitialFormState() {
    initialEstimateData = $('#estimate-form').serialize();
    $('#generate-btn').prop('disabled', true); 
}


        $('#estimate-form').submit(function (e) {
    e.preventDefault();

    const customerId = $('#customer_id').val();
    const customerAddress = $('#customer_address').val().trim();
    const customerName = $('#customer_id option:selected').text().trim();

    if (!customerId) {
        showAlert('Please Select A Customer.', 'danger');
        return;
    }

    if (!customerAddress) {
        showAlert('Please enter the customer address.', 'danger');
        return;
    }

  
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

    $('#generate-btn').prop('disabled', true).text('Generating...');

   
    const formData = new FormData(this);
    formData.append('customer_name', customerName);

    $.ajax({
        url: "<?= site_url('estimate/save') ?>",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {
    if (res.status === 'success') {
        showAlert(res.message, 'success');

        updateInitialFormState(); 

        setTimeout(function () {
            window.location.href = "<?= site_url('estimate/generateEstimate/') ?>" + res.estimate_id;
        }, 1500);
    } else if (res.status === 'nochange') {
        showAlert(res.message, 'warning');
        $('#generate-btn').prop('disabled', true).text('Generate Estimate');
    } else {
        showAlert(res.message || 'Failed To Save Estimate.', 'danger');
        $('#generate-btn').prop('disabled', false).text('Generate Estimate');
    }
},

        error: function () {
            showAlert('Something Went Wrong While Saving The Estimate.', 'danger');
            $('#generate-btn').prop('disabled', false).text('Generate Estimate');
        }
    });
});


function showAlert(message, type = 'success') {
    $('.alert')
        .removeClass('d-none alert-success alert-danger alert-warning')
        .addClass('alert-' + type)
        .text(message)
        .fadeIn()
        .delay(3000)
        .fadeOut();
}

    });

    $(window).on('keydown', function (e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            $('#generate-btn').trigger('click');
        }

        if (e.ctrlKey && e.key.toLowerCase() === 'f') {
            e.preventDefault();
            $('#add-item').trigger('click');
        }
    });
</script>


