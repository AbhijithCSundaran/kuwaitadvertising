<?php namespace App\Models;

use CodeIgniter\Model;

class CashReceiptModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';
    protected $allowedFields = [
        'estimate_id','company_id','customer_id','customer_address','customer_phone','customer_email',
        'total_amount','status','user_id','discount','invoice_date','billing_address',
        'shipping_address','lpo_no','phone_number','delivery_date','paid_amount','balance_amount','payment_mode'
    ];

    public function getAllFilteredCashReceipts($search = '', $start = 0, $length = 10, $orderColumn = 'invoice_id', $orderDir = 'DESC')
    {
        $builder = $this->db->table('invoices i')
            ->select('i.*, c.name AS customer_name')
            ->join('customers c', 'c.customer_id = i.customer_id', 'left')
            ->where('i.status !=', 'unpaid');

        if($search){
            $builder->groupStart()
                    ->like('REPLACE(LOWER(c.customer_name)," ","")', str_replace(' ', '', strtolower($search)))
                    ->orLike('i.status', $search)
                    ->orLike('i.payment_mode', $search)
                    ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        return $builder->get()->getResultArray();
    }

    public function getAllCashReceiptsCount()
    {
        return $this->db->table('invoices')->countAll();
    }

  public function getFilteredCashReceiptsCount($search = '')
{
    $builder = $this->db->table('invoices i')
                        ->join('customers c', 'c.customer_id = i.customer_id', 'left')
                        ->where('i.status !=', 'unpaid'); // <-- exclude unpaid

    if ($search) {
        $builder->groupStart()
                ->like('REPLACE(LOWER(c.customer_name)," ","")', str_replace(' ', '', strtolower($search)))
                ->orLike('i.status', $search)
                ->orLike('i.payment_mode', $search)
                ->groupEnd();
    }

    return (object)['filReceipts' => $builder->countAllResults()];
}

}
