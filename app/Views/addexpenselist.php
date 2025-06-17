<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container">
     <div class="alert d-none text-center position-fixed" role="alert"></div>
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Expense List</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('addexpense') ?>" class="btn btn-secondary">Add New Expense</a>
        </div>
    </div>
    <hr>
    <table class="table table-bordered" id="expenseTable">
        <thead>
            <tr>
                <th>SI NO</th>
                <th>Date</th>
                <th>Particular</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
$(document).ready(function () {
    const table = $('#expenseTable').DataTable({
        ajax: {
            url: "<?= base_url('expense/getExpensesAjax') ?>",
            type: "GET",
            dataSrc: "" 
        },
        columns: [
            { data: null }, 
            { data: "date" },
            {
                data: "particular",
                render: function (data) {
                    return data.replace(/\b\w/g, c => c.toUpperCase());
                }
            },

            { data: "amount" },
            {
                data: "payment_mode",
                render: function (data) {
                    return data.replace(/\b\w/g, c => c.toUpperCase());
                }
            },
            {
                data: "id",
                render: function (data) {
                    return `
                        <a href="<?= base_url('addexpense/') ?>${data}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-expense" data-id="${data}">Delete</button>
                    `;
                }
            }
        ],
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        lengthMenu: [5, 10, 25, 50],
        pageLength: 10,
        order: [[1, 'desc']],
        columnDefs: [
            { orderable: false, searchable: false, targets: [0, 5] }
        ]
    });
    table.on('order.dt search.dt draw.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });
    $('#expenseTable').on('click', '.delete-expense', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this expense?')) {
            $.ajax({
                url: "<?= base_url('expense/delete') ?>",
                type: "POST",
                data: { id: id },
                dataType: "json",
                success: function (res) {
                    const alertBox = $('.alert');
                    if (res.status === 'success') {
                        alertBox.removeClass('d-none').addClass('alert-danger')
                        .html('Deleted successfully').fadeIn();
                        setTimeout(() => {
                            alertBox.fadeOut(() => {
                                alertBox.addClass('d-none').removeClass('alert-success');
                            });
                        }, 2000);
                        table.ajax.reload(null, false);
                    } else {
                        alertBox.removeClass('d-none').html('Failed to delete expense.').fadeIn();
                        setTimeout(() => {
                            alertBox.fadeOut(() => {
                                alertBox.addClass('d-none');
                            });
                        }, 3000);
                    }
                },
                error: function () {
                    const alertBox = $('.alert');
                    alertBox.removeClass('d-none').html('Error deleting expense.').fadeIn();
                    setTimeout(() => {
                        alertBox.fadeOut(() => {
                            alertBox.addClass('d-none');
                        });
                    }, 3000);
                }
            });
        }
    });
});
</script>
