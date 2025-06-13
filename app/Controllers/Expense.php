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
        $data = [
            'isEdit' => !empty($id),
            'expense' => null
        ];

        if ($id) {
            $expenseModel = new Expense_Model();
            $data['expense'] = $expenseModel->find($id);
        }

        return view('addexpense', $data);
    }

    public function store()
    {
        $expenseModel = new Expense_Model();

        $id           = $this->request->getPost('id');
        $date         = $this->request->getPost('date');
        $particular   = $this->request->getPost('particular');
        $amount       = $this->request->getPost('amount');
        $payment_mode = $this->request->getPost('payment_mode');

        if (empty($date) || empty($particular) || empty($amount) || empty($payment_mode)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Please fill all mandatory fields.'
            ]);
        }

        $data = [
            'date'         => $date,
            'particular'   => $particular,
            'amount'       => $amount,
            'payment_mode' => $payment_mode
        ];

        if (!empty($id)) {
            $existing = $expenseModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Expense not found.'
                ]);
            }

            $hasChanges = (
                $existing['date'] !== $data['date'] ||
                $existing['particular'] !== $data['particular'] ||
                $existing['amount'] != $data['amount'] ||
                $existing['payment_mode'] !== $data['payment_mode']
            );

            if ($hasChanges) {
                $expenseModel->update($id, $data);
                $msg = 'Expense updated successfully.';
            } else {
                $msg = 'No changes detected.';
            }
        } else {
            $expenseModel->insert($data);
            $msg = 'Expense added successfully.';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $msg
        ]);
    }

   public function getExpensesAjax()
{
    $expenseModel = new \App\Models\Expense_Model();
    $expenses = $expenseModel->orderBy('id', 'DESC')->findAll();

    return $this->response->setJSON($expenses); // Return as plain array
}

 public function delete()
{
    $id = $this->request->getPost('id');

    if (!$id) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No ID provided.'
        ]);
    }

    $expenseModel = new \App\Models\Expense_Model();
    $expense = $expenseModel->find($id);

    if (!$expense) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Expense not found.'
        ]);
    }

    if ($expenseModel->delete($id)) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Expense deleted successfully.'
        ]);
    }

    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'Failed to delete expense.'
    ]);
}


}
