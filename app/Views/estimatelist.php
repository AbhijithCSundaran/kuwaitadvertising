<?php include "common/header.php"; ?>
<style>
#estimateTable td {
    white-space: normal !important;
    word-break: break-word;
    vertical-align: top;
}
</style>

<div class="form-control mb-3 right_container">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Estimate Directory</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('add_estimate') ?>" class="btn btn-secondary">Add New Estimate</a>
        </div>
    </div>
    <hr>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered" id="estimateTable">
                <thead>
                    <tr>
                        <th style="display:none;">ID</th> <!-- Hidden column for sorting -->
                        <th>SI</th>
                        <th>Customer Name</th>
                        <th>Customer Address</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Date</th>
                        <th>Description</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
$(document).ready(function () {
    var table = $('#estimateTable').DataTable({
        ajax: {
            url: "<?= base_url('estimate/estimatelistajax') ?>",
            type: "GET",
            dataSrc: "data"
        },
        paging: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        lengthMenu: [5, 10, 15, 20, 25],
        pageLength: 10,
        order: [[0, 'desc']], // Sort by hidden estimate_id
        columns: [
            { data: "estimate_id" }, // Hidden for sorting
            { data: null },          // SI
            { data: "customer_name" },
            { data: "customer_address" },
            {
                data: "total_amount",
                render: function (data) {
                    return parseFloat(data).toFixed(2) + ' KWD';
                }
            },
            {
                data: "discount",
                render: function (data) {
                    return parseFloat(data).toFixed(2);
                }
            },
            {
                data: "date",
                render: function (data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: "description",
                render: function (desc) {
                    if (!desc) return '-';
                    let items = desc.split(',').map(item => item.trim());
                    return items.map((item, i) => `${i + 1}. ${item}`).join('<br>');
                }
            },
            {
                data: "estimate_id",
                render: function (id) {
                    return `
                        <a href="<?= base_url('estimate/edit/') ?>${id}" class="btn btn-sm btn-primary-edit">Edit</a>
                        <button class="btn btn-sm btn-danger" onclick="deleteEstimate(${id})">Delete</button>
                    `;
                }
            }
        ],
        columnDefs: [
            { targets: 0, visible: false }, // Hide estimate_id
            { targets: 1, searchable: false, orderable: false }, // SI
            { width: "8%", targets: 5 },   // Discount
            { width: "12%", targets: 6 },  // Date
            { width: "22%", targets: 7 }   // Description
        ]
    });

    // SI number auto-fill
    table.on('order.dt search.dt draw.dt', function () {
        table.column(1, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });
});

// Delete estimate
function deleteEstimate(id) {
    if (confirm('Are you sure you want to delete this estimate?')) {
        $.ajax({
            url: "<?= base_url('estimate/delete') ?>",
            method: "POST",
            data: { estimate_id: id },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $('#estimateTable').DataTable().ajax.reload(null, false);
                } else {
                    alert('Failed to delete estimate.');
                }
            },
            error: function () {
                alert('Error occurred while deleting estimate.');
            }
        });
    }
}
</script>
