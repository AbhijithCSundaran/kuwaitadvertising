<?php
namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\CustomerModel;

class Sales extends BaseController
{
    public function report()
    {
        $salesModel = new SalesModel();
        $customerModel = new CustomerModel();

        $from = $this->request->getGet('from_date');
        $to = $this->request->getGet('to_date');
        $customer_id = $this->request->getGet('customer_id');

        $data['sales'] = $salesModel->getSalesReport($from, $to, $customer_id);
        $data['customers'] = $customerModel->findAll();
        $data['filters'] = compact('from', 'to', 'customer_id');

        return view('salesform', $data);
    }
}
