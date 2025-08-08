<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Manageuser_Model;
use App\Models\RoleModel;
use App\Models\Managecompany_Model;

class Manageuser extends BaseController
{
    public function __construct()
    {
       $session = \Config\Services::session();
        if (!$session->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }
public function index($uid = null)
{
    $session = session();
    $isEdit = !empty($uid);

    $userModel         = new Manageuser_Model();
    $roleModel         = new RoleModel();
    $managecompany_Model = new Managecompany_Model();

    $userData = $isEdit ? $userModel->find($uid) : [];
    $roles    = $roleModel->findAll();

    $loggedInCompanyId = $session->get('company_id');
    $loggedInRoleId    = $session->get('role_Id');

    if ($loggedInRoleId == 1) {
        $companies = $managecompany_Model->getActiveCompanies();

    } else {
        $companies = $managecompany_Model->where('company_id', $loggedInCompanyId)->findAll();
    }

    return view('adduser', [
        'uid'        => $uid,
        'isEdit'     => $isEdit,
        'userData'   => $userData,
        'roles'      => $roles,
        'companies'  => $companies,
        // 'loggedInRoleId'   => $loggedInRoleId
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
            'message' => 'Please Select A Role.'
        ]);
    }

    if (empty($companyId)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Please Select A Company.'
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
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    $roleId = $session->get('role_Id');
    $companyId = $session->get('company_id');

    $draw = $_POST['draw'] ?? 1;
    $fromstart = $_POST['start'] ?? 0;
    $tolimit = $_POST['length'] ?? 10;
    $orderDir = $_POST['order'][0]['dir'] ?? 'desc';
    $columnIndex = $_POST['order'][0]['column'] ?? 1;
    $search = $_POST['search']['value'] ?? '';
    $slno = $fromstart + 1;

    $columnMap = [
        0 => 'user_id',
        1 => 'name',
        2 => 'role_name',
        3 => 'email',
        4 => 'phonenumber',
        5 => 'user_id'
    ];
    $orderColumn = $columnMap[$columnIndex] ?? 'user_id';

  $condition = "1=1";
if ($roleId != 1) {
    $condition .= " AND user.company_id = " . (int)$companyId;
}


    // Search logic
    $search = trim(preg_replace('/\s+/', ' ', $search));
    if (!empty($search)) {
        $normalizedSearch = str_replace(' ', '', strtolower($search));
        $condition .= " AND (
            REPLACE(LOWER(user.name), ' ', '') LIKE '%$normalizedSearch%' OR
            REPLACE(LOWER(user.email), ' ', '') LIKE '%$normalizedSearch%' OR
            REPLACE(LOWER(user.phonenumber), ' ', '') LIKE '%$normalizedSearch%' OR
            REPLACE(LOWER(role_acces.role_name), ' ', '') LIKE '%$normalizedSearch%'
        )";
    }

    $userModel = new \App\Models\Manageuser_Model();
    $users = $userModel->getAllFilteredRecords($condition, $fromstart, $tolimit, $orderColumn, $orderDir,$roleId);

    $result = [];
    if (!empty($users)){
        foreach ($users as $user) {
        $result[] = [
            'slno'        => $slno++,
            'user_id'     => $user->user_id,
            'name'        => $user->name,
            'role_name'   => $user->role_name ?? '',
            'email'       => $user->email,
            'phonenumber' => $user->phonenumber
        ];
    }

    }
    
    $totalCondition = ($roleId == 1) ? "1=1" : "user.company_id = " . (int)$companyId;
    $total = $userModel->getAllUserCount($totalCondition)->totuser ?? 0;
    $filtered = $userModel->getFilterUserCount($condition);
    $filteredTotal = isset($filtered->totuser) ? (int) $filtered->totuser : 0;

    return $this->response->setJSON([
        'draw' => intval($draw),
        'recordsTotal' => $total,
        'recordsFiltered' => $filteredTotal,
        'data' => $result
    ]);
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

