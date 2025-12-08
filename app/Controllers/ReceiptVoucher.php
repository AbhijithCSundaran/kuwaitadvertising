<?php
namespace App\Controllers;

use App\Models\Managecompany_Model;
use App\Models\InvoiceModel;
use App\Models\customerModel;
use App\Models\Manageuser_Model;
use App\Models\TransactionModel;

class ReceiptVoucher extends BaseController
{
    private function convertAmountToWords($kd, $fils)
    {
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        $kdWords   = $kd > 0 ? ucfirst($f->format($kd)) . " Kuwaiti Dinar" : "";
        $filsWords = $fils > 0 ? ucfirst($f->format($fils)) . " Fils" : "";

        if ($kdWords && $filsWords) return $kdWords . " and " . $filsWords;
        if ($kdWords) return $kdWords;
        if ($filsWords) return $filsWords;

        return "Zero KD";
    }


 public function index($transaction_id = null)
{
    if (!$transaction_id) {
        return "Invalid ID";
    }

    $companyModel     = new Managecompany_Model();
    $invoiceModel     = new InvoiceModel();
    $customerModel    = new customerModel();
    $transactionModel = new TransactionModel();

    $company = $companyModel->first();

    // Find the transaction by transaction_id
    $transaction = $transactionModel->find($transaction_id);
    if (!$transaction) {
        return "Transaction not found";
    }

    // Find the linked invoice
    $invoice_id = $transaction['invoice_id'] ?? null;
    $invoice = $invoiceModel->find($invoice_id);

    // Amount calculation
    $paid = floatval($transaction['paid_amount'] ?? 0);
    $kd   = floor($paid);
    $fils = round(($paid - $kd) * 1000);

    // Fetch directly from transactions table
    $cash_cheque_knet = $transaction['cash_cheque_knet'] ?? '';
    $being_of         = $transaction['being_of'] ?? '';

    $customer_id = $transaction['customer_id'] ?? $invoice['customer_id'] ?? null;

    // Load customer
    $customer = null;
    if ($customer_id) {
        $cust = $customerModel->find($customer_id);
        if ($cust) {
            $customer = [
                'customer_name' => $cust['name'] ?? '',
                'address'       => $cust['address'] ?? ''
            ];
        }
    }

    // Amount in words
    $amount_in_words = $this->convertAmountToWords($kd, $fils);

    // Invoice date fallback
    if (!empty($invoice['invoice_date'])) {
        $formatted_date = date('d-m-Y', strtotime($invoice['invoice_date']));
    } else {
        $formatted_date = date('d-m-Y', strtotime($transaction['created_at'] ?? date('Y-m-d')));
    }

    return view('print_receipt', [
        'mode' => 'print',
        'company'         => $company,
        'invoice'         => $invoice,
        'customer'        => $customer,
        'invoice_no'      => $invoice['invoice_no'] ?? 'N/A',
        'kd'              => $kd,
        'fils'            => $fils,
        'cash_cheque_knet'=> $cash_cheque_knet,
        'being_of'        => $being_of,
        'invoice_date'    => $formatted_date,
        'amount_in_words' => $amount_in_words,
        'transaction_id'  => $transaction_id 
    ]);
}






