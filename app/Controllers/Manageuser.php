<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Manageuser_Model;
use App\Models\RoleModel;

class Manageuser extends BaseController
{
    // Render form (add/edit user)
   public function index($uid = null){
    $isEdit = !empty($uid);

    $userModel = new Manageuser_Model();
    $roleModel = new RoleModel();

   $userData = $isEdit ? $userModel->find($uid) : [];
    $roles    = $roleModel->findAll();

    return view('adduser', [
        'uid'      => $uid,
        'isEdit'   => $isEdit,
        'userData' => $userData,
        'roles'    => $roles
    ]);
}

    // Render listing page
    public function add(){
        return view('adduserlist');
    }

    // Save user (create or update)
   public function save(){
    $model   = new Manageuser_Model();
    $id      = $this->request->getPost('uid');
    $name    = trim($this->request->getPost('name'));
    $email   = trim($this->request->getPost('email'));
    $phone   = trim($this->request->getPost('phonenumber'));
    $pw      = trim($this->request->getPost('password'));
    $newPw   = trim($this->request->getPost('new_password'));
    $confPw  = trim($this->request->getPost('confirm_new_password'));
    $roleId  = $this->request->getPost('role_id');

    $isEdit = !empty($id);

    // Validation
    if ($name === '' || $email === '' || (!$isEdit && $pw === '')) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Please Fill All Mandatory Fields.'
        ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Invalid email address.'
        ]);
    }

    if ($isEdit && ($newPw !== '' || $confPw !== '')) {
        if (strlen($newPw) < 6 || strlen($newPw) > 15) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'New Password Must Be Between 6 And 15 Characters.'
            ]);
        }

        if ($newPw !== $confPw) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'New Password And Confirm Password Do Not Match.'
            ]);
        }
    }

    if (empty($roleId)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Please select a role.'
        ]);
    }

    // Form data
    $data = [
        'name'        => $name,
        'email'       => $email,
        'phonenumber' => $phone,
        'role_id'     => $roleId
    ];

    if (!$isEdit && $pw !== '') {
        $data['password'] = password_hash($pw, PASSWORD_DEFAULT);
    } elseif ($isEdit && $newPw !== '') {
        $data['password'] = password_hash($newPw, PASSWORD_DEFAULT);
    }

    if (!$isEdit) {
        $model->insert($data);
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'User Created Successfully.'
        ]);
    } else {
        $existing = $model->find($id);
        if (!$existing) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'User not found.'
            ]);
        }

        // Compare fields (excluding password)
        $unchanged = (
            $existing['name'] === $name &&
            $existing['email'] === $email &&
            $existing['phonenumber'] === $phone &&
            $existing['role_id'] == $roleId &&
            empty($newPw)
        );

        if ($unchanged) {
            return $this->response->setJSON([
                'status'  => 'info',
                'message' => 'No changes detected.'
            ]);
        }

        $model->update($id, $data);
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'User Details Updated Successfully.'
        ]);
    }
}

    public function userlistajax(){
        $model = new \App\Models\Manageuser_Model();

    $draw = $_POST['draw'];
    $fromstart = $_POST['start'];
    $tolimit = $_POST['length'];
    $order = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
    $search = $_POST['search']['value'];
    $slno = $fromstart + 1;

    $condition = "1=1";
    if ($search) {
        $condition .= " AND name LIKE '%" . trim($search) . "%'";
    }

    $totalRec = $model->getAllFilteredRecords($condition, $fromstart, $tolimit);

    $result = [];
    foreach ($totalRec as $user) {
        $result[] = [
            'slno' => $slno++,
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'phonenumber' => $user->phonenumber
        ];
    }

    $totUserCount = $model->getAllUserCount();
    $totFilterCounts = $model->getFilterUserCount($condition, $fromstart, $tolimit);

    $response = [
        "draw" => intval($draw),
        "iTotalRecords" => $totUserCount->totuser ?? 0,
        "iTotalDisplayRecords" => $totFilterCounts->filRecords ?? 0,
        "data" => $result
    ];

    return $this->response->setJSON($response);
}


    // Delete a user
   public function delete()
{
    $user_id = $this->request->getPost('user_id');

    if (!$user_id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'User ID is missing']);
    }

    $userModel = new \App\Models\Manageuser_Model();
    $userModel->delete($user_id);

    return $this->response->setJSON(['status' => 'success']);
}

}
