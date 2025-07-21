<?php
namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $allowedFields = [
        'customer_id',
        'customer_address',
        'total_amount',
        'invoice_date',
        'status',
        'user_id'
    ];

    protected $returnType = 'array';

    // Get all invoices with customer name
    public function getInvoiceListWithCustomer()
{
    return $this->select('invoices.*, customers.name as customer_name, (SELECT item_name FROM invoice_items WHERE invoice_items.invoice_id = invoices.invoice_id LIMIT 1) as item_name')
                ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
                ->orderBy('invoices.invoice_id', 'desc')
                ->findAll();
}


    // Get single invoice with its items
    public function getInvoiceWithItems($id)
    {
        $invoice = $this->select('invoices.*, customers.name as customer_name')
                        ->join('customers', 'customers.customer_id = invoices.customer_id', 'left')
                        ->where('invoice_id', $id)
                        ->first();

        if (!$invoice) return null;

        // Fetch items from invoice_item table
        $db = \Config\Database::connect();
        $builder = $db->table('invoice_items');
        $items = $builder->where('invoice_id', $id)->get()->getResultArray();

        $invoice['items'] = $items;
        return $invoice;
    }
}
