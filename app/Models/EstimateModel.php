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

    public function getEstimateCount($companyId)
    {
        return $this->where('company_id', $companyId)->countAllResults();
    }

   public function getFilteredCount($searchValue, $companyId)
{
    $searchValue = trim($searchValue);

    $builder = $this->db->table('estimates')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimates.company_id', $companyId);

    if (!empty($searchValue)) {
    $normalizedSearch = preg_replace('/\s+/', '', strtolower($searchValue)); 

    $builder->groupStart()
        ->like('customers.name', $searchValue)
        ->orLike('customers.address', $searchValue)
        ->orLike('estimates.estimate_id', $searchValue)
        ->orWhere("REPLACE(REPLACE(REPLACE(LOWER(customers.name), ' ', ''), '\n', ''), '\r', '') LIKE '%{$normalizedSearch}%'", null, false)
        ->orWhere("REPLACE(REPLACE(REPLACE(LOWER(customers.address), ' ', ''), '\n', ''), '\r', '') LIKE '%{$normalizedSearch}%'", null, false)
    ->groupEnd();
}

    return $builder->countAllResults();
}


public function getFilteredEstimates($searchValue, $start, $length, $orderByColumn, $orderDir, $companyId)
{
    $searchValue = trim($searchValue); 

    $builder = $this->db->table('estimates')
        ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimates.company_id', $companyId);

    if (!empty($searchValue)) {
        $normalizedSearch = str_replace(' ', '', strtolower($searchValue));

        $builder->groupStart()
            ->like('customers.name', $searchValue)
            ->orLike('customers.address', $searchValue)
            ->orLike('estimates.estimate_id', $searchValue)

           ->orWhere("REPLACE(REPLACE(REPLACE(LOWER(customers.name), ' ', ''), '\n', ''), '\r', '') LIKE '%{$normalizedSearch}%'", null, false)
        ->orWhere("REPLACE(REPLACE(REPLACE(LOWER(customers.address), ' ', ''), '\n', ''), '\r', '') LIKE '%{$normalizedSearch}%'", null, false)
        ->groupEnd();
    }

    $builder->orderBy($orderByColumn, $orderDir)
            ->limit($length, $start);

    return $builder->get()->getResultArray();
}

  public function getRecentEstimatesWithCustomer($limit = 5)
{
    $companyId = session()->get('company_id');
    return $this->db->table('estimates')
        ->select('estimates.*, customers.name AS customer_name, customers.address AS customer_address')
        ->join('customers', 'customers.customer_id = estimates.customer_id', 'left')
        ->where('estimates.company_id', $companyId) 
        ->orderBy('estimates.date', 'DESC')
        ->limit($limit)
        ->get()
        ->getResultArray();
}

}
