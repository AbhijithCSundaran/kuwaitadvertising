<?php

namespace App\Models;

use CodeIgniter\Model;

class Managecompany_Model extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';
    protected $allowedFields = ['company_name', 'address', 'billing_address','tax_number', 'company_logo', 'email', 'phone','company_status'];

    
    // public function getAllCompanyCount()
    // {
    //     return $this->db->query("SELECT COUNT(*) AS totcompanies FROM {$this->table}WHERE company_status = 1")->getRow();
    // }
    public function getAllCompanyCount()
{
    return $this->db
        ->table($this->table)
        ->where('company_status', 1)
        ->selectCount('*', 'totcompanies')
        ->get()
        ->getRow();
}    
   //    public function getFilteredCompanyCount($search = '')
    // {
    //     $builder = $this->db->table($this->table);
    //      $builder->where('company_status', 1);
    //     if ($search) {
    //         $search = strtolower(trim($search));
    //         $search = str_replace(' ', '', $search);

    //         $builder->groupStart()
    //             ->like("REPLACE(LOWER(company_name), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(address), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(tax_number), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(email), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(phone), ' ', '')", $search)
    //             ->groupEnd();
    //     }

    //     $count = $builder->countAllResults();

    //     return (object)['filCompanies' => $count];
    // }
    public function getFilteredCompanyCount($search = '')
{
    $builder = $this->db->table($this->table);
    $builder->where('company_status', 1);

    if ($search) {
        $search = strtolower(trim($search));
        $search = str_replace(' ', '', $search);

        $builder->groupStart()
            ->like("REPLACE(LOWER(company_name), ' ', '')", $search)
            ->orLike("REPLACE(LOWER(address), ' ', '')", $search)
            ->orLike("REPLACE(LOWER(tax_number), ' ', '')", $search)
            ->orLike("REPLACE(LOWER(email), ' ', '')", $search)
            ->orLike("REPLACE(LOWER(phone), ' ', '')", $search)
            ->groupEnd();
    }

    $count = $builder->countAllResults();

    return (object)['filCompanies' => $count];
}
    // public function getAllFilteredRecords($search = '', $fromstart = 0, $tolimit = 10, $orderColumn = 'company_id', $orderDir = 'DESC')
    // {
    //     $allowedColumns = ['company_id', 'company_name', 'address', 'tax_number', 'email', 'phone', 'company_logo'];
    //     if (!in_array($orderColumn, $allowedColumns)) {
    //         $orderColumn = 'company_id';
    //     }

    //     $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

    //     $builder = $this->db->table($this->table);
    //     $builder->where('company_status', 1); 

    //     if ($search) {
    //         $search = strtolower(trim($search));
    //         $search = str_replace(' ', '', $search);

    //         $builder->groupStart()
    //             ->like("REPLACE(LOWER(company_name), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(address), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(tax_number), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(email), ' ', '')", $search)
    //             ->orLike("REPLACE(LOWER(phone), ' ', '')", $search)
    //             ->groupEnd();
    //     }

    //     $builder->orderBy($orderColumn, $orderDir)
    //             ->limit($tolimit, $fromstart);

    //     return $builder->get()->getResultArray();
    // }
    public function getAllFilteredRecords($search = '', $fromstart = 0, $tolimit = 10, $orderColumn = 'company_id', $orderDir = 'DESC')
    {
        $allowedColumns = ['company_id', 'company_name', 'address', 'tax_number', 'email', 'phone', 'company_logo'];
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'company_id';
        }

        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $builder = $this->db->table($this->table);
        $builder->where('company_status', 1);

        if ($search) {
            $search = strtolower(trim($search));
            $search = str_replace(' ', '', $search);

            $builder->groupStart()
                ->like("REPLACE(LOWER(company_name), ' ', '')", $search)
                ->orLike("REPLACE(LOWER(address), ' ', '')", $search)
                ->orLike("REPLACE(LOWER(tax_number), ' ', '')", $search)
                ->orLike("REPLACE(LOWER(email), ' ', '')", $search)
                ->orLike("REPLACE(LOWER(phone), ' ', '')", $search)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($tolimit, $fromstart);

        return $builder->get()->getResultArray();
    }
    public function getActiveCompanies()
{
    return $this->where('company_status', 1)->findAll();
}

}
