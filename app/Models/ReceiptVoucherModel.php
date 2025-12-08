<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceiptVoucherModel extends Model
{
    protected $table = 'receipt_vouchers';
    protected $primaryKey = 'receipt_id';

    protected $allowedFields = [
        'invoice_id',
        'customer_id',
        'user_id',
        'company_id',
        'invoice_amount',
        'paid_amount',
        'partial_paid_amount',
        'payment_mode',
        'cash_cheque_knet', 
        'being_of',
        'created_at',
        'updated_at'
    ];
}
