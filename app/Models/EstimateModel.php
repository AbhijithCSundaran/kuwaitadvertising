<?php
namespace App\Models;

use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table = 'estimates';
    protected $primaryKey = 'estimate_id';
    protected $allowedFields = [
        'company_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'discount',
        'user_id',
        'date',
        'customer_address',
        'description'
    ];

    protected $useTimestamps = false; 
}
