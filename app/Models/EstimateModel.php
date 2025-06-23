<?php
// app/Models/EstimateModel.php
namespace App\Models;
use CodeIgniter\Model;

class EstimateModel extends Model
{
    protected $table = 'estimates';
    protected $primaryKey = 'estimate_id';
    protected $allowedFields = ['customer_id', 'customer_address', 'discount', 'total_amount', 'date'];

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
}

