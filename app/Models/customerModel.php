<?php
namespace App\Models;

use CodeIgniter\Model;

class customerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $allowedFields = ['name', 'address'];
    protected $returnType = 'array';

}
