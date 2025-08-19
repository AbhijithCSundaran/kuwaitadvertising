<?php namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';
    protected $allowedFields = [
        'estimate_id','company_id','customer_id','customer_address','customer_phone','customer_email',
        'total_amount','status','user_id','discount','invoice_date','billing_address',
        'shipping_address','lpo_no','phone_number','delivery_date','paid_amount','balance_amount','payment_mode'
    ];
}