 public function edit($transaction_id = null)
{
    if (!$transaction_id) {
        return "Invalid ID";
    }

    $companyModel     = new Managecompany_Model();
    $invoiceModel     = new InvoiceModel();
    $customerModel    = new CustomerModel();
    $transactionModel = new TransactionModel();

    $company = $companyModel->first();

    // Get the transaction
    $transaction = $transactionModel->find($transaction_id);
    if (!$transaction) {
        return "Transaction not found";
    }

    // Get linked invoice
    $invoice_id = $transaction['invoice_id'] ?? null;
    $invoice    = $invoiceModel->find($invoice_id);

    // Prefill amounts from transaction
    $paid = floatval($transaction['paid_amount'] ?? 0);
    $kd   = floor($paid);
    $fils = round(($paid - $kd) * 1000);

    $cash_cheque_knet = $transaction['payment_mode'] ?? '';
    $being_of         = $transaction['being_of'] ?? '';

    // Customer info
    $customer = null;
    $customer_id = $transaction['customer_id'] ?? $invoice['customer_id'] ?? null;
    if ($customer_id) {
        $cust = $customerModel->find($customer_id);
        if ($cust) {
            $customer = [
                'customer_id'   => $customer_id,
                'customer_name' => $cust['name'] ?? '',
                'address'       => $cust['address'] ?? ''
            ];
        }
    }

    // Amount in words (optional)
    $amount_in_words = $this->convertAmountToWords($kd, $fils);

    return view('edit_receipt', [
        'company'          => $company,
        'invoice'          => $invoice,
        'customer'         => $customer,
        'kd'               => $kd,
        'fils'             => $fils,
        'cash_cheque_knet' => $cash_cheque_knet,
        'being_of'         => $being_of,
        'amount_in_words'  => $amount_in_words,
        'invoice_id'       => $invoice_id,
        'transaction_id'   => $transaction_id
    ]);
}




public function update($transaction_id = null)
{
    $db = db_connect();

    // Get transaction_id from URL or POST
    $transaction_id = $transaction_id ?? $this->request->getPost('transaction_id');
    if (!$transaction_id) {
        return redirect()->back()->with('error', 'Invalid Transaction ID');
    }

    // Get the transaction to find the invoice_id
    $transaction = $db->table('transactions')
                      ->where('transaction_id', $transaction_id)
                      ->get()
                      ->getRowArray();
    if (!$transaction) {
        return redirect()->back()->with('error', 'Transaction not found');
    }

    $invoice_id = $transaction['invoice_id'];

    // Get KD and Fils
    $kd   = (float) $this->request->getPost('kd');
    $fils = (float) $this->request->getPost('fils');

    // Calculate total paid amount
    $paid_amount = $kd + ($fils / 1000);

    // Prepare data for receipt_vouchers table
    $data = [
        'customer_id'      => $this->request->getPost('customer_id'),
        'paid_amount'      => $paid_amount,
        'cash_cheque_knet' => $this->request->getPost('cash_cheque_knet'),
        'being_of'         => $this->request->getPost('being_of'),
        'updated_by'       => session()->get('user_id'),
        'updated_at'       => date('Y-m-d H:i:s')
    ];

    $receiptTable = $db->table('receipt_vouchers');

    // Check if receipt voucher exists for this invoice
    $exists = $receiptTable->where('invoice_id', $invoice_id)->countAllResults(false);

    if ($exists) {
        $receiptTable->where('invoice_id', $invoice_id)->update($data);
    } else {
        $data['invoice_id'] = $invoice_id;
        $receiptTable->insert($data);
    }

    // --- Update transactions table ---
    $transactionData = [
        'paid_amount'        => $paid_amount,
        'partial_paid_amount'=> $paid_amount, // if you track partials separately
        'cash_cheque_knet'   => $this->request->getPost('cash_cheque_knet'),
        'being_of'           => $this->request->getPost('being_of'),
        'updated_by'         => session()->get('user_id'),
        'updated_at'         => date('Y-m-d H:i:s')
    ];

    $db->table('transactions')
       ->where('transaction_id', $transaction_id)
       ->update($transactionData);
    // ---------------------------------

    return redirect()->to(base_url('receiptvoucher/index/' . $transaction_id));
}

 public function view($transaction_id = null)
{
    if (!$transaction_id) {
        return "Invalid ID";
    }

    $companyModel     = new Managecompany_Model();
    $invoiceModel     = new InvoiceModel();
    $customerModel    = new customerModel();
    $transactionModel = new TransactionModel();

    $company = $companyModel->first();

    // Find the transaction by transaction_id
    $transaction = $transactionModel->find($transaction_id);
    if (!$transaction) {
        return "Transaction not found";
    }

    // Find the linked invoice
    $invoice_id = $transaction['invoice_id'] ?? null;
    $invoice = $invoiceModel->find($invoice_id);

    // Amount calculation
    $paid = floatval($transaction['paid_amount'] ?? 0);
    $kd   = floor($paid);
    $fils = round(($paid - $kd) * 1000);


    // Fetch directly from transactions table
    $cash_cheque_knet = $transaction['cash_cheque_knet'] ?? '';
    $being_of         = $transaction['being_of'] ?? '';

    $customer_id = $transaction['customer_id'] ?? $invoice['customer_id'] ?? null;

     // Amount in words
    $amount_in_words = $this->convertAmountToWords($kd, $fils);
    // Load customer
    $customer = null;
    if ($customer_id) {
        $cust = $customerModel->find($customer_id);
        if ($cust) {
            $customer = [
                'customer_name' => $cust['name'] ?? '',
                'address'       => $cust['address'] ?? ''
            ];
        }
    }

    // Invoice date fallback
    if (!empty($invoice['invoice_date'])) {
        $formatted_date = date('d-m-Y', strtotime($invoice['invoice_date']));
    } else {
        $formatted_date = date('d-m-Y', strtotime($transaction['created_at'] ?? date('Y-m-d')));
    }

     return view('print_receipt', [
        'mode'            => 'view', 
        'company'         => $company,
        'invoice'         => $invoice,
        'customer'        => $customer,
        'invoice_no'      => $invoice['invoice_no'] ?? 'N/A',
        'kd'              => $kd,
        'fils'            => $fils,
        'cash_cheque_knet'=> $cash_cheque_knet,
        'being_of'        => $being_of,
        'invoice_date'    => $formatted_date,
        'amount_in_words' => $amount_in_words,
        'transaction_id'  => $transaction_id 
    ]);

}




}
