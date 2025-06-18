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
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Permissions</th>
                <th>Action</th>
                <th class="d-none">ID</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
    const table="";
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
                    render: function (id, type, row, meta) {
                        const permissions = row.permissions || [];
                        if (permissions.length > 0) {
                            return '<ul class="mb-0">' + permissions.map(p => `<li>${p}</li>`).join('') + '</ul>';
                        }
                        return '<em>No permissions assigned</em>';
                    }
                },
                {
                    data: "role_id",
                    render: function (id) {
                        return `
                        <a href="<?= base_url('rolemanagement/edit/') ?>${id}" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger delete-role" data-id="${id}">Delete</button>
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
                .each((cell, i) => cell.innerHTML = i + 1);
        });


        $(document).on('click', '.delete-role', function () {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this role?')) {
                $.ajax({
                    url: "<?= base_url('rolemanagement/delete') ?>",
                    type: "POST",
                    data: {
                        role_id: id,
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
            }
        });
    });
</script>