<?php

namespace App\Controllers;

use App\Models\CompanyLedgerModel;
use App\Models\Managecompany_Model;

class CompanyLedger extends BaseController
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
        $companyModel = new Managecompany_Model();
        $data['companies'] = $companyModel->findAll();

        return view('companyledger', $data);
    }

    public function save()
    {
        $companyId = $this->request->getPost('company_id');

        if (!$companyId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Company ID is required']);
        }

        $ledgerModel = new CompanyLedgerModel();

        $ledgerModel->insert([
            'company_id'     => $companyId,
            'invoice_id'     => 0, // placeholder
            'customer_id'    => 0, // placeholder
            'invoice_amount' => 0  // placeholder
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Ledger Entry Created For The Company.']);
    }
    public function getPaidInvoices()
{
    $companyId = $this->request->getPost('company_id');

    if (!$companyId) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Company ID is required.'
        ]);
    }

    $invoiceModel = new \App\Models\InvoiceModel();

    $builder = $invoiceModel->builder()
        ->select('invoices.invoice_id, invoices.date, invoices.total_amount, customers.name AS customer_name')
        ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
        ->where('invoices.company_id', $companyId)
        ->where('LOWER(invoices.status)', 'paid')  // Handles lowercase match
        ->orderBy('invoices.date', 'DESC');

    $results = $builder->get()->getResultArray();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $results
    ]);
}

}
