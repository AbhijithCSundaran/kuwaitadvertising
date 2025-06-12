<?php include "common/header.php";?>

<div class="form-control mb-3 right_container">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">User Directory</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('adduser') ?>" class="btn btn-secondary">Add New User</a>
        </div>
        <div class="alert w-25 mx-auto text-center fixed top mt-3" role="alert" style="z-index: 1000; display: none;"  ></div>
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
</div>
<?php include "common/footer.php"; ?>
<script>
function loadUsers() {
    $.ajax({
        url: "<?= base_url('manageuser/userlist') ?>",
        method: "POST",
        dataType: "json",
        success: function(data) {
            let rows = '';

            if (data.user && data.user.length > 0) {
                data.user.forEach(function(user, index) {
                    rows += `<tr>
                        <td>${index + 1}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.phonenumber}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editUser(${user.user_id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.user_id})">Delete</button>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="6" class="text-center">No users found</td></tr>`;
            }

            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().clear().destroy();
            }

            $('#userTable tbody').html(rows);

            $('#userTable').DataTable({
                searching: true,
                lengthChange: true,
                ordering: true,
                pageLength: 10,
                dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>'
            });
        },
        error: function() {
            alert("Failed to load user data.");
        }
    });
}

function editUser(id) {
    window.location.href = "<?= base_url('adduser') ?>/" + id;
}

function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        $.ajax({
            url: '<?= base_url('manageuser/delete') ?>/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('.alert')
                        .removeClass('alert-success')
                        .addClass('alert-danger text-center')
                        .html('Deleted successfully')
                        .fadeIn();

                    setTimeout(() => {
                        $('.alert').fadeOut();
                        loadUsers(); // Refresh the table after delete
                    }, 2000);
                } else {
                    $('.alert')
                        .removeClass('alert-success')
                        .addClass('alert-danger text-center')
                        .html('Failed to delete user.')
                        .fadeIn();

                    setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
                }
            },
            error: function(xhr) {
                console.error("AJAX error:", xhr.responseText);
                $('.alert')
                    .removeClass('alert-success')
                    .addClass('alert-danger text-center')
                    .html('An error occurred while deleting.')
                    .fadeIn();

                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 3000);
            }
        });
    }
}
$(document).ready(function() {
    loadUsers();
});
</script>
