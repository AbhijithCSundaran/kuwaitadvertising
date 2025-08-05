<?php
namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'sale_id';
    protected $allowedFields = ['customer_id', 'date', 'total_amount'];

  public function getSalesReport($from = null, $to = null, $customer_id = null)
{
    $builder = $this->db->table('sales')
        ->select('sales.date, customers.name as customer_name, invoices.total_amount, invoices.status')
        ->join('customers', 'customers.customer_id = sales.customer_id')
        ->join('invoices', 'invoices.sale_id = sales.sale_id', 'left');

    if ($from) {
        $builder->where('DATE(sales.date) >=', $from);
    }
    if ($to) {
        $builder->where('DATE(sales.date) <=', $to);
    }
    if ($customer_id) {
        $builder->where('sales.customer_id', $customer_id);
    }

    return $builder->get()->getResultArray();
}

}
