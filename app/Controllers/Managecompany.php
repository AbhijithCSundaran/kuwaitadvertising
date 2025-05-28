<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Managecompany_Model;

class Managecompany extends BaseController
{
    public function __construct()
    {
        $this->Managecompany_Model = new Managecompany_Model();
    }

     public function index()
    {
        return view('addcompany');  
    }
	public function add()
	{
		return view('addcompany');
	}

     public function save()
    {
         $companyModel = new Managecompany_Model();

         $logoFile = $this->request->getFile('company_logo');
         $logoName = null;

         if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
             $logoName = $logoFile->getRandomName();
             $logoFile->move(ROOTPATH . 'public/uploads', $logoName);
         }

         $data = [
             'company_name'  => $this->request->getPost('company_name'),
             'address'     => $this->request->getPost('address'),
             'tax_number'  => $this->request->getPost('tax_number'),
         ];

         if ($logoName) {
             $data['company_logo'] = $logoName;
         }

         $uid = $this->request->getPost('uid');

         if ($uid) {
             $companyModel->update($uid, $data);
             return $this->response->setJSON(['status' => 'success', 'message' => 'Company updated successfully']);
         } else {
             $companyModel->insert($data);
             return $this->response->setJSON(['status' => 'success', 'message' => 'Company added successfully']);
         }
     }
	 public function companyList()
	{
		$data['companies'] = $this->Managecompany_Model->findAll();
		return view('companylist', $data);
	}
	
	public function getCompany($id)
	{
		$company = $this->Managecompany_Model->find($id);
		return $this->response->setJSON($company);
	}
	
	public function delete($id)
	{
		$model = new \App\Models\Managecompany_Model();
			if ($model->delete($id)) {
				return $this->response->setJSON(['status' => 'success']);
			} else {
				return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete company']);
			}
	}

}
