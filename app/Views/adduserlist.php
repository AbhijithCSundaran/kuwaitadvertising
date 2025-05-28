<?php include "common/header.php"; ?>
<div class="form-control">
    <div class="row">
        <div class="col-md-6">
            <h4>Manage Users List</h4>
        </div>
        <div class="col-md-6 text-right">
            <a href="<?= base_url('adduser') ?>"><button class="btn btn-secondary">Add New User</button></a>
        </div>
        <div class="col-md-12"><hr/></div>
    </div>

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
            <tbody>
                <!-- Filled by JavaScript -->
            </tbody>
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
                data.user.forEach(function(u, index) {
                    rows += `<tr>
                        <td>${index + 1}</td>
                        <td>${u.name}</td>
                        <td>${u.email}</td>
                        <td>********</td> 
						<td>${u.phonenumber}</td> 
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editUser(${u.user_id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.user_id})">Delete</button>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="6" class="text-center">No users found</td></tr>`;
            }
            $('#userTable tbody').html(rows);
        },
        error: function() {
            alert('Failed to load user data.');
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


