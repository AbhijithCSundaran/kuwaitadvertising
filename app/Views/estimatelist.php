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
                    <th>Sub Total</th>
                    <th>Discount %</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Description</th> 
                    <th>Action</th>
                </tr>
            </thead>
                <tbody>
                </tbody>
            </table>
        </div>
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
        Are you sure you want to delete this estimate?
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelDeleteBtn">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
      
    </div>
  </div>
</div>
<?php include "common/footer.php"; ?>

<script>
    let table="";
$(document).ready(function () {
    $('#addCustomerBtn').on('click', function () {
        $('#addCustomerModal').modal('show');
        $('#customerForm')[0].reset();
        $('#customerError').addClass('d-none').text('');
    });

    $('#customerForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("customer/create") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    const newOption = new Option(res.customer.name, res.customer.customer_id, true, true);
                    $('#customer_id').append(newOption).trigger('change');
                    $('#addCustomerModal').modal('hide');
                } else {
                    $('#customerError').removeClass('d-none').text(res.message);
                }
            },
            error: function () {
                $('#customerError').removeClass('d-none').text('Server error, try again.');
            }
        });
    });
    table = $('#estimateTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "<?= base_url('estimate/estimatelistajax') ?>",
        type: "POST"
    },
    columns: [
        { data: "estimate_id", visible: false },
        { data: "slno" },
        {
            data: "customer_name",
            render: data => (data ?? '').replace(/\b\w/g, c => c.toUpperCase())
        },
        {
            data: "customer_address",
            className: "d-none d-xxl-table-cell",
            render: data => (data ?? '').replace(/\b\w/g, c => c.toUpperCase())
        },
        {
            data: "subtotal",
            render: data => `${data} KWD`
        },
        {
            data: "discount",
            render: data => `${parseFloat(data).toFixed(2)} %`
        },
        {
            data: "total_amount",
            render: data => `${data} KWD`
        },
        {
            data: "date",
            className: "d-none d-xxl-table-cell",
            render: data => new Date(data).toLocaleDateString('en-GB')
        },
        {
            data: "description",
            render: function (desc) {
                if (!desc) return '-';
                return desc.split(',').map((item, i) => `${i + 1}. ${item.trim().charAt(0).toUpperCase() + item.trim().slice(1)}`).join('<br>');
            }
        },
        {
            data: "estimate_id",
            render: function (id) {
                return `
                    <a href="<?= base_url('estimate/edit/') ?>${id}" title="Edit" style="color:rgb(13, 162, 199); margin-right: 10px;">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                    <a href="javascript:void(0);" class="delete-all" data-id="${id}" title="Delete" style="color: #dc3545;">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                `;
            }
        }
    ],
    order: [[0, 'desc']],
    lengthMenu: [5, 10, 15, 25],
    pageLength: 10,
    columnDefs: [
        { targets: 1, searchable: false, orderable: false },
        { targets: 9, orderable: false }
    ]
});

   let estimateIdToDelete = null;

        // Trigger the Bootstrap 5 modal
        $(document).on('click', '.delete-all', function () {
            estimateIdToDelete = $(this).data('id');
            const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        });

        // Delete confirmation button
        $('#confirm-delete-btn').on('click', function () {
            if (!estimateIdToDelete) return;

            $.ajax({
                url: "<?= base_url('estimate/delete') ?>",
                type: "POST",
                data: { estimate_id: estimateIdToDelete },
                dataType: "json",
                success: function (res) {
                    const deleteModalElement = document.getElementById('confirmDeleteModal');
                    const deleteModalInstance = bootstrap.Modal.getInstance(deleteModalElement);
                    deleteModalInstance.hide();

                    const alertBox = $('.alert');
                    if (res.status === 'success') {
                        alertBox.removeClass('d-none alert-success alert-warning alert-danger')
                                .addClass('alert-danger')
                                .text('Estimate Deleted Successfully')
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
                    const deleteModalElement = document.getElementById('confirmDeleteModal');
                    const deleteModalInstance = bootstrap.Modal.getInstance(deleteModalElement);
                    deleteModalInstance.hide();

                    const alertBox = $('.alert');
                    alertBox.removeClass('d-none alert-success alert-warning alert-danger')
                            .addClass('alert-danger')
                            .text('Error Deleting Estimate.')
                            .fadeIn();

                    setTimeout(() => {
                        alertBox.fadeOut(() => {
                            alertBox.addClass('d-none').text('');
                        });
                    }, 3000);
                }
            });

            estimateIdToDelete = null;
        });
});

</script>
