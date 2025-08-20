<?php include "common/header.php"; ?>
<style>
    #cashReceiptTable.dataTable tbody td {
        font-size: 14px;
        vertical-align: middle;
    }
</style>

<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h3 class="mb-0">Cash Receipt List</h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="cashReceiptTable" style="width:100%">
            <thead>
                <tr>
                    <th class="d-none">ID</th>
                    <th>Sl No</th>
                    <th>Customer</th>
                    <th>Payment Date</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Balance Amount</th>
                    <th>Payment Status</th>
                    <th>Payment Mode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Are You Sure You Want To Delete This Cash Receipt?</div>
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
let table = '';
let deleteId = null;
const alertBox = $('.alert');

$(document).ready(function () {
    const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

    table = $('#cashReceiptTable').DataTable({
        ajax: {
            url: "<?= base_url('cashreceipt/ajaxListJson') ?>",
            type: "POST",
            dataSrc: "data"
        },
        processing: true,
        serverSide: true,
        order: [[3, 'desc']],
        columnDefs: [
        { targets: 0, visible: false }, // Hidden ID
        { targets: 1, orderable: false, width: "30px" }, // Sl No - No Sorting
        { targets: [4, 5, 6], orderable: false } // Disable sorting for columns 4, 5, 6
    ],
        columns: [
            { data: 'payment_id' }, // hidden
            { data: 'slno' },
            { data: 'customer_name' },
            { data: 'payment_date' },
            { data: 'amount', render: data => parseFloat(data).toFixed(2) },
            { data: 'paid_amount', render: data => parseFloat(data).toFixed(2) },
            { data: 'balance_amount', render: data => parseFloat(data).toFixed(2) },
            { 
                data: 'payment_status',
                render: function(data) {
                    if (!data) return '';
                    let className = '';
                    switch(data.toLowerCase()) {
                        case 'paid': className = 'badge bg-success'; break;
                        case 'unpaid': className = 'badge bg-danger'; break;
                        case 'partial paid': className = 'badge bg-warning'; break;
                        default: className = 'badge bg-secondary'; break;
                    }
                    return `<span class="${className}">${data}</span>`;
                }
            },
            {
                data: 'payment_mode',
                render: function(data, type, row) {
                    if (row.payment_status.toLowerCase() === 'unpaid') return '-N/A-';
                    switch(data) {
                        case 'cash': return 'Cash';
                        case 'bank_transfer': return 'Bank Transfer';
                        case 'bank_link': return 'Bank Link';
                        case 'wamd': return 'WAMD';
                        default: return data;
                    }
                }
            },
            {
                data: 'payment_id',
                orderable: false,
                searchable: false,
                render: function(id, type, row) {
                    let printBtn = '';
                    if (row.payment_status.toLowerCase() !== 'unpaid') {
                        if (row.payment_mode === 'cash') {
                            printBtn = `<a href="<?= base_url('cashreceipt/print/') ?>${id}" title="Print" style="color:green;">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>`;
                        } else {
                          printBtn = `<a href="<?= base_url('paymentvoucher/print/') ?>${id}" title="Print" style="color:green;">
                <i class="bi bi-printer-fill"></i>
            </a>`;
                        }
                    } else {
                        printBtn = `<a href="javascript:void(0);" title="Unable to print unpaid receipt" style="color:gray;">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>`;
                    }

                    return `
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('cashreceipt/view/') ?>${id}" title="View" style="color: rgb(13, 162, 199);">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            ${printBtn}
                            <a href="javascript:void(0);" class="delete-cashreceipt" data-id="${id}" title="Delete" style="color: #dc3545;">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ],
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>"
    });

    // Delete handler
    $(document).on('click', '.delete-cashreceipt', function () {
        deleteId = $(this).data('id');
        deleteModal.show();
    });

    $('#confirm-delete-btn').click(function () {
        if (!deleteId) return;

        $.post("<?= base_url('cashreceipt/delete') ?>", { id: deleteId }, function(res) {
            if (res.status === 'success') {
                alertBox.removeClass().addClass('alert alert-success text-center position-fixed')
                        .text(res.message).fadeIn();
            } else {
                alertBox.removeClass().addClass('alert alert-danger text-center position-fixed')
                        .text(res.message).fadeIn();
            }
            setTimeout(() => alertBox.fadeOut(), 2000);
            table.ajax.reload(null, false);
        }, 'json').always(() => {
            deleteModal.hide();
            deleteId = null;
        });
    });
});
</script>
