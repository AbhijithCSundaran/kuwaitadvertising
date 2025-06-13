<?php
namespace App\Controllers;

use App\Models\Manageuser_Model;
use App\Controllers\BaseController;

class Manageuser extends BaseController
{
    public function index($uid = null)
    {
        $isEdit = !empty($uid);
        $userData = null;

        if ($isEdit) {
            $model = new Manageuser_Model();
            $userData = $model->find($uid);
        }

        return view('adduser', [
            'uid'      => $uid,
            'isEdit'   => $isEdit,
            'userData' => $userData
        ]);
    }

    public function add()
    {
        return view('adduserlist');
    }

    public function save()
    {
        $model = new Manageuser_Model();

        $id       = $this->request->getPost('uid');
        $name     = trim($this->request->getPost('name'));
        $email    = trim($this->request->getPost('email'));
        $phone    = trim($this->request->getPost('phonenumber'));
        $password = trim($this->request->getPost('password'));

        // Basic validation
        if ($name === '' || $email === '' || (empty($id) && $password === '')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please fill all mandatory fields.'
            ]);
        }

        $data = [
            'name'        => $name,
            'email'       => $email,
            'phonenumber' => $phone
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!empty($id)) {
            $existingUser = $model->find($id);
            if (!$existingUser) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found.'
                ]);
            }

            $changes = array_diff_assoc($data, $existingUser);
            $hasChanges = !empty($changes) || isset($data['password']);


            $model->update($id, $data);
            $msg = 'User details updated successfully.';
        } else {
            $model->insert($data);
            $msg = 'User added successfully.';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $msg
        ]);
    }
// ajax integration
   public function userlistajax()
{
    $model = new \App\Models\Manageuser_Model();
    $data = $model->orderBy('user_id', 'DESC')->findAll();

    return $this->response->setJSON($data);
}

   public function delete($id)
    {
        $userModel = new \App\Models\Manageuser_Model();

        if ($userModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }


}
