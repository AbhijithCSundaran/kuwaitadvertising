<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Roles and Permissions</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('rolemanagement/create') ?>" class="btn btn-secondary">Add New Role</a>
        </div>
    </div>
    <hr>

    <table class="table table-bordered" id="roleTable" style="width:100%">
        <thead>
            <tr>
                <th>SI NO</th>
                <th>Role Name</th>
                <th>Permissions</th>
                <th style="width: 110px;">Created Date</th>
                <th  style="width: 110px;">Updated Date</th>
                <th style="width: 160px;">Action</th>
                <th class="d-none">ID</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
    let table="";
    $(document).ready(function () {
        const alertBox = $('.alert');
        table = $('#roleTable').DataTable({
            ajax: {
                url: "<?= base_url('rolemanagement/rolelistajax') ?>",
                type: "POST",
                dataSrc: "roles"
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
                    data: "role_name",
                    render: function (data, type, row) {
                        if (!data || typeof data !== 'string') return '';
                        return data.replace(/\b\w/g, c => c.toUpperCase());
                    }
                },

                {
                    data: "role_id",
                    render: function (id, type, row, meta) {
                        const permissions = row.permissions || [];
                        if (permissions.length > 0) {
                            return '<ul class="mb-0">' + permissions.map(p => `<li>${p}</li>`).join('') + '</ul>';
                        }
                        return '<em>No Permissions Assigned</em>';
                    }
                },
                {
                    data: "created_at",
                    render: function (data) {
                        const d = new Date(data);
                        return isNaN(d) ? '-' : d.toISOString().split('T')[0];
                    }
                },
                {
                    data: "updated_at",
                    render: function (data) {
                        const d = new Date(data);
                        return isNaN(d) ? '-' : d.toISOString().split('T')[0];
                    }
                },
                {
                    data: "role_id",
                    render: function (id) {
                        return `
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= base_url('rolemanagement/edit/') ?>${id}" title="Edit" style="color:rgb(13, 162, 199); margin-right: 10px;">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="javascript:void(0);" class="delete-all" data-id="${id}" title="Delete" style="color: #dc3545;">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    `;
                    }
                },
                { data: "role_id", visible: false }
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


        let deleteId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

        $(document).on('click', '.delete-all', function () {
            deleteId = $(this).data('id');
            deleteModal.show(); 
        });

        $('#confirm-delete-btn').on('click', function () {
            if (!deleteId) return;

            $.ajax({
                url: "<?= base_url('rolemanagement/delete') ?>",
                type: "POST",
                data: {
                    role_id: deleteId,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        alertBox.removeClass().addClass('alert alert-danger text-center position-fixed').text('Role deleted successfully.').fadeIn();
                        setTimeout(() => alertBox.fadeOut(), 2000);
                        table.ajax.reload(null, false);
                    } else {
                        alertBox.removeClass().addClass('alert alert-warning text-center position-fixed').text(res.message || 'Delete failed.').fadeIn();
                        setTimeout(() => alertBox.fadeOut(), 3000);
                    }
                },
                error: function () {
                    alertBox.removeClass().addClass('alert alert-danger text-center position-fixed').text('Error occurred while deleting role.').fadeIn();
                    setTimeout(() => alertBox.fadeOut(), 3000);
                }
            });

            deleteModal.hide(); 
            deleteId = null;   
        });
    });
</script>


