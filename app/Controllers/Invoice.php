<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\CustomerModel;

class Invoice extends BaseController
{
 public function add()
    {
        $customerModel = new CustomerModel();
        $data['customers'] = $customerModel->where('is_deleted', 0)->findAll();
        return view('invoice_form', $data);
    }

    public function list()
    {
        return view('invoicelist');
    }

    public function fetchInvoices()
    {
        $invoiceModel = new InvoiceModel();
        $data = $invoiceModel->getInvoiceListWithCustomer();
        return $this->response->setJSON(['data' => $data]);
    }

    public function print($id)
    {
        $invoiceModel = new InvoiceModel();
        $invoice = $invoiceModel->getInvoiceWithItems($id);

        if (!$invoice) {
            return redirect()->to('/invoicelist')->with('error', 'Invoice not found.');
        }

        return view('invoice_print', [
            'invoice' => $invoice,
            'items' => $invoice['items'],
            'user_name' => session()->get('user_name') ?? 'Salesman'
        ]);
    }

public function save()
{
    $request = $this->request;
    $invoiceModel = new InvoiceModel();
    $itemModel = new \App\Models\InvoiceItemModel(); // Make sure this model exists
    $customerModel = new CustomerModel();

    $customerId = $request->getPost('customer_id');
    $customer = $customerModel->find($customerId);

    if (!$customer) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid customer selected.'
        ]);
    }

    $invoiceData = [
        'customer_id'      => $customerId,
        'customer_address' => $request->getPost('customer_address'),
        'total_amount'     => $this->calculateTotal($request),
        'invoice_date'     => date('Y-m-d'),
        'status'           => 'unpaid',
        'user_id'          => session()->get('user_id') ?? 1
    ];

    $invoiceId = $request->getPost('invoice_id');

    if ($invoiceId) {
        $invoiceModel->update($invoiceId, $invoiceData);
        $itemModel->where('invoice_id', $invoiceId)->delete(); // Remove old items
    } else {
        $invoiceModel->insert($invoiceData);
        $invoiceId = $invoiceModel->getInsertID();
    }

    // Save items
    $descriptions = $request->getPost('description');
    $quantities   = $request->getPost('quantity');
    $prices       = $request->getPost('price');

    foreach ($descriptions as $i => $desc) {
        if (!empty($desc) && $quantities[$i] > 0 && $prices[$i] > 0) {
            $itemModel->insert([
                'invoice_id' => $invoiceId,
                'item_name'  => $desc,
                'quantity'   => $quantities[$i],
                'price'      => $prices[$i]
            ]);
        }
    }

    return $this->response->setJSON([
        'status'   => 'success',
        'message'  => 'Invoice saved successfully',
        'redirect' => site_url('invoice/print/' . $invoiceId)
    ]);
}


    // Helper to calculate totals
    private function calculateTotal($request)
    {
        $prices   = $request->getPost('price');
        $qtys     = $request->getPost('quantity');
        $discount = floatval($request->getPost('discount') ?? 0);
        $subtotal = 0;

        foreach ($prices as $i => $price) {
            $subtotal += floatval($price) * floatval($qtys[$i]);
        }

        $discountAmt = ($subtotal * $discount) / 100;
        return $subtotal - $discountAmt;
    }

    public function edit($id)
{
    $invoiceModel = new InvoiceModel();
    $customerModel = new CustomerModel();

    $invoice = $invoiceModel->find($id);
    if (!$invoice) {
        return redirect()->to(base_url('invoicelist'))->with('error', 'Invoice not found.');
    }

    $data = [
        'invoice' => $invoice,
        'customers' => $customerModel->where('is_deleted', 0)->findAll()
    ];

    return view('invoice_form', $data); // if it's directly inside Views/

}

public function delete($id = null)
{
    if (!$id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid ID']);
    }

    $model = new \App\Models\InvoiceModel();
    $deleted = $model->delete($id);

    return $this->response->setJSON([
        'status' => $deleted ? 'success' : 'error',
        'message' => $deleted ? 'Invoice deleted successfully.' : 'Failed to delete invoice.'
    ]);
}



}
