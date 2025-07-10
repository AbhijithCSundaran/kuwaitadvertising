<?php
namespace App\Controllers;

use App\Models\customerModel;
use CodeIgniter\Controller;

class Customer extends BaseController
{
    public function __construct()
    {
       $session = \Config\Services::session();
        if (!$session->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }
   public function create()
{
    $name = $this->request->getPost('name');
    $address = $this->request->getPost('address');
    $customer_id = $this->request->getPost('customer_id'); // NEW LINE

    if (empty($name) || empty($address)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Name and address are required'
        ]);
    }

    $model = new customerModel();
    $data = [
        'name' => $name,
        'address' => $address
    ];

    // ✅ Check if it's an update or create
    if (!empty($customer_id)) {
        $updated = $model->update($customer_id, $data);

        if ($updated) {
            $data['customer_id'] = $customer_id;
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Customer updated successfully',
                'customer' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update customer.'
            ]);
        }

    } else {
        // Insert new customer
        $id = $model->insert($data);

        if ($id) {
            $data['customer_id'] = $id;
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Customer created successfully',
                'customer' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save customer.'
            ]);
        }
    }
}

    public function get_address()
    {
        $customer_id = $this->request->getPost('customer_id');
        $model = new customerModel();
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

    $model = new \App\Models\customerModel();
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