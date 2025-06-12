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
        $estimate_id = $this->request->getPost('estimate_id'); // for edit
        $isUpdate = !empty($estimate_id);

        $estimateData = [
            'company_id'        => 1,
            'customer_name'     => $this->request->getPost('customer_name'),
            'customer_address'  => $this->request->getPost('customer_address'),
            'discount'          => $this->request->getPost('discount'),
            'user_id'           => 1,
            'date'              => date('Y-m-d H:i:s'),
        ];

        if ($isUpdate) {
            $estimateModel->update($estimate_id, $estimateData);
            $itemModel->where('estimate_id', $estimate_id)->delete(); // clear old items
        } else {
            $estimate_id = $estimateModel->insert($estimateData);
        }

        $descriptions = $this->request->getPost('description');
        $prices = $this->request->getPost('price');
        $quantities = $this->request->getPost('quantity');
        $totals = $this->request->getPost('total');
        $subTotal = 0;

        if ($descriptions && is_array($descriptions)) {
            for ($i = 0; $i < count($descriptions); $i++) {
                $itemData = [
                    'estimate_id' => $estimate_id,
                    'description' => $descriptions[$i],  
                    'quantity'    => (int)$quantities[$i],
                    'price'       => (float)$prices[$i],
                    'total'       => (float)$totals[$i],
                    'discount'    => 0,
                    'date'        => date('Y-m-d H:i:s')
                ];
                $itemModel->insert($itemData);
                $subTotal += $itemData['total'];
            }
        }

        // Update total amount
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

        // Combine descriptions (or you can pick the first only)
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
