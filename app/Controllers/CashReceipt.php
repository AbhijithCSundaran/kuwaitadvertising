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

        // Fetch all invoices (later you can add pagination & filtering)
        $invoices = $invoiceModel->findAll();

        $data = [];
        $slno = 1;

        foreach ($invoices as $row) {
            $data[] = [
                'slno'           => $slno++,
                'customer_name'  => "Customer #" . $row['customer_id'], // replace with join if you have customer table
                'payment_date'   => $row['invoice_date'],
                'amount'         => $row['total_amount'],
                'payment_status' => $row['status'], // 'paid','unpaid','partial paid'
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
