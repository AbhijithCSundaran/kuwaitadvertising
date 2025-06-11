<?php include "common/header.php"; ?>

<div class="content-wrapper">
    <div class="container md-6">
        <div class="row mb-3 rol-mb3">
            <div class="col-md-6 role-con">
                <h3><?= isset($role['role_id']) ? 'Edit Role' : 'Role Configuration' ?></h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="<?= base_url('rolemanagement/rolelist') ?>" class="btn btn-secondary">Role List</a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('info')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert" id="infoAlert">
                <?= session()->getFlashdata('info') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Role form -->
        <form id="roleForm" action="<?= isset($role['role_id']) ? base_url('rolemanagement/update/' . $role['role_id']) : base_url('rolemanagement/store') ?>" method="post" class="p-3 border rounded bg-light">
            <div class="mb-3">
                <label for="role_name" class="form-label">Role Name:</label>
                <input type="text" name="role_name" class="form-control role-box" value="<?= isset($role['role_name']) ? esc($role['role_name']) : '' ?>" required>
            </div>

            <div class="card md-3">
                <div class="card-header">
                    <h5 class="mb-0">Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($menus as $menu): ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="access[<?= $menu ?>]" value="1" id="perm_<?= $menu ?>"
                                        <?= isset($access[$menu]) && $access[$menu] == 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm_<?= $menu ?>">
                                        <?= esc($menu) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button type="submit" id="saveBtn" class="btn mt-3 <?= isset($role['role_id']) ? 'btn-secondary' : 'btn-primary' ?>" 
                <?= isset($role['role_id']) ? 'disabled' : '' ?>>
                <?= isset($role['role_id']) ? 'Update Role' : 'Save Role' ?>
            </button>
        </form>
    </div>
</div>
</div>

<?php include "common/footer.php"; ?>

<!-- Flash alert auto-hide -->
<script>
    setTimeout(function () {
        ['successAlert', 'infoAlert', 'errorAlert'].forEach(function(id) {
            let alert = document.getElementById(id);
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 3000);
</script>

<?php if (isset($role['role_id'])): ?>
<!-- Enable save button only when form changes (Edit mode only) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('roleForm');
        const saveBtn = document.getElementById('saveBtn');

        const originalFormData = new FormData(form);
        const originalData = Object.fromEntries(originalFormData.entries());

        function isFormChanged() {
            const currentFormData = new FormData(form);
            for (let [key, value] of currentFormData.entries()) {
                if (originalData[key] !== value) return true;
            }

            // Check checkboxes
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            for (let checkbox of checkboxes) {
                const name = checkbox.name;
                const originallyChecked = originalFormData.get(name) === '1';
                if (checkbox.checked !== originallyChecked) return true;
            }

            return false;
        }

        function updateSaveButton() {
            if (isFormChanged()) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('btn-secondary');
                saveBtn.classList.add('btn-primary');
            } else {
                saveBtn.disabled = true;
                saveBtn.classList.remove('btn-primary');
                saveBtn.classList.add('btn-secondary');
            }
        }

        form.addEventListener('input', updateSaveButton);
        form.addEventListener('change', updateSaveButton);
    });
</script>
<?php endif; ?>
