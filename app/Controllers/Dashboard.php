<?php

namespace App\Controllers;
use App\Models\Login_Model;

class Dashboard extends BaseController
{
    public function __construct()
    {
        $session = \Config\Services::session();
        if (!$session->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }
   public function index()
{
    $session = session();
    $company_id = $session->get('company_id'); // Get logged-in user's company

    // Get recent estimates
    $estimateModel = new \App\Models\EstimateModel();
    $recentEstimates = $estimateModel->getRecentEstimatesWithCustomer($company_id);

    // Get revenue data
    $invoiceModel = new \App\Models\InvoiceModel();
    $dailyRevenue = $invoiceModel->getTodayRevenue($company_id);
    $monthlyRevenue = $invoiceModel->getMonthlyRevenue($company_id);

    return view('dashboard', [
        'estimates' => $recentEstimates,
        'dailyRevenue' => $dailyRevenue,
        'monthlyRevenue' => $monthlyRevenue
    ]);
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

public function getTodayRevenueTotal()
{
    $invoiceModel = new \App\Models\InvoiceModel();
    $session = session();
    $company_id = $session->get('company_id');

    $today = date('Y-m-d'); // only date

    $total = $invoiceModel
        ->selectSum('total_amount')
        ->where('invoice_date', $today)
        ->where('company_id', $company_id)
        ->first();

    return $this->response->setJSON(['total' => (float)($total['total_amount'] ?? 0)]);
}


public function getMonthlyRevenueTotal()
{
    $invoiceModel = new \App\Models\InvoiceModel();
    $session = session();
    $company_id = $session->get('company_id');

    $total = $invoiceModel
        ->selectSum('total_amount')
        ->where('MONTH(invoice_date)', date('m'))
        ->where('YEAR(invoice_date)', date('Y'))
        ->where('company_id', $company_id)
        ->first();

    return $this->response->setJSON(['total' => (float)($total['total_amount'] ?? 0)]);
}


}
