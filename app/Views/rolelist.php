<?php include "common/header.php";?>
<div id="roleDeleteAlert" class="alert text-center alert-fixed" style="display: none;"></div>

<div class="right_container">
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="mb-12 role-permission">Roles and Permissions</h3>
            </div>
            <div class="col-md-6 text-end p-2">
                <a href="<?= base_url('rolemanagement/create') ?>" class="btn btn-secondary">Add New Role</a>
            </div>
            <hr/>
        </div>
        <table class="table table-bordered" id="roleTable">
            <thead>
                <tr>
                    <th>SI NO</th>
                    <th style="width: 170px;">Role Name</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <th>Permissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>



<script>
function formatDate(dateStr) {
    const date = new Date(dateStr);
    if (isNaN(date)) return '-';
    return date.toISOString().split('T')[0]; 
}

function loadRoles() {
    $.ajax({
        url: "<?= base_url('rolemanagement/rolelistajax') ?>",
        method: "GET",
        dataType: "json",
        success: function(data) {
            let rows = '';
            if (data.roles && data.roles.length > 0) {
                data.roles.forEach(function(role, index) {
                    let perms = '';
                    if (data.rolePermissions && data.rolePermissions[role.role_id]) {
                        let filtered = data.rolePermissions[role.role_id].filter(p => p.access == 1);
                        if (filtered.length > 0) {
                            perms = '<ul class="mb-0">';
                            filtered.forEach(function(p) {
                                perms += `<li>${p.menu_name}</li>`;
                            });
                            perms += '</ul>';
                        } else {
                            perms = '<em>No permissions assigned</em>';
                        }
                    } else {
                        perms = '<em>No permissions assigned</em>';
                    }

                    rows += `<tr>
                        <td>${index + 1}</td>
                        <td>${role.role_name}</td>
                        <td>${formatDate(role.created_at)}</td>
                        <td>${formatDate(role.updated_at)}</td>
                        <td>${perms}</td>
                        <td>
                            <button class="btn btn-sm btn-primary-edit" onclick="editRole(${role.role_id})">Edit</button>
                            <button class="btn btn-danger btn-sm delete-role" data-id="${role.role_id}">Delete</button>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="6" class="text-center">No roles found</td></tr>`;
            }

            $('#roleTable tbody').html(rows);

            // Re-initialize DataTable
            if ($.fn.DataTable.isDataTable('#roleTable')) {
                $('#roleTable').DataTable().destroy();
            }
            $('#roleTable').DataTable({
                pageLength: 5,
                lengthChange: false,
                searching: true,
                ordering: false,
                info: false
            });

            $('.delete-role').off('click').on('click', function () {
                const id = $(this).data('id');
                deleteRole(id);
            });
        },
        error: function() {
            alert('Failed to load roles.');
        }
    });
}

function editRole(id) {
    window.location.href = "<?= base_url('rolemanagement/edit') ?>/" + id;
}

function deleteRole(id) 
{
    if (confirm('Are you sure you want to delete this role?')) {
        $.ajax({
            url: "<?= base_url('rolemanagement/delete') ?>",
            method: "POST",
            data: {
                role_id: id,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    const table = $('#roleTable').DataTable();
                    table.rows().every(function () {
                        const row = this.node();
                        if ($(row).find('.delete-role').data('id') === id) {
                            this.remove();
                        }
                    });
                    table.draw(false);
                    $('#roleDeleteAlert')
                        .removeClass()
                        .addClass('alert alert-danger text-center alert-fixed')
                        .text('Role successfully deleted.')
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();
                } else {
                    alert(response.message || 'Failed to delete role.');
                }
            },

            error: function() {
                alert('Error occurred while deleting role.');
            }
        });
    }
}



$(document).ready(function() {
    loadRoles();


    setTimeout(function () {
        $('#successAlert').fadeOut('slow', function () {
            $(this).remove();
        });
    }, 3000);
});
</script>

