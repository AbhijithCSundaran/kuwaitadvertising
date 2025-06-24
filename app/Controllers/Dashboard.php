<?php

namespace App\Controllers;
use App\Models\Login_Model;

class Dashboard extends BaseController
{
    public function index()
    {
         return view('dashboard');
    }
    public function getTodayExpenseTotal()
    {
        $expenseModel = new \App\Models\Expense_Model();
        $today = date('Y-m-d');

        $total = $expenseModel
            ->selectSum('amount')
            ->where('date', $today)
            ->first();

        return $this->response->setJSON(['total' => (int)$total['amount']]);
    }

    public function getMonthlyExpenseTotal()
{
    $expenseModel = new \App\Models\Expense_Model();

    $start = date('Y-m-01'); // 1st of this month
    $end = date('Y-m-t');    // Last day of this month

    $total = $expenseModel
        ->where('date >=', $start)
        ->where('date <=', $end)
        ->selectSum('amount')
        ->first();

    return $this->response->setJSON(['total' => $total['amount'] ?? 0]);
}

}
