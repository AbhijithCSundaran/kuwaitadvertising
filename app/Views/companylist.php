<?php include "common/header.php"; ?>
<style>
    /* Reduce header height and padding */
    table.dataTable thead th {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        font-size: 16px;
        line-height: 1.2 !important;
        vertical-align: middle !important;
    }

    /* Optional: make body text consistent */
    table.dataTable tbody td {
        font-size: 14px;
        vertical-align: middle;
    }
</style>


<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h3 class="mb-0">Manage Companies</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('addcompany') ?>" class="btn btn-secondary">Add New Company</a>
        </div>
    </div>

    <hr>

    <table class="table table-bordered" id="companiesTable" style="width:100%">
        <thead>
            <tr>
                <th style="width: 50px;">Sl No</th>
                <th>Name</th>
                <th>Address</th>
                <th>Tax Number</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Logo</th>
                <th style="width: 100px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this company?</div>
            <div class="modal-footer">
                <button type="button" id="confirm-delete-btn" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
    let table = '';
    let deleteId = null;
    const alertBox = $('.alert');

    $(document).ready(function () {
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

        table = $('#companiesTable').DataTable({
            ajax: {
                url: "<?= base_url('managecompany/companylistjson') ?>",
                type: "POST",
                dataSrc: "data"
            },
            sort:true,
		    searching:true,
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
             columnDefs: [
                { targets: 0, width: "50px" },         
                { targets: 2, width: "350px" }, 
                { targets: 6, width: "50px" },       
                { targets: 7, width: "100px" }        
            ],
            columns: [

                { data: "slno" },
                {
                    data: "company_name",
                    render: data => data ? data.replace(/\b\w/g, c => c.toUpperCase()) : ''
                },
                {
                    data: "address",
                    render: data => data ? data.replace(/\b\w/g, c => c.toUpperCase()) : ''
                },
                {
                    data: "tax_number",
                    render: data => data && data.trim() !== "" ? data : '-N/A-'
                },
                { data: "email" },
                { data: "phone" },
                {
                    data: "company_logo",
                    render: data => data ? `<img src="<?= base_url('public/uploads/') ?>${data}" width="30">` : ''
                },
                {
                    data: "company_id",
                    render: data => `
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('addcompany/') ?>${data}" title="Edit" style="color:rgb(13, 162, 199); margin-right: 10px;">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="javascript:void(0);" class="delete-company" data-id="${data}" title="Delete" style="color: #dc3545;">
                            
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>`
                }
            ]
        });

        // Handle delete click
        $(document).on('click', '.delete-company', function () {
            deleteId = $(this).data('id');
            deleteModal.show();
        });

        // Confirm deletion
        $('#confirm-delete-btn').click(function () {
            if (!deleteId) return;

            $.post("<?= base_url('managecompany/delete') ?>", {
                id: deleteId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }, function (res) {
                if (res.status === 'success') {
                    alertBox.removeClass().addClass('alert alert-success text-center position-fixed')
                        .text(res.message).fadeIn();
                } else {
                    alertBox.removeClass().addClass('alert alert-danger text-center position-fixed')
                        .text(res.message).fadeIn();
                }
                setTimeout(() => alertBox.fadeOut(), 2000);
                table.ajax.reload(null, false);
            }).always(() => {
                deleteModal.hide();
                deleteId = null;
            });
        });
    });
</script>
