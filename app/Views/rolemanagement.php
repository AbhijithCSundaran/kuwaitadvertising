<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Role Permissions Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; }
        h2 { margin-bottom: 20px; }
        label { font-weight: bold; }
        select, button { padding: 8px 12px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .message { margin: 10px 0; padding: 10px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<h2>Role Permissions Management</h2>

<?php if(session()->getFlashdata('success')): ?>
    <div class="message success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="message error"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form method="GET" action="">
    <label for="role_id">Select Role:</label>
    <select name="role_id" id="role_id" onchange="this.form.submit()">
        <option value="">-- Select Role --</option>
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role->role_id ?>" <?= ($role->role_id == $selected_role) ? 'selected' : '' ?>>
                <?= esc($role->role_name) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selected_role): ?>
<form method="POST" action="<?= site_url('rolemanagement/save') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="role_id" value="<?= $selected_role ?>">

    <table>
        <thead>
            <tr>
                <th>Menu Name</th>
                <th>Access</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menus as $menu): 
                $currentAccess = $permissions[$menu] ?? '1-denied'; // default denied
            ?>
            <tr>
                <td><?= esc($menu) ?></td>
                <td>
                    <label>
                        <input type="radio" name="access[<?= esc($menu) ?>]" value="0-allowed" <?= $currentAccess === '0-allowed' ? 'checked' : '' ?>>
                        Allowed
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="access[<?= esc($menu) ?>]" value="1-denied" <?= $currentAccess === '1-denied' ? 'checked' : '' ?>>
                        Denied
                    </label>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit">Save Permissions</button>
</form>
<?php endif; ?>

</body>
</html>
