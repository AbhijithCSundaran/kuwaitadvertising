<?php
namespace App\Controllers;
use App\Models\Login_Model;
use App\Controllers\BaseController;


class Login extends BaseController{
	public function __construct(){
		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->Login_Model= new \App\Models\Login_Model();
	}

	public function authenticate(){
		$email = $this->request->getPost('email');
        $password = md5($this->request->getPost('password'));
		
		if($email && $password){
			$result=$this->Login_Model->authenticateNow($email,$password);
			$loginModel = new Login_Model();
            $result = $loginModel->authenticateNow($email, $password);
			if($result){
				echo json_encode(1);
			}
			else{
				echo json_encode(0);
			}
		}
		else{
			echo json_encode(0);
		}
	}
}

?>