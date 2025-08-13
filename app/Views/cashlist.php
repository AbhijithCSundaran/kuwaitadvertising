<?php include "common/header.php"; ?>

<div class="form-control mb-3 right_container">
    <h3>Cash Receipt List</h3>
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
                        case 'advance': className = 'badge bg-primary'; break;
                        case 'partial': className = 'badge bg-warning'; break;
                        case 'full': className = 'badge bg-success'; break;
                        default: className = 'badge bg-secondary'; break;
                    }
                    return `<span class="${className}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'payment_mode' },
            { data: 'payment_id', visible: false }
        ],
        columnDefs: [
            { searchable: false, orderable: false, targets: 0 }
        ]
    });
});
</script>
