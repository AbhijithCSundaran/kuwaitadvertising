<?php
namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'sale_id';
    protected $allowedFields = ['customer_id', 'date', 'total_amount', 'discount'];

    public function getSalesReport($from = null, $to = null, $customer_id = null)
    {
        $builder = $this->select('sales.*, customers.name as customer_name')
                        ->join('customers', 'customers.customer_id = sales.customer_id');

        if ($from) $builder->where('sales.date >=', $from);
        if ($to) $builder->where('sales.date <=', $to);
        if ($customer_id) $builder->where('sales.customer_id', $customer_id);

        return $builder->orderBy('sales.date', 'DESC')->findAll();
    }
}
