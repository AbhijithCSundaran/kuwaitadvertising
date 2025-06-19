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
    $estimateModel = new EstimateModel();
    $itemModel     = new EstimateItemModel();
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        $estimate_id   = $this->request->getPost('estimate_id');
        $isUpdate      = !empty($estimate_id);
        $customer_id   = $this->request->getPost('customer_id');

        if (empty($customer_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Customer is required.'
            ]);
        }

  
       $customer_id   = $this->request->getPost('customer_id');
        $customer_name = trim($this->request->getPost('customer_name'));
        $customer_address = trim($this->request->getPost('customer_address'));

        $customerModel = new \App\Models\CustomerModel();

        if (empty($customer_id)) {
            if (strlen($customer_name) < 2 || strlen($customer_address) < 2) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please enter a valid customer name and address.'
                ]);
            }
            $customer_id = $customerModel->insert([
                'name'    => $customer_name,
                'address' => $customer_address,
            ]);
        }

        $customer = $customerModel->find($customer_id);

        if (!$customer) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Selected customer not found.'
            ]);
        }

        $estimateData = [
            'company_id'       => 1,
            'customer_id'      => $customer_id,
            'customer_name'    => $customer['name'],  
            'customer_address' => $customer['address'], 
            'discount'         => $this->request->getPost('discount'),
            'user_id'          => 1,
            'date'             => date('Y-m-d H:i:s'),
        ];

        if ($isUpdate) {
            $estimateModel->update($estimate_id, $estimateData);
            $itemModel->where('estimate_id', $estimate_id)->delete();
        } else {
            $estimate_id = $estimateModel->insert($estimateData);
        }

        $descriptions = $this->request->getPost('description');
        $prices       = $this->request->getPost('price');
        $quantities   = $this->request->getPost('quantity');
        $totals       = $this->request->getPost('total');

        $subTotal = 0;
        $validItemCount = 0;

        if ($descriptions && is_array($descriptions)) {
            for ($i = 0; $i < count($descriptions); $i++) {
                $desc  = trim($descriptions[$i]);
                $price = floatval($prices[$i]);
                $qty   = intval($quantities[$i]);
                $total = floatval($totals[$i]);

                if (empty($desc) || $price <= 0 || $qty <= 0 || $total <= 0) {
                    continue;
                }

                $itemModel->insert([
                    'estimate_id' => $estimate_id,
                    'description' => $desc,
                    'quantity'    => $qty,
                    'price'       => $price,
                    'total'       => $total,
                    'discount'    => 0,
                    'date'        => date('Y-m-d H:i:s')
                ]);

                $subTotal += $total;
                $validItemCount++;
            }
        }

        if ($validItemCount === 0) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please enter at least one valid item.'
            ]);
        }

     
        $discount = floatval($this->request->getPost('discount') ?? 0);
        $discountAmount = ($subTotal * $discount) / 100;
        $totalAfterDiscount = $subTotal - $discountAmount;

        $estimateModel->update($estimate_id, [
            'total_amount' => $totalAfterDiscount,
            'discount'     => $discount
        ]);

        $db->transComplete();

        return $this->response->setJSON([
            'status'      => 'success',
            'message'     => $isUpdate ? 'Estimate updated successfully!' : 'Estimate generated successfully!',
            'estimate_id' => $estimate_id
        ]);

    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

    public function estimatelistajax()
    {
        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();

        $estimates = $estimateModel->findAll();

        foreach ($estimates as &$estimate) {
            $items = $itemModel->where('estimate_id', $estimate['estimate_id'])->findAll();

            
            $descriptions = array_column($items, 'description');
            $estimate['description'] = implode(', ', $descriptions);
        }

        return $this->response->setJSON(['data' => $estimates]);
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

        $estimate = $estimateModel->find($id);

        if (!$estimate) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Estimate with ID $id not found");
        }

        $items = $estimateItemModel->where('estimate_id', $id)->findAll();

        return view('add_estimate', [
            'estimate' => $estimate,
            'items' => $items
        ]);
    }
}
