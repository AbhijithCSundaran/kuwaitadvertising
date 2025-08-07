<?php
namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $allowedFields = 
    ['customer_id', 
    'billing_address',
    'shipping_address',
    'phone_number',
    'lpo_no',
    'discount', 
    'total_amount',
    'invoice_date',
    'status',
    'delivery_date',
    'paid_amount',
    'balance_amount',
    'user_id',
    'company_id' ];
    protected $returnType = 'array';

     public function getInvoiceCount()
    {
        return $this->db->table('invoices')->countAllResults();
    }
    public function getInvoiceListWithCustomer()
{
    return $this->select('invoices.*, customers.name as customer_name, (SELECT item_name FROM invoice_items WHERE invoice_items.invoice_id = invoices.invoice_id LIMIT 1) as item_name')
                ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
                ->orderBy('invoices.invoice_id', 'desc')
                ->findAll();
}
   public function getFilteredCount($search = '')
    {
        $builder = $this->db->table('invoices')
            ->join('customers', 'customers.customer_id = invoices.customer_id', 'left');

        if ($search) {
            $search = trim(strtolower($search));

            $builder->groupStart()
                ->like('LOWER(customers.name)', $search)
                ->orLike('LOWER(customers.address)', $search)
                ->orLike('FORMAT(invoices.discount, 2)', $search)
                ->orLike('DATE_FORMAT(invoices.invoice_date, "%d-%m-%Y")', $search)
                ->groupEnd();
        }
        return $builder->countAllResults();
    }



   public function getFilteredInvoices($search = '', $start = 0, $length = 10, $orderColumn = 'invoice_id', $orderDir = 'desc', $companyId = null)
{
    $builder = $this->db->table('invoices')
        ->select('invoices.invoice_id, invoices.customer_id, invoices.discount, invoices.total_amount, invoices.invoice_date, invoices.shipping_address, invoices.phone_number, invoices.lpo_no, invoices.status, customers.name AS customer_name, customers.address AS customer_address')
        ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
        ->join('user', 'user.user_id = invoices.user_id', 'left');

    if ($companyId) {
        $builder->where('user.company_id', $companyId);
    }
    if ($search) {
        $search = trim(strtolower($search));
        $builder->groupStart()
            ->like('LOWER(customers.name)', $search)
            ->orLike('LOWER(customers.address)', $search)
            ->orLike('FORMAT(invoices.discount, 2)', $search)
            ->orLike('DATE_FORMAT(invoices.invoice_date,"%d-%m-%Y")', $search)
            ->groupEnd();
    }

    return $builder->orderBy($orderColumn, $orderDir)
                   ->limit($length, $start)
                   ->get()->getResultArray();
}

   public function getInvoiceWithItems($id)
{
    $invoice = $this->select(
        'invoices.invoice_id,
         invoices.customer_id,
         invoices.phone_number,
         invoices.billing_address,
         invoices.shipping_address,
         invoices.discount,
         invoices.total_amount,
         invoices.paid_amount,
         invoices.balance_amount,
         invoices.status,
         invoices.lpo_no,
         invoices.invoice_date,
         company.company_name AS company_name,
         customers.name AS customer_name, 
         customers.address AS customer_address'
    )
    ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
    ->join('user', 'user.user_id = invoices.user_id', 'left')
    ->join('company', 'company.company_id = user.company_id', 'left')
    ->where('invoices.invoice_id', $id)
    ->first();

    if ($invoice) {
        $itemModel = new InvoiceItemModel();
        $invoice['items'] = $itemModel->where('invoice_id', $id)->findAll();
    }

    return $invoice;
}

}
