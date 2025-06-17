<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Manageuser_Model;

class Manageuser extends BaseController
{
    // Render form (add/edit user)
    public function index($uid = null)
    {
        $isEdit   = !empty($uid);
        $userData = $isEdit ? (new Manageuser_Model())->find($uid) : null;

        return view('adduser', [
            'uid'      => $uid,
            'isEdit'   => $isEdit,
            'userData' => $userData
        ]);
    }

    // Render listing page
    public function add()
    {
        return view('adduserlist');
    }

    // Save user (create or update)
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

        $isEdit = !empty($id);

        // Validate required fields
        if ($name === '' || $email === '' || (!$isEdit && $pw === '')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Please fill all mandatory fields.'
            ]);
        }

        // Optional: Email format validation (uncomment if needed)
        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     return $this->response->setJSON([
        //         'status'  => 'error',
        //         'message' => 'Invalid email format.'
        //     ]);
        // }

        // In edit mode, validate new password (if provided)
        if ($isEdit && ($newPw !== '' || $confPw !== '')) {
            if (strlen($newPw) < 6 || strlen($newPw) > 15) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'New password must be between 6 and 15 characters.'
                ]);
            }

            if ($newPw !== $confPw) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'New password and confirm password do not match.'
                ]);
            }
        }

        // Build data array
        $data = [
            'name'        => $name,
            'email'       => $email,
            'phonenumber' => $phone
        ];

        // Password logic
        if (!$isEdit && $pw !== '') {
            $data['password'] = password_hash($pw, PASSWORD_DEFAULT);
        } elseif ($isEdit && $newPw !== '') {
            $data['password'] = password_hash($newPw, PASSWORD_DEFAULT);
        }

        // Save to DB
        if (!$isEdit) {
            $model->insert($data);
            $message = 'User added successfully.';
        } else {
            $existing = $model->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'User not found.'
                ]);
            }

            $model->update($id, $data);
            $message = 'User details updated successfully.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    // List users via AJAX
    public function userlistajax()
    {
        $model = new Manageuser_Model();
        $data  = $model->orderBy('user_id', 'DESC')->findAll();

        return $this->response->setJSON($data);
    }

    // Delete a user
    public function delete($id)
    {
        $model = new Manageuser_Model();
        $ok    = $model->delete($id);

        return $this->response->setJSON([
            'status'  => $ok ? 'success' : 'error',
            'message' => $ok ? 'User deleted successfully.' : 'Failed to delete user.'
        ]);
    }
}
