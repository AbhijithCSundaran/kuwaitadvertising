<?php include "common/header.php";?>
<div class="form-control mb-3 right_container">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Expense List</h3> 
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('addexpense') ?>" class="btn btn-secondary">Add New Expense</a>
        </div>
        <div class="alert d-none w-25 mx-auto text-center fixed top mt-3" role="alert"></div>
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
</div>
<script>
$(document).ready(function () {
let table = $('#expenseTable').DataTable({
    processing: true,
    serverSide: false,
    lengthChange: true, 
    pageLength: 10,    
    ajax: {
        url: "<?= base_url('expense/list') ?>",
        type: "POST",
        dataSrc: 'data'
    },
    columns: [
        { data: null },
        { data: 'date' },
        { data: 'particular' },
        { data: 'amount' },
        { data: 'payment_mode' },
        {
            data: 'id',
            render: function (data, type, row) {
                return `
                    <a href="<?= base_url('addexpense/') ?>${data}" class="btn btn-sm btn-danger">Edit</a>
                    <button class="btn btn-sm btn-danger delete-expense" data-id="${data}">Delete</button>
                `;
            }
        }
    ],
    columnDefs: [
        {
            targets: 0,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        }
    ],
    dom: 'lfrtip' 
});
function showAlert(message, type = 'danger') {
    const alertBox = $('.alert');
    alertBox
        .removeClass('alert-success alert-danger alert-info')
        .addClass('alert-' + type)
        .html(message)
        .fadeIn();

    setTimeout(() => {
        alertBox.fadeOut();
    }, 3000);
}
    $(document).on('click', '.delete-expense', function () {
    const id = $(this).data('id');
    if (confirm('Are you sure you want to delete this expense?')) {
        $.ajax({
            url: "<?= base_url('expense/delete') ?>",
            method: "POST",
            data: { id: id },
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    showAlert('Deleted successfully', 'danger');
                    table.ajax.reload();
                } else {
                    showAlert('Failed to delete expense.', 'danger');
                }
            },
            error: function () {
                showAlert('Error deleting expense.', 'danger');
            }
        });
    }
});

});
</script>

<?php include "common/footer.php"; ?>
