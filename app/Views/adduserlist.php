<?php include "common/header.php"; ?>

<div class="form-control mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">User Directory</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('adduser') ?>" class="btn btn-secondary">Add New User</a>
        </div>
    </div>
    <hr>
    <div class="card">
        <table class="table table-bordered" id="userTable">
            <thead>
                <tr>
                    <th>SI</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Phone Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id ="userTable tbody"></tbody>
        </table>
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
                data.user.forEach(function(data, index) {
                    rows += `<tr>
                        <td>${index + 1}</td>
                        <td>${data.name}</td>
                        <td>${data.email}</td>
                        <td>********</td>
                        <td>${data.phonenumber}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editUser(${data.user_id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${data.user_id})">Delete</button>
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
            url: "<?= base_url('manageuser/deleteuser') ?>",
            method: "POST",
            data: { user_id: id },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#userTable').DataTable();//destroy();
                    loadUsers();
                } else {
                    alert('Failed to delete user.');
                }
            },
            error: function() {
                alert('Error occurred while deleting user.');
            }
            
        });
    }
}

$(document).ready(function() {
    loadUsers();
});
</script>
