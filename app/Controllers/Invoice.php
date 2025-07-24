<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\customerModel;

class Invoice extends BaseController
{
    public function add()
    {
        $customerModel = new customerModel();
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
    public function invoicelistajax()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];

        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 8;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

        $columns = [
            0 => 'invoices.invoice_id',
            1 => 'customers.name',
            2 => 'customers.address',
            3 => 'subtotal',
            4 => 'invoices.discount',
            5 => 'invoices.total_amount',
            6 => 'invoices.invoice_date',
            7 => 'invoices.invoice_id',
            8 => 'invoices.invoice_id'
        ];

        $orderByColumn = $columns[$orderColumnIndex] ?? 'invoices.invoice_id';

        $invoiceModel = new \App\Models\InvoiceModel();
        $itemModel = new \App\Models\InvoiceItemModel();

        $totalRecords = $invoiceModel->getInvoiceCount();
        $filteredRecords = $invoiceModel->getFilteredCount($searchValue);
        $records = $invoiceModel->getFilteredInvoices($searchValue, $start, $length, $orderByColumn, $orderDir);

        $data = [];
        $slno = $start + 1;

        foreach ($records as $row) {
            $items = $itemModel->where('invoice_id', $row['invoice_id'])->findAll();

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $data[] = [
                'slno' => $slno++,
                'invoice_id' => $row['invoice_id'],
                'customer_name' => $row['customer_name'],
                'customer_address' => $row['customer_address'],
                'subtotal' => number_format($subtotal, 2),
                'discount' => $row['discount'],
                'total_amount' => round($row['total_amount'], 2),
                'status' => $row['status'] ?? 'unpaid',
                'invoice_date'  => date('d-m-Y', strtotime($row['invoice_date'])),
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }



  public function print($id)
{
    $invoiceModel = new InvoiceModel();
    $invoice = $invoiceModel->getInvoiceWithItems($id);

    if (!$invoice) {
        return redirect()->to('/invoicelist')->with('error', 'Invoice not found.');
    }

    $companyId = session()->get('company_id');

    if ($companyId == 69) {
        return view('invoice_print', [
            'invoice' => $invoice,
            'items' => $invoice['items'],
            'user_name' => session()->get('user_name') ?? 'Salesman'
        ]);
    } elseif ($companyId == 70) {
        return view('generate_invoice', [
            'invoice' => $invoice,
            'items' => $invoice['items'],
            'user_name' => session()->get('user_name') ?? 'Salesman'
        ]);
    } else {
        return view('invoice_print', [ // default fallback
            'invoice' => $invoice,
            'items' => $invoice['items'],
            'user_name' => session()->get('user_name') ?? 'Salesman'
        ]);
    }
}




    public function save()
    {
        $request = $this->request;
        $invoiceModel = new InvoiceModel();
        $itemModel = new \App\Models\InvoiceItemModel(); // Make sure this model exists
        $customerModel = new customerModel();
        $discount = $this->request->getPost('discount');
        $customerId = $request->getPost('customer_id');
        $customer = $customerModel->find($customerId);

        if (!$customer) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid customer selected.'
            ]);
        }

       $invoiceData = [
            'customer_id'       => $customerId,
            'billing_address'   => $customer['billing_address'] ?? $customer['customer_address'] ?? '',
            'shipping_address'  => $customer['shipping_address'] ?? $request->getPost('shipping_address') ?? '',
            'phone_number'             => $customer['phone_number'] ?? $request->getPost('phone_number') ?? '',
            'lpo_no'            => $request->getPost('lpo_no'),
            'total_amount'      => $this->calculateTotal($request),
            'discount'          => $this->request->getPost('discount'),
            'invoice_date'      => date('Y-m-d'),
            'status'            => 'unpaid',
            'user_id'           => session()->get('user_id') ?? 1
        ];


        $invoiceId = $request->getPost('invoice_id');

        if ($invoiceId) {
            $invoiceModel->update($invoiceId, $invoiceData);
            $itemModel->where('invoice_id', $invoiceId)->delete(); // Remove old items
             $message = 'Invoice Updated Successfully';
        } else {
            $invoiceModel->insert($invoiceData);
            $invoiceId = $invoiceModel->getInsertID();
            $message = 'Generating Invoice';
        }

        $descriptions = $request->getPost('description');
        $quantities = $request->getPost('quantity');
        $prices = $request->getPost('price');
        if ($descriptions && $quantities && $prices) {
            foreach ($descriptions as $i => $desc) {
                if (!empty($desc) && $quantities[$i] > 0 && $prices[$i] > 0) {
                    $itemModel->insert([
                        'invoice_id' => $invoiceId,
                        'item_name' => $desc,
                        'quantity' => $quantities[$i],
                        'price' => $prices[$i]
                    ]);
                }
            }

        }
        return $this->response->setJSON([
        'status'   => 'success',
        'message'  => $message ,
        'redirect' => site_url('invoice/print/' . $invoiceId)
    ]);
    }


    // Helper to calculate totals
    private function calculateTotal($request)
    {
        $prices = $request->getPost('price');
        $qtys = $request->getPost('quantity');
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
        $itemModel = new \App\Models\InvoiceItemModel();
        $customerModel = new customerModel();

        // Fetch invoice
        $invoice = $invoiceModel->find($id);
        if (!$invoice) {
            return redirect()->to(base_url('invoicelist'))->with('error', 'Invoice not found.');
        }

        // Fetch customer
        $customer = $customerModel->find($invoice['customer_id']);
        if ($customer) {
            $invoice['customer_name'] = $customer['customer_name'] ?? '';
            $invoice['customer_address'] = $customer['address'] ?? '';
        } else {
            $invoice['customer_name'] = '';
            $invoice['customer_address'] = '';
        }

        // Fetch invoice items
        $items = $itemModel->where('invoice_id', $id)->findAll();

        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'customers' => $customerModel->where('is_deleted', 0)->findAll()
        ];

        return view('invoice_form', $data);
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
    public function printInvoice($id)
    {
        $model = new InvoiceModel();
        $invoice = $model->find($id);

        $itemModel = new InvoiceItemModel();
        $items = $itemModel->where('invoice_id', $id)->findAll(); // ✅ GET ITEMS

        $user_id = $invoice['user_id'];
        $userModel = new UserModel();
        $user = $userModel->find($user_id);
        $user_name = $user['name'] ?? 'N/A';

        return view('invoice/invoice_print', [
            'invoice' => $invoice,
            'items' => $items,          // ✅ PASS TO VIEW
            'user_name' => $user_name,  // ✅ PASS TO VIEW
        ]);
    }

}
