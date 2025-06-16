<?php include "common/header.php"; ?>
<div id="estimateDeleteAlert" class="alert text-center alert-fixed" style="display: none;"></div>

<style>
#estimateTable td {
    white-space: normal !important;
    word-break: break-word;
    vertical-align: top;
}
#estimateTable thead th {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
    line-height: 1.2 !important;
    vertical-align: middle !important;
    font-size: 14px;
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
    <div class="table-responsive">
        <table class="table table-bordered" id="estimateTable">
            <thead>
                <tr>
                    <th style="display:none;">ID</th> <!-- Hidden column for sorting -->
                    <th>SI NO</th>
                    <th>Customer Name</th>
                    <th style="width: 200px;">Customer Address</th>
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
            { data: "customer_address", className: "d-none d-xxl-table-cell" },
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
                data: "date", className: "d-none d-xxl-table-cell",
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
            { targets: 8, orderable: false }, // Disable sorting on Action column
            { width: "8%", targets: 2 },   // Discount
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

                // Show the success alert message
                $('#estimateDeleteAlert')
                    .removeClass()
                    .addClass('alert alert-danger text-center alert-fixed') // Red for delete
                    .text('Estimate deleted successfully!')
                    .fadeIn()
                    .delay(3000)
                    .fadeOut();
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
 