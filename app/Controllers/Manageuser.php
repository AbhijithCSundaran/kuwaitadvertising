<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Manageuser_Model;
use App\Models\RoleModel;

class Manageuser extends BaseController
{
   public function index($uid = null){
        $isEdit = !empty($uid);

        $userModel = new Manageuser_Model();
        $roleModel = new RoleModel();
        $managecompany_Model = new \App\Models\Managecompany_Model();
        $userData = $isEdit ? $userModel->find($uid) : [];
        $roles    = $roleModel->findAll();
        $companies = $managecompany_Model->findAll();

        return view('adduser', [
            'uid'      => $uid,
            'isEdit'   => $isEdit,
            'userData' => $userData,
            'roles'    => $roles,
            'companies' => $companies
        ]);
    }
    public function add(){
        return view('adduserlist');
    }
public function save()
{
    $model   = new Manageuser_Model();
    $id      = $this->request->getPost('uid');
    $name    = trim($this->request->getPost('name'));
    $email   = trim($this->request->getPost('email'));
    $phone   = trim($this->request->getPost('phonenumber'));
    $pw      = trim($this->request->getPost('password'));
    $newPw   = trim($this->request->getPost('new_password'));
    $confPw  = trim($this->request->getPost('confirm_new_password'));
    $roleId  = $this->request->getPost('role_id');
    $companyId = $this->request->getPost('company_id');

    $isEdit = !empty($id);

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

    if (empty($companyId)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Please select a company.'
        ]);
    }
    $data = [
        'name'        => $name,
        'email'       => $email,
        'phonenumber' => $phone,
        'role_id'     => $roleId,
        'company_id'  => $companyId
    ];
    if (!$isEdit && $pw !== '') {
        $data['password'] = md5($pw);
    } elseif ($isEdit && $newPw !== '') {
        $data['password'] = md5($newPw);
    }

    if (!$isEdit) {
        $model->insert($data);
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'User Created Successfully.'
        ]);
    }
    $existing = $model->find($id);
    if (!$existing) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'User Not Found.'
        ]);
    }
    $unchanged = (
        $existing['name'] === $name &&
        $existing['email'] === $email &&
        $existing['phonenumber'] === $phone &&
        $existing['role_id'] == $roleId &&
        $existing['company_id'] == $companyId &&
        empty($newPw)
    );

    if ($unchanged) {
        return $this->response->setJSON([
            'status'  => 'info',
            'message' => 'No Changes Detected.'
        ]);
    }
    $model->update($id, $data);
    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'User Updated Successfully.'
    ]);
}


    public function userlistajax()
{
    $model = new \App\Models\Manageuser_Model();
    $menuModel = new \App\Models\RoleModel();

    $draw = $_POST['draw'];
    $fromstart = $_POST['start'];
    $tolimit = $_POST['length'];
    $order = $_POST['order'][0]['dir'] ?? 'desc';
    $search = $_POST['search']['value'];
    $slno = $fromstart + 1;

    $condition = "1=1";
    if ($search) {
        $condition .= " AND name LIKE '%" . trim($search) . "%' OR role_name like '%" . trim($search). "%' OR email LIKE '%" . trim($search) . "%' OR phonenumber LIKE '%" . trim($search) ."%'";
    }

    $totalRec = $model->getAllFilteredRecords($condition, $fromstart, $tolimit);
    $result = [];

    foreach ($totalRec as $user) {
        $permissions = $menuModel->where('role_id', $user->role_id)->findAll();
        $menuList = array_column($permissions, 'menu_name');
        $result[] = [
            'slno' => $slno++,
            'user_id' => $user->user_id,
            'name' => $user->name,
            'role_name' => $user->role_name ?? '',
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

   public function delete()
    {
        $user_id = $this->request->getPost('user_id');

        if (!$user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User ID is missing']);
        }

        $userModel = new Manageuser_Model();
        $userModel->delete($user_id);

        return $this->response->setJSON(['status' => 'success']);
    }
}

