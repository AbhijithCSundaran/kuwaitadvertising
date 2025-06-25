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
                <th>Sl No</th>
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

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeDeleteModalBtn">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        Are you sure you want to delete this expense?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
         <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelDeleteBtn">Cancel</button>
      </div>

    </div>
  </div>
</div>
<?php include "common/footer.php"; ?>

<script>
let table="";
$(document).ready(function () {
     const alertBox = $('.alert');
     table = $('#expenseTable').DataTable({
        ajax: {
            url: "<?= base_url('expense/getExpensesAjax') ?>",
            type: "POST",
            dataSrc: "data"
        },
        sort:true,
        searching:true,
        paging:true,
        processing: true,
        serverSide: true,
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        columns: [
            {
                data: "slno",
                render: function (data) {
                    return data;
                }
            },
            { 
                data: "date" ,
            },
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
                         <a href="<?= base_url('addexpense/') ?>${data}" title="Edit" style="color:rgb(13, 162, 199); margin-right: 10px;">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="javascript:void(0);" class="delete-btn" data-id="${data}" title="Delete" style="color: #dc3545;">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    `;
                }
            }
        ],
        order: [[6, 'desc']],
            columnDefs: [
                { searchable: false, orderable: false, targets: [0, 4, 5] }
            ]
    });
    table.on('order.dt search.dt draw.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' })
            .nodes()
            .each(function (cell, i) {
                var pageInfo = table.page.info();
                cell.innerHTML = pageInfo.start + i + 1;
            });
    });
   let expenseIdToDelete = null;

$(document).on('click', '.delete-btn', function () {
    expenseIdToDelete = $(this).data('id');
    $('#deleteModal .modal-body').text('Are you sure you want to delete this expense?');
    $('#deleteModal').modal('show');
});
$('#cancelDeleteBtn, #closeDeleteModalBtn').on('click', function () {
    $('#deleteModal').modal('hide');
});

$('#confirmDeleteBtn').on('click', function () {
    if (!expenseIdToDelete) return;

    $.ajax({
        url: "<?= base_url('expense/delete') ?>",
        type: "POST",
        data: { expense_id: expenseIdToDelete },
        dataType: "json",
        success: function (res) {
            $('#deleteModal').modal('hide');
            const alertBox = $('.alert');

            if (res.status === 'success') {
                alertBox.removeClass('d-none alert-warning alert-danger')
                        .addClass('alert-success')
                        .text('Expense Deleted Successfully')
                        .fadeIn();

                setTimeout(() => {
                    alertBox.fadeOut(() => {
                        alertBox.addClass('d-none').text('');
                    });
                }, 2000);

                $('#expenseTable').DataTable().ajax.reload(null, false);
            } else {
                alertBox.removeClass('d-none alert-success alert-danger')
                        .addClass('alert-warning')
                        .text(res.message || 'Delete Failed.')
                        .fadeIn();

                setTimeout(() => {
                    alertBox.fadeOut(() => {
                        alertBox.addClass('d-none').text('');
                    });
                }, 3000);
            }
        },
        error: function () {
            $('#deleteModal').modal('hide');
            const alertBox = $('.alert');

            alertBox.removeClass('d-none alert-success alert-warning')
                    .addClass('alert-danger')
                    .text('Error Deleting Expense.')
                    .fadeIn();

            setTimeout(() => {
                alertBox.fadeOut(() => {
                    alertBox.addClass('d-none').text('');
                });
            }, 3000);
        }
    });
});

});
</script>
