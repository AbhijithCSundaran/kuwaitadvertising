<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">User Directory</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('adduser') ?>" class="btn btn-secondary">Add New User</a>
        </div>
        <div class="alert alert-danger d-none w-25 mx-auto text-center fixed top mt-3" role="alert"></div>
    </div>
    <hr>
    <table class="table table-bordered" id="userTable">
        <thead>
            <tr>
                <th>SI NO</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
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
    const table = $('#userTable').DataTable({
        ajax: {
            url: "<?= base_url('manageuser/userlistajax') ?>", // <- Use AJAX-specific method
            type: "GET",
            dataSrc: ""
        },
        columns: [
            { data: null },
            { data: "name" },
            { data: "email" },
            { data: "phonenumber" },
            {
                data: "user_id",
                render: function (data) {
                    return `
                        <a href="<?= base_url('adduser/') ?>${data}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-user" data-id="${data}">Delete</button>
                    `;
                }
            }
        ],
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        lengthMenu: [5, 10, 25, 50],
        pageLength: 10,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, searchable: false, targets: [0, 4] }
        ]
    });

    // Serial Number
    table.on('order.dt search.dt draw.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });

    // Delete
    $('#userTable').on('click', '.delete-user', function () {
        const id = $(this).data('id');
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: "<?= base_url('manageuser/delete') ?>/" + id,
                type: "POST",
                dataType: "json",
                success: function (res) {
                    const alertBox = $('.alert');
                    if (res.status === 'success') {
                        alertBox.removeClass('d-none').html('Deleted successfully').fadeIn();
                        setTimeout(() => {
                            alertBox.fadeOut(() => alertBox.addClass('d-none'));
                        }, 2000);
                        table.ajax.reload(null, false);
                    } else {
                        alertBox.removeClass('d-none').html('Failed to delete user.').fadeIn();
                        setTimeout(() => {
                            alertBox.fadeOut(() => alertBox.addClass('d-none'));
                        }, 3000);
                    }
                },
                error: function (xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    const alertBox = $('.alert');
                    alertBox.removeClass('d-none').html('An error occurred.').fadeIn();
                    setTimeout(() => {
                        alertBox.fadeOut(() => alertBox.addClass('d-none'));
                    }, 3000);
                }
            });
        }
    });
});
</script>
