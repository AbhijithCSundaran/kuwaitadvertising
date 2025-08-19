<?php include "common/header.php"; ?>

<div class="form-control mb-3 right_container">
    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h3 class="mb-0">Cash Receipt List</h3>
        </div>
    </div>

    <table id="cashReceiptTable" class="table table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Sl No</th>
                <th>Customer</th>
                <th>Payment Date</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance Amount</th>
                <th>Payment Status</th>
                <th>Payment Mode</th>
                <th class="d-none">ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('#cashReceiptTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('cashlist/ajax') ?>",
            type: "POST",
            dataSrc: 'data'
        },
        order: [[0, 'asc']],
        columns: [
            { data: 'slno' },
            { data: 'customer_name' },
            { data: 'payment_date' },
            { 
                data: 'amount', 
                render: function(amount) { return parseFloat(amount).toFixed(2); } 
            },
            { 
                data: 'paid_amount',
                render: function(amount) { return parseFloat(amount).toFixed(2); } 
            },
            { 
                data: 'balance_amount',
                render: function(amount) { return parseFloat(amount).toFixed(2); } 
            },
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
                    if (row.payment_status.toLowerCase() === 'unpaid') {
                        return '-N/A-'; // Show -N/A- if unpaid
                    }
                    switch(data) {
                        case 'cash': return 'Cash';
                        case 'bank_transfer': return 'Bank Transfer';
                        case 'bank_link': return 'Bank Link';
                        case 'wamd': return 'WAMD';
                        default: return data;
                    }
                }
            },
            { data: 'payment_id', visible: false },
            {
                data: 'payment_id',
                orderable: false,
                searchable: false,
                render: function(id, type, row) {
                    let printBtn = '';

                    if (row.payment_status.toLowerCase() === 'unpaid') {
                    printBtn = `<a href="javascript:void(0);"title="Unable to print unpaid receipt" style="color:gray;">
                                    <i class="bi bi-printer-fill"></i>
                                </a>`;
                    } else {
                       
                        if (row.payment_mode === 'cash') {
                            printBtn = `<a href="<?= base_url('cashreceipt/print/') ?>${id}" title="Print" style="color:green;">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>`;
                        } else if (['bank_transfer','bank_link','wamd'].includes(row.payment_mode)) {
                            printBtn = `<a href="<?= base_url('paymentreceipt/print/') ?>${id}" title="Print" style="color:green;">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>`;
                        }
                    }

                    return `
                        <div class="d-flex align-items-center gap-3">
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
    });

    // Delete button handler
    $(document).on("click", ".delete-cashreceipt", function () {
        let id = $(this).data("id");
        if (confirm("Are you sure you want to delete this receipt?")) {
            $.post("<?= base_url('cashreceipt/delete') ?>", { id: id }, function (res) {
                if (res.success) {
                    $('#cashReceiptTable').DataTable().ajax.reload();
                    alert("Receipt deleted successfully!");
                } else {
                    alert("Failed to delete receipt.");
                }
            }, 'json');
        }
    });
});
</script>
