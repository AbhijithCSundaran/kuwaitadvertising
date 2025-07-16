<?php
namespace App\Models;

use CodeIgniter\Model;

class customerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $allowedFields = ['name', 'address', 'is_deleted'];
    protected $returnType = 'array';

    public function getAllCustomerCount()
    {
        return $this->db->query("SELECT COUNT(*) AS totcustomers FROM {$this->table}")->getRow();
    }

    public function getFilteredCustomerCount($search = '')
    {
        $builder = $this->db->table($this->table);
        $builder->where('is_deleted', 0);


        if ($search) {
            $search = strtolower(trim($search));
            $search = str_replace(' ', '', $search);

            $builder->groupStart()
                ->like("REPLACE(LOWER(name), ' ', '')", $search)
                ->orLike("REPLACE(LOWER(address), ' ', '')", $search)
                ->groupEnd();
                
        }

        $count = $builder->countAllResults();

        return (object)['filCustomers' => $count];
    }

    public function getAllFilteredRecords($search = '', $fromstart = 0, $tolimit = 10, $orderColumn = 'customer_id', $orderDir = 'DESC')
    {
        $allowedColumns = ['customer_id', 'name', 'address'];
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'customer_id';
        }

        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $builder = $this->db->table($this->table);
        $builder->where('is_deleted', 0);


        if ($search) {
            $search = strtolower(trim($search));
            $search = str_replace(' ', '', $search);

            $builder->groupStart()
                ->like("REPLACE(LOWER(name), ' ', '')", $search)
                ->orLike("REPLACE(LOWER(address), ' ', '')", $search)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($tolimit, $fromstart);

        return $builder->get()->getResultArray();
    }
}
