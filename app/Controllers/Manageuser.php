<?php
namespace App\Controllers;
use App\Models\Manageuser_Model;
use App\Controllers\BaseController;

class Manageuser extends BaseController {

    // public function index() {
        // return view('adduser');
    // }
	
	public function index($uid = null) {
		return view('adduser', ['uid' => $uid]);
	}



    public function add() {
        return view('adduserlist');
    }

    public function save() {
    $model = new Manageuser_Model();
    $data = [
        'name'        => $this->request->getPost('name'),
        'email'       => $this->request->getPost('email'),
        'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        'phonenumber' => $this->request->getPost('phonenumber'),
    ];

    $id = $this->request->getPost('uid'); 
    if ($id) {
        $model->update($id, $data);
        $msg = 'User updated successfully.';
    } else {
        $model->insert($data);
        $msg = 'User added successfully.';
    }

    return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
}
    public function getUser($id) {
        $model = new Manageuser_Model();
        return $this->response->setJSON($model->find($id));
    }

   public function userlist() {
    $model = new \App\Models\Manageuser_Model();
    return $this->response->setJSON(['user' => $model->findAll()]);
	}

	public function deleteuser() {
    $user_id = $this->request->getPost('user_id');
    if ($user_id) {
        $model = new \App\Models\Manageuser_Model();
        if ($model->delete($user_id)) {
            return $this->response->setJSON(['status' => 'success']);
        }
    }
    return $this->response->setJSON(['status' => 'error']);
	}

}
