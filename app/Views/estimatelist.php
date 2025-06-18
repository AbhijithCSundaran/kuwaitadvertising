<?php include "common/header.php"; ?>
<div class="alert d-none position-fixed" role=alert ></div>
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
label {
    display: inline-block;
    padding-bottom: 11px;
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
                    <th style="display:none;">ID</th> 
                    <th>SI NO</th>
                    <th>Customer Name</th>
                    <th style="width: 200px;">Customer Address</th>
                    <th>Total Amount</th>
                    <th>Discount %</th>
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
        order: [[0, 'desc']],
        columns: [
            { data: "estimate_id" },
            { data: null },
            { data: "customer_name",
                render: function (data, type, row) {
                    if (!data || typeof data !== 'string') return '';
                    return data.replace(/\b\w/g, c => c.toUpperCase());
                }
            },
            { data: "customer_address", className: "d-none d-xxl-table-cell",
                render: function (data, type, row) {
                    if (!data || typeof data !== 'string') return '';
                    return data.replace(/\b\w/g, c => c.toUpperCase());
                }
             },
            {
                data: "total_amount",
                render: data => parseFloat(data).toFixed(2) + ' KWD'
            },
            {
                data: "discount",
                render: data => parseFloat(data).toFixed(2)
            },
            {
                data: "date", className: "d-none d-xxl-table-cell",
                render: data => new Date(data).toLocaleDateString()
            },
            {
                data: "description",
                render: function (desc) {
                    if (!desc || typeof desc !== 'string') return '-';
                    let items = desc.split(',').map(item => item.trim());
                    return items.map((item, i) => `${i + 1}. ${item.charAt(0).toUpperCase() + item.slice(1)}`).join('<br>');
                }
            },
            {
                data: "estimate_id",
                render: function (id) {
                    return `
                        <a href="<?= base_url('estimate/edit/') ?>${id}" class="btn btn-sm btn-primary-edit">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${id}">Delete</button>
                    `;
                }
            }
        ],
        columnDefs: [
            { targets: 0, visible: false },
            { targets: 1, searchable: false, orderable: false },
            { targets: 8, orderable: false },
            { width: "8%", targets: 2 },
            { width: "12%", targets: 6 },
            { width: "22%", targets: 7 }
        ]
    });

    table.on('order.dt search.dt draw.dt', function () {
        table.column(1, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });

    
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        if (confirm('Are you sure you want to delete this estimate?')) {
            $.ajax({
                url: "<?= base_url('estimate/delete') ?>",
                type: "POST",
                data: { estimate_id: id },
                dataType: "json",
                success: function (res) {
                    const alertBox = $('.alert');
                    if (res.status === 'success') {
                        alertBox.removeClass('d-none alert-success alert-warning alert-danger')
                                .addClass('alert-danger')
                                .text('Estimate deleted successfully!')
                                .fadeIn();

                        setTimeout(() => {
                            alertBox.fadeOut(() => {
                                alertBox.addClass('d-none').text('');
                            });
                        }, 2000);

                        table.ajax.reload(null, false);
                    } else {
                        alertBox.removeClass('d-none alert-success alert-warning alert-danger')
                                .addClass('alert-warning')
                                .text(res.message || 'Delete failed.')
                                .fadeIn();

                        setTimeout(() => {
                            alertBox.fadeOut(() => {
                                alertBox.addClass('d-none').text('');
                            });
                        }, 3000);
                    }
                },
                error: function () {
                    const alertBox = $('.alert');
                    alertBox.removeClass('d-none alert-success alert-warning alert-danger')
                            .addClass('alert-danger')
                            .text('Error deleting estimate.')
                            .fadeIn();

                    setTimeout(() => {
                        alertBox.fadeOut(() => {
                            alertBox.addClass('d-none').text('');
                        });
                    }, 3000);
                }
            });
        }
    });
});
</script>
