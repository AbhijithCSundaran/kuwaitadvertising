<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\CustomerModel;

class Estimate extends BaseController
{
     public function estimatelist()
    {
        return view('estimatelist');
    }
    public function add_estimate($id = null)
{
    $estimateModel = new EstimateModel();
    $estimateItemModel = new EstimateItemModel();
    $customerModel = new \App\Models\CustomerModel();

    $data['customers'] = $customerModel->findAll();
    $data['estimate'] = null;
    $data['items'] = [];

    if ($id) {
        $data['estimate'] = $estimateModel->find($id);
        $data['items'] = $estimateItemModel->where('estimate_id', $id)->findAll();
    }

    return view('add_estimate', $data);
}

public function save()
{
    $customerId = $this->request->getPost('customer_id');
    $address = $this->request->getPost('customer_address');
    $discount = (float) $this->request->getPost('discount');
    $description = $this->request->getPost('description');
    $price = $this->request->getPost('price');
    $quantity = $this->request->getPost('quantity');
    $total = $this->request->getPost('total');

    $subtotal = 0;
    foreach ($total as $t) {
        $subtotal += (float)$t;
    }
    $discountAmount = ($subtotal * $discount) / 100;
    $grandTotal = $subtotal - $discountAmount;

    $estimateData = [
        'customer_id' => $customerId,
        'customer_address' => $address,
        'discount' => $discount,
        'total' => $grandTotal,
        'date' => date('Y-m-d')
    ];

    $items = [];
    foreach ($description as $key => $desc) {
        $items[] = [
            'description' => $desc,
            'price' => (float)$price[$key],
            'quantity' => (float)$quantity[$key],
            'total' => (float)$total[$key]
        ];
    }

    $estimateModel = new \App\Models\EstimateModel();
    $estimateId = $estimateModel->insertEstimateWithItems($estimateData, $items);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Estimate saved successfully.',
        'estimate_id' => $estimateId
    ]);
}


   public function estimatelistajax()
{
    $db = \Config\Database::connect();
    $estimateModel = new EstimateModel();
    $itemModel = new EstimateItemModel();
 
    $estimates = $estimateModel->findAll();
 
    $builder = $db->table('estimates');
    $builder->select('estimates.estimate_id, estimates.date, estimates.total_amount AS total_amount, estimates.discount, customers.name AS customer_name, customers.address AS customer_address');

    $builder->join('customers', 'customers.customer_id = estimates.customer_id', 'left');
    
    $query = $builder->get()->getResultArray();

    $data = [];
    foreach ($query as $i => $row) {

        $items = $itemModel->where('estimate_id',  $row['estimate_id'])->findAll();
        $descriptions = array_column($items, 'description');

        $data[] = [
            'estimate_id'       => $row['estimate_id'],
            'customer_name'     => $row['customer_name'],
            'customer_address'  => $row['customer_address'],
            'date'              => $row['date'],
            'discount'          => $row['discount'],
            'total_amount'      => $row['total_amount'],
            'description'       => implode(', ', $descriptions), 
        ];
    }

    return $this->response->setJSON([
        'data' => $data
    ]);
}

    public function delete()
    {
        $estimate_id = $this->request->getPost('estimate_id');
        if (!$estimate_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid ID']);
        }

        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();

        $itemModel->where('estimate_id', $estimate_id)->delete();
        $estimateModel->delete($estimate_id);

        return $this->response->setJSON(['status' => 'success']);
    }

   public function edit($id)
{
    $estimateModel = new EstimateModel();
    $estimateItemModel = new EstimateItemModel();
    $customerModel = new CustomerModel();

    $data['estimate'] = $estimateModel->find($id); 
    $data['items'] = $estimateItemModel->where('estimate_id', $id)->findAll(); 
    $data['customers'] = $customerModel->findAll();

    if (!$data['estimate']) {
        return redirect()->to('estimatelist')->with('error', 'Estimate not found.');
    }

    return view('add_estimate', $data); 
}

    public function generateestimate($id)
{
    $estimateModel = new \App\Models\EstimateModel();
    $itemModel = new \App\Models\EstimateitemModel();

    $estimate = $estimateModel
        ->select('estimates.*, customers.name AS customer_name')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimate_id', $id)
        ->first();

    $items = $itemModel->where('estimate_id', $id)->findAll();

    return view('generateestimate', [
        'estimate' => $estimate,
        'items' => $items
    ]);
}

}
