<?php include "common/header.php";?>
<div class="form-control mb-3 right_container">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Expense List</h3> 
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('addexpense') ?>" class="btn btn-secondary">Add New Expense</a>
        </div>
    </div>
    <hr>
    <!-- <style>
    #expenseTable thead th {
        font-weight: bold;
    } -->
<!-- </style> -->

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
    lengthChange: true, // allows "Show entries" dropdown
    pageLength: 10,     // default rows per page
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
    dom: 'lfrtip' // IMPORTANT: enables length menu (l), filtering (f), table (t), pagination (p)
});


    // Delete handler
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
                        table.ajax.reload(); 
                    } else {
                        alert('Failed to delete expense.');
                    }
                },
                error: function () {
                    alert('Error deleting expense.');
                }
            });
        }
    });
});
</script>

<?php include "common/footer.php"; ?>
