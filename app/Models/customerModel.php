<?php
namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table      = 'customers';
    protected $primaryKey = 'customer_id';
    protected $allowedFields = ['name', 'address'];
    protected $useTimestamps = false;

    public function getCustomerByName($name)
    {
        return $this->where('name', $name)->first();
    }
}
