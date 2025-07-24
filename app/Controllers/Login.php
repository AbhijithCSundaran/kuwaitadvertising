<?php
namespace App\Controllers;

use App\Models\Login_Model;
use App\Models\Rolemanagement_Model;
use App\Controllers\BaseController;

class Login extends BaseController
{
    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function authenticate()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if ($email && $password) {
            $loginModel = new Login_Model();
            $result = $loginModel->authenticateNow($email, $password);

            if ($result) {
                // Load the role-based menu permissions
                $roleMenuModel = new Rolemanagement_Model();
                $permissions = $roleMenuModel
                    ->where('role_id', $result->role_id)
                    ->where('access', 1)
                    ->findAll();

                $allowedMenus = array_column($permissions, 'menu_name');

                // Set session variables
                $this->session->set([
                    'user_Id'       => $result->user_id,
                    'user_Name'     => $result->name,
                    'role_Id'       => $result->role_id,
                    'allowed_menus' => $allowedMenus,
                    'status'        => 1,
                    'logged_in'     => true,
                    'company_id'    => $result->company_id 
                ]);

                return $this->response->setJSON([
                    'status'   => 1,
                    'user_Id'  => $result->user_id
                ]);
            } else {
                return $this->response->setJSON(['status' => 0, 'message' => 'Invalid credentials']);
            }
        } else {
            return $this->response->setJSON(['status' => 0, 'message' => 'Email and password are required']);
        }
    }
}
