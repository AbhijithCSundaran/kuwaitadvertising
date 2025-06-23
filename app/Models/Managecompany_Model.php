<?php

namespace App\Models;

use CodeIgniter\Model;

class Managecompany_Model extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';
    protected $allowedFields = ['company_name', 'address', 'tax_number', 'company_logo', 'email', 'phone'];

    
    public function getAllCompanyCount()
    {
        return $this->db->query("SELECT COUNT(*) AS totcompanies FROM {$this->table}")->getRow();
    }

    
    public function getFilteredCompanyCount($condition)
    {
        return $this->db->query("SELECT COUNT(*) AS filCompanies FROM {$this->table} WHERE $condition")->getRow();
    }

   
    public function getAllFilteredRecords($condition, $fromstart, $tolimit, $order = 'DESC')
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY company_id $order LIMIT $fromstart, $tolimit")
            ->getResultArray();
    }
}
