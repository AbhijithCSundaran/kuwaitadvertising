<?php
namespace App\Models;
use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table = 'estimates';
    protected $primaryKey = 'estimate_id';
    protected $allowedFields = ['customer_id','discount', 'total_amount', 'sub_total','date','phone_number'];

    public function insertEstimateWithItems($estimateData, $items)
    {
        $estimateId = $this->insert($estimateData);
        $itemModel = new \App\Models\EstimateItemModel();

        foreach ($items as $item) {
            $item['estimate_id'] = $estimateId;
            $itemModel->insert($item);
        }

        return $estimateId;
    }
    public function updateEstimateWithItems($estimateId, $estimateData, $items)
    {
        $this->update($estimateId, $estimateData);
        $itemModel = new \App\Models\EstimateItemModel();

        $itemModel->where('estimate_id', $estimateId)->delete();

        foreach ($items as $item) {
            $item['estimate_id'] = $estimateId;
            $itemModel->insert($item);
        }
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
            $search = trim(strtolower($search));

            $builder->groupStart()
                ->like('LOWER(customers.name)', $search)
                ->orLike('LOWER(customers.address)', $search)
                ->orLike('FORMAT(estimates.discount, 2)', $search)
                ->orLike('DATE_FORMAT(estimates.date, "%d-%m-%Y")', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }



    public function getFilteredEstimates($search = '', $start = 0, $length = 10, $orderColumn = 'estimate_id', $orderDir = 'desc')
    {
        $builder = $this->db->table('estimates')
            ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left');

        if ($search) {
            $search = trim(strtolower($search));

            $builder->groupStart()
                ->like('LOWER(customers.name)', $search)
                ->orLike('LOWER(customers.address)', $search)
                ->orLike('FORMAT(estimates.discount, 2)', $search)
                ->orLike('DATE_FORMAT(estimates.date, "%d-%m-%Y")', $search)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        return $builder->get()->getResultArray();
    }


    public function getRecentEstimatesWithCustomer($limit = 5)
    {
        return $this->db->table('estimates')
            ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
            ->orderBy('estimates.date', 'DESC') // Or 'estimate_id' if you prefer
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

}

