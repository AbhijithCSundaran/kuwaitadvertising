<?php namespace App\Controllers;

use App\Models\InvoiceModel;
use CodeIgniter\API\ResponseTrait;

class CashReceipt extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('cashlist'); // DataTable view
    }

    public function create()
    {
        return view('print_receipt'); // form view
    }

   public function ajaxList()
{
    $request = service('request');
    $invoiceModel = new InvoiceModel();

    // Fetch invoices with customer names
    $invoices = $invoiceModel->getInvoicesWithCustomer();

    $data = [];
    $slno = 1;

    foreach ($invoices as $row) {
        $data[] = [
            'slno'           => $slno++,
            'customer_name'  => $row['customer_name'], 
            'payment_date'   => $row['invoice_date'],
            'amount'         => $row['total_amount'],
            'paid_amount' => $row['paid_amount'] ?? 0,
            'balance_amount' => $row['balance_amount'] ?? ($row['total_amount'] ?? 0),
            'payment_status' => $row['status'],
            'payment_mode'   => $row['payment_mode'],
            'payment_id'     => $row['invoice_id'],
        ];
    }

    return $this->response->setJSON([
        "draw" => intval($request->getPost('draw')),
        "recordsTotal" => count($invoices),
        "recordsFiltered" => count($invoices),
        "data" => $data
    ]);
}


}
