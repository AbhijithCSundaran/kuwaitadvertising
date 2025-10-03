<?php 
namespace App\Controllers;
use App\Models\CustomerReportModel;
use CodeIgniter\Controller;

class CustomerReport extends Controller
{
    public function index()
    {
        $customerModel = new \App\Models\customerModel();
        $data['customers'] = $customerModel->findAll();
        return view('customer_report', $data);
    }

    // public function getReport()
    // {
    //     $request = \Config\Services::request();
    //     $customer_id = $request->getPost('customer_id');
    //     $type = $request->getPost('type'); // 'invoice' or 'estimate'

    //     $reportModel = new CustomerReportModel();

    //     if ($type == 'invoice') {
    //         $data = $reportModel->getInvoicesByCustomer($customer_id);
    //     } else {
    //         $data = $reportModel->getEstimatesByCustomer($customer_id);
    //     }

    //     return $this->response->setJSON($data);
    // }

    public function getReport()
{
    $request = \Config\Services::request();
    $customer_id = $request->getPost('customer_id');
    $type = $request->getPost('type'); // 'invoice' or 'estimate'
    $companyId  = session()->get('company_id');
    $db = db_connect();

    $data = [];

    if ($type == 'invoice') {
        $invoices = $db->table('invoices i')
                       ->select('i.*, c.name as customer_name')
                       ->join('customers c', 'c.customer_id = i.customer_id', 'left')
                       ->where('i.customer_id', $customer_id)
                       ->where('i.company_id', $companyId)
                       ->orderBy('i.invoice_id','DESC')
                       ->get()
                       ->getResultArray();

        foreach ($invoices as $inv) {
            $items = $db->table('invoice_items')
                        ->where('invoice_id', $inv['invoice_id'])
                        ->get()->getResultArray();
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $data[] = [
                'id' => $inv['invoice_id'],
                'no' => $inv['invoice_no'],
                'customer' => $inv['customer_name'],
                'subtotal' => $subtotal,
                'discount' => $inv['discount'],
                'total' => $inv['total_amount'],
                'paid' => $inv['paid_amount'],
                'balance' => $inv['balance_amount'],
                'date' => $inv['invoice_date']
            ];
        }
    }

    if ($type == 'estimate') {
        $estimates = $db->table('estimates e')
                        ->select('e.*, c.name as customer_name')
                        ->join('customers c', 'c.customer_id = e.customer_id', 'left')
                        ->where('e.customer_id', $customer_id)
                        ->where('e.company_id', $companyId)
                        ->orderBy('e.estimate_id','DESC')
                        ->get()
                        ->getResultArray();

        foreach ($estimates as $est) {
            $items = $db->table('estimate_items')
                        ->where('estimate_id', $est['estimate_id'])
                        ->get()->getResultArray();
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $data[] = [
                'id' => $est['estimate_id'],
                'no' => $est['estimate_no'],
                'customer' => $est['customer_name'],
                'subtotal' => $subtotal,
                'discount' => $est['discount'],
                'total' => $est['total_amount'],
                'paid' => null,
                'balance' => null,
                'date' => $est['date']
            ];
        }
    }

    return $this->response->setJSON($data);
}

}
