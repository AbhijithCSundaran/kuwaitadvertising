<?php
namespace App\Models;
use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table = 'estimates';
    protected $primaryKey = 'estimate_id';
    protected $allowedFields = ['customer_id','discount', 'total_amount', 'sub_total','date','phone_number', 'is_converted', 'company_id'];

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

    // ✅ Filter by company ID
    public function getEstimateCount($companyId)
    {
        return $this->where('company_id', $companyId)->countAllResults();
    }

    // ✅ Filtered Count with search and company
    public function getFilteredCount($searchValue, $companyId)
    {
        $builder = $this->db->table('estimates')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
            ->where('estimates.company_id', $companyId);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('customers.name', $searchValue)
                ->orLike('customers.address', $searchValue)
                ->orLike('estimates.estimate_id', $searchValue)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    // ✅ Filtered Estimates List with search, pagination, and company
    public function getFilteredEstimates($searchValue, $start, $length, $orderByColumn, $orderDir, $companyId)
    {
        $builder = $this->db->table('estimates')
            ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
            ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
            ->where('estimates.company_id', $companyId);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('customers.name', $searchValue)
                ->orLike('customers.address', $searchValue)
                ->orLike('estimates.estimate_id', $searchValue)
                ->groupEnd();
        }

        $builder->orderBy($orderByColumn, $orderDir)
                ->limit($length, $start);

        return $builder->get()->getResultArray();
    }

  public function getRecentEstimatesWithCustomer($limit = 5)
{
    $companyId = session()->get('company_id'); // Ensure company filter
    return $this->db->table('estimates')
        ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimates.company_id', $companyId) // ✅ Add this line
        ->orderBy('estimates.date', 'DESC')
        ->limit($limit)
        ->get()
        ->getResultArray();
}

}
