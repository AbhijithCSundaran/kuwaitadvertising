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
            'message' => 'Name And Address Are Required'
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
                'message' => 'Customer Updated Successfully',
                'customer' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed To Update Customer.'
            ]);
        }

    } else {
        // Insert new customer
        $id = $model->insert($data);

        if ($id) {
            $data['customer_id'] = $id;
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Customer Created Successfully',
                'customer' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed To Save Customer.'
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
            return $this->response->setJSON(['status' => 'error', 'message' => 'Customer Not Found']);
        }
    }
    public function search()
{
    $term = $this->request->getGet('term');

    $model = new \App\Models\customerModel();
    $results = $model
        ->where('is_deleted', 0) // ✅ Only show active customers
        ->like('name', $term)
        ->select('customer_id, name, address')
        ->orderBy('customer_id', 'DESC') // ✅ Newest first
        ->findAll(10); // or remove limit if needed

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

public function list()
{
    return view('customerlist');
}

public function fetch()
{
    $request = service('request');
    $model = new \App\Models\customerModel();

    $draw = $request->getPost('draw') ?? 1;
    $start = $request->getPost('start') ?? 0;
    $length = $request->getPost('length') ?? 10;
    $order = $request->getPost('order');
    $search = $request->getPost('search')['value'] ?? '';

    $columnIndex = $order[0]['column'] ?? 0;
    $orderDir = $order[0]['dir'] ?? 'desc';

    $columnMap = [
        0 => 'customer_id',
        1 => 'name',
        2 => 'address'
    ];
    $orderColumn = $columnMap[$columnIndex] ?? 'customer_id';

    $customers = $model->getAllFilteredRecords($search, $start, $length, $orderColumn, $orderDir);

    $result = [];
    $slno = $start + 1;

    foreach ($customers as $row) {
        $result[] = [
            'slno' => $slno++,
            'customer_id' => $row['customer_id'],
            'name' => ucwords(strtolower($row['name'])),
            'address' => ucwords(strtolower($row['address'])),
        ];
    }

    $total = $model->getAllCustomerCount()->totcustomers;
    $filteredTotal = $model->getFilteredCustomerCount($search)->filCustomers;

    return $this->response->setJSON([
        'draw' => intval($draw),
        'recordsTotal' => $total,
        'recordsFiltered' => $filteredTotal,
        'data' => $result
    ]);
}

public function getCustomer($id)
{
    $model = new \App\Models\customerModel();
    $customer = $model->find($id);

    if ($customer) {
        return $this->response->setJSON($customer);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Customer Not Found']);
    }
}

public function edit($id)
{
    $model = new \App\Models\customerModel();
    $data['customer'] = $model->find($id);

    if (!$data['customer']) {
        return redirect()->to(base_url('customer/list'))->with('error', 'Customer Not Found');
    }

    return view('editcustomer', $data);
}


public function delete()
{
    $id = $this->request->getPost('id');
    $model = new \App\Models\customerModel();

    if ($model->update($id, ['is_deleted' => 1])) {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Customer Deleted Successfully.']);
    }else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed To Delete Customer.']);
    }
}
public function viewByCustomer($customerId)
{
    $estimateModel = new \App\Models\Estimate_Model(); // adjust to your model
    $data['estimates'] = $estimateModel->where('customer_id', $customerId)->findAll();
    $data['customer'] = (new \App\Models\Customer_Model())->find($customerId);
    
    return view('estimate/customer_estimates', $data); // create this view file
}


}