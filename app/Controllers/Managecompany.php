<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Managecompany_Model;
use CodeIgniter\Exceptions\PageNotFoundException;

class Managecompany extends BaseController
{
    protected $companyModel;

    public function __construct()
    {
        $this->companyModel = new Managecompany_Model();
    }

    public function index()
    {
        return view('addcompany');
    }

    public function add($id = null)
    {
        $data = [];

        if ($id) {
            $company = $this->companyModel->find($id);
            if (!$company) {
                throw PageNotFoundException::forPageNotFound('Company not found.');
            }
            $data['company'] = $company;
        }

        return view('addcompany', $data);
    }

    public function save()
	{
		helper(['form']);
		$validation = \Config\Services::validation();

		$uid = $this->request->getPost('uid');
		$logoFile = $this->request->getFile('company_logo');

		$rules = [
			'company_name' => 'required',
			'address' => 'required',
			'tax_number' => 'permit_empty',
			'email' => 'required|valid_email',
			'phone' => 'required',
		];

		if (!$uid) {
			$rules['company_logo'] = 'uploaded[company_logo]|is_image[company_logo]|mime_in[company_logo,image/jpg,image/jpeg,image/png,image/gif]';
		} else {
			if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
				$rules['company_logo'] = 'is_image[company_logo]|mime_in[company_logo,image/jpg,image/jpeg,image/png,image/gif]';
			}
		}

		if (!$this->validate($rules)) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => $this->validator->getErrors()
			]);
		}

		$logoName = null;
		if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
			$logoName = $logoFile->getRandomName();
			$logoFile->move(ROOTPATH . 'public/uploads', $logoName);
		}

		$newData = [
			'company_name' => $this->request->getPost('company_name'),
			'address' => $this->request->getPost('address'),
			'tax_number' => $this->request->getPost('tax_number'),
			'email' => $this->request->getPost('email'),
			'phone' => $this->request->getPost('phone'),
			
		];

		if ($uid) {
			$existing = $this->companyModel->find($uid);
			if (!$existing) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Company not found for update.'
				]);
			}

			$duplicate = $this->companyModel
				->where('company_name', $newData['company_name'])
				->where('tax_number', $newData['tax_number'])
				->where('company_id !=', $uid)
				->first();

			if ($duplicate) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Another company with the same name and tax number already exists.'
				]);
			}

			// Handle logo and update
			if ($logoName) {
				$newData['company_logo'] = $logoName;
			} else {
				$newData['company_logo'] = $existing['company_logo'];
			}

			$hasChanges = (
				$newData['company_name'] !== $existing['company_name'] ||
				$newData['address'] !== $existing['address'] ||
				$newData['tax_number'] !== $existing['tax_number'] ||
				$newData['email'] !== $existing['email'] ||
				$newData['phone'] !== $existing['phone'] ||
				$newData['company_logo'] !== $existing['company_logo']
			);

			if (!$hasChanges) {
				return $this->response->setJSON([
					'status' => 'warning',
					'message' => 'No changes detected to update.'
				]);
			}

			// Delete 
			if ($logoName && !empty($existing['company_logo']) && file_exists(ROOTPATH . 'public/uploads/' . $existing['company_logo'])) {
				unlink(ROOTPATH . 'public/uploads/' . $existing['company_logo']);
			}

			$this->companyModel->update($uid, $newData);
			return $this->response->setJSON(['status' => 'success', 'message' => 'Company updated successfully']);
		} else {
			
			
			$existing = $this->companyModel
				->where('company_name', $newData['company_name'])
				->where('tax_number', $newData['tax_number'])
				->first();

			if ($existing) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Company with the same name and tax number already exists.'
				]);
			}

			if ($logoName) {
				$newData['company_logo'] = $logoName;
			}

			$this->companyModel->insert($newData);
			return $this->response->setJSON([
				'status' => 'success',
				'message' => 'Company added successfully'
			]);
		}
	}


    public function companyList()
    {
        $data['companies'] = $this->companyModel->findAll();
        return view('companylist', $data);
    }

    public function getCompany($id)
    {
        $company = $this->companyModel->find($id);
        return $this->response->setJSON($company);
    }

   public function delete()
{
    $id = $this->request->getPost('id');

    if (!$id) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No ID provided.'
        ]);
    }

    $companyModel = new \App\Models\Managecompany_Model();
    $company = $companyModel->find($id);

    if (!$company) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Company not found.'
        ]);
    }

    if ($companyModel->delete($id)) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Company deleted successfully.'
        ]);
    }

    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'Failed to delete company.'
    ]);
}

	public function companylistjson()
	{
		$companies = $this->companyModel->findAll();
		return $this->response->setJSON(['companies' => $companies]);
	}
	public function getAllCompanies()
	{
		$companies = $this->companyModel->findAll();
		return $this->response->setJSON($companies);
	}


}
