<?php
namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\Controller;

class Customer extends BaseController
{
    public function create()
    {
        $name = $this->request->getPost('name');
        $address = $this->request->getPost('address');

        if (empty($name) || empty($address)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Name and address are required'
            ]);
        }

        $model = new CustomerModel();
        $data = [
            'name' => $name,
            'address' => $address
        ];

        $id = $model->insert($data);

        if ($id) {
            $data['customer_id'] = $id;
            return $this->response->setJSON([
                'status' => 'success',
                'customer' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save customer.'
            ]);
        }
    }
    public function get_address()
    {
        $customer_id = $this->request->getPost('customer_id');
        $model = new CustomerModel();
        $customer = $model->find($customer_id);

        if ($customer) {
            return $this->response->setJSON(['status' => 'success', 'address' => $customer['address']]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Customer not found']);
        }
    }
    public function search()
{
    $term = $this->request->getGet('term');

    $model = new \App\Models\CustomerModel();
    $results = $model
        ->like('name', $term)
        ->select('customer_id, name, address')
        ->findAll(10);

    $data = [];
    foreach ($results as $row) {
        $data[] = [
            'id' => $row['customer_id'],
            'text' => $row['name'],
            'address' => $row['address']
        ];
    }

    return $this->response->setJSON($data);
}

}