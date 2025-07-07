<?php

namespace App\Models;

use CodeIgniter\Model;

class Managecompany_Model extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';
    protected $allowedFields = ['company_name', 'address', 'billing_address','tax_number', 'company_logo', 'email', 'phone'];

    
    public function getAllCompanyCount()
    {
        return $this->db->query("SELECT COUNT(*) AS totcompanies FROM {$this->table}")->getRow();
    }

    
    public function getFilteredCompanyCount($condition)
    {
        return $this->db->query("SELECT COUNT(*) AS filCompanies FROM {$this->table} WHERE $condition")->getRow();
    }

   
    public function getAllFilteredRecords($condition, $fromstart, $tolimit, $orderColumn = 'company_id', $orderDir = 'DESC')
{
    $allowedColumns = ['company_id', 'company_name', 'address', 'tax_number', 'email', 'phone', 'company_logo'];
    
    if (!in_array($orderColumn, $allowedColumns)) {
        $orderColumn = 'company_id';
    }

    $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

    return $this->db
        ->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $orderColumn $orderDir LIMIT $fromstart, $tolimit")
        ->getResultArray();
}


}
