<?php
namespace App\Controllers;
use App\Models\CustomerModel;

class Customer extends BaseController
{
    public function create()
    {
        $name = trim($this->request->getPost('name'));
        $address = trim($this->request->getPost('address'));

        if (strlen($name) < 2 || strlen($address) < 2) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please provide valid name and address.']);
        }

        $customerModel = new CustomerModel();
        $id = $customerModel->insert([
            'name' => $name,
            'address' => $address
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'customer' => [
                'customer_id' => $id,
                'name' => $name
            ]
        ]);
    }
  public function getAddressById()
{
    $customerId = $this->request->getPost('customer_id');
    $model = new \App\Models\CustomerModel();
    $customer = $model->find($customerId);

    if ($customer) {
        return $this->response->setJSON([
            'status' => 'success',
            'address' => $customer['address']
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Customer not found'
        ]);
    }
}

}
