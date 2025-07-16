<?php include "common/header.php"; ?>
<style>
    #customersTable.dataTable tbody td {
        font-size: 14px;
        vertical-align: middle;
    }
</style>

<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h3 class="mb-0">Manage Customers</h3>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-secondary" id="addCustomerBtn">Add New Customer</button>
        </div>
    </div>
    <hr>

    <table class="table table-bordered" id="customersTable" style="width:100%">
        <thead>
            <tr>
                <th class="d-none">ID</th>
                <th>Sl No</th>
                <th>Name</th>
                <th>Address</th>
                <th style="width: 100px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal: Add/Edit Customer -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="customerForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="customer_id">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" id="address" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Are You Sure You Want To Delete This Customer?</div>
            <div class="modal-footer">
                <button type="button" id="confirm-delete-btn" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
    let table;
    let deleteId = null;
    const alertBox = $('.alert');
    const customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

    $(document).ready(function () {
        // Load DataTable
        table = $('#customersTable').DataTable({
            ajax: {
                url: "<?= base_url('customer/fetch') ?>",
                type: "POST",
                dataSrc: "data"
            },
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
            columnDefs: [
                { targets: 0, visible: false },
                { targets: 1, orderable: false, width: "30px" },
                { targets: 2, width: "150px" },
                { targets: 3, width: "300px" },
                { targets: 4, orderable: false, width: "50px" }
            ],
            columns: [
                { data: "customer_id" },
                { data: "slno" },
                {
                    data: "name",
                    render: data => data ? data.replace(/\b\w/g, c => c.toUpperCase()) : ''
                },
                {
                    data: "address",
                    render: data => data ? data.replace(/\b\w/g, c => c.toUpperCase()) : '-N/A-'
                },
                {
                    data: "customer_id",
                    render: data => `
                        <div class="d-flex gap-2">
                            <a href="javascript:void(0);" class="view-estimate" data-id="${data}" title="View Estimates" style="color:green;">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="javascript:void(0);" class="edit-customer" data-id="${data}" title="Edit" style="color:rgb(13, 162, 199);">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="javascript:void(0);" class="delete-customer" data-id="${data}" title="Delete" style="color: #dc3545;">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>`
                }

            ]
        });

        // Show add form
        $('#addCustomerBtn').click(() => {
            $('#customerForm')[0].reset();
            $('#customer_id').val('');
            customerModal.show();
        });

        // Handle form submit (Add/Edit)
        $('#customerForm').submit(function (e) {
            e.preventDefault();
            $.post("<?= base_url('customer/create') ?>", $(this).serialize(), function (res) {
                if (res.status === 'success') {
                    showAlert('success', res.message);
                    table.ajax.reload(null, false);
                    customerModal.hide();
                } else {
                    showAlert('danger', res.message);
                }
            }, 'json');
        });

        // Edit click
        $(document).on('click', '.edit-customer', function () {
            const id = $(this).data('id');
            $.get("<?= base_url('customer/getCustomer/') ?>" + id, function (data) {
                if (data.status !== 'error') {
                    $('#customer_id').val(data.customer_id);
                    $('#name').val(data.name);
                    $('#address').val(data.address);
                    customerModal.show();
                } else {
                    showAlert('danger', data.message);
                }
            });
        });

        // View Estimate Click
        $(document).on('click', '.view-estimate', function () {
            const customerId = $(this).data('id');
            window.location.href = "<?= base_url('estimate/customer/') ?>" + customerId;
        });


        // Delete click
        $(document).on('click', '.delete-customer', function () {
            deleteId = $(this).data('id');
            deleteModal.show();
        });

        $('#confirm-delete-btn').click(function () {
            if (!deleteId) return;

            $.post("<?= base_url('customer/delete') ?>", {
                id: deleteId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }, function (res) {
                if (res.status === 'success') {
                    showAlert('success', res.message);
                    table.ajax.reload(null, false);
                } else {
                    showAlert('danger', res.message);
                }
                deleteModal.hide();
                deleteId = null;
            }, 'json');
        });

        function showAlert(type, message) {
            alertBox.removeClass().addClass(`alert alert-${type} text-center position-fixed`)
                .text(message).fadeIn();
            setTimeout(() => alertBox.fadeOut(), 2000);
        }
    });
</script>
