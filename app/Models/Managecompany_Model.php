<?php

namespace App\Models;

use CodeIgniter\Model;

class Managecompany_Model extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';
    protected $allowedFields = ['company_name', 'address', 'tax_number', 'company_logo', 'email', 'phone'];
    
}
