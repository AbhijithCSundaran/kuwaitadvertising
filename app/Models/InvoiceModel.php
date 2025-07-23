<?php
namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $allowedFields = ['customer_id', 'billing_address','shipping_address','phone_number','lpo_no','discount', 'total_amount', 'sub_total','invoice_date','status'];
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



    public function getFilteredInvoices($search = '', $start = 0, $length = 10, $orderColumn = 'invoice_id', $orderDir = 'desc')
    {

    $builder = $this->db->table('invoices')
    ->select('invoices.invoice_id, invoices.customer_id, invoices.discount, invoices.total_amount, invoices.invoice_date, invoices.shipping_address, invoices.phone_number, invoices.lpo_no, customers.name AS customer_name, customers.address AS customer_address')
    ->join('customers', 'customers.customer_id = invoices.customer_id', 'left');

        if ($search) {
            $search = trim(strtolower($search));

            $builder->groupStart()
                ->like('LOWER(customers.name)', $search)
                ->orLike('LOWER(customers.address)', $search)
                ->orLike('FORMAT(invoices.discount, 2)', $search)
                ->orLike('DATE_FORMAT(invoices.invoice_date,"%d-%m-%Y")', $search)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        return $builder->get()->getResultArray( );
    }

    // Get single invoice with its items
     public function getInvoiceWithItems($id)
    {
        $invoice = $this->select('invoices.*, customers.name AS customer_name, customers.address AS customer_address')
            ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
            ->where('invoices.invoice_id', $id)
            ->first();
    
        if ($invoice) {
            $itemModel = new \App\Models\InvoiceItemModel();
            $invoice['items'] = $itemModel->where('invoice_id', $id)->findAll();
        }
    
        return $invoice;
    }
}
