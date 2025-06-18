<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstimateModel;
use App\Models\EstimateItemModel;

class Estimate extends BaseController
{
    public function add_estimate($id = null)
    {
        $data = [];

        if ($id) {
            $estimateModel = new EstimateModel();
            $estimateItemModel = new EstimateItemModel();

            $data['estimate'] = $estimateModel->find($id);
            $data['items'] = $estimateItemModel->where('estimate_id', $id)->findAll();
        }

        return view('add_estimate', $data);
    }

    public function estimatelist()
    {
        return view('estimatelist');
    }

   
    public function save()
    {
        $estimateModel = new EstimateModel();
        $itemModel = new EstimateItemModel();
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $estimate_id = $this->request->getPost('estimate_id');
            $isUpdate = !empty($estimate_id);

            
            $customer_name = trim($this->request->getPost('customer_name'));
            $customer_address = trim($this->request->getPost('customer_address'));

           
            if (!preg_match('/^[A-Za-z0-9\s\'.-]{2,}$/', $customer_name) || !preg_match('/[A-Za-z]/', $customer_name)) {
    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'Invalid Customer Name. Must be at least 2 characters and contain at least one letter.'
    ]);
}


        
            if (!preg_match('/[A-Za-z0-9]/', $customer_address)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid Customer Address. Please enter a valid address.'
                ]);
            }

            
            $estimateData = [
                'company_id'        => 1,
                'customer_name'     => $customer_name,
                'customer_address'  => $customer_address,
                'discount'          => $this->request->getPost('discount'),
                'user_id'           => 1,
                'date'              => date('Y-m-d H:i:s'),
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
                    $desc = trim($descriptions[$i]);
                    $price = floatval($prices[$i]);
                    $qty = intval($quantities[$i]);
                    $total = floatval($totals[$i]);

                   
                    if (empty($desc) || $price <= 0 || $qty <= 0 || $total <= 0) {
                        continue;
                    }

                    $itemData = [
                        'estimate_id' => $estimate_id,
                        'description' => $desc,
                        'quantity'    => $qty,
                        'price'       => $price,
                        'total'       => $total,
                        'discount'    => 0,
                        'date'        => date('Y-m-d H:i:s')
                    ];

                    $itemModel->insert($itemData);
                    $subTotal += $total;
                    $validItemCount++;
                }
            }

            
            if ($validItemCount === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please enter at least one valid item with Description, Quantity > 0, and Price > 0.'
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
                'status' => 'success',
                'message' => $isUpdate ? 'Estimate updated successfully!' : 'Estimate generated successfully!',
                'estimate_id' => $estimate_id
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
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
