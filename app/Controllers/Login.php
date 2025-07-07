<?php
namespace App\Controllers;

use App\Models\Login_Model;
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
                $this->session->set([
                    'user_Id'    => $result->user_id,
                    'user_Name'  => $result->name,
                    'status'     => 1,
                    'logged_in'  => true
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
