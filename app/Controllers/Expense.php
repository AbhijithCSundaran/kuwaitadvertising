<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Expense_Model;

class Expense extends BaseController
{
    public function index()
    {
        return view('addexpenselist');
    }

   public function create($id = null)
{
    $data = [];

    if ($id) {
        $expenseModel = new \App\Models\Expense_Model();
        $data['expense'] = $expenseModel->find($id);
        $data['isEdit'] = true;
    }

    return view('addexpense', $data);
}


public function store()
{
    $expenseModel = new \App\Models\Expense_Model();
    $id = $this->request->getPost('id');

    $data = [
        'date' => $this->request->getPost('date'),
        'particular' => $this->request->getPost('particular'),
        'amount' => $this->request->getPost('amount'),
        'payment_mode' => $this->request->getPost('payment_mode'),
    ];

    if ($id) {
        // Check if any actual changes are made
        $existing = $expenseModel->find($id);

        // Compare each field
        $hasChanged = (
            $existing['date'] !== $data['date'] ||
            $existing['particular'] !== $data['particular'] ||
            $existing['amount'] != $data['amount'] || // != to ignore type
            $existing['payment_mode'] !== $data['payment_mode']
        );

        if ($hasChanged) {
            $expenseModel->update($id, $data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Expense updated successfully.']);
        }
    } else {
        $expenseModel->insert($data);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Expense added successfully.']);
    }
}
public function expenselistajax()
{
    $expenseModel = new \App\Models\Expense_Model();
    $expenses = $expenseModel->findAll();

    return $this->response->setJSON([
        'data' => $expenses 
    ]);
}


    public function list()
    {
        $model = new Expense_Model();
        $data = $model->findAll();

        return $this->response->setJSON(['expenses' => $data]);
    }

    public function delete()
{
    $id = $this->request->getPost('id');
    $model = new \App\Models\Expense_Model();

    if ($model->delete($id)) {
        return $this->response->setJSON(['status' => 'success']);
    } else {
        return $this->response->setJSON(['status' => 'error']);
    }
}

}
