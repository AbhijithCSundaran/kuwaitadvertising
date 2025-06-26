<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;
use App\Models\customerModel;

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
        $customerModel = new customerModel();

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
    $estimateId = $this->request->getPost('estimate_id');
    $customerId = $this->request->getPost('customer_id');
    $address = $this->request->getPost('customer_address');
    $discount = (float) $this->request->getPost('discount');
    $description = $this->request->getPost('description');
    $price = $this->request->getPost('price');
    $quantity = $this->request->getPost('quantity');
    $total = $this->request->getPost('total');

    if (empty($customerId) || empty($address)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Please fill customer name and address.'
        ]);
    }

    $validItems = 0;
    foreach ($description as $key => $desc) {
        if (!empty(trim($desc))) {
            $validItems++;
        }
    }
    if ($validItems === 0) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Please fill at least one item with description.'
        ]);
    }


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
        'total_amount' => $grandTotal,
        'date' => date('Y-m-d')
    ];


$items = [];
    foreach ($description as $key => $desc) {
        $desc = trim($desc);
        $unitPrice = trim($price[$key]);
        $qty = trim($quantity[$key]);

        if ($desc === '' || $unitPrice === '' || $qty === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Each item must have Description, Unit Price, and Quantity filled.'
            ]);
        }

        $items[] = [
            'description' => $desc,
            'price' => (float)$unitPrice,
            'quantity' => (float)$qty,
            'total' => (float)$total[$key]
        ];
    }

    $estimateModel = new \App\Models\EstimateModel();
    $estimateItemModel = new \App\Models\EstimateItemModel();

    if (!empty($estimateId)) {
        // Update mode
        $existing = $estimateModel->find($estimateId);
        if (!$existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Estimate Not Found.'
            ]);
        }

        $hasChanges = (
            $existing['customer_id'] != $customerId ||
            $existing['customer_address'] !== $address ||
            $existing['discount'] != $discount ||
            $existing['total_amount'] != $grandTotal
        );

        if ($hasChanges) {
            $estimateModel->update($estimateId, $estimateData);
            $estimateItemModel->where('estimate_id', $estimateId)->delete();
            foreach ($items as &$item) {
                $item['estimate_id'] = $estimateId;
            }
            $estimateItemModel->insertBatch($items);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Estimate Updated Successfully.',
                'estimate_id' => $estimateId
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'nochange',
                'message' => 'No Changes Detected.',
                'estimate_id' => $estimateId
            ]);
        }

    } else {
        // Insert mode
        $estimateId = $estimateModel->insert($estimateData);
        foreach ($items as &$item) {
            $item['estimate_id'] = $estimateId;
        }
        $estimateItemModel->insertBatch($items);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Generating Estimate.',
            'estimate_id' => $estimateId
        ]);
    }
}

    public function estimatelistajax()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];

        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();

        $totalRecords = $estimateModel->getEstimateCount();
        $filteredRecords = $estimateModel->getFilteredCount($searchValue);
        $records = $estimateModel->getFilteredEstimates($searchValue, $start, $length);

        $data = [];
        $slno = $start + 1;

        foreach ($records as $row) {
            $items = $itemModel->where('estimate_id', $row['estimate_id'])->findAll();
            $descList = array_column($items, 'description');

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += (float)$item['total'];
            }

            $data[] = [
    'slno'              => $slno++,
    'estimate_id'       => $row['estimate_id'],
    'customer_name'     => $row['customer_name'],
    'customer_address'  => $row['customer_address'],
    'subtotal'          => round($subtotal, 2),
    'discount'          => $row['discount'],
    'total_amount'      => round($row['total_amount'], 2),
    'date'              => $row['date'],
    'description'       => implode(', ', $descList),
];

        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
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
        $customerModel = new customerModel();

       
        $data['items'] = $estimateItemModel->where('estimate_id', $id)->findAll();
        $data['customers'] = $customerModel->findAll();
        $data['estimate'] = $estimateModel
            ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
            ->where('estimate_id', $id)
            ->first();


        if (!$data['estimate']) {
            return redirect()->to('estimatelist')->with('error', 'Estimate not found.');
        }

        return view('add_estimate', $data);
    }

    public function generateEstimate($id)
    {
        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();

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
    // dashboardlisting
    public function recentEstimates()
    {
        $estimateModel = new \App\Models\EstimateModel();
        $itemModel = new \App\Models\EstimateItemModel();

        $estimates = $estimateModel
            ->select('estimates.*, customers.name AS customer_name')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
            ->orderBy('estimates.estimate_id', 'DESC')
            ->limit(10)
            ->findAll();

        foreach ($estimates as &$est) {
            $items = $itemModel->where('estimate_id', $est['estimate_id'])->findAll();
            $subtotal = 0;
            $descriptions = [];

            foreach ($items as $item) {
                $subtotal += (float)$item['total'];
                $descriptions[] = $item['description'];
            }

            $est['sub_total'] = $subtotal;
            $est['description'] = implode(', ', $descriptions);
        }

        return $this->response->setJSON($estimates);
    }


 
}
