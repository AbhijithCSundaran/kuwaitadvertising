<?php
namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\customerModel;
use App\Models\InvoiceModel;

class Sales extends BaseController
{
    public function __construct()
{
    $this->db = \Config\Database::connect();
}

 public function report()
{
    $salesModel = new SalesModel();
    $customerModel = new customerModel();

    $from = $this->request->getGet('from_date');
    $to = $this->request->getGet('to_date');
    $customer_id = $this->request->getGet('customer_id');

    $result = $salesModel->getSalesReport($from, $to, $customer_id);

    if ($this->request->isAJAX()) {
       return $this->response->setJSON(['sales' => $result]);
    }

    $data['sales'] = $result;
    $data['customers'] = $customerModel->findAll();
    $data['filters'] = compact('from', 'to', 'customer_id');

    return view('salesform', $data);
}

public function getSalesReportAjax()
{
    $from = $this->request->getPost('fromDate');
    $to = $this->request->getPost('toDate');
    $customerId = $this->request->getPost('customerId');

    $db = \Config\Database::connect();
    $builder = $db->table('sales');
    $builder->select('sales.date, customers.name as customer_name, invoices.total_amount, invoices.status');
    $builder->join('customers', 'customers.customer_id = sales.customer_id');
    $builder->join('invoices', 'invoices.sale_id = sales.sale_id', 'left');

    if (!empty($from)) {
        $builder->where('sales.date >=', $from);
    }
    if (!empty($to)) {
        $builder->where('sales.date <=', $to);
    }
    if (!empty($customerId)) {
        $builder->where('sales.customer_id', $customerId);
    }

    $query = $builder->get();
    $data = $query->getResultArray();

    return $this->response->setJSON(['sales' => $data]);
}

}
