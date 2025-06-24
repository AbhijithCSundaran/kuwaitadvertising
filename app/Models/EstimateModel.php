<?php
// app/Models/EstimateModel.php
namespace App\Models;
use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table = 'estimates';
    protected $primaryKey = 'estimate_id';
    protected $allowedFields = ['customer_id','discount', 'total_amount', 'sub_total','date'];

    public function insertEstimateWithItems($estimateData, $items)
    {
        $estimateId = $this->insert($estimateData);
        $itemModel = new \App\Models\EstimateitemModel();

        foreach ($items as $item) {
            $item['estimate_id'] = $estimateId;
            $itemModel->insert($item);
        }

        return $estimateId;
    }
    public function getEstimateCount()
    {
        return $this->db->table('estimates')->countAllResults();
    }

    public function getFilteredCount($search = '')
    {
        $builder = $this->db->table('estimates')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left');

        if ($search) {
            $builder->like('customers.name', $search)
                    ->orLike('customers.address', $search);
        }

        return $builder->countAllResults();
    }

    public function getFilteredEstimates($search = '', $start = 0, $length = 10)
    {
        $builder = $this->db->table('estimates')
            ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
            ->orderBy('estimate_id', 'DESC')
            ->limit($length, $start);

        if ($search) {
            $builder->like('customers.name', $search)
                    ->orLike('customers.address', $search);
        }

        return $builder->get()->getResultArray();
    }
}

