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
}
