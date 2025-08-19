<?php include "common/header.php"; ?>

<div class="form-control mb-3 right_container">
    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h3 class="mb-0">Cash Receipt List</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('cashreceipt/create') ?>" class="btn btn-secondary">
                <i class="fa fa-plus"></i> Create Receipt
            </a>
        </div>
    </div>

    <table id="cashReceiptTable" class="table table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Sl No</th>
                <th>Customer</th>
                <th>Payment Date</th>
                <th>Amount</th>
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
    { data: 'amount' },
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
    { data: 'payment_mode' },
    { data: 'payment_id', visible: false },
    {
        data: 'payment_id',
        orderable: false,
        searchable: false,
        render: function(id) {
            return `
                <div class="btn-group">
                    <a href="<?= base_url('cashreceipt/view/') ?>${id}" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="<?= base_url('cashreceipt/edit/') ?>${id}" class="btn btn-sm btn-warning">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${id}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `;
        }
    }
]
    });

    // Delete button handler
    $(document).on("click", ".delete-btn", function () {
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
